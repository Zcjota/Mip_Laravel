<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\OrdenTrabajo;
use App\HPlagas;
use App\HLugares;
use App\SolicitudModificarOt;

class OrdenTrabajoController extends Controller
{
    public function reprogramar(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');

        $ot = OrdenTrabajo::findOrFail($request->idOT);
        $ot->SUSPENDIDA = '0';
        $ot->REPROGRAMADA = '1';
        $ot->FECHA_SERVICIO = $request->fechaReprogramar;
        $ot->HORA_SERVICIO = $request->horaReprogramar;
        $ot->save();
    }

    public function suspender(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');

        $ot = OrdenTrabajo::findOrFail($request->idOT);
        $ot->SUSPENDIDA = '1';
        $ot->REPROGRAMADA = '0';
        $ot->save();
    }
    /*
    <?php
	include("../lib/conex.php");
	$descripcion  = "'". strtoupper($_POST["solicitud"]). "'";
	$codigo       = "'". $_REQUEST["codigo"]. "'";
	if (!VerificaConBD())
	{	echo '{"Success": false, "errors":{"reason": "No se puede conectar con la BD"}}';	
		exit;
	}	
		// echo "/$tipoPersonal/";
			$sqli = "INSERT INTO `solicitud_modf_ot` (COD_ORDEN_TRABAJO, FECHA,DESCRIPCION,ESTADO,ACTIVO)" .
					" VALUES (". $codigo .",NOW(),".$descripcion .",'SM', 1)";
			
			$sqlu = ' UPDATE `orden_trabajo` SET'. 
			       ' ESTADO_PM = "SM"'.
				   ' WHERE COD_ORDEN_TRABAJO = '.$codigo;
				   //echo "/$sql/";

	$resultadoi = mySQL_Query($sqli , $_SESSION['BD']);
	$resultadou = mySQL_Query($sqlu , $_SESSION['BD']);
	echo "{success: true}";
	  
    	

?>
    */
    public function modificar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            
            $ot = OrdenTrabajo::findOrFail($request->idOT);
            $ot->ESTADO_PM = 'SM';
            $ot->save();

