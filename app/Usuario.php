<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'COD_USUARIO';
    protected $fillable = ['COD_USUARIO','NOMBRE','AP_PATERNO','AP_MATERNO','LOGIN','PASSWORD','CORREO','COD_TIPOU','ACTIVO'];

    public $timestamps = false;

    public function personal(){
        return $this->belongsTo('App\Personal');
    }

    public function rol(){
        return $this->belongsTo('App\Roles');
    }
    
    public function TipoUsuario(){
        return $this->belongsTo('App\TipoUsuario');
    }
}
