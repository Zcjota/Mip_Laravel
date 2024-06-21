<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cliente;

class ClienteController extends Controller
{
    public function listado(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');

        $cliente = Cliente::all();
        return $cliente;
    }
    
    public function listadoActivo(Request $request)
    {
        //$charset = "utf8mb4";
        /*
        $cliente= DB::select('SELECT * FROM cliente where ACTIVO=1 order by COD_CLIENTE desc LIMIT 5');
        return $cliente;
        */
        if($request->buscar==''){
            $cliente= DB::table('cliente')
            ->where('ACTIVO',1)
            ->orderBy('NOMBRE', 'asc')
            ->paginate(20);
        }
        else
        {
           
            $cliente = Cliente::
            
            //where('NOMBRE','like','%'.$request->buscar.'%')
            where(DB::raw('concat(NOMBRE," ",AP_CLIENTE)'),'like','%'.$request->buscar.'%')
            ->where('ACTIVO',1)
            ->orWhere('cliente.TELEFONO',$request->buscar)
            ->orderBy('NOMBRE', 'asc')
            ->paginate(20);
        }


        
        return [
            'pagination' => [
                'total'        => $cliente->total(),
                'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastItem(),
            ],
            'cliente' => $cliente
        ];
    }
    public function listaCliente(Request $request)
    {/*
        $cliente= DB::select('SELECT * FROM cliente  order by COD_CLIENTE desc LIMIT 5');
        return $cliente;*/
        $charset = "utf8mb4";
        if(!$request->ajax()) return redirect('/login');

        if($request->buscar==''){
            $cliente= DB::table('cliente')
            ->where('ACTIVO',1)
            ->orderBy('COD_CLIENTE', 'desc')
            ->paginate(20);
        }
        else
        {
           
            $cliente = Cliente::
            where(DB::raw('concat(NOMBRE," ",AP_CLIENTE)'),'like','%'.$request->buscar.'%')
            ->where('ACTIVO',1)
            ->orWhere('cliente.TELEFONO',$request->buscar)
            //where('NOMBRE','like','%'.$request->buscar.'%')
            ->orderBy('COD_CLIENTE', 'desc')
            ->paginate(20);
        }


        
        return [
            'pagination' => [
                'total'        => $cliente->total(),
                'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastItem(),
            ],
            'cliente' => $cliente
        ];
    }

    public function listaTipoCliente(Request $request)
    {
       

        $TipoCliente = DB::select('SELECT COD_TIPO_CLIENTE, DESCRIPCION FROM `tipo_cliente` WHERE ACTIVO = 1 ORDER BY DESCRIPCION');
        return $TipoCliente;
    }
    public function ciudad(Request $request)
    {
   

        $ciudad = DB::select('SELECT COD_CIUDAD, DESCRIPCION FROM `ciudad` WHERE ACTIVO = 1 ORDER BY DESCRIPCION');
        return $ciudad;
    }

    public function ejecutivo(Request $request)
    {
        

        $ejecutivo = DB::select('SELECT COD_PERSONAL, NOMBRE, AP_PATERNO, AP_MATERNO  FROM personal WHERE TIPO_PERSONAL = 1 AND ACTIVO = 1');
        return $ejecutivo;
    }

