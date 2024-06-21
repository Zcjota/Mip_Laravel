<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetalleEquipo;
class DetalleEquipoController extends Controller
{

    public function listado()
    {
        $detalleEquipos = DetalleEquipo::all();
        return $detalleEquipos;
    }
}
