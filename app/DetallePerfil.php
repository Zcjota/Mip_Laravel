<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallePerfil extends Model
{
    protected $table = 'detalle_perfil';
    protected $primaryKey = 'COD_SUB_MENU';
    protected $fillable = ['COD_TIPOU','ROL','ACTIVO'];

    public $timestamps = false;
}
