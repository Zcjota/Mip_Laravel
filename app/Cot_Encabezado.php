<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Encabezado extends Model
{
    protected $table = 'cot_encabezado_cotizacion';
    protected $primaryKey = 'id';
    protected $fillable = ['titulo','revision','fecha','codificacion','activo','id_usuario_alta','fecha_alta','id_usuario_update','fecha_update',
                           'id_usuario_baja','fecha_baja'];

    public $timestamps = false;
}
