<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CertificadoEjecutivo;
use App\OrdenTrabajo;
use App\Cliente;

class CertificadoEjecutivoController extends Controller
{
    /*
    protected $primaryKey = 'COD_CERTIFICADO';
    protected $fillable = ['COD_OT','COD_PARAMETRO','COD_DETALLE','FECHA_TRAT','FECHA_VENC','NRO','NOMB_PRINC','NOMB_SEC','SERVICIO','CREADO','MODIFICADO','ACTIVO'];
    */
    public function listadoCertificado(Request $request)
    {

        if($request->buscar==''){
            $certificado = CertificadoEjecutivo::join('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','certificado.COD_OT')
            ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
            ->join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
            ->select('certificado.COD_CERTIFICADO','certificado.COD_OT','certificado.COD_DETALLE','certificado.FECHA_TRAT','certificado.FECHA_VENC','certificado.NRO','certificado.SERVICIO','certificado.CREADO',
            'orden_trabajo.NRO_ORDEN','cliente.NOMBRE','cliente.AP_CLIENTE','cliente.AM_CLIENTE')
            ->where('certificado.ACTIVO',1)
            ->orderBy('certificado.CREADO','desc')
            ->paginate(20);
        }
        else
        {
            $certificado = CertificadoEjecutivo::join('orden_trabajo','orden_trabajo.COD_ORDEN_TRABAJO','=','certificado.COD_OT')
            ->join('personal','personal.COD_PERSONAL','=','orden_trabajo.COD_EJECUTIVO_VENTAS')
            ->join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
            ->select('certificado.COD_CERTIFICADO','certificado.COD_OT','certificado.COD_DETALLE','certificado.FECHA_TRAT','certificado.FECHA_VENC','certificado.NRO','certificado.SERVICIO','certificado.CREADO',
            'orden_trabajo.NRO_ORDEN','cliente.NOMBRE','cliente.AP_CLIENTE','cliente.AM_CLIENTE')
            ->where('certificado.ACTIVO',1)
            ->orWhere(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$request->buscar.'%')
            ->orWhere('orden_trabajo.NRO_ORDEN',$request->buscar)
            ->orWhere('certificado.COD_OT',$request->buscar)
            ->orderBy('certificado.CREADO','desc')
            ->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $certificado->total(),
                'current_page' => $certificado->currentPage(),
                'per_page'     => $certificado->perPage(),
                'last_page'    => $certificado->lastPage(),
                'from'         => $certificado->firstItem(),
                'to'           => $certificado->lastItem(),
            ],
            'certificado' => $certificado
        ];
        

    }

    public function listadoOTCertificado(Request $request)
    {
    
        if($request->buscar==''){
 
            $OT = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
            ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','orden_trabajo.FECHA_SERVICIO','orden_trabajo.NRO_ORDEN','cliente.NOMBRE',
            'cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.C_NOMB_P','cliente.C_NOMB_S','cliente.C_DIRECCION')
            ->where('orden_trabajo.ACTIVO',1)
            ->where('orden_trabajo.ANULADA',0)
            ->where('orden_trabajo.CERTIFICADO',0)
            ->where('orden_trabajo.CERTIFICADO',0)
            ->where('orden_trabajo.FECHA_SERVICIO','>=','2018-11-01')
            ->paginate(5);
        }
        else
        {
            $OT = OrdenTrabajo::join('cliente','cliente.COD_CLIENTE','=','orden_trabajo.COD_CLIENTE')
            ->select('orden_trabajo.COD_ORDEN_TRABAJO','orden_trabajo.COD_CLIENTE','orden_trabajo.FECHA_SERVICIO','orden_trabajo.NRO_ORDEN','cliente.NOMBRE',
            'cliente.AP_CLIENTE','cliente.AM_CLIENTE','cliente.C_NOMB_P','cliente.C_NOMB_S','cliente.C_DIRECCION')
            ->where('orden_trabajo.ACTIVO',1)
            ->where('orden_trabajo.ANULADA',0)
            ->where('orden_trabajo.CERTIFICADO',0)
            ->where('orden_trabajo.CERTIFICADO',0)
            ->where('orden_trabajo.FECHA_SERVICIO','>=','2018-11-01')
            //->where(DB::raw('concat(cliente.NOMBRE," ",cliente.AP_CLIENTE," ",cliente.AM_CLIENTE)'),'like','%'.$request->buscar.'%')
            ->where('NRO_ORDEN','like','%'.$request->buscar.'%')
                
            ->paginate(5);
        }
        return [
            'pagination' => [
                'total'        => $OT->total(),
                'current_page' => $OT->currentPage(),
                'per_page'     => $OT->perPage(),
                'last_page'    => $OT->lastPage(),
                'from'         => $OT->firstItem(),
                'to'           => $OT->lastItem(),
            ],
            'OT' => $OT
        ];


       
    }
    public function parametro()
	{
        $sql = DB::select('SELECT COD_PARAMETRO FROM parametro_certificado WHERE ACTIVO = 1 ORDER BY COD_PARAMETRO DESC LIMIT 0,1');
        return $sql[0]->COD_PARAMETRO;
	}
    public function nuevoNumero()
    {
        $nro = DB::select('SELECT * FROM `certificado` WHERE YEAR(FECHA_TRAT) = YEAR(CURDATE()) ORDER BY NRO DESC LIMIT 0,1');
        $nroOrden=1;
        if(count($nro)>0)
            $nroOrden=$nro[0]->NRO+1;
        
		return $nroOrden;
    }

    public function cero()
    {
        $numero = 11144;
		return str_pad($numero, 4, "0", STR_PAD_LEFT);
    }

    public function registrar(Request $request)
    {
        
        $this->validate($request,[
            'certificadoPrincipal'=>'required',
            'certificadoSecundario'=>'required',
            'direccion'=>'required',
            'descripcion'=>'required',
            'quimicos'=>'required',
            'nombreOT'=>'required',
            'descripcion'=>'required',
        ]);
        try
        {
            DB::beginTransaction();

            $certificado = new CertificadoEjecutivo();
            $certificado->COD_OT =$request->codOT;
            $certificado->COD_PARAMETRO=$this->parametro();
            $certificado->COD_DETALLE=$request->quimicos;
            $certificado->FECHA_TRAT=$request->fechaServicio;
            $certificado->FECHA_VENC=$request->fechaVencimiento;
            $certificado->NRO=$this->nuevoNumero();
            
            $certificado->NOMB_PRINC=$request->certificadoPrincipal;
            $certificado->NOMB_SEC=$request->certificadoSecundario;
            $certificado->SERVICIO=$request->descripcion;

            //$certificado->CREADO=date('Y-m-d h:m:s');
            $certificado->CREADO=date('Ymd');
            $certificado->MODIFICADO=date('Ymd');  
            $certificado->ACTIVO = 1;
            $certificado->save();
            

        
            $cliente = Cliente::findOrFail($request->codCliente);
            $cliente->C_NOMB_P = $request->certificadoPrincipal;
            $cliente->C_NOMB_S = $request->certificadoSecundario;
            $cliente->C_DIRECCION =  $request->direccion;
            $cliente->save();


            DB::commit();
        } 
        catch (Exception $e){
            DB::rollBack();
        }
        
        return $certificado->COD_CERTIFICADO;
    }
}
