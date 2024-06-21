<?php

namespace App\EXPORTS;
use Ghidev\Fpdf\Fpdf;
use App\F_SolicitudEfectivo;

use DB;


class pdf extends Fpdf
{
    
    public function Header()
    {
      $idsolicitud=session()->get('idsolicitud');
        $titulo='';
        $revision='';
        $fecha='';
        $codificacion='';
   
        $encabezado=F_SolicitudEfectivo::join('fin_tiposolicitud','fin_solicitud_efectivo.COD_TSE','=','fin_tiposolicitud.COD_TSE')
        ->join('fin_area','fin_solicitud_efectivo.COD_AREA','=','fin_area.COD_AREA') 
        ->select('fin_solicitud_efectivo.COD_SE','fin_solicitud_efectivo.FECHA','fin_tiposolicitud.NOMBRE','fin_tiposolicitud.REV_ISO','fin_tiposolicitud.FECHA_ISO',
        'fin_tiposolicitud.COD_ISO','fin_tiposolicitud.ORIGEN','fin_area.DESCRIPCION','fin_solicitud_efectivo.TIPO_EFECTIVO'
       )
        ->where('fin_solicitud_efectivo.COD_SE','=',$idsolicitud)
        ->orderBy('fin_solicitud_efectivo.COD_SE')->get();
        
        
        foreach($encabezado as $ep=>$det)
        {
          $titulo= $det['NOMBRE'];
          $revision= $det['REV_ISO'];
          $fecha= $det['FECHA_ISO'];
          $codificacion= $det['COD_ISO'];
        
         }
        
        
        $this->Image('img/encabezado_cotizacion.jpeg',25,4,23,20);
        $this-> Image('img/marcaagua.jpeg',47,45,180);	
        $this-> rect(15,5,50,22);
        // $this-> SetFillColor(220,220,220);//RELLENO DE LA GRILLA
        $this-> SetFillColor(250,250,250);//RELLENO DE LA GRILLA
        $this-> SetFont('Arial','B',6);			
        $this->Ln(13);
        $this -> Cell(-18);
        $this -> Cell(90,5,'CASA MATRIZ SANTA CRUZ',0,0,'C',false);
        $this->Ln(-18);
        $this-> SetFont('Arial','B',14);			
        $this-> SetLineWidth(0.2);
        $this -> Cell(55);$this -> Cell(80,22,$titulo,1,0,'C',false);
        // $this->Ln();
        // $this -> Cell(10);
        $this-> SetFont('Arial','',12);			
        $this -> Cell(55,7.33,'Revision: '.$revision,1,0,'C',false);
        $this->Ln();
        $this -> Cell(135);$this -> Cell(55,7.33,'Fecha:'.$fecha,1,0,'C',false);
        $this->Ln();
        $this -> Cell(135);$this -> Cell(55,7.33,$codificacion,1,0,'C',false);
        $this->Ln();
        $this->Ln();
        
    }

    public function Footer()
    {
        $this->SetFont('Arial','',7);
		$this->SetY(-25);
		$this-> Ln(3.2);
		$this->SetTextColor(164, 182, 11);
		$this->Cell(80,10,'telf:',0,0,'R');
		$this->SetTextColor(0);
		$this->Cell(10.7,10,'3.345678',0,0,'R');
		$this->SetTextColor(164, 182, 11);
		$this->Cell(7,10,'web:',0,0,'L');
		$this->SetTextColor(0);
		$this->Cell(19,10,'www.mipsrl.com',0,0,'R');
		$this-> Ln(3.3);
		$this->Cell(190,10,'Plan 12 de Hamacas calle 1 Oeste #17, Santa Cruz, Bolivia',0,0,'C');
		// Arial italic 8
		$this->SetFont('Arial','',9);
		// Posici�n: a 1,5 cm del final
		$this->SetY(-15);
		// N�mero de p�gina
		$this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
		
        
    }
    

 
}