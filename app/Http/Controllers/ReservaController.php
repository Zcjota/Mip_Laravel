<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reserva;
use App\DetalleReserva;
use App\OrdenTrabajo;
use App\HPlagas;
use App\HLugares;
use App\HOrdenTrabajoModelo;
use App\ControlNocturno;
use App\BitacoraReserva;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;


class ReservaController extends Controller
{
    public function obtCodPersonal(Request $request)
    {
        return session()->get('codPersonal');
    }

    public function vincularOT(Request $request)
    {
        $reserva = Reserva::findOrFail($request->codReserva);
        $reserva->codOT = $request->COD_ORDEN_TRABAJO;
        $reserva->save();
    }

    public function listarOTSinAsignacion(Request $request)
    {
        $buscar = $request->buscar;

        $reserva = Reserva::rightJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
            ->leftJoin('cliente', 'cliente.COD_CLIENTE', '=', 'orden_trabajo.COD_CLIENTE')
            ->select(
                'orden_trabajo.COD_ORDEN_TRABAJO',
                'orden_trabajo.ESTADO_PM',
                'orden_trabajo.NRO_ORDEN',
                'cliente.NOMBRE AS NOMCLIENTE',
                'cliente.AP_CLIENTE',
                'cliente.AM_CLIENTE',
                'cliente.CONTACTO',
                'cliente.DIRECCION',
                'cliente.TELEFONO',
                'orden_trabajo.FECHA_SERVICIO',
                'orden_trabajo.ACTIVO',
                'orden_trabajo.FECHA_EMISION_FC'
            )
            ->where('reserva.codOT', null)
            ->where('orden_trabajo.ACTIVO', 1)
            ->where((DB::raw('YEAR(orden_trabajo.FECHA_SERVICIO)')), '>=', '2019')
            //->where('orden_trabajo.NRO_ORDEN',$buscar)


            ->where(function ($query) use ($buscar) {
                $query-> //where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$buscar.'%')
                    orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscar . '%');
            })

            ->orderBy('reserva.fecha', 'desc')
            ->paginate(20);


