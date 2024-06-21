<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HLugares extends Model
{
    protected $table = 'hs_lugares';
    protected $primaryKey = 'COD_HS_LUGARES';
    protected $fillable = ['COD_ORDEN_TRABAJO','COD_LUGARES','ACTIVO'];

    public $timestamps = false;
}
