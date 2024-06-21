<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HOrdenTrabajoModelo extends Model
{
    protected $table = 'hist_orden_trabajo';
    protected $primaryKey = 'COD_HIST_ORDEN_TRABAJO';
    protected $fillable = ['COD_ORDEN_TRABAJO','ESTADO','COD_USUARIO','FECHA_ESTADO'];

    public $timestamps = false;
}
