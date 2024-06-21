<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegistrarProveedorPersonal;
use Illuminate\Support\Facades\DB;
use App\Model\Z_Prov_Pers;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonalProveedor\PersonalExport;

class ProveedorPersonalController extends Controller
{
    public function listaProveedorPersonal(Request $request)
    {
        $charset = "utf8mb4";
        if(!$request->ajax()) return redirect('/login');
        $buscar=$request->buscar;
        // if($request->buscar==''){
            $proveedor_personal=Z_Prov_Pers::leftJoin('fin_fondo','z_prov_pers.fondo','=','fin_fondo.COD_FONDO') 
            ->leftJoin('nro_cuenta','z_prov_pers.nro_cuenta','=','nro_cuenta.CODNUM') 
            ->leftJoin('cuenta','z_prov_pers.cuenta','=','cuenta.COD_CUENTA')
            ->leftJoin('z_banco_cuenta as bc', 'z_prov_pers.cod_bc','=', 'bc.cod_bc')
            ->join('z_banco as bb','bc.cod_b','=','bb.cod_b')
            ->select(
                'z_prov_pers.id','z_prov_pers.tipo','z_prov_pers.nombre_completo','z_prov_pers.fondo','z_prov_pers.nro_cuenta',
                'z_prov_pers.cuenta','z_prov_pers.activo','fin_fondo.DESCRIPCION as nombre_fondo','nro_cuenta.DESCRIPCION as nombre_nro_cuenta',
                'cuenta.DESCRIPCION as nombre_cuenta', 'z_prov_pers.cod_bc'
                , 'bb.sigla as b_sigla', 'bb.nombre as b_nombre'
                , 'bc.nro_cuenta as bc_cuenta', 'bc.moneda as bc_moneda' , 'z_prov_pers.elim'
            )
            ->where('z_prov_pers.elim = 0')
            ->where('z_prov_pers.nombre_completo', 'like', '%' . $buscar . '%')
                ->orWhere('bc.nro_cuenta', 'like', '%' . $buscar . '%')
            ->orderBy('z_prov_pers.id','desc')->paginate(20);
        // }
        // else
        // {
        //     $proveedor_personal=Z_Prov_Pers::where('nombre_completo','like','%'.$request->buscar.'%')->orderBy('id','desc')->paginate(20);
           /* $cliente = ProveedorPersonal::
            where(DB::raw('concat(NOMBRE," ",AP_CLIENTE)'),'like','%'.$request->buscar.'%')
            ->where('ACTIVO',1)
            ->orWhere('cliente.TELEFONO',$request->buscar)
            //where('NOMBRE','like','%'.$request->buscar.'%')
            ->orderBy('COD_CLIENTE', 'desc')
            ->paginate(20);*/
        // }


        
        return [
            'pagination' => [
                'total'        => $proveedor_personal->total(),
                'current_page' => $proveedor_personal->currentPage(),
                'per_page'     => $proveedor_personal->perPage(),
                'last_page'    => $proveedor_personal->lastPage(),
                'from'         => $proveedor_personal->firstItem(),
                'to'           => $proveedor_personal->lastItem(),
            ],
            'proveedor_personal' => $proveedor_personal
        ];
    } 

    public function exportarExcel(Request $request)
{
    $export = new PersonalExport(
        $request->buscar,
        // $request->tipoMovimiento,
        // $request->estado,
        // $request->fechaFin,
        // $request->fechaInicio
    );

    return Excel::download($export, 'Personal-Proveedor.xlsx');
}

