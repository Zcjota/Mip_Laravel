<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    protected $table = 'apertura';
    protected $primaryKey = 'COD_APERTURA';
    public $timestamps = false;
    protected $fillable = [
        'MES',
        'GESTION',
        'ACTIVO',
        'ESTADO',
    ];
}
