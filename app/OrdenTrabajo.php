<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table = 'orden_trabajo';
    protected $primaryKey = 'COD_ORDEN_TRABAJO';
    protected $fillable = ['COD_ORDEN_TRABAJO','COD_CLIENTE','FECHA','FECHA_SERVICIO','FECHA_EMISION_FC','HORA_SERVICIO','DATOS_FACTURA','RAZON_SOCIAL','NIT','PRECIO','BS',
    'OBSERVACIONES','NRO_TECNICOS','TIEMPO_SERVICIO','COD_EJECUTIVO_VENTAS','COD_EQUIPO','RESPONSABLES','APROBADA','COBRADA','SUSPENDIDA','REPROGRAMADA',
    'PAGO','ANULADA','MEMO_ANU','FECHA_ANU','NRO_ORDEN','NRO_ORDEN_PADRE','ACTIVO','TIPO_PAGO','PLAZO','INICIAL','CLIENTE_INICIAL','IMPRESION_RECIBO','COD_CATEGORIA',
    'COD_ESPECIFICACION','FECHA_ALTA','COD_USUARIO','FRECUENCIA','ESTADO_FRECUENCIA','ESTADO_PM','ENVIO_EMAIL_CREDITO','TECNICO','FECHA_COBRADA','FACTURA','CERTIFICADO'];

    public $timestamps = false;
    
    public function cliente(){
        return $this->belongsTo('App\Cliente');
    }

    public function Reclamos(){
        return $this->belongsTo('App\Reclamos');
    }

    public function Personal(){
        return $this->belongsTo('App\Personal');
    }



}
