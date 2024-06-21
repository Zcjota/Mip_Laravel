<?php

namespace App\Http\Controllers\RegistroVentasAdm;


use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiController;
use App\Model\Apertura;
use App\Model\Cuenta;
use App\Model\DetalleGasto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\NumerosEnLetras;
use App\Model\GastoPresupuesto;
use App\Model\PresupuestoCuenta;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistroVentasExport;
use App\Exports\AsignarPresupuestoGlobal\AsignarPresupuestoGExport;

class RegistroVentasAdmController extends ApiController
{
   

    public function BuscarRegistroVentas(Request $request)
{
    if (!$request->ajax()) return redirect('/login');

    $query = DB::table("orden_trabajo as O")
        ->leftJoin('cliente AS C', 'O.COD_CLIENTE', '=', 'C.COD_CLIENTE')
        ->leftJoin('personal AS P', 'O.COD_EJECUTIVO_VENTAS', '=', 'P.COD_PERSONAL')
        ->leftJoin('hojaservicio AS H', 'H.COD_ORDEN_TRABAJO', '=', 'O.COD_ORDEN_TRABAJO')
        ->leftJoin('registro_venta AS R', 'R.COD_ORDEN_TRABAJO', '=', 'O.COD_ORDEN_TRABAJO') // Asegúrate de tener una relación correcta aquí
        ->selectRaw('O.COD_ORDEN_TRABAJO, O.COD_CLIENTE, C.NOMBRE AS NOMCLIENTE, C.AP_CLIENTE, C.AM_CLIENTE, C.CONTACTO, 
            C.DIRECCION, C.TELEFONO, O.FECHA AS FECHA1, O.FECHA_SERVICIO AS FECHAS, O.HORA_SERVICIO, O.RAZON_SOCIAL, O.NIT, 
            O.OBSERVACIONES, O.PRECIO, O.BS, O.NRO_TECNICOS, O.COD_EQUIPO, O.APROBADA, O.COBRADA, O.SUSPENDIDA, 
            O.TIEMPO_SERVICIO, O.COD_EJECUTIVO_VENTAS, P.NOMBRE, P.AP_PATERNO, P.AP_MATERNO, O.NRO_ORDEN, 
            O.RESPONSABLES, H.NRO_SERVICIO, H.ACTIVO, O.TIPO_PAGO, O.PLAZO, O.REPROGRAMADA, O.INICIAL,
            R.CODNUM, R.DESCRIPCION, DATE_FORMAT(R.FECHA_RECIBO, "%d/%m/%Y") AS FECHA_RECIBO, 
            R.NRO_RECIBO, R.CONTADO_NRO_DEPOSITO, R.NRO_CHEQUE_NRO_DEPOSITO, R.TRANSFERENCIA, 
            IFNULL(DATE_FORMAT(R.FECHA_FACTURA, "%d/%m/%Y"),"") AS FECHA_FACTURA, R.NRO_FACTURA, 
            R.DEBE, R.MONTO_BS, R.MONTO_DOLAR, R.BANDERA')
        ->where('O.PRECIO', '>', 0)
        ->where('O.ACTIVO', '=', 1)
        ->where('O.ANULADA', '=', 0)
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('registro_venta as R2')
                  ->whereRaw('R2.COD_ORDEN_TRABAJO = O.COD_ORDEN_TRABAJO')
                  ->where('R2.ACTIVO', '=', 1);
        });

    if ($request->estado != "ALL") {
        $query->where('O.ESTADO', '=', $request->estado);
    }

    if ($request->filled('buscar')) {
        $buscar = str_replace('*', '%', trim(strtoupper($request->buscar)));
        $query->where(function($q) use ($buscar) {
            $q->where('O.NRO_ORDEN', 'LIKE', $buscar)
                ->orWhere('C.AP_CLIENTE', 'LIKE', $buscar)
                ->orWhere('C.AM_CLIENTE', 'LIKE', $buscar)
                ->orWhere('C.NOMBRE', 'LIKE', $buscar)
                ->orWhere('C.CONTACTO', 'LIKE', $buscar)
                ->orWhere('P.AP_PATERNO', 'LIKE', $buscar);
        });
    }

    if ($request->filled('fechaInicio') && $request->filled('fechaFin')) {
        $query->whereBetween('O.FECHA_SERVICIO', [$request->fechaInicio, $request->fechaFin]);
    }

