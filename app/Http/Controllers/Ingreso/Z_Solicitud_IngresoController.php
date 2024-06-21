<?php

namespace App\Http\Controllers\Ingreso;

use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Detalle_Solicitud_Ingresos;
use App\Model\Z_Solicitud_Ingresos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use App\Model\Apertura;
use App\Model\GastoPresupuesto;
use App\Model\DetalleGasto;
use Exception;

class Z_Solicitud_IngresoController extends ApiController
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        if ($request->estado == "ALL") {
            $result = DB::table("z_solicitud_ingreso as zsg")
                ->selectRaw('zsg.cod_si,zsg.cod_tsi,zsg.fecha_si,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_si,5,0)) as nroCorrelativo,zsg.nro_si,
            zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
            zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO')
                ->whereRaw('
                zsg.cod_tsi="' . $request->cod_tsi . '"
                and zsg.tipo_gasto like "%' . $request->tipo_gasto . '%"
                and (zsg.fecha_si>="' . $request->fechaInicio . '" and zsg.fecha_si<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_si,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->paginate(1000);
        } else {
            $result = DB::table("z_solicitud_ingreso as zsg")
                ->selectRaw('zsg.cod_si,zsg.cod_tsi,zsg.fecha_si,
            zsg.fecha_registro,zsg.cod_usuario,
            concat("C-",LPAD(zsg.nro_si,5,0)) as nroCorrelativo,zsg.nro_si,
            zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.tipo_gasto,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
            zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO')
                ->whereRaw('
                zsg.cod_tsi="' . $request->cod_tsi . '"
                and zsg.tipo_gasto like "%' . $request->tipo_gasto . '%"
                and zsg.estado="' . $request->estado . '"
                and (zsg.fecha_si>="' . $request->fechaInicio . '" and zsg.fecha_si<="' . $request->fechaFin . '")
                and (concat("C-",LPAD(zsg.nro_si,5,0)) like "%' . $request->buscar . '%" or zsg.glosa like "%' . $request->buscar . '%")')
                ->paginate(1000);
        }
        return $this->responseOk($result);
    }
    public function get($cod_si)
    {
        $result = DB::table("z_solicitud_ingreso as zsg")
            ->join('usuario as usu', 'zsg.cod_usuario', '=', 'usu.COD_USUARIO')
            ->join('z_banco_cuenta as bc', 'zsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as nombre_usuario,
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_origen,
                zsg.cod_si,zsg.cod_tsi,zsg.fecha_si,
                zsg.fecha_registro,zsg.cod_usuario,
                concat("C-",LPAD(zsg.nro_si,5,0)) as nroCorrelativo,zsg.nro_si,
                zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
                zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO,
                b.sigla as banco_sigla, b.nombre as banco_nombre, bc.nro_cuenta as banco_nro_cuenta, bc.moneda as banco_moneda
            ')
            ->where('zsg.cod_si', '=', $cod_si)->first();
        if ($result == null) $this->responseVal("El Registro no Existe.");

        $detalle = DB::table('z_detalle_solicitud_ingreso as dsg')
            ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable,    
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_destino,
                pp.nombre_completo as nombre_proveedor,
                dsg.cod_si, dsg.cod_proveedor, dsg.cod_cc, dsg.detalle, dsg.importe_bs, dsg.importe_usd, dsg.cod_bc, dsg.ACTIVO,
                b.sigla as banco_sigla, bc.nro_cuenta as banco_nroCta, bc.moneda as banco_moneda

            ')
            ->where('dsg.ACTIVO', '=', 1)
            ->where("dsg.cod_si", '=', $cod_si)->get();

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
                    'cod_tsi' => 'required',
                    'fecha_si' => 'required',
                    // 'cod_usuario' => Rule::requiredIf($this->userValidate()),
                    // 'nro_si' => 'required',
                    // 'gestion' => 'required',
                    'tipo_pago' => 'required',
                    'tipo_gasto' => 'required',
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
                    'cod_tsi' => 'tipo',
                    'fecha_si' => 'fecha solicitud de gasto',
                    'cod_usuario' => 'usuario',
                    'nro_si' => 'correlativo',
                    'gestion' => 'gestion',
                    'tipo_pago' => 'tipo de pago',
                    'tipo_gasto' => 'tipo de gasto',
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
            $ini = date("Y-m-01", strtotime($obj["fecha_si"]));
            $fin = date("Y-m-t", strtotime($obj["fecha_si"]));
            $obj["nro_si"] = DB::table('z_solicitud_ingreso as zsg')
                ->whereBetween('zsg.fecha_si', [$ini, $fin])
                ->max('nro_si') + 1;
            $obj["fecha_registro"] = Carbon::now()->toDateTimeString();
            $obj["fecha_revision"] = null;
            $obj['revisado_por_usuario'] = null;
            $obj['gestion'] = date("m-Y");
            $obj["estado"] = "INI";
            $obj["ACTIVO"] = 1;
            $obj["cod_usuario"] = $this->userId();
            // $obj["cod_usuario"] = 2;
            $data = Z_Solicitud_Ingresos::create($obj);
            $data->save();
            $detalleLog = array();
            foreach ($request->detalle as $ep => $det) {
                if (((float)$det["importe_bs"] + (float)$det["importe_usd"]) == (float) 0) {
                    throw new ExceptionValidate('Ingresar Monto en Bs o $us en el detalle');
                }
                $det["cod_si"] = $data->cod_si;
                $det["ACTIVO"] = 1;
                $detalle = Z_Detalle_Solicitud_Ingresos::create($det);
                $detalle->save();
                array_push($detalleLog, $detalle);
            }
            DB::commit();
            $this->LogCreate("z_solicitud_ingreso", $data->cod_si, ["data" => $data, "detalle" => $detalleLog]);
            return $this->responseOk($data->cod_si, "Guardado Exitosamente");
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
                    'cod_si' => 'required',
                    'fecha_si' => 'required',
                    'glosa' => 'required',
                    'tipo_gasto' => 'required',
                ],
                [],
                [
                    'cod_si' => 'codigo solicitud de gasto',
                    'fecha_si' => 'fecha solicitud de gasto',
                    'tipo_gasto' => 'Tipo gasto requerido',
                    'glosa' => 'glosa',
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

            // $data = Z_Solicitud_Ingresos::findOrFail($request->data["cod_si"]);
            // $this->responseOk($request->data);
            // $cod_si=$request->data["cod_si"];
            $data = Z_Solicitud_Ingresos::findOrFail($request->data["cod_si"]);
            if ($data == null) throw new ExceptionValidate("La Solicitud no Existe");
            $data->fecha_si = $request->data["fecha_si"];
            $data->glosa = $request->data["glosa"];
            $data->total_bs = $request->data["total_bs"];
            $data->total_usd = $request->data["total_usd"];
            $data->tipo_gasto = $request->data["tipo_gasto"];
            $data->save();
            Z_Detalle_Solicitud_Ingresos::where('cod_si', '=', $data->cod_si)->update(['ACTIVO' => '0']);
            // DB::update('update Z_Detalle_Solicitud_Ingresos set ACTIVO = 0 where cod_si = ?', [$data->cod_si]);
            $detalleLog = array();
            foreach ($request->detalle as $ep => $det) {
                $detalle = Z_Detalle_Solicitud_Ingresos::create(
                    [
                        'cod_si' => $data->cod_si,
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
            $this->LogUpdate("z_solicitud_ingreso", $data->cod_si, ["data" => $data, "detalle" => $detalleLog]);

            return $this->responseOk($data->cod_si, "Actualizado Exitosamente");
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
    public function delete($cod_si)
    {
        try {
            if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
            DB::beginTransaction();
            try {
                $data = Z_Solicitud_Ingresos::findOrFail($cod_si);
                $data->estado = "ANU";
                $data->ACTIVO = 0;
                $data->fecha_revision = Carbon::now()->toDateTimeString();
                $data->revisado_por_usuario = $this->userId();
                $data->save();
                DB::commit();
                $this->LogTxn("z_solicitud_ingreso", $data->cod_si, ["data" => $data, "detalle" => null], "ANU");
                return $this->responseOk($data->cod_si, "Anulado Exitosamente");
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
                $data = Z_Solicitud_Ingresos::findOrFail($request->cod_si);
                $tipo_pago = ($data->cod_tsi == 1 ? 1 : 2);
                if ($request->estado == "APR") {
                    if ($data->estado != "INI") {
                        throw new ExceptionValidate("La Solicitud no se encuentra en estado Iniciado::Solicitud=>" . $data->cod_si);
                    }
                    $apertura = DB::table('apertura as ape')
                        ->WhereRaw('MONTH("' . $data->fecha_si . '")=ape.MES 
                        and YEAR("' . $data->fecha_si . '")=ape.GESTION
                    and ape.ACTIVO=1')
                        ->select('ape.COD_APERTURA')
                        ->first();
                    // return $this->responseOk($detalle_gasto);

                    if ($apertura == null) {
                        throw new ExceptionValidate("primero se tiene que aperturar presupuesto
                        para el mes y año que indica la fecha de
                        la solicitud gasto=>" . $data->cod_si);
                    }
                    // $queryPresupuestCuenta = DB::table('presupuesto_cuenta as pcu')
                    //     ->Where('pcu.ACTIVO', '=', '1')
                    //     ->where('pcu.COD_APERTURA', '=', $apertura->COD_APERTURA)
                    //     ->select('pcu.COD_APERTURA', 'pcu.COD_CUENTA', 'pcu.MONTO_PRESUPUESTO');

                    $detalle_gasto = DB::table('z_detalle_solicitud_ingreso as zdg')
                        ->letfJoin('presupuesto_cuenta as pcu', function ($join) use ($apertura) {
                            $join->on('zdg.COD_CUENTA', '=', 'pcu.COD_CUENTA')
                                ->on('pcu.COD_APERTURA', '=', $apertura->COD_APERTURA);
                        })
                        ->letfJoin('detalle_gasto as dga', function ($join) use ($data, $apertura) {
                            $join->on('pcu.COD_CUENTA', '=', 'dga.COD_CUENTA')
                                ->on('dga.COD_APERTURA', '=', $apertura->COD_APERTURA)
                                ->on('dga.TIPO_PAGO', '=', $data->cod_tsi)
                                ->on('dga.FECHA', '=', $data->fecha_si)->on('dga.ACTIVO', '=', 1);
                        })
                        ->where('zdg.ACTIVO', '=', '1')
                        ->select('zdg.cod_dsi', 'dga.COD_DETALLE', 'dga.COD_CUENTA', 'dga.COD_APERTURA')
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
                return $this->responseOk($data->cod_si, $mensaje . " Exitosamente");
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
    public function getDataForPdfByID2()
    {
        $ingresos = DB::table('z_detalle_solicitud_ingreso as dsg')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('nro_cuenta as a', 'cc.NRO_CUENTA', '=', 'a.CODNUM')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable,    
                concat(a.CODNUM, "-",a.DESCRIPCION) as titulo_cuenta,
                dsg.importe_bs, dsg.importe_usd')
            ->where('dsg.ACTIVO', '=', 1);

        $gastos = DB::table('z_detalle_solicitud_gasto as dsg')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('nro_cuenta as a', 'cc.NRO_CUENTA', '=', 'a.CODNUM')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable, 
                concat(a.CODNUM, "-",a.DESCRIPCION) as titulo_cuenta,  
                dsg.importe_bs,dsg.importe_usd')
            ->where('dsg.ACTIVO', '=', 1);
        // return response()->json(['message' => 'Test']);
        // return \PDF::loadView('pdf.imprimirSIIngreso')->stream('test.pdf');
        return \PDF::loadView('pdf.imprimirSIIngreso', ['data' => $ingresos->get(), 'detalle' => $gastos->get()])->stream('test.pdf');
        // Solo para pruebas, renderizar la vista directamente sin generar un PDF
        // return view('pdf.imprimirSIIngreso', ['data' => $ingresos->get(), 'detalle' => $gastos->get()]);

    }
    public function getDataForPdfByID($cod_si)
    {
        $result = DB::table("z_solicitud_ingreso as zsg")
            ->join('usuario as usu', 'zsg.cod_usuario', '=', 'usu.COD_USUARIO')
            ->join('z_banco_cuenta as bc', 'zsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(usu.NOMBRE," ",usu.AP_PATERNO," ",usu.AP_MATERNO) as nombre_usuario,
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_origen,
                zsg.cod_si,zsg.cod_tsi,zsg.fecha_si,
                zsg.fecha_registro,zsg.cod_usuario,
                zsg.cod_tsi as tipo_si,
                concat("C-",LPAD(zsg.nro_si,5,0)) as nroCorrelativo,zsg.nro_si,
                zsg.gestion,zsg.tipo_pago,zsg.tipo_gasto,zsg.cod_bc,zsg.total_bs,zsg.total_usd,zsg.fecha_pago,
                zsg.glosa,zsg.estado,zsg.revisado_por_usuario,zsg.fecha_revision,zsg.ACTIVO,
                b.sigla as banco_sigla, b.nombre as banco_nombre, bc.nro_cuenta as banco_nro_cuenta, bc.moneda as banco_moneda
            ')
            ->where('zsg.cod_si', '=', $cod_si)->first();
        if ($result == null) $this->responseVal("El Registro no Existe.");

        $detalle = DB::table('z_detalle_solicitud_ingreso as dsg')
            ->join('z_prov_pers as pp', 'dsg.cod_proveedor', '=', 'pp.id')
            ->join('cuenta as cc', 'dsg.cod_cc', '=', 'cc.COD_CUENTA')
            ->join('fin_fondo as ff', 'cc.COD_FONDO', '=', 'ff.COD_FONDO')
            ->join('z_banco_cuenta as bc', 'dsg.cod_bc', '=', 'bc.cod_bc')
            ->join('z_banco as b', 'bc.cod_b', '=', 'b.cod_b')
            ->selectRaw('
                concat(ff.DESCRIPCION, "-",cc.NRO_CUENTA,"-",cc.DESCRIPCION) as cuenta_contable,    
                concat(b.sigla, "-",bc.nro_cuenta,"-",bc.moneda) as banco_destino,
                pp.nombre_completo as nombre_proveedor,
                dsg.cod_si, dsg.cod_proveedor, dsg.cod_cc, dsg.detalle, dsg.importe_bs, dsg.importe_usd, dsg.cod_bc, dsg.ACTIVO

            ')
            ->where('dsg.ACTIVO', '=', 1)
            ->where("dsg.cod_si", '=', $cod_si)->get();
        // $pdf = \PDF::loadView('pdf.imprimirSGContado',['data'=>$result, 'detalle'=>$detalle]);
        return \PDF::loadView('pdf.imprimirSIIngreso', ['data' => $result, 'detalle' => $detalle])->stream('test.pdf');
        // return  $pdf->download('prueba.pdf');
    }
    public function getDataforApproved(Request $request){
        if (!$request->ajax()) return redirect('/login');
        if ($request->cod_tsi == "ALL") {
            $result = DB::table("z_solicitud_ingreso as zsg")
    ->join('usuario as u', 'zsg.cod_usuario', '=', 'u.COD_USUARIO')
    ->join('z_detalle_solicitud_ingreso as zdsgi', 'zsg.cod_si', '=', 'zdsgi.cod_si')
    ->join('cuenta', 'zdsgi.cod_cc', '=', 'cuenta.COD_CUENTA')
    ->join('z_prov_pers', 'z_prov_pers.id', '=', 'zdsgi.cod_proveedor')
    ->selectRaw("
        concat(u.NOMBRE, ' ', u.AP_PATERNO, ' ', u.AP_MATERNO) as registrado_por,
        zsg.cod_si,
        zsg.cod_tsi,
        zsg.fecha_si,
        zsg.fecha_registro,
        zsg.cod_usuario,
        concat('C-', LPAD(zsg.nro_si, 5, 0)) as nroCorrelativo,
        zsg.nro_si,
        zsg.gestion,
        zsg.tipo_pago,
        zsg.tipo_gasto,
        zsg.cod_bc,
        zsg.total_bs,
        zsg.total_usd,
        zsg.fecha_pago,
        zsg.glosa,
        zsg.estado,
        zsg.revisado_por_usuario,
        zsg.fecha_revision,
        zsg.ACTIVO,
        GROUP_CONCAT(zdsgi.BANDERA) as BANDERA ,
        GROUP_CONCAT(zdsgi.cod_cc) as cod_cc,
        GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
        GROUP_CONCAT(zdsgi.cod_proveedor) as cod_proveedor,
        GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo
    ")
    ->whereRaw("
            
        zsg.estado='INI' and zsg.tipo_gasto like '%$request->tipo_gasto%'
        and  zsg.ACTIVO=1
        and  zdsgi.ACTIVO=1
        and (zsg.fecha_si >= '$request->fechaInicio' and zsg.fecha_si <= '$request->fechaFin')
        and (concat('C-', LPAD(zsg.nro_si, 5, 0)) like '%$request->buscar%' or zsg.glosa like '%$request->buscar%')
    ")
    ->groupBy('zsg.cod_si')
    ->paginate(500);
        } else {
            $result = DB::table("z_solicitud_ingreso as zsg")
            ->join('usuario as u', 'zsg.cod_usuario', '=', 'u.COD_USUARIO')
            ->join('z_detalle_solicitud_ingreso as zdsgi', 'zsg.cod_si', '=', 'zdsgi.cod_si')
            ->join('cuenta', 'zdsgi.cod_cc', '=', 'cuenta.COD_CUENTA')
            ->join('z_prov_pers', 'z_prov_pers.id', '=', 'zdsgi.cod_proveedor')
            ->selectRaw("
                concat(u.NOMBRE, ' ', u.AP_PATERNO, ' ', u.AP_MATERNO) as registrado_por,
                zsg.cod_si,
                zsg.cod_tsi,
                zsg.fecha_si,
                zsg.fecha_registro,
                zsg.cod_usuario,
                concat('C-', LPAD(zsg.nro_si, 5, 0)) as nroCorrelativo,
                zsg.nro_si,
                zsg.gestion,
                zsg.tipo_pago,
                zsg.tipo_gasto,
                zsg.cod_bc,
                zsg.total_bs,
                zsg.total_usd,
                zsg.fecha_pago,
                zsg.glosa,
                zsg.estado,
                zsg.revisado_por_usuario,
                zsg.fecha_revision,
                zsg.ACTIVO,
                GROUP_CONCAT(zdsgi.BANDERA) as BANDERA ,
                GROUP_CONCAT(zdsgi.cod_cc) as cod_cc,
                GROUP_CONCAT(cuenta.DESCRIPCION) as descripcion,
                GROUP_CONCAT(zdsgi.cod_proveedor) as cod_proveedor,
                GROUP_CONCAT(z_prov_pers.nombre_completo) as nombre_completo
            ")
            ->whereRaw("
                zsg.cod_tsi = '$request->cod_tsi'
                and  zsg.ACTIVO=1
                and  zdsgi.ACTIVO=1
                and zsg.estado = 'INI' 
                and zsg.tipo_gasto like '%$request->tipo_gasto%'
                and (zsg.fecha_si >= '$request->fechaInicio' and zsg.fecha_si <= '$request->fechaFin')
                and (concat('C-', LPAD(zsg.nro_si, 5, 0)) like '%$request->buscar%' or zsg.glosa like '%$request->buscar%')
            ")
            ->groupBy('zsg.cod_si')
            ->paginate(500);
        }
        return $this->responseOk($result);
    }
    public function ApprovedRejectRegister(Request $request)
    {
        if (!$request->ajax()) return redirect('/login');
        try {
            DB::beginTransaction();
            $data = Z_Solicitud_Ingresos::findOrFail($request->cod_si);
            if ($data == null) throw new ExceptionValidate("El Registro no Existe");

            $detalle = Z_Detalle_Solicitud_Ingresos::where("cod_si", '=', $data->cod_si)->where("ACTIVO", '=', 1)->get();
            $tipo_pago = ($data->cod_tsi == 1 ? 1 : 2);
            $tipo_gasto = $data->tipo_gasto; // Asegúrate de que $tipo_gasto esté definido
            if ($request->estado == "APR") {
                if ($data->estado != "INI") {
                    throw new ExceptionValidate("La Solicitud no se encuentra en estado Inicial::Solicitud=>" . $data->cod_si);
                }
                // $apertura = $this->validarApertura($data->fecha_si);
                // if ($apertura == null) {
                //     throw new ExceptionValidate("Se Tiene que Aperturar Presupuesto para el Mes y Año que Indica la Fecha de Solicitud: $data->fecha_si");
                // }
                // foreach ($detalle as $item) {
                //     $presupuestoCuenta = $this->validarAperturaPresupuestoCuenta($apertura->COD_APERTURA, $item);
                //     if ($presupuestoCuenta == null) {
                //         throw new ExceptionValidate("Presupuesto sin Saldo o Invalido Para la Cuenta: $item->cod_cc");
                //     }
                // }
                // foreach ($detalle as $item) {
                //     $detalle_gasto = $this->insertarDetalleGasto($apertura->COD_APERTURA, $item, $tipo_pago, $tipo_gasto, $data->fecha_si);
                //     $detalle_gasto = DetalleGasto::create($detalle_gasto);
                //     $detalle_gasto->save();


                //     $gasto_presupuesto = DB::table('gasto_presupuesto')
                //     ->Where('FECHA_GASTO', '=', $data->fecha_si)
                //         ->Where('COD_CUENTA', '=', $item->cod_cc)
                //         ->Where('COD_APERTURA', '=', $apertura->COD_APERTURA)
                //         ->select('COD_APERTURA')
                //         ->first();
                //     if ($gasto_presupuesto == null) {
                //          GastoPresupuesto::insert([
                //             'FECHA_GASTO' => $data->fecha_si,
                //             'COD_APERTURA' => $apertura->COD_APERTURA,
                //             'COD_CUENTA' => $item->cod_cc,
                //             'MONTO_GASTO' => $detalle_gasto->MONTO,
                //             'DESCRIPCION' => $item->detalle,
                //         ]);

                //     } else {
                //         $gasto_presupuesto = DB::update("
                //             UPDATE gasto_presupuesto SET MONTO_GASTO = MONTO_GASTO + $detalle_gasto->MONTO 
                //             where FECHA_GASTO = '$data->fecha_si' and COD_APERTURA = $apertura->COD_APERTURA and COD_CUENTA = $item->cod_cc
                //         ");
                //     }                      
                // }
            }
            $data->estado = $request->estado;
            $data->fecha_revision = Carbon::now()->toDateTimeString();
            $data->revisado_por_usuario = $this->userId();
            $data->save();
            DB::commit();
            $this->LogTxn("z_solicitud_ingreso", $data->cod_si, ["data" => $data, "detalle" => null], $data->estado);
            $mensaje = ($data->estado == "INI" ? "Inicial"
                : ($data->estado == "APR" ? "Aprobado"
                    : ($data->estado == "REC" ? "Rechazado" : "Rechazado")));
            return $this->responseOk($data->cod_si, $mensaje . " Exitosamente");
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
    public function insertarDetalleGasto($cod_apertura, $obj, $tipo_pago,  $tipo_gasto, $fecha_si, $tipo_cambio = 6.96)
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
            'FECHA' => $fecha_si,
            'TIPO_PAGO' => $tipo_pago,
            'CATEGORIA' => $tipo_gasto,
            'ACTIVO' => 1,
            'COD_SG' => $obj->cod_si
        ];
    }
}
