<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_Detalle_Solicitud extends Model
{
    protected $table = 'fin_detalle_solicitud';
    protected $primaryKey = 'COD_DETALLE';
    protected $fillable = ['COD_SE','RESP_PROV','DETALLE','MONEDA','IMPORTE','CANT','COD_UNIDAD','SUBTOTAL','ACTIVO'];
   
    public $timestamps = false;
}
