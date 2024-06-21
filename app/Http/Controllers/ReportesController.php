<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\OrdenTrabajo;
use Illuminate\Support\Str;

class ReportesController extends Controller
{
    public function obtDiasMes(Request $request)
    {
        $año = DB::select("SELECT LAST_DAY(CURDATE()) AS año");
        $año = Str::substr($año[0]->año, 1, 4); 
        $fecha = "'".$año."-".$request->mes."-01'";
        $dias = DB::select("SELECT LAST_DAY(".$fecha.") AS ultimoDia");
        $array = array();
        for ($i = 0; $i < 31; $i++) {
            $array[$i] = $i+1;
        }
        //return Str::substr($dias[0]->ultimoDia, 8, 2); 
        return $array;
    }

    public function obtMes(Request $request)
    {
        return DB::select("SELECT MONTH(CURDATE()) as mes"); 
    }
    //FORMATO 1
    public function listarDetalleReservasMes(Request $request)
    {
        //DATE_FORMAT(DATE_ADD(STR_TO_DATE(r.horaInicio, '%H:%i:%s'),INTERVAL r.tiempoServicio minute), '%H:%i:%s') hora
        //".$request->mes."
        
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            $verificar = DB::select("SELECT dr.codDetReserva,dr.codTrabajador,dr.codReserva,r.fecha,SUBSTRING(r.horaInicio, 1, 5) as horaInicio,r.tiempoServicio,
             DATE_FORMAT(DATE_ADD(r.horaInicio,INTERVAL r.tiempoServicio minute), '%H:%i:%s') as 'hora',dr.estadoTrabajador,r.codCliente,DAY(r.fecha) AS dia,CONCAT(p.NOMBRE,' ',p.AP_PATERNO,' ',p.AP_MATERNO) AS 'tecnico'
            FROM detallereserva dr INNER JOIN reserva r ON dr.codReserva=r.codReserva INNER JOIN personal p ON p.COD_PERSONAL=dr.codTrabajador 
            WHERE MONTH(r.fecha) = ".$request->mes." AND YEAR(r.fecha)=YEAR(CURDATE()) AND dr.estadoTrabajador=1 AND r.estado=1
            ORDER BY DAY(r.fecha),r.codCliente"); 
            return $verificar;
        }
        else{
            return "expiro";
        }

    }

    public function listarReservasMesF2(Request $request)
    {
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            $verificar = DB::select("SELECT distinct CONCAT(c.NOMBRE,' ',c.AP_CLIENTE,' ',c.AM_CLIENTE) AS cliente,c.COD_CLIENTE
            FROM reserva r INNER JOIN cliente c ON r.codCliente=c.COD_CLIENTE
            WHERE MONTH(r.fecha) = ".$request->mes." AND YEAR(r.fecha)=YEAR(CURDATE()) AND r.estado=1
            ORDER BY DAY(r.fecha)"); 
            return $verificar;
        }
        else{
            return "expiro";
        }
    }

    public function listarReservasMes(Request $request)
    {
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            $verificar = DB::select("SELECT CONCAT(c.NOMBRE,' ',c.AP_CLIENTE,' ',c.AM_CLIENTE) AS cliente,r.codReserva,r.horaInicio,r.tiempoServicio,r.tiempoTransporte,r.estado,r.estadoCoordinacion,r.fecha,ROUND((r.tiempoServicio/60),1) AS 'horas',r.codCliente,DAY(r.fecha) AS dia
            FROM reserva r INNER JOIN cliente c ON r.codCliente=c.COD_CLIENTE
            WHERE MONTH(r.fecha) = ".$request->mes." AND YEAR(r.fecha)=YEAR(CURDATE()) AND r.estado=1
            ORDER BY DAY(r.fecha)"); 
            return $verificar;
        }
        else{
            return "expiro";
        }
    }


