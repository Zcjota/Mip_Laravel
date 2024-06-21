<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuenta';
    protected $primaryKey = 'COD_CUENTA';
    public $timestamps = false;
    protected $fillable = [
        'COD_FONDO',
        'NRO_CUENTA',
        'DESCRIPCION',
        'ACTIVO'
        
    ];
}
