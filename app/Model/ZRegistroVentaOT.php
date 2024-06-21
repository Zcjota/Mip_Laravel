<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ZRegistroVentaOT extends Model
{
    protected $table = 'registro_venta';
    public $timestamps = false;
    protected $fillable = [
        'COD_ORDEN',
        'CODNUM',
        'COD_ORDEN_TRABAJO',
        'DESCRIPCION',
        'MONTO_BS',
        'MONTO_DOLAR',
        'FECHA_RECIBO_OLD',
        'FECHA_RECIBO',
        'NRO_RECIBO',
        'CONTADO_NRO_DEPOSITO',
        'NRO_CHEQUE_NRO_DEPOSITO',
        'TRANSFERENCIA',
        'FECHA_FACTURA',
        'NRO_FACTURA',
        'DEBE',
        'FINANCIADO',
        'BANDERA',
        'ACTIVO',
        'OBSERVACION',
    ];
}
