<?php

namespace App\Model\Ingreso;

use Illuminate\Database\Eloquent\Model;

class Z_Tipo_Solicitud_Ingreso extends Model
{
    protected $table = 'z_solicitud_tipo_ingreso';
    protected $primaryKey = 'tipo_sg';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'ACTIVO',
    ];
}
