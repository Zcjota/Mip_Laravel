<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cot_Cotizacion extends Model
{
    protected $table = 'cot_cotizacion';
    protected $primaryKey = 'id';
    protected $fillable = ['fecha_cot','id_personal','empresa','contacto','id_cliente','id_encabezado','creacion',
                           'estado_cot','total_cot','telf_contacto','referencia'];

    public $timestamps = false;

    public function servicio(){
        return $this->belongsTo('App\Cot_Cat_Servicio');
    }

    public function aplicaciones(){
        return $this->belongsTo('App\Cot_Aplicaciones');
    }
    public function encabezado(){
        return $this->belongsTo('App\Cot_Encabezado');
    }
    public function cliente(){
        return $this->belongsTo('App\Cliente');
    }
}
