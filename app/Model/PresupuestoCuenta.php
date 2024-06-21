<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PresupuestoCuenta extends Model
{
    protected $table = 'presupuesto_cuenta';
    protected $primaryKey = ['COD_CUENTA'];
    public $timestamps = false;
    protected $fillable = [
        'MONTO_PRESUPUESTO',
        'DESCRIPCION',
        'ACTIVO',
        'EXCEDIDO',
        'TOTAL',
    ];
}
