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

class IngresoExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
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
        $query = DB::table('vi_conciliacion_ingresos')
            ->where('vi_conciliacion_ingresos.fecha', '>=', $this->fechaInicio)
            ->where('vi_conciliacion_ingresos.fecha', '<=', $this->fechaFin);


        if ($this->tipoMovimiento == 1) {
            $query->where('vi_conciliacion_ingresos.tipo', 'OTROS INGRESOS')
                ->whereIn('vi_conciliacion_ingresos.BANDERA', [0, 2]);
        } elseif ($this->tipoMovimiento == 2) {
            $query->where('vi_conciliacion_ingresos.tipo', 'DEVOLUCIONES PERSONAL')
                ->whereIn('vi_conciliacion_ingresos.BANDERA', [0, 2]);
        } elseif ($this->tipoMovimiento == 3) {
            $query->where('vi_conciliacion_ingresos.tipo', 'COBRANZA')
                ->whereIn('vi_conciliacion_ingresos.BANDERA', [0, 2]);
        } elseif ($this->tipoMovimiento == '4') {
            $query->where('vi_conciliacion_ingresos.tipo', 'TRASPASO')
                ->whereIn('vi_conciliacion_ingresos.BANDERA', [0, 2]);
        }

        if ($this->buscar) {
            $query->where(function ($q) {
                $q->where('vi_conciliacion_ingresos.nro', 'like', '%' . $this->buscar . '%')
                    ->orWhere('vi_conciliacion_ingresos.responsable', 'like', '%' . $this->buscar . '%');
            });
        }
        if ($this->estado) {
            $query->where('vi_conciliacion_ingresos.bandera', $this->estado);
        }
        $query->orderBy('vi_conciliacion_ingresos.fecha', 'asc');

        return $query->get();
        //  return $resultadosCombinados->get();
    }

    public function headings(): array
    {
        return [
            'FECHA',
            'DOCUMENTO',
            'NRO',
            'FACTURA',
            'Numero OT',
            'BENEFICIARIO/PROVEEDOR',
            'CUENTA PRESUPUESTO',
            'CUENTA',
            'DETALLE',
            'IMPORTE BS',
            'USD',
            'BANCO ORIGEN',
            'BANCO DESTINO',
            'OBSERVACIONES',
            'MONTO BANCO',
            'DIFERENCIA',
            'CONCILIACION',
            'SEMANA DEL MES'

        ];
    }
    private function getSemanaDelMes($fecha)
    {
        $date = new \DateTime($fecha);
        $firstDayOfMonth = new \DateTime($date->format('Y-m-01'));
        $weekOfMonth = ceil(($date->format('j') + $firstDayOfMonth->format('w')) / 7);
        
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $mesLiteral = $meses[(int)$date->format('n')];
        
        return $mesLiteral . '-Semana-' . $weekOfMonth;
    }
    public function map($ingreso): array
    {
        // Acumula los totales de BS y USD para cada recibo
        $this->totalBs += $ingreso->totalbs;
        $this->totalUsd += $ingreso->usd;
        // $numero_solicitud = 'C-' . str_pad($ingreso->nro, 2, '0', STR_PAD_LEFT);
        $prefijo = $ingreso->tipo === 'OTROS INGRESOS' ? 'I-' : ($ingreso->tipo === 'TRASPASO' ? 'T-' : ($ingreso->tipo === 'COBRANZA' ? 'C-' : ''));
        $correlativo = $prefijo . str_pad($ingreso->nro, 2, '0', STR_PAD_LEFT);

        // Aquí convertimos la columna 'Bandera' en un texto descriptivo.
        $bandera = '';
        $tipor = '';
        switch ($ingreso->tipo_resp) {
            case '4':
                $tipor = 'PERSONAL/TRASPASO';
                break;
            case 1:
                $tipor = 'PERSONAL';
                break;
            case 2:
                $tipor = 'PROVEEDOR';
                break;
            default:
                $tipor = 'DESCONOCIDO';
        }
        // Calcula la semana del mes para la fecha
        $semanaDelMes = $this->getSemanaDelMes($ingreso->fecha);
        // Devuelve los datos de cada fila en el formato que deseas exportar.
        return [
            $ingreso->fecha,
            $ingreso->tipo,
            $ingreso->nro,
            $ingreso->fact,
            $ingreso->ot,
            $ingreso->responsable,
            $ingreso->cuenta, //cuenta presuspuesto 
            $ingreso->nrocuenta,
            $ingreso->detalle,
            $ingreso->totalbs,
            $ingreso->usd,
            $ingreso->banco_origen, //BANCO Origen
            $ingreso->banco_destino, //banco destino
            $ingreso->nota, // 
            $ingreso->monto_banco,
            $ingreso->diferencia,
            $bandera,
           $semanaDelMes // Nueva columna para la semana del mes
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();
                $cellTotalBs = "J" . ($lastRow + 1);
                $cellTotalUsd = "K" . ($lastRow + 1);
    
                // Establecer los valores
                $sheet->setCellValue($cellTotalBs, "Total BS: " . $this->totalBs);
                $sheet->setCellValue($cellTotalUsd, "Total USD: " . $this->totalUsd);
    
                // Aplicar estilos a las celdas de totales
                $sheet->getStyle($cellTotalBs)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF4CAF50'], // Verde estándar
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
    
                $sheet->getStyle($cellTotalUsd)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF4CAF50'], // Verde estándar
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
            
        ];



        
    }


    public function styles(Worksheet $sheet)
    {
        // Inserta nuevas filas al principio para los encabezados del reporte
        $sheet->getProtection()->setSheet(false);
        $sheet->insertNewRowBefore(1, 7);
        $sheet->mergeCells('C1:E2'); // Fusionar celdas para el título
        $sheet->setCellValue('C1', 'CONCILIACIÓN BANCARIA INGRESOS POR SERVICIOS');
        $sheet->mergeCells('C3:E3');
        $sheet->setCellValue('C3', 'FECHA: ' . $this->fechaInicio . ' AL ' . $this->fechaFin);
        $sheet->mergeCells('C4:E4');
        $sheet->setCellValue('C4', 'ÁREA: CONTABILIDAD');
        $sheet->mergeCells('C5:E5');
        $sheet->setCellValue('C5', 'RESPONSABLE: CAMILA RIBERA ROCA');
        $sheet->mergeCells('C6:E6');
        $sheet->setCellValue('C6', 'BANCO: ECONÓMICO');
        $sheet->getStyle('C2:C6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
        ]);

        // Inserción de la imagen
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('img/mip.png');
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);

        // Establecer estilos de los encabezados de la tabla
        $sheet->getStyle('A8:R8')->applyFromArray([
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

        // Establecer estilos para las celdas de datos y bordes
        $dataRange = 'A9:R' . (count($this->collection()) + 8);
        $sheet->getStyle($dataRange)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DDDDDD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'BFBFBF'] // Un gris claro
                ]
            ],
        ]);


        // Estilos adicionales basados en condiciones
        $rowIndex = 9;
        foreach ($this->collection() as $ingreso) {
            $cellValue = $ingreso->bandera;
            $banderaCell = "Q$rowIndex";
            $cellDiferencia = $ingreso->diferencia;
            $colorDiferencia = "P$rowIndex";

            if ($cellDiferencia > 0) {
                $sheet->getStyle($colorDiferencia)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => Color::COLOR_GREEN],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            } elseif ($cellDiferencia < 0) {
                $sheet->getStyle($colorDiferencia)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => Color::COLOR_RED],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }

            if ($cellValue == 2) {
                $sheet->getStyle($banderaCell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
                $sheet->getCell($banderaCell)->setValue('Observación');
            } elseif ($cellValue == 1) {
                $sheet->getStyle($banderaCell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_GREEN);
                $sheet->getCell($banderaCell)->setValue('Conciliado');
            } else {
                $sheet->getCell($banderaCell)->setValue('Pendiente');
            }

            $rowIndex++;
        }
        // Ajuste de ancho y envoltura de texto
        foreach (range('A', 'C') as $column) {

            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);

            $sheet->getStyle($column)->applyFromArray([

                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }
        $sheet->getColumnDimension('D')->setWidth(25); // Define un ancho específico
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->applyFromArray([

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        $sheet->getColumnDimension('E')->setWidth(25); // Define un ancho específico
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->applyFromArray([

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        foreach (range('F', 'H') as $column) {

            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $sheet->getStyle($column)->applyFromArray([

                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }


        $sheet->getColumnDimension('I')->setWidth(50); // Define un ancho específico
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->applyFromArray([

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        foreach (range('J', 'M') as $column) {

            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);

            $sheet->getStyle($column)->applyFromArray([

                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }

        $sheet->getColumnDimension('N')->setWidth(30); // Define un ancho específico
        $sheet->getStyle('N')->getAlignment()->setWrapText(true);
        $sheet->getStyle('N')->applyFromArray([

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        foreach (range('O', 'R') as $column) {

            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);

            $sheet->getStyle($column)->applyFromArray([

                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }
    }
}