    public function registrar(Request $request)
    {
        //'apellidoMaterno'=>'required',
        // 'correo'=>'required|email',
        //   'razonSocial'=>'required',
        if(!$request->ajax()) return redirect('/login');

        if($request->clickCertificado==0)
        {
            if($request->correo!="")
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'correo'=>'email',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                ]);
            }
            else
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                ]);
            }
        }
        else
        {
            if($request->correo!="")
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'correo'=>'email',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                    'nombrePrincipalCertificado'=>'required',
                    'nombreSecundarioCertificado'=>'required',
                    'DireccionCertificado'=>'required',
                
                ]);
            }
            else
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                    'nombrePrincipalCertificado'=>'required',
                    'nombreSecundarioCertificado'=>'required',
                    'DireccionCertificado'=>'required',
                
                ]);
            }
        }

        $nomPr="";
        $nomSec="";
        $direc="";
        if($request->nombrePrincipalCertificado!="")
        {
            $nomPr = $request->nombrePrincipalCertificado;
            $nomSec = $request->nombreSecundarioCertificado;
            $direc = $request->DireccionCertificado;
        }

        $cliente = new Cliente();
        $cliente->NOMBRE = $request->nombre;
        $cliente->AP_CLIENTE = $request->apellidoPaterno;
        $cliente->AM_CLIENTE = $request->apellidoMaterno;
        $cliente->CI_NIT = $request->nit;
        $cliente->RAZON_SOCIAL = $request->razonSocial;
        $cliente->CORREO = $request->correo;
        $cliente->TELEFONO = $request->telefono;
        $cliente->CONTACTO = $request->contacto;
        $cliente->COD_TIPO_CLIENTE = $request->tipoCliente;
        $cliente->CATEGORIA = $request->categoria;
        $cliente->FRECUENCIA_SERVICIO = $request->frecuencia;
        $cliente->CIUDAD = $request->ciudad;
        $cliente->DIRECCION = $request->direccion;
        $cliente->COD_EJECUTIVO = $request->ejecutivo;
        $cliente->JESUS = 0;
     
        $cliente->C_NOMB_P = $nomPr;
        $cliente->C_NOMB_S = $nomSec;
        $cliente->C_DIRECCION = $direc;
        $cliente->ACTIVO = 1;
        $cliente->save();
    }

    public function modificar(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');

        if($request->clickCertificado==0)
        {
            if($request->correo!="")
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'correo'=>'email',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                ]);
            }
            else
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                ]);
            }
        }
        else
        {
            if($request->correo!="")
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'correo'=>'email',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                    'nombrePrincipalCertificado'=>'required',
                    'nombreSecundarioCertificado'=>'required',
                    'DireccionCertificado'=>'required',
                
                ]);
            }
            else
            {
                $this->validate($request,[
                    'nombre'=>'required',
                    'apellidoPaterno'=>'required',
                    'telefono'=>'required',
                    'contacto'=>'required',
                    'tipoCliente'=>'required',
                    'categoria'=>'required',
                    'frecuencia'=>'required',
                    'ciudad'=>'required',
                    'ejecutivo'=>'required',
                    'nombrePrincipalCertificado'=>'required',
                    'nombreSecundarioCertificado'=>'required',
                    'DireccionCertificado'=>'required',
                
                ]);
            }
        }

        $nomPr="";
        $nomSec="";
        $direc="";
        if($request->nombrePrincipalCertificado!="")
        {
            $nomPr = $request->nombrePrincipalCertificado;
            $nomSec = $request->nombreSecundarioCertificado;
            $direc = $request->DireccionCertificado;
        }

        $cliente = Cliente::findOrFail($request->idCliente);
        $cliente->NOMBRE = $request->nombre;
        $cliente->AP_CLIENTE = $request->apellidoPaterno;
        $cliente->AM_CLIENTE = $request->apellidoMaterno;
        $cliente->CI_NIT = $request->nit;
        $cliente->RAZON_SOCIAL = $request->razonSocial;
        $cliente->CORREO = $request->correo;
        $cliente->TELEFONO = $request->telefono;
        $cliente->CONTACTO = $request->contacto;
        $cliente->COD_TIPO_CLIENTE = $request->tipoCliente;
        $cliente->CATEGORIA = $request->categoria;
        $cliente->FRECUENCIA_SERVICIO = $request->frecuencia;
        $cliente->CIUDAD = $request->ciudad;
        $cliente->DIRECCION = $request->direccion;
        $cliente->COD_EJECUTIVO = $request->ejecutivo;
        $cliente->JESUS = 0;

        $cliente->C_NOMB_P = $nomPr;
        $cliente->C_NOMB_S = $nomSec;
        $cliente->C_DIRECCION = $direc;
        $cliente->ACTIVO = 1;
        $cliente->save();
    }

    public function cambiarEstado(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');
        
        $cliente = Cliente::findOrFail($request->idCliente);
        $cliente->ACTIVO = $request->estado;
        $cliente->save();
    }
}
