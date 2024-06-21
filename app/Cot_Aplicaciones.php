<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Aplicaciones extends Model
{
    protected $table = 'cot_aplicaciones';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre','descripcion','activo'];
   
    public $timestamps = false;
}
