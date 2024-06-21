<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Detalle_Solicitud_Ingresos extends Model
{
    protected $table = 'z_detalle_solicitud_ingreso';
    protected $primaryKey = 'cod_dsi';
    public $timestamps = false;
    protected $fillable = [
        'cod_si',
        'cod_proveedor',
        'cod_cc',
        'detalle',
        'importe_bs',
        'importe_usd',
        'cod_bc',
        'ACTIVO',
    ];
}
