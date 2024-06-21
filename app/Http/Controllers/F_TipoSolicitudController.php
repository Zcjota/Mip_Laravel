<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\F_TipoSolicitud;

class F_TipoSolicitudController extends Controller
{
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $tiposolicitud=F_TipoSolicitud::orderBy('COD_TSE','asc')->paginate(30);
        }
        else
        {
            $numero_cuenta=F_NumeroCuenta::where('nro_cuenta.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('nro_cuenta.DESCRIPCION','asc')->paginate(30); 
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $tiposolicitud->total(),
                'current_page'=>$tiposolicitud->currentPage(),
                'per_page'=> $tiposolicitud->perPage(),
                'last_page'=>$tiposolicitud->lastPage(),
                'from'=>$tiposolicitud->firstItem(),
                'to'=>$tiposolicitud->lastItem(),
            ],
            'tiposolicitud'=>$tiposolicitud
        ];
    }


    public function listarActivos(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $tiposolicitud=F_TipoSolicitud::orderBy('COD_TSE','asc')
            ->where('ACTIVO',1)
            ->paginate(30);
        }
        else
        {
            $numero_cuenta=F_NumeroCuenta::where('nro_cuenta.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('nro_cuenta.DESCRIPCION','asc')->paginate(30); 
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $tiposolicitud->total(),
                'current_page'=>$tiposolicitud->currentPage(),
                'per_page'=> $tiposolicitud->perPage(),
                'last_page'=>$tiposolicitud->lastPage(),
                'from'=>$tiposolicitud->firstItem(),
                'to'=>$tiposolicitud->lastItem(),
            ],
            'tiposolicitud'=>$tiposolicitud
        ];
    }

    public function CambiarEstado(Request $request)
    {
        //return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        $tiposolicitud =  F_TipoSolicitud::findOrFail($request->idtiposolicitud);
        $tiposolicitud->ACTIVO=$request->estado;
        $tiposolicitud->save();
    }
    
}
