<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Logs extends Model
{
    protected $table = 'z_logs';
    protected $primaryKey = 'log_id';
    public $timestamps = false;
    protected $fillable = [
        'log_tabla',
        'log_codigo',
        'log_json',
        'log_estado',
        'log_descripcion',
        'log_fecha_registro',
        'log_cod_usuario',
    ];
}
