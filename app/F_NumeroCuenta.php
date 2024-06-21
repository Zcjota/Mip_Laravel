<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_NumeroCuenta extends Model
{
    protected $table = 'nro_cuenta';
    protected $primaryKey = 'CODNUM';
    protected $fillable = ['DESCRIPCION','ACTIVO'];
   
    public $timestamps = false;
}
