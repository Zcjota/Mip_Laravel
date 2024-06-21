<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cot_Encabezado;

class EncabezadoController extends Controller
{
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;

        if($buscar==''){
            $encabezado=Cot_Encabezado::where('cot_encabezado_cotizacion.activo','=','1')
            ->orderBy('titulo','asc')->paginate(30);
        }
        else
        {
            $cargo=Cargo::where($criterio,'like','%'.$buscar.'%')->orderBy('nombre','asc')->paginate(30);
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $encabezado->total(),
                'current_page'=>$encabezado->currentPage(),
                'per_page'=> $encabezado->perPage(),
                'last_page'=>$encabezado->lastPage(),
                'from'=>$encabezado->firstItem(),
                'to'=>$encabezado->lastItem(),
            ],
            'encabezado'=>$encabezado
        ];

       
       
       
    }
    public function modificar(Request $request)
    {
       
        $encabezado= DB::update('update cot_encabezado_cotizacion set activo= '. '0' .
                 ' where id='.$request->idEncabezado);

       
        
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        DB::beginTransaction();
        $this->validarCampos($request);
        $encabezado =  new Cot_Encabezado();
        $encabezado->titulo=$request->titulo;
        $encabezado->revision=$request->revision;
        $encabezado->fecha=$request->fecha;
        $encabezado->codificacion=$request->codificacion;
        $encabezado->activo=1;
        $encabezado->save();
       // $this->bitacora('E',$cargo->id);
            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }
    public function ListarPdf()
    {
        
        $encabezado=Cot_Encabezado::select('cot_encabezado_cotizacion.titulo',
        'cot_encabezado_cotizacion.revision','cot_encabezado_cotizacion.fecha',
        'cot_encabezado_cotizacion.codificacion',)
        ->where('cot_encabezado_cotizacion.activo','=','1')
        ->orderBy('titulo','asc')->get();
        
        $pdf=\PDF::loadView('pdf.encabezadopdf',['encabezado'=>$encabezado]);
        return $pdf->download('encabezado.pdf');
    }

    public function validarCampos(Request $request){
        
        $this->validate($request,[
            'titulo'=>'required',
            'revision'=>'required',
            'fecha'=>'date',
            'codificacion'=>'required'
            
        ]);
     }
}
