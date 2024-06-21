<?php

namespace App\Exports;

use App\ReportStatusResult;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class ResultStatusExport implements FromView
{
    use Exportable;
    private $fechaIni;
    private $fechaFin;
    public function __construct($fechaIni, $fechaFin)
    {
        $this->fechaIni = $fechaIni;
        $this->fechaFin = $fechaFin;
    }
    public function view(): View
    {
        return view(
            'exports.ReporteEstadoResultados',
            [
                'data'=>DB::table("vi_reporteEstadoResultado as vrer")
                ->whereRaw("vrer.fecha between '$this->fechaIni' and '$this->fechaFin'")
                ->orderBy('vrer.fecha', 'DESC')
                ->get()
            ]
        );
    }
}
