<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\F_NumeroCuenta;

class F_NumeroCuentaController extends Controller
{
    public function selectNroCuenta(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $nrocuenta= F_NumeroCuenta::where('activo','=','1')
        ->select('CODNUM','DESCRIPCION')->orderBy('DESCRIPCION','asc')->get();
        return ['nrocuenta' => $nrocuenta] ;
        // return response()->json($request);
    }
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $numero_cuenta=F_NumeroCuenta::orderBy('DESCRIPCION','asc')->paginate(30);
        }
        else
        {
           
            
            $numero_cuenta=F_NumeroCuenta::where('nro_cuenta.DESCRIPCION', 'like','%'.$buscar.'%')
            ->orderBy('nro_cuenta.DESCRIPCION','asc')->paginate(30);
           
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $numero_cuenta->total(),
                'current_page'=>$numero_cuenta->currentPage(),
                'per_page'=> $numero_cuenta->perPage(),
                'last_page'=>$numero_cuenta->lastPage(),
                'from'=>$numero_cuenta->firstItem(),
                'to'=>$numero_cuenta->lastItem(),
            ],
            'numero_cuenta'=>$numero_cuenta
        ];

       
       
       
    }
    public function registrar(Request $request)
    {

       //return response()->json($request->descripcion);
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        $this->validarCamposRegistrar($request);
        $numero_cuenta= new F_NumeroCuenta();
        $numero_cuenta->DESCRIPCION= $request->descripcion;
        $numero_cuenta->ACTIVO=1;
        $numero_cuenta->save();
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
        $numero_cuenta =  F_NumeroCuenta::findOrFail($request->idnumero_cuenta);
        $numero_cuenta->DESCRIPCION=$request->descripcion;
        $numero_cuenta->ACTIVO=1;
        $numero_cuenta->save();
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
        $numero_cuenta =  F_NumeroCuenta::findOrFail($request->idnumero_cuenta);
        $numero_cuenta->ACTIVO=$request->estado;
        $numero_cuenta->save();
    }
    public function validarCamposRegistrar(Request $request){
        
        $this->validate($request,[
            'descripcion'=>'required | unique:nro_cuenta,DESCRIPCION',

            
        ]);
     }
}
