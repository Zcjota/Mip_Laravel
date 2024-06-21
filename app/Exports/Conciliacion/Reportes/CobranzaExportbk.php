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


// class CobranzaExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
class CobranzaExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping, WithEvents
{
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('img/mip.png')); // Asegúrate de tener el logo en tu proyecto
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    protected $buscar;
    // protected $tipoMovimiento;
    // protected $estado;
    protected $fechaFin;
    protected $fechaInicio;
    protected $totalBs = 0;
    protected $totalUsd = 0;

   public function __construct($buscar = null, $fechaFin = null, $fechaInicio = null)
    {
        $this->buscar = $buscar;
        // $this->tipoMovimiento = $tipoMovimiento;
        // $this->estado = $estado;
        $this->fechaFin = $fechaFin;
        $this->fechaInicio = $fechaInicio;
    }

    public function collection()
    {
        $recibos = DB::table('z_control_recibos_ot as ot')
    ->join('z_detalle_control_recibos_ot as d', 'd.cod_cr', '=', 'ot.cod_cr')
    ->join('z_banco_cuenta as b', 'b.cod_bc', '=', 'ot.cod_bc_destino')
    ->join('z_banco as c', 'c.cod_b', '=', 'b.cod_b')
    ->leftjoin('conciliacion_bancaria as i', 'i.id_detalle_solicitud', '=', 'ot.cod_cr')
    ->select(
        'ot.fecha AS fecha',
        'ot.nro_recibo AS nro',
        DB::raw('GROUP_CONCAT(d.nro_ot) AS nro_ot'), // Aquí se utiliza DB::raw() para GROUP_CONCAT()
        'ot.glosa AS detalle',
        'ot.recibi_de AS responsable',
        'ot.importe_bs AS totalbs',
        'ot.importe_usd AS usd',
        'ot.tipo_cambio AS tipo_cambio',
        'c.nombre AS banco',
        'b.nro_cuenta AS nro_cuenta',
        'i.nota AS nota',
        'ot.BANDERA AS bandera'
    )
    ->where('ot.ACTIVO', 1)
    ->whereIn('ot.bandera', [0, 2])
    ->where(function ($query) {
        if (!is_null($this->buscar)) {
            $query->where('ot.nro_recibo', 'like', '%' . $this->buscar . '%')
                ->orWhere('ot.recibi_de', 'like', '%' . $this->buscar . '%');
        }
    })
    ->whereBetween('ot.fecha', [$this->fechaInicio, $this->fechaFin])
    ->groupBy(
        'ot.fecha',
        'ot.nro_recibo',
        'ot.glosa',
        'ot.recibi_de',
        'ot.importe_bs',
        'ot.importe_usd',
        'ot.tipo_cambio',
        'c.nombre',
        'b.nro_cuenta',
        'i.nota',
        'ot.BANDERA'
    )
    ->orderBy('ot.fecha', 'asc');

return $recibos->get();

    }

    public function headings(): array
    {
        return [
            'FECHA',
            'NRO.RECIBO',
            'NRO.OT',
            'DESCRIPCIÓN',
            'CLIENTE O RAZON SOCIAL',
            'MONTO BS',
            'MONTO SUS',
            'TIPO CAMBIO',
            'BANCO',
            'NRO.CUENTA',
            'OBSERVACIONES',
            'CONCILIACION' 
        ];
    }

    public function map($recibos): array
    {
        // Acumula los totales de BS y USD para cada recibo
        $this->totalBs += $recibos->totalbs;
        $this->totalUsd += $recibos->usd;

        $numero_solicitud = 'C-' . str_pad($recibos->nro, 2, '0', STR_PAD_LEFT);

        // Aquí convertimos la columna 'Bandera' en un texto descriptivo.
        $bandera = '';
        // switch ($recibos->bandera) {
        //     case 0: $bandera = 'Pendiente'; break;
        //     case 1: $bandera = 'Conciliado'; break;
        //     case 2: $bandera = 'Observación'; break;
        //     default: $bandera = 'Desconocido';
        // }

        // $tipor = '';
        // switch ($recibos->tipo_resp) {
        //     case 0: $tipor = 'OTROS'; break;
        //     case 1: $tipor = 'PERSONAL'; break;
        //     case 2: $tipor = 'PROVEEDOR'; break;
        //     default: $tipor = 'DESCONOCIDO';
        // }


        // Devuelve los datos de cada fila en el formato que deseas exportar.
        return [
            $recibos->fecha,
            $numero_solicitud,
            $recibos->nro_ot,
            $recibos->detalle,
            $recibos->responsable,
            $recibos->totalbs,
            $recibos->usd,
            $recibos->tipo_cambio,
            $recibos->banco,
            $recibos->nro_cuenta,
            $recibos->nota,
            $bandera,
            $recibos->totalbs,
            $recibos->usd,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();
                $sheet->setCellValue("F" . ($lastRow + 1), "Total BS: " . $this->totalBs);
                $sheet->setCellValue("G" . ($lastRow + 1), "Total USD: " . $this->totalUsd);
                
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
    $sheet->setCellValue('C1', 'CONCILIACIÓN BANCARIA INGRESOS POR SERVICIOS ');
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
    $drawing->setPath(public_path('img/mip.png'));
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
    foreach ($this->collection() as $recibos) {
        $cellValue = $recibos->bandera;
        $banderaCell = "L$rowIndex";
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
