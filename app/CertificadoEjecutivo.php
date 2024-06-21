<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificadoEjecutivo extends Model
{
    protected $table = 'certificado';
    protected $primaryKey = 'COD_CERTIFICADO';
    protected $fillable = ['COD_OT','COD_PARAMETRO','COD_DETALLE','FECHA_TRAT','FECHA_VENC','NRO','NOMB_PRINC','NOMB_SEC','SERVICIO','CREADO','MODIFICADO','ACTIVO'];
    public $timestamps = false;

    public function personal(){
        return $this->belongsTo('App\Personal');
    }

    public function cliente(){
        return $this->belongsTo('App\Cliente');
    }
}
