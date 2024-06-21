<?php
namespace App\Http\Controllers\AsignarPresupuesto;

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
use App\Exports\AsignarPresupuestoGlobal\AsignarPresupuestoGExport;

class AsignarPresupuestoController extends ApiController
{

    public function listaAsignarP(Request $request)
    {
        $anio = $request->anio;
        $mes = $request->mes;
        $busqueda = $request->buscar;
    
        // Obtener la apertura
        $apertura = DB::table('apertura')
            ->where('MES', $mes)
            ->where('GESTION', $anio)
            ->where('ACTIVO', 1)
            ->first();
    
        if (!$apertura) {
            return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados en el filtro.','action' => 'APERTURAR_MES'
        ], 404);
            
        }
    
        $codApertura = $apertura->COD_APERTURA;
    
        // Prepara la consulta base
        
        $query = DB::table('cuenta as C')
            ->join('nro_cuenta as n', 'C.NRO_CUENTA', '=', 'n.CODNUM')
            ->join('fin_fondo as F', 'C.COD_FONDO', '=', 'F.COD_FONDO')
            ->join('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
            ->select('C.COD_CUENTA as codigo', 'C.DESCRIPCION as descripcion', 'pc.DESCRIPCION as observacion', 'n.DESCRIPCION as nrocuenta', 'F.DESCRIPCION as fondo',
             'pc.MONTO_PRESUPUESTO as montoPresupuesto', 'pc.MONTO_EXCEDIDO AS MONTO_EXCEDIDO')
            ->where('pc.COD_APERTURA', $codApertura)
            ->where('pc.ACTIVO', 1)
            ->where('C.ACTIVO', 1)
            ->groupBy('C.COD_CUENTA', 'C.DESCRIPCION', 'n.DESCRIPCION', 'F.DESCRIPCION', 'pc.MONTO_PRESUPUESTO', 'pc.DESCRIPCION')
            ->orderBy('n.DESCRIPCION', 'asc')
            ->orderBy('C.DESCRIPCION', 'asc');
    
        // Aplica búsqueda condicional si es necesario
        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('C.DESCRIPCION', 'LIKE', "%{$busqueda}%")
                  ->orWhere('n.DESCRIPCION', 'LIKE', "%{$busqueda}%");
            });
        }
    
        // Ejecuta la consulta y pagina los resultados
        $resultados = $query->paginate(100);
    
        // Procesa los resultados para incluir gastos fijos y eventuales
        $resultados->getCollection()->transform(function ($item) use ($codApertura) {
            $codigo = $item->codigo;
            $item->montoGastoActual = $this->gastoactual($codigo, $codApertura);
            $item->montoGastoF = $item->montoGastoActual;
            $item->saldoActual = ($item->montoPresupuesto + $item->MONTO_EXCEDIDO) - $item->montoGastoF;
            
            $presupuestoTotal = $item->montoPresupuesto + $item->MONTO_EXCEDIDO;
            $item->porcentaje = $presupuestoTotal > 0 ? ($item->montoGastoActual / $presupuestoTotal) * 100 : 0;
            // $item->saldoActual = $item->montoPresupuesto - $item->montoGastoF;
            // $item->TOTAL = $item->MONTO_EXCEDIDO;
            // Formatear a 2 decimales
            // $item->montoPresupuesto = number_format($item->montoPresupuesto, 2, ',','.');
            // $item->MONTO_EXCEDIDO = number_format($item->MONTO_EXCEDIDO, 2, '.', '.');
            $item->saldoActual = number_format($item->saldoActual, 2, ',','.');
            $item->montoGastoF = number_format($item->montoGastoF, 2, ',','.');
            // $anio = $anio;
            // $item->mes = $item->mes;
            
            // $item->TOTAL = number_format($item->TOTAL, 2, '.','');

            // Esto para poder obtener los Gastos espesificos

            // $item->montoGastoFijo = $this->gastoFijo($codigo, $codApertura);
            // $item->montoGastoEventual = $this->gastoEventual($codigo, $codApertura);
            // $item->montoGastoEventualR = $this->gastoEventualR($codigo, $codApertura);
            // $item->montoGastoF =  $item->montoGastoFijo + $item->montoGastoEventual + $item->montoGastoEventualR;
            // $item->saldoActual = $item->montoPresupuesto - ($item->montoGastoFijo + $item->montoGastoEventual + $item->montoGastoEventualR);
            
            
            return $item;
        });
    
        return response()->json($resultados);
    }

    public function gastoactual($cuenta, $apertura)
    {
        $resultado = DB::table('gasto_presupuesto')
            ->where('COD_CUENTA', $cuenta)
            ->where('COD_APERTURA', $apertura)
            ->sum('MONTO_GASTO');
        return $resultado;
    }
    
    public function gastoFijo($cuenta, $apertura)
    {
    $resultado = DB::table('detalle_gasto')
        ->where('COD_CUENTA', $cuenta)
        ->where('COD_APERTURA', $apertura)
        ->where('ACTIVO', 1)
        ->where('CATEGORIA', 'F')
        ->sum('MONTO');

    return $resultado;
    }

    public function gastoEventual($cuenta, $apertura)
    {
    $resultado = DB::table('detalle_gasto')
        ->where('COD_CUENTA', $cuenta)
        ->where('COD_APERTURA', $apertura)
        ->where('ACTIVO', 1)
        ->where('CATEGORIA', 'E')
        ->sum('MONTO');

    return $resultado;
    }

    public function gastoEventualR($cuenta, $apertura)
    {
    $resultado = DB::table('detalle_gasto')
        ->where('COD_CUENTA', $cuenta)
        ->where('COD_APERTURA', $apertura)
        ->where('ACTIVO', 1)
        ->where('CATEGORIA', 'E')
        ->where('TIPO_PAGO', 2)
        ->sum('MONTO');

    return $resultado;
    }

    public function formatearNumero($numero)
   {
    // Formatea el número a 3 decimales, usa punto como separador decimal y coma para miles
    return number_format($numero, 2, ',', '.');
   }

   public function actualizarMontos(Request $request)
{
    $cuentas = $request->input('cuentas');
    $anio = $request->input('anio');
    $mes = $request->input('mes');

    // Obtener la apertura
    $apertura = DB::table('apertura')
        ->where('MES', $mes)
        ->where('GESTION', $anio)
        ->where('ACTIVO', 1)
        ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    foreach ($cuentas as $cuenta) {
        // Asegúrate de validar o limpiar adecuadamente los datos aquí.
        $codCuenta = $cuenta['codigo'];
        $montoPresupuesto = $cuenta['montoPresupuesto'];

        $montoGastoActual = $this->gastoactual($cuenta['codigo'], $codApertura);

        // Inicializar variables en cada iteración
        $MONTO_EXCEDIDO = 0;
        // $TOTAL = 0;

        if ($montoPresupuesto < $montoGastoActual) {
            $MONTO_EXCEDIDO = $montoPresupuesto - $montoGastoActual;
            // $TOTAL = $montoPresupuesto - $montoGastoActual;
        }

        // Aquí insertar o actualizar la base de datos.
        DB::table('presupuesto_cuenta')->updateOrInsert(
            ['COD_CUENTA' => $codCuenta, 'COD_APERTURA' => $codApertura],
            [
                'MONTO_PRESUPUESTO' => $montoPresupuesto,
                'MONTO_EXCEDIDO' => $MONTO_EXCEDIDO,
                // 'TOTAL' => $TOTAL,
                'DESCRIPCION' => 'A descripción',
                'ACTIVO' => 1
            ]
        );
    }

}

