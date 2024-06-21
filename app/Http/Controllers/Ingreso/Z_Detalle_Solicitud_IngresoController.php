<?php

namespace App\Http\Controllers\Ingreso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Z_Detalle_Solicitud_IngresoController extends Controller
{
    public function index(Request $request){
        $query=DB::table('z_solicitud_ingreso as zsg')
        ->join('z_detalle_solicitud_ingreso as dsg','zsg.cod_si','=','dsg.cod_si')
        ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
        ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
        ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
        ->selectRaw("
            dsg.cod_dsi,
            dsg.cod_si,
            dsg.cod_control,
            zsg.fecha_si,
            concat('C-',LPAD(zsg.fecha_si,5,0)) as nroCorrelativo,
            zsg.fecha_si,
            concat(b.sigla, '-',bc.nro_cuenta,'-',bc.moneda) as banco_destino,
            pp.nombre_completo as nombre_proveedor,
            dsg.detalle,
            dsg.importe_bs, 
            dsg.importe_usd,
            zsg.glosa,
            dsg.cod_bc
        ")
        // ->where('dsg.ACTIVO', '=', 1)
        // ->where('zsg.ACTIVO','=',1)
        // ->where('zsg.estado','=','APR')
        // ->where('zsg.cod_tsi','=','CREDITO')->orderBy('zsg.fecha_si', 'DESC')
        ->whereRaw(" 
            dsg.ACTIVO=1 AND zsg.ACTIVO=1 AND 
            dsg.cod_control = 0 AND zsg.estado='APR' AND
            zsg.cod_tsi= 2 AND zsg.fecha_si between '".$request->fechaInicio."' and '".$request->fechaFin."' AND
            (concat('C-',LPAD(zsg.fecha_si,5,0)) like '%".$request->buscar."%' or zsg.glosa like '%".$request->buscar."%')
        ")
        ->orderBy('zsg.fecha_si', 'DESC')
        
        ->paginate(10);
        return $this->responseOk($query);
    }
}
