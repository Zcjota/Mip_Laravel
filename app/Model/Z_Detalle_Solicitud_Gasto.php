<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Detalle_Solicitud_Gasto extends Model
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
