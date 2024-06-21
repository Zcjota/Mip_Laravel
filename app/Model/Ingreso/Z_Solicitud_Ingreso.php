<?php

namespace App\Model\Ingreso;

use Illuminate\Database\Eloquent\Model;

class Z_Solicitud_Ingreso extends Model
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
    ];
}
