<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SubMenu;
use App\Menu;

class SubMenuController extends Controller
{
    public function acceso(Request $request){
        $sub_menu = new SubMenu;
        $menu = new Menu;

        $result['sub_menu'] = $sub_menu->acceso($request->id);
        $result['menu'] = $menu->listarMenuAcceso($request->id);

        return $result;
    }

    public function listarMenuId(Request $request){
        $result = new SubMenu;
        $result = $result->listarMenuId($request->id);

        return $result;
    }
}
