<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleOEF extends Model
{
    protected $table = 'DetalleOEF';
    protected $primaryKey = 'codigo';

    protected $fillable = ['fecha','horaInicio','tiempoServicio','tiempoTransporte','activo','codCliente','codTecnico1','codTecnico2','codTecnico3','codTecnico4','codTecnico5','codMaestroOEF'];

    public $timestamps = false;
}
