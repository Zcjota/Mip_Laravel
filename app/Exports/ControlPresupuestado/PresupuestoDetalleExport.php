<?php

namespace App\Exports\ControlPresupuestado;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PresupuestoDetalleExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $codigo;
    protected $tipoMovimiento;
    protected $mes;

    protected $totalGasto = 0;

    public function __construct($codigo = null, $tipoMovimiento = null, $mes = null)
    {
        $this->codigo = $codigo;
        $this->tipoMovimiento = $tipoMovimiento;
        $this->mes = $mes;
    }

    public function collection()
    {
        // Obtener la apertura
        $apertura = DB::table('apertura')
            ->where('MES', $this->mes)
            ->where('GESTION', $this->tipoMovimiento)
            ->where('ACTIVO', 1)
            ->first();

        if (!$apertura) {
            return collect(); // Retorna una colección vacía si no hay apertura
        }

        $codApertura = $apertura->COD_APERTURA;

        // Prepara la consulta base
        $query = DB::table('detalle_gasto as dg')
            ->select('dg.COD_DETALLE AS codigod', 'dg.DESCRIPCION as descripcion', 'dg.MONTO as montoGasto', 'dg.CATEGORIA as categoria', 'dg.TIPO_PAGO as tipoPago', 'dg.FECHA as fechaGasto')
            ->where('dg.COD_APERTURA', $codApertura)
            ->where('dg.COD_CUENTA', $this->codigo)  // Usar el código de cuenta proporcionado
            ->where('dg.ACTIVO', 1)
            ->orderBy('dg.FECHA', 'asc');

        // Ejecuta la consulta y transforma los resultados
        $resultados = $query->get();
        $resultados->transform(function ($item) {
            // Acumular el total del gasto
            $this->totalGasto += $item->montoGasto;
            return $item;
        });

        return $resultados;
    }

    public function headings(): array
    {
        return [
            'DESCRIPCION',
            'MONTO GASTADO',
            'TIPO DE GASTO',
            'TIPO DE PAGO',
            'FECHA DE GASTO'
        ];
    }

    public function map($gasto): array
    {
        return [
            $gasto->descripcion,
            number_format($gasto->montoGasto, 2, ',', '.'),
            $this->getTipoGastoDescripcion($gasto->categoria),
            $this->getTipoPagoDescripcion($gasto->tipoPago),
            $gasto->fechaGasto,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF808080'],
            ],
        ]);

        $sheet->getStyle('A:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow() + 1;

                // Agregar la fila de totales al final
                $sheet->setCellValue('A' . $lastRow, 'TOTAL:');
                $sheet->setCellValue('B' . $lastRow, $this->totalGasto);

                // Estilo para la fila de totales
                $sheet->getStyle('A' . $lastRow . ':B' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
            },
        ];
    }

    protected function getTipoGastoDescripcion($tipo)
    {
        switch ($tipo) {
            case 'F':
                return 'FIJO';
            case 'O':
                return 'OTRO';
            case 'E':
                return 'EVENTUAL';
            default:
                return $tipo;
        }
    }

    protected function getTipoPagoDescripcion($tipo)
    {
        switch ($tipo) {
            case 1:
                return 'TRANSFERENCIA';
            case 2:
                return 'EFECTIVO';
            case 3:
                return 'CHEQUE';
            default:
                return $tipo;
        }
    }
}
