<?php

namespace App\Exports\ControlPresupuestado;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PresupuestoGExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $buscar;
    protected $tipoMovimiento;
    protected $estado;
    protected $totalBs = 0;
    protected $totalUsd = 0;

    public function __construct($buscar = null, $tipoMovimiento = null, $estado = null)
    {
        $this->buscar = $buscar;
        $this->tipoMovimiento = $tipoMovimiento;
        $this->estado = $estado;
    }

    public function collection()
    {
        // Obtener la apertura
        $apertura = DB::table('apertura')
            ->where('MES', $this->estado)
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
                'pc.MONTO_PRESUPUESTO as montoPresupuesto')
            ->where('pc.COD_APERTURA', $codApertura)
            ->where('pc.ACTIVO', 1)
            ->groupBy('C.COD_CUENTA', 'C.DESCRIPCION', 'n.DESCRIPCION', 'F.DESCRIPCION', 'pc.MONTO_PRESUPUESTO', 'pc.DESCRIPCION')
            ->orderBy('n.DESCRIPCION', 'asc');

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

            if ($item->montoPresupuesto >=  $item->montoGastoF ) {
                $item->saldoActual = $item->montoPresupuesto - $item->montoGastoF;
                $item->saldoActual = number_format($item->saldoActual, 2, '.','');
            }
            if ($item->montoPresupuesto <  $item->montoGastoF ) {
                $item->EXCEDIDO = $item->montoPresupuesto - $item->montoGastoF;
                $item->TOTALM = $item->montoPresupuesto - $item->montoGastoF;
            }
            $item->montoGastoF = number_format($item->montoGastoF, 2, '.','');
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
            'GASTO TOTAL',
            'PRESUPUESTO',
            'SALDO'
        ];
    }

    public function map($gasto): array
    {
        return [
            $gasto->fondo,
            $gasto->nrocuenta,
            $gasto->descripcion,
            $gasto->montoGastoF,
            $gasto->montoPresupuesto,
            $gasto->saldoActual,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aquí puedes añadir estilos a tu hoja de cálculo si es necesario
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aquí puedes añadir eventos después de generar la hoja
            },
        ];
    }
}
