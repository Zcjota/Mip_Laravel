<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reserva';
    protected $primaryKey = 'codReserva';

    protected $fillable = ['fecha','horaInicio','tiempoServicio','tiempoTransporte','estado','tipo','codCliente','codOT','codPersonal','estadoCoordinacion'];

    public $timestamps = false;

    public function cliente(){
        return $this->belongsTo('App\Cliente');
    }
}
