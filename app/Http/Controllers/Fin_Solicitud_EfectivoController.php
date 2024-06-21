<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Fin_Solicitud_Efectivo;
use App\Fin_Detalle_Solicitud_Efectivo;

use App\F_TipoSolicitud;
use App\F_SolicitudEfectivo;
use App\F_Area;
use App\F_Cuenta;
use App\F_Detalle_Solicitud;
use Ghidev\Fpdf\Fpdf;
use App\EXPORTS\pdf;

class Fin_Solicitud_EfectivoController extends Controller
{
    public function imprimir(Request $request)
    {
    
    session()->put('idsolicitud',$request->id);
    
        //header
      $titulo='';
      $revision='';
      $fecha='';
      $codificacion='';
      $area='';
      $origen='';
      $fechaSE='';
      $nombre=session()->get('nombre');
      $tipo_efectivo='';
      $observacion='';
      $totalbs='';
      $totalusd='';
      $numero='';
      $encabezado=F_SolicitudEfectivo::join('fin_tiposolicitud','fin_solicitud_efectivo.COD_TSE','=','fin_tiposolicitud.COD_TSE')
      ->join('fin_area','fin_solicitud_efectivo.COD_AREA','=','fin_area.COD_AREA') 
      ->select('fin_solicitud_efectivo.COD_SE','fin_solicitud_efectivo.FECHA','fin_tiposolicitud.NOMBRE','fin_tiposolicitud.REV_ISO','fin_tiposolicitud.FECHA_ISO',
      'fin_tiposolicitud.COD_ISO','fin_tiposolicitud.ORIGEN','fin_area.DESCRIPCION','fin_solicitud_efectivo.TIPO_EFECTIVO','fin_solicitud_efectivo.OBSERVACION',
      'fin_solicitud_efectivo.TOTAL_USD','fin_solicitud_efectivo.TOTAL_BS','fin_solicitud_efectivo.NRO'
     )
      ->where('fin_solicitud_efectivo.COD_SE','=',$request->id)
      ->orderBy('fin_solicitud_efectivo.COD_SE','asc')->get();
      
      
      foreach($encabezado as $ep=>$det)
     {
       $titulo= $det['NOMBRE'];
       $revision= $det['REV_ISO'];
       $fecha= $det['FECHA_ISO'];
       $codificacion= $det['COD_ISO'];
       $area= $det['DESCRIPCION'];
       $origen= $det['ORIGEN'];
       $fechaSE= $det['FECHA'];
       $tipo_efectivo= $det['TIPO_EFECTIVO'];
       $observacion= $det['OBSERVACION'];
       $totalbs= $det['TOTAL_BS'];
       $totalusd= $det['TOTAL_USD'];
       $numero= $det['NRO'];
      }
      $fpdf = new pdf('P','mm','letter');
      $fpdf->AddPage();
      

     
    //////////////////////////
     $fpdf-> SetFont('Arial','',8);
    $fpdf -> Cell(5);$fpdf -> Cell(85,6,'Area : '.$area,'LTB',0,'L',false);
    $fpdf -> Cell(50,6,'','TB',0,'L',false);
    if( $numero<9)
    {
        $fpdf -> Cell(50,6,$origen.' - 00'.$numero,1,0,'L',false);
        $fpdf->Ln();
    }
    if($numero>9 and $numero<100)
    {
        $fpdf -> Cell(50,6,$origen.' - 0'.$numero,1,0,'L',false);
       $fpdf->Ln();
    }
    
    $fpdf-> SetFont('Arial','',8);
    $fpdf -> Cell(5);$fpdf -> Cell(85,6,'Solicitado por :  '.$nombre,1,0,'L',false);
    $fpdf -> Cell(50,6,'Firma ',1,0,'L',false);
    $fpdf -> Cell(50,6, 'Fecha: '.$fechaSE,1,0,'L',false);
    $fpdf->Ln();
    $fpdf->Ln();
    //al contado
    $fpdf-> SetFont('Arial','B',10);
    if($tipo_efectivo==1)
    {
        $fpdf -> Cell(5);
        $fpdf -> Cell(184.98,6, 'Tipo:    Contado(X)      Credito( )',1,0,'C',false);
        $fpdf->Ln();
    }
    else{
        $fpdf -> Cell(5);
        $fpdf -> Cell(184.98,6, 'Tipo:    Contado( )      Credito(X)',1,0,'C',false);
        $fpdf->Ln();
    }
    ///////////////////////////////DETALLE/////////////////////////////////////////
    $fpdf->Ln();
    $fpdf-> SetFont('Arial','B',9);
      $fpdf -> Cell(5);
        $fpdf -> Cell(184.98,6, 'Detalle de las especificaciones',1,0,'C',false);
        $fpdf->Ln();

        $fpdf-> SetFont('Arial','B',10);
        $fpdf -> Cell(5);$fpdf -> Cell(61.66,6,'RESPONSABLE/PROVEEDOR ',1,0,'C',false);
        $fpdf -> Cell(73.32,6,'DETALLE  ',1,0,'C',false);
        $fpdf -> Cell(25,6,'IMPORTE Bs',1,0,'C',false);
        $fpdf -> Cell(25,6,'IMPORTE $us',1,0,'C',false);


        $detalle=F_Detalle_Solicitud::
        select('fin_detalle_solicitud.RESP_PROV','fin_detalle_solicitud.DETALLE','fin_detalle_solicitud.IMPORTE',
        'fin_detalle_solicitud.MONEDA')
        ->where('fin_detalle_solicitud.COD_SE','=',$request->id)
        ->get();
        /*$detalle=DB::table('fin_detalle_solicitud')
        ->select('fin_detalle_solicitud.RESP_PROV','fin_detalle_solicitud.DETALLE','fin_detalle_solicitud.IMPORTE',
        'fin_detalle_solicitud.MONEDA')
        ->where('fin_detalle_solicitud.COD_SE','=',$request->id)
        ->get();*/

        $data=$detalle;

        $responsable='';
        $sdetalle='';
        $importe='';
        $moneda='';
        

      //  return response()->json($data);
       
       
        foreach($data as $ep=>$det)
       {
        $responsable= $det['RESP_PROV'];
        $sdetalle= $det['DETALLE'];
        $importe= $det['IMPORTE'];
        $moneda= $det['MONEDA'];
          /*$responsable=$det->RESP_PROV;
          $sdetalle= $det->DETALLE;
          $importe= $det->IMPORTE;
          $moneda= $det->MONEDA;*/
        
        $fpdf-> SetFont('Arial','',9);	
        $fpdf->Ln();
        $fpdf -> Cell(5);$fpdf -> Cell(61.66,6,$responsable,1,0,'L',false);
        $fpdf -> Cell(73.32,6,$sdetalle,1,0,'L',false);
        if( $moneda==1)
        {
         $fpdf -> Cell(25,6,$importe,1,0,'C',false);
         $fpdf -> Cell(25,6,'-',1,0,'C',false);  
        }
        if( $moneda==2)
        {
            $fpdf -> Cell(25,6,'-',1,0,'C',false);
            $fpdf -> Cell(25,6,$importe,1,0,'C',false);  
        }
        
        
       }
        $fpdf->Ln();
        $fpdf -> Cell(5);
        $fpdf -> Cell(134.98,6, 'Total :',1,0,'L',false);
        $fpdf -> Cell(25,6, $totalbs,1,0,'C',false);
        $fpdf -> Cell(25,6, $totalusd,1,0,'C',false);
        $fpdf->Ln();



        $fpdf->Ln();
        $fpdf->Ln();
        $fpdf->Ln();


        //////////////////////////////////////////
        $fpdf-> SetFont('Arial','B',10);
        $fpdf -> Cell(5);$fpdf -> Cell(44.75,6,'Aprobado ',1,0,'C',false);
        $fpdf -> Cell(44.75,6,'Fecha ',1,0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(44.75,6,'Aprobado',1,0,'C',false);
        $fpdf -> Cell(44.75,6,'Fecha',1,0,'C',false);
        $fpdf->Ln();
        $fpdf -> Cell(5);$fpdf -> Cell(44.75,6,'Aprobado por: ','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,'Firma ','LR',0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(44.75,6,'Aprobado por: ','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,'Firma','LR',0,'C',false);
        $fpdf->Ln(4);
        $fpdf-> SetFont('Arial','',10);
        $fpdf -> Cell(5);$fpdf -> Cell(44.75,6,'Marco A. Rojas','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,' ','LR',0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(44.75,6,'Jaime P. Sandy','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,'','LR',0,'C',false);
        $fpdf->Ln(4);
        $fpdf -> Cell(5);$fpdf -> Cell(44.75,6,'Gutierrez ','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,'','LR',0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(44.75,6,'Gamarra','LR',0,'C',false);
        $fpdf -> Cell(44.75,6,'','LR',0,'C',false);
        $fpdf->Ln(4);
        $fpdf -> Cell(5);$fpdf -> Cell(44.75,6,'','LBR',0,'C',false);
        $fpdf -> Cell(44.75,6,'','LBR',0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(44.75,6,'','LBR',0,'C',false);
        $fpdf -> Cell(44.75,6,'','LBR',0,'C',false);
        $fpdf->Ln();
        $fpdf -> Cell(5);
        $fpdf -> Cell(184.98,6, utf8_decode('Nota:En la aprobación de esta solicitud bastara solo una firma.'),0,0,'C',false);
        $fpdf->Ln();
        $fpdf->Ln();
        ////////////////////////////////////////////////////////////////////////////////
        $fpdf-> SetFont('Arial','B',10);
        $fpdf -> Cell(5);$fpdf -> Cell(184.98,6,'OBSERVACIONES ',1,0,'C',false);
        $fpdf->Ln();
        $fpdf-> SetFont('Arial','',10);
        $fpdf -> Cell(5);
        $fpdf -> MultiCell(185,5,utf8_decode('-  '. $observacion),'LR','L',0);
        $fpdf -> Cell(5);
        $fpdf -> MultiCell(185,5,'','LBR','L',0);


        ///////////////////////////////////////////////////////////////////////////////////
        $fpdf->Ln();
        $fpdf->Ln();
        $fpdf -> Cell(5);
        $fpdf -> Cell(184.98,6, utf8_decode('Estas casillas seran llenadas por el area de contabilidad y finanzas'),0,0,'C',false);
        $fpdf->Ln();
        $fpdf-> SetFont('Arial','B',10);
        $fpdf -> Cell(5);$fpdf -> Cell(57.673,6,'Cerrado por :',1,0,'C',false);
        $fpdf -> Cell(5.98,6,' ',0,0,'C',false);
        $fpdf -> Cell(57.673,6,'Recibido por: ',1,0,'C',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(57.673,6,'Ingresado al Sistema por',1,0,'C',false);
        $fpdf->Ln();
        $fpdf-> SetFont('Arial','',10);
        $fpdf -> Cell(5);$fpdf -> Cell(57.673,6,'Nombre: :',1,0,'L',false);
        $fpdf -> Cell(5.98,6,' ',0,0,'C',false);
        $fpdf -> Cell(57.673,6,'Nombre: ',1,0,'L',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(57.673,6,'Nombre:',1,0,'L',false);
        $fpdf->Ln();
        $fpdf-> SetFont('Arial','',10);
        $fpdf -> Cell(5);$fpdf -> Cell(57.673,6,'Fecha: :',1,0,'L',false);
        $fpdf -> Cell(5.98,6,' ',0,0,'',false);
        $fpdf -> Cell(57.673,6,'Fecha: ',1,0,'L',false);
        $fpdf -> Cell(5.98,6,'',0,0,'C',false);
        $fpdf -> Cell(57.673,6,'Fecha:',1,0,'L',false);
        $fpdf->Ln();
        
         $fpdf->Output();
         exit;
     
              
      }


      
    public function index1(Request $request)
    {

        
        $buscar = $request->buscar;
        $criterio= $request->criterio;
        $idusuario=session()->get('codigo');
        if($buscar==''){
            
            $solicitud_efectivo=Fin_Solicitud_Efectivo::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
            ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
            ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
            ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
            'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
            'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
            )
            ->where('cot_cotizacion.id_personal','=',$idusuario)
            ->orderBy('cot_cotizacion.id','desc')->paginate(30);
        }
        else
        {
            if($criterio=='Fecha')
            {
                $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
                ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
                )
                ->where('cot_cotizacion.fecha_cot','like','%'.$buscar.'%')
                ->where('cot_cotizacion.id_personal','=',$idusuario)
                ->orderBy('cot_cotizacion.fecha_cot','asc')->paginate(30);
            }
            if($criterio=='Empresa')
            {
                $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
                ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
                )
                ->where('cot_cotizacion.empresa','like','%'.$buscar.'%')
                ->where('cot_cotizacion.id_personal','=',$idusuario)
                ->orderBy('cot_cotizacion.fecha_cot','asc')->paginate(30);
            }
            if($criterio=='Contacto')
            {
                $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
                ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
                )
                ->where('cot_cotizacion.contacto','like','%'.$buscar.'%')
                ->where('cot_cotizacion.id_personal','=',$idusuario)
                ->orderBy('cot_cotizacion.fecha_cot','asc')->paginate(30);
            }
           // $cotizacion=Cot_Cotizacion::where('nombre like','%'.$buscar.'%')->orderBy('nombre','asc')->paginate(30);
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $cotizacion->total(),
                'current_page'=>$cotizacion->currentPage(),
                'per_page'=> $cotizacion->perPage(),
                'last_page'=>$cotizacion->lastPage(),
                'from'=>$cotizacion->firstItem(),
                'to'=>$cotizacion->lastItem(),
            ],
            'cotizacion'=>$cotizacion
        ];
    }



