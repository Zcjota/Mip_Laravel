<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class F_SolicitudEfectivo extends Model
{
    protected $table = 'fin_solicitud_efectivo';
    protected $primaryKey = 'COD_SE';
    protected $fillable = ['COD_TSE','COD_CUENTA','COD_AREA','NRO','FECHA','FECHA_APROBACION','SOLICITADO_POR',
                           'APROBADO_POR','TIPO_EFECTIVO','ESTADO','ESTADO_RENDICION','TOTAL_USD',
                           'TOTAL_BS','RECIBIDO_POR','OBSERVACION','ACTIVO'];
   
    public $timestamps = false;
}
