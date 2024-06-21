<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Equipos;
use App\DetalleEquipo;
use App\Personal;

class EquiposController extends Controller
{
    public function index()
    {
        $equipos = Equipos::all();
        return $equipos;
    }

    public function listadoActivo()
    {
        $equipos = Equipos::join('DetalleEquipo','DetalleEquipo.codEquipo','=','equipos.COD_EQUIPO')
        ->select('equipos.COD_EQUIPO','equipos.DESCRIPCION','equipos.ACTIVO')
        ->distinct()
        ->get();
        return $equipos;
    }
    //muestra los tecnicos que pertenecen a un equipo
    public function listadoEquipoConTecnicos(Request $request)
    {
        
        $equipos = Equipos::join('personal as p','p.COD_EQUIPO','=','equipo_tecnico.COD_EQUIPO')
        ->select('p.COD_PERSONAL','p.NOMBRE','p.AP_PATERNO','p.AP_MATERNO','p.TIPO_PERSONAL')
        ->where('equipo_tecnico.COD_EQUIPO',$request->codEquipo)
        ->where('p.ACTIVO',1)
        ->get();
        return $equipos;
    }

    public function listadoEquipo()
    {
        $equipos = Equipos::select('COD_EQUIPO','DESCRIPCION')
        ->where('ACTIVO', 1)
        ->get();
        return $equipos;
    }
    //muestra todos los equipos menos empresas fijas
    public function listadoEquipoOperaciones()
    {
        $equipos = Equipos::select('COD_EQUIPO','DESCRIPCION')
        ->where('ACTIVO', 1)
        ->where('COD_EQUIPO','<>', 3)
        ->get();
        
        return $equipos;
    }

    public function store(Request $request)
    {
        $equipos = new Equipos();
        $equipos->DESCRIPCION = $request->descripcion;
        $equipos->ACTIVO = '1';
        $equipos->save();
    }

    public function update(Request $request)
    {
        $equipos = Equipos::findOrFail($request->id);
        $equipos->DESCRIPCION = $request->descripcion;
        $equipos->ACTIVO = '1';
        $equipos->save();
    }
}
