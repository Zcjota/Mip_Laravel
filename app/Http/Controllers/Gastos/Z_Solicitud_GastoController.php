<?php

namespace App\Http\Controllers\Gastos;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Detalle_Solicitud_Gasto;
use App\Model\Z_Solicitud_Gasto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;

use App\Model\GastoPresupuesto;
use App\Model\DetalleGasto;
use Exception;


class Z_Solicitud_GastoController extends ApiController
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        if ($request->estado == "ALL") {
            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,
            zsg.vc,zsg.cod_bc,zsg.total_bs,
            zsg.total_usd,zsg.fecha_pago,
            zsg.glosa,zsg.estado,
            zsg.fecha_revision,zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
        GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->whereRaw('
                zsg.tipo_sg="' . $request->tipo_sg . '"
                and (zsg.tipo_gasto like "%' . $request->tipo_gasto . '%" or zsg.tipo_gasto IS NULL or zsg.tipo_gasto = "")
                and (zsg.fecha_sg>="' . $request->fechaInicio . '" and zsg.fecha_sg<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_sg,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        } else {
            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,zsg.tipo_pago,
            zsg.tipo_gasto,zsg.vc,
            zsg.cod_bc,zsg.total_bs,
            zsg.total_usd,
            zsg.fecha_pago,
            zsg.glosa,
            zsg.estado,
            zsg.fecha_revision,
            zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->whereRaw('
                zsg.tipo_sg="' . $request->tipo_sg . '"
                and (zsg.tipo_gasto like "%' . $request->tipo_gasto . '%" or zsg.tipo_gasto IS NULL or zsg.tipo_gasto = "")
                and (zsg.fecha_sg>="' . $request->fechaInicio . '" and zsg.fecha_sg<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_sg,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        }
        return $this->responseOk($result);
    }


    public function TraerPorProveedor(Request $request)
    {

        if (!$request->ajax()) return redirect('/login');
        if ($request->estado == "ALL") {

            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,zsg.tipo_pago,
            zsg.tipo_gasto,zsg.vc,
            zsg.cod_bc,
            zsg.total_bs,
            zsg.total_usd,
            zsg.fecha_pago,
            zsg.glosa,
            zsg.estado,
            zsg.fecha_revision,
            zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->whereRaw('
                zsg.tipo_sg="' . $request->tipo_sg . '"
                and (zsg.tipo_gasto like "%' . $request->tipo_gasto . '%" or zsg.tipo_gasto IS NULL or zsg.tipo_gasto = "")
                and (zsg.fecha_sg>="' . $request->fechaInicio . '" and zsg.fecha_sg<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_sg,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        } else {

            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('
            zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,zsg.tipo_pago,
            zsg.tipo_gasto,zsg.vc,
            zsg.tipo_gasto,
            zsg.cod_bc,zsg.total_bs,
            zsg.total_usd,zsg.fecha_pago,
            zsg.glosa,
            zsg.estado,zsg.fecha_revision,
            zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->where('zsg.fecha_sg', '>=', $request->fechaInicio)
                ->where('zsg.fecha_sg', '<=', $request->fechaFin)
                ->where('z_detalle_solicitud_gasto.cod_proveedor', '=', $request->cod_proveedor)
                ->where('zsg.ACTIVO', '=', 1)
                ->where('z_detalle_solicitud_gasto.ACTIVO', '=', 1)
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        }
        return $this->responseOk($result);
    }


    public function ProveedorCuentaContable(Request $request)
    {

        if (!$request->ajax()) return redirect('/login');
        $result = DB::table("z_solicitud_gasto as zsg")
            ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
            ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
            ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
            ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
            ->selectRaw('
        zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
        concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
        zsg.fecha_registro,zsg.cod_usuario,
        concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
        zsg.gestion,
        zsg.tipo_pago,zsg.tipo_gasto,
        zsg.vc,zsg.tipo_gasto,
        zsg.cod_bc,zsg.total_bs,
        zsg.total_usd,zsg.fecha_pago,
        zsg.glosa,
        zsg.estado,zsg.fecha_revision,
        zsg.ACTIVO,
        GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
            ->where('zsg.fecha_sg', '>=', $request->fechaInicio)
            ->where('zsg.fecha_sg', '<=', $request->fechaFin)
            ->where('z_detalle_solicitud_gasto.cod_proveedor', '=', $request->cod_proveedor)
            ->where('z_detalle_solicitud_gasto.cod_cc', '=', $request->cod_cc)
            ->where('zsg.ACTIVO', '=', 1)
            ->where('z_detalle_solicitud_gasto.ACTIVO', '=', 1)
            ->groupBy('zsg.cod_sg')
            ->paginate(1000);

        return $this->responseOk($result);
    }

    public function TraerProveedorCC(Request $request)
    {

        if (!$request->ajax()) return redirect('/login');
        if ($request->estado == "ALL") {

            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,
            zsg.tipo_pago,
            zsg.tipo_gasto,
            zsg.vc,
            zsg.cod_bc,
            zsg.total_bs,
            zsg.total_usd,
            zsg.fecha_pago,
            zsg.glosa,
            zsg.estado,
            zsg.fecha_revision,
            zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->whereRaw('
                zsg.tipo_sg="' . $request->tipo_sg . '"
                and (zsg.tipo_gasto like "%' . $request->tipo_gasto . '%" or zsg.tipo_gasto IS NULL or zsg.tipo_gasto = "")
                and (zsg.fecha_sg>="' . $request->fechaInicio . '" and zsg.fecha_sg<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_sg,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        } else {

            $result = DB::table("z_solicitud_gasto as zsg")
                ->leftjoin('usuario as usu', 'zsg.revisado_por_usuario', '=', 'usu.COD_USUARIO')
                ->join('z_detalle_solicitud_gasto', 'zsg.cod_sg', '=', 'z_detalle_solicitud_gasto.cod_sg')
                ->join('cuenta', 'z_detalle_solicitud_gasto.cod_cc', '=', 'cuenta.COD_CUENTA')
                ->join('z_prov_pers', 'z_prov_pers.id', '=', 'z_detalle_solicitud_gasto.cod_proveedor')
                ->selectRaw('
            zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
            concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as revisado_por_usuario,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
            zsg.gestion,zsg.tipo_pago,
            zsg.tipo_gasto,zsg.vc,
            zsg.tipo_gasto,
            zsg.cod_bc,
            zsg.total_bs,
            zsg.total_usd,
            zsg.fecha_pago,
            zsg.glosa,
            zsg.estado,
            zsg.fecha_revision,
            zsg.ACTIVO,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_cc) as cod_cc,
            GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
            GROUP_CONCAT(z_detalle_solicitud_gasto.cod_proveedor) as cod_proveedor,
            GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo')
                ->where('zsg.fecha_sg', '>=', $request->fechaInicio)
                ->where('zsg.fecha_sg', '<=', $request->fechaFin)
                ->where('z_detalle_solicitud_gasto.cod_cc', '=', $request->cod_cc)
                ->groupBy('zsg.cod_sg')
                ->paginate(1000);
        }
        return $this->responseOk($result);
    }


    public function get($cod_sg)
    {
        // if(!$request->ajax()) return redirect('/login');
        // $result = DB::table("z_solicitud_gasto as zsg")
        //     ->join('usuario as usu', 'zsg.cod_usuario', '=', 'usu.COD_USUARIO')
        //     ->selectRaw('concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as nombre_usuario,
        //     zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
        // zsg.fecha_registro,zsg.cod_usuario,
        // concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
        // zsg.gestion,zsg.tipo_pago,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
        // zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO')
        //     ->where('zsg.cod_sg', '=', $cod_sg)->first();
        // if ($result == null) $this->responseVal("La Solicitud no Existe");

        // $detalle = Z_Detalle_Solicitud_Gasto::where("cod_sg", '=', $cod_sg)
        //     ->where("ACTIVO", '=', 1)
        //     ->get();
        $result = DB::table("z_solicitud_gasto as zsg")
            ->join('usuario as usu', 'zsg.cod_usuario', '=', 'usu.COD_USUARIO')
            ->join('z_banco_cuenta as bc', 'zsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as nombre_usuario,
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_origen,
                zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
                zsg.fecha_registro,zsg.cod_usuario,
                concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
                zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.vc,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
                zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO,
                b.sigla as banco_sigla, b.nombre as banco_nombre, bc.nro_cuenta as banco_nro_cuenta, bc.moneda as banco_moneda
            ')
            ->where('zsg.cod_sg', '=', $cod_sg)->first();
        if ($result == null) $this->responseVal("El Registro no Existe.");

        $detalle = DB::table('z_detalle_solicitud_gasto as dsg')
            ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable,    
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_destino,
                pp.nombre_completo as nombre_proveedor,
                dsg.cod_sg, dsg.cod_proveedor, dsg.cod_cc, dsg.detalle, dsg.importe_bs, dsg.importe_usd, dsg.cod_bc, dsg.ACTIVO,
                b.sigla as banco_sigla, bc.nro_cuenta as banco_nroCta, bc.moneda as banco_moneda,
                dsg.BANDERA AS bandera

            ')
            ->where('dsg.ACTIVO', '=', 1)
            ->where("dsg.cod_sg", '=', $cod_sg)->get();

        return $this->responseOk([
            'data' => $result,
            'detalle' => $detalle,
        ]);
    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();

        try {
            // return $this->responseOk($input, "Guardado Exitosamente");
            $validator = Validator::make(
                $request->data,
                [
                    'tipo_sg' => 'required',
                    'fecha_sg' => 'required',
                    // 'cod_usuario' => Rule::requiredIf($this->userValidate()),
                    // 'nro_sg' => 'required',
                    // 'gestion' => 'required',
                    'tipo_pago' => 'required',
                    'tipo_gasto' => 'required',
                    // 'vc' => 'required',
                    'cod_bc' => 'required',
                    'total_bs' => 'required|numeric|min:0',
                    'total_usd' => 'required|numeric|min:0',
                    // 'fecha_pago' => 'required',
                    'glosa' => 'required',
                    // 'estado' => 'required',
                    ''
                    // 'revisado_por_usuario' => Rule::requiredIf(function () {
                    //     return session()->get('codigo') == null;
                    // }),
                    // // 'fecha_revision' => 'required',
                ],
                [],
                [
                    'tipo_sg' => 'tipo',
                    'fecha_sg' => 'fecha solicitud de gasto',
                    'cod_usuario' => 'usuario',
                    'nro_sg' => 'correlativo',
                    'gestion' => 'gestion',
                    'tipo_pago' => 'tipo de pago',
                    'tipo_gasto' => 'tipo de gasto',
                    // 'vc' => 'vinculacion credito',
                    'cod_bc' => 'codigo banco',
                    'total_bs' => 'total bs',
                    'total_usd' => 'total usd',
                    // 'fecha_pago' => 'required',
                    'glosa' => 'glosa',
                    'estado' => 'estado',
                    // 'revisado_por_usuario' => 'usuario'
                ]
            );

            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }

            if (((float)$request->data["total_bs"] + (float)$request->data["total_usd"]) == (float) 0) {
                throw new ExceptionValidate('Ingresar Monto en Bs o $us');
            }
            if ($request->detalle == null) {
                throw new ExceptionValidate("Agregar detalle");
            }
            if (count($request->detalle) == 0) {
                throw new ExceptionValidate("Agregar detalle");
            }
            $validate = Validator::make(
                $request->detalle,
                [
                    '*.cod_proveedor' => 'required',
                    '*.cod_cc' => 'required',
                    '*.detalle' => 'required',
                    '*.importe_bs' => 'required',
                    '*.importe_usd' => 'required',
                    '*.cod_bc' => 'required',
                    // '*.ACTIVO' => 'required',
                ],
                [],
                [
                    '*.cod_proveedor' => 'proveedor',
                    '*.cod_cc' => 'cuenta',
                    '*.detalle' => 'descripcion',
                    '*.importe_bs' => 'importe bs',
                    '*.importe_usd' => 'importe usd',
                    '*.cod_bc' => 'banco cuenta',
                    '*.ACTIVO' => 'activo',
                ]
            );

            if ($validate->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validate);
            }
            $obj = $request->data;
            $ini = date("Y-m-01", strtotime($obj["fecha_sg"]));
            $fin = date("Y-m-t", strtotime($obj["fecha_sg"]));
            $obj["nro_sg"] = DB::table('z_solicitud_gasto as zsg')
                ->whereBetween('zsg.fecha_sg', [$ini, $fin])
                ->max('nro_sg') + 1;
            $obj["fecha_registro"] = Carbon::now()->toDateTimeString();
            $obj["fecha_revision"] = null;
            $obj['revisado_por_usuario'] = null;
            $obj['gestion'] = date("m-Y");
            $obj["estado"] = "INI";
            $obj["ACTIVO"] = 1;
            $obj["cod_usuario"] = $this->userId();
            // $obj["cod_usuario"] = 2;
            $data = Z_Solicitud_Gasto::create($obj);
            $data->save();
            $detalleLog = array();
            foreach ($request->detalle as $ep => $det) {
                if (((float)$det["importe_bs"] + (float)$det["importe_usd"]) == (float) 0) {
                    throw new ExceptionValidate('Ingresar Monto en Bs o $us en el detalle');
                }
                $det["cod_sg"] = $data->cod_sg;
                $det["ACTIVO"] = 1;
                $detalle = Z_Detalle_Solicitud_Gasto::create($det);
                $detalle->save();
                array_push($detalleLog, $detalle);
            }
            DB::commit();
            $this->LogCreate("z_solicitud_gasto", $data->cod_sg, ["data" => $data, "detalle" => $detalleLog]);
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
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');

        DB::beginTransaction();
        try {
            if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
            // return $this->responseOk($input, "Guardado Exitosamente");
            $validator = Validator::make(
                $request->data,
                [
                    'cod_sg' => 'required',
                    'fecha_sg' => 'required',
                    'glosa' => 'required',
                    'tipo_gasto' => 'required',
                    // 'vc' => 'required',
                ],
                [],
                [
                    'cod_sg' => 'codigo solicitud de gasto',
                    'fecha_sg' => 'fecha solicitud de gasto',
                    'glosa' => 'glosa',
                    'tipo_gasto' => 'Tipo gasto requerido',
                    // 'vc' => 'Vinvulacion de credito requerida',
                ]
            );
            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }

            $validate = Validator::make(
                $request->detalle,
                [
                    '*.cod_proveedor' => 'required',
                    '*.cod_cc' => 'required',
                    '*.detalle' => 'required',
                    '*.importe_bs' => 'required',
                    '*.importe_usd' => 'required',
                    '*.cod_bc' => 'required',
                    // '*.ACTIVO' => 'required',
                ],
                [],
                [
                    '*.cod_proveedor' => 'proveedor',
                    '*.cod_cc' => 'cuenta',
                    '*.detalle' => 'descripcion',
                    '*.importe_bs' => 'importe bs',
                    '*.importe_usd' => 'importe usd',
                    '*.cod_bc' => 'banco cuenta',
                    '*.ACTIVO' => 'activo',
                ]
            );
            if ($validate->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validate);
            }

            // $data = Z_Solicitud_Gasto::findOrFail($request->data["cod_sg"]);
            // $this->responseOk($request->data);
            // $cod_sg=$request->data["cod_sg"];
            $data = Z_Solicitud_Gasto::findOrFail($request->data["cod_sg"]);
            if ($data == null) throw new ExceptionValidate("La Solicitud no Existe");
            $data->fecha_sg = $request->data["fecha_sg"];
            $data->glosa = $request->data["glosa"];
            $data->total_bs = $request->data["total_bs"];
            $data->total_usd = $request->data["total_usd"];
            $data->tipo_gasto = $request->data["tipo_gasto"];
            // $data->vc = $request->data["vc"];
            $data->save();
            Z_Detalle_Solicitud_Gasto::where('cod_sg', '=', $data->cod_sg)->update(['ACTIVO' => '0']);
            // DB::update('update z_detalle_solicitud_gasto set ACTIVO = 0 where cod_sg = ?', [$data->cod_sg]);
            $detalleLog = array();
            foreach ($request->detalle as $ep => $det) {
                $detalle = Z_Detalle_Solicitud_Gasto::create(
                    [
                        'cod_sg' => $data->cod_sg,
                        'ACTIVO' => 1,
                        'cod_proveedor' => $det['cod_proveedor'],
                        'cod_cc' => $det['cod_cc'],
                        'detalle' => $det['detalle'],
                        'importe_bs' => $det['importe_bs'],
                        'importe_usd' => $det['importe_usd'],
                        'cod_bc' => $det['cod_bc']
                    ]
                );
                $detalle->save();
                array_push($detalleLog, $detalle);
            }
            DB::commit();
            $this->LogUpdate("z_solicitud_gasto", $data->cod_sg, ["data" => $data, "detalle" => $detalleLog]);

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
    public function delete($cod_sg)
    {
        try {
            if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
            DB::beginTransaction();
            try {
                $data = Z_Solicitud_Gasto::findOrFail($cod_sg);
                $data->estado = "ANU";
                $data->ACTIVO = 0;
                $data->fecha_revision = Carbon::now()->toDateTimeString();
                $data->revisado_por_usuario = $this->userId();
                $data->save();
                DB::commit();
                $this->LogTxn("z_solicitud_gasto", $data->cod_sg, ["data" => $data, "detalle" => null], "ANU");
                return $this->responseOk($data->cod_sg, "Anulado Exitosamente");
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->responseEx($ex, "Error al Anular");
            }
        } catch (Exception $ex) {
            return $this->responseEx($ex);
        }
    }
    public function updateEstado(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        // if (!$this->userValid()) return $this->responseVal("Usuario no Valido");

        try {
            DB::beginTransaction();
            try {
                $data = Z_Solicitud_Gasto::findOrFail($request->cod_sg);
                $tipo_pago = ($data->tipo_sg == "CONTADO" ? 1 : 2);
                if ($request->estado == "APR") {
                    if ($data->estado != "INI") {
                        throw new ExceptionValidate("La Solicitud no se encuentra en estado Iniciado::Solicitud=>" . $data->cod_sg);
                    }
                    $apertura = DB::table('apertura as ape')
                        ->WhereRaw('MONTH("' . $data->fecha_sg . '")=ape.MES 
                        and YEAR("' . $data->fecha_sg . '")=ape.GESTION
                    and ape.ACTIVO=1')
                        ->select('ape.COD_APERTURA')
                        ->first();
                    // return $this->responseOk($detalle_gasto);

                    if ($apertura == null) {
                        throw new ExceptionValidate("primero se tiene que aperturar presupuesto
                        para el mes y año que indica la fecha de
                        la solicitud gasto=>" . $data->cod_sg);
                    }
                    // $queryPresupuestCuenta = DB::table('presupuesto_cuenta as pcu')
                    //     ->Where('pcu.ACTIVO', '=', '1')
                    //     ->where('pcu.COD_APERTURA', '=', $apertura->COD_APERTURA)
                    //     ->select('pcu.COD_APERTURA', 'pcu.COD_CUENTA', 'pcu.MONTO_PRESUPUESTO');

                    $detalle_gasto = DB::table('z_detalle_solicitud_gasto as zdg')
                        ->letfJoin('presupuesto_cuenta as pcu', function ($join) use ($apertura) {
                            $join->on('zdg.COD_CUENTA', '=', 'pcu.COD_CUENTA')
                                ->on('pcu.COD_APERTURA', '=', $apertura->COD_APERTURA);
                        })
                        ->letfJoin('detalle_gasto as dga', function ($join) use ($data, $apertura) {
                            $join->on('pcu.COD_CUENTA', '=', 'dga.COD_CUENTA')
                                ->on('dga.COD_APERTURA', '=', $apertura->COD_APERTURA)
                                ->on('dga.TIPO_PAGO', '=', $data->tipo_sg)
                                ->on('dga.FECHA', '=', $data->fecha_sg)->on('dga.ACTIVO', '=', 1);
                        })
                        ->where('zdg.ACTIVO', '=', '1')
                        ->select('zdg.cod_dsg', 'dga.COD_DETALLE', 'dga.COD_CUENTA', 'dga.COD_APERTURA')
                        ->first();
                    return $this->responseOk($detalle_gasto);

                    $gasto_presupuesto = GastoPresupuesto::updateOrCreate(
                        [
                            'FECHA_GASTO' => $detalle_gasto->FECHA,
                            'COD_CUENTA' => $detalle_gasto->COD_CUENTA,
                            'COD_APERTURA' => $detalle_gasto->COD_APERTURA,
                        ],
                        [
                            'MONTO_GASTO' => $detalle_gasto->MONTO,
                            'DESCRIPCION' => $detalle_gasto->DESCRIPCION,
                        ]
                    );
                } else if ($request->estado == "ANU") {
                    $data->ACTIVO = 0;
                }
                $data->estado = $request->estado;
                $data->fecha_revision = Carbon::now()->toDateTimeString();
                $data->revisado_por_usuario = $this->userId();
                $data->save();


                DB::commit();
                $mensaje = ($data->estado == "INI" ? "Inicial"
                    : ($data->estado == "APR" ? "Aprobado"
                        : ($data->estado == "ANU" ? "Anulado" : "Rechazado")));
                return $this->responseOk($data->cod_sg, $mensaje . " Exitosamente");
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
        } catch (Exception $ex) {
            return $this->responseEx($ex);
        }
    }
    public function getDataForPdfByID($cod_sg)
    {
        $result = DB::table("z_solicitud_gasto as zsg")
            ->join('usuario as usu', 'zsg.cod_usuario', '=', 'usu.COD_USUARIO')
            ->join('z_banco_cuenta as bc', 'zsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as nombre_usuario,
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_origen,
                zsg.cod_sg,zsg.tipo_sg,zsg.fecha_sg,
                zsg.fecha_registro,zsg.cod_usuario,
                concat("C-",LPAD(zsg.nro_sg,5,0)) as nroCorrelativo,zsg.nro_sg,
                zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.vc,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
                zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO,
                b.sigla as banco_sigla, b.nombre as banco_nombre, bc.nro_cuenta as banco_nro_cuenta, bc.moneda as banco_moneda
            ')
            ->where('zsg.cod_sg', '=', $cod_sg)->first();
        if ($result == null) $this->responseVal("El Registro no Existe.");

        $detalle = DB::table('z_detalle_solicitud_gasto as dsg')
            ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable,    
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_destino,
                pp.nombre_completo as nombre_proveedor,
                dsg.cod_sg, dsg.cod_proveedor, dsg.cod_cc, dsg.detalle, dsg.importe_bs, dsg.importe_usd, dsg.cod_bc, dsg.ACTIVO,
                dsg.BANDERA AS bandera

            ')
            ->where('dsg.ACTIVO', '=', 1)
            ->where("dsg.cod_sg", '=', $cod_sg)->get();
        // $pdf = \PDF::loadView('pdf.imprimirSGContado',['data'=>$result, 'detalle'=>$detalle]);
        return \PDF::loadView('pdf.imprimirSGContado', ['data' => $result, 'detalle' => $detalle])->stream('test.pdf');
        // return  $pdf->download('prueba.pdf');
    }

    //Aprobacion de gastoa
    public function getDataforApproved(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        if ($request->tipo_sg == "ALL") {
            $result = DB::table("z_solicitud_gasto as zsg")
            ->join('usuario as u', 'zsg.cod_usuario', '=', 'u.COD_USUARIO')
            ->join('z_detalle_solicitud_gasto as zdsg', 'zsg.cod_sg', '=', 'zdsg.cod_sg')
            ->selectRaw("
                concat(u.NOMBRE, ' ', u.AP_PATERNO, ' ', u.AP_MATERNO) as registrado_por,
                zsg.cod_sg,
                zsg.tipo_sg,
                zsg.fecha_sg,
                zsg.fecha_registro,
                zsg.cod_usuario,
                concat('C-', LPAD(zsg.nro_sg, 5, 0)) as nroCorrelativo,
                zsg.nro_sg,
                zsg.gestion,
                zsg.tipo_pago,
                zsg.tipo_gasto,
                zsg.vc,
                zsg.cod_bc,
                zsg.total_bs,
                zsg.total_usd,
                zsg.fecha_pago,
                zsg.glosa,
                zsg.estado,
                zsg.revisado_por_usuario,
                zsg.fecha_revision,
                zsg.ACTIVO,
                GROUP_CONCAT(zdsg.BANDERA) as BANDERA
            ")
            ->whereRaw("
                zsg.estado = 'INI'
                and zsg.ACTIVO=1
                and zdsg.ACTIVO=1
                and zsg.tipo_gasto like '%$request->tipo_gasto%'
                and (zsg.fecha_sg >= '$request->fechaInicio' and zsg.fecha_sg <= '$request->fechaFin')
                and (concat('C-', LPAD(zsg.nro_sg, 5, 0)) like '%$request->buscar%' or zsg.glosa like '%$request->buscar%')
            ")
            ->groupBy('zsg.cod_sg')
            ->paginate(10);
        } else {
          $result = DB::table("z_solicitud_gasto as zsg")
    ->join('usuario as u', 'zsg.cod_usuario', '=', 'u.COD_USUARIO')
    ->join('z_detalle_solicitud_gasto as zdsg', 'zsg.cod_sg', '=', 'zdsg.cod_sg')
    ->selectRaw("
        concat(u.NOMBRE, ' ', u.AP_PATERNO, ' ', u.AP_MATERNO) as registrado_por,
        zsg.cod_sg,
        zsg.tipo_sg,
        zsg.fecha_sg,
        zsg.fecha_registro,
        zsg.cod_usuario,
        concat('C-', LPAD(zsg.nro_sg, 5, 0)) as nroCorrelativo,
        zsg.nro_sg,
        zsg.gestion,
        zsg.tipo_pago,
        zsg.tipo_gasto,
        zsg.vc,
        zsg.cod_bc,
        zsg.total_bs,
        zsg.total_usd,
        zsg.fecha_pago,
        zsg.glosa,
        zsg.estado,
        zsg.revisado_por_usuario,
        zsg.fecha_revision,
        zsg.ACTIVO,
        GROUP_CONCAT(zdsg.BANDERA) as BANDERA
    ")
    ->whereRaw("
        zsg.tipo_sg = '$request->tipo_sg'
        and zsg.estado = 'INI'
        and zsg.ACTIVO=1
        and zdsg.ACTIVO=1
        and zsg.tipo_gasto like '%$request->tipo_gasto%'
        and (zsg.fecha_sg >= '$request->fechaInicio' and zsg.fecha_sg <= '$request->fechaFin')
        and (concat('C-', LPAD(zsg.nro_sg, 5, 0)) like '%$request->buscar%' or zsg.glosa like '%$request->buscar%')
    ")
    ->groupBy('zsg.cod_sg')
    ->paginate(10);
        }
        return $this->responseOk($result);
    }
    public function ApprovedRejectRegister(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        try {
            DB::beginTransaction();
            $data = Z_Solicitud_Gasto::findOrFail($request->cod_sg);
            if ($data == null) throw new ExceptionValidate("El Registro no Existe");

            $detalle = Z_Detalle_Solicitud_Gasto::where("cod_sg", '=', $data->cod_sg)->where("ACTIVO", '=', 1)->get();
            $tipo_pago = ($data->tipo_sg == "CONTADO" ? 1 : 2);
            $tipo_gasto = $data->tipo_gasto;
            $vc = $data->vc; // Asegúrate de que $tipo_gasto esté definido
            if ($request->estado == "APR") {
                if ($data->estado != "INI") {
                    throw new ExceptionValidate("La Solicitud no se encuentra en estado Inicial::Solicitud=>" . $data->cod_sg);
                }
                $apertura = $this->validarApertura($data->fecha_sg);
                if ($apertura == null) {
                    throw new ExceptionValidate("Se Tiene que Aperturar Presupuesto para el Mes y Año que Indica la Fecha de Solicitud: $data->fecha_sg");
                }
                // foreach ($detalle as $item) {
                //     $presupuestoCuenta = $this->validarAperturaPresupuestoCuenta($apertura->COD_APERTURA, $item);
                //     if ($presupuestoCuenta == null) {
                //         throw new ExceptionValidate("Presupuesto sin Saldo o Invalido Para la Cuenta: $item->cod_cc");
                //     }
                // }
                foreach ($detalle as $item) {
                    $detalle_gasto = $this->insertarDetalleGasto($apertura->COD_APERTURA, $item, $tipo_pago, $tipo_gasto, $vc,  $data->fecha_sg);
                    $detalle_gasto = DetalleGasto::create($detalle_gasto);
                    $detalle_gasto->save();


                    $gasto_presupuesto = DB::table('gasto_presupuesto')
                        ->Where('FECHA_GASTO', '=', $data->fecha_sg)
                        ->Where('COD_CUENTA', '=', $item->cod_cc)
                        ->Where('COD_APERTURA', '=', $apertura->COD_APERTURA)
                        ->select('COD_APERTURA')
                        ->first();
                    if ($gasto_presupuesto == null) {
                        GastoPresupuesto::insert([
                            'FECHA_GASTO' => $data->fecha_sg,
                            'COD_APERTURA' => $apertura->COD_APERTURA,
                            'COD_CUENTA' => $item->cod_cc,
                            'MONTO_GASTO' => $detalle_gasto->MONTO,
                            'DESCRIPCION' => $item->detalle,
                        ]);
                    } else {
                        $gasto_presupuesto = DB::update("
                            UPDATE gasto_presupuesto SET MONTO_GASTO = MONTO_GASTO + $detalle_gasto->MONTO 
                            where FECHA_GASTO = '$data->fecha_sg' and COD_APERTURA = $apertura->COD_APERTURA and COD_CUENTA = $item->cod_cc
                        ");
                    }
                }
            }
            $data->estado = $request->estado;
            $data->fecha_revision = Carbon::now()->toDateTimeString();
            $data->revisado_por_usuario = $this->userId();
            $data->save();
            DB::commit();
            $this->LogTxn("z_solicitud_gasto", $data->cod_sg, ["data" => $data, "detalle" => null], $data->estado);
            $mensaje = ($data->estado == "INI" ? "Inicial"
                : ($data->estado == "APR" ? "Aprobado"
                    : ($data->estado == "REC" ? "Rechazado" : "Rechazado")));
            return $this->responseOk($data->cod_sg, $mensaje . " Exitosamente");
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
    public function validarApertura($fecha)
    {
        $result = DB::table('apertura as a')->WhereRaw("
            a.MES = MONTH('$fecha') and
            a.GESTION = YEAR('$fecha') and
            a.ACTIVO = 1
        ")->first();
        return $result;
    }
    public function validarAperturaPresupuestoCuenta($cod_apertura, $obj)
    {
        $result = DB::table('presupuesto_cuenta as pc')->WhereRaw("
            pc.COD_APERTURA = $cod_apertura and
            pc.COD_CUENTA = $obj->cod_cc and
            pc.MONTO_PRESUPUESTO > $obj->importe_bs and
            pc.ACTIVO = 1
        ")->first();
        return $result;
    }
    public function insertarDetalleGasto($cod_apertura, $obj, $tipo_pago,  $tipo_gasto, $vc, $fecha_sg, $tipo_cambio = 6.96)
    {
        $monto_bs = 0;
        $monto_usd = 0;
        if ($obj->importe_usd > 0) {
            $monto_bs = $obj->importe_usd * $tipo_cambio;
            $monto_usd = $obj->importe_usd;
        } else {
            $monto_bs = $obj->importe_bs;
            $monto_usd = 0;
        }
        return [
            'DESCRIPCION' => $obj->detalle,
            'NRO_FACTURA' => '',
            'NIT' => '',
            'NRO_RECIBO' => '',
            'MONTO' => $monto_bs,
            'MONTO_USD' => $monto_usd,
            'COD_CUENTA' => $obj->cod_cc,
            'COD_APERTURA' => $cod_apertura,
            'FECHA' => $fecha_sg,
            'TIPO_PAGO' => $tipo_pago,
            'CATEGORIA' => $tipo_gasto,
            'VC' => $vc,
            'ACTIVO' => 1,
            'COD_SG' => $obj->cod_sg
        ];
    }
}