    public function cambiarEstado(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');
        
        $proveedor_personal = Z_Prov_Pers::findOrFail($request->id);
        $proveedor_personal->activo = $request->estado;
        $proveedor_personal->save();
    }
    public function selectFondo(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $fondo = DB::table('fin_fondo')
            ->select('COD_FONDO', 'DESCRIPCION')
            ->orderBy('fin_fondo.DESCRIPCION','asc')
            ->get();
        return ['fondo' => $fondo] ;
        // return response()->json($request);
    }
    public function selectNroCuenta(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $nro_cuenta = DB::table('nro_cuenta')
            ->select('CODNUM', 'DESCRIPCION')
            ->orderBy('nro_cuenta.DESCRIPCION','asc')
            ->get();
        return ['nro_cuenta' => $nro_cuenta] ;
        // return response()->json($request);
    }
    public function selectCuenta(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $cuenta = DB::table('cuenta')
            ->select('COD_CUENTA', 'DESCRIPCION')
            ->orderBy('cuenta.DESCRIPCION','asc')
            ->get();
        return ['cuenta' => $cuenta] ;
        // return response()->json($request);
    }
    public function listCuenta(Request $request){
        // $charset = "utf8mb4";
        // if(!$request->ajax()) return redirect('/login');
        // $buscar=$request->buscar;
        // // if($request->buscar==''){
        //     $cuenta = DB::table('cuenta AS A')
        //     ->leftJoin('nro_cuenta AS ','A.NRO_CUENTA','=','nro_cuenta.CODNUM') 
        //     ->leftJoin('cuenta','z_prov_pers.cuenta','=','cuenta.COD_CUENTA')
        //     ->leftJoin('z_banco_cuenta as bc', 'z_prov_pers.cod_bc','=', 'bc.cod_bc')
        //     ->join('z_banco as bb','bc.cod_b','=','bb.cod_b')
        //     ->select(
        //         'z_prov_pers.id','z_prov_pers.tipo','z_prov_pers.nombre_completo','z_prov_pers.fondo','z_prov_pers.nro_cuenta',
        //         'z_prov_pers.cuenta','z_prov_pers.activo','fin_fondo.DESCRIPCION as nombre_fondo','nro_cuenta.DESCRIPCION as nombre_nro_cuenta',
        //         'cuenta.DESCRIPCION as nombre_cuenta', 'z_prov_pers.cod_bc'
        //         , 'bb.sigla as b_sigla', 'bb.nombre as b_nombre'
        //         , 'bc.nro_cuenta as bc_cuenta', 'bc.moneda as bc_moneda'
        //     )
        //     ->where('z_prov_pers.nombre_completo', 'like', '%' . $buscar . '%')
        //         ->orWhere('bc.nro_cuenta', 'like', '%' . $buscar . '%')
        //     ->orderBy('z_prov_pers.id','desc')->paginate(20);
        // return [
        //     'pagination' => [
        //         'total'        => $proveedor_personal->total(),
        //         'current_page' => $proveedor_personal->currentPage(),
        //         'per_page'     => $proveedor_personal->perPage(),
        //         'last_page'    => $proveedor_personal->lastPage(),
        //         'from'         => $proveedor_personal->firstItem(),
        //         'to'           => $proveedor_personal->lastItem(),
        //     ],
        //     'proveedor_personal' => $proveedor_personal
        // ];

        // if(!$request->ajax()) return redirect('/login');
        // $cuenta = DB::table('cuenta')
        //     ->select('COD_CUENTA', 'DESCRIPCION')
        //     ->orderBy('cuenta.DESCRIPCION','asc')
        //     ->get();
        // return ['cuenta' => $cuenta] ;
        // // return response()->json($request);
    }
    public function registrar(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');
        if($request->tipo==1)
        {
            //return response()->json($request); 
            $proveedor_personal= new Z_Prov_Pers();
            $proveedor_personal->tipo=$request->tipo;
            $proveedor_personal->nombre_completo=strtoupper($request->nombre);
            $proveedor_personal->cod_bc = $request->cod_bc;
            $proveedor_personal->activo=1;
            $proveedor_personal->save();
        }
        else{
            $proveedor_personal= new Z_Prov_Pers();   
            $proveedor_personal->tipo=$request->tipo;
            $proveedor_personal->nombre_completo=strtoupper($request->nombre);
            $proveedor_personal->nro_cuenta=$request->nro_cuenta;
            $proveedor_personal->cuenta=$request->cuenta;
            $proveedor_personal->fondo=$request->fondo;
            $proveedor_personal->cod_bc = $request->cod_bc;
            $proveedor_personal->activo=1;
            $proveedor_personal->save();
        }
    }
    public function modificar(Request $request)
    {
        //return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        $proveedor_personal =  Z_Prov_Pers::findOrFail($request->id);
        $proveedor_personal->tipo=$request->tipo;
        $proveedor_personal->nombre_completo=strtoupper($request->nombre);
        $proveedor_personal->nro_cuenta=$request->nro_cuenta;
        $proveedor_personal->cuenta=$request->cuenta;
        $proveedor_personal->fondo=$request->fondo;
        $proveedor_personal->cod_bc = $request->cod_bc;
        $proveedor_personal->activo=1;
        $proveedor_personal->save();
       // $this->bitacora('E',$cargo->id);
          
    }
}
