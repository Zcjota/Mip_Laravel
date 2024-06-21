<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_solicitud_prestamo extends Model
{
    protected $table = 'z_solicitud_prestamos';
    protected $primaryKey = 'cod_sp';
    public $timestamps = false;
    protected $fillable = [
        'cod_pp',
        'proveedor',
        'tipo_pago',
        'cod_bc_origen',
        'cod_bc_destino',
        'fecha_pago',
        'importe_bs',
        'importe_usd',
        'interes_porcentaje',
        'dias',
        'glosa',
        'estado',
        'fecha_modificacion',
        'modificado_por',
        'ACTIVO'
    ];
}
