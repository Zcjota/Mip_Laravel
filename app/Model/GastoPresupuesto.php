<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GastoPresupuesto extends Model
{
    protected $table = 'gasto_presupuesto';
    protected $primaryKey = 'COD_GASTO';
    public $timestamps = false;
    protected $fillable = [
        'MONTO_GASTO',
        'DESCRIPCION',
        'FECHA_GASTO',
        'COD_CUENTA',
        'COD_APERTURA',
    ];
}