    if ($request->filled('filtroinicial')) {
        switch ($request->filtroinicial) {
            case 1:
                $query->where('O.APROBADA', '=', 0)
                    ->where('O.COBRADA', '=', 0)
                    ->where('O.SUSPENDIDA', '=', 0)
                    ->where('O.REPROGRAMADA', '=', 0);
                break;
            case 2:
                $query->where('O.APROBADA', '=', 1)
                    ->where('O.COBRADA', '=', 0)
                    ->where('O.SUSPENDIDA', '=', 0)
                    ->where('O.REPROGRAMADA', '=', 0);
                break;
            case 3:
                $query->where('O.APROBADA', '=', 1)
                    ->where('O.COBRADA', '=', 1)
                    ->where('O.SUSPENDIDA', '=', 0)
                    ->where('O.REPROGRAMADA', '=', 0);
                break;
            case 4:
                $query->where('O.APROBADA', '=', 0)
                    ->where('O.COBRADA', '=', 0)
                    ->where('O.SUSPENDIDA', '=', 1)
                    ->where('O.REPROGRAMADA', '=', 0);
                break;
            case 5:
                $query->where('O.APROBADA', '=', 0)
                    ->where('O.COBRADA', '=', 0)
                    ->where('O.SUSPENDIDA', '=', 0)
                    ->where('O.REPROGRAMADA', '=', 1);
                break;
        }
    }

    // Aquí se aplica el filtro tipoDebe en la consulta principal
    if ($request->filled('tipoDebe')) {
        $query->where('R.DEBE', '=', $request->tipoDebe);
    }

    $ordenes = $query->orderBy('O.FECHA_SERVICIO', 'asc')
                     ->orderBy('C.NOMBRE', 'asc')
                     ->paginate(5);

