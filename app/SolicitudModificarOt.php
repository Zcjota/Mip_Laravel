<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudModificarOt extends Model
{
    protected $table = 'solicitud_modf_ot';
    protected $primaryKey = 'COD_HIST_MOT';

    protected $fillable = ['COD_ORDEN_TRABAJO','DESCRIPCION','FECHA','ESTADO','COD_USUARIO','FECHA_RESP','ACTIVO'];

    public $timestamps = false;

    
}
