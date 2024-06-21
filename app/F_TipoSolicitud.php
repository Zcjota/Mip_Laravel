<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_TipoSolicitud extends Model
{
    protected $table = 'fin_tiposolicitud';
    protected $primaryKey = 'COD_TSE';
    protected $fillable = ['NOMBRE','DESCRIPCION','MOVIMIENTO','ORIGEN','REV_ISO','FECHA_ISO','COD_ISO',
    'ACTIVO'];
   
    public $timestamps = false;
}
