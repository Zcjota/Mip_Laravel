<?php

namespace App\Http\Controllers\Comunes\Combo;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Banco;

class Z_BancoController extends ApiController
{
    public function ListarTodo()
    {
        // if(!$request->ajax()) return redirect('/login');
        $result = Z_Banco::all();
        return $this->responseOk($result);
    }
}
