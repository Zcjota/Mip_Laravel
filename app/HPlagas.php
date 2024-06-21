<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HPlagas extends Model
{
    protected $table = 'hs_plagas';
    protected $primaryKey = 'COD_HS_PLAGAS';
    protected $fillable = ['COD_ORDEN_TRABAJO','COD_PLAGA','ACTIVO'];

    public $timestamps = false;
}
