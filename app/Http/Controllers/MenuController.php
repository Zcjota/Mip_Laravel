<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Menu;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function listarMenu(){
        $menu = new Menu;
        $menu = $menu->listarMenu();

        return $menu;
    }    
}
