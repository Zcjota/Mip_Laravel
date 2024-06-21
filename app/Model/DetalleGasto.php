<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DetalleGasto extends Model
{
    protected $table = 'detalle_gasto';
    protected $primaryKey = 'COD_DETALLE';
    public $timestamps = false;
    protected $fillable = [
        'DESCRIPCION',
        'NRO_FACTURA',
        'NIT',
        'NRO_RECIBO',
        'MONTO',
        'MONTO_USD',
        'COD_CUENTA',
        'COD_APERTURA',
        'FECHA',
        'TIPO_PAGO',
        'ACTIVO',
        'COD_SG',
        'CATEGORIA',
        'VC'
    ];
}
