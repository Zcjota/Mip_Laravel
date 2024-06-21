<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Control_Credito extends Model
{
    protected $table = 'z_control_credito';
    protected $primaryKey = 'cod_control';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'glosa',
        'cod_bc_origen',
        'total_bs',
        'total_usd',
        'fecha_registro',
        'usuario_registro',
        'ACTIVO',
    ];
}
