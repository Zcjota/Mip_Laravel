<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipos extends Model
{
    //equipos
    protected $table = 'equipo_tecnico';
    protected $primaryKey = 'COD_EQUIPO';
    protected $fillable = ['DESCRIPCION','ACTIVO'];
    public $timestamps = false;

    public function Equipos(){
        return $this->belongsTo('App\Personal');
    }
}
