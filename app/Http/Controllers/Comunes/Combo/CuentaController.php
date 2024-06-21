<?php

namespace App\Http\Controllers\Comunes\Combo;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Cuenta;

class CuentaController extends ApiController
{
    public function ListarTodo(Request $request)
    {
        // if(!$request->ajax()) return redirect('/login');
        $result = Cuenta::all();
        return $this->responseOk($result);
    }
}