<?php

namespace App\Http\Controllers\Comunes\Combo;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Banco_Cuenta;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Model\Z_MovimientoBancoCuenta;
use Exception;
use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use Illuminate\Support\Facades\Validator;


class Z_Banco_CuentaController extends ApiController
{
    public function ListarTodo()
    {
        // if(!$request->ajax()) return redirect('/login');
        $result = DB::table('z_banco_cuenta as zbc')
        ->join('z_banco as zba','zbc.cod_b','=','zba.cod_b')
        ->select('zbc.cod_bc','zbc.nro_cuenta','zbc.moneda',
        'zba.cod_b','zba.sigla','zba.nombre', 'zbc.tipo')
        ->where('zbc.tipo','=','1')
        ->where('zbc.ACTIVO','=','1')
        ->get();
        return $this->responseOk($result);
    }
    public function ListarDestino()
    {
        // if(!$request->ajax()) return redirect('/login');
        // $result = DB::table('z_banco_cuenta as zbc')
        // ->join('z_banco as zba','zbc.cod_b','=','zba.cod_b')
        // ->select('zbc.cod_bc','zbc.nro_cuenta','zbc.moneda',
        // 'zba.cod_b','zba.sigla','zba.nombre', 'zbc.tipo')
        // ->where('zbc.tipo','=','2')
        // ->where('zbc.ACTIVO','=','1')
        // ->get();
        // return $this->responseOk($result);
        $result = DB::table('z_banco_cuenta as zbc')
        ->join('z_banco as zba', 'zbc.cod_b', '=', 'zba.cod_b')
        ->select('zbc.cod_bc', 'zbc.nro_cuenta', 'zbc.moneda', 'zba.cod_b', 'zba.sigla', 'zba.nombre', 'zbc.tipo')
        ->where('zbc.tipo', '=', '2')
        ->where('zbc.ACTIVO', '=', '1')
        ->orderBy('zba.sigla')
        ->orderBy('zbc.nro_cuenta')
        ->get();
 
         return $this->responseOk($result);

    }
    public function ListarDestinozpers()
    {
        // if(!$request->ajax()) return redirect('/login');
        // $result = DB::table('z_banco_cuenta as zbc')
        // ->join('z_banco as zba','zbc.cod_b','=','zba.cod_b')
        // ->select('zbc.cod_bc','zbc.nro_cuenta','zbc.moneda',
        // 'zba.cod_b','zba.sigla','zba.nombre', 'zbc.tipo')
        // ->where('zbc.tipo','=','2')
        // ->where('zbc.ACTIVO','=','1')
        // ->get();
        // return $this->responseOk($result);
        $result = DB::table('z_banco_cuenta as zbc')
        ->join('z_banco as zba', 'zbc.cod_b', '=', 'zba.cod_b')
        ->select('zbc.cod_bc', 'zbc.nro_cuenta', 'zbc.moneda', 'zba.cod_b', 'zba.sigla', 'zba.nombre', 'zbc.tipo')
        ->where('zbc.tipo', '=', '2')
        ->where('zbc.ACTIVO', '=', '1')
        ->orderBy('zba.sigla')
        ->orderBy('zbc.nro_cuenta')
        ->get();

         return $this->responseOk($result);

    }

    //----------------------------------------------------------------------------
    public function getAll(Request $request){
        if(!$request->ajax()) return redirect('/login');
        $query = DB::table('z_movimiento_banco_cuenta as mbc')
            ->join('z_banco_cuenta as bco', 'mbc.cod_bc_origen', '=', 'bco.cod_bc')
            ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
            ->join('z_banco_cuenta as bcd', 'mbc.cod_bc_destino', '=', 'bcd.cod_bc')
            ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
            ->join('usuario as usu','mbc.modificado_por','=','usu.COD_USUARIO')
            ->selectRaw(
                'mbc.cod_mbc, mbc.fecha, mbc.tipo_movimiento, mbc.cod_bc_origen,
                mbc.cod_bc_destino, mbc.importe_bs, mbc.importe_usd, mbc.glosa,
                mbc.fecha_modificacion, CONCAT(usu.NOMBRE," ", usu.AP_PATERNO," ", usu.AP_MATERNO) AS modificado_por, mbc.ACTIVO,
                bo.sigla as bo_sigla, bco.nro_cuenta as bo_nrocuenta, bco.moneda as bo_moneda,
                bd.sigla as bd_sigla, bcd.nro_cuenta as bd_nrocuenta, bcd.moneda as bd_moneda'
            )
        ->where('mbc.ACTIVO','=','1')
            ->whereRaw("mbc.glosa like '%".$request->buscar."%' and fecha between '".$request->fechaInicio."' and '".$request->fechaFin."'")->paginate(10);
        return $this->responseOk($query);
    }
    public function getByID($cod_mbc){
        // if(!$request->ajax()) return redirect('/login');
        $result = DB::table('z_movimiento_banco_cuenta as mbc')
            ->selectRaw(
                'mbc.cod_mbc, mbc.fecha, mbc.tipo_movimiento, mbc.cod_bc_origen,
                mbc.cod_bc_destino, mbc.importe_bs, mbc.importe_usd, mbc.glosa,
                mbc.fecha_modificacion, mbc.modificado_por, mbc.ACTIVO'
            )->whereRaw("mbc.cod_mbc=".$cod_mbc."")->first();
        return $this->responseOk($result);
    }
    public function create(Request $request){
        // if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->data,
                [
                    'fecha'=>'required',
                    'cod_bc_origen' => 'required',
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
            $data = Z_MovimientoBancoCuenta::create($obj);
            $data->save();
            DB::commit();
            $this->LogCreate("z_movimiento_banco_cuenta",$data->cod_mbc,$obj);
            return $this->responseOk($data->cod_sg, "Guardado Exitosamente");
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
                    'fecha'=>'required',
                    'cod_bc_origen' => 'required',
                    'cod_bc_destino' => 'required',
                    'importe_bs' => 'required',
                    'glosa' => 'required'
                ]
            );
            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }
            $obj = $request->data;
            $data = Z_MovimientoBancoCuenta::findOrFail($obj["cod_mbc"]);
            $data->fecha = $obj["fecha"];
            $data->cod_bc_origen = $obj["cod_bc_origen"];
            $data->cod_bc_destino = $obj["cod_bc_destino"];
            $data->importe_bs = $obj["importe_bs"];
            $data->importe_usd = $obj["importe_usd"];
            $data->glosa = $obj["glosa"];
            $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            $this->LogCreate("z_movimiento_banco_cuenta",$data->cod_mbc,$obj);
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
            $data = Z_MovimientoBancoCuenta::findOrFail($request->cod_mbc);
            $data->ACTIVO = 0;
            $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            // $this->LogCreate("z_movimiento_banco_cuenta",$data->cod_mbc, $request->cod_mbc);
            return $this->responseOk($data->cod_mbc, "Registro Anulado Correctamente");
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
