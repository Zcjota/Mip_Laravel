<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lugares;
class LugaresController extends Controller
{
    public function listaLugares()
    {
        $lugares = Lugares::select('COD_LUGARES','DESCRIPCION')
        ->where('ACTIVO',1)
        ->orderBy('DESCRIPCION', 'asc')
        ->get();
        
        return $lugares;


    }
}
