<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipo_usuario';
    protected $primaryKey = 'COD_TIPOU';

    protected $fillable = ['NOMB_TIPOU','ACTIVO'];
    public $timestamps = false;
}