//    public function aperturarmes(Request $request)
//    {
//     // Lógica para aperturar un nuevo mes
//     // Por ejemplo, crear una nueva entrada en la tabla 'apertura'
//     $nuevoMes = $request->mes;
//     $nuevoAnio = $request->gestion;
    
//     // Aquí se agregaría la lógica específica para crear la nueva apertura
//     $apertura = DB::table('apertura')->insert([
//         'MES' => $nuevoMes,
//         'GESTION' => $nuevoAnio,
//         'ACTIVO' => 1,
//         'ESTADO' => 1
//         // 'created_at' => now(),
//         // 'updated_at' => now()
//     ]);

//     if ($apertura) {
//         return response()->json(['message' => 'El mes ha sido aperturado correctamente.'], 200);
//     } else {
//         return response()->json(['message' => 'Ocurrió un error al aperturar el mes.'], 500);
//     }
//    }

   public function aperturarmes(Request $request)
{
    $nuevoMes = $request->mes;
    $nuevoAnio = $request->gestion;
    
    // Obtener la última apertura (mes anterior)
    $ultimaApertura = DB::table('apertura')
        ->where('ACTIVO', 1)
        ->orderBy('GESTION', 'desc')
        ->orderBy('MES', 'desc')
        ->first();
    
    if (!$ultimaApertura) {
        return response()->json(['message' => 'No se encontró una apertura anterior.'], 404);
    }

    // Crear la nueva apertura
    $aperturaCreada = DB::table('apertura')->insertGetId([
        'MES' => $nuevoMes,
        'GESTION' => $nuevoAnio,
        'ACTIVO' => 1,
        'ESTADO' => 1
    ]);

    if (!$aperturaCreada) {
        return response()->json(['message' => 'Ocurrió un error al aperturar el mes.'], 500);
    }

    // Copiar todas las cuentas del mes anterior a la nueva apertura
    $cuentasAnteriores = DB::table('presupuesto_cuenta')
        ->where('COD_APERTURA', $ultimaApertura->COD_APERTURA)
        ->where('ACTIVO', 1)
        ->get();

    foreach ($cuentasAnteriores as $cuenta) {
        DB::table('presupuesto_cuenta')->insert([
            'COD_CUENTA' => $cuenta->COD_CUENTA,
            'COD_APERTURA' => $aperturaCreada,
            'MONTO_PRESUPUESTO' => $cuenta->MONTO_PRESUPUESTO,
            'MONTO_EXCEDIDO' => $cuenta->MONTO_EXCEDIDO,
            'DESCRIPCION' => $cuenta->DESCRIPCION,
            'ACTIVO' => 1
        ]);
    }

    return response()->json(['message' => 'El mes ha sido aperturado correctamente y las cuentas del mes anterior han sido copiadas.'], 200);
}


   public function irUltimaApertura()
   {
    // Obtener la última apertura
    $ultimaApertura = DB::table('apertura')
        ->where('ACTIVO', 1)
        ->orderBy('GESTION', 'desc')
        ->orderBy('MES', 'desc')
        ->first();

    if ($ultimaApertura) {
        return response()->json($ultimaApertura, 200);
    } else {
        return response()->json(['message' => 'No se encontró la última apertura.'], 404);
    }
   }





