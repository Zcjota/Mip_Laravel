<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\DB;

use App\Exports\BankExport;
use App\Exports\ExportGenericoExcel;
use App\Exports\ResultStatusExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportsController extends ApiController
{
    public function GetDataForBankReport(Request $request)
    {
        try {
            if ($request->bancoOrigen == "ALL") {
                $result = DB::table("vi_reporteBancario as vrb")
                    ->whereRaw("vrb.fecha between '$request->fechaInicio' and '$request->fechaFin'")
                    ->orderBy('vrb.fecha', 'DESC')
                    ->paginate(200);
            } else {
                $result = DB::table("vi_reporteBancario as vrb")
                    ->whereRaw("
                    vrb.fecha between '$request->fechaInicio' and '$request->fechaFin' 
                    and (vrb.banco_origen ='$request->bancoOrigen' or vrb.banco_destino ='$request->bancoOrigen')
                ")->orderBy('vrb.fecha', 'DESC')
                    ->paginate(200);
            }
            return $this->responseOk($result);
        } catch (\Exception $e) {
            return $this->responseEx($e);
        }
    }


    public function listDataBusqueda(Request $request)
    {
        try {
            $searchTerm = $request->input('buscar'); // Obtener el término de búsqueda del request
            $fechaInicio = $request->input('fechaInicio');
            $fechaFin = $request->input('fechaFin');
    
            $query = DB::table("vi_reporteBancario as vrb")
                ->whereRaw("vrb.fecha between '$fechaInicio' and '$fechaFin'");
    
            // Agregar la búsqueda en todos los campos si se proporciona
            if (!empty($searchTerm)) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->Where('vrb.responsable', 'LIKE', "%$searchTerm%");
                      
                });
            }
    
            $result = $query->orderBy('vrb.fecha', 'DESC')->paginate(500);
    
            return $this->responseOk($result);
        } catch (\Exception $e) {
            return $this->responseEx($e);
        }
    }
    


    public function ListaBancoOrigen()
    {
        try {
            $result = DB::table('vi_cuentaBanco as cb')->whereRaw("cb.tipo=1")->get();
            return $this->responseOk($result);
        } catch (\Exception $e) {
            return $this->responseEx($e);
        }
    }
    public function ExportarToExcelDataBank(Request $request)
    {
        return (new BankExport($request->fechaInicio, $request->fechaFin, $request->banco))->download('ReportBank.xlsx');
    }
    //
    public function GetDataForResultStatusReport(Request $request)
    {
        try {
            $result = DB::table("vi_reporteEstadoResultado as vrer")
                ->whereRaw("vrer.fecha between '$request->fechaInicio' and '$request->fechaFin'")
                ->orderBy('vrer.fecha', 'DESC')
                ->paginate(200);
            return $this->responseOk($result);
        } catch (\Exception $e) {
            return $this->responseEx($e);
        }
    }
    public function ExportarToExcelStatusResult(Request $request)
    {
        return (new ResultStatusExport($request->fechaInicio, $request->fechaFin))->download('ReportResults.xlsx');
    }
}
