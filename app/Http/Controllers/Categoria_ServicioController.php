<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cot_Cat_Servicio;

class Categoria_ServicioController extends Controller
{
    public function index(Request $request)
    {
       
        $buscar = $request->buscar;
        
        if($buscar==''){
            $categoria_servicio=Cot_Cat_Servicio::orderBy('nombre','asc')->paginate(30);
        }
        else
        {
           
            
            $categoria_servicio=Cot_Cat_Servicio::where('cot_cat_servicio.nombre', 'like','%'.$buscar.'%')
            ->orderBy('cot_cat_servicio.nombre','asc')->paginate(30);
           
        }
        //return response()->json($tipo_construccion);
        return[
            'pagination'=>[
                'total' => $categoria_servicio->total(),
                'current_page'=>$categoria_servicio->currentPage(),
                'per_page'=> $categoria_servicio->perPage(),
                'last_page'=>$categoria_servicio->lastPage(),
                'from'=>$categoria_servicio->firstItem(),
                'to'=>$categoria_servicio->lastItem(),
            ],
            'categoria_servicio'=>$categoria_servicio
        ];

       
       
       
    }
    public function registrar(Request $request)
    {
        
        if(!$request->ajax()) return redirect('/login');
        try
        {
            
        //$this->validarCamposRegistrar($request);
        $categoria_servicio= new Cot_Cat_Servicio();
        $categoria_servicio->nombre= $request->nombre;
        $categoria_servicio->descripcion=$request->descripcion;
        $categoria_servicio->activo=1;
        $categoria_servicio->save();
       // $this->bitacora('R',$cargo->id);
        DB::commit();
        }
       
        catch (Exception $e){
           
            DB::rollBack();
        }
        
       
    }
    public function modificar(Request $request)
    {
     
        
        //return response()->json($request);
        
        if(!$request->ajax()) return redirect('/login');
        try
        {
            DB::beginTransaction();

        $fecha_registro=date("Y-m-d H:i:s");
        $categoria_servicio =  Cot_Cat_Servicio::findOrFail($request->idCategoria_Servicio);
        $categoria_servicio->nombre=$request->nombre;
        $categoria_servicio->descripcion=$request->descripcion;
        $categoria_servicio->activo=1;
        $categoria_servicio->fecha_update=$fecha_registro;
        $categoria_servicio->save();
       // $this->bitacora('E',$cargo->id);
            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
        }
    }

    public function CambiarEstado(Request $request)
    {
        
        if(!$request->ajax()) return redirect('/login');
        $categoria_servicio =  Cot_Cat_Servicio::findOrFail($request->idCategoria_Servicio);
        $categoria_servicio->activo=$request->estado;
        $categoria_servicio->save();
    }
    public function ListarPdf()
    {
        
        $categoria_servicio=Cot_Cat_Servicio::orderBy('nombre','asc')->get();
        $cont=Cot_Cat_Servicio::count();
        $pdf=\PDF::loadView('pdf.categoriapdf',['categoria'=>$categoria_servicio,'cont'=>$cont]);
        return $pdf->download('categoria.pdf');
    }

}
