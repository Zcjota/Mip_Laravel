<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'COD_MENU';

    protected $fillable = ['DESCRIPCION','ORDEN','ACTIVO','ICON'];

    public $timestamps = false;
}
