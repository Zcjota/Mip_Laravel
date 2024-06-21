<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plagas extends Model
{
    protected $table = 'plagas';
    protected $primaryKey = 'COD_PLAGA';
    protected $fillable = ['COD_PLAGA','DESCRIPCION','ACTIVO'];

    public $timestamps = false;
}
