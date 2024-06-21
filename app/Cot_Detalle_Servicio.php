<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Detalle_Servicio extends Model
{
    protected $table = 'cot_detalle_servicio';
    protected $primaryKey = 'id';
    protected $fillable=[
         'id_servicio','id_cotizacion','monto_sub',
         'activo'
     ];
     public $timestamps = false;
}
