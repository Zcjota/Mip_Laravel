<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlNocturno extends Model
{
    protected $table = 'control_Nocturno';
    protected $primaryKey = 'codControlNocturno';
    protected $fillable = ['fechaFinalizacion','activo','codPersonal','codReserva','incremento'];
    public $timestamps = false;
}
