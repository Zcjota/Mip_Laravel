<?php

namespace App\Exports;

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

class RegistroVentasExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table("orden_trabajo as O")
            ->leftJoin('cliente AS C', 'O.COD_CLIENTE', '=', 'C.COD_CLIENTE')
            ->leftJoin('personal AS P', 'O.COD_EJECUTIVO_VENTAS', '=', 'P.COD_PERSONAL')
            ->leftJoin('hojaservicio AS H', 'H.COD_ORDEN_TRABAJO', '=', 'O.COD_ORDEN_TRABAJO')
            ->leftJoin('registro_venta AS R', 'R.COD_ORDEN_TRABAJO', '=', 'O.COD_ORDEN_TRABAJO')
            ->selectRaw('O.COD_ORDEN_TRABAJO, O.NRO_ORDEN, CONCAT(COALESCE(C.AP_CLIENTE, ""), " ", COALESCE(C.AM_CLIENTE, ""), " ", COALESCE(C.NOMBRE, "")) AS cliente,
                O.PRECIO, O.FECHA_SERVICIO AS FECHAS,
                CASE O.TIPO_PAGO WHEN 1 THEN "AL CONTADO" ELSE "CREDITO" END AS TIPO_PAGOS, 
                CONCAT(O.PLAZO, " DIAS") AS PLAZOS, DATE_ADD(O.FECHA_SERVICIO, INTERVAL O.PLAZO DAY) AS fecEstimada,
                R.MONTO_DOLAR AS montopagodolar, R.MONTO_BS AS BS, 
                (R.MONTO_BS - (R.MONTO_DOLAR * 6.96)) AS difpago, R.FECHA_RECIBO, R.NRO_RECIBO, 
                R.CONTADO_NRO_DEPOSITO AS contado_nro_deposito, R.NRO_CHEQUE_NRO_DEPOSITO AS nro_cheque, 
                R.TRANSFERENCIA, R.FECHA_FACTURA, R.NRO_FACTURA, R.DEBE, 
                C.CONTACTO, C.DIRECCION, C.TELEFONO, P.NOMBRE, 
                CASE WHEN O.COBRADA = 1 THEN "COBRADA" 
                    WHEN O.SUSPENDIDA = 1 THEN "SUSPENDIDA" 
                    WHEN O.REPROGRAMADA = 1 THEN "REPROGRAMADA" 
                    WHEN O.APROBADA = 1 THEN "APROBADA" 
                    ELSE "SIN APROBAR" END AS estado')
            ->where('O.PRECIO', '>', 0)
            ->where('O.ACTIVO', '=', 1)
            ->where('O.ANULADA', '=', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('registro_venta as R2')
                      ->whereRaw('R2.COD_ORDEN_TRABAJO = O.COD_ORDEN_TRABAJO')
                      ->where('R2.ACTIVO', '=', 1);
            });

        if ($this->request->estado != "ALL") {
            $query->where('O.ESTADO', '=', $this->request->estado);
        }

        if ($this->request->filled('buscar')) {
            $buscar = str_replace('*', '%', trim(strtoupper($this->request->buscar)));
            $query->where(function($q) use ($buscar) {
                $q->where('O.NRO_ORDEN', 'LIKE', $buscar)
                    ->orWhere('C.AP_CLIENTE', 'LIKE', $buscar)
                    ->orWhere('C.AM_CLIENTE', 'LIKE', $buscar)
                    ->orWhere('C.NOMBRE', 'LIKE', $buscar)
                    ->orWhere('C.CONTACTO', 'LIKE', $buscar)
                    ->orWhere('P.AP_PATERNO', 'LIKE', $buscar);
            });
        }

        if ($this->request->filled('fechaInicio') && $this->request->filled('fechaFin')) {
            $query->whereBetween('O.FECHA_SERVICIO', [$this->request->fechaInicio, $this->request->fechaFin]);
        }

        if ($this->request->filled('filtroinicial')) {
            switch ($this->request->filtroinicial) {
                case 1:
                    $query->where('O.APROBADA', '=', 0)
                        ->where('O.COBRADA', '=', 0)
                        ->where('O.SUSPENDIDA', '=', 0)
                        ->where('O.REPROGRAMADA', '=', 0);
                    break;
                case 2:
                    $query->where('O.APROBADA', '=', 1)
                        ->where('O.COBRADA', '=', 0)
                        ->where('O.SUSPENDIDA', '=', 0)
                        ->where('O.REPROGRAMADA', '=', 0);
                    break;
                case 3:
                    $query->where('O.APROBADA', '=', 1)
                        ->where('O.COBRADA', '=', 1)
                        ->where('O.SUSPENDIDA', '=', 0)
                        ->where('O.REPROGRAMADA', '=', 0);
                    break;
                case 4:
                    $query->where('O.APROBADA', '=', 0)
                        ->where('O.COBRADA', '=', 0)
                        ->where('O.SUSPENDIDA', '=', 1)
                        ->where('O.REPROGRAMADA', '=', 0);
                    break;
                case 5:
                    $query->where('O.APROBADA', '=', 0)
                        ->where('O.COBRADA', '=', 0)
                        ->where('O.SUSPENDIDA', '=', 0)
                        ->where('O.REPROGRAMADA', '=', 1);
                    break;
            }
        }

        if ($this->request->filled('tipoDebe')) {
            $query->where('R.DEBE', '=', $this->request->tipoDebe);
        }

        $resultados = $query->orderBy('O.FECHA_SERVICIO', 'asc')
            ->orderBy('C.NOMBRE', 'asc')
            ->get();

        return $resultados;
    }

    public function headings(): array
    {
        return [
            'COD_ORDEN_TRABAJO', 'NRO_ORDEN', 'CLIENTE', 'PRECIO', 'FECHA_SERVICIO', 'TIPO_PAGO', 'PLAZO', 
            'FECHA_ESTIMADA', 'MONTO_USD', 'MONTO_BS', 'DIF_PAGO', 'FECHA_RECIBO', 'NRO_RECIBO', 
            'CONTADO_DEPOSITO', 'CHEQUE_DEPOSITO', 'TRANSFERENCIA', 'FECHA_FACTURA', 'NRO_FACTURA', 'DEBE', 
            'CONTACTO', 'DIRECCION', 'TELEFONO', 'EJECUTIVO_VENTAS', 'ESTADO'
        ];
    }

    public function map($registro): array
    {
        return [
            $registro->COD_ORDEN_TRABAJO, $registro->NRO_ORDEN, $registro->cliente, $registro->PRECIO, $registro->FECHAS, 
            $registro->TIPO_PAGOS, $registro->PLAZOS, $registro->fecEstimada,
            $registro->montopagodolar, $registro->BS, $registro->difpago, $registro->FECHA_RECIBO, $registro->NRO_RECIBO, 
            $registro->contado_nro_deposito, $registro->nro_cheque, $registro->TRANSFERENCIA, $registro->FECHA_FACTURA, 
            $registro->NRO_FACTURA, $registro->DEBE, $registro->CONTACTO, $registro->DIRECCION, $registro->TELEFONO, 
            $registro->NOMBRE, $registro->estado
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF808080'],
            ],
        ]);

        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'Z') as $column) {
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
                // Agrega los totales de las columnas que necesites, por ejemplo:
                // $sheet->setCellValue('O' . $lastRow, $this->totalPrecio);
                // $sheet->setCellValue('P' . $lastRow, $this->totalBS);

                // Estilo para la fila de totales
                $sheet->getStyle('A' . $lastRow . ':Z' . $lastRow)->applyFromArray([
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
}
