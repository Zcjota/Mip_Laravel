<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    protected $table = 'submenu';
    protected $primaryKey = 'COD_SUB_MENU';

    protected $fillable = ['COD_MENU','DESCRIPCION','RUTA','ACTIVO','ICON'];

    public $timestamps = false;
}
