<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NumerosEnLetras;  //llamando la clase 

class NumeroLetrasController extends Controller
{
    public function getCovertNumberToLiteral(Request $request)
    {
        return NumerosEnLetras::convertir($request->numero, $request->moneda, $request->formato, $request->centavos);
    }
}