    public function index(Request $request)
    {    
       //if(!$request->ajax())return redirect('/');//proteccion solicitud http
       /*
        if($request->fechaInicio!=''){
            if($request->fechaFin=='')
            $request->fechaFin = $request->fechaInicio;
          }
        if($request->fechaFin!=''){
            if($request->fechaInicio=='')
            $request->fechaInicio = $request->fechaFin;
          }
        */
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        $obj = DB::table('fin_solicitud_efectivo as se')
            ->join('fin_tiposolicitud as ts', 'ts.COD_TSE', '=', 'se.COD_TSE')
            ->join('cuenta as c', 'c.COD_CUENTA', '=', 'se.COD_CUENTA')
            ->join('fin_area as a', 'a.COD_AREA', '=', 'se.COD_AREA')
            ->select('se.*','ts.NOMBRE as tipoSolicitud','c.DESCRIPCION as cuenta','a.DESCRIPCION as area')
            ->where('se.SOLICITADO_POR',session()->get('codigo'))
            ->where($criterio,'like','%'.$buscar.'%')
            ->orderBy('se.COD_SE', 'desc')
            ->paginate(50);

        return [
            'pagination' => [
                'total'  => $obj->total(),
                "current_page" => $obj->currentPage(),
                "per_page" => $obj->perPage(),
                "last_page" => $obj->lastPage(),
                "from" => $obj->firstItem(),
                "to" => $obj->lastItem(),
            ],
            'solicitud' => $obj
        ];
    }


