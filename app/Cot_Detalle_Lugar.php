<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Detalle_Lugar extends Model
{
    protected $table = 'cot_detalle_lugar';
    protected $primaryKey = 'id';
    protected $fillable=[
         'id_detalle_servicio','id_lugar'
     ];
     public $timestamps = false;
}
