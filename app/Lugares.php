<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lugares extends Model
{
    protected $table = 'lugares';
    protected $primaryKey = 'COD_LUGARES';
    protected $fillable = ['COD_LUGARES','DESCRIPCION','ACTIVO'];

    public $timestamps = false;
}
