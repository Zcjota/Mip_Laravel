<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\F_Fondo;

class F_FondoController extends Controller
{
    public function selectFondo(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $fondo= F_Fondo::where('activo','=','1')
        ->select('COD_FONDO','DESCRIPCION')->orderBy('DESCRIPCION','asc')->get();
        return ['fondo' => $fondo] ;
        // return response()->json($request);
    }
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $fondo=F_Fondo::orderBy('DESCRIPCION','asc')->paginate(30);
        }
        else
        {
           
            
            $fondo=F_Fondo::where('fin_fondo.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('fin_fondo.DESCRIPCION','asc')->paginate(30);
           
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $fondo->total(),
                'current_page'=>$fondo->currentPage(),
                'per_page'=> $fondo->perPage(),
                'last_page'=>$fondo->lastPage(),
                'from'=>$fondo->firstItem(),
                'to'=>$fondo->lastItem(),
            ],
            'fondo'=>$fondo
        ];

       
       
       
    }
    public function registrar(Request $request)
    {

       //return response()->json($request->descripcion);
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        $this->validarCamposRegistrar($request);
        $fondo= new F_Fondo();
        $fondo->DESCRIPCION= $request->descripcion;
        $fondo->ACTIVO=1;
        $fondo->save();
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
        $fondo =  F_Fondo::findOrFail($request->idfondo);
        $fondo->DESCRIPCION=$request->descripcion;
        $fondo->ACTIVO=1;
        $fondo->save();
       // $this->bitacora('E',$cargo->id);
            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }
    public function CambiarEstado(Request $request)
    {
        
        if(!$request->ajax()) return redirect('/login');
        $fondo =  F_Fondo::findOrFail($request->idfondo);
        $fondo->ACTIVO=$request->estado;
        $fondo->save();
    }
    public function validarCamposRegistrar(Request $request){
        
        $this->validate($request,[
            'descripcion'=>'required | unique:fin_fondo,DESCRIPCION',

            
        ]);
     }
}
