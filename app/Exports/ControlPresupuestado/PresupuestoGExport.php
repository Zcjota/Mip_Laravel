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

class PresupuestoGExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $buscar;
    protected $tipoMovimiento;
    protected $mes;

    protected $totalGasto = 0;
    protected $totalPresupuesto = 0;
    protected $totalSaldo = 0;
    protected $totalExcedido = 0;

    public function __construct($buscar = null, $tipoMovimiento = null, $mes = null)
    {
        $this->buscar = $buscar;
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
        $query = DB::table('cuenta as C')
            ->join('nro_cuenta as n', 'C.NRO_CUENTA', '=', 'n.CODNUM')
            ->join('fin_fondo as F', 'C.COD_FONDO', '=', 'F.COD_FONDO')
            ->leftJoin('presupuesto_cuenta as pc', 'C.COD_CUENTA', '=', 'pc.COD_CUENTA')
            ->select('C.COD_CUENTA as codigo', 'C.DESCRIPCION as descripcion', 'pc.DESCRIPCION as observacion', 'n.DESCRIPCION as nrocuenta', 'F.DESCRIPCION as fondo',
                'pc.MONTO_PRESUPUESTO as montoPresupuesto', 'pc.MONTO_EXCEDIDO as MONTO_EXCEDIDO' )
            ->where('pc.COD_APERTURA', $codApertura)
            ->where('pc.ACTIVO', 1)
            ->where('C.ACTIVO', 1)
            ->groupBy('C.COD_CUENTA', 'C.DESCRIPCION', 'n.DESCRIPCION', 'F.DESCRIPCION', 'pc.MONTO_PRESUPUESTO', 'pc.DESCRIPCION')
            ->orderBy('n.DESCRIPCION', 'asc')
            ->orderBy('C.DESCRIPCION', 'asc');

        // Aplica búsqueda condicional si es necesario
        if (!empty($this->buscar)) {
            $query->where(function($q) {
                $q->where('C.DESCRIPCION', 'LIKE', "%{$this->buscar}%")
                  ->orWhere('F.DESCRIPCION', 'LIKE', "%{$this->buscar}%");
            });
        }

        // Ejecuta la consulta y transforma los resultados
        $resultados = $query->get();
        $resultados->transform(function ($item) use ($codApertura) {
            $codigo = $item->codigo;
            $item->montoGastoActual = $this->gastoactual($codigo, $codApertura);
            $item->montoGastoF = $item->montoGastoActual;
            $item->saldoActual = ($item->montoPresupuesto + $item->MONTO_EXCEDIDO) - $item->montoGastoF;
            $presupuestoTotal = $item->montoPresupuesto + $item->MONTO_EXCEDIDO;
            $item->porcentaje = $presupuestoTotal > 0 ? ($item->montoGastoActual / $presupuestoTotal) * 100 : 0;
            // $item->saldoActual = $item->montoPresupuesto - $item->montoGastoF;
            
            // $item->saldoActual = number_format($item->saldoActual, 2, ',','.');
            // $item->montoGastoF = number_format($item->montoGastoF, 2, ',','.');
            // $item->MONTO_EXCEDIDO = number_format($item->MONTO_EXCEDIDO, 2, ',','.');
            // $item->montoPresupuesto = number_format($item->montoPresupuesto, 2, ',','.');

            // Acumular los totales
            $this->totalGasto += $item->montoGastoF;
            $this->totalPresupuesto += $item->montoPresupuesto;
            $this->totalSaldo += $item->saldoActual;
            $this->totalExcedido += $item->MONTO_EXCEDIDO; 
            return $item;
        });

        return $resultados;
    }

    public function headings(): array
    {
        return [
            'FONDO',
            'NRO.CUENTA',
            'CUENTA',
            'PRESUPUESTO',
            'MONTO EXCEDIDO',
            'GASTO TOTAL',
            'SALDO',
            'OBSERVACIONES',
            'PORCENTAJE %'
        ];
    }

    public function map($gasto): array
    {
        return [
            $gasto->fondo,
            $gasto->nrocuenta,
            $gasto->descripcion,
            $gasto->montoPresupuesto,
            $gasto->MONTO_EXCEDIDO,
            $gasto->montoGastoF,
            $gasto->saldoActual,
            $gasto->observacion,
            $gasto->porcentaje,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF808080'],
            ],
        ]);

        $sheet->getStyle('A:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'I') as $column) {
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
                $sheet->setCellValue('D' . $lastRow, $this->totalPresupuesto);
                $sheet->setCellValue('E' . $lastRow, $this->totalExcedido);
                $sheet->setCellValue('F' . $lastRow, $this->totalGasto);
                $sheet->setCellValue('G' . $lastRow, $this->totalSaldo);

                // Estilo para la fila de totales
                $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)->applyFromArray([
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

    protected function gastoactual($codigo, $codApertura)
    {
        // Lógica para calcular gastoactual
        return DB::table('gasto_presupuesto')
            ->where('COD_CUENTA', $codigo)
            ->where('COD_APERTURA', $codApertura)
            ->sum('MONTO_GASTO');
    }
}
