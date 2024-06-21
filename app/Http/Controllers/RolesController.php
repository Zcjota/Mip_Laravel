<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Roles;
use App\DetallesRoles;

class RolesController extends Controller
{
    public function desactivar(Request $request)
    {
        $rol = Roles::findOrFail($request->idRol);
        $rol->condicion = '0';
        $rol->save();
    }

    public function activar(Request $request)
    {
        ///if (!$request->ajax()) return redirect('/');
        $rol = Roles::findOrFail($request->idRol);
        $rol->condicion = '1';
        $rol->save();
    }

    public function obtDetalleRoles(Request $request)
    {
        $subMenu = Roles::join('detallerol','detallerol.codRol','=','roles.id')
        ->join('submenu','submenu.codSubMen','=','detallerol.codSubMenu')
        ->select('submenu.codSubMen')
        ->where('roles.id', '=',$request->idRol)
        ->get();
        return $subMenu;
    }

    public function listado()
    {
        $rol = Roles::orderBy('id', 'desc')->get();
        return $rol;
    }

    public function listadoActivos()
    {
        $rol = Roles::where('condicion', '1')->get();
        return $rol;
    }

    public function registrar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $roles = new Roles();
            $roles->nombre = $request->nombre;
            $roles->descripcion = $request->descripcion;
            $roles->condicion = '1';
            $roles->save();

            $detalles = $request->detalles;
            for($i = 0; $i < count($detalles); $i++) {
                $detalle = new DetallesRoles();
                $detalle->codRol = $roles->id;;
                $detalle->codSubMenu = $detalles[$i];
                $detalle->save();
            }
            DB::commit();
        } 
        catch (Exception $e){
            DB::rollBack();
        }
    }
}
