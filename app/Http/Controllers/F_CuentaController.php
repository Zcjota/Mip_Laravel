<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\F_Cuenta;


class F_CuentaController extends Controller
{
    public function index(Request $request)
    { 
        $buscar = $request->buscar;
        $criterio= $request->criterio;
        //$idusuario=session()->get('codigo');
        if($buscar==''){
            
            $cuenta=F_Cuenta::leftJoin('nro_cuenta','cuenta.NRO_CUENTA','=','nro_cuenta.CODNUM')
            ->leftJoin('fin_fondo','cuenta.COD_FONDO','=','fin_fondo.COD_FONDO')  
            ->select('cuenta.COD_CUENTA','cuenta.COD_FONDO','cuenta.NRO_CUENTA','cuenta.DESCRIPCION','cuenta.ACTIVO',
            'nro_cuenta.CODNUM','nro_cuenta.DESCRIPCION as nro_cuenta','fin_fondo.COD_FONDO','fin_fondo.DESCRIPCION as fondo'
            )
            //->where('cuenta.id_personal','=',$idusuario)
            ->orderBy('cuenta.COD_CUENTA','desc')->paginate(30);
        }
        else
        {
            if($criterio=='Fecha')
            {
                $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
                ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
                )
                ->where('cot_cotizacion.fecha_cot','like','%'.$buscar.'%')
                ->where('cot_cotizacion.id_personal','=',$idusuario)
                ->orderBy('cot_cotizacion.fecha_cot','asc')->paginate(30);
            }
           
          
           // $cotizacion=Cot_Cotizacion::where('nombre like','%'.$buscar.'%')->orderBy('nombre','asc')->paginate(30);
        }
       // return response()->json($cuenta);
        return[
            'pagination'=>[
                'total' => $cuenta->total(),
                'current_page'=>$cuenta->currentPage(),
                'per_page'=> $cuenta->perPage(),
                'last_page'=>$cuenta->lastPage(),
                'from'=>$cuenta->firstItem(),
                'to'=>$cuenta->lastItem(),
            ],
            'cuenta'=>$cuenta
        ];  
    }



    public function listarActivos(Request $request)
    { 
        
        $buscar = $request->buscar;
        $criterio= $request->criterio;

        $cuenta = DB::table('cuenta as c')
            ->join('fin_fondo as f', 'f.COD_FONDO', '=', 'c.COD_FONDO')
            ->join('nro_cuenta as n', 'n.CODNUM', '=', 'c.NRO_CUENTA')
            ->select('c.*','f.DESCRIPCION as fondo','n.DESCRIPCION as nroCuenta')
            ->where('c.ACTIVO',1)
            ->where($criterio,'like','%'.$buscar.'%')
            ->orderBy('c.COD_CUENTA', 'desc')
            ->paginate(50);


        return[
            'pagination'=>[
                'total' => $cuenta->total(),
                'current_page'=>$cuenta->currentPage(),
                'per_page'=> $cuenta->perPage(),
                'last_page'=>$cuenta->lastPage(),
                'from'=>$cuenta->firstItem(),
                'to'=>$cuenta->lastItem(),
            ],
            'cuenta'=>$cuenta
        ];  
    }

    public function registrar(Request $request)
    {

        //return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        $this->validarCamposRegistrar($request);
        $cuenta= new F_Cuenta();
        $cuenta->COD_FONDO= $request->id_Fondo;
        $cuenta->NRO_CUENTA= $request->id_NroCuenta;
        $cuenta->DESCRIPCION= $request->descripcion;
        $cuenta->ACTIVO=1;
        $cuenta->save();
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
        $cuenta =  F_Cuenta::findOrFail($request->idcuenta);
        $cuenta->COD_FONDO= $request->id_Fondo;
        $cuenta->NRO_CUENTA= $request->id_NroCuenta;
        $cuenta->DESCRIPCION= $request->descripcion;
        $cuenta->ACTIVO=1;
        $cuenta->save();
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
        $cuenta =  F_Cuenta::findOrFail($request->idcuenta);
        $cuenta->ACTIVO=$request->estado;
        $cuenta->save();
    }
    public function validarCamposRegistrar(Request $request){
        
        $this->validate($request,[
            'descripcion'=>'required',
            'id_Fondo'=>'required', 
           'id_NroCuenta'=>'required',
            
        ]);
     }

}
