<?php

namespace App\Http\Controllers\Conciliacion;

use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Conciliacion\Z_Conciliacion;
use App\Model\Z_Detalle_Solicitud_Ingresos;
use App\Model\ZControlRecibosOT;
use App\Model\Z_Detalle_Solicitud_Gasto;
use App\Model\Z_MovimientoBancoCuenta;
use App\Model\Ingreso\Z_Tipo_Solicitud_Ingreso;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\NumerosEnLetras;  //llamando la clase 
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Conciliacion\Reportes\GastosExport;
use App\Exports\Conciliacion\Reportes\IngresoExport;
use App\Exports\Conciliacion\Reportes\CobranzaExport;

class ZConciliacionController extends ApiController
{

   

    // listado conciliacion de egresos 
    public function getDataOTForReceipts(Request $request)
    {
        $buscar = $request->buscar;
        $tipoMovimiento = $request->tipoMovimiento;
        $estado = $request->estado;
        $fechaFin = $request->fechaFin;
        $fechaInicio = $request->fechaInicio;


        $gastos = DB::table('z_solicitud_gasto as sg')
            ->join('z_detalle_solicitud_gasto as dsg', 'dsg.cod_sg', '=', 'sg.cod_sg')
            ->join('z_prov_pers as c', 'c.id', '=', 'dsg.cod_proveedor')
            ->join('z_banco_cuenta as d', 'd.cod_bc', '=', 'sg.cod_bc')
            ->join('z_banco as e', 'e.cod_b', '=', 'd.cod_b')
            ->join('cuenta as f', 'f.COD_CUENTA', '=', 'dsg.cod_cc')
            ->join('nro_cuenta as i', 'i.CODNUM', '=', 'f.NRO_CUENTA')
            ->join('z_banco_cuenta as g', 'g.cod_bc', '=', 'dsg.cod_bc')
            ->join('z_banco as h', 'h.cod_b', '=', 'g.cod_b')
            ->select([
                'sg.cod_sg AS id_solicitud',
                'dsg.cod_dsg AS id_detalle_solicitud',
                'sg.fecha_sg AS fecha',
                'sg.tipo_sg AS tipo',
                'sg.nro_sg AS nro',
                DB::raw('CONCAT("G-", LPAD(sg.nro_sg, 4, 0)) as nroCorrelativo'),
                'c.tipo AS tipo_resp',
                'c.nombre_completo AS responsable',
                'f.DESCRIPCION AS cuenta',
                'dsg.detalle AS detalle',
                'dsg.importe_bs AS totalbs',
                'dsg.importe_usd AS usd',
                DB::raw("CONCAT(e.sigla, '-', SUBSTRING(d.nro_cuenta, LENGTH(d.nro_cuenta)-3, 4), '-', d.moneda) AS bancoo"),
                DB::raw("CONCAT(h.sigla, '-', SUBSTRING(g.nro_cuenta, LENGTH(g.nro_cuenta)-3, 4), '-', g.moneda) AS bancod"),
                'sg.cod_bc AS bancoorigen',
                'dsg.cod_bc AS bancodestino',
                'dsg.BANDERA AS bandera',
                'i.DESCRIPCION AS nrocuenta'
            ])
            ->where('sg.ACTIVO', 1)
            ->where('dsg.ACTIVO', 1)
            ->whereIn('dsg.BANDERA', [0, 2])
            ->whereIn('sg.estado', ['INI', 'APR']);


        // Consulta de traspasos
        $traspaso = DB::table('z_movimiento_banco_cuenta as mbc')
            ->join('z_banco_cuenta as bco', 'mbc.cod_bc_origen', '=', 'bco.cod_bc')
            ->join('z_banco as bo', 'bco.cod_b', '=', 'bo.cod_b')
            ->join('z_banco_cuenta as bcd', 'mbc.cod_bc_destino', '=', 'bcd.cod_bc')
            ->join('z_banco as bd', 'bcd.cod_b', '=', 'bd.cod_b')
            ->join('usuario as usu', 'mbc.modificado_por', '=', 'usu.COD_USUARIO')
            ->select([
                'mbc.cod_mbc AS id_solicitud',
                'mbc.cod_mbc AS id_detalle_solicitud',
                'mbc.fecha AS fecha',
                // 'mbc.tipo AS tipo',
                DB::raw("'4' AS tipo"),
                'mbc.cod_mbc AS nro',
                DB::raw('CONCAT("T-", LPAD(mbc.cod_mbc, 4, 0)) as nroCorrelativo'),
                DB::raw("'4' AS tipo_resp"),
                // 'mbc.tipo_movimiento AS tipo_resp',
                // 'mbc.tipo_movimiento AS tipo_resp',
                DB::raw("CONCAT(usu.NOMBRE,' ', usu.AP_PATERNO,' ', usu.AP_MATERNO) AS responsable"),
                DB::raw("'MANEJO INTEGRADO DE PLAGAS MIP SRL' AS cuenta"),
                'mbc.glosa AS detalle',
                'mbc.importe_bs AS totalbs',
                'mbc.importe_usd AS usd',
                DB::raw("CONCAT(bo.sigla, '-', SUBSTRING(bco.nro_cuenta, LENGTH(bco.nro_cuenta)-3, 4), '-', bco.moneda) AS bancoo"),
                DB::raw("CONCAT(bd.sigla, '-', SUBSTRING(bcd.nro_cuenta, LENGTH(bcd.nro_cuenta)-3, 4), '-', bcd.moneda) AS bancod"),
                'mbc.cod_bc_origen AS bancoorigen',
                'mbc.cod_bc_destino AS bancodestino',
                'mbc.BANDERAE AS bandera',
                DB::raw("'MANEJO INTEGRADO DE PLAGAS' AS nrocuenta"),

            ])
            ->where('mbc.ACTIVO', '=', '1');

        //  ->whereIn('mbc.BANDERAE', [0, 2]);



        //  $resultadosCombinados = $gastos->union($traspaso);
        $resultadosCombinados = $gastos->union($traspaso)->orderBy('fecha', 'asc');

        $query = DB::query()->fromSub($resultadosCombinados, 'resultadoCombinado')
            ->where('resultadoCombinado.fecha', '>=', $fechaInicio)
            ->where('resultadoCombinado.fecha', '<=', $fechaFin);


        // if ($buscar) {
        //     $query->where(function($q) use ($buscar) {
        //       $q->where('resultadoCombinado.nro', 'like', '%' . $buscar . '%')
        //         ->orWhere('resultadoCombinado.responsable', 'like', '%' . $buscar . '%');
        //     });
        //     }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('resultadoCombinado.nro', 'like', '%' . $buscar . '%')
                    ->orWhere('resultadoCombinado.responsable', 'like', '%' . $buscar . '%')
                    // Repite la lógica de formación de nroCorrelativo para "C-"
                    ->orWhere(DB::raw('CONCAT("G-", LPAD(resultadoCombinado.nro, 4, "0"))'), 'like', '%' . $buscar . '%')
                    // Repite la lógica de formación de nroCorrelativo para "T-"
                    ->orWhere(DB::raw('CONCAT("T-", LPAD(resultadoCombinado.nro, 4, "0"))'), 'like', '%' . $buscar . '%')
                    ->orWhere('resultadoCombinado.detalle', 'like', '%' . $buscar . '%');
            });
        }




        if ($tipoMovimiento) {
            $query->where('resultadoCombinado.tipo', $tipoMovimiento);
        }


        // if ($estado) {
        //      $query->where('resultadoCombinado.bandera', $estado);
        //     }

        $query->where('resultadoCombinado.bandera', '!=', 1);
        $resultadosPaginados = $query->paginate(1000);


        return $this->responseOk($resultadosPaginados);
    }



    public function create(Request $request)
    {
        try {

            $detalles = $request->input('detalles');
            $nota = $request->input('nota');

            foreach ($detalles as $detalle) {
                // Z_Conciliacion::where('id_detalle_solicitud', $detalle['id_detalle_solicitud'])
                // ->update(['ACTIVO' => 0]);
                // Asumiendo que 'ACTIVO' es el campo que marca la entrada como activa o inactiva
                $tipoConciliacion = '';
                if (in_array($detalle['tipo'], ['CONTADO', 'CREDITO'])) {
                    $tipoConciliacion = 'EGRESO';
                } else if ($detalle['tipo'] == '4') {
                    $tipoConciliacion = 'EGRESO';
                }
                $tipoC = '';
                if (in_array($detalle['tipo'], ['CONTADO', 'CREDITO'])) {
                    $tipoC = 'GASTOS';
                } else if ($detalle['tipo'] == '4') {
                    $tipoC = 'TRASPASO';
                }
                $conciliacion = new Z_Conciliacion([
                    'tipo' => $tipoConciliacion,
                    'fecha_conciliacion' => Carbon::now()->toDateTimeString(), // Fecha actual
                    'fecha_banco' => $detalle['fecha_sg'], // Fecha de la solicitud
                    'banco_origen' => $detalle['bancoo'], // Banco origen
                    'banco_destino' => $detalle['bancod'], // Banco destino
                    'monto' => $detalle['totalbs'], // Monto en Bs
                    'id_solicitud' => $detalle['id_solicitud'], // ID de la solicitud
                    'id_detalle_solicitud' => $detalle['id_detalle_solicitud'], // ID del detalle de la solicitud
                    'nota' => $nota, // Nota ingresada por el usuario
                    'ACTIVO' => 1, // Activo por defecto
                    'BANDERA' => $tipoC, // Activo por defecto
                ]);

                // Guardar la nueva conciliación en la base de datos
                $conciliacion->save();

                $banderaValor = 1; // Si nota está vacía, bandera es 1, si no, es 2

                if ($tipoC == 'TRASPASO') {
                    Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERAE' => 1]);
                }
                if ($tipoC == 'GASTOS') {
                    Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                        ->update(['BANDERA' => 1]);
                }
                // elseif ($tipoC == 'COBRANZA') {
                //     ZControlRecibosOT::where('cod_cr', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                //                       ->update(['BANDERA' => 1]);
                // } 
                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => $banderaValor]);

                // Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud'])
                // ->update(['BANDERAE' => $banderaValor]);

                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => 1]);
            }


            return response()->json(['success' => true, 'message' => 'Conciliaciones creadas correctamente.']);
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function exportarExcel(Request $request)
    {
        $export = new GastosExport(
            $request->buscar,
            $request->tipoMovimiento,
            $request->estado,
            $request->fechaFin,
            $request->fechaInicio
        );

        return Excel::download($export, 'gastos.xlsx');
    }
    public function createCIog(Request $request)
    {
        try {

            $detalles = $request->input('detalles');
            $nota = $request->input('nota');

            foreach ($detalles as $detalle) {
                // Z_Conciliacion::where('id_detalle_solicitud', $detalle['id_detalle_solicitud'])
                // ->update(['ACTIVO' => 0]); // Asumiendo que 'ACTIVO' es el campo que marca la entrada como activa o inactiva
                $tipoConciliacion = '';
                if (in_array($detalle['tipo'], ['CONTADO', 'CREDITO'])) {
                    $tipoConciliacion = 'EGRESOB';
                } else if ($detalle['tipo'] == '4') {
                    $tipoConciliacion = 'EGRESOB';
                }
                $tipoC = '';
                if (in_array($detalle['tipo'], ['CONTADO', 'CREDITO'])) {
                    $tipoC = 'GASTOSB';
                } else if ($detalle['tipo'] == '4') {
                    $tipoC = 'TRASPASOB';
                }
                $conciliacion = new Z_Conciliacion([
                    // 'tipo' => 'EGRESO', // Valor por defecto
                    'tipo' => $tipoConciliacion,
                    'fecha_conciliacion' => Carbon::now()->toDateTimeString(), // Fecha actual
                    'fecha_banco' => $detalle['fecha_sg'], // Fecha de la solicitud
                    'banco_origen' => $detalle['bancoo'], // Banco origen
                    'banco_destino' => $detalle['bancod'], // Banco destino
                    'monto' => $detalle['totalbs'], // Monto en Bs
                    'id_solicitud' => $detalle['id_solicitud'], // ID de la solicitud
                    'id_detalle_solicitud' => $detalle['id_detalle_solicitud'], // ID del detalle de la solicitud
                    'nota' => $nota, // Nota ingresada por el usuario
                    'ACTIVO' => 1, // Activo por defecto
                    'BANDERA' => $tipoC, // Activo por defecto
                ]);

                // Guardar la nueva conciliación en la base de datos
                $conciliacion->save();

                $banderaValor = 2;
                if ($tipoC == 'TRASPASOB') {
                    Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERAE' => 2]);
                } elseif ($tipoC == 'GASTOSB') {
                    Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                        ->update(['BANDERA' => 2]);
                }
                //  elseif ($tipoC == 'COBRANZA') {
                //     ZControlRecibosOT::where('cod_cr', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                //                       ->update(['BANDERA' => 2]);
                // }  

                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => $banderaValor]);

                // Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud'])
                // ->update(['BANDERAE' => $banderaValor]);

                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => 1]);
            }


            return response()->json(['success' => true, 'message' => 'Conciliaciones creadas correctamente.']);
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function exportarExcel2(Request $request)
    {
        $export = new IngresoExport(
            $request->buscar,
            $request->tipoMovimiento,
            $request->estado,
            $request->fechaFin,
            $request->fechaInicio
        );

        return Excel::download($export, 'ingresos.xlsx');
    }

    public function exportarExcel3(Request $request)
    {
        $export = new CobranzaExport(
            $request->buscar,
            // $request->tipoMovimiento,
            // $request->estado,
            $request->fechaFin,
            $request->fechaInicio
        );

        return Excel::download($export, 'Cobranza.xlsx');
    }

    //Listado  de Conciliacion  de Ingresos 
    public function getDataOTForReceiptsCI(Request $request)
    {
        $buscar = $request->buscar;
        $fechaInicio = $request->fechaInicio;
        $fechaFin = $request->fechaFin;
        $tipoMovimiento = $request->tipoMovimiento;
        $estado = $request->estado;
    
        // Inicializa la consulta base
        $gastosQuery = DB::table('vi_conciliacion_ingresos as si')
            ->whereIn('si.BANDERA', [0, 2]) // Por revisar observación
            ->whereBetween('si.fecha', [$fechaInicio, $fechaFin])
            ->where(function ($query) use ($buscar) {
                $query->where('si.nro', 'like', '%' . $buscar . '%')
                      ->orWhere('si.responsable', 'like', '%' . $buscar . '%');
            });
    
        // Aplica los filtros según el tipo de movimiento
        if ($tipoMovimiento == 1) {
            $gastosQuery->where('si.tipo', 'OTROS INGRESOS')
            ->whereIn('si.BANDERA', [0, 2]);
        } elseif ($tipoMovimiento == 2) {
            $gastosQuery->where('si.tipo', 'DEVOLUCIONES PERSONAL')
            ->whereIn('si.BANDERA', [0, 2]);
        } elseif ($tipoMovimiento == 3) {
            $gastosQuery->where('si.tipo', 'COBRANZA')
            ->whereIn('si.BANDERA', [0, 2]);
        } elseif ($tipoMovimiento == '4') {
            $gastosQuery->where('si.tipo', 'TRASPASO')
            ->whereIn('si.BANDERA', [0, 2]);
        }
        if ($estado !== null && $estado !== '') {
            $gastosQuery->where('si.BANDERA', $estado);
        }
    
        $gastosQuery->orderBy('si.fecha', 'asc');
        $perPage = 1000; // Número de elementos por página
        $page = $request->input('page', 1); // Página actual
        $gastos = $gastosQuery->paginate($perPage, ['*'], 'page', $page);
    
        // Devolver los resultados paginados
        return response()->json($gastos);
    }
    

    //Reporte de Engres e Ingresos
    public function getDataOTForReceiptsRCI(Request $request)
    {
        $buscar = $request->buscar;
        $fechaInicio = $request->fechaInicio;
        $fechaFin = $request->fechaFin;
        $tipoMovimiento = $request->tipoMovimiento;
        $estado = $request->estado;
        $gastos = collect();
        if ($tipoMovimiento == 1) {
            // Realiza la consulta para INGRESOS
            $gastosQuery = DB::table('z_solicitud_ingreso as si')
                ->join('z_detalle_solicitud_ingreso as dsi', 'dsi.cod_si', '=', 'si.cod_si')
                ->join('z_prov_pers as c', 'c.id', '=', 'dsi.cod_proveedor')
                ->join('z_banco_cuenta as d', 'd.cod_bc', '=', 'si.cod_bc')
                ->join('z_banco as e', 'e.cod_b', '=', 'd.cod_b')
                ->join('cuenta as f', 'f.COD_CUENTA', '=', 'dsi.cod_cc')
                // ->join('usuario as u', 'u.COD_USUARIO', '=', 'si.revisado_por_usuario')
                ->join('z_banco_cuenta as g', 'g.cod_bc', '=', 'dsi.cod_bc')
                ->join('z_banco as h', 'h.cod_b', '=', 'g.cod_b')
                // ->leftjoin('conciliacion_bancaria as i', 'i.id_detalle_solicitud', '=', 'dsi.cod_dsg')
                ->select(
                    'si.cod_si AS id_solicitud',
                    'dsi.cod_dsi AS id_detalle_solicitud',
                    'si.fecha_si AS fecha',
                    'c.tipo AS tipo_resp',
                    'si.cod_tsi AS tipo',
                    'si.nro_si AS nro',
                    'c.nombre_completo AS responsable',
                    'f.DESCRIPCION AS cuenta',
                    'dsi.detalle AS detalle',
                    'dsi.importe_bs AS totalbs',
                    'dsi.importe_usd AS usd',
                    DB::raw("CONCAT(e.sigla, '-', SUBSTRING(d.nro_cuenta, LENGTH(d.nro_cuenta)-3, 4), '-', d.moneda) AS bancoo"),
                    DB::raw("CONCAT(h.sigla, '-', SUBSTRING(g.nro_cuenta, LENGTH(g.nro_cuenta)-3, 4), '-', g.moneda) AS bancod"),
                    'si.cod_bc AS bancoorigen',
                    'dsi.cod_bc AS bancodestino',
                    'dsi.BANDERA AS bandera'
                )
                ->where('si.ACTIVO', 1)
                ->where('si.estado', 'INI')
                ->whereIn('dsi.BANDERA', [0, 2])
                ->whereBetween('si.fecha_si', [$fechaInicio, $fechaFin])
                ->where(function ($query) use ($buscar) {
                    $query->where('si.nro_si', 'like', '%' . $buscar . '%')
                        ->orWhere('c.nombre_completo', 'like', '%' . $buscar . '%');
                });
            if ($estado !== null && $estado !== '') {
                $gastosQuery->where('dsi.BANDERA', $estado);
            }
            $gastos = $gastosQuery->get();
        }
        // if (!empty($tipoMovimiento)) {
        //     $gastosQuery->where('si.cod_tsi', $tipoMovimiento);
        // }
        if ($tipoMovimiento == 2) {
            //CONSULTA PARA GASTOS
            $gastosQuery = DB::table('z_solicitud_gasto as sg')
                ->join('z_detalle_solicitud_gasto as dsg', 'dsg.cod_sg', '=', 'sg.cod_sg')
                ->join('z_prov_pers as c', 'c.id', '=', 'dsg.cod_proveedor')
                ->join('z_banco_cuenta as d', 'd.cod_bc', '=', 'sg.cod_bc')
                ->join('z_banco as e', 'e.cod_b', '=', 'd.cod_b')
                ->join('cuenta as f', 'f.COD_CUENTA', '=', 'dsg.cod_cc')
                // ->leftjoin('usuario as u', 'u.COD_USUARIO', '=', 'sg.revisado_por_usuario')
                ->join('z_banco_cuenta as g', 'g.cod_bc', '=', 'dsg.cod_bc')
                ->join('z_banco as h', 'h.cod_b', '=', 'g.cod_b')
                // ->leftjoin('conciliacion_bancaria as i', 'i.id_detalle_solicitud', '=', 'dsg.cod_dsg')
                ->selectRaw("
             sg.cod_sg AS id_solicitud,
             dsg.cod_dsg AS id_detalle_solicitud, 
             dsg.cod_dsg as cod_detalle,
             sg.fecha_sg AS fecha,
             sg.tipo_sg AS tipo,
             sg.nro_sg AS nro,
             c.tipo AS tipo_resp,
             c.nombre_completo AS responsable,
             f.DESCRIPCION AS cuenta,
             dsg.detalle AS detalle,
             dsg.importe_bs AS totalbs,
             dsg.importe_usd AS usd,
             CONCAT(e.sigla, '-', SUBSTRING(d.nro_cuenta, LENGTH(d.nro_cuenta)-3, 4), '-', d.moneda) AS bancoo,
             CONCAT(h.sigla, '-', SUBSTRING(g.nro_cuenta, LENGTH(g.nro_cuenta)-3, 4), '-', g.moneda) AS bancod,
             sg.cod_bc AS bancoorigen,
             dsg.cod_bc AS bancodestino,
             dsg.BANDERA AS bandera
             ")
                ->where('sg.ACTIVO', 1)
                ->where('sg.estado', 'INI')
                ->whereBetween('sg.fecha_sg', [$fechaInicio, $fechaFin])
                ->where(function ($query) {
                    $query->where('dsg.BANDERA', 0)
                        ->orWhere('dsg.BANDERA', 2)
                        ->orWhereNull('dsg.BANDERA');
                })
                ->where(function ($query) use ($buscar) {
                    $query->where('sg.nro_sg', 'like', '%' . $buscar . '%')
                        ->orWhere('c.nombre_completo', 'like', '%' . $buscar . '%');
                    //   ->orWhere('c.AP_CLIENTE', 'like', '%' . $buscar . '%')
                    //   ->orWhere('ot.NRO_ORDEN', 'like', '%' . $buscar . '%');
                });

            $gastosQuery->orderBy('sg.fecha_sg', 'asc');
            if ($estado !== null && $estado !== '') {
                $gastosQuery->where('dsi.BANDERA', $estado);
            }
            $gastos = $gastosQuery->get();
        }


        // $gastos = $gastosQuery->get();
        // Recibos solo se incluyen si no se especifica un tipo de movimiento
        $recibos = collect();
        if ($tipoMovimiento == 3) {
            $recibosQuery  = DB::table('z_control_recibos_ot as ot')
                ->join('z_detalle_control_recibos_ot as d', 'd.cod_cr', '=', 'ot.cod_cr')
                ->join('z_banco_cuenta as b', 'b.cod_bc', '=', 'ot.cod_bc_destino')
                ->join('z_banco as c', 'c.cod_b', '=', 'b.cod_b')
                ->select(
                    'ot.cod_cr AS id_solicitud',
                    'ot.cod_cr AS id_detalle_solicitud',
                    'ot.tipo_cambio AS tipocambio',
                    'ot.total_usd AS totalusd',
                    'ot.tipo_pago AS tipopago',
                    DB::raw("ot.fecha AS fecha"),
                    DB::raw("3 AS tipo"),
                    DB::raw("ot.nro_recibo AS nro"),
                    DB::raw("ot.recibi_de AS responsable"),
                    DB::raw("NULL AS cuenta"),
                    DB::raw("GROUP_CONCAT(d.nro_ot) AS nro_ot"),
                    DB::raw("ot.glosa AS detalle"),
                    DB::raw("ot.importe_bs AS totalbs"),
                    DB::raw("ot.importe_usd AS usd"),
                    DB::raw("CONCAT(c.sigla, '-', SUBSTRING(b.nro_cuenta, LENGTH(b.nro_cuenta)-3, 4), '-', b.moneda) AS bancoo"),
                    DB::raw("ot.cod_bc_destino AS bancoorigen"),
                    DB::raw("ot.cod_bc_destino AS bancodestino"),
                    DB::raw("CONCAT(c.sigla, '-', SUBSTRING(b.nro_cuenta, LENGTH(b.nro_cuenta)-3, 4), '-', b.moneda) AS bancod"),
                    DB::raw("ot.BANDERA AS bandera")
                )
                ->where('ot.ACTIVO', 1)
                ->whereIn('ot.bandera', [0, 2])
                ->where(function ($query) use ($buscar) {
                    $query->where('ot.nro_recibo', 'like', '%' . $buscar . '%')
                        ->orWhere('ot.recibi_de', 'like', '%' . $buscar . '%');
                })
                ->whereBetween('ot.fecha', [$fechaInicio, $fechaFin])
                ->groupBy(
                    'ot.cod_cr',
                    'ot.fecha',
                    'ot.nro_recibo',
                    'ot.recibi_de',
                    'ot.glosa',
                    'ot.importe_bs',
                    'ot.importe_usd',
                    'ot.cod_bc_destino',
                    'ot.BANDERA',
                    'ot.tipo_cambio',
                    'ot.total_usd',
                    'ot.tipo_pago',
                    'c.sigla',
                    'b.nro_cuenta',
                    'b.moneda'
                )
                ->orderBy('ot.fecha', 'asc');
            // ->get();
            if ($estado !== null && $estado !== '') {
                $recibosQuery->where('ot.BANDERA', $estado);
            }
            $recibos = $recibosQuery->get();
        }
        // $recibos = $recibosQuery->get();
        // Combinar resultados de gastos y recibos
        $resultadosCombinados = $gastos->merge($recibos);

        // Convertir los resultados en una colección para aplicar paginación
        $collection = collect($resultadosCombinados);

        // Paginación manual
        $page = $request->input('page', 1);

        $perPage = 10; // paginado ;
        $offset = ($page - 1) * $perPage;
        $itemsForCurrentPage = $collection->slice($offset, $perPage)->values()->all();

        $paginatedItems = new LengthAwarePaginator($itemsForCurrentPage, $collection->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        // Devolver los resultados paginados
        return response()->json($paginatedItems);
    }



    // GUARDAR CONCILIACION
    public function createCI(Request $request)
    {
        try {

            $detalles = $request->input('detalles');
            $nota = $request->input('nota');
            $montobanco = $request->input('montobanco');
            foreach ($detalles as $detalle) {
                // Z_Conciliacion::where('id_detalle_solicitud', $detalle['id_detalle_solicitud'])
                //   ->update(['ACTIVO' => 0]); // Asumiendo que 'ACTIVO' es el campo que marca la entrada como activa o inactiva
                $tipoConciliacion = '';
                if (in_array($detalle['tipo'], ['OTROS INGRESOS', 'DEVOLUCIONES'])) {
                    $tipoConciliacion = 'INGRESO';
                } else if ($detalle['tipo'] == 'COBRANZA') {
                    $tipoConciliacion = 'INGRESO';
                } else if ($detalle['tipo'] == 'TRASPASO') {
                    $tipoConciliacion = 'INGRESO';
                }
                $tipoC = '';
                if (in_array($detalle['tipo'], ['OTROS INGRESOS', 'DEVOLUCIONES'])) {
                    $tipoC = 'INGRESO';
                } else if ($detalle['tipo'] == 'COBRANZA') {
                    $tipoC = 'COBRANZA';
                } else if ($detalle['tipo'] == 'TRASPASO') {
                    $tipoC = 'TRASPASO';
                }
                $conciliacion = new Z_Conciliacion([
                    'tipo' => $tipoConciliacion,
                    // 'tipo' => $detalle['tipo'], // Valor por defecto
                    'fecha_conciliacion' => Carbon::now()->toDateTimeString(), // Fecha actual
                    'fecha_banco' => $detalle['fecha_si'], // Fecha de la solicitud
                    'banco_origen' => $detalle['bancoo'], // Banco origen
                    'banco_destino' => $detalle['bancod'], // Banco destino
                    'monto' => $detalle['totalbs'], // Monto en Bs
                    'id_solicitud' => $detalle['id_solicitud'], // ID de la solicitud
                    'id_detalle_solicitud' => $detalle['id_detalle_solicitud'], // ID del detalle de la solicitud
                    'nota' => $nota, // Nota ingresada por el usuario
                    'monto_banco' => $montobanco,
                    'ACTIVO' => 1, // Activo por defecto
                    'BANDERA' => $tipoC, // Activo por defecto
                ]);

                // Guardar la nueva conciliación en la base de datos
                $conciliacion->save();

                $banderaValor = 1; // Si nota está vacía, bandera es 1, si no, es 2

                if ($tipoC == 'TRASPASO') {
                    Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERAI' => 1]);
                } elseif ($tipoC == 'INGRESO') {

                    Z_Detalle_Solicitud_Ingresos::where('cod_dsi', $detalle['id_detalle_solicitud'])
                        ->update(['BANDERA' => 1]);
                } elseif ($tipoC == 'COBRANZA') {

                    ZControlRecibosOT::where('cod_cr', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERA' => 1]);
                }


                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => 1]);
            }
            return response()->json(['success' => true, 'message' => 'Conciliaciones creadas correctamente.']);
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    // solicitudes no conciliadas -observadas
    public function createCIo(Request $request)
    {
        try {

            $detalles = $request->input('detalles');
            $nota = $request->input('nota');

            foreach ($detalles as $detalle) {
                // Z_Conciliacion::where('id_detalle_solicitud', $detalle['id_detalle_solicitud'])
                // ->update(['ACTIVO' => 0]); // Asumiendo que 'ACTIVO' es el campo que marca la entrada como activa o inactiva
                $tipoConciliacion = '';
                if (in_array($detalle['tipo'], ['OTROS INGRESOS', 'DEVOLUCIONES'])) {
                    $tipoConciliacion = 'INGRESOB';
                } else if ($detalle['tipo'] == 'COBRANZA') {
                    $tipoConciliacion = 'COBRANZAB';
                } else if ($detalle['tipo'] == 'TRASPASO') {
                    $tipoConciliacion = 'INGRESOB';
                }
                $tipoC = '';
                if (in_array($detalle['tipo'], ['OTROS INGRESOS', 'DEVOLUCIONES'])) {
                    $tipoC = 'INGRESOB';
                } else if ($detalle['tipo'] == 'COBRANZA') {
                    $tipoC = 'COBRANZAB';
                } else if ($detalle['tipo'] == 'TRASPASO') {
                    $tipoC = 'TRASPASOB';
                }

                $conciliacion = new Z_Conciliacion([
                    'tipo' => $tipoConciliacion,
                    // 'tipo' => $detalle['tipo'], // Valor por defecto
                    'fecha_conciliacion' => Carbon::now()->toDateTimeString(), // Fecha actual
                    'fecha_banco' => $detalle['fecha_si'], // Fecha de la solicitud
                    'banco_origen' => $detalle['bancoo'], // Banco origen
                    'banco_destino' => $detalle['bancod'], // Banco destino
                    'monto' => $detalle['totalbs'], // Monto en Bs
                    'id_solicitud' => $detalle['id_solicitud'], // ID de la solicitud
                    'id_detalle_solicitud' => $detalle['id_detalle_solicitud'], // ID del detalle de la solicitud
                    'nota' => $nota, // Nota ingresada por el usuario
                    'ACTIVO' => 1, // Activo por defecto
                    'BANDERA' => $tipoC, // Activo por defecto
                ]);

                // Guardar la nueva conciliación en la base de datos
                $conciliacion->save();

                $banderaValor = 2; // Si nota está vacía, bandera es 1, si no, es 2

                if ($tipoC == 'TRASPASOB') {
                    Z_MovimientoBancoCuenta::where('cod_mbc', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERAI' => 2]);
                } elseif ($tipoConciliacion == 'INGRESOB') {
                    Z_Detalle_Solicitud_Ingresos::where('cod_dsi', $detalle['id_detalle_solicitud'])
                        ->update(['BANDERA' => 2]);
                } elseif ($tipoConciliacion == 'COBRANZAB') {
                    ZControlRecibosOT::where('cod_cr', $detalle['id_solicitud']) // Asegúrate de que 'cod_cr' es la columna correcta para identificar el detalle en ZControlRecibosOT
                        ->update(['BANDERA' => 2]);
                }

                // Z_Detalle_Solicitud_Gasto::where('cod_dsg', $detalle['id_detalle_solicitud'])
                // ->update(['BANDERA' => 1]);
            }


            return response()->json(['success' => true, 'message' => 'Conciliaciones creadas correctamente.']);
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDataForPdfByID($codigo)
    {
    }
    public function getDataReceipts(Request $request)
    {
    }
    private function validateRegister_registro_venta($codigo)
    {
    }
    private function updateTable_registro_venta($codigo)
    {
    }
    private function insertTable_registro_venta($codigo)
    {
    }
    private function getCorrelativeNumber($codigo)
    {
    }

    private function registerDetailControlReceipts($codigo)
    {
    }
}
