<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetalleReservaMigracion;
use App\OrdenTrabajo;

class DetalleReservaMigracionController extends Controller
{
    public function listaDetalleReservasFechaActualMigracion(Request $request)
    {
        $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');
        
    
        $detalleReserva = DetalleReservaMigracion::join('personal','detallereserva_migracion.codTrabajador','=','personal.COD_PERSONAL')
        
            ->join('reserva_migracion','detallereserva_migracion.codReserva','=','reserva_migracion.codReserva')
            ->join('cliente','reserva_migracion.codCliente','=','cliente.COD_CLIENTE')

            ->leftJoin('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','reserva_migracion.codOT')
            
            ->select('detallereserva_migracion.codReserva','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO',
            'reserva_migracion.fecha','reserva_migracion.horaInicio','reserva_migracion.tiempoServicio','reserva_migracion.tiempoTransporte','personal.COD_PERSONAL'
            ,'cliente.NOMBRE as C_NOMBRE','cliente.AP_CLIENTE as C_AP_CLIENTE','cliente.AM_CLIENTE as C_AM_CLIENTE','cliente.DIRECCION','orden_trabajo.NRO_ORDEN')
            ->where('reserva_migracion.fecha',$fechaSistema)
            ->where('reserva_migracion.estado',1)
            ->where('detallereserva_migracion.estadoTrabajador',1)
            ->get();
            
        return  $detalleReserva;

    }
}
