<?php

namespace App\Http\Controllers;
use App\F_Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class F_AreaController extends Controller
{
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $f_area=F_Area::orderBy('DESCRIPCION','asc')->paginate(30);
        }
        else
        {
            $f_area=F_Area::where('fin_area.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('fin_area.DESCRIPCION','asc')->paginate(30);
        }
        return[
            'pagination'=>[
                'total' => $f_area->total(),
                'current_page'=>$f_area->currentPage(),
                'per_page'=> $f_area->perPage(),
                'last_page'=>$f_area->lastPage(),
                'from'=>$f_area->firstItem(),
                'to'=>$f_area->lastItem(),
            ],
            'f_area'=>$f_area
        ];

       
       
       
    }


    public function listarActivos(Request $request)
    {
        $buscar = $request->buscar;
        
        if($buscar==''){
            $f_area=F_Area::orderBy('DESCRIPCION','asc')
            ->where('ACTIVO',1)
            ->paginate(30);
        }
        else
        {
            $f_area=F_Area::where('fin_area.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('fin_area.DESCRIPCION','asc')->paginate(30);
        }
        return[
            'pagination'=>[
                'total' => $f_area->total(),
                'current_page'=>$f_area->currentPage(),
                'per_page'=> $f_area->perPage(),
                'last_page'=>$f_area->lastPage(),
                'from'=>$f_area->firstItem(),
                'to'=>$f_area->lastItem(),
            ],
            'f_area'=>$f_area
        ];
    }
    public function registrar(Request $request)
    {

        //return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        $this->validarCamposRegistrar($request);
        $f_area= new F_Area();
        $f_area->DESCRIPCION= $request->descripcion;
        $f_area->ACTIVO=1;
        $f_area->save();
       // $this->bitacora('R',$cargo->id);
        DB::commit();
        }
       
        catch (Exception $e){
           
            DB::rollBack();
        }
        
       
    }
    public function modificar(Request $request)
    {
     
        
        //return response()->json($request);
        
        if(!$request->ajax()) return redirect('/login');
        try
        {
            DB::beginTransaction();

        //$fecha_registro=date("Y-m-d H:i:s");
        $f_area =  F_Area::findOrFail($request->idarea);
        $f_area->DESCRIPCION=$request->descripcion;
        $f_area->ACTIVO=1;
        $f_area->save();
       // $this->bitacora('E',$cargo->id);
            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }
    public function CambiarEstado(Request $request)
    {
       // return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        $area =  F_Area::findOrFail($request->idarea);
        $area->ACTIVO=$request->estado;
        $area->save();
    }
    public function validarCamposRegistrar(Request $request){
        
        $this->validate($request,[
            'descripcion'=>'required | unique:fin_area,DESCRIPCION',

            
        ]);
     }
}
