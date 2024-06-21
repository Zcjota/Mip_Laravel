<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallesRoles extends Model
{
    protected $table = 'detallerol';

    protected $fillable = ['codRol','codSubMenu'];
    public $timestamps = false;
}
