<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Cat_Servicio extends Model
{

    protected $table = 'cot_cat_servicio';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre','descripcion','activo','id_usuario_alta','fecha_alta','id_usuario_update','fecha_update',
                           'id_usuario_baja','fecha_baja'];
   
    public $timestamps = false;
   
}
