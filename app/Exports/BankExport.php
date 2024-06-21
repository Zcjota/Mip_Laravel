<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class BankExport implements FromView
{
    use Exportable;
    private $fechaIni;
    private $fechaFin;
    private $banco;
    public function __construct($fechaIni, $fechaFin, $banco)
    {
        $this->fechaIni = $fechaIni;
        $this->fechaFin = $fechaFin;
        $this->banco = $banco;
    }
    public function view(): View
    {
        if($this->banco == "ALL"){
            return view(
                'exports.ReporteBancario',
                [
                    'data'=>DB::table("vi_reporteBancario as vrb")
                    ->whereRaw("vrb.fecha between '$this->fechaIni' and '$this->fechaFin'")
                    ->orderBy('vrb.fecha', 'DESC')
                    ->get()
                ]
            );
        } else {
            return view(
                'exports.ReporteBancario',
                [
                    'data'=>DB::table("vi_reporteBancario as vrb")
                    ->whereRaw("vrb.fecha between '$this->fechaIni' and '$this->fechaFin' and (vrb.banco_origen = '$this->banco' or vrb.banco_destino = '$this->banco')")
                    ->orderBy('vrb.fecha', 'DESC')
                    ->get()
                ]
            );
        }
    }
}