    public function listadoOTEjecutivo(Request $request)
    {
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            $buscarOT = $request->buscar;
        if($request->fechaInicio=='' && $request->fechaFin=='')
        {
            $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
            ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
            ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
            'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
            'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
            'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA','orden_trabajo.ANULADA',
            'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
            ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL','orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')
            ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
            ->where('orden_trabajo.ACTIVO', 1)
            ->where(function ($query) use($buscarOT){
                $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscarOT.'%')
                        ->orWhere('cliente.TELEFONO','like','%'.$buscarOT.'%')
                        ->orWhere('cliente.CONTACTO','like','%'.$buscarOT.'%') 
                        ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscarOT.'%');
            })           
            ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
            ->paginate(20);
        }
        else
        {
            if($request->fechaInicio!='' && $request->fechaFin!='')
            {
                $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
                ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
                'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
                'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA','orden_trabajo.ANULADA',
                'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
                ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL','orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')
                ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                ->where('orden_trabajo.ACTIVO', 1)
                ->whereBetween('orden_trabajo.FECHA_SERVICIO', [$request->fechaInicio,$request->fechaFin])
                ->where(function ($query) use($buscarOT){
                    $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscarOT.'%')
                            ->orWhere('cliente.TELEFONO','like','%'.$buscarOT.'%')
                            ->orWhere('cliente.CONTACTO','like','%'.$buscarOT.'%') 
                            ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscarOT.'%');
                })  
                ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                ->paginate(20);
            }
            else
            {
                $fecha='';

                if($request->fechaInicio!='')
                    $fecha = $request->fechaInicio;
                else
                    $fecha = $request->fechaFin;

                $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
                ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
                'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
                'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA','orden_trabajo.ANULADA',
                'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
                ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL','orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')

                ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                ->where('orden_trabajo.ACTIVO', 1)
                ->where('orden_trabajo.FECHA_SERVICIO',$fecha)
                ->where(function ($query) use($buscarOT){
                    $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscarOT.'%')
                            ->orWhere('cliente.TELEFONO','like','%'.$buscarOT.'%')
                            ->orWhere('cliente.CONTACTO','like','%'.$buscarOT.'%') 
                            ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscarOT.'%');
                })                  
                ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                ->paginate(20);
            }
        }  
         
        

        return [
            'pagination' => [
                'total'        => $reporte->total(),
                'current_page' => $reporte->currentPage(),
                'per_page'     => $reporte->perPage(),
                'last_page'    => $reporte->lastPage(),
                'from'         => $reporte->firstItem(),
                'to'           => $reporte->lastItem(),
            ],
            'ot' => $reporte
        ];
        }
        else{
            return "expiro";
        }
    }


    public function listadoFrecuencia(Request $request)
    {   
        
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            $buscar =  $request->buscar;
            if($request->fechaInicio=='' && $request->fechaFin=='')
            {
                $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.ESTADO_FRECUENCIA',
                'orden_trabajo.FRECUENCIA',DB::raw('DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY) AS fechaFR'),
                DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY)) AS diasPFR'),
                'orden_trabajo.APROBADA','orden_trabajo.COBRADA',
                'orden_trabajo.NRO_ORDEN'
                ,'orden_trabajo.TIPO_PAGO')
                ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                ->where('orden_trabajo.ACTIVO', 1)
                ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'<=', 15)
                ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'>=', -7)
                ->where('orden_trabajo.ESTADO_FRECUENCIA', 1)
                ->where('orden_trabajo.ANULADA', 0)
                ->where('orden_trabajo.SUSPENDIDA', 0)
                ->where(function ($query) use($buscar){
                    $query->
                            where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscar.'%')
                            ->orWhere('cliente.TELEFONO','like','%'.$buscar.'%')
                            ->orWhere('cliente.CONTACTO','like','%'.$buscar.'%') 
                            ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscar.'%');
                })   
                
                ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                ->paginate(20);
            }
            else
            {
                if($request->fechaInicio!='' && $request->fechaFin!='')
                {
                    $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                    ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                    'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.ESTADO_FRECUENCIA',
                    'orden_trabajo.FRECUENCIA',DB::raw('DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY) AS fechaFR'),
                    DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY)) AS diasPFR'),
                    'orden_trabajo.APROBADA','orden_trabajo.COBRADA',
                    'orden_trabajo.NRO_ORDEN'
                    ,'orden_trabajo.TIPO_PAGO')
                    ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                    ->where('orden_trabajo.ACTIVO', 1)
                    ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'<=', 15)
                    ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'>=', -7)
                    ->where('orden_trabajo.ESTADO_FRECUENCIA', 1)
                    ->where('orden_trabajo.ANULADA', 0)
                    ->where('orden_trabajo.SUSPENDIDA', 0)
                    ->whereBetween('orden_trabajo.FECHA_SERVICIO', [$request->fechaInicio,$request->fechaFin])
                    ->where(function ($query) use($buscar){
                        $query->
                                where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscar.'%')
                                ->orWhere('cliente.TELEFONO','like','%'.$buscar.'%')
                                ->orWhere('cliente.CONTACTO','like','%'.$buscar.'%') 
                                ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscar.'%');
                    })  
                    ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                    ->paginate(20);
                }
                else
                {
                    $fecha='';

                    if($request->fechaInicio!='')
                        $fecha = $request->fechaInicio;
                    else
                        $fecha = $request->fechaFin;

                    $reporte = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                    ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                    'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.ESTADO_FRECUENCIA',
                    'orden_trabajo.FRECUENCIA',DB::raw('DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY) AS fechaFR'),
                    DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY)) AS diasPFR'),
                    'orden_trabajo.APROBADA','orden_trabajo.COBRADA',
                    'orden_trabajo.NRO_ORDEN'
                    ,'orden_trabajo.TIPO_PAGO')
                    ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                    ->where('orden_trabajo.ACTIVO', 1)
                    ->where('orden_trabajo.ANULADA', 0)
                    ->where('orden_trabajo.SUSPENDIDA', 0)
                    ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'<=', 15)
                    ->where(DB::raw('DATEDIFF(CURDATE(),DATE_ADD(orden_trabajo.FECHA_SERVICIO, INTERVAL orden_trabajo.FRECUENCIA DAY))'),'>=', -7)
                    ->where('orden_trabajo.ESTADO_FRECUENCIA', 1)
                    ->where('orden_trabajo.FECHA_SERVICIO',$fecha)
                    ->where(function ($query) use($buscar){
                        $query->
                                where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscar.'%')
                                ->orWhere('cliente.TELEFONO','like','%'.$buscar.'%')
                                ->orWhere('cliente.CONTACTO','like','%'.$buscar.'%') 
                                ->orWhere('orden_trabajo.NRO_ORDEN','like','%'.$buscar.'%');
                    })  
                    ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                    ->paginate(20);
                }
            }  
            

            return [
                'pagination' => [
                    'total'        => $reporte->total(),
                    'current_page' => $reporte->currentPage(),
                    'per_page'     => $reporte->perPage(),
                    'last_page'    => $reporte->lastPage(),
                    'from'         => $reporte->firstItem(),
                    'to'           => $reporte->lastItem(),
                ],
                'frecuencia' => $reporte
            ];
        }
        else
            return "expiro";
    }

}
