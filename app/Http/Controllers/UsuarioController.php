<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;


class UsuarioController extends Controller
{
    public function desactivar(Request $request)
    {
        ///if (!$request->ajax()) return redirect('/');
        $usuario = Usuario::findOrFail($request->idUsuario);
        $usuario->estado = '0';
        $usuario->save();
    }

    public function activar(Request $request)
    {
        ///if (!$request->ajax()) return redirect('/');
        $usuario = Usuario::findOrFail($request->idUsuario);
        $usuario->estado = '1';
        $usuario->save();
    }
    public function listado()
    {
        $usuario = Usuario::join('personal','personal.COD_PERSONAL','=','usuario.codPersonal')
        ->join('roles','roles.id','=','usuario.codRol')
        ->select('personal.NP_NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','usuario.codUsuario','usuario.codPersonal','usuario.imagen','usuario.codRol','usuario.usuario','usuario.password','usuario.estado','roles.nombre')
        ->orderBy('codUsuario', 'desc')
        ->get();
        return $usuario;
    }

  

    public function obtId()
    {
        //return session()->get('codigo');
        
        return session()->get('tipoUsuario');
    }

    /*
    public function obtId1()
    {
        return session()->get('tipoPersonal');
    }
    */
    public function verificar(Request $request)
    {
        /*
        $usuario = Usuario::where('LOGIN', '=', $request->usuario)->where('PASSWORD', '=', $request->password)->count();
        
        $usuario1 = Usuario::select('COD_USUARIO','NOMBRE','AP_PATERNO','AP_MATERNO')
        ->where('LOGIN', '=', $request->usuario)
        ->where('PASSWORD', '=', $request->password)
        ->get();
        
        if ($usuario > 0) { 
            
            $nombre = $usuario1[0]->NOMBRE.' '.$usuario1[0]->AP_PATERNO.' '.$usuario1[0]->AP_MATERNO;
            $imagen = 'imgUsuarios/logo1.png';

            $request->session()->put('nombre',$nombre);
            $request->session()->put('imagen',$imagen);
            $request->session()->put('codigo',$usuario1[0]->COD_USUARIO);
            return view('contenido/contenido')
            ->with('nombre',$request->session()->get('nombre'))
            ->with('imagen',$request->session()->get('imagen'))
            ->with('codigo',$request->session()->get('codigo'));
        } 
        else
        {
            return back()
            ->withErrors(['usuario' => trans('auth.failed')])
            ->withInput(request(['usuario']));
        }
        */
        


        
        $usuario = Usuario::where('LOGIN', '=', $request->usuario)->where('PASSWORD', '=', $request->password)->count();                
        
        if ($usuario > 0) 
        { 
            $usuario1 = Usuario::leftJoin('personal','personal.COD_USUARIO','=','usuario.COD_USUARIO')
        ->select('usuario.COD_USUARIO','usuario.NOMBRE','usuario.AP_PATERNO','usuario.AP_MATERNO','personal.TIPO_PERSONAL','personal.COD_PERSONAL','usuario.COD_TIPOU')
        ->where('LOGIN', '=', $request->usuario)
        ->where('PASSWORD', '=', $request->password)
        ->first();
            $tipoPersonal=24;
            $codPersonal=0;
            if($usuario1->TIPO_PERSONAL==1 || $usuario1->TIPO_PERSONAL=='1')
            {
                $tipoPersonal= $usuario1->COD_PERSONAL;
                $codPersonal= $usuario1->COD_PERSONAL;
            }

            session()->put('tipoPersonal',$tipoPersonal);
            session()->put('codPersonal',$codPersonal);
            session()->put('tipoUsuario', $usuario1->COD_TIPOU);
            $nombre = $usuario1->NOMBRE.' '.$usuario1->AP_PATERNO.' '.$usuario1->AP_MATERNO;
            $imagen = 'imgUsuarios/logo1.png';

            $request->session()->put('nombre',$nombre);
            $request->session()->put('imagen',$imagen);
            $request->session()->put('codigo',$usuario1->COD_USUARIO);
            
            return view('contenido/contenido')
            ->with('nombre',$request->session()->get('nombre'))
            ->with('imagen',$request->session()->get('imagen'))
            ->with('codigo',$request->session()->get('codigo'));
        } 
        else
        {
            return back()
            ->withErrors(['usuario' => trans('auth.failed')])
            ->withInput(request(['usuario']));
        }
        
    }

    public function cerrarSession(Request $request)
    {
        $request->session()->invalidate();
        return redirect('/');
            
    }

    public function registrar(Request $request)
    {
            $usuario = new usuario();
            $usuario->codPersonal = $request->personal;
            $usuario->codRol = $request->rol;
            $usuario->imagen = $request->imagen;
            $usuario->usuario = $request->usuario;
            $usuario->password = $request->password;
            $usuario->estado = '1';
            $usuario->save();
    }

   
}
