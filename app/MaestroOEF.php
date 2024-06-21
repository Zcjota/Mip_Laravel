<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaestroOEF extends Model
{
    protected $table = 'maestroOEF';
    protected $primaryKey = 'codigo';

    protected $fillable = ['fechaCreacion','mes','gestion','cantidadClientes','cantidadTecnicos','activo','codUsuario'];

    public $timestamps = false;

}
