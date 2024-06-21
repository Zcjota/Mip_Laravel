<?php

namespace App\Http\Controllers\Comunes\Combo;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Prov_Pers;
use Illuminate\Support\Facades\DB;

class Z_PersonaController extends ApiController
{
    public function ListarTodo()
    {
        $result = Z_Prov_Pers::where('tipo', 1)->get(); // Filtrar por tipo = 1
        return $this->responseOk($result);
        // // if(!$request->ajax()) return redirect('/login');
        // $result = Z_Prov_Pers::all();
        // return $this->responseOk($result);
    }

    public function ListarCuenta(Request $request)
    {
        // nombre
        $query = DB::table('z_prov_pers as zpp')
        ->join('z_banco_cuenta as zbc','zpp.cod_bc','=','zbc.cod_bc')
        ->join('z_banco as zba','zbc.cod_b','=','zba.cod_b')
        ->leftjoin('cuenta as cue','zpp.cuenta','=','cue.COD_CUENTA')
        ->leftjoin('fin_fondo as ffo','zpp.fondo','=','ffo.COD_FONDO')
        ->selectRaw('zpp.id,(case when zpp.tipo=1 then "PERSONAL"  when zpp.tipo=2 then "PROVEEDOR"  else null end) as tipoPersona, zpp.fondo, zpp.nombre_completo,
        ffo.COD_FONDO, ffo.DESCRIPCION as fondoDescripcion, cue.COD_CUENTA as cuenta_COD_CUENTA, cue.NRO_CUENTA, cue.DESCRIPCION as cuentaDescripcion,
        zbc.cod_bc, zba.cod_b, zba.sigla, zba.nombre as banco_nombre,
        zbc.nro_cuenta as bancoCuenta_nro_cuenta, zbc.moneda')
            ->where('zpp.nombre_completo', 'like', '%' . $request->buscar . '%')
            ->where('zpp.activo','=',1)
            ->where('zpp.tipo','=', 1)
            ->paginate(10);
        return $this->responseOk($query);
    }

}
