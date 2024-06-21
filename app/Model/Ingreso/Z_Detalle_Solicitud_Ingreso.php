<?php

namespace App\Model\Ingreso;

use Illuminate\Database\Eloquent\Model;

class Z_Detalle_Solicitud_Ingreso extends Model
{
    protected $table = 'z_detalle_solicitud_gasto';
    protected $primaryKey = 'cod_dsg';
    public $timestamps = false;
    protected $fillable = [
        'cod_sg',
        'cod_proveedor',
        'cod_cc',
        'detalle',
        'importe_bs',
        'importe_usd',
        'cod_bc',
        'ACTIVO',
    ];
}
