<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ZDetalleControlRecibosOT extends Model
{
    protected $table = 'z_detalle_control_recibos_ot';
    protected $primaryKey = 'cod_dcr';
    public $timestamps = false;
    protected $fillable = [
        'cod_cr',
        'cod_ot',
        'nro_ot',
        'cod_cliente_ot',
        'razon_social_ot',
        'precio_ot',
        'moneda_ot',
        'fecha_servicio_ot',
        'ACTIVO',
    ];
}
