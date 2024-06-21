<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_Fondo extends Model
{
    protected $table = 'fin_fondo';
    protected $primaryKey = 'COD_FONDO';
    protected $fillable = ['DESCRIPCION','ACTIVO'];
   
    public $timestamps = false;
}
