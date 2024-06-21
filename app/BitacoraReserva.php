<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitacoraReserva extends Model
{
    protected $table = 'bitacora_Reserva';
    protected $primaryKey = 'codBitacora';
    protected $fillable = ['fecha','codReserva','codPersonal','tipo','activo'];

    public $timestamps = false;
}
