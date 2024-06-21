<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_Cuenta extends Model
{
    protected $table = 'cuenta';
    protected $primaryKey = 'COD_CUENTA';
    protected $fillable = ['COD_FONDO','NRO_CUENTA','DESCRIPCION','ACTIVO'];

    public $timestamps = false;

    public function fondo(){
        return $this->belongsTo('App\F_Fondo');
    }

    public function nrocuenta(){
        return $this->belongsTo('App\F_NumeroCuenta');
    }

}