    foreach ($ordenes as $orden) {
        $orden->cliente = $orden->AP_CLIENTE . ' ' . $orden->AM_CLIENTE . ' ' . $orden->NOMCLIENTE;
        $orden->TIPO_PAGOS = ($orden->TIPO_PAGO == 1) ? 'AL CONTADO' : 'CREDITO';
        $orden->PLAZOS = $orden->PLAZO . ' DIAS';

        $registros_venta = DB::table('registro_venta as R')
            ->selectRaw('R.CODNUM, R.DESCRIPCION, DATE_FORMAT(R.FECHA_RECIBO, "%d/%m/%Y") AS FECHA_RECIBO, 
                         R.NRO_RECIBO, R.CONTADO_NRO_DEPOSITO, R.NRO_CHEQUE_NRO_DEPOSITO, R.TRANSFERENCIA, 
                         IFNULL(DATE_FORMAT(R.FECHA_FACTURA, "%d/%m/%Y"),"") AS FECHA_FACTURA, R.NRO_FACTURA, 
                         R.DEBE, R.MONTO_BS, R.MONTO_DOLAR, R.BANDERA')
            ->where('R.ACTIVO', '=', 1)
            ->where('R.COD_ORDEN', '=', $orden->NRO_ORDEN)
            ->where('R.COD_ORDEN_TRABAJO', '=', $orden->COD_ORDEN_TRABAJO);

        $registros_venta = $registros_venta->get();

        foreach ($registros_venta as $registro) {
            $montopagod = "";

            if ($registro->BANDERA == 1) {
                $tipoCambio = 6.96;
                $diferencia = is_numeric($registro->MONTO_DOLAR) ? $registro->MONTO_DOLAR * $tipoCambio : 0;
                $diferenciaf = is_numeric($registro->MONTO_BS) ? $registro->MONTO_BS - $diferencia : 0;
                $montobs2 = $registro->MONTO_BS;
                if ($montobs2 > 0) {
                    $montopagod = round($diferenciaf, 2);
                }
            }

            $fecha_estimada = "S/F";
            if ($registro->FECHA_FACTURA != "") {
                $fechaFactura = Carbon::createFromFormat('d/m/Y', $registro->FECHA_FACTURA);
                $fecha_estimada = $fechaFactura->addDays($orden->PLAZO)->format('d/m/Y');
            }

            $orden->fecEstimada = $fecha_estimada;
            $orden->montopagodolar = $registro->MONTO_DOLAR . ' USD';
            $orden->BS = $registro->MONTO_BS;
            $orden->difpago = $montopagod;
            $orden->fecha_recibo = $registro->FECHA_RECIBO;
            $orden->nro_recibo = $registro->NRO_RECIBO;
            $orden->contado_nro_deposito = $registro->CONTADO_NRO_DEPOSITO;
            $orden->nro_cheque = $registro->NRO_CHEQUE_NRO_DEPOSITO;
            $orden->transferencia = $registro->TRANSFERENCIA;
            $orden->fecha_factura = $registro->FECHA_FACTURA;
            $orden->nro_factura = $registro->NRO_FACTURA;
            $orden->debe = $registro->DEBE == "" ? "DEBE" : $registro->DEBE;
        }

        // Calcular el estado de la orden
        $estado = 'SIN APROBAR';
        if ($orden->COBRADA) {
            $estado = 'COBRADA';
        } elseif ($orden->SUSPENDIDA) {
            $estado = 'SUSPENDIDA';
        } elseif ($orden->REPROGRAMADA) {
            $estado = 'REPROGRAMADA';
        } elseif ($orden->APROBADA) {
            $estado = 'APROBADA';
        }
        $orden->estado = $estado;
    }

    return response()->json(['result' => $ordenes]);
}
    
public function EditarRegistroVentas($COD_ORDEN_TRABAJO, Request $request)
{
    if (!$request->ajax()) return redirect('/login');

    $result = DB::table("orden_trabajo as O")
        ->leftJoin('cliente AS C', 'O.COD_CLIENTE', '=', 'C.COD_CLIENTE')
        ->leftJoin('personal AS P', 'O.COD_EJECUTIVO_VENTAS', '=', 'P.COD_PERSONAL')
        ->leftJoin('hojaservicio AS H', 'H.COD_ORDEN_TRABAJO', '=', 'O.COD_ORDEN_TRABAJO')
        ->selectRaw('O.COD_ORDEN_TRABAJO, O.COD_CLIENTE, C.NOMBRE AS NOMCLIENTE, C.AP_CLIENTE, C.AM_CLIENTE, C.CONTACTO, 
            C.DIRECCION, C.TELEFONO, O.FECHA AS FECHA1, O.FECHA_SERVICIO AS FECHAS, O.HORA_SERVICIO, O.RAZON_SOCIAL, O.NIT, 
            O.OBSERVACIONES, O.PRECIO, O.BS, O.NRO_TECNICOS, O.COD_EQUIPO, O.APROBADA, O.COBRADA, O.SUSPENDIDA, 
            O.TIEMPO_SERVICIO, O.COD_EJECUTIVO_VENTAS, P.NOMBRE, P.AP_PATERNO, P.AP_MATERNO, O.NRO_ORDEN, 
            O.RESPONSABLES, H.NRO_SERVICIO, H.ACTIVO, O.TIPO_PAGO, O.PLAZO, O.REPROGRAMADA, O.INICIAL')
        ->where('O.COD_ORDEN_TRABAJO', '=', $COD_ORDEN_TRABAJO)
        ->first();

    if ($result == null) return $this->responseVal("El Registro no Existe.");

    $result->cliente = $result->AP_CLIENTE . ' ' . $result->AM_CLIENTE . ' ' . $result->NOMCLIENTE;
    $result->TIPO_PAGOS = ($result->TIPO_PAGO == 1) ? 'AL CONTADO' : 'CREDITO';
    $result->PLAZOS = $result->PLAZO . ' DIAS';

    $detalle = DB::table('registro_venta as R')
        ->selectRaw('R.CODNUM, R.DESCRIPCION, R.FECHA_RECIBO AS FECHA_RECIBO, 
            R.NRO_RECIBO, R.CONTADO_NRO_DEPOSITO, R.NRO_CHEQUE_NRO_DEPOSITO, R.TRANSFERENCIA, 
            R.FECHA_FACTURA, R.NRO_FACTURA, 
            R.DEBE, R.MONTO_BS, R.MONTO_DOLAR, R.BANDERA')
        ->where('R.ACTIVO', '=', 1)
        ->where("R.COD_ORDEN_TRABAJO", '=', $COD_ORDEN_TRABAJO)
        ->get()
        ->map(function ($item) {
            $item->FECHA_FACTURA = $this->formatFechaFactura($item->FECHA_FACTURA);
            return $item;
        });

    foreach ($detalle as $registro) {
        $montopagod = "";
        
        if ($registro->BANDERA == 1) {
            $tipoCambio = 6.96;
            $diferencia = is_numeric($registro->MONTO_DOLAR) ? $registro->MONTO_DOLAR * $tipoCambio : 0;
            $diferenciaf = is_numeric($registro->MONTO_BS) ? $registro->MONTO_BS - $diferencia : 0;
            $montobs2 = $registro->MONTO_BS;
            if ($montobs2 > 0) {
                $montopagod = round($diferenciaf, 2);
            }
        }

        $fecha_estimada = "S/F";
        if ($registro->FECHA_FACTURA != "") {
            $fechaFactura = Carbon::createFromFormat('Y-m-d', $registro->FECHA_FACTURA);
            $fecha_estimada = $fechaFactura->addDays($result->PLAZO)->format('Y-m-d');
        }

        $registro->fecEstimada = $fecha_estimada;
        $registro->montopagodolar = $registro->MONTO_DOLAR . ' USD';
        $registro->BS = $registro->MONTO_BS . ' BS';
        $registro->difpago = $montopagod;
        $registro->fecha_recibo_ = $registro->FECHA_RECIBO;
        $registro->nro_recibo = $registro->NRO_RECIBO;
        $registro->contado_nro_deposito = $registro->CONTADO_NRO_DEPOSITO;
        $registro->nro_cheque = $registro->NRO_CHEQUE_NRO_DEPOSITO;
        $registro->transferencia = $registro->TRANSFERENCIA;
        $registro->fecha_factura_ = $registro->FECHA_FACTURA;
        $registro->nro_factura = $registro->NRO_FACTURA;
        $registro->debe = $registro->DEBE == "" ? "DEBE" : $registro->DEBE;
    }

    // Calcular el estado de la orden
    $estado = 'SIN APROBAR';
    if ($result->COBRADA) {
        $estado = 'COBRADA';
    } elseif ($result->SUSPENDIDA) {
        $estado = 'SUSPENDIDA';
    } elseif ($result->REPROGRAMADA) {
        $estado = 'REPROGRAMADA';
    } elseif ($result->APROBADA) {
        $estado = 'APROBADA';
    }
    $result->estado = $estado;

    // Verifica si el último registro en el detalle existe para usarlo
    $registroUltimo = isset($detalle[0]) ? $detalle[0] : null;

    return $this->responseOk([
        'data' => $result,
        'data1' => $registroUltimo, // Asegúrate de pasar un valor válido o null
        // 'detalle' => $detalle,
    ]);
}

    private function formatFechaFactura($fecha)
    {
        // Define los posibles formatos de entrada
        $formats = ['Y-m-d', 'd-m-Y', 'Y/m/d'];
    
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $fecha);
                return $date->format('Y-m-d'); // Define el formato de salida esperado por <input type="date">
            } catch (\Exception $e) {
                // Si falla, intenta el siguiente formato
            }
        }
    
