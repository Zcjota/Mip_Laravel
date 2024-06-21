<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MaestroOEF;
use App\DetalleOEF;

class MaestroOEFController extends Controller
{
    public function listarDetalle(Request $request)
    {
        //SELECT * FROM DetalleOEF d WHERE d.activo=1 and d.codMaestroOEF=1
        return DB::select("SELECT * FROM DetalleOEF d WHERE d.activo=1 and d.codMaestroOEF=1"); 
    }

    public function listarDatos(Request $request)
    {
        $oef = MaestroOEF::select('codigo','fechaCreacion','mes','gestion','cantidadClientes','cantidadTecnicos','activo')
        ->where('activo',1)
        ->orderBy('codigo', 'desc')
      
        ->paginate(20);

   
        return [
            'pagination' => [
                'total'        => $oef->total(),
                'current_page' => $oef->currentPage(),
                'per_page'     => $oef->perPage(),
                'last_page'    => $oef->lastPage(),
                'from'         => $oef->firstItem(),
                'to'           => $oef->lastItem(),
            ],
            'oef' => $oef
        ];
    }

    public function listaReservasPorGestion(Request $request)
    {
        return DB::select("SELECT r.codReserva,r.fecha,r.horaInicio,r.tiempoServicio,r.tiempoTransporte,r.estado,r.codCliente,
        CONCAT(c.NOMBRE,c.AP_CLIENTE,c.AM_CLIENTE) AS 'cliente' FROM reserva r INNER JOIN cliente c ON c.COD_CLIENTE=r.codCliente
        WHERE MONTH(r.fecha)=".$request->mes." AND YEAR(r.fecha)=".$request->gestion." AND estadoOEF=1 
        AND estado=1"); 
    }

    public function listarTecnicosReserva(Request $request)
    {
        return DB::select("SELECT dr.codDetReserva,dr.codTrabajador,dr.estadoTrabajador,dr.codReserva,CONCAT(p.NOMBRE,' ',p.AP_PATERNO,' ',p.AP_MATERNO) AS 'tecnico'
        FROM reserva r INNER JOIN detallereserva dr ON r.codReserva=dr.codReserva
        INNER JOIN personal p ON dr.codTrabajador=p.COD_PERSONAL
        WHERE MONTH(r.fecha)=".$request->mes." AND YEAR(r.fecha)=".$request->gestion." AND estadoOEF=1 
        AND estado=1 AND dr.estadoTrabajador=1"); 
    }

    public function registrar(Request $request)
    {   
        try 
        {
            DB::beginTransaction();

            $obj = new MaestroOEF();
            $obj->fechaCreacion = Now();
            $obj->mes = $request->mes;
            $obj->gestion = $request->gestion;
            $obj->cantidadClientes = 1;
            $obj->cantidadTecnicos = 1;
            $obj->activo = 1;
            $obj->codUsuario = session()->get('codigo');
            $obj->save(); 
    
            $detalles = $request->datos;
            foreach($detalles as $ep=>$det)
            {
                $objDet = new DetalleOEF();
                $objDet->fecha = $det['fecha'];
                $objDet->horaInicio = $det['horaInicio'];
                $objDet->tiempoServicio = $det['tiempoServicio'];
                $objDet->tiempoTransporte =  $det['tiempoTransporte'];
                $objDet->codCliente =  $det['codCliente'];
                $objDet->codTecnico1 = $det['codTecnico1'];
                $objDet->codTecnico2 = $det['codTecnico2'];
                $objDet->codTecnico3 = $det['codTecnico3'];
                $objDet->codTecnico4 = $det['codTecnico4'];
                $objDet->codTecnico5 = $det['codTecnico5'];
                $objDet->activo = 1;
                $objDet->codMaestroOEF = $obj->codigo;
                $objDet->save();
            }       

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        } 
       
    }
}
