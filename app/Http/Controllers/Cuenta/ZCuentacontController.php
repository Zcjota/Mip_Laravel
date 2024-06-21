<?php

namespace App\Http\Controllers\Cuenta;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
// use App\Http\Requests\RegistrarCuentaContable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Cuenta;
// use App\Model\Cuenta;
use Exception; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Cuenta\CuentaContExport;


class ZCuentacontController extends ApiController
{
    
    public function listacuenta(Request $request)
    {
        $charset = "utf8mb4";
        if(!$request->ajax()) return redirect('/login');
        $buscar=$request->buscar;
        // if($request->buscar==''){
            $cuenta_contable=Cuenta::leftjoin('fin_fondo AS c','cuenta.COD_FONDO','=','c.COD_FONDO') 
            ->leftjoin('nro_cuenta AS b', 'cuenta.NRO_CUENTA', '=', 'b.CODNUM')
            ->select(
                'cuenta.COD_CUENTA as id','c.DESCRIPCION as nombre_fondo','b.DESCRIPCION as nombre_nro_cuenta',
                'cuenta.DESCRIPCION as nombre_cuenta','cuenta.ACTIVO as activo',
                'cuenta.COD_FONDO as cod_fondo','cuenta.NRO_CUENTA as cod_cuentas'
            )
            ->where('cuenta.DESCRIPCION', 'like', '%' . $buscar . '%')
                // ->orWhere('b.DESCRIPCION', 'like', '%' . $buscar . '%')
            ->orderBy('cuenta.COD_CUENTA','desc')->paginate(20);
        return [
            'pagination' => [
                'total'        => $cuenta_contable->total(),
                'current_page' => $cuenta_contable->currentPage(),
                'per_page'     => $cuenta_contable->perPage(),
                'last_page'    => $cuenta_contable->lastPage(),
                'from'         => $cuenta_contable->firstItem(),
                'to'           => $cuenta_contable->lastItem(),
            ],
            'cuenta_contable' => $cuenta_contable
        ];
    } 

    public function exportarExcel(Request $request)
{
    $export = new CuentaContExport(
        $request->buscar,
        // $request->tipoMovimiento,
        // $request->estado,
        // $request->fechaFin,
        // $request->fechaInicio
    );

    return Excel::download($export, 'Cuentas-Contables.xlsx');
}

    public function cambiarEstado(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');
        
        $cuenta_contable = Cuenta::findOrFail($request->id);
        $cuenta_contable->activo = $request->estado;
        $cuenta_contable->save();
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
    public function registrar(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');
        if($request->tipo==1)
        {
            //return response()->json($request); 
            $cuenta_contable= new Cuenta();
            // $cuenta_contable->tipo=$request->tipo;
            $cuenta_contable->DESCRIPCION=strtoupper($request->nombre);
            $cuenta_contable->NRO_CUENTA=$request->nro_cuenta;
            $cuenta_contable->COD_FONDO=$request->fondo;
            // $cuenta_contable->cod_bc = $request->cod_bc;
            $cuenta_contable->ACTIVO=1;
            $cuenta_contable->save();
        }
        else{
            $cuenta_contable= new Cuenta();   
            // $cuenta_contable->tipo=$request->tipo;
            $cuenta_contable->DESCRIPCION=strtoupper($request->nombre);
            $cuenta_contable->NRO_CUENTA=$request->nro_cuenta;
            // $cuenta_contable->cuenta=$request->cuenta;
            $cuenta_contable->COD_FONDO=$request->fondo;
            // $cuenta_contable->cod_bc = $request->cod_bc;
            $cuenta_contable->ACTIVO=1;
            $cuenta_contable->save();
        }
    }
    public function modificar(Request $request)
    {
        //return response()->json($request);
        if(!$request->ajax()) return redirect('/login');
        $cuenta_contable =  Cuenta::findOrFail($request->id);
        // $cuenta_contable->tipo=$request->tipo;
        $cuenta_contable->DESCRIPCION=strtoupper($request->nombre);
        $cuenta_contable->NRO_CUENTA=$request->nro_cuenta;
        // $cuenta_contable->cuenta=$request->cuenta;
        $cuenta_contable->COD_FONDO=$request->fondo;
        // $cuenta_contable->cod_bc = $request->cod_bc;
        $cuenta_contable->ACTIVO=1;
        $cuenta_contable->save();
       // $this->bitacora('E',$cargo->id);
          
    }
}
