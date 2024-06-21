<?php

namespace App\EXPORTS;

use App\Cot_Cotizacion;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class reporte_cotizacionExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'Fecha',
            'Empresa',
            'Contacto',
            'Telefono',
            'Referencia',
            'Monto Total',
            'Estado',
            'Ejecutivo',
            'Creacion'
        ];
    }
    public function collection()
    {
        $cotizacion= DB::table('cot_cotizacion')
             ->leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
             ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
             
              ->leftJoin('usuario','cot_cotizacion.id_personal','=','usuario.COD_USUARIO') 
             ->select('cot_cotizacion.fecha_cot','cot_cotizacion.empresa',
             'cot_cotizacion.contacto','cot_cotizacion.telf_contacto','cot_cotizacion.referencia','cot_cotizacion.total_cot','cot_cotizacion.estado_cot',
             DB::raw('concat(usuario.NOMBRE," ",usuario.AP_PATERNO," ",usuario.AP_MATERNO) as nombre'),'cot_cotizacion.creacion')
             ->orderBy('cot_cotizacion.id','desc')
             ->get();
             return $cotizacion; 
    }

 
}