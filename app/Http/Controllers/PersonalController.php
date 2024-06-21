<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Personal;
use App\equipo_tecnico;



class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
     
        if($request->buscar!='')
        {
            $personal = Personal::leftJoin('equipo_tecnico','equipo_tecnico.COD_EQUIPO','=','personal.COD_EQUIPO')
            ->select('personal.COD_PERSONAL','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','personal.DIRECCION',
            'personal.TELEFONO','personal.TIPO_PERSONAL','personal.COD_EQUIPO','equipo_tecnico.DESCRIPCION','personal.COD_USUARIO','personal.ACTIVO')
            
            
            ->where('personal.ACTIVO',1)
            ->where(function ($query) use($buscar){
                $query->
                orwhere(DB::raw('concat(personal.NOMBRE," ",personal.AP_PATERNO)'),'like','%'.$buscar.'%')
                ->orwhere('equipo_tecnico.DESCRIPCION','like','%'.$buscar.'%')
                ->orwhere('personal.TELEFONO',$buscar);
            })
            
            ->orderBy('personal.COD_PERSONAL', 'desc')
            ->paginate(100);
        }
        else
        {
            $personal = Personal::leftJoin('equipo_tecnico','equipo_tecnico.COD_EQUIPO','=','personal.COD_EQUIPO')
            ->select('personal.COD_PERSONAL','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','personal.DIRECCION',
            'personal.TELEFONO','personal.TIPO_PERSONAL','personal.COD_EQUIPO','equipo_tecnico.DESCRIPCION','personal.COD_USUARIO','personal.ACTIVO')
            ->where('personal.ACTIVO',1)
            ->orderBy('personal.COD_PERSONAL', 'desc')
            ->paginate(100);
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

    public function listadoActivo()
    {
        $tecnico = Personal::where('ACTIVO', '1')->get();
        return $tecnico;
    }

    public function registrar(Request $request)
    {
        $this->validate($request,[
            'nombre'=>'required',
            'apellidoPaterno'=>'required',
            'apellidoMaterno'=>'required',
            'telefono'=>'required',
            'tipoPersonal'=>'required',
            'direccion'=>'required',
        ]);


        $personal = new Personal();
        $personal->NOMBRE = $request->nombre;
        $personal->AP_PATERNO = $request->apellidoPaterno;
        $personal->AP_MATERNO = $request->apellidoMaterno;
        $personal->TELEFONO = $request->telefono;
        $personal->TIPO_PERSONAL = $request->tipoPersonal;
        $personal->COD_EQUIPO = $request->grupo;
        $personal->DIRECCION = $request->direccion;
        $personal->COD_USUARIO = 0;
        $personal->ACTIVO = 1;
        $personal->save();
    }


    public function cambiarEstado(Request $request)
    {
        //if(!$request->ajax()) return redirect('/login');
        
        $personal = Personal::findOrFail($request->idPersonal);
        $personal->ACTIVO = $request->estado;
        $personal->save();
    }

    public function actualizar(Request $request)
    {
        //if(!$request->ajax()) return redirect('/login');
        $this->validate($request,[
            'nombre'=>'required',
            'apellidoPaterno'=>'required',
            'apellidoMaterno'=>'required',
            'telefono'=>'required',
            'tipoPersonal'=>'required',
            'direccion'=>'required',
        ]);

        $personal = Personal::findOrFail($request->COD_PERSONAL);
        $personal->NOMBRE = $request->nombre;
        $personal->AP_PATERNO = $request->apellidoPaterno;
        $personal->AP_MATERNO = $request->apellidoMaterno;
        $personal->TELEFONO = $request->telefono;
        $personal->TIPO_PERSONAL = $request->tipoPersonal;
        $personal->COD_EQUIPO = $request->grupo;
        $personal->DIRECCION = $request->direccion;
        $personal->COD_USUARIO = 0;
        $personal->ACTIVO = 1;
        $personal->save();

    }
}
