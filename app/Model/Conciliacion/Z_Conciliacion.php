<?php

namespace App\Model\Conciliacion;

use Illuminate\Database\Eloquent\Model;

class Z_Conciliacion extends Model
{
    protected $table = 'conciliacion_bancaria';
    protected $primaryKey = 'cod_cs';
    public $timestamps = false;
    protected $fillable = [
        'tipo',
        'fecha_conciliacion',
        'fecha_banco',
        'banco_origen',
        'banco_destino',
        'monto',
        'id_solicitud',
        'id_detalle_solicitud',
        'nota',
        'monto_banco',
        'ACTIVO',
        'BANDERA',
    ];
}