        return [
            'pagination' => [
                'total'        => $reserva->total(),
                'current_page' => $reserva->currentPage(),
                'per_page'     => $reserva->perPage(),
                'last_page'    => $reserva->lastPage(),
                'from'         => $reserva->firstItem(),
                'to'           => $reserva->lastItem(),
            ],
            'reserva' => $reserva
        ];
    }

    public function listarReserva(Request $request)
    {
        $buscarReserva = $request->buscar;
        if ($request->fechaInicio == '' && $request->fechaFin == '') {
            $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                ->select(
                    'orden_trabajo.NRO_ORDEN',
                    'reserva.codReserva',
                    'reserva.codOT',
                    'reserva.fecha',
                    'reserva.horainicio',
                    'reserva.tiempoServicio',
                    'reserva.tipo',
                    'reserva.tiempoTransporte',
                    'reserva.estado',
                    'reserva.tipo',
                    'reserva.codCliente',
                    'reserva.codPersonal',
                    'cliente.NOMBRE',
                    'cliente.AP_CLIENTE',
                    (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                    'cliente.TELEFONO',
                    'cliente.CONTACTO',
                    'cliente.RAZON_SOCIAL',
                    'cliente.CI_NIT',
                    'cliente.DIRECCION',
                    'personal.NOMBRE as NOMB_PERSONAL',
                    'personal.AP_PATERNO as APP_PERSONAL'
                )

                ->where('reserva.estado', 1)

                ->where(function ($query) use ($buscarReserva) {
                    $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                        ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                })
                ->orderBy('reserva.fecha', 'desc')
                ->paginate(20);
        } else {
            if ($request->fechaInicio != '' && $request->fechaFin != '') {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )

                    ->where('reserva.estado', 1)
                    ->whereBetween('reserva.fecha', [$request->fechaInicio, $request->fechaFin])
                    //," ",cliente.AM_CLIENTE
                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            } else {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )

                    ->where('reserva.estado', 1)

                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $request->buscar . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            }
        }

        return [
            'pagination' => [
                'total'        => $reserva->total(),
                'current_page' => $reserva->currentPage(),
                'per_page'     => $reserva->perPage(),
                'last_page'    => $reserva->lastPage(),
                'from'         => $reserva->firstItem(),
                'to'           => $reserva->lastItem(),
            ],
            'reserva' => $reserva
        ];
    }

    public function listarReservaMenosOperaciones(Request $request)
    {
        $buscarReserva = $request->buscar;
        if ($request->fechaInicio == '' && $request->fechaFin == '') {
            $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                ->select(
                    'orden_trabajo.NRO_ORDEN',
                    'reserva.codReserva',
                    'reserva.codOT',
                    'reserva.fecha',
                    'reserva.horainicio',
                    'reserva.tiempoServicio',
                    'reserva.tipo',
                    'reserva.tiempoTransporte',
                    'reserva.estado',
                    'reserva.tipo',
                    'reserva.codCliente',
                    'reserva.codPersonal',
                    'cliente.NOMBRE',
                    'cliente.AP_CLIENTE',
                    (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                    'cliente.TELEFONO',
                    'cliente.CONTACTO',
                    'cliente.RAZON_SOCIAL',
                    'cliente.CI_NIT',
                    'cliente.DIRECCION',
                    'personal.NOMBRE as NOMB_PERSONAL',
                    'personal.AP_PATERNO as APP_PERSONAL'
                )
                ->where('reserva.codPersonal', '<>', 24)
                ->where('reserva.estado', 1)
                //->where('reserva.estadoCoordinacion',0)
                //," ",cliente.AM_CLIENTE
                ->where(function ($query) use ($buscarReserva) {
                    $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                        ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                })
                ->orderBy('reserva.fecha', 'desc')
                ->paginate(20);
        } else {
            if ($request->fechaInicio != '' && $request->fechaFin != '') {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )
                    ->where('reserva.codPersonal', '<>', 24)
                    ->where('reserva.estado', 1)
                    //  ->where('reserva.estadoCoordinacion',0)

                    ->whereBetween('reserva.fecha', [$request->fechaInicio, $request->fechaFin])
                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            } else {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )
                    ->where('reserva.codPersonal', '<>', 24)
                    ->where('reserva.estado', 1)
                    //    ->where('reserva.estadoCoordinacion',0)
                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE)'), 'like', '%' . $request->buscar . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            }
        }

        return [
            'pagination' => [
                'total'        => $reserva->total(),
                'current_page' => $reserva->currentPage(),
                'per_page'     => $reserva->perPage(),
                'last_page'    => $reserva->lastPage(),
                'from'         => $reserva->firstItem(),
                'to'           => $reserva->lastItem(),
            ],
            'reserva' => $reserva
        ];
    }
    //listado de las reservas que no tiene tecnicos vinculados
    public function listarReservasOperaciones(Request $request)
    {
        $buscarReserva = $request->buscar;
        if ($request->fechaInicio == '' && $request->fechaFin == '') {
            $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                ->select(
                    'orden_trabajo.NRO_ORDEN',
                    'reserva.codReserva',
                    'reserva.codOT',
                    'reserva.fecha',
                    'reserva.horainicio',
                    'reserva.tiempoServicio',
                    'reserva.tipo',
                    'reserva.tiempoTransporte',
                    'reserva.estado',
                    'reserva.tipo',
                    'reserva.codCliente',
                    'reserva.codPersonal',
                    'cliente.NOMBRE',
                    'cliente.AP_CLIENTE',
                    (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                    'cliente.TELEFONO',
                    'cliente.CONTACTO',
                    'cliente.RAZON_SOCIAL',
                    'cliente.CI_NIT',
                    'cliente.DIRECCION',
                    'personal.NOMBRE as NOMB_PERSONAL',
                    'personal.AP_PATERNO as APP_PERSONAL'
                )
                ->where('reserva.codPersonal', '<>', 24)
                ->where('reserva.estado', 1)
                ->where('reserva.estadoCoordinacion', 1)

                ->where(function ($query) use ($buscarReserva) {
                    $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                        ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                })
                ->orderBy('reserva.fecha', 'desc')
                ->paginate(20);
        } else {
            if ($request->fechaInicio != '' && $request->fechaFin != '') {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )
                    ->where('reserva.codPersonal', '<>', 24)
                    ->where('reserva.estado', 1)
                    ->where('reserva.estadoCoordinacion', 1)

                    ->whereBetween('reserva.fecha', [$request->fechaInicio, $request->fechaFin])
                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'), 'like', '%' . $buscarReserva . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            } else {
                $reserva = Reserva::join('cliente', 'cliente.COD_CLIENTE', '=', 'reserva.codCliente')
                    ->leftJoin('orden_trabajo', 'orden_trabajo.COD_ORDEN_TRABAJO', '=', 'reserva.codOT')
                    ->leftJoin('personal', 'reserva.codPersonal', '=', 'personal.COD_PERSONAL')
                    ->select(
                        'orden_trabajo.NRO_ORDEN',
                        'reserva.codReserva',
                        'reserva.codOT',
                        'reserva.fecha',
                        'reserva.horainicio',
                        'reserva.tiempoServicio',
                        'reserva.tipo',
                        'reserva.tiempoTransporte',
                        'reserva.estado',
                        'reserva.tipo',
                        'reserva.codCliente',
                        'reserva.codPersonal',
                        'cliente.NOMBRE',
                        'cliente.AP_CLIENTE',
                        (DB::raw('IFNULL(cliente.AM_CLIENTE,"") AS AM_CLIENTE')),
                        'cliente.TELEFONO',
                        'cliente.CONTACTO',
                        'cliente.RAZON_SOCIAL',
                        'cliente.CI_NIT',
                        'cliente.DIRECCION',
                        'personal.NOMBRE as NOMB_PERSONAL',
                        'personal.AP_PATERNO as APP_PERSONAL'
                    )
                    ->where('reserva.codPersonal', '<>', 24)
                    ->where('reserva.estado', 1)
                    ->where('reserva.estadoCoordinacion', 1)
                    ->where(function ($query) use ($buscarReserva) {
                        $query->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'), 'like', '%' . $request->buscar . '%')
                            ->orWhere('orden_trabajo.NRO_ORDEN', 'like', '%' . $buscarReserva . '%');
                    })
                    ->orderBy('reserva.fecha', 'desc')
                    ->paginate(20);
            }
        }

        return [
            'pagination' => [
                'total'        => $reserva->total(),
                'current_page' => $reserva->currentPage(),
                'per_page'     => $reserva->perPage(),
                'last_page'    => $reserva->lastPage(),
                'from'         => $reserva->firstItem(),
                'to'           => $reserva->lastItem(),
            ],
            'reserva' => $reserva
        ];
    }


    public function desactivar(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->estado == 0) {
                if ($request->COD_ORDEN_TRABAJO != null) {
                    $ot = OrdenTrabajo::findOrFail($request->COD_ORDEN_TRABAJO);
                    $ot->ANULADA = 1;
                    $ot->APROBADA = 0;
                    $ot->COBRADA = 0;
                    $ot->SUSPENDIDA = 0;
                    $ot->REPROGRAMADA = 0;

                    $ot->save();
                }
            }


            $reserva = Reserva::findOrFail($request->id);
            $reserva->estado = $request->estado;
            $reserva->save();

            $this->bitacora($request->id, "B");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }


    // Ernesto OT
    // se añadio campo emision de factura 
    public function guardarOT(Request $request)
    {
        $codOT = 0;
        $plazo = 0;

        // PAGO AL CREDITO

        if ($request->factura === 0) {
            $this->validate($request, [
                'categoria' => 'required',
                'especificacion' => 'required',
                'tipoPago' => 'required',
                'precio' => 'required',
                'nroTecnicos' => 'required',
                'frecuencia' => 'required',
                'ejecutivoVentas' => 'required',
                'observaciones' => 'required',

            ]);
        } else {
            $this->validate($request, [
                'categoria' => 'required',
                'especificacion' => 'required',
                'FECHA_EMISION_FC' => 'required',
                'tipoPago' => 'required',
                'precio' => 'required',
                'nroTecnicos' => 'required',
                'frecuencia' => 'required',
                'ejecutivoVentas' => 'required',
                'observaciones' => 'required',
            ]);
        }


        if ($request->tipoPago === 2) {
            $this->validate($request, [
                'plazo' => 'required'
            ]);
        }
        try {
            DB::beginTransaction();
            $numero =  $this->objNroOrden();
            if ($request->plazo != "")
                $plazo = $request->plazo;

            $OT = new OrdenTrabajo();
            $OT->COD_CLIENTE = $request->codCliente;
            $OT->FECHA = date('Ymd'); //fecha actual
            $OT->FECHA_SERVICIO = $request->fechaServicio;
            $OT->FECHA_EMISION_FC = $request->FECHA_EMISION_FC;
            $OT->HORA_SERVICIO = $request->horaServico;
            $OT->DATOS_FACTURA = "";
            $OT->COBRADA = 0;
            $OT->SUSPENDIDA = 0;
            $OT->REPROGRAMADA = 0;
            $OT->PAGO = 0;
            $OT->ANULADA = 0;
            $OT->MEMO_ANU = "";
            //$OT->FECHA_ANU= date('0000-00-00 00:00:00');
            $OT->IMPRESION_RECIBO = 0;
            $OT->ENVIO_EMAIL_CREDITO = 0;
            $OT->TECNICO = 0;
            //$OT->FECHA_COBRADA=$request->fechaServicio;
            $OT->CERTIFICADO = 0;

            $OT->RAZON_SOCIAL = $request->razonSocial;
            $OT->NIT = $request->nit;
            $OT->TIPO_PAGO = $request->tipoPago;
            $OT->PLAZO = $plazo;
            $OT->PRECIO = $request->precio;
            $OT->BS = 0;
            $OT->OBSERVACIONES = $request->observaciones;
            $OT->NRO_TECNICOS = $request->nroTecnicos;
            $OT->TIEMPO_SERVICIO = $request->tiempoServicio;
            $OT->COD_EJECUTIVO_VENTAS = session()->get('tipoPersonal');
            $OT->COD_EQUIPO =  $request->equipo;
            $OT->RESPONSABLES = $request->reponsable;
            $OT->NRO_ORDEN = $numero;
            $OT->NRO_ORDEN_PADRE = 0;
            $OT->APROBADA = 0;
            $OT->INICIAL = 1;
            $OT->CLIENTE_INICIAL = 0;
            $OT->COD_CATEGORIA = $request->categoria;
            $OT->COD_ESPECIFICACION = $request->especificacion;
            $OT->COD_USUARIO = session()->get('codigo');
            $OT->FECHA_ALTA = date('Ymd');
            $OT->ACTIVO = 1;
            $OT->ESTADO_FRECUENCIA = 1;
            $OT->FRECUENCIA = $request->frecuencia;
            $OT->FACTURA = $request->factura;
            $OT->save();

            $codOT = $OT->COD_ORDEN_TRABAJO;


            $reserva = Reserva::findOrFail($request->codReserva);
            $reserva->codOT = $OT->COD_ORDEN_TRABAJO;
            $reserva->save();


            $fechaSistema = date('Ymd');
            $HistorialOT = new HOrdenTrabajoModelo();
            $HistorialOT->COD_ORDEN_TRABAJO = $OT->COD_ORDEN_TRABAJO;
            $HistorialOT->ESTADO = 0;
            $HistorialOT->COD_USUARIO = session()->get('codigo');;
            $HistorialOT->FECHA_ESTADO = $fechaSistema;
            $HistorialOT->save();

            //plagas 

            $detallesPlaga = $request->dataPlaga;
            for ($i = 0; $i < count($detallesPlaga); $i++) {
                $plaga = new HPlagas();
                $plaga->COD_PLAGA = $detallesPlaga[$i];
                $plaga->COD_ORDEN_TRABAJO = $OT->COD_ORDEN_TRABAJO;
                $plaga->ACTIVO = 1;
                $plaga->save();
            }

            //lugares
            $detallesLugar = $request->dataLugar;
            for ($v = 0; $v < count($detallesLugar); $v++) {
                $lugar = new HLugares();
                $lugar->COD_LUGARES = $detallesLugar[$v];
                $lugar->COD_ORDEN_TRABAJO = $OT->COD_ORDEN_TRABAJO;
                $lugar->ACTIVO = 1;
                $lugar->save();
            }

            DB::commit();
            // return response()->json(['mensaje' => 'Registro de OT Realizada con Exito. '], 201);
            return response()->json(['mensaje' => 'Registro de OT realizada con éxito.', 'COD_ORDEN_TRABAJO' => $OT->COD_ORDEN_TRABAJO], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Error de validación: ' . $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function facturasPorEmitir(Request $request)
    {
        try {
            // Recibe los parámetros de paginación de la solicitud
            $page = $request->input('page', 1);
            $perPage = $request->input('perPage', 10);
            $fechaInicio = $request->input('fechaInicio');
            $fechaFin = $request->input('fechaFin');
            $busqueda = $request->input('busqueda');

            // Verifica si se proporcionaron fechas de inicio y fin
            if ($fechaInicio && $fechaFin) {
                // Si se proporcionaron fechas, filtra por el rango de fechas especificado
                $fechaInicio = Carbon::parse($fechaInicio)->toDateString();
                $fechaFin = Carbon::parse($fechaFin)->toDateString();
            } else {
                // Si no se proporcionaron fechas, establece la fecha actual como fecha de inicio y fin
                $fechaInicio = Carbon::now()->toDateString();
                $fechaFin = Carbon::now()->toDateString();
            }

            // Construye la consulta SQL base
            $query = DB::table('orden_trabajo as ot')
                ->select(
                    'ot.FECHA',
                    'ot.FECHA_EMISION_FC',
                    'ot.NRO_ORDEN',
                    'cliente.NOMBRE as cliente_nombre',
                    'cliente.RAZON_SOCIAL',
                    'ot.NIT',
                    'ot.TIPO_PAGO',
                    'ot.PRECIO'
                )
                ->join('cliente', 'ot.COD_CLIENTE', '=', 'cliente.COD_CLIENTE')
                ->where('ot.ACTIVO', 1)
                ->where('ot.FACTURA', 1)
                ->whereBetween('ot.FECHA_EMISION_FC', [$fechaInicio, $fechaFin]);

            // Agrega la condición de búsqueda LIKE si la variable $busqueda tiene datos
            if ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('ot.NRO_ORDEN', 'like', "%$busqueda%")
                        ->orWhere('cliente.NOMBRE', 'like', "%$busqueda%")
                        ->orWhere('cliente.RAZON_SOCIAL', 'like', "%$busqueda%")
                        ->orWhere('ot.NIT', 'like', "%$busqueda%");
                });
            }

            // Ejecuta la consulta y realiza la paginación
            $results = $query->orderBy('ot.NRO_ORDEN', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            if ($results->isEmpty()) {
                // Mensaje si no se encuentra ningún dato
                return response()->json(['mensaje' => 'No se encontraron facturas por emitir en el rango de fechas especificado. '], 201);
            } else {
                // Aquí puedes realizar cualquier operación adicional con los resultados obtenidos
                return response()->json($results);
            }
        } catch (QueryException $e) {
            // Mensaje en caso de error de servidor
            return "Error de servidor: " . $e->getMessage();
        }
    }



    public function facturasPorNotificar()
    {
        try {
            // Recibe los parámetros de búsqueda de la solicitud
         
    
            // Construye la consulta SQL base
            $query = DB::table('orden_trabajo as ot')
                ->select(
                    'ot.FECHA',
                    'ot.FECHA_EMISION_FC',
                    'ot.NRO_ORDEN',
                    'cliente.NOMBRE as cliente_nombre',
                    'cliente.RAZON_SOCIAL',
                    'ot.NIT',
                    'ot.TIPO_PAGO',
                    'ot.PRECIO',
                    DB::raw('DATEDIFF(ot.FECHA_EMISION_FC, NOW()) AS dias_faltantes')
                )
                ->join('cliente', 'ot.COD_CLIENTE', '=', 'cliente.COD_CLIENTE')
                ->where('ot.TIPO_PAGO', 2) // Filtra solo facturas a crédito
                ->where('ot.PLAZO', '>', 0); // Filtra solo facturas con plazo mayor a 0
    
       
    
            // Ejecuta la consulta y obtiene los resultados
            $results = $query->get();
    
            // Filtra los resultados para incluir solo las facturas próximas a vencer (con 7 días o menos)
            $facturasProximasVencer = $results->filter(function ($factura) {
                return $factura->dias_faltantes <= 7 && $factura->dias_faltantes >= 0;
            });
    
            // Verifica si se encontraron facturas próximas a vencer
            if ($facturasProximasVencer->isEmpty()) {
                // Mensaje si no se encontraron facturas próximas a vencer
                return response()->json(['mensaje' => 'No se encontraron facturas  próximas a vencer en el rango de fechas especificado.'], 201);
            } else {
                // Devuelve las facturas próximas a vencer como respuesta
                return response()->json($facturasProximasVencer);
            }
        } catch (QueryException $e) {
            // Mensaje en caso de error de servidor
            return "Error de servidor: " . $e->getMessage();
        }
    }

    //Ernesto OT

    public function objNroOrden()
    {
        $cliente = DB::select('SELECT NRO_ORDEN FROM `orden_trabajo` WHERE YEAR(FECHA_ALTA) = YEAR(CURDATE()) ORDER BY NRO_ORDEN DESC LIMIT 0,1');
        $nroOrden = 1;
        if (count($cliente) > 0)
            $nroOrden = $cliente[0]->NRO_ORDEN + 1;

        return $nroOrden;
    }

    public function validarReserva(Request $request)
    {
        $this->validate($request, [
            'cliente' => 'required',
            'tiempoTransporte' => 'required',
            'fechaReserva' => 'required',
            'horaInicio' => 'required',
            'tiempoServicio' => 'required',
            //'equipo'=>'required',
        ]);
    }
    public function validarReservaAdmin(Request $request)
    {
        $this->validate($request, [
            'cliente' => 'required',
            'tiempoTransporte' => 'required',
            'fechaReserva' => 'required',
            'horaInicio' => 'required',
            'tiempoServicio' => 'required',
        ]);
    }
    public function verificarTecnicoDiaSig(Request $request)
    {
        //se agrego AND A.estadoTrabajador=1
        $verificar = DB::select('SELECT A.codTrabajador,C.NOMBRE,C.AP_PATERNO,C.AP_MATERNO
        FROM detallereserva A 
        INNER JOIN reserva B ON A.codReserva = B.codReserva INNER JOIN personal C ON A.codTrabajador=C.COD_PERSONAL
        WHERE B.fecha= "' . $request->fecha . '" AND B.horaInicio <= "09:30:00" AND B.estado=1 AND A.estadoTrabajador=1');
        return $verificar;
    }

    public function guardar(Request $request)
    {
        try {
            DB::beginTransaction();
            $reserva = new Reserva();
            $reserva->fecha = $request->fechaReserva;
            // $reserva->fechaemision = $request->fechaemision;
            $reserva->horaInicio = $request->horaInicio;
            $reserva->tiempoServicio = $request->tiempoServicio;
            $reserva->tiempoTransporte = $request->tiempoTransporte;
            $reserva->estado = '1';
            $reserva->tipo = 0;
            $reserva->codCliente = $request->cliente;
            $reserva->codPersonal = session()->get('codPersonal');

            $reserva->estadoCoordinacion = $request->estadoOperacion;
            $reserva->save();


            if ($request->estadoOperacion == 0) {
                $this->bitacora($reserva->codReserva, "A");

                $detalles = $request->data;
                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new DetalleReserva();
                    $detalle->codTrabajador = $detalles[$i];
                    $detalle->codReserva = $reserva->codReserva;
                    $detalle->estadoTrabajador = 1;
                    $detalle->save();
                }
            } else {
                //AO: ALTA OPERACIONES, RESERVA SIN TECNICOS
                $this->bitacora($reserva->codReserva, "AO");
            }


            if ($request->horarioNocturno == 1) {
                for ($c = 0; $c < count($detalles); $c++) {
                    $controlN = new ControlNocturno();
                    $controlN->fechaFinalizacion = $request->fechaFinalizacion;
                    $controlN->activo = 1;
                    $controlN->codPersonal = $detalles[$c];
                    $controlN->incremento = $request->contadorNocturno;
                    $controlN->codReserva = $reserva->codReserva;
                    $controlN->save();
                }
            }
            echo (session()->get('codPersonal'));
            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
        }
    }

    public function registrarReservaReplicacion(Request $request)
    {
        try {
            DB::beginTransaction();
            $reserva = new Reserva();
            $reserva->codCliente = $request->cliente;
            $reserva->tiempoTransporte = $request->tiempoTransporte;
            $reserva->fecha = $request->fechaReserva;
            $reserva->horaInicio = $request->horaInicio;
            $reserva->tiempoServicio = $request->tiempoServicio;
            $reserva->estado = '1';
            $reserva->codPersonal = session()->get('codPersonal');
            $reserva->tipo = 0;
            $reserva->estadoCoordinacion = $request->estadoOperacion;
            $reserva->save();


            if ($request->estadoOperacion == 0) {
                $this->bitacora($reserva->codReserva, "A");

                $detalles = $request->data;

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new DetalleReserva();
                    $detalle->codTrabajador = $detalles[$i];
                    $detalle->codReserva = $reserva->codReserva;
                    $detalle->estadoTrabajador = 1;
                    $detalle->save();
                }
            } else {
                //AO: ALTA OPERACIONES, RESERVA SIN TECNICOS
                $this->bitacora($reserva->codReserva, "AO");
            }


            if ($request->horarioNocturnoReserva == 1) {
                for ($c = 0; $c < count($detalles); $c++) {

                    $controlN = new ControlNocturno();
                    $controlN->fechaFinalizacion = $request->fechaFinalizacionReserva;
                    $controlN->activo = 1;
                    $controlN->codPersonal = $detalles[$c];
                    $controlN->incremento = $request->contadorNocturnoVReserva;
                    $controlN->codReserva = $reserva->codReserva;
                    $controlN->save();
                }
            }





            $reservaRep = new Reserva();
            $reservaRep->codCliente = $request->cliente;
            $reservaRep->tiempoTransporte = $request->tiempoTransporte;
            $reservaRep->fecha = $request->fechaReservaReplicacion;
            $reservaRep->horaInicio = $request->horaInicioReplicacion;
            $reservaRep->tiempoServicio = $request->tiempoServicioReplicacion;
            $reservaRep->estado = '1';
            $reservaRep->tipo = 1;
            $reservaRep->codPersonal = session()->get('codPersonal');
            $reservaRep->estadoCoordinacion = $request->estadoOperacion;
            $reservaRep->save();


            if ($request->estadoOperacion == 0) {
                $this->bitacora($reservaRep->codReserva, "A");
                $detallesRep = $request->dataReplicacion;
                for ($i = 0; $i < count($detallesRep); $i++) {
                    $detalle = new DetalleReserva();
                    $detalle->codTrabajador = $detallesRep[$i];
                    $detalle->codReserva = $reservaRep->codReserva;
                    $detalle->estadoTrabajador = 1;
                    $detalle->save();
                }
            } else {
                //AO: ALTA OPERACIONES, RESERVA SIN TECNICOS
                $this->bitacora($reserva->codReserva, "AO");
            }


            if ($request->horarioNocturnoReaplicacion == 1) {
                for ($q = 0; $q < count($detallesRep); $q++) {
                    $controlN = new ControlNocturno();
                    $controlN->fechaFinalizacion = $request->fechaFinalizacionReaplicacion;
                    $controlN->activo = 1;
                    $controlN->codPersonal = $detalles[$q];
                    $controlN->incremento = $request->contadorNocturnoVReaplicacion;
                    $controlN->codReserva = $reservaRep->codReserva;
                    $controlN->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }



    public function modificarReserva(Request $request)
    {
        try {
            DB::beginTransaction();

            $reserva = Reserva::findOrFail($request->idReserva);
            $reserva->fecha = $request->fechaReserva;
            $reserva->fechaemision = $request->fechaemision;
            $reserva->horaInicio = $request->horaInicio;
            $reserva->tiempoServicio = $request->tiempoServicio;
            $reserva->tiempoTransporte = $request->tiempoTransporte;
            $reserva->estadoCoordinacion = 0;
            $reserva->save();

            $this->bitacora($request->idReserva, "M");


            //COD_ORDEN_TRABAJO
            //codOT
            if ($request->COD_ORDEN_TRABAJO != null && $request->cambio == 1) {
                $ot = OrdenTrabajo::findOrFail($request->COD_ORDEN_TRABAJO);
                $ot->ANULADA = 0;
                $ot->APROBADA = 0;
                $ot->COBRADA = 0;
                $ot->SUSPENDIDA = 0;
                $ot->REPROGRAMADA = 1;
                $ot->FECHA_SERVICIO = $request->fechaReserva;
                $ot->HORA_SERVICIO = $request->horaInicio;
                $ot->FECHA_EMISION_FC = $request->fechaemision;
                $ot->save();
            }
            /*else
            {
                $ot = OrdenTrabajo::findOrFail($request->codOT);
                $ot->FECHA_SERVICIO=$request->fechaReserva;
                $ot->HORA_SERVICIO=$request->horaInicio;
                $ot->save();
            }
            */

            $reserva2 = DetalleReserva::where('codReserva', $request->idReserva)
                ->update(['estadoTrabajador' => 0]);

            $detalles = $request->trabajadores;

            for ($i = 0; $i < count($detalles); $i++) {
                $detalle = new DetalleReserva();
                $detalle->codTrabajador = $detalles[$i];
                $detalle->codReserva = $request->idReserva;
                $detalle->estadoTrabajador = 1;
                $detalle->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }



    public function bitacora($codReserva, $tipo)
    {
        //bitacora
        $fechaSistema = date('Ymd');

        $bitacora = new BitacoraReserva();
        $bitacora->fecha = NOW();
        $bitacora->codReserva = $codReserva;
        $bitacora->codPersonal = session()->get('codPersonal');
        $bitacora->tipo = $tipo;
        $bitacora->activo = "A";
        $bitacora->save();
    }
}
