<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personal';
    protected $primaryKey = 'COD_PERSONAL';
    protected $fillable = ['NOMBRE','AP_PATERNO','AP_MATERNO','DIRECCION','TELEFONO','TIPO_PERSONAL','COD_EQUIPO','COD_USUARIO','ACTIVO'];

    public $timestamps = false;

    public function Equipos(){
        return $this->belongsTo('App\Equipos');
    }
}
