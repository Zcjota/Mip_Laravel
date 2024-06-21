<?php

namespace App\Http\Controllers\ControlCredito;

use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Z_Control_Credito;
use App\Model\Z_Detalle_Control_Credito;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ZControlCreditoController extends ApiController
{
    public function index(Request $request)
    {
    }

    public function create(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        // if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $obj = $request->data;
            $obj["fecha_registro"] = Carbon::now()->toDateTimeString(); //date("Y-m-t");
            $obj["usuario_registro"] = $this->userId();
            $ojb["ACTIVO"] = 1;
            $data = Z_Control_Credito::create($obj);
            $data->save();
            $detalleLog = array();
            foreach ($request->detalle as $index => $item) {
                Z_Detalle_Control_Credito::insert([
                    'cod_control' => $data->cod_control,
                    'cod_dsg' => $item["cod_dsg"],
                    'subtotal_bs' => $item["importe_bs"],
                    'subtotal_usd' => $item["importe_usd"],
                    'cod_bc_destino' => $item["cod_bc"],
                    'ACTIVO' => 1
                ]);
                $rep = $this->updateControlCodeDSG($item["cod_dsg"], $data->cod_control);
                if ($rep == null) {
                    throw new ExceptionValidate("Error Al Actualizar el Codigo de Control en el Detalle de las Solicitudes a Credito. =>" . $item["cod_dsg"]);
                }
                $rep = $this->updatePaymentStatusDSG($item["cod_dsg"], "PAGADO");
                if ($rep == null) {
                    throw new ExceptionValidate("Error Al Actualizar Estado de Pago en el Detalle de las Solicitudes a Credito. =>" . $item["cod_dsg"]);
                }
                array_push($detalleLog, $item);
            }
            $this->validateDetalleSG($request->detalle);
            DB::commit();
            $this->LogCreate("z_control_credito", $data->cod_control, ["data" => $data, "detalle" => $detalleLog]);
            return $this->responseOk($data->cod_control, "Guardado Exitosamente");
        } catch (ExceptionValidate $ex) {
            DB::rollBack();
            return $this->responseVal($ex->getMessage(), $ex);
        } catch (ExceptionError $ex) {
            DB::rollBack();
            return $this->responseEx($ex, $ex->getMessage());
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->responseEx($ex);
        }
    }
    private function validateDetalleSG($detalle){
        $flag = false;
        $cod_sg_aux = 0;
        foreach($detalle as $index => $item){
            if($flag){
                if($cod_sg_aux != $item["cod_sg"]){
                    $cod_sg_aux = $item["cod_sg"];
                    $resp = DB::table('z_detalle_solicitud_gasto as a')->WhereRaw("a.estado_pago = 'SIN PAGAR' and a.ACTIVO = 1 and a.cod_sg=".$cod_sg_aux)->first();
                    if($resp == null){
                        $resp1 = $this->updatePaymentStatusSG($cod_sg_aux, "FINALIZADO");
                        if ($resp1 == null) {
                            throw new ExceptionValidate("Error Al Actualizar Estado de Pago en las Solicitudes a Credito. =>" . $cod_sg_aux);
                        }
                    } else {
                        $resp1 = $this->updatePaymentStatusSG($cod_sg_aux, "PARCIAL");
                        if ($resp1 == null) {
                            throw new ExceptionValidate("Error Al Actualizar Estado de Pago en las Solicitudes a Credito. =>" . $cod_sg_aux);
                        }
                    }
                }
            } else{
                $cod_sg_aux = $item["cod_sg"];
                $resp = DB::table('z_detalle_solicitud_gasto as a')->WhereRaw("a.estado_pago = 'SIN PAGAR' and a.ACTIVO = 1 and a.cod_sg=".$cod_sg_aux)->first();
                if($resp == null){
                    $resp1 = $this->updatePaymentStatusSG($cod_sg_aux, "FINALIZADO");
                    if ($resp1 == null) {
                        throw new ExceptionValidate("Error Al Actualizar Estado de Pago en las Solicitudes a Credito. =>" . $cod_sg_aux);
                    }
                } else {
                    $resp1 = $this->updatePaymentStatusSG($cod_sg_aux, "PARCIAL");
                    if ($resp1 == null) {
                        throw new ExceptionValidate("Error Al Actualizar Estado de Pago en las Solicitudes a Credito. =>" . $cod_sg_aux);
                    }
                }
                $flag = true;
            }
        }
    }
    private function updatePaymentStatusSG($cod_sg, $estado_pago){
        return $result = DB::update("
            update z_solicitud_gasto set estado_pago='".$estado_pago."'
            where cod_sg=".$cod_sg
        );
    }
    private function updateControlCodeDSG($cod_dsg, $cod_control){
        return $result = DB::update("
            update z_detalle_solicitud_gasto set cod_control=".$cod_control."
            where cod_dsg=".$cod_dsg
        );
    }
    private function updatePaymentStatusDSG($cod_dsg, $estado_pago){
        return $result = DB::update("
            update z_detalle_solicitud_gasto set estado_pago='".$estado_pago."' 
            where cod_dsg=".$cod_dsg
        );
    }
}
