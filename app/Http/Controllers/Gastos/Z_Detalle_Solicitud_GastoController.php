<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Z_Detalle_Solicitud_GastoController extends ApiController
{
    public function index(Request $request){
        $query=DB::table('z_solicitud_gasto as zsg')
        ->join('z_detalle_solicitud_gasto as dsg','zsg.cod_sg','=','dsg.cod_sg')
        ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
        ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
        ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
        ->selectRaw("
            dsg.cod_dsg,
            dsg.cod_sg,
            dsg.cod_control,
            zsg.fecha_sg,
            concat('C-',LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,
            zsg.nro_sg,
            concat(b.sigla, '-',bc.nro_cuenta,'-',bc.moneda) as banco_destino,
            pp.nombre_completo as nombre_proveedor,
            dsg.detalle,
            dsg.importe_bs, 
            dsg.importe_usd,
            zsg.glosa,
            dsg.cod_bc,
            dsg.BANDERA AS bandera
        ")
        // ->where('dsg.ACTIVO', '=', 1)
        // ->where('zsg.ACTIVO','=',1)
        // ->where('zsg.estado','=','APR')
        // ->where('zsg.tipo_sg','=','CREDITO')->orderBy('zsg.nro_sg', 'DESC')
        ->whereRaw(" 
            dsg.ACTIVO=1 AND zsg.ACTIVO=1 AND 
            dsg.cod_control = 0 AND zsg.estado='APR' AND
            zsg.tipo_sg='CREDITO' AND zsg.fecha_sg between '".$request->fechaInicio."' and '".$request->fechaFin."' AND
            (concat('C-',LPAD(zsg.nro_sg,5,0)) like '%".$request->buscar."%' or zsg.glosa like '%".$request->buscar."%')
        ")
        ->orderBy('zsg.nro_sg', 'DESC')
        
        ->paginate(10);
        return $this->responseOk($query);
    }
}
