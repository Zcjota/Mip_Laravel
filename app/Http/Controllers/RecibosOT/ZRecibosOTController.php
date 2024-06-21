<?php

namespace App\Http\Controllers\RecibosOT;

use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ZControlRecibosOT;
use App\Model\ZDetalleControlRecibosOT;
use App\Model\ZRegistroVentaOT;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\NumerosEnLetras;  //llamando la clase 

class ZRecibosOTController extends ApiController
{
    
    public function getDataOTForReceipts(Request $request) {

        $buscar = $request->buscar;
    if (strpos($buscar, ',') !== false) {
        $nroOrdenes = explode(',', $buscar);
        // Limpia los espacios en blanco y asegúrate de que cada elemento sea numérico para evitar inyecciones SQL
        $nroOrdenes = array_filter($nroOrdenes, function($value) {
            return is_numeric(trim($value));
        });
    } else {
        $nroOrdenes = [];
    }
        $query=DB::table('orden_trabajo as ot')
        ->leftjoin('registro_venta as rv', 'ot.COD_ORDEN_TRABAJO','=','rv.COD_ORDEN_TRABAJO')
        ->leftjoin('cliente as c','ot.COD_CLIENTE','=','c.COD_CLIENTE')
        ->selectRaw("
            ot.COD_ORDEN_TRABAJO,
            ot.NRO_ORDEN,
            ot.COD_CLIENTE,
            concat(c.NOMBRE, ' ', c.AP_CLIENTE, ' ', c.AM_CLIENTE) AS CLIENTE,
            ot.RAZON_SOCIAL,
            ot.FECHA_SERVICIO,
            ot.PRECIO,
            ot.BS AS MONEDA,
            ot.TIPO_PAGO,
            ot.PLAZO
        ")->whereRaw(" 
            ot.ACTIVO=1 AND ot.PRECIO>0 AND ot.ANULADA=0 AND (rv.NRO_RECIBO='' OR rv.NRO_RECIBO='0' OR rv.NRO_RECIBO IS null or rv.COD_ORDEN_TRABAJO IS NULL OR rv.ACTIVO=0 )
            AND ot.FECHA_SERVICIO between '$request->fechaInicio' and '$request->fechaFin'
            ")
            //Linea agregada
            ->where(function($q) use ($nroOrdenes, $buscar) {
                if (!empty($nroOrdenes)) {
                    // Usa whereIn para buscar múltiples NRO_ORDEN si $nroOrdenes no está vacío
                    $q->whereIn('ot.NRO_ORDEN', $nroOrdenes);
                } else {
                    // Realiza una búsqueda normal si no se especificaron múltiples NRO_ORDEN
                    $q->where('c.NOMBRE', 'like', "%{$buscar}%")
                      ->orWhere('c.AP_CLIENTE', 'like', "%{$buscar}%")
                      ->orWhere('ot.NRO_ORDEN', 'like', "%{$buscar}%");
                }
            })
        //     (c.NOMBRE like '%$request->buscar%' or c.AP_CLIENTE like '%$request->buscar%' or ot.NRO_ORDEN like '%$request->buscar%')
        // ")
        ->orderBy('ot.FECHA_SERVICIO', 'DESC')->paginate(10);
        return $this->responseOk($query);
    }
    public function listadoRecibosAnular() {
        try {
            $recibos = DB::table('z_control_recibos_ot')
                ->join('z_detalle_control_recibos_ot', 'z_control_recibos_ot.cod_cr', '=', 'z_detalle_control_recibos_ot.cod_dcr')
                ->where('z_control_recibos_ot.BANDERA', 0)
                ->where('z_control_recibos_ot.ACTIVO', 1)
                ->where('z_detalle_control_recibos_ot.ACTIVO', 1)
                ->select('z_control_recibos_ot.*', 'z_detalle_control_recibos_ot.*')
                ->get();
            
            return response()->json(['data' => $recibos], 200);
        } catch (\Exception $e) {
            // Manejo de la excepción
            return response()->json(['error' => 'Ocurrió un error al obtener los recibos', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function create(Request $request) {
        if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $obj = $request->data;
            $obj["nro_recibo"] = $this->getCorrelativeNumber($obj["fecha"]);
            $obj["monto_literal_usd"] = NumerosEnLetras::convertir($obj["total_usd"], "Dolares Americanos", true, "Centavos");
            $obj["glosa"] = strtoupper($obj["glosa"]);
            $obj["usuario_registro"] = $this->userId();
            $obj["fecha_registro"] = Carbon::now()->toDateTimeString();
            $obj["ACTIVO"] = 1;
            $obj["dif"] = isset($request->data['dif']) ? $request->data['dif'] : null;
            $obj["dif_ot"] = $request->input('data.dif_ot', 0);
            $data = ZControlRecibosOT::create($obj);
            $data->save();
    
            foreach ($obj["detalle"] as $key => $item) {
                if (isset($item["cod_ot"]) && isset($item["nro_ot"])) {
                    if ($this->validateRegister_registro_venta($item["cod_ot"], $item["nro_ot"])) {
                        $rep = $this->updateTable_registro_venta($item["cod_ot"], $item["nro_ot"], $obj["fecha"], $obj["nro_recibo"], $obj["total_bs"], $obj["tipo_pago"],$obj["glosa"]);
                        if ($rep == null) {
                            throw new ExceptionValidate("Error al Actualizar Fecha y Número de Recibo en la Tabla registro_venta, Nro OT: " . $item["nro_ot"]);
                        }
                    } else {
                        $resp = null;
                        if ($obj["tipo_pago"] == 1) {
                            $resp = ZRegistroVentaOT::insert([
                                'COD_ORDEN' => $item["nro_ot"],
                                'CODNUM' => 1,
                                'COD_ORDEN_TRABAJO' => $item["cod_ot"],
                                'DESCRIPCION' => $obj["glosa"],
                                'MONTO_BS' => $obj["total_bs"],
                                'MONTO_DOLAR' => $obj["total_usd"],
                                'FECHA_RECIBO_OLD' => '',
                                'FECHA_RECIBO' => $obj["fecha"],
                                'NRO_RECIBO' => $obj["nro_recibo"],
                                'CONTADO_NRO_DEPOSITO' => "SI",
                                'NRO_CHEQUE_NRO_DEPOSITO' => "",
                                'TRANSFERENCIA' => "",
                                'FECHA_FACTURA' => "",
                                'NRO_FACTURA' => "",
                                'DEBE' => "NO DEBE",
                                'FINANCIADO' => "",
                                'BANDERA' => 1,
                                'ACTIVO' => 1,
                                'OBSERVACION' => ""
                            ]);
                        } elseif ($obj["tipo_pago"] == 2) {
                            $resp = ZRegistroVentaOT::insert([
                                'COD_ORDEN' => $item["nro_ot"],
                                'CODNUM' => 1,
                                'COD_ORDEN_TRABAJO' => $item["cod_ot"],
                                'DESCRIPCION' => $obj["glosa"],
                                'MONTO_BS' => $obj["total_bs"],
                                'MONTO_DOLAR' => $obj["total_usd"],
                                'FECHA_RECIBO_OLD' => '',
                                'FECHA_RECIBO' => $obj["fecha"],
                                'NRO_RECIBO' => $obj["nro_recibo"],
                                'CONTADO_NRO_DEPOSITO' => "",
                                'NRO_CHEQUE_NRO_DEPOSITO' => "SI",
                                'TRANSFERENCIA' => "",
                                'FECHA_FACTURA' => "",
                                'NRO_FACTURA' => "",
                                'DEBE' => "NO DEBE",
                                'FINANCIADO' => "",
                                'BANDERA' => 1,
                                'ACTIVO' => 1,
                                'OBSERVACION' => ""
                            ]);
                        } elseif ($obj["tipo_pago"] == 3) {
                            $resp = ZRegistroVentaOT::insert([
                                'COD_ORDEN' => $item["nro_ot"],
                                'CODNUM' => 1,
                                'COD_ORDEN_TRABAJO' => $item["cod_ot"],
                                'DESCRIPCION' => $obj["glosa"],
                                'MONTO_BS' => $obj["total_bs"],
                                'MONTO_DOLAR' => $obj["total_usd"],
                                'FECHA_RECIBO_OLD' => '',
                                'FECHA_RECIBO' => $obj["fecha"],
                                'NRO_RECIBO' => $obj["nro_recibo"],
                                'CONTADO_NRO_DEPOSITO' => "",
                                'NRO_CHEQUE_NRO_DEPOSITO' => "",
                                'TRANSFERENCIA' => "SI",
                                'FECHA_FACTURA' => "",
                                'NRO_FACTURA' => "",
                                'DEBE' => "NO DEBE",
                                'FINANCIADO' => "",
                                'BANDERA' => 1,
                                'ACTIVO' => 1,
                                'OBSERVACION' => ""
                            ]);
                        } else {
                            // Código en caso de que tipo_pago no sea 1, 2 o 3
                        }
    
                        if (!$resp) {
                            throw new ExceptionValidate("Error al insertar en la tabla registro_venta.");
                        }
                    }
    
                    if (!$this->registerDetailControlReceipts($data->cod_cr, $item)) {
                        throw new ExceptionValidate("Se Presentó una Exepción al Momento de Registrar en la tabla: z_detalle_control_recibos_ot, de la Base de Datos.");
                    }
                } else {
                    throw new ExceptionValidate("El elemento del detalle no contiene 'cod_ot' o 'nro_ot'.");
                }
            }
    
            DB::commit();
            return $this->responseOk($data["cod_cr"], "Guardado Exitosamente");
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
    public function getDataForPdfByID($codigo){
        $result = DB::table("z_control_recibos_ot as ot")
            ->join('z_banco_cuenta as bc','bc.cod_bc','=','ot.cod_bc_destino')
            ->join('z_banco as b','b.cod_b','=','bc.cod_b')
            ->selectRaw('
                concat("R-",LPAD(ot.nro_recibo,5,0)) as nro_recibo, ot.recibi_de,
                ot.importe_bs, ot.importe_usd, ot.total_usd, ot.monto_literal_usd,
                ot.glosa as por_concepto, ot.tipo_pago, ot.tipo_cambio, ot.fecha,
                concat(b.sigla,"-",bc.nro_cuenta) as banco_destino,ot.dif,ot.dif_ot 
            ')->where('ot.cod_cr', '=', $codigo)->first();

        $resutDetalle = DB::table("z_detalle_control_recibos_ot as dot")->where('dot.cod_cr','=', $codigo)->get();
        return \PDF::loadView('pdf.imprimirReciboOT', ['data' => $result, 'detalle' => $resutDetalle])->stream('ReciboOT_'.$result->nro_recibo.'.pdf');
        // return $this->responseOk($resutDetalle);
    }
    public function getDataReceipts(Request $request){
        $query = DB::table('z_control_recibos_ot as ot')
        ->selectRaw("
            ot.cod_cr, ot.cod_bc_destino, ot.nro_recibo,
            concat('R-',LPAD(ot.nro_recibo,5,0)) as nro_recibo_format, ot.fecha, ot.recibi_de, ot.tipo_pago,
            ot.importe_bs, ot.importe_usd, ot.glosa,ot.dif,ot.dif_ot,ot.BANDERA AS bandera 
        ")->whereRaw(" 
            ot.ACTIVO=1 AND ot.fecha between '".$request->fechaInicio."' AND '".$request->fechaFin."' 
            AND (ot.glosa like '%".$request->buscar."%' OR ot.recibi_de like '%".$request->buscar."%')
        ")->orderBy('ot.fecha', 'DESC')->paginate(10);
        return $this->responseOk($query);
    }
    private function validateRegister_registro_venta($cod_ot_, $nro_ot_){
        return $result = DB::table('registro_venta')->Where('COD_ORDEN_TRABAJO', '=', $cod_ot_)->Where('COD_ORDEN', '=', $nro_ot_)->first();
    }
    private function updateTable_registro_venta($cod_ot_, $nro_ot_, $fecha_recibo_, $nro_recibo_, $total_bs, $tipo_pago,$glosa){
       
        $dataToUpdate = [
            'FECHA_RECIBO' => $fecha_recibo_,
            'NRO_RECIBO' => $nro_recibo_,
            'DEBE' => 'NO DEBE',
            'MONTO_BS' => $total_bs,
            'DESCRIPCION'=>$glosa,
            'ACTIVO' => 1
        ];
    
        if ($tipo_pago == 1) {
            $dataToUpdate['CONTADO_NRO_DEPOSITO'] = 'SI';
            $dataToUpdate['NRO_CHEQUE_NRO_DEPOSITO'] = '';
            $dataToUpdate['TRANSFERENCIA'] = '';
        } elseif ($tipo_pago == 2) {
            $dataToUpdate['CONTADO_NRO_DEPOSITO'] = '';
            $dataToUpdate['NRO_CHEQUE_NRO_DEPOSITO'] = 'SI';
            $dataToUpdate['TRANSFERENCIA'] = '';
        } elseif ($tipo_pago == 3) {
            $dataToUpdate['CONTADO_NRO_DEPOSITO'] = '';
            $dataToUpdate['NRO_CHEQUE_NRO_DEPOSITO'] = '';
            $dataToUpdate['TRANSFERENCIA'] = 'SI';
        }
    
        $result = DB::table('registro_venta')
            ->where('COD_ORDEN_TRABAJO', $cod_ot_)
            ->where('COD_ORDEN', $nro_ot_)
            ->where('ACTIVO', 0)
            ->update($dataToUpdate);
    
        return $result;
    }
    // private function updateTable_registro_venta($cod_ot_, $nro_ot_, $fecha_recibo_, $nro_recibo_, $total_bs, $tipo_pago){
    //     return $result = DB::update("
    //         UPDATE registro_venta SET FECHA_RECIBO = '$fecha_recibo_', NRO_RECIBO = '$nro_recibo_', DEBE = 'NO DEBE', MONTO_BS = '$total_bs' 
    //         WHERE COD_ORDEN_TRABAJO = $cod_ot_ AND COD_ORDEN = $nro_ot_
    //     ");
    // }
    private function insertTable_registro_venta($obj, $nro_recibo){
        try {
            $result = ZRegistroVenta::insert([
                'COD_ORDEN' => $obj["nro_ot"],
                'COD_NUM' => 1,
                'COD_ORDEN_TRABAJO' => $obj["cod_ot"],
                'DESCRIPCION' => $obj["glosa"],
                'MONTO_BS' => $obj["total_bs"],
                'MONTO_DOLAR' => $obj["total_usd"],
                'FECHA_RECIBO_OLD' => '',
                'FECHA_RECIBO' => $obj["fecha"],
                'NRO_RECIBO' => $nro_recibo,
                'CONTADO_NRO_DEPOSITO' => '',
                'NRO_CHEQUE_NRO_DEPOSITO' => '',
                'TRANSFERENCIA' => '',
                'FECHA_FACTURA' => '',
                'NRO_FACTURA' => '',
                'DEBE' => "DEBE",
                'FINANCIADO' => '',
                'BANDERA' => 1,
                'ACTIVO' => 1,
                'OBSERVACION' => ''
            ]);
        } catch (Exception $ex) {
            return $this->responseEx($ex);
            $result = false;
        }
        return $result;
    }
    private function getCorrelativeNumber($fecha){
        $ini = date("Y-m-01",strtotime($fecha));
        $fin = date("Y-m-t",strtotime($fecha));
        $result = DB::table('z_control_recibos_ot as cr')->whereBetween('cr.fecha', [$ini, $fin])->max('nro_recibo') + 1;
        return $result;
    }
    // private function getCorrelativeNumber($fecha){
    //     //RECIBOS NR DE OT JR
    //     // Convertir la fecha a un objeto Carbon para facilitar la manipulación
    //     $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);
    
    //     // Buscar el último número de recibo para la fecha dada
    //     $ultimoRecibo = ZControlRecibosOT::whereDate('fecha', '=', $fechaCarbon->toDateString())->max('nro_recibo');
    
    //     if ($ultimoRecibo) {
    //         // Si se encuentra un recibo, incrementar el último número encontrado
    //         return $ultimoRecibo + 1;
    //     } else {
    //         // Si no hay recibos para esa fecha, comenzar con 1
    //         return 1;
    //     }
    // }
    
    private function registerDetailControlReceipts($id, $obj){
        return $result = ZDetalleControlRecibosOT::insert([
            'cod_cr' => $id,
            'cod_ot' => $obj["cod_ot"],
            'nro_ot' => $obj["nro_ot"],
            'cod_cliente_ot' => $obj["cod_cliente_ot"],
            'razon_social_ot' => $obj["razon_social_ot"],
            'precio_ot' => $obj["precio_ot"],
            'moneda_ot' => $obj["moneda_ot"]==0?"USD":"BS",
            'fecha_servicio_ot' => $obj["fecha_servicio_ot"],
            'ACTIVO' => 1
        ]);
    }
}