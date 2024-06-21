<?php

namespace App\Http\Controllers;

use App\Models\DetalleUsuario;
use Illuminate\Http\Request;
use App\Models\TipoUsuario;

class TipoUsuarioController extends Controller
{
    public function listarTiposUsuarios(){
        $result = new TipoUsuario;
        $result = $result->listarTiposUsuarios();

        return $result;
    }

    public function guardarRol(Request $request){
        set_time_limit(240);
        $tipo_usuario = new TipoUsuario;
        $tipo_usuario = $tipo_usuario->guardarRol($request->nombre);
        
        $detalle = new DetalleUsuario;

        for ($i=0; $i < count($request->arrayMenu); $i++) { 
            $datos['tipo_usuario'] = $tipo_usuario->id_tipo_usuario;
            $datos['sub_menu'] = $request->arrayMenu[$i];
            $detalle->guardarDetallePerfilMenu($datos);
        }

        return $detalle;
    }

    public function editarRol(Request $request){
        set_time_limit(240);
        $tipo_usuario = new TipoUsuario;
        $tipo_usuario = $tipo_usuario->editarRol($request);
        
        $detalle = new DetalleUsuario;
        $detalle->cambiarEstadoDetallePerfil($request->id);

        for ($i=0; $i < count($request->arrayMenu); $i++) { 
            $datos['tipo_usuario'] = $request->id;
            $datos['sub_menu'] = $request->arrayMenu[$i];
            $detalle->editarDetallePerfilMenu($datos);
        }

        return $tipo_usuario;
    }

    public function cambiarEstadoTipoUsuario(Request $request){
        $datos['tipo_usuario'] = $request->tipo_usuario;
        $datos['estado'] = $request->estado ? 0 : 1;

        $tipo_usuario = new TipoUsuario;
        $tipo_usuario = $tipo_usuario->cambiarEstadoTipoUsuario($datos);

        return $tipo_usuario;
    }
}
