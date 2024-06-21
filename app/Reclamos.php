<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reclamos extends Model
{
    protected $table = 'reclamos';
    protected $primaryKey = 'COD_RECLAMO';
    protected $fillable = ['COD_RECLAMO','COD_ORDEN_TRABAJO','DESCRIPCION','FECHA_RECLAMO'];

    public $timestamps = false;
}
