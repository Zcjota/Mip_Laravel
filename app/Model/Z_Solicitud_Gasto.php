<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Solicitud_Gasto extends Model
{
    protected $table = 'z_solicitud_gasto';
    protected $primaryKey = 'cod_sg';
    public $timestamps = false;
    protected $fillable = [
        'tipo_sg',
        'fecha_registro',
        'fecha_sg',
        'cod_usuario',
        'nro_sg',
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
        'vc'
    ];
}
