<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'COD_CLIENTE';
    protected $fillable = ['COD_CLIENTE','AP_CLIENTE','AM_CLIENTE','NOMBRE','CI_NIT','RAZON_SOCIAL','CORREO','DIRECCION','TELEFONO','CONTACTO','COD_TIPO_CLIENTE','CIUDAD','CATEGORIA',
    'FRECUENCIA_SERVICIO','COD_EJECUTIVO','JESUS','C_NOMB_P','C_NOMB_S','C_DIRECCION','ACTIVO'];

    /*
     protected $fillable = ['COD_CLIENTE','AP_CLIENTE','AM_CLIENTE','NOMBRE','CI_NIT','RAZON_SOCIAL','CORREO','DIRECCION','TELEFONO','CONTACTO','COD_TIPO_CLIENTE','CIUDAD','CATEGORIA',
    'FRECUENCIA_SERVICIO','COD_EJECUTIVO','C_NOMB_P','C_NOMB_S','C_DIRECCION','ACTIVO'];
    */
    public $timestamps = false;
}
