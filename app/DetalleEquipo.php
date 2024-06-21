<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleEquipo extends Model
{
    protected $table = 'detalleequipo';

    protected $fillable = ['codEquipo','codPersonal'];
    public $timestamps = false;
}
