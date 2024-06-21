<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleReservaMigracion extends Model
{
    protected $table = 'detallereserva_migracion';
    protected $primaryKey = 'codDetReserva';
    protected $fillable = ['codTrabajador','codReserva','estadoTrabajador'];
    public $timestamps = false;

    
    
    public function personal(){
        return $this->belongsTo('App\Personal');
    }

    public function reserva(){
        return $this->belongsTo('App\ReservaMigracion');
    }

    public function Cliente(){
        return $this->belongsTo('App\Cliente');
    }

    public function OrdenTrabajo(){
        return $this->belongsTo('App\OrdenTrabajo');
    }
    
}
