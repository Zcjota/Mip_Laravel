<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_MovimientoBancoCuenta extends Model
{
    protected $table = 'z_movimiento_banco_cuenta';
    protected $primaryKey = 'cod_mbc';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'tipo_movimiento',
        'cod_bc_origen',
        'cod_bc_destino',
        'importe_bs',
        'importe_usd',
        'glosa',
        'ACTIVO',
        'fecha_modificacion',
        'modificado_por',
    ];
}
