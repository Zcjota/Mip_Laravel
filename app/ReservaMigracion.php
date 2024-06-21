<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReservaMigracion extends Model
{
    protected $table = 'reserva_migracion';
    protected $primaryKey = 'codReserva';

    protected $fillable = ['fecha','horaInicio','tiempoServicio','tiempoTransporte','estado','tipo','codCliente','codOT','codPersonal'];

    public $timestamps = false;

}

