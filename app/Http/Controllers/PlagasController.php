<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plagas;

class PlagasController extends Controller
{
    public function listaPlagas()
    {
        $cliente = Plagas::select('COD_PLAGA','DESCRIPCION')
        ->where('ACTIVO',1)
        ->orderBy('DESCRIPCION', 'asc')
        ->get();
        
        return $cliente;
    }
}
