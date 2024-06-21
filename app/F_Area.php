<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_Area extends Model
{
    protected $table = 'fin_area';
    protected $primaryKey = 'COD_AREA';
    protected $fillable = ['DESCRIPCION','ACTIVO'];
   
    public $timestamps = false;
}
