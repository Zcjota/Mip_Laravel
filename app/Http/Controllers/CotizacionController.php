<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cot_Cat_Servicio;
use App\Cot_Cotizacion;
use App\Cot_Aplicaciones;
use App\Lugares;
use App\Cot_Detalle_Servicio;
use App\Cot_Detalle_Lugar;
use App\Cot_Notas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ghidev\Fpdf\Fpdf;
use App\BitacoraCotizacion;
use App\EXPORTS\reporte_cotizacionExport;
use App\EXPORTS\mypdf;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionController extends Controller
{
    public function selectServicio(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $servicio= Cot_Cat_Servicio::where('activo','=','1')
        ->select('id','nombre')->orderBy('nombre','asc')->get();
        return ['servicio' => $servicio] ;
        // return response()->json($request);
    }
    public function selectAplicaciones(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $aplicacion= Cot_Aplicaciones::where('activo','=','1')
        ->select('id','nombre')->orderBy('nombre','asc')->get();
        return ['aplicacion' => $aplicacion] ;
        // return response()->json($request);
    }
    public function selectLugar(Request $request){
        
        if(!$request->ajax()) return redirect('/login');
        $lugar= Lugares::where('ACTIVO','=','1')
        ->select('COD_LUGARES','DESCRIPCION')->orderBy('DESCRIPCION','asc')->get();
        return ['lugar' => $lugar] ;
        // return response()->json($request);
    }
    public function index(Request $request)
    {

        
        $buscar = $request->buscar;
        $criterio= $request->criterio;
        $idusuario=session()->get('codigo');
        if($buscar==''){
            
            $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
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



    public function registrar(Request $request)
    {
        
        //return response()->json($request); 
        $idusuario=session()->get('codigo');
        $idencabezado=DB::table('cot_encabezado_cotizacion')->where('activo',1)->value('id');
        
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
            DB::beginTransaction();
        $fecha_registro=date("Y-m-d H:i:s");
        $this->validarCamposRegistrar($request);
        $cotizacion= new Cot_Cotizacion();
        
        $cotizacion->fecha_cot=$request->fecha_cot;
        $cotizacion->id_personal=$idusuario;
        $cotizacion->empresa=strtoupper($request->nom_cliente);
        $cotizacion->telf_contacto=strtoupper($request->telf_contacto);
        $cotizacion->contacto=strtoupper($request->contacto);
        $cotizacion->id_cliente= $request->idcliente;
        
        $cotizacion->referencia=$request->referencia;
        $cotizacion->id_encabezado=$idencabezado;
        $cotizacion->creacion=$fecha_registro;
        $cotizacion->estado_cot=1;
        $cotizacion->total_cot=$request->montoTotal;
        $cotizacion->save();

        $detalles=$request->arrayDetalleServicio; //array detalle
            
        foreach($detalles as $ep=>$det)
        {
            $detalle= new Cot_Detalle_Servicio();
            $detalle->id_cotizacion=$cotizacion->id;
            $detalle->id_servicio=$det['idservicio'];
            $detalle->monto_sub=$det['monto'];
     
            $detalle->id_aplicaciones=$det['idaplicacion'];
            $detalle->activo=1;
            $detalle->save();
            //$this->bitacoraDetalle('R',$detalle->id);
            $detalles_lugar=$request->arrayDetalleLugar; //array detalle
            
            foreach($detalles_lugar as $ep=>$det)
            {
                if( $detalle->id_servicio==$det['idservicio'])
                {
                 $detalle_lugar= new Cot_Detalle_Lugar();
                 $detalle_lugar->id_detalle_servicio=$detalle->id;
                 $detalle_lugar->id_lugar=$det['idlugar']; 
                 $detalle_lugar->save();
                }
            }
        }
        $notas=$request->arrayNotas; //array detalle
            
        foreach($notas as $ep=>$det)
        {
            $nota= new Cot_Notas();
            $nota->id_cotizacion=$cotizacion->id;
            $nota->descripcion=$det['descripcion'];
            $nota->save();

        }

        $this->bitacora('Registrar',$cotizacion->id);
        DB::commit();
        }
       
        catch (Exception $e){
           
            DB::rollBack();
        }
        
       
    }
    public function ListarPdf(Request $request,$id)
    {

        $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
        ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
        ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
        ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
        'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
        'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia',
        'cot_encabezado_cotizacion.titulo',
        'cot_encabezado_cotizacion.revision','cot_encabezado_cotizacion.fecha',
        'cot_encabezado_cotizacion.codificacion'
        
        )
        ->where('cot_cotizacion.id','=',$id)
        ->orderBy('cot_cotizacion.fecha_cot','asc')->get();
        //return response()->json($cotizacion);

        $nota=Cot_Notas::select('cot_nota.descripcion')
        ->where('cot_nota.id_cotizacion','=',$id)
        ->orderBy('cot_nota.id','asc')->get();

      

        //return response()->json($nota);
        $nombre=session()->get('nombre');

        //detalleservicio
        $detalle_servicio=Cot_Detalle_Servicio::join('cot_cat_servicio','cot_detalle_servicio.id_servicio','=','cot_cat_servicio.id') 
            ->join('cot_aplicaciones','cot_detalle_servicio.id_aplicaciones','=','cot_aplicaciones.id')
            ->join('cot_detalle_lugar','cot_detalle_servicio.id','=','cot_detalle_lugar.id_detalle_servicio')
            ->join('lugares','cot_detalle_lugar.id_lugar','=','lugares.COD_LUGARES')
            ->select('cot_detalle_servicio.id','cot_cat_servicio.nombre as nombre_servicio',
            'cot_aplicaciones.nombre','cot_detalle_servicio.monto_sub'
            ,'lugares.COD_LUGARES as id_lugar','lugares.DESCRIPCION as nombre_lugar')
            ->where('cot_detalle_servicio.id_cotizacion','=',$id)
            ->orderBy('cot_detalle_servicio.id','asc')->get();
         // return response()->json($detalle_servicio);
       
         // $lugar=Lugares::orderBy('COD_LUGARES','asc')->get();

        //return response()->json($detalle_lugar);

        $pdf=\PDF::loadView('pdf.cotizacionpdf',['cotizacion'=>$cotizacion,'nota'=>$nota,'nombre'=> $nombre,'detalle_servicio'=>$detalle_servicio]);
        return $pdf->setPaper('a4', 'portrait')
        ->download('cotizacion.pdf');
    }

    public function validarCamposRegistrar(Request $request){
        $this->validate($request,[
            'referencia'=>'required',//validar nombre unico
            'contacto'=>'required',
            'telf_contacto'=>'required',
            'fecha_cot'=>'date',
            'montoTotal'=>'gt:0',
            'nom_cliente'=>'required'
            
        ]);
     }

     public function validarCamposNotas(Request $request){
         
        $this->validate($request,[
            'descripcion'=>'required'
              
        ]);  
       
     }
     public function validarCamposServicios(Request $request){
         
        $this->validate($request,[
            'id_servicio'=>'required',
            'id_aplicacion'=>'required',
            'monto'=>'gt:0',
              
        ]);  
       
     }
     public function validarCamposLugar(Request $request){
         
        $this->validate($request,[
            'id_servicioL'=>'required',
            'id_lugar'=>'required',
            
              
        ]);  
       
     }
     
     public function imprimir(Request $request){
      
       $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
       ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
       ->leftJoin('personal','cot_cotizacion.id_personal','=','personal.COD_PERSONAL') 
       ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
       'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
       'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia'
       
       )
       ->where('cot_cotizacion.id','=',$request->id)
       ->orderBy('cot_cotizacion.fecha_cot','asc')->get();
       
       $data=$cotizacion; //array detalle
        $fpdf = new mypdf('P','mm','letter');
        $fpdf->AddPage();

        $empresa='';
        $contacto='';
        $referencia='';
        $fecha_cot='';
        $telf_contacto='';
        $nombre=session()->get('nombre');
        foreach($data as $ep=>$det)
       {
         
         $empresa= $det['empresa'];
         $contacto= $det['contacto'];
         $referencia= $det['referencia'];
         $fecha_cot= $det['fecha_cot'];
         $telf_contacto= $det['telf_contacto'];
        }
      
     // $pdf->SetAutoPageBreak(true ,24);
		
		// $pdf-> SetFillColor(220,220,220);//RELLENO DE LA GRILLA
		$fpdf-> SetFillColor(250,250,250);//RELLENO DE LA GRILLA
		
		//******************************************************COPIA CLIENTE**************************************************
		$fpdf->SetLineWidth(.4);
		// $fpdf->SetFont('','B');
		$fpdf-> SetFont('Arial','',10);	
		// $fpdf->SetFont('Arial','',7);
		
		$fill = false;
	
		$fpdf -> Cell(5);$fpdf -> Cell(120,8,'Elaborado Por:  '.$nombre,1,0,'L',false); $fpdf -> Cell(65,8,'Fecha: '.$fecha_cot,1,0,'L',false);
		$fpdf->Ln();
		$fpdf -> Cell(5);$fpdf -> MultiCell(185,8,utf8_decode('Empresa / Organización:  ').$empresa,'LRB','L',false);
		$fpdf -> Cell(5);$fpdf -> Cell(120,8,'Persona de Contacto:  '.$contacto,'LRB','L',false); $fpdf -> Cell(65,8,utf8_decode('Telf de Contacto: ').$telf_contacto,1,0,'L',false);
        $fpdf->Ln();
        $fpdf -> Cell(5);$fpdf -> MultiCell(185,8,utf8_decode('Referencía:  ').$referencia,'LRB','L',false);		
		$fpdf-> SetFont('Arial','BU',12);	
		$fpdf->Ln();
		$fpdf -> Cell(5);$fpdf -> MultiCell(185,6,'SERVICIOS:','','L',false);		
		$fpdf-> SetFont('Arial','',12);	
		$fpdf->Ln();
		$fpdf -> Cell(5);$fpdf -> MultiCell(185,6,'MIP S.R.L. realiza una gran gama de diferentes servicios como ser:','','L',false);		
        $fpdf->Ln();
        //Categoria Servicio
        $servicio= Cot_Cat_Servicio::where('activo','=','1')
        ->select('id','nombre')->orderBy('nombre','asc')->get();
        $a=1;
        foreach($servicio as $ep=>$det)
        {
            if($a==1)
            {
                $fpdf -> Cell(5);$fpdf -> Cell(92.5,6,'-  '.$det['nombre'],0,0,'L',false);
                $a=0;
            }
            else{
                if($a==0)
                {
                    $fpdf -> Cell(92.5,6,'-  '.$det['nombre'],0,0,'L',false);
                    $a=1;
                    $fpdf->Ln();
                }
            }
           
            
        }
        $fpdf->Ln();
        $detalle_servicio=Cot_Detalle_Servicio::join('cot_cat_servicio','cot_detalle_servicio.id_servicio','=','cot_cat_servicio.id') 
        ->join('cot_aplicaciones','cot_detalle_servicio.id_aplicaciones','=','cot_aplicaciones.id')
        ->select('cot_detalle_servicio.id','cot_detalle_servicio.id_servicio','cot_cat_servicio.nombre as nombre_servicio',
        'cot_cat_servicio.descripcion'
       )
        ->where('cot_detalle_servicio.id_cotizacion','=',$request->id)
        
        ->orderBy('cot_detalle_servicio.id','asc')->get();
  
        foreach($detalle_servicio as $ep=>$det)
    {

        $fpdf-> SetFont('Arial','BU',12);	
        $fpdf->Ln();
        $fpdf -> Cell(5);$fpdf -> MultiCell(185,6,$det['nombre_servicio'],'','L',false);
        $fpdf->Ln();
        $fpdf-> SetFont('Arial','',10);
        $fpdf -> Cell(5);$fpdf -> MultiCell(185,5,utf8_decode($det['descripcion']),0,'L',0);  
        $fpdf->Ln();
    }
    
	//fin descripcion servicio	
        
        
        /*$fpdf-> SetTextColor('255','255','255');
        $fpdf-> SetFillColor('79','78','77');
        $fpdf-> SetFont('Arial','B',12);
        $fpdf -> Cell(5);$fpdf -> Cell(61.66,8,'TRATAMIENTOS ',1,0,'C',1);$fpdf -> Cell(61.66,8,'APLICACIONES  ',1,0,'C',1); $fpdf -> Cell(61.66,8,'PRECIO ',1,0,'C',1);
        $fpdf->Ln();*/
      
       

        //$fpdf-> rect(1,1,5,2,'D');
       // $fpdf->SetFont('Arial','B',12);
        
		// $this-> SetFillColor(220,220,220);//RELLENO DE LA GRILLA
		/*
        $fpdf->SetFont('Arial','B',12);
        $fpdf->Cell(4,1.5,' ',1,0,false);
        $fpdf->Cell(9.5,1.5,'Cotizacion ',1,0,false);
        $fpdf->Cell(9.5,1,'Cotizacion ',1,0,false);
        $fpdf->Ln();
        $fpdf->Ln();

        $fpdf-> SetFillColor(250,250,250);
        $fpdf->Cell(9.5,1,'Elaborado por : ',1,0,false);
        $fpdf->Cell(9.5,1,'Fecha: ',1,0,false);
        $fpdf->Ln();
        $fpdf->Cell(19,1,utf8_decode('Empresa u Organización : '),1,0,false);
        $fpdf->Ln();
        $fpdf->Cell(9.5,1,'Persona de Contacto : ',1,0,false);
        $fpdf->Cell(9.5,1,utf8_decode('Teléfono de Contacto: '),1,0,false);
        $fpdf->Ln();
        $fpdf->Cell(19,1,utf8_decode('Referencía : '),1,0,false);
        $fpdf->Ln();
        $fpdf->Ln();
        $fpdf->SetFont('Arial','BU',12);
        $fpdf->Cell(9.5,1,'SERVICIOS : ',0,0,false);
        
        $fpdf->AddPage();
        
        $fpdf->SetFont('Arial','B',12);
        $fpdf->Cell(6,1,'ESTADO',1,0,'C',1);
        $fpdf->Cell(6,1,'ID',1,0,'C',1);
        $fpdf->Cell(6,1,'MUNICIPIO',1,1,'C',1);
       $fpdf->text(0,15, $request->id);
       */

    
      //pagina 2
     
      $fpdf->AddPage();
     
      ///////////////////////////////////////////////////////////////////////////////////////////////
      //Detalle Servicio
        $fpdf-> SetFont('Arial','BU',12);	
        $fpdf -> Cell(5);$fpdf -> MultiCell(185,6,'PROPUESTA ECONOMICA','','C',false);
        $fpdf->Ln();
       $fpdf-> SetFont('Arial','B',12);
       $fpdf -> Cell(5);$fpdf -> Cell(61.66,6,'LUGARES ',1,0,'L',false);
       $fpdf -> Cell(73.32,6,'SERVICIOS  ',1,0,'L',false);
       $fpdf -> Cell(50,6,'PRECIO ',1,0,'L',false);
       $fpdf->Ln();
       $detalle_servicio=Cot_Detalle_Servicio::join('cot_cat_servicio','cot_detalle_servicio.id_servicio','=','cot_cat_servicio.id') 
       ->join('cot_aplicaciones','cot_detalle_servicio.id_aplicaciones','=','cot_aplicaciones.id')
       ->select('cot_detalle_servicio.id','cot_detalle_servicio.id_servicio','cot_cat_servicio.nombre as nombre_servicio',
       'cot_aplicaciones.nombre','cot_detalle_servicio.monto_sub'
      )
       ->where('cot_detalle_servicio.id_cotizacion','=',$request->id)
       
       ->orderBy('cot_detalle_servicio.id','asc')->get();
     
       foreach($detalle_servicio as $ep=>$det)
       {
             $detalle_lugar=Cot_Detalle_Lugar::join('lugares','cot_detalle_lugar.id_lugar','=','lugares.COD_LUGARES') 
             ->select('cot_detalle_lugar.id','cot_detalle_lugar.id_detalle_servicio','cot_detalle_lugar.id_lugar',
             'lugares.DESCRIPCION'
             )
             ->where('cot_detalle_lugar.id_detalle_servicio','=',$det['id'])
             ->orderBy('cot_detalle_lugar.id','asc')->get();
             $lugar="";
             foreach($detalle_lugar as $ep1=>$det1)
             {
                 $lugar= $lugar.$det1['DESCRIPCION'].",";
             }
             
             $cellWidth=61.59;//wrapped cell width
             $cellHeight=6;//normal one-line cell height
             $b=$fpdf->GetStringWidth($lugar);
             $fpdf-> SetFont('Arial','',10);
             //$textLength=strlen($lugar);
             //return response()->json($b);
            
             if($b < $cellWidth){
               // $b=$fpdf->GetStringWidth($lugar);
               // return response()->json($b);
                 //if not, then do nothing
                 $line=1;
                 //return response()->json($lugar);
             }
             else{
                 //if it is, then calculate the height needed for wrapped cell
                 //by splitting the text to fit the cell width
                 //then count how many lines are needed for the text to fit the cell
                // return response()->json($line);
                 $textLength=strlen($lugar);//total text length// longitud total del texto  
                    //return response()->json($textLength);                         
                 $errMargin=10;		//cell width error margin, just in case //margen de error de ancho de celda, por si acaso
                 $startChar=0;		//character start position for each line//posición de inicio de caracteres para cada línea
                 $maxChar=0;			//maximum character in a line, to be incremented later// carácter máximo en una línea, que se incrementará más tarde
                 $textArray=array();	//to hold the strings for each line//para mantener las cadenas para cada línea
                 $tmpString="";	
                 $x=0;	//to hold the string for a line (temporary)//o mantener la cadena para una línea (temporal)
                  // return response()->json($textLength);
                    while($startChar < $textLength)
                    { //loop until end of text
                        //loop until maximum character reached
                        while( 
                        $fpdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                        ($startChar+$maxChar) < $textLength )
                        {
                            $maxChar++;
                            $tmpString=substr($lugar,$startChar,$maxChar);
                             //return response()->json($fpdf->GetStringWidth( $tmpString ));
                        }
                        // return response()->json($tmpString);
                        //move startChar to next line
                        $startChar=$startChar+$maxChar;
                        //then add it into the array so we know how many line are needed
                        array_push($textArray,$tmpString);
                        
                        //reset maxChar and tmpString
                        $maxChar=0;
                        $tmpString=''; 
                    }
                     
                 //get number of line
                 //return response()->json($textArray);
                 $line=count($textArray);
                //return response()->json($line);
            }
             //return response()->json($line);
             $fpdf-> SetFont('Arial','',10);
             $xPos=$fpdf->GetX()+5;
             $yPos=$fpdf->GetY();
             $fpdf-> Cell(5);
             //$fpdf -> MultiCell(185,5,utf8_decode('-  '.$det['descripcion']),'LR','L',0);
             $fpdf->MultiCell($cellWidth,$cellHeight,utf8_decode($lugar),1,'L',0);
             
             //return the position for next cell next to the multicell
             //and offset the x with multicell width
             $fpdf->SetXY($xPos + $cellWidth , $yPos);
             //$fpdf -> Cell(61.66,6,$lugar,1,0,'L',false);
            
             $fpdf -> Cell(73.32,($line * $cellHeight), $det['nombre_servicio'].'('.$det['nombre'].')',1,0);
             $fpdf -> Cell(50,($line * $cellHeight),$det['monto_sub'],1,0,);
             $fpdf->Ln();
             
       }
              

       
        $total=DB::table('cot_cotizacion')->where('id',$request->id)->value('total_cot');
        $fpdf -> Cell(5);$fpdf -> Cell(134.98,6,'TOTAL',1,0,'L',false);
        $fpdf -> Cell(50,6,$total,1,0,'L',false);
        $fpdf->Ln();
        $fpdf->Ln();
        //notas
        $nota=Cot_Notas::select('cot_nota.descripcion')
        ->where('cot_nota.id_cotizacion','=',$request->id)
        ->orderBy('cot_nota.id','asc')->get();

        $fpdf-> SetFont('Arial','B',12);
        $fpdf -> Cell(5);$fpdf -> Cell(185,6,'NOTAS ',1,0,'C',false);
       // $fpdf->SetDrawColor(114,244,136);
        ///$fpdf->Line(15,178,200,178);
        $fpdf->Ln();
       
        
        foreach($nota as $ep=>$det)
        {
            $fpdf-> SetFont('Arial','',12);
            $fpdf -> Cell(5);
            $fpdf -> MultiCell(185,5,utf8_decode('-  '.$det['descripcion']),'LR','L',0);
            
        }
        $fpdf -> Cell(5);
        $fpdf -> MultiCell(185,5,'','LBR','L',0);
        ///////////////////////////////////////////////wrap multicell
        $fpdf->Ln();
        $fpdf->Ln();
        $fpdf -> Cell(5);
        $fpdf -> MultiCell(185,5
        ,
        utf8_decode('Esperamos que la presente cotización sea de su aceptación, en caso de requerir cualquier información adicional, estamos a su entera disposición. Aprovechamos la oportunidad para reiterarles nuestras consideraciones más distinguidas.
         ')
        ,
        0,'L',0);
        $fpdf->Ln();
        $fpdf -> Cell(5);
        $fpdf -> MultiCell(185,5,utf8_decode('Atentamente'),0,'L',0);
        $fpdf->Output();
        exit;
    
             
     }

     
     
     public function reporte(Request $request)
     {
         
         $buscar = $request->buscar;
         $buscar1 = $request->buscar1;
         $criterio= $request->criterio;
         $idusuario=session()->get('codigo');
       

         
         if($buscar=='' and $buscar1==''){
             
             $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
             ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
             
              ->leftJoin('usuario','cot_cotizacion.id_personal','=','usuario.COD_USUARIO') 
             ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
             'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
             'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia','cot_cotizacion.creacion'
             ,DB::raw('concat(usuario.NOMBRE," ",usuario.AP_PATERNO," ",usuario.AP_MATERNO) as nombre'))
             ->orderBy('cot_cotizacion.fecha_cot','desc')->paginate(30);
         }
         else
         {
             if($criterio=='Fecha')
             {
                if($buscar!='' and $buscar1!=''){
                    $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                    ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                    
                    ->leftJoin('usuario','cot_cotizacion.id_personal','=','usuario.COD_USUARIO') 
                    ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                    'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                    'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia','cot_cotizacion.creacion'
                    ,DB::raw('concat(usuario.NOMBRE," ",usuario.AP_PATERNO," ",usuario.AP_MATERNO) as nombre')
                    )
                    ->where('cot_cotizacion.fecha_cot','>=',$buscar)
                    ->where('cot_cotizacion.fecha_cot','<=',$buscar1)
                    //->where('cot_cotizacion.fecha_cot','like','%'.$buscar.'%')
                    
                    ->orderBy('cot_cotizacion.fecha_cot','desc')->paginate(30);
                }
                
             }
             if($criterio=='Empresa')
             {
                 $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                 ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                 
                 ->leftJoin('usuario','cot_cotizacion.id_personal','=','usuario.COD_USUARIO') 
                 ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                 'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                 'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia','cot_cotizacion.creacion'
                 ,DB::raw('concat(usuario.NOMBRE," ",usuario.AP_PATERNO," ",usuario.AP_MATERNO) as nombre')
                 )
                 ->where('cot_cotizacion.empresa','like','%'.$buscar.'%')

                 
                 ->orderBy('cot_cotizacion.fecha_cot','desc')->paginate(30);
               
             }
             if($criterio=='Contacto')
             {
                 $cotizacion=Cot_Cotizacion::leftJoin('cliente','cot_cotizacion.id_cliente','=','cliente.COD_CLIENTE')
                 ->leftJoin('cot_encabezado_cotizacion','cot_cotizacion.id_encabezado','=','cot_encabezado_cotizacion.id') 
                
                 ->leftJoin('usuario','cot_cotizacion.id_personal','=','usuario.COD_USUARIO') 
                 ->select('cot_cotizacion.id','cot_cotizacion.fecha_cot','cot_cotizacion.id_personal','cot_cotizacion.empresa',
                 'cot_cotizacion.contacto','cot_cotizacion.id_cliente','cot_cotizacion.id_encabezado','cot_cotizacion.estado_cot',
                 'cot_cotizacion.total_cot','cot_cotizacion.telf_contacto','cot_cotizacion.referencia','cot_cotizacion.creacion'
                 ,DB::raw('concat(usuario.NOMBRE," ",usuario.AP_PATERNO," ",usuario.AP_MATERNO) as nombre')
                 )
                 ->where('cot_cotizacion.contacto','like','%'.$buscar.'%')
                
                 ->orderBy('cot_cotizacion.fecha_cot','desc')->paginate(30);
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
     public function CambiarEstado(Request $request)
    {
        
        if(!$request->ajax()) return redirect('/login');
        $cotizacion =  Cot_Cotizacion::findOrFail($request->idCotizacion);
        $cotizacion->estado_cot=$request->estado;
        $cotizacion->save();
        if($request->estado==2)
        {
            $this->bitacora('Aprobar',$request->idCotizacion);
        }
        if($request->estado==3)
        {
            $this->bitacora('Rechazar',$request->idCotizacion);
        }
        
    }

    public function bitacora($accion,$codRegistro)
    {
        $bitacora = new BitacoraCotizacion();
        $bitacora->fechaRegistro = Now();
        $bitacora->accion = $accion;
        $bitacora->codRegistro = $codRegistro;
        $bitacora->codUsuario = session()->get('codigo');
        $bitacora->save();
    }

    public function export()
    {
       
       return Excel::download(new reporte_cotizacionExport, 'reporte.xlsx');
    }

   


    

   
   
}