    public function listarDetalle(Request $request)
    {    

        $obj = DB::table('fin_detalle_solicitud as d')
            ->select('d.DETALLE as descripcion','d.CANT as cantidad','d.IMPORTE as precio','d.MONEDA as tipoMoneda','d.RESP_PROV as responsable')
            ->where('COD_SE',$request->codSolicitud)
            ->where('ACTIVO',1)

            ->paginate(50);

        return [
            'pagination' => [
                'total'  => $obj->total(),
                "current_page" => $obj->currentPage(),
                "per_page" => $obj->perPage(),
                "last_page" => $obj->lastPage(),
                "from" => $obj->firstItem(),
                "to" => $obj->lastItem(),
            ],
            'detalle' => $obj
        ];
    }


    public function objNro($tipo){
        $data = DB::select('SELECT NRO FROM fin_solicitud_efectivo WHERE COD_TSE='.$tipo.' and MONTH(FECHA) = MONTH(CURDATE()) ORDER BY NRO DESC LIMIT 0,1');
        $nro=1;
        if(count($data)>0)
            $nro=$data[0]->NRO+1;
        
		return $nro;
    }
    public function registrar(Request $request)
    {
        if(!$request->ajax())return redirect('/');//proteccion solicitud http

        $this->validate($request,[
            'area'=>'required',
            'tipoSolicitud'=>'required',
            'cuenta'=>'required',
            'tipoEfectivo'=>'required',
            'Observacion'=>'required',
        ]);

        try{
            DB::beginTransaction();

            $resp="";
            $resp = $this->validar($request->detalle);
            if($resp=="ok"){
                $numero =  $this->objNro($request->tipoSolicitud);
                $obj = new Fin_Solicitud_Efectivo();
                $obj->COD_TSE = $request->tipoSolicitud;
                $obj->COD_CUENTA = $request->cuenta;
                $obj->COD_AREA = $request->area;
                $obj->NRO = $numero; 
                $obj->SOLICITADO_POR = session()->get('codigo'); //DEFECTO
                $obj->TIPO_EFECTIVO = $request->tipoEfectivo;
                $obj->ESTADO = 0;
                $obj->ESTADO_RENDICION = 0;
                $obj->TOTAL_USD = $request->totalUsd;
                $obj->TOTAL_BS = $request->totalBs;
                $obj->RECIBIDO_POR = '';
                $obj->OBSERVACION = $request->Observacion;
                $obj->ACTIVO = 1;
                $obj->save(); 

                $detalles = $request->detalle;
                $this->registrarDetalle($detalles,$obj->COD_SE);
            }
            return $resp;
            DB::commit();
        } 
        catch (Exception $e){
            DB::rollBack();
        } 
    }
    public function validar($detalles)
    {
        $resp="ok";
        if(count($detalles)>0)
        {
            foreach($detalles as $ep=>$det)
            {
                if($det['descripcion']=="" || $det['cantidad']=="" || $det['precio']=="" || $det['responsable']==""){
                    $resp="DEBE INTRODUCIR LOS CAMPOS REQUERIDOS";
                    break;
                }
            }
        }
        else
            $resp="DEBE AGREGAR REGISTROS";

        return $resp;
    }


