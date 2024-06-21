<?php
namespace App\Http\Controllers\ControlPresupuestado;

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
use App\Exports\ControlPresupuestado\PresupuestoGExport;
use App\Exports\ControlPresupuestado\PresupuestoDetalleExport;

class ControlPresupuestadoGlobalController extends ApiController
{

    public function listaPGlobal(Request $request)
    {
        $anio = $request->tipoMovimiento;
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
            ->leftJoin('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
            ->select('C.COD_CUENTA as codigo', 'C.DESCRIPCION as descripcion', 'pc.DESCRIPCION as observacion', 'n.DESCRIPCION as nrocuenta', 'F.DESCRIPCION as fondo',
             'pc.MONTO_PRESUPUESTO as montoPresupuesto','pc.MONTO_EXCEDIDO as MONTO_EXCEDIDO')
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
            $item->MONTO_EXCEDIDO = $item->MONTO_EXCEDIDO;
            $item->saldoActual = ($item->montoPresupuesto + $item->MONTO_EXCEDIDO) - $item->montoGastoF;

            $presupuestoTotal = $item->montoPresupuesto + $item->MONTO_EXCEDIDO;
            $item->porcentaje = $presupuestoTotal > 0 ? ($item->montoGastoActual / $presupuestoTotal) * 100 : 0;
            // Separador de milessi 
            $item->montoGastoF = number_format($item->montoGastoF, 2, ',','.');
            $item->montoPresupuesto = number_format($item->montoPresupuesto, 2, ',','.');
            $item->MONTO_EXCEDIDO = number_format($item->MONTO_EXCEDIDO, 2, ',','.');
             // $item->saldoActual = number_format($item->saldoActual, 2, '.','');
            $item->saldoActual = number_format($item->saldoActual, 2, ',','.');
           
            
            // if ($item->montoPresupuesto >=  $item->montoGastoF ) {
            // $item->saldoActual = $item->montoPresupuesto - $item->montoGastoF;
            // $item->saldoActual = number_format($item->saldoActual, 2, '.','');
            // }
            // if ($item->montoPresupuesto <  $item->montoGastoF ) {
            //     $item->EXCEDIDO = $item->montoPresupuesto - $item->montoGastoF;
            //     $item->TOTALM = $item->montoPresupuesto - $item->montoGastoF;
            // }
            // // Formatear a 2 decimales
            // $item->montoGastoF = number_format($item->montoGastoF, 2, '.','');
           
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

   public function listaPGlobalDetalle(Request $request)
   {
       $anio = $request->tipoMovimiento;
       $mes = $request->mes;
       $codigo = $request->codigo;
       $busqueda = $request->buscar;
   
       // Obtener la apertura
       $apertura = DB::table('apertura')
           ->where('MES', $mes)
           ->where('GESTION', $anio)
           ->where('ACTIVO', 1)
           ->first();
   
       if (!$apertura) {
           return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados en el filtro.', 'action' => 'APERTURAR_MES'], 404);
       }
   
       $codApertura = $apertura->COD_APERTURA;
   
       // Prepara la consulta base
       $query = DB::table('detalle_gasto as dg')
           ->join('z_solicitud_gasto as a', 'a.cod_sg', '=', 'dg.COD_SG')
           ->select('dg.COD_DETALLE AS codigod', 'dg.DESCRIPCION as descripcion', 'dg.MONTO as montoGasto', 'dg.CATEGORIA as categoria', 'a.tipo_pago as tipoPago', 'dg.FECHA as fechaGasto')
           ->where('dg.COD_APERTURA', $codApertura)
           ->where('dg.COD_CUENTA', $codigo)  // Usar el código de cuenta proporcionado
           ->where('dg.ACTIVO', 1)
           ->orderBy('dg.FECHA', 'asc');
   
       // Aplica búsqueda condicional si es necesario
       if (!empty($busqueda)) {
           $query->where(function($q) use ($busqueda) {
               $q->where('dg.DESCRIPCION', 'LIKE', "%{$busqueda}%");
           });
       }
   
       // Ejecuta la consulta y pagina los resultados
       $resultados = $query->paginate(10); // Paginar con 20 resultados por página
       $resultados->getCollection()->transform(function($item) {
        $item->montoGasto = number_format($item->montoGasto, 2, '.', ',');
        // Transformar tipo_pago a su valor numérico correspondiente
        switch ($item->tipoPago) {
            case 'TRANSFERENCIA':
                $item->tipoPago = 1;
                break;
            case 'EFECTIVO':
                $item->tipoPago = 2;
                break;
            case 'CHEQUE':
                $item->tipoPago = 3;
                break;
        }
        return $item;
    });
   
       return response()->json($resultados);
   }

   public function exportarExcel(Request $request)
    {
        $mes = $request->mes;
        $anio = $request->tipoMovimiento;

        // Obtener el nombre del mes en español
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $nombreMes = $meses[$mes] ?? 'Mes_Desconocido';
        $nombreArchivo = "ControlPresupuestadoGlobal-{$nombreMes}-{$anio}.xlsx";

        $export = new PresupuestoGExport(
            $request->buscar,
            $request->tipoMovimiento,
            $request->mes
        );

        return Excel::download($export, $nombreArchivo);
    }

    public function exportarDetalle(Request $request)
{
    $codigo = $request->codigo;
    $mes = $request->mes;
    $anio = $request->tipoMovimiento;

    // Obtener el nombre del mes en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    $nombreMes = $meses[$mes] ?? 'Mes_Desconocido';
    $nombreArchivo = "DetalleCuentaGastos-{$nombreMes}-{$anio}.xlsx";

    $export = new PresupuestoDetalleExport(
        $codigo,
        $anio,
        $mes
    );

    return Excel::download($export, $nombreArchivo);
}


   


//    public function listaPGlobalDetalle(Request $request)
// {
//     $anio = 2024;
//     $mes = 6;
//     $codigo = $request->codigo;

//     // Obtener la apertura
//     $apertura = DB::table('apertura')
//         ->where('MES', $mes)
//         ->where('GESTION', $anio)
//         ->where('ACTIVO', 1)
//         ->first();

//     if (!$apertura) {
//         return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados en el filtro.', 'action' => 'APERTURAR_MES'], 404);
//     }

//     $codApertura = $apertura->COD_APERTURA;

//     // Prepara la consulta base
//     $query = DB::table('detalle_gasto as dg')
//         ->select('dg.COD_DETALLE AS codigod', 'dg.DESCRIPCION as descripcion', 'dg.MONTO as montoGasto', 'dg.CATEGORIA as categoria', 'dg.TIPO_PAGO as tipoPago', 'dg.FECHA as fechaGasto')
//         ->where('dg.COD_APERTURA', $codApertura)
//         ->where('dg.COD_CUENTA', $codigo)  // Usar el código de cuenta proporcionado
//         ->where('dg.ACTIVO', 1)
//         ->orderBy('dg.FECHA', 'asc');

//     // Ejecuta la consulta
//     $resultados = $query->get();

//     return response()->json($resultados);
// }


//    public function listaPGlobalDetalle(Request $request)
//    {
//     //    $anio = $request->tipoMovimiento;
//     //    $mes = $request->mes;

//        $anio = 2024;
//        $mes =6;

//        $codigo = $request->codigo;

//        $busqueda = $request->buscar;
   
//        // Obtener la apertura
//        $apertura = DB::table('apertura')
//            ->where('MES', $mes)
//            ->where('GESTION', $anio)
//            ->where('ACTIVO', 1)
//            ->first();
   
//        if (!$apertura) {
//            return response()->json(['message' => 'No se encontró la apertura para el mes y año especificados en el filtro.','action' => 'APERTURAR_MES'
//        ], 404);
           
//        }
   
//        $codApertura = $apertura->COD_APERTURA;
   
//        // Prepara la consulta base
//        $query = DB::table('detalle_gasto as dg')
//         //    ->join('nro_cuenta as n', 'C.NRO_CUENTA', '=', 'n.CODNUM')
//         //    ->join('fin_fondo as F', 'C.COD_FONDO', '=', 'F.COD_FONDO')
//         //    ->leftJoin('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
//            ->select('dg.COD_DETALLE AS codigod', 'dg.DESCRIPCION as descripcion', 'dg.MONTO as montoGasto', 'dg.CATEGORIA as categoria', 'dg.TIPO_PAGO as tipoPago',
//             'dg.FECHA as fechaGasto')
//            ->where('dg.COD_APERTURA', $codApertura)
//            ->where('dg.COD_CUENTA', 145)
//            ->where('dg.ACTIVO', 1)
//            ->orderBy('dg.FECHA', 'asc');
   
//        // Aplica búsqueda condicional si es necesario
//        if (!empty($busqueda)) {
//            $query->where(function($q) use ($busqueda) {
//                $q->where('dg.DESCRIPCION', 'LIKE', "%{$busqueda}%");
//                 //  ->orWhere('F.DESCRIPCION', 'LIKE', "%{$busqueda}%");
//            });
//        }
   
//        // Ejecuta la consulta y pagina los resultados
//        $resultados = $query->paginate(100);
   
//     //    // Procesa los resultados para incluir gastos fijos y eventuales
//     //    $resultados->getCollection()->transform(function ($item) use ($codApertura) {
//     //        $codigo = $item->codigo;
//     //        $item->montoGastoActual = $this->gastoactual($codigo, $codApertura);
//     //        $item->montoGastoF = $item->montoGastoActual;

//     //        if ($item->montoPresupuesto >=  $item->montoGastoF ) {
//     //        $item->saldoActual = $item->montoPresupuesto - $item->montoGastoF;
//     //        $item->saldoActual = number_format($item->saldoActual, 2, '.','');
//     //        }
//     //        if ($item->montoPresupuesto <  $item->montoGastoF ) {
//     //            $item->EXCEDIDO = $item->montoPresupuesto - $item->montoGastoF;
//     //            $item->TOTALM = $item->montoPresupuesto - $item->montoGastoF;
//     //        }
//     //        // Formatear a 2 decimales
//     //        $item->montoGastoF = number_format($item->montoGastoF, 2, '.','');
//     //        return $item;
//     //    });
   
//        return response()->json($resultados);
//    }




public function procesarResultados($cuentas)
{
    foreach ($cuentas->items() as $cuenta) {
        // Ajustar a 2 decimales y formato de miles y decimales según lo solicitado
        $cuenta->montoGastoF = $this->formatearNumero($cuenta->montoGastoF);
        $cuenta->saldoActual = $this->formatearNumero($cuenta->saldoActual);
    }

    return $cuentas;
}


public function cuentas(Request $request)
{
    $terminoBusqueda = $request->input('term', ''); // Cambio aquí para usar 'term'

    $cuentas = Cuenta::where('DESCRIPCION', 'LIKE', '%' . $terminoBusqueda . '%')
              ->where('ACTIVO', 1)
              ->get(['COD_CUENTA as id', 'DESCRIPCION']); // Cambio aquí para que el key sea 'id'

    return response()->json($cuentas);
}

}