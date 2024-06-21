<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\DetalleReserva;
use App\OrdenTrabajo;
/*
use App\Cliente;
use App\OrdenTrabajo;
*/

class DetalleReservaController extends Controller
{
    public function listaDetalleReservasFechaActual(Request $request)
    {
        /*
        $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');
        
    
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
        
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->join('cliente','reserva.codCliente','=','cliente.COD_CLIENTE')

            ->leftJoin('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','reserva.codOT')
            
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL'
            ,'cliente.NOMBRE as C_NOMBRE','cliente.AP_CLIENTE as C_AP_CLIENTE','cliente.AM_CLIENTE as C_AM_CLIENTE','cliente.DIRECCION','orden_trabajo.NRO_ORDEN')
            ->where('reserva.fecha',$fechaSistema)
            ->where('reserva.estado',1)
            ->where('detallereserva.estadoTrabajador',1)
            ->get();

            */

            $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');
        
    
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
        
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->join('cliente','reserva.codCliente','=','cliente.COD_CLIENTE')

            ->leftJoin('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','reserva.codOT')
            
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL'
            ,'cliente.NOMBRE as C_NOMBRE','cliente.AP_CLIENTE as C_AP_CLIENTE','cliente.AM_CLIENTE as C_AM_CLIENTE','cliente.DIRECCION','orden_trabajo.NRO_ORDEN')
            ->where('reserva.fecha',$fechaSistema)
            ->where('reserva.estado',1)
            //->where('personal.ACTIVO',1)
            //->where('cliente.ACTIVO',1)
            ->where('detallereserva.estadoTrabajador',1)
            ->get();
            
        return  $detalleReserva;

    }
    public function listarTrabajadoresModificar(Request $request)
    {
        $detalleReserva = DetalleReserva::where('codReserva',$request->idReserva)
        ->where('estadoTrabajador',1)
        ->select('detallereserva.codTrabajador')
        ->get();
         

        return $detalleReserva;
    }
    public function listaReservaModificar(Request $request)
    {
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->join('cliente','reserva.codCliente','=','cliente.COD_CLIENTE')
            ->leftJoin('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','reserva.codOT')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL'
            ,'cliente.NOMBRE as C_NOMBRE','cliente.AP_CLIENTE as C_AP_CLIENTE','cliente.AM_CLIENTE as C_AM_CLIENTE','cliente.DIRECCION','orden_trabajo.NRO_ORDEN')
            ->where('reserva.fecha',$request->fecha)
            ->where('estadoTrabajador',1)
            ->where('reserva.codReserva','<>',$request->idReserva)
            ->where('estado',1)
            ->get();
        return $detalleReserva;
    }
/*
    public function index()
    {
        $fechaSistema = date('Ymd');
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('fecha',$fechaSistema)
          
            ->where('estado',1)
            ->get();
        return  ['detalleReserva' => $detalleReserva];
    }

    public function listaReservaModificar(Request $request)
    {
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('reserva.fecha',$request->fecha)
            ->where('reserva.codReserva','<>',$request->idReserva)
            ->where('estado',1)
            ->get();
        return $detalleReserva;
    }

    public function listarTrabajadoresModificar(Request $request)
    {

        $detalleReserva = DetalleReserva::where('codReserva',$request->idReserva)
        ->where('estadoTrabajador',1)
        ->select('detallereserva.codTrabajador')
        
        ->get();

        return $detalleReserva;
    }

    public function fechaSeleccionada(Request $request)
    {
        $fechaSistema = $request->fecha;
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('fecha',$fechaSistema)
            ->where('tipo',0)
            ->where('estado',1)
            ->get();
        return  ['detalleReserva' => $detalleReserva];   
    }
    public function DetalleReservaFechaCompleto(Request $request)
    {
        $fechaSistema = $request->fecha;
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('fecha',$fechaSistema)
            ->where('estado',1)
            ->get();
        return  ['detalleReserva' => $detalleReserva];   
    }

    public function fechaSeleccionadaReplicacion(Request $request)
    {
        $fechaSistema = $request->fecha;
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('fecha',$fechaSistema)
       
            ->where('estado',1)
            ->get();
        return  ['detalleReserva' => $detalleReserva];   
    }

    public function fechaSeleccionadaReplicacionCompleto(Request $request)
    {
        $fechaSistema = $request->fecha;
        $detalleReserva = DetalleReserva::join('personal','detallereserva.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva','detallereserva.codReserva','=','reserva.codReserva')
            ->select('detallereserva.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva.fecha','reserva.horaInicio','reserva.tiempoServicio','reserva.tiempoTransporte','personal.COD_PERSONAL')
            ->where('fecha',$fechaSistema)
            ->where('estado',1)
            ->get();
        return  ['detalleReserva' => $detalleReserva];   
    }
    */
}
