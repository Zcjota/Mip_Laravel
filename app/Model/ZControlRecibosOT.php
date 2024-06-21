<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ZControlRecibosOT extends Model
{
    protected $table = 'z_control_recibos_ot';
    protected $primaryKey = 'cod_cr';
    public $timestamps = false;
    protected $fillable = [
        'nro_recibo',
        'fecha',
        'recibi_de',
        'tipo_pago',
        'cod_bc_destino',
        'tipo_cambio',
        'importe_bs',
        'importe_usd',
        'total_usd',
        'monto_literal_usd',
        'glosa',
        'usuario_registro',
        'fecha_registro',
        'ACTIVO',
        'dif_ot',
        'dif',
    ];
}
