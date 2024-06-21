<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tecnico;
use App\ControlNocturno;

use App\Reserva;
use App\DetalleReserva;
use App\OrdenTrabajo;
use App\Cliente;
use App\equipo_tecnico;

class TecnicoController extends Controller
{
    public function index()
    {
        $tecnico = Tecnico::where('TIPO_PERSONAL',2)->get();
        return $tecnico;
    }
    //activo
    public function listarReservasFechaUnTecnico(Request $request)
    {
        
        $Tecnico = DB::select(
            'SELECT dr.codTrabajador,dr.codDetReserva,r.fecha,r.horainicio,r.tiempoServicio,r.tiempoTransporte,CONCAT(c.NOMBRE," ",AP_CLIENTE," ",c.AM_CLIENTE) AS NOMBRE 
            FROM detallereserva dr 
            INNER JOIN reserva r ON dr.codReserva=r.codReserva 
            INNER JOIN cliente c ON r.codCliente=c.COD_CLIENTE
            WHERE dr.codTrabajador="'.$request->codTecnico.'" AND dr.estadoTrabajador=1 
            AND r.estado=1 AND r.fecha="'.$request->fecha.'"');
        
           
        /*
        $Tecnico = DB::select(
            'SELECT dr.codTrabajador,dr.codDetReserva,r.fecha,r.horainicio,r.tiempoServicio,r.tiempoTransporte
            FROM detallereserva dr 
            INNER JOIN reserva r ON dr.codReserva=r.codReserva 
            WHERE dr.codTrabajador="'.$request->codTecnico.'" AND dr.estadoTrabajador=1 
            AND r.estado=1 AND r.fecha="'.$request->fecha.'"');
        */
        return $Tecnico;
    }

    //activo
    public function registrarReasignacion(Request $request)
    {
        try
        {
            DB::beginTransaction();
            if($request->ReasignacionObaja=="Reasignacion"){
                $this->validate($request,[
                    'Tecnico'=>'required',
                ]);
            }
            $detalles = $request->selectTecnicoReserva;
            for($i = 0; $i < count($detalles); $i++) 
            {
                $reserva2 = DetalleReserva::where('codDetReserva', $detalles[$i]['codDetReserva'])
                ->update(['estadoTrabajador' => 0]);

                if($request->ReasignacionObaja=="Reasignacion"){
                $DetalleReserva = new DetalleReserva();
                $DetalleReserva->codTrabajador = $request->Tecnico;
                $DetalleReserva->codReserva = $detalles[$i]['codReserva'];
                $DetalleReserva->estadoTrabajador = 1;
                $DetalleReserva->save();
                }
            }
            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }
    //activo
    public function listadoTecnicoReserva(Request $request)
    {
        $Tecnico = Tecnico::join('detallereserva as dr','dr.codTrabajador','=','personal.COD_PERSONAL')
            ->join('reserva as r','dr.codReserva','=','r.codReserva')
            ->join('cliente as c','r.codCliente','=','c.COD_CLIENTE')
            ->select('dr.codDetReserva','r.codReserva','personal.COD_PERSONAL','r.fecha','r.horainicio','r.tiempoServicio',
            'r.tiempoTransporte','dr.codTrabajador','c.NOMBRE','c.AP_CLIENTE','c.AM_CLIENTE','r.estado','dr.estadoTrabajador')
            ->where('personal.COD_PERSONAL',$request->codTecnico)
            ->where('r.fecha','>',date('Ymd'))
            ->where('r.estado',1)
            ->where('dr.estadoTrabajador',1)
           
            ->orderBy('r.fecha', 'desc')
            ->paginate(20);
            
            
       
        return [
            'pagination' => [
                'total'        => $Tecnico->total(),
                'current_page' => $Tecnico->currentPage(),
                'per_page'     => $Tecnico->perPage(),
                'last_page'    => $Tecnico->lastPage(),
                'from'         => $Tecnico->firstItem(),
                'to'           => $Tecnico->lastItem(),
            ],
            'Tecnico' => $Tecnico
        ];
    }
    //activo
    public function listadoTecnicos(Request $request)
    {
        /*
        $personal = Tecnico::
        where('ACTIVO',1)
        ->where(function ($query){
            $query->Where('TIPO_PERSONAL',2)
                    ->orWhere('TIPO_PERSONAL',3);
        })
        ->paginate(20);
        */


        $buscar = $request->buscar;
      if($request->buscar!='')
      {
          $personal = Tecnico::join('equipo_tecnico','equipo_tecnico.COD_EQUIPO','=','personal.COD_EQUIPO')
          ->select('personal.COD_PERSONAL','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','personal.DIRECCION',
          'personal.TELEFONO','personal.TIPO_PERSONAL','personal.COD_EQUIPO','equipo_tecnico.DESCRIPCION','personal.COD_USUARIO','personal.ACTIVO')
          ->where('personal.ACTIVO',1)
          ->where(function ($query) use($buscar){
            $query->where('personal.TIPO_PERSONAL',2)
            ->orwhere('personal.TIPO_PERSONAL',3)
            ->where(DB::raw('concat(personal.NOMBRE," ",personal.AP_PATERNO," ",personal.AP_MATERNO)'),'like','%'.$buscar.'%');
            
        })
          ->orderBy('personal.COD_PERSONAL', 'desc')
          ->paginate(10);
      }
      else
      {
          $personal = Tecnico::join('equipo_tecnico','equipo_tecnico.COD_EQUIPO','=','personal.COD_EQUIPO')
          ->select('personal.COD_PERSONAL','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','personal.DIRECCION',
          'personal.TELEFONO','personal.TIPO_PERSONAL','personal.COD_EQUIPO','equipo_tecnico.DESCRIPCION','personal.COD_USUARIO','personal.ACTIVO')
          ->where('personal.ACTIVO',1)
          ->where(function ($query) use($buscar){
            $query->where('personal.TIPO_PERSONAL',2)
            ->orwhere('personal.TIPO_PERSONAL',3);
        })
        ->orderBy('personal.COD_PERSONAL', 'desc')
        ->paginate(20);
      }
      
        return [
            'pagination' => [
                'total'        => $personal->total(),
                'current_page' => $personal->currentPage(),
                'per_page'     => $personal->perPage(),
                'last_page'    => $personal->lastPage(),
                'from'         => $personal->firstItem(),
                'to'           => $personal->lastItem(),
            ],
            'personal' => $personal
        ];
    }

    public function listadoTecnicosFechaActual(Request $request)
    {

        $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');

        $sql = DB::select('SELECT p.COD_PERSONAL,p.NOMBRE,p.AP_PATERNO,p.AP_MATERNO,cn.activo,cn.fechaFinalizacion,cn.codReserva,cn.incremento,p.COD_EQUIPO,p.TIPO_PERSONAL
        FROM control_Nocturno cn right join personal p on cn.codPersonal=p.COD_PERSONAL and cn.fechaFinalizacion="'.$fechaSistema.'" 
        where p.ACTIVO=1 and (p.TIPO_PERSONAL=2 or p.TIPO_PERSONAL=4) order by p.COD_EQUIPO');

        return $sql;
    }

    public function listadoTecnicosFechaActualAdmin(Request $request)
    {
        
        //original
        $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');

        $sql = DB::select('SELECT p.COD_PERSONAL,p.NOMBRE,p.AP_PATERNO,p.AP_MATERNO,cn.activo,cn.fechaFinalizacion,cn.codReserva,cn.incremento
        FROM control_Nocturno cn right join personal p on cn.codPersonal=p.COD_PERSONAL and cn.fechaFinalizacion="'.$fechaSistema.'" 
        where p.ACTIVO=1 and (p.TIPO_PERSONAL=2 || p.TIPO_PERSONAL=3) order by p.COD_EQUIPO');
        //p.TIPO_PERSONAL=2 ||
        return $sql;
    }

    //operaciones y ejecutivos
    public function listadoTecnicosFechaActualAdminAmbos(Request $request)
    {
        //and cn.activo=1
        //original
        $fechaSistema = $request->fecha;
        if($request->fecha=="")
            $fechaSistema=date('Y-m-d');

        $sql = DB::select('SELECT p.COD_PERSONAL,p.NOMBRE,p.AP_PATERNO,p.AP_MATERNO,cn.activo,cn.fechaFinalizacion,cn.codReserva,cn.incremento,p.COD_EQUIPO,p.TIPO_PERSONAL
        FROM control_Nocturno cn right join personal p on cn.codPersonal=p.COD_PERSONAL and cn.fechaFinalizacion="'.$fechaSistema.'" 
        where p.ACTIVO=1  and (p.TIPO_PERSONAL=2 || p.TIPO_PERSONAL=3) order by p.COD_EQUIPO');

        return $sql;
    }


    //operaciones Empresas Fijas 
    public function listarOEF(Request $request)
    {
        $sql = DB::select('SELECT p.COD_PERSONAL,p.NOMBRE,p.AP_PATERNO,p.AP_MATERNO,p.COD_EQUIPO
        FROM personal p  
        where p.ACTIVO=1 and (p.TIPO_PERSONAL=2 || p.TIPO_PERSONAL=3) order by p.COD_EQUIPO');

        return $sql;
    }
}
