<?php

namespace App\Http\Controllers\Prestamos;

use App\Http\Controllers\Api\ApiController;
use App\Model\Z_solicitud_prestamo;
use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class ZSolicitudPrestamosController extends ApiController
{
    public function getAll(Request $request){
        if(!$request->ajax()) return redirect('/login');
        if($request->estado == "ALL"){
            $query = DB::table('z_solicitud_prestamos as sp')
            ->join('z_banco_cuenta as bco', 'sp.cod_bc_origen', '=', 'bco.cod_bc')
            ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
            ->join('z_banco_cuenta as bcd', 'sp.cod_bc_destino', '=', 'bcd.cod_bc')
            ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
            ->join('usuario as usu','sp.modificado_por','=','usu.COD_USUARIO')
            ->selectRaw('sp.cod_sp, sp.cod_pp, sp.proveedor, sp.tipo_pago, sp.cod_bc_origen, sp.cod_bc_destino,
                sp.fecha_pago, sp.importe_bs, sp.importe_usd, sp.interes_porcentaje, sp.dias, sp.glosa, sp.estado,
                sp.fecha_modificacion, sp.modificado_por, sp.ACTIVO,
                bo.sigla as bo_sigla, bco.nro_cuenta as bo_nrocuenta, bco.moneda as bo_moneda,
                bd.sigla as bd_sigla, bcd.nro_cuenta as bd_nrocuenta, bcd.moneda as bd_moneda'
            )
            ->whereRaw("sp.glosa like '%$request->buscar%' and sp.fecha_pago between '$request->fechaInicio' and '$request->fechaFin'")
            ->paginate(10);
        } else {
            $query = DB::table('z_solicitud_prestamos as sp')
            ->join('z_banco_cuenta as bco', 'sp.cod_bc_origen', '=', 'bco.cod_bc')
            ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
            ->join('z_banco_cuenta as bcd', 'sp.cod_bc_destino', '=', 'bcd.cod_bc')
            ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
            ->join('usuario as usu','sp.modificado_por','=','usu.COD_USUARIO')
            ->selectRaw('sp.cod_sp, sp.cod_pp, sp.proveedor, sp.tipo_pago, sp.cod_bc_origen, sp.cod_bc_destino,
                sp.fecha_pago, sp.importe_bs, sp.importe_usd, sp.interes_porcentaje, sp.dias, sp.glosa, sp.estado,
                sp.fecha_modificacion, sp.modificado_por, sp.ACTIVO,
                bo.sigla as bo_sigla, bco.nro_cuenta as bo_nrocuenta, bco.moneda as bo_moneda,
                bd.sigla as bd_sigla, bcd.nro_cuenta as bd_nrocuenta, bcd.moneda as bd_moneda'
            )
            ->whereRaw("sp.estado='$request->estado' and sp.glosa like '%$request->buscar%' and sp.fecha_pago between '$request->fechaInicio' and '$request->fechaFin'")
            ->paginate(10);
        }
        return $this->responseOk($query);
    }
    public function getByID($cod_sp){
        $result = DB::table('z_solicitud_prestamos as sp')
            ->join('usuario as usu', 'sp.modificado_por', '=', 'usu.COD_USUARIO')
            ->selectRaw(
                "concat(usu.NOMBRE,' ',usu.AP_PATERNO,' ',usu.AP_MATERNO) as nombre_usuario,
                sp.cod_sp, sp.cod_pp, sp.proveedor, sp.fecha_pago, sp.tipo_pago, sp.cod_bc_origen, 
                sp.cod_bc_destino, sp.importe_bs, sp.importe_usd, sp.interes_porcentaje, sp.dias, sp.glosa, 
                sp.fecha_modificacion, sp.modificado_por, sp.ACTIVO"
            )->whereRaw("sp.cod_sp = $cod_sp")->first();
        return $this->responseOk($result);
    }
    public function create(Request $request){
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->data,
                [
                    'fecha_pago'=>'required',
                    'cod_bc_destino' => 'required',
                    'importe_bs' => 'required',
                    'glosa' => 'required'
                ]             
            );
            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }
            $obj = $request->data;
            $obj["fecha_modificacion"] = Carbon::now()->toDateTimeString();
            $obj["modificado_por"] = $this->userId();
            $obj["ACTIVO"] = 1;
            $obj["estado"] = "INI";
            $data = Z_solicitud_prestamo::create($obj);
            $data->save();
            DB::commit();
            $this->LogCreate("z_solicitud_prestamos", $data->cod_sp,$obj);
            return $this->responseOk($data->cod_sp, "Guardado Exitosamente");
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
    public function update(Request $request){
        if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->data,
                [
                    'fecha_pago'=>'required',
                    'cod_bc_destino' => 'required',
                    'importe_bs' => 'required',
                    'glosa' => 'required'
                ]             
            );
            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }
            $obj = $request->data;
            $data = Z_solicitud_prestamo::findOrFail($obj["cod_sp"]);
            $data->fecha_pago = $obj["fecha_pago"];
            $data->tipo_pago = $obj["tipo_pago"];
            $data->cod_pp = $obj["cod_pp"];
            $data->proveedor = $obj["proveedor"];
            $data->cod_bc_origen = $obj["cod_bc_origen"];
            $data->cod_bc_destino = $obj["cod_bc_destino"];
            $data->importe_bs = $obj["importe_bs"];
            $data->importe_usd = $obj["importe_usd"];
            $data->interes_porcentaje = $obj["interes_porcentaje"];
            $data->dias = $obj["dias"];
            $data->glosa = $obj["glosa"];
            $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            return $this->responseOk($data->cod_sg, "Actualizado Exitosamente");
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
    public function delete(Request $request){
        if (!$request->ajax()) return redirect('/login');   
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $data = Z_solicitud_prestamo::findOrFail($request->cod_sp);
            $data->ACTIVO = 0;
            $data->estado = "ANU";
            $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            return $this->responseOk($data->cod_sp, "Registro Anulado Correctamente");
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
    public function getDataforApproved(Request $request)
    {
        $query = DB::table('z_solicitud_prestamos as sp')
            ->join('z_banco_cuenta as bco', 'sp.cod_bc_origen', '=', 'bco.cod_bc')
            ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
            ->join('z_banco_cuenta as bcd', 'sp.cod_bc_destino', '=', 'bcd.cod_bc')
            ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
            ->join('usuario as usu','sp.modificado_por','=','usu.COD_USUARIO')
            ->selectRaw('sp.cod_sp, sp.cod_pp, sp.proveedor, sp.tipo_pago, sp.cod_bc_origen, sp.cod_bc_destino,
                sp.fecha_pago, sp.importe_bs, sp.importe_usd, sp.interes_porcentaje, sp.dias, sp.glosa, sp.estado,
                sp.fecha_modificacion, sp.modificado_por, sp.ACTIVO,
                bo.sigla as bo_sigla, bco.nro_cuenta as bo_nrocuenta, bco.moneda as bo_moneda,
                bd.sigla as bd_sigla, bcd.nro_cuenta as bd_nrocuenta, bcd.moneda as bd_moneda'
            )
            ->whereRaw("sp.estado='INI' and sp.ACTIVO = 1 and sp.glosa like '%$request->buscar%' and sp.fecha_pago between '$request->fechaInicio' and '$request->fechaFin'")
            ->paginate(10);
        return $this->responseOk($query);
    }
    public function ApprovedRejectRegister(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');   
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $data = Z_solicitud_prestamo::findOrFail($request->cod_sp);
            $data->estado = $request->estado;
            $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            return $this->responseOk($data->cod_sp, "Estado de Registro Actualizado Correctamente.");
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
}