        return ''; // Retorna cadena vacía si no coincide con ningún formato
    }
    

    public function guardar(Request $request)
{
    DB::beginTransaction();
    
    try {
        // Obtener datos enviados
        $data = $request->all();
        
        // Validar que se haya enviado el identificador
        if (!isset($data['COD_ORDEN_TRABAJO'])) {
            throw new \Exception('El identificador COD_ORDEN_TRABAJO es requerido.');
        }

        // Actualizar la tabla `registro_venta`
        $registro = DB::table('registro_venta')
            ->where('COD_ORDEN_TRABAJO', $data['COD_ORDEN_TRABAJO'])
            ->update([
                'FECHA_RECIBO' => $data['fecha_recibo'],
                'NRO_RECIBO' => $data['nro_recibo'],
                'CONTADO_NRO_DEPOSITO' => $data['contadodep_'],
                'NRO_CHEQUE_NRO_DEPOSITO' => $data['chequedep_'],
                'TRANSFERENCIA' => $data['transferencia_'],
                'FECHA_FACTURA' => $data['fecha_factura_'],
                'NRO_FACTURA' => $data['nro_factura'],
                'DEBE' => $data['debe_']
            ]);

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'Registro actualizado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
    
public function exportRegistroVentas(Request $request)
{
    return Excel::download(new RegistroVentasExport($request), 'registro_ventas.xlsx');
}


public function desactivarRegistro(Request $request)
{
    if (!$request->ajax()) return redirect('/login');

    $codservicio = $request->codservicio;
    $codnum = $request->codnum;
    $codigo = $request->codigo;

    if (isset($codservicio)) {
        DB::beginTransaction();
        try {
            // Verificar si el registro puede ser desactivado
            $registroVenta = DB::table('registro_venta')
                ->where('COD_ORDEN_TRABAJO', $codigo)
                ->where('COD_ORDEN', $codservicio)
                ->where('CODNUM', $codnum)
                ->first();

            if ($registroVenta->DEBE !== "NO DEBE") {
                return response()->json(['success' => false, 'errors' => ['reason' => 'No se puede desactivar un registro que está en estado DEBE']]);
            }

            // Desactivar el registro de venta
            DB::table('registro_venta')
                ->where('COD_ORDEN_TRABAJO', $codigo)
                ->where('DEBE', 'NO DEBE')
                ->where('COD_ORDEN', $codservicio)
                ->where('CODNUM', $codnum)
                ->update(['ACTIVO' => 0]);

            // Actualizar la tabla `orden_trabajo`
            DB::table('orden_trabajo')
                ->where('COD_ORDEN_TRABAJO', $codigo)
                ->update([
                    'APROBADA' => 1,
                    'COBRADA' => 0,
                    'SUSPENDIDA' => 0,
                    'REPROGRAMADA' => 0,
                    'PAGO' => 0,
                    'ANULADA' => 0
                ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['reason' => 'Registro No Desactivado']]);
        }
    } else {
        return response()->json(['success' => false, 'errors' => ['reason' => 'No se ha enviado el parámetro correcto']]);
    }
}


}