// public function quitarCuenta(Request $request)
// {
//     $codCuenta = $request->input('codCuenta');
//     $codApertura = $request->input('codApertura');

//     // Aquí asumimos que tienes un modelo PresupuestoCuenta que representa la tabla 'presupuesto_cuenta'
//     // y que 'COD_CUENTA' y 'COD_APERTURA' son las claves para identificar un registro único
//     $presupuestoCuenta = PresupuestoCuenta::where('COD_CUENTA', $codCuenta)
//                                            ->where('COD_APERTURA', $codApertura)
//                                            ->first();

//     if ($presupuestoCuenta) {
//         $presupuestoCuenta->ACTIVO = 0; // Marcar como inactivo
//         $presupuestoCuenta->save();

//         return response()->json(['message' => 'Cuenta quitada con éxito.']);
//     } else {
//         return response()->json(['message' => 'Cuenta no encontrada.'], 404);
//     }
// }

public function quitarCuenta(Request $request)
{
    $codCuenta = $request->input('codCuenta');
    $mes = $request->input('mes');
    $anio = $request->input('anio');

    // Obtener el código de apertura basado en el mes y tipo de movimiento
    $apertura = DB::table('apertura')
                    ->where('MES', $mes)
                    ->where('GESTION', $anio)
                    ->where('ACTIVO', 1)
                    ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    // Actualizar el mes a ACTIVO = 0 para la cuenta y apertura especificadas
    DB::table('presupuesto_cuenta')
        ->where('COD_CUENTA', $codCuenta)
        ->where('COD_APERTURA', $codApertura)
        ->update(['ACTIVO' => 0]);

    return response()->json(['message' => 'Cuenta quitada con éxito.']);
}




// public function listaAsignarP(Request $request)
// {
//     $anio = $request->anio;
//     $mes = $request->mes;
//     $busqueda = $request->searchData;

//     // Obtener la apertura 
//     $apertura = DB::table('apertura')
//         ->where('MES', $mes)
//         ->where('GESTION', $anio)
//         ->where('ACTIVO', 1)
//         ->first();

//     if (!$apertura) {
//         return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
//     }

//     $codApertura = $apertura->COD_APERTURA;

//     // Aquí asumimos que quieres paginar los resultados
//     // Cambio principal: usar paginate() en lugar de get()
    
//     $cuentas = DB::table('cuenta as C')
//         ->select('C.COD_CUENTA as codigo', 'C.DESCRIPCION as descripcion', 
//                  'n.DESCRIPCION as nrocuenta', 'F.DESCRIPCION as fondo', 
//                  'pc.MONTO_PRESUPUESTO as montoPresupuesto', 
//                  DB::raw('ROUND(SUM(dg.MONTO), 3) as montoGastoF'),
//                  DB::raw('ROUND(pc.MONTO_PRESUPUESTO - SUM(dg.MONTO), 3) as saldoActual'),
//                 //  DB::raw('SUM(dg.MONTO) as montoGastoF'),
//                 //  DB::raw('pc.MONTO_PRESUPUESTO - SUM(dg.MONTO) as saldoActual'),
//                  'pc.COD_APERTURA as codApertura')
//         ->leftJoin('nro_cuenta as n', 'C.NRO_CUENTA', '=', 'n.CODNUM')
//         ->leftJoin('fin_fondo as F', 'C.COD_FONDO', '=', 'F.COD_FONDO')
//         ->leftJoin('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
//         ->leftJoin('detalle_gasto as dg', function($join) use ($codApertura) {
//             $join->on('C.COD_CUENTA', '=', 'dg.COD_CUENTA')
//                  ->where('dg.COD_APERTURA', '=', $codApertura)
//                  ->where('dg.ACTIVO', '=', 1)
//                  ->where('dg.CATEGORIA', '=', 'F');
//         })
//         ->where('C.ACTIVO', '=', 1)
//         ->where('pc.COD_APERTURA', '=', $codApertura);
//         ->groupBy('n.DESCRIPCION','C.COD_CUENTA', 'C.DESCRIPCION', 'F.DESCRIPCION', 'pc.MONTO_PRESUPUESTO', 'pc.COD_APERTURA')
//         ->orderBy('n.DESCRIPCION', 'asc')
//         ->paginate(10); // Aquí puedes ajustar el número de elementos por página a tus necesidades