    public function modificar(Request $request)
    {
        if(!$request->ajax())return redirect('/');//proteccion solicitud http

        $this->validate($request,[
            'area'=>'required',
            'tipoSolicitud'=>'required',
            'cuenta'=>'required',
            'tipoEfectivo'=>'required',
            'Observacion'=>'required',
        ]);

        try{
            DB::beginTransaction();
            $resp="";
            $resp = $this->validar($request->detalle);
            if($resp=="ok")
            {
                $obj = Fin_Solicitud_Efectivo::findOrFail($request->codSolicitud);
                $obj->COD_TSE = $request->tipoSolicitud;
                $obj->COD_CUENTA = $request->cuenta;
                $obj->COD_AREA = $request->area;
                $obj->TIPO_EFECTIVO = $request->tipoEfectivo;
                $obj->TOTAL_USD = $request->totalUsd;
                $obj->TOTAL_BS = $request->totalBs;
                $obj->OBSERVACION = $request->Observacion;
                $obj->save(); 

                Fin_Detalle_Solicitud_Efectivo::where('COD_SE', $request->codSolicitud)
                ->update(['ACTIVO' => 0]);

                $detalles = $request->detalle;
                $this->registrarDetalle($detalles,$obj->COD_SE);
            }
            return $resp;
            DB::commit();
        } 
        catch (Exception $e){
            DB::rollBack();
        } 
    }


    public function registrarDetalle($detalles,$codSolicitud)
    {
        foreach($detalles as $ep=>$det)
        {
            $subTotal;
            if($det['tipoMoneda']==1)
                $subTotal = $det['precio'] * $det['cantidad'];
            else
                $subTotal =  number_format(($det['precio'] * $det['cantidad']/6.96), 2, '.', '');

            $objDet = new Fin_Detalle_Solicitud_Efectivo();
            $objDet->COD_SE = $codSolicitud;
            $objDet->RESP_PROV =$det['responsable'];
            $objDet->DETALLE = $det['descripcion'];
            $objDet->MONEDA = $det['tipoMoneda'];
            $objDet->IMPORTE = $det['precio'];
            $objDet->CANT = $det['cantidad'];
            $objDet->COD_UNIDAD = 0;
            $objDet->SUBTOTAL =$subTotal;
            $objDet->ACTIVO = 1;
            $objDet->save(); 
        }  
    }


    public function desactivar(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $obj = Fin_Solicitud_Efectivo::findOrFail($request->codigo);
            $obj->ACTIVO = $request->estado;
            $obj->save();

            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }
}
