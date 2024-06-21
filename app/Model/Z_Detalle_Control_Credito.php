<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Detalle_Control_Credito extends Model
{
    protected $table = 'z_detalle_control_credito';
    protected $primaryKey = 'cod_dcc';
    public $timestamps = false;
    protected $fillable = [
        'cod_control',
        'cod_dsg',
        'subtotal_bs',
        'subtotal_usd',
        'cod_bc_destino',
        'ACTIVO',
    ];
}