//     // La respuesta ya incluye tanto los datos como la información de paginación
//     $cuentasProcesadas = $this->procesarResultados($cuentas);
//     // return response()->json($cuentas);
//     return response()->json($cuentasProcesadas);
// }

public function listaAsignarP2(Request $request)
{
    $anio = $request->anio;
    $mes = $request->mes;

    // Obtener la apertura
    $apertura = DB::table('apertura')
        ->where('MES', $mes)
        ->where('GESTION', $anio)
        ->where('ACTIVO', 1)
        ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    // Aquí asumimos que quieres paginar los resultados
    // Cambio principal: usar paginate() en lugar de get()
    
    $cuentas = DB::table('cuenta as C')
        ->select('C.COD_CUENTA as codigo', 'C.DESCRIPCION as descripcion', 
                 'n.DESCRIPCION as nrocuenta', 'F.DESCRIPCION as fondo', 
                 'pc.MONTO_PRESUPUESTO as montoPresupuesto', 'pc.DESCRIPCION as observacion',
                 DB::raw('ROUND(SUM(dg.MONTO), 3) as montoGastoF'),
                 DB::raw('ROUND(pc.MONTO_PRESUPUESTO - SUM(dg.MONTO), 3) as saldoActual'),
                //  DB::raw('SUM(dg.MONTO) as montoGastoF'),
                //  DB::raw('pc.MONTO_PRESUPUESTO - SUM(dg.MONTO) as saldoActual'),
                 'pc.COD_APERTURA as codApertura','pc.MONTO_EXCEDIDO as MONTO_EXCEDIDO')
        ->leftJoin('nro_cuenta as n', 'C.NRO_CUENTA', '=', 'n.CODNUM')
        ->leftJoin('fin_fondo as F', 'C.COD_FONDO', '=', 'F.COD_FONDO')
        ->leftJoin('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
        ->leftJoin('detalle_gasto as dg', function($join) use ($codApertura) {
            $join->on('C.COD_CUENTA', '=', 'dg.COD_CUENTA')
                 ->where('dg.COD_APERTURA', '=', $codApertura)
                 ->where('dg.ACTIVO', '=', 1);
                //  ->where('dg.CATEGORIA', '=', 'F');
        })
        // ->where('C.ACTIVO', '=', 1)
        ->where('pc.ACTIVO', 1)
        ->where('pc.COD_APERTURA', '=', $codApertura)
        ->groupBy('n.DESCRIPCION','C.COD_CUENTA', 'C.DESCRIPCION', 'F.DESCRIPCION', 'pc.MONTO_PRESUPUESTO', 'pc.COD_APERTURA', 'pc.DESCRIPCION')
        ->orderBy('n.DESCRIPCION', 'asc')
        ->paginate(1000); // Aquí puedes ajustar el número de elementos por página a tus necesidades

    // La respuesta ya incluye tanto los datos como la información de paginación
    $cuentasProcesadas = $this->procesarResultados($cuentas);
    // return response()->json($cuentas);
    return response()->json($cuentasProcesadas);
}

public function procesarResultados($cuentas)
{
    foreach ($cuentas->items() as $cuenta) {
        // Ajustar a 2 decimales y formato de miles y decimales según lo solicitado
        $cuenta->montoGastoF = $this->formatearNumero($cuenta->montoGastoF);
        $cuenta->saldoActual = $this->formatearNumero($cuenta->saldoActual);
    }

    return $cuentas;
}

// public function formatearNumero($numero)
// {
//     // Formatea el número a 3 decimales, usa punto como separador decimal y coma para miles
//     return number_format($numero, 2, ',', '.');
// }


public function actualizarPresupuesto(Request $request)
{
    $anio = $request->anio;
    $mes = $request->mes;

    // Obtener la apertura
    $apertura = DB::table('apertura')
        ->where('MES', $mes)
        ->where('GESTION', $anio)
        ->where('ACTIVO', 1)
        ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    $validatedData = $request->validate([
        'codCuenta' => 'required|integer',
        'montoPresupuesto' => 'required|numeric',
        'MONTO_EXCEDIDO' => 'required|numeric',
        // 'codApertura' => 'required|integer',
    ]);

    // Encuentra el presupuesto de la cuenta para la apertura específica
    $presupuestoCuenta = DB::table('presupuesto_cuenta')
                            ->where('COD_CUENTA', $validatedData['codCuenta'])
                            ->where('COD_APERTURA', $codApertura)
                            ->first();

    if (!$presupuestoCuenta) {
        return response()->json(['message' => 'Presupuesto no encontrado.'], 404);
    }

    // Actualiza el presupuesto
    DB::table('presupuesto_cuenta')
        ->where('COD_CUENTA', $validatedData['codCuenta'])
        ->where('COD_APERTURA', $codApertura)
        ->update([
            'MONTO_PRESUPUESTO' => $validatedData['montoPresupuesto'],
            'MONTO_EXCEDIDO' => $validatedData['MONTO_EXCEDIDO']
        ]);
        // ->update(['MONTO_PRESUPUESTO' => $validatedData['montoPresupuesto']]);

    return response()->json(['message' => 'Presupuesto actualizado con éxito.']);
}




public function crearApertura(Request $request) {
    $mes = $request->input('mes');
    $gestion = $request->input('gestion');

    $apertura = new Apertura();
    $apertura->MES = $mes;
    $apertura->GESTION = $gestion;
    $apertura->ACTIVO = 1; // Asumiendo que ACTIVO indica que la apertura está habilitada
    $apertura->ESTADO = 1;
    $apertura->save();

    return response()->json(['mensaje' => 'Apertura creada con éxito.', 'apertura' => $apertura]);
}

    public function listarCuentasPorApertura($codApertura)
    {
        $cuentas = DB::table('cuenta as c')
                    ->join('presupuesto_cuenta as pc', 'c.COD_CUENTA', '=', 'pc.COD_CUENTA')
                    ->where('pc.COD_APERTURA', $codApertura)
                    ->select('c.COD_CUENTA', 'c.DESCRIPCION', 'pc.MONTO_PRESUPUESTO')
                    ->get();

        return response()->json($cuentas);
    }
    public function cuentasAsignadas($aperturaId)
{
    // Asumimos que tienes una relación entre Apertura y Cuenta
    // a través de una tabla intermedia, por ejemplo, 'presupuesto_cuenta'
    $cuentas = DB::table('presupuesto_cuenta as pc')
        ->join('cuenta as c', 'pc.COD_CUENTA', '=', 'c.COD_CUENTA')
        ->join('nro_cuenta as nc', 'c.NRO_CUENTA', '=', 'nc.CODNUM')
        ->join('fin_fondo as ff', 'c.COD_FONDO', '=', 'ff.COD_FONDO')
        ->where('pc.COD_APERTURA', $aperturaId)
        ->select('c.COD_CUENTA', 'c.DESCRIPCION as descripcionCuenta', 'nc.DESCRIPCION as numeroCuenta', 'ff.DESCRIPCION as fondo', 'pc.MONTO_PRESUPUESTO')
        ->get();

    if ($cuentas->isEmpty()) {
        return response()->json(['message' => 'No se encontraron cuentas asignadas a esta apertura.'], 404);
    }

    return response()->json($cuentas);
}
public function guardarCuentasConApertura(Request $request)
{
    $validated = $request->validate([
        'cuentas' => 'required|array',
        'cuentas.*.codCuenta' => 'required|integer',
        'cuentas.*.montoPresupuesto' => 'required|numeric',
        'codApertura' => 'required|integer',
    ]);

    $cuentas = $validated['cuentas'];
    $codApertura = $validated['codApertura'];

    foreach ($cuentas as $cuenta) {
        // Asegúrate de que el modelo PresupuestoCuenta esté correctamente definido para interactuar con tu tabla
        $presupuestoCuenta = new PresupuestoCuenta();
        $presupuestoCuenta->cod_cuenta = $cuenta['codCuenta'];
        $presupuestoCuenta->cod_apertura = $codApertura;
        $presupuestoCuenta->monto_presupuesto = $cuenta['montoPresupuesto'];
        // Añade aquí cualquier otra propiedad necesaria
        $presupuestoCuenta->save();
    }

    return response()->json(['message' => 'Cuentas guardadas con éxito.']);
}

public function exportarExcel(Request $request)
{
    $mes = $request->input('mes');
    $anio = $request->input('tipoMovimiento');

    // Verifica que los parámetros se están recibiendo correctamente
    if (is_null($mes) || is_null($anio)) {
        return response()->json(['error' => 'Faltan parámetros requeridos'], 400);
    }

    // Obtener el nombre del mes en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    $nombreMes = $meses[$mes] ?? 'Mes_Desconocido';
    $nombreArchivo = "PresupuestoAsignadoGlobal-{$nombreMes}-{$anio}.xlsx";

    $export = new AsignarPresupuestoGExport(
        $request->input('buscar'),
        $anio, // Asegúrate de pasar 'anio' en lugar de 'tipoMovimiento' si corresponde
        $mes
    );

    return Excel::download($export, $nombreArchivo);
}



    // public function anadirCuentaApertura(Request $request)
    // {
    //     $request->validate([
    //         'codCuenta' => 'required|integer',
    //         'codApertura' => 'required|integer',
    //         'montoPresupuesto' => 'required|numeric',
    //     ]);

    //     $codCuenta = $request->input('codCuenta');
    //     $codApertura = $request->input('codApertura');
    //     $montoPresupuesto = $request->input('montoPresupuesto');

    //     // Añade la cuenta a la apertura
    //     $presupuestoCuenta = DB::table('presupuesto_cuenta')->insert([
    //         'COD_CUENTA' => $codCuenta,
    //         'COD_APERTURA' => $codApertura,
    //         'MONTO_PRESUPUESTO' => $montoPresupuesto,
    //     ]);

    //     return response()->json(['message' => 'Cuenta añadida a la apertura con éxito.']);
    // }

    public function verificarApertura(Request $request) {
        $mes = $request->input('mes');
        $gestion = $request->input('gestion');
    
        $apertura = Apertura::where('MES', $mes)
                            ->where('GESTION', $gestion)
                            ->where('ACTIVO', 1)
                            ->first();
    
        if ($apertura) {
            return response()->json(['existeApertura' => true, 'apertura' => $apertura]);
        } else {
            return response()->json(['existeApertura' => false, 'mensaje' => 'No se encontró la apertura.']);
        }
    }

// public function cuentas(Request $request)
// {
//     $terminoBusqueda = $request->input('term', ''); // Cambio aquí para usar 'term'

//     $cuentas = Cuenta::where('DESCRIPCION', 'LIKE', '%' . $terminoBusqueda . '%')
//               ->where('ACTIVO', 1)
//               ->get(['COD_CUENTA as id', 'DESCRIPCION']); // Cambio aquí para que el key sea 'id'

//     return response()->json($cuentas);
// }
// public function cuentas(Request $request)
// {
//     $terminoBusqueda = $request->input('term', '');

//     $cuentas = Cuenta::where('Cuenta.DESCRIPCION', 'LIKE', '%' . $terminoBusqueda . '%')
//         ->where('Cuenta.ACTIVO', 1)
//         ->leftJoin('nro_cuenta as nc', 'Cuenta.NRO_CUENTA', '=', 'nc.CODNUM')
//         ->leftJoin('fin_fondo as ff', 'Cuenta.COD_FONDO', '=', 'ff.COD_FONDO')
//         ->get([
//             'Cuenta.COD_CUENTA as id', 
//             'Cuenta.DESCRIPCION', 
//             'nc.DESCRIPCION as nrocuenta', 
//             'ff.DESCRIPCION as fondo'
//         ]);

//     return response()->json($cuentas);
// }

public function cuentas(Request $request)
{
    $terminoBusqueda = $request->input('term', '');

    $cuentas = DB::table('cuenta as C')
        ->where('C.DESCRIPCION', 'LIKE', '%' . $terminoBusqueda . '%')
        ->where('C.ACTIVO', 1)
        ->leftJoin('nro_cuenta as nc', 'C.NRO_CUENTA', '=', 'nc.CODNUM')
        ->leftJoin('fin_fondo as ff', 'C.COD_FONDO', '=', 'ff.COD_FONDO')
        ->get([
            'C.COD_CUENTA as id', 
            'C.DESCRIPCION', 
            'nc.DESCRIPCION as nrocuenta', 
            'ff.DESCRIPCION as fondo'
        ]);

    return response()->json($cuentas);
}





// public function anadirCuentaApertura(Request $request)
// {
//     $request->validate([
//         'codCuenta' => 'required|integer',
//         'codApertura' => 'required|integer',
//         'montoPresupuesto' => 'required|numeric',
//     ]);

//     $codCuenta = $request->codCuenta;
//     $codApertura = $request->codApertura;
//     $montoPresupuesto = $request->montoPresupuesto;

//     // Intenta encontrar una entrada existente y actualízala o crea una nueva
//     $presupuestoCuenta = DB::table('presupuesto_cuenta')
//         ->updateOrInsert(
//             ['COD_CUENTA' => $codCuenta, 'COD_APERTURA' => $codApertura],
//             ['MONTO_PRESUPUESTO' => $montoPresupuesto, 'ACTIVO' => 1]
//         );

//     return response()->json(['message' => 'Cuenta añadida a la apertura con éxito.']);
// }

public function anadirCuentaApertura(Request $request)
{
    $anio = $request->anio;
    $mes = $request->mes;

    // Obtener la apertura
    $apertura = DB::table('apertura')
        ->where('MES', $mes)
        ->where('GESTION', $anio)
        ->where('ACTIVO', 1)
        ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    $request->validate([
        'codCuenta' => 'required|integer',
    ]);

    $codCuenta = $request->codCuenta;
    $montoPresupuesto = 0; // Suponiendo que el monto inicial es 0
    $descripcion = " ";

    // Verifica si ya existe una cuenta con ese código en la apertura
    $existeCuenta = DB::table('presupuesto_cuenta')
        ->where('COD_CUENTA', $codCuenta)
        ->where('COD_APERTURA', $codApertura)
        ->exists();

    if ($existeCuenta) {
        return response()->json(['message' => 'La cuenta ya está insertada en la apertura.'], 409); // Código 409 indica un conflicto.
    }

    // Si no existe, inserta la nueva cuenta
    DB::table('presupuesto_cuenta')->insert([
        'COD_CUENTA' => $codCuenta,
        'COD_APERTURA' => $codApertura,
        'MONTO_PRESUPUESTO' => $montoPresupuesto,
        'DESCRIPCION' => $descripcion,
        'ACTIVO' => 1
    ]);

    return response()->json(['message' => 'Cuenta añadida a la apertura con éxito.']);
}

// public function guardarMasivo(Request $request)
// {
//     $cuentas = $request->input('cuentas', []); // Las cuentas a guardar o actualizar.
//     $cuentasAQuitar = $request->input('cuentasAQuitar', []); // Los códigos de las cuentas a quitar.
//     $anio = $request->input('anio');
//     $mes = $request->input('mes');

//     // Obtener la apertura basada en el mes, año y mes activo.
//     $apertura = DB::table('apertura')
//         ->where('MES', $mes)
//         ->where('GESTION', $anio)
//         ->where('ACTIVO', 1)
//         ->first();

//     if (!$apertura) {
//         return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
//     }

//     $codApertura = $apertura->COD_APERTURA;

//     foreach ($cuentas as $cuenta) {
//         if (isset($cuenta['codigo'])) {
//             $codCuenta = $cuenta['codigo'];
//             $montoPresupuesto = $cuenta['montoPresupuesto'];

//             // Insertar o actualizar la base de datos con los datos de la cuenta.
//             DB::table('presupuesto_cuenta')->updateOrInsert(
//                 ['COD_CUENTA' => $codCuenta, 'COD_APERTURA' => $codApertura],
//                 ['MONTO_PRESUPUESTO' => $montoPresupuesto, 'DESCRIPCION' => 'Descripción actualizada', 'ACTIVO' => 1]
//             );
//         }
//     }

//     // Procesar las cuentas a quitar.
//     foreach ($cuentasAQuitar as $codCuentaQuitar) {
//         // Aquí actualizamos el mes a ACTIVO = 0 para las cuentas a quitar.
//         DB::table('presupuesto_cuenta')
//             ->where('COD_CUENTA', $codCuentaQuitar)
//             ->where('COD_APERTURA', $codApertura)
//             ->update(['ACTIVO' => 0]);
//     }

//     return response()->json(['message' => 'Operación realizada con éxito.']);
// }

public function guardarMasivo(Request $request)
{
    $cuentas = $request->input('cuentas');
    $anio = $request->input('anio');
    $mes = $request->input('mes');

    // Obtener la apertura
    $apertura = DB::table('apertura')
        ->where('MES', $mes)
        ->where('GESTION', $anio)
        ->where('ACTIVO', 1)
        ->first();

    if (!$apertura) {
        return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
    }

    $codApertura = $apertura->COD_APERTURA;

    // Iniciar la transacción
    DB::beginTransaction();

    try {
        // Recolectar los nuevos datos a insertar/actualizar
        $newData = [];
        $cuentasCodigos = [];

        // Validar y limpiar datos
        foreach ($cuentas as $cuenta) {
            $codCuenta = $cuenta['codigo'];
            $montoPresupuesto = $cuenta['montoPresupuesto'];
            $MONTO_EXCEDIDO = $cuenta['MONTO_EXCEDIDO'];
            $observacion = $cuenta['observacion'] ?? ' ';

            // Preparar los datos para inserción/actualización
            $newData[] = [
                'COD_CUENTA' => $codCuenta,
                'COD_APERTURA' => $codApertura,
                'MONTO_PRESUPUESTO' => $montoPresupuesto,
                'MONTO_EXCEDIDO' => $MONTO_EXCEDIDO,
                'DESCRIPCION' => $observacion,
                'ACTIVO' => 1
            ];
            $cuentasCodigos[] = $codCuenta;
        }

        // Insertar o actualizar los nuevos datos
        foreach ($newData as $data) {
            DB::table('presupuesto_cuenta')->updateOrInsert(
                ['COD_CUENTA' => $data['COD_CUENTA'], 'COD_APERTURA' => $data['COD_APERTURA']],
                [
                    'MONTO_PRESUPUESTO' => $data['MONTO_PRESUPUESTO'],
                    'MONTO_EXCEDIDO' => $data['MONTO_EXCEDIDO'],
                    // 'total' => $data['total'],
                    'DESCRIPCION' => $data['DESCRIPCION'],
                    'ACTIVO' => 1
                ]
            );
        }

        // Desactivar las cuentas que no están en la lista actualizada
        DB::table('presupuesto_cuenta')
            ->where('COD_APERTURA', $codApertura)
            ->whereNotIn('COD_CUENTA', $cuentasCodigos)
            ->update(['ACTIVO' => 0]);

        // Confirmar la transacción
        DB::commit();

        return response()->json(['message' => 'Presupuestos guardados con éxito.']);
    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        return response()->json(['message' => 'Error al guardar los presupuestos. Ningún cambio fue realizado.'], 500);
    }
}



// public function guardarMasivo(Request $request)
// {
//     $cuentas = $request->input('cuentas');
//     $anio = $request->input('anio');
//     $mes = $request->input('mes');

//     // Obtener la apertura
//     $apertura = DB::table('apertura')
//         ->where('MES', $mes)
//         ->where('GESTION', $anio)
//         ->where('ACTIVO', 1)
//         ->first();

//     if (!$apertura) {
//         return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
//     }

//     $codApertura = $apertura->COD_APERTURA;
// // Aquí insertar o actualizar la base de datos.

// DB::table('presupuesto_cuenta')
//                 ->where('COD_APERTURA', $codApertura)
//                 ->update(['ACTIVO' => 0]);

//     foreach ($cuentas as $cuenta) {
//         // Asegúrate de validar o limpiar adecuadamente los datos aquí.
//         $codCuenta = $cuenta['codigo'];
//         $montoPresupuesto = $cuenta['montoPresupuesto'];
//         $MONTO_EXCEDIDO = $cuenta['MONTO_EXCEDIDO'];
//         $observacion = $cuenta['observacion'] ?? ' '; // Asigna un espacio si es nulo
//         // $observacion = $cuenta['observacion'];


//         $montoGastoActual = $this->gastoactual($cuenta['codigo'], $codApertura);

//         // Inicializar variables en cada iteración
//         // $TOTAL = 0;

//         // if ($montoPresupuesto < $montoGastoActual) {
//         //     $MONTO_EXCEDIDO = $montoPresupuesto - $montoGastoActual;
//         //     $TOTAL = $montoPresupuesto - $montoGastoActual;
//         // }

//         // Aquí insertar o actualizar la base de datos.
//         DB::table('presupuesto_cuenta')->updateOrInsert(
//             ['COD_CUENTA' => $codCuenta, 'COD_APERTURA' => $codApertura],
//             [
//                 'MONTO_PRESUPUESTO' => $montoPresupuesto,
//                 'MONTO_EXCEDIDO' => $MONTO_EXCEDIDO,
//                 // 'TOTAL' => $TOTAL,
//                 'DESCRIPCION' => $observacion,
//                 'ACTIVO' => 1
//             ]
//         );
//     }

//     return response()->json(['message' => 'Presupuestos guardados con éxito.']);
// }




// public function anadirCuentaApertura(Request $request)
// {
//     $anio = $request->anio;
//     $mes = $request->mes;

//     // Obtener la apertura
//     $apertura = DB::table('apertura')
//         ->where('MES', $mes)
//         ->where('GESTION', $anio)
//         ->where('ACTIVO', 1)
//         ->first();

//     if (!$apertura) {
//         return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados.'], 404);
//     }

//     $codApertura = $apertura->COD_APERTURA;
    
//     $request->validate([
//         'codCuenta' => 'required|integer',
//         // 'codApertura' => 'required|integer',
//         // 'montoPresupuesto' => 'required|numeric',
//     ]);

//     $codCuenta = $request->codCuenta;
//     // $codApertura = $request->codApertura;
//     // $montoPresupuesto = $request->montoPresupuesto;
//     $montoPresupuesto = 0 ;
//     $descripcion = " ";

//     // Intenta encontrar una entrada existente y actualízala o crea una nueva
//     $presupuestoCuenta = DB::table('presupuesto_cuenta')
//         ->updateOrInsert(
//             ['COD_CUENTA' => $codCuenta, 'COD_APERTURA' => $codApertura],
//             ['MONTO_PRESUPUESTO' => $montoPresupuesto, 'DESCRIPCION' => $descripcion,'ACTIVO' => 1]
//         );

//     return response()->json(['message' => 'Cuenta añadida a la apertura con éxito.']);
// }


}