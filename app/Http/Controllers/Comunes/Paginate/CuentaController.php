<?php

namespace App\Http\Controllers\Comunes\Paginate;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Cuenta;
use Illuminate\Support\Facades\DB;

class CuentaController extends ApiController
{
    public function Buscar(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        $buscar = $request->buscar;
        $query = DB::table('cuenta as cue')
            ->join('fin_fondo as ffo', 'cue.COD_FONDO', '=', 'ffo.COD_FONDO')
            ->select(
                'cue.COD_CUENTA',
                'cue.NRO_CUENTA',
                'cue.DESCRIPCION as cueDescripcion',
                'ffo.COD_FONDO',
                'ffo.DESCRIPCION as fondoDescripcion'
            )
            ->where("cue.ACTIVO", "=", "1");
        if ($buscar != null) {
            if (is_numeric($buscar)) {
                $query = $query->whereRaw("cast(cue.NRO_CUENTA as char(100)) like '%$buscar%'");
            } else {
                $query = $query->whereRaw("cue.DESCRIPCION like '%$buscar%'");
            }
        }
        $result = $query->paginate(400);
        return $this->responseOk($result);
    }
}
