<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    protected $table = 'detallereserva';
    protected $primaryKey = 'codDetReserva';
    protected $fillable = ['codTrabajador','codReserva','estadoTrabajador'];
    public $timestamps = false;

    public function personal(){
        return $this->belongsTo('App\Personal');
    }

    public function reserva(){
        return $this->belongsTo('App\Reserva');
    }

    public function Cliente(){
        return $this->belongsTo('App\Cliente');
    }

    public function OrdenTrabajo(){
        return $this->belongsTo('App\OrdenTrabajo');
    }

    
}