            $fechaSistema = date('Ymd');    
            $modificar = new SolicitudModificarOt();
            $modificar->COD_ORDEN_TRABAJO = $request->idOT;
            $modificar->DESCRIPCION = $request->descripcionModificar;
            $modificar->FECHA = $fechaSistema;
            $modificar->FECHA_RESP = $fechaSistema;
            $modificar->ESTADO = "SM";
            $modificar->ACTIVO = 1;
            $modificar->COD_USUARIO = session()->get('codigo');
            $modificar->save();
            DB::commit();
        } 
        catch (\Exception $e){
            DB::rollBack();
        } 
    }


    public function listado1(Request $request)
    {
        if(session()->get('codigo')!="" || session()->get('codigo')!=null)
        {
            if($request->buscar==''){
                $cliente = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
                ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
                'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
                'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA','orden_trabajo.ANULADA',
                'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
                ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL','orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')
        
                ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
                ->where('orden_trabajo.ACTIVO', 1)
                ->where('orden_trabajo.REPROGRAMADA', 0)
                ->where('orden_trabajo.SUSPENDIDA', 0)
                ->where('orden_trabajo.APROBADA', 0)
                ->where('orden_trabajo.ANULADA', 0)
    
                ->orWhere('orden_trabajo.REPROGRAMADA', 1)
                ->orWhere('orden_trabajo.SUSPENDIDA', 1)
                ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                ->paginate(20);
            }
            else
            {
                $cliente = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
                ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
                ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
                'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
                'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
                'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA',
                'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
                ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL'
                ,'orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')
        
                ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))    
                ->where('orden_trabajo.ACTIVO', 1)
                
                
                /*
                ->where('orden_trabajo.REPROGRAMADA', 0)
                ->where('orden_trabajo.SUSPENDIDA', 0)
                ->where('orden_trabajo.APROBADA', 0)
                ->where('orden_trabajo.ANULADA', 0)
    
                ->orWhere('orden_trabajo.REPROGRAMADA', 1)
                ->orWhere('orden_trabajo.SUSPENDIDA', 1)
                */
                
                ->Where('orden_trabajo.NRO_ORDEN',$request->buscar)
                ->orwhere(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$request->buscar.'%')
                ->orWhere('cliente.TELEFONO',$request->buscar)
                ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
                ->paginate(20);
               
            }
    
    
            
            return [
                'pagination' => [
                    'total'        => $cliente->total(),
                    'current_page' => $cliente->currentPage(),
                    'per_page'     => $cliente->perPage(),
                    'last_page'    => $cliente->lastPage(),
                    'from'         => $cliente->firstItem(),
                    'to'           => $cliente->lastItem(),
                ],
                'ot' => $cliente
            ];
        }
        else
        return "expiro";
        


    }
    public function listado()
    {
        if(!$request->ajax()) return redirect('/login');

        /*
        $ordenTrabajo = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
        ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
        ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.NRO_ORDEN','cliente.NOMBRE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO','cliente.DIRECCION','cliente.TELEFONO',
        'orden_trabajo.FECHA_SERVICIO','orden_trabajo.HORA_SERVICIO','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.RAZON_SOCIAL','orden_trabajo.NIT'
        ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS',
        'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','orden_trabajo.COD_EQUIPO','orden_trabajo.RESPONSABLES',
        'orden_trabajo.APROBADA','orden_trabajo.SUSPENDIDA','orden_trabajo.REPROGRAMADA')
        ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
        ->where('orden_trabajo.ACTIVO', 1)
        ->where('orden_trabajo.APROBADA', 0)
        ->where('orden_trabajo.COBRADA', 0)
        ->where('orden_trabajo.ANULADA', 0)
        ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
        ->get();
        return $ordenTrabajo; 
        */
        
        /*
$cliente = DB::select('SELECT O.COD_ORDEN_TRABAJO, O.COD_CLIENTE, C.NOMBRE AS NOMCLIENTE, C.AP_CLIENTE, C.AM_CLIENTE, C.CONTACTO, C.DIRECCION, C.TELEFONO, '.
        ' DATE_FORMAT(O.FECHA, "%d/%m/%Y")  AS FECHA1, DATE_FORMAT(O.FECHA_SERVICIO, "%d/%m/%Y")  AS FECHAS, O.HORA_SERVICIO, O.RAZON_SOCIAL, O.FRECUENCIA,'.
        '  O.NIT,O.OBSERVACIONES, O.PRECIO, O.BS, O.NRO_TECNICOS, O.COD_EQUIPO, O.APROBADA, O.COBRADA, O.SUSPENDIDA, R.DESCRIPCION, R.FECHA_RECLAMO, '.
        ' O.TIEMPO_SERVICIO, O.COD_EJECUTIVO_VENTAS, P.NOMBRE, P.AP_PATERNO, P.AP_MATERNO, O.NRO_ORDEN, O.RESPONSABLES, '.
        ' O.TIPO_PAGO, O.PLAZO, O.REPROGRAMADA, O.INICIAL,O.COD_CATEGORIA,O.COD_ESPECIFICACION, O.ESTADO_PM,S.DESCRIPCION as DES,S.FECHA_SUSPENDER, O.FACTURA  '.
        ' FROM orden_trabajo O '.
        ' LEFT JOIN cliente C ON O.COD_CLIENTE = C.COD_CLIENTE '.
        ' LEFT JOIN personal P ON O.COD_EJECUTIVO_VENTAS = P.COD_PERSONAL '.
        ' LEFT JOIN reclamos R ON (SELECT MAX(COD_RECLAMO) FROM reclamos WHERE COD_ORDEN_TRABAJO =O.COD_ORDEN_TRABAJO) = R.COD_RECLAMO '.
        ' LEFT JOIN suspender S ON (SELECT MAX(COD_SUSPENDER) FROM suspender WHERE COD_ORDEN_TRABAJO =O.COD_ORDEN_TRABAJO) = S.COD_SUSPENDER '.
        ' WHERE O.ACTIVO = 1  AND O.APROBADA = 0 AND O.COBRADA = 0 AND O.ANULADA = 0 AND O.COD_USUARIO ='.session()
        */
        $cliente = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
        ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
        ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','cliente.NOMBRE AS NOMCLIENTE','cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.CONTACTO',
        'cliente.DIRECCION','cliente.TELEFONO','orden_trabajo.FECHA AS FECHA1','orden_trabajo.FECHA_SERVICIO AS FECHAS','orden_trabajo.HORA_SERVICIO','orden_trabajo.RAZON_SOCIAL',
        'orden_trabajo.FRECUENCIA','orden_trabajo.NIT','orden_trabajo.OBSERVACIONES','orden_trabajo.PRECIO','orden_trabajo.BS','orden_trabajo.NRO_TECNICOS','orden_trabajo.COD_EQUIPO',
        'orden_trabajo.APROBADA','orden_trabajo.COBRADA','orden_trabajo.SUSPENDIDA',
        'orden_trabajo.TIEMPO_SERVICIO','orden_trabajo.COD_EJECUTIVO_VENTAS','personal.NOMBRE','personal.AP_PATERNO','personal.AP_MATERNO','orden_trabajo.NRO_ORDEN','orden_trabajo.RESPONSABLES'
        ,'orden_trabajo.TIPO_PAGO','orden_trabajo.PLAZO','orden_trabajo.REPROGRAMADA','orden_trabajo.INICIAL','orden_trabajo.COD_CATEGORIA','orden_trabajo.COD_ESPECIFICACION','orden_trabajo.ESTADO_PM','orden_trabajo.FACTURA')

        ->where('orden_trabajo.COD_USUARIO', session()->get('codigo'))
        ->where('orden_trabajo.ACTIVO', 1)
        ->where('orden_trabajo.APROBADA', 0)
        ->where('orden_trabajo.COBRADA', 0)
        ->where('orden_trabajo.ANULADA', 0)
        ->orderBy('orden_trabajo.FECHA_SERVICIO', 'desc')
        ->paginate(7);

        return [
            'pagination' => [
                'total'        => $cliente->total(),
                'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastItem(),
            ],
            'ot' => $cliente
        ];
    }

    public function plagas(Request $request)
    {
        if(!$request->ajax()) return redirect('/login');

        $cliente = DB::select('SELECT * from hsc_plagas where COD_ORDEN_TRABAJO='.$request->id);
        return $cliente;
    }

    public function guardar(Request $request){
        if(!$request->ajax()) return redirect('/login');

        try
        {
            DB::beginTransaction();
            $numero =  $this->objNroOrden();

            $OT = new OrdenTrabajo();
            $OT->COD_CLIENTE = $request->cliente;
            $OT->FECHA = $request->fechaServicio;
            $OT->FECHA_SERVICIO = $request->fechaServicio;
            $OT->HORA_SERVICIO = $request->horaServico;
            $OT->DATOS_FACTURA = "";
            $OT->COBRADA = 0;
            $OT->SUSPENDIDA=0;
            $OT->REPROGRAMADA=0;
            $OT->PAGO=0;
            $OT->ANULADA=0;
            $OT->MEMO_ANU="";
            $OT->FECHA_ANU=$request->fechaServicio;
            $OT->IMPRESION_RECIBO=0;
            $OT->ENVIO_EMAIL_CREDITO=0;
            $OT->TECNICO=0;
            $OT->FECHA_COBRADA=$request->fechaServicio;
            $OT->CERTIFICADO=0;

            $OT->RAZON_SOCIAL = $request->razonSocial;
            $OT->NIT = $request->nit;
            $OT->TIPO_PAGO = $request->tipoPago;
            $OT->PLAZO =0;
            $OT->PRECIO = $request->precio;
            $OT->BS = 0;
            $OT->OBSERVACIONES = $request->observaciones;
            $OT->NRO_TECNICOS = $request->nroTecnicos;
            $OT->TIEMPO_SERVICIO = $request->tiempoServicio;
            $OT->COD_EJECUTIVO_VENTAS = $request->ejecutivoVentas;
            $OT->COD_EQUIPO =  $request->equipo;
            $OT->RESPONSABLES = $request->reponsable;
            $OT->NRO_ORDEN = $numero;
            $OT->NRO_ORDEN_PADRE = 0;
            $OT->APROBADA = 0;
            $OT->INICIAL=1;
            $OT->CLIENTE_INICIAL = 0;
            $OT->COD_CATEGORIA = $request->categoria;
            $OT->COD_ESPECIFICACION = $request->especificacion;
            $OT->COD_USUARIO = session()->get('codigo');
            $OT->FECHA_ALTA = $request->fechaServicio;
            $OT->ACTIVO=1;
            $OT->ESTADO_FRECUENCIA=1;
            $OT->FRECUENCIA = $request->frecuencia;
            $OT->FACTURA=0;
            $OT->save(); 

            /*
            protected $primaryKey = 'COD_HS_PLAGAS';
            protected $fillable = ['COD_ORDEN_TRABAJO','COD_PLAGA','ACTIVO'];
            */

            $detallesPlaga = $request->dataPlaga;
            // ['COD_ORDEN_TRABAJO','COD_PLAGA','ACTIVO'];
            for($i = 0; $i < count($detalles); $i++) {
                $plaga = new HPlagas();
                $plaga->COD_PLAGA = $detallesPlaga[$i];
                $plaga->codReserva = $OT->COD_ORDEN_TRABAJO;
                $plaga->ACTIVO = 1;
                $detalle->save();
            }
            DB::commit();
        } 
        catch (\Exception $e){
            DB::rollBack();
        }   
    }

    public function objNroOrden(){
        if(!$request->ajax()) return redirect('/login');
        
        $cliente = DB::select('SELECT NRO_ORDEN FROM `orden_trabajo` WHERE YEAR(FECHA_ALTA) = YEAR(CURDATE()) ORDER BY NRO_ORDEN DESC LIMIT 0,1 ');
        $nroOrden=1;
        if(count($cliente)>0)
            $nroOrden=$cliente[0]->NRO_ORDEN+1;
        
		return $nroOrden;
    }


}
