<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Solicitud_Ingresos extends Model
{
    protected $table = 'z_solicitud_ingreso';
    protected $primaryKey = 'cod_si';
    public $timestamps = false;
    protected $fillable = [
        'cod_tsi',
        'fecha_registro',
        'fecha_si',
        'cod_usuario',
        'nro_si',
        'gestion',
        'tipo_pago',
        'cod_bc',
        'total_bs',
        'total_usd',
        'fecha_pago',
        'glosa',
        'estado',
        'revisado_por_usuario',
        'fecha_revision',
        'ACTIVO',
        'tipo_gasto',
    ];
}
