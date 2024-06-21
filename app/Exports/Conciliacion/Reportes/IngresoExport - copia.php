<?php

namespace App\Exports\Conciliacion\Reportes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth si no está ya importado
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class IngresoExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
        public function drawings()
        {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('img/mip.png'); // Asegúrate de tener el logo en tu proyecto
            $drawing->setHeight(90);
            $drawing->setCoordinates('A1');
    
            return $drawing;
        }
    
        protected $buscar;
        protected $tipoMovimiento;
        protected $estado;
        protected $fechaFin;
        protected $fechaInicio;
        protected $totalBs = 0;
        protected $totalUsd = 0;
    
       public function __construct($buscar = null, $tipoMovimiento = null, $estado = null, $fechaFin = null, $fechaInicio = null)
        {
            $this->buscar = $buscar;
            $this->tipoMovimiento = $tipoMovimiento;
            $this->estado = $estado;
            $this->fechaFin = $fechaFin;
            $this->fechaInicio = $fechaInicio;
        }
    
        public function collection() 
        {
            $gastos = DB::table('z_solicitud_ingreso as sg')
                ->join('z_detalle_solicitud_ingreso as dsg', 'dsg.cod_si', '=', 'sg.cod_si')
                ->join('z_prov_pers as c', 'c.id', '=', 'dsg.cod_proveedor')
                ->join('z_banco_cuenta as d', 'd.cod_bc', '=', 'sg.cod_bc')
                ->join('z_banco as e', 'e.cod_b', '=', 'd.cod_b')
                ->join('cuenta as f', 'f.COD_CUENTA', '=', 'dsg.cod_cc')
                ->join('nro_cuenta as j', 'j.CODNUM', '=', 'f.NRO_CUENTA')
                ->join('z_banco_cuenta as g', 'g.cod_bc', '=', 'dsg.cod_bc')
                ->join('z_banco as h', 'h.cod_b', '=', 'g.cod_b')
                ->leftjoin('conciliacion_bancaria as i', 'i.id_detalle_solicitud', '=', 'dsg.cod_dsi')
                ->selectRaw("
                    sg.fecha_si AS fecha,
                    sg.cod_tsi AS tipo,
                    sg.nro_si AS nro,
                    c.tipo AS tipo_resp,
                    c.nombre_completo AS responsable,
                    f.DESCRIPCION AS cuenta,
                    dsg.detalle AS detalle,
                    dsg.importe_bs AS totalbs,
                    dsg.importe_usd AS usd,
                    e.nombre AS banco,
                    d.nro_cuenta AS cuenta_numero,
                    h.nombre AS banco2,
                    g.nro_cuenta AS cuenta_numero2,
                    dsg.BANDERA AS bandera,
                    i.nota AS nota,
                    j.DESCRIPCION AS nrocuenta
                ")
                ->where('sg.ACTIVO', '=', 1)
                ->where('sg.estado', 'INI')->orWhere('sg.estado', 'APR')
                ->whereBetween('sg.fecha_si', [$this->fechaInicio, $this->fechaFin])
                ->where(function ($query) {
                    if (!is_null($this->buscar)) {
                        $query->where('sg.nro_si', 'like', '%' . $this->buscar . '%')
                              ->orWhere('c.nombre_completo', 'like', '%' . $this->buscar . '%');
                    }
                });

                   // Consulta de traspasos
        $traspaso = DB::table('z_movimiento_banco_cuenta as mbc')
        ->join('z_banco_cuenta as bco', 'mbc.cod_bc_origen', '=', 'bco.cod_bc')
        ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
        ->join('z_banco_cuenta as bcd', 'mbc.cod_bc_destino', '=', 'bcd.cod_bc')
        ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
        ->join('usuario as usu','mbc.modificado_por','=','usu.COD_USUARIO')
        ->leftjoin('conciliacion_bancaria as i', 'i.id_detalle_solicitud', '=', 'mbc.cod_mbc')
        ->select([
            'mbc.fecha AS fecha',
            DB::raw("'TRASPASO' AS tipo"),
            'mbc.cod_mbc AS nro',
            'mbc.tipo AS tipo_resp',
            DB::raw("CONCAT(usu.NOMBRE,' ', usu.AP_PATERNO,' ', usu.AP_MATERNO) AS responsable"),
            DB::raw("'MANEJO INTEGRADO DE PLAGAS MIP SRL' AS cuenta"),
            'mbc.glosa AS detalle',
            'mbc.importe_bs AS totalbs',
            'mbc.importe_usd AS usd',
            'bo.nombre AS banco',
            'bco.nro_cuenta AS cuenta_numero',
            'bd.nombre AS banco2',
            'bcd.nro_cuenta AS cuenta_numero2',
            'mbc.BANDERAE AS bandera',
            'i.nota AS nota',
            DB::raw("'MANEJO INTEGRADO DE PLAGAS' AS nrocuenta")

       ])
       ->where('mbc.ACTIVO', '=', '1')
       ->whereBetween('mbc.fecha', [$this->fechaInicio, $this->fechaFin]);
 
 
        // $resultadosCombinados = $gastos->union($traspaso);
        $resultadosCombinados = $gastos->union($traspaso)->orderBy('nro', 'asc');
 
        $query = DB::query()->fromSub($resultadosCombinados, 'resultadoCombinado')
     
        ->where('resultadoCombinado.fecha', '>=', $this->fechaInicio)
        ->where('resultadoCombinado.fecha', '<=', $this->fechaFin);
 
        if ($this->buscar) {
         $query->where(function($q) {
             $q->where('resultadoCombinado.nro', 'like', '%' . $this->buscar . '%')
               ->orWhere('resultadoCombinado.responsable', 'like', '%' . $this->buscar . '%');
         });
     }
     
 
 
    //  if ($this->tipoMovimiento) {
    //      $query->where('resultadoCombinado.tipo', '=', $this->tipoMovimiento);
    //  }

    if ($this->tipoMovimiento) {
        if($this->tipoMovimiento == 4)
        {
            $tipoMovimiento2 = "TRASPASO";
            $query->where('resultadoCombinado.tipo', '=', $tipoMovimiento2);
        }else {
            $query->where('resultadoCombinado.tipo', '=', $this->tipoMovimiento);
        }
        //  $query->where('resultadoCombinado.tipo', '=', $this->tipoMovimiento);
     }
     
 
 
     if ($this->estado) {
         $query->where('resultadoCombinado.bandera', $this->estado);
     }
     
 
     return $query->get();
        //  return $resultadosCombinados->get();
       }
    
        public function headings(): array
        {
            return [
                'Fecha de Solicitud',
                'Tipo de Ingreso',
                'Nro.Solicitud',
                'Tipo Responsable',
                'Responsable',
                'Cuenta Presupuesto',
                'Nro. Cuenta P.',
                'Detalle',
                'Importe BS',
                'USD',
                'BANCO ORIGEN',
                'NRO.CUENTA ORIGEN',
                'BANCO DESTINO',
                'NRO.CUENTA DESTINO',
                'OBSERVACIONES',
                'CONCILIACION' 
            ];
        }
    
        public function map($gasto): array
        {
             // Acumula los totales de BS y USD para cada recibo
        $this->totalBs += $gasto->totalbs;
        $this->totalUsd += $gasto->usd;
            // $numero_solicitud = 'C-' . str_pad($gasto->nro, 2, '0', STR_PAD_LEFT);
            $prefijo = $gasto->tipo === 'TRASPASO' ? 'T-' : 'C-';
            $numero_solicitud = $prefijo . str_pad($gasto->nro, 2, '0', STR_PAD_LEFT);
    
            // Aquí convertimos la columna 'Bandera' en un texto descriptivo.
            $bandera = '';
            // switch ($gasto->bandera) {
            //     case 0: $bandera = 'Pendiente'; break;
            //     case 1: $bandera = 'Conciliado'; break;
            //     case 2: $bandera = 'Observación'; break;
            //     default: $bandera = 'Desconocido';
            // }

            $tipor = '';
            switch ($gasto->tipo_resp) {
                case '4' : $tipor = 'PERSONAL/TRASPASO'; break;
                case 1: $tipor = 'PERSONAL'; break;
                case 2: $tipor = 'PROVEEDOR'; break;
                default: $tipor = 'DESCONOCIDO';
            }
    
            $tipo2 = '';
            switch ($gasto->tipo) {
                case 'TRASPASO' : $tipo2 = 'TRASPASO'; break;
                case 1: $tipo2 = 'OTROS INGRESOS'; break;
                case 2: $tipo2 = 'DEVOLUCIONES PERSONAL'; break;
                default: $tipo2 = 'DESCONOCIDO';
            }
    
    
            // Devuelve los datos de cada fila en el formato que deseas exportar.
            return [
                $gasto->fecha,
                $tipo2,
                $numero_solicitud,
                // $gasto->nro,
                $tipor,
                $gasto->responsable,
                $gasto->cuenta,
                $gasto->nrocuenta,
                $gasto->detalle,
                $gasto->totalbs,
                $gasto->usd,
                $gasto->banco,
                $gasto->cuenta_numero,
                $gasto->banco2,
                $gasto->cuenta_numero2,
                $gasto->nota,
                $bandera
            ];
        }

        public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();
                $sheet->setCellValue("i" . ($lastRow + 1), "Total BS: " . $this->totalBs);
                $sheet->setCellValue("j" . ($lastRow + 1), "Total USD: " . $this->totalUsd);
                
                // Aquí puedes agregar cualquier estilo adicional para las celdas de totales si lo deseas
            },
        ];
    }
    
        public function styles(Worksheet $sheet)
    {
        // Inserta nuevas filas al principio para los encabezados del reporte
        $sheet->insertNewRowBefore(1, 7);
    
        // Configuración de las celdas de título
        // $sheet->setCellValue('B2', 'FECHA:');
        // $sheet->setCellValue('C2', $this->fechaInicio . ' AL ' . $this->fechaFin);
        // $sheet->setCellValue('B3', 'ÁREA:');
        // $sheet->setCellValue('C3', 'CONTABILIDAD');
        // $sheet->setCellValue('B4', 'RESPONSABLE:');
        // $sheet->setCellValue('C4', Auth::user()->name ?? 'Nombre no disponible');
        // $sheet->setCellValue('B5', 'BANCO:');
        // $sheet->setCellValue('C5', 'ECONÓMICO');
        
        $sheet->mergeCells('C1:E2'); // Fusionar celdas para el título
        $sheet->setCellValue('C1', 'CONCILIACIÓN BANCARIA INGRESOS POR SERVICIOS');
        $sheet->mergeCells('C3:E3');
        $sheet->setCellValue('C3', 'FECHA: ' . $this->fechaInicio . ' AL ' . $this->fechaFin);
        $sheet->mergeCells('C4:E4');
        // $sheet->setCellValue('C2', $this->fechaInicio . ' AL ' . $this->fechaFin);
        $sheet->setCellValue('C4', 'ÁREA: CONTABILIDAD');
        // $sheet->setCellValue('C3', 'CONTABILIDAD');
        $sheet->mergeCells('C5:E5');
        $sheet->setCellValue('C5', 'RESPONSABLE: CAMILA RIBERA ROCA');
        // $sheet->setCellValue('C4', Auth::user()->name ?? 'Nombre no disponible');
        $sheet->mergeCells('C6:E6');
        $sheet->setCellValue('C6', 'BANCO: ECONÓMICO');
        // $sheet->setCellValue('C5', 'ECONÓMICO');
        // Estilos para las celdas de título
        $sheet->getStyle('C2:C6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
        ]);
        
         
        //  // Establecer el tamaño de las celdas para los datos
        //  $sheet->getRowDimension('A8')->setRowHeight(10); // Establecer la altura de la fila para los datos
        //  $sheet->getRowDimension('B8')->setRowHeight(10); 
        //  $sheet->getRowDimension('C8')->setRowHeight(10); 
        //  $sheet->getRowDimension('B8')->setRowHeight(10); // Establecer la altura de la fila para los datos
        //  // Continúa con las filas necesarias para ajustar el tamaño según tus datos
     
        //  // Orientación del texto en las celdas de datos (horizontal)
        //  $sheet->getStyle('A8:Q100')->getAlignment()->setTextRotation(0);
    
        // Insertar la imagen en la celda 'A1'
        // $sheet->mergeCells('A1:B7');
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('img/mip.png');
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    
        // Establecer estilos de los encabezados de la tabla
        $sheet->getStyle('A8:Q8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '526D84'],
            ],
        ]);
    
        // $columnas = ['A', 'B', 'C', 'D'];
        // foreach ($columnas as $columna) {
        //     $sheet->getStyle($columna.'A8')->getAlignment()->setWrapText(true);
        // }
        
    
       
        // Estilos condicionales para las celdas de estado
        $rowIndex = 9;
        foreach ($this->collection() as $gasto) {
            $cellValue = $gasto->bandera;
            $banderaCell = "P$rowIndex";
            if ($cellValue == 2) {
                $sheet->getStyle($banderaCell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
                $sheet->getCell($banderaCell)->setValue('Observación');
            } elseif ($cellValue == 1) {
                $sheet->getStyle($banderaCell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_GREEN);
                $sheet->getCell($banderaCell)->setValue('Conciliado');
            } else {
                $sheet->getCell($banderaCell)->setValue('Pendiente');
                // Puedes cambiar el valor y el estilo según necesites para los otros estados
            }
            $rowIndex++;


            
        }
    }
    
}
