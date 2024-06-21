<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Notas extends Model
{
    protected $table = 'cot_nota';
    protected $primaryKey = 'id';
    protected $fillable = ['id_cotizacion','descripcion'];
   
    public $timestamps = false;
}
