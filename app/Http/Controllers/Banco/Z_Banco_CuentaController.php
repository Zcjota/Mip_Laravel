<?php

namespace App\Http\Controllers\Banco;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\Z_Banco_Cuenta_B;
use Illuminate\Support\Facades\DB;
use App\Model\Z_Banco;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
// use App\Model\Z_Banco_Cuenta_B;
use Exception;
use App\Exceptions\ExceptionError;
use App\Exceptions\ExceptionValidate;
use Illuminate\Support\Facades\Validator;

class Z_Banco_CuentaController extends ApiController
{
    public function ListarTodo()
    {
        $result = DB::table('z_banco as b')
            ->select('b.cod_b', 'b.sigla', 'b.nombre')
            ->where('b.ACTIVO', '=', '1')
            ->get();
        
        return $this->responseOk($result);
    }
    

    public function ListarDestino()
    {
        // if(!$request->ajax()) return redirect('/login');
        $result = DB::table('z_banco as bac')
        ->select('bac.cod_b', 'bac.sigla', 'bac.nombre') // Corregido aquí
        ->where('bac.ACTIVO','=','1')
        ->get();
    
        return response()->json([
            'result' => $result,
        ]);
    }    

    //----------------------------------------------------------------------------
    public function getAll(Request $request){
        if(!$request->ajax()) return redirect('/login');
        $query = DB::table('z_banco_cuenta as bc')
    ->join('z_banco as bo', 'bc.cod_b', '=', 'bo.cod_b')
    ->leftJoin('z_prov_pers as bpp', 'bpp.cod_bc', '=', 'bc.cod_bc')
    ->selectRaw('bc.cod_bc, bc.cod_b, bc.nro_cuenta, bc.moneda, bc.tipo, bo.nombre, bc.ACTIVO, bpp.nombre_completo as nombre_cuenta')
    ->where(function($query) use ($request) {
        $query->where("bc.nro_cuenta", "like", "%" . $request->buscar . "%")
              ->orWhere("bo.nombre", "like", "%" . $request->buscar . "%")
              ->orWhere("bpp.nombre_completo", "like", "%" . $request->buscar . "%"); 
    })
    ->where('bo.ACTIVO', '=', '1')
    ->where('bc.ACTIVO', '=', '1')
    ->groupBy('bc.nro_cuenta','bc.cod_bc', 'bc.cod_b', 'bc.moneda', 'bc.tipo', 'bo.nombre', 'bc.ACTIVO', 'bpp.nombre_completo') // Ajusta según las columnas que necesitas
    ->paginate(20);
        return $this->responseOk($query);
    }
    // En Z_Banco_CuentaController.php

public function checkAccountNumberExists(Request $request){
    $nro_cuenta = $request->nro_cuenta;

    $exists = Z_Banco_Cuenta_B::where('nro_cuenta', $nro_cuenta)->exists();

    return response()->json(['exists' => $exists]);
}

    public function getByID($cod_bc){
        // if(!$request->ajax()) return redirect('/login');
        $result = DB::table('z_banco_cuenta as bc')
            ->selectRaw(
                'bc.cod_bc,bc.cod_b,bc.nro_cuenta,bc.moneda,bc.tipo,bc.ACTIVO'
            )->whereRaw("bc.cod_bc=".$cod_bc."")->first();
        return $this->responseOk($result);
    }
    public function create(Request $request){
        // if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->data,
                [
                    'cod_b'=>'required',
                    'moneda' => 'required',
                    'tipo' => 'required',
                    // 'nro_cuenta' => 'required'
                    
                ]             
            );

            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }
            $obj = $request->data;
            // $obj["fecha_modificacion"] = Carbon::now()->toDateTimeString();
            // $obj["modificado_por"] = $this->userId();
            $obj["ACTIVO"] = 1;    
            $data = Z_Banco_Cuenta_B::create($obj);
            $data->save();
            DB::commit();
            $this->LogCreate("z_banco_cuenta",$data->cod_bc,$obj);
            return $this->responseOk($data->cod_bc, "Guardado Exitosamente");
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
    public function update(Request $request){
        if (!$request->ajax()) return redirect('/login');
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();

        try {
            $validator = Validator::make(
                $request->data,
                [
                    // 'cod.b'=>'required',
                    // 'nro.cuenta' => 'required',
                    'moneda' => 'required',
                    'tipo' => 'required'
                   
                ]             
            );
            if ($validator->fails()) {
                throw new ExceptionValidate('Error de Validacion', $validator);
            }
            $obj = $request->data;
            $data = Z_Banco_Cuenta_B::findOrFail($obj["cod_bc"]);
            $data->nro_cuenta = $obj["nro_cuenta"];
            $data->cod_b = $obj["cod_b"];
            $data->moneda = $obj["moneda"];
            $data->tipo = $obj["tipo"];
            // $data->fecha_modificacion = Carbon::now()->toDateTimeString();
            // $data->modificado_por = $this->userId();
            $data->save();
            DB::commit();
            $this->LogCreate("z_banco_cuenta",$data->cod_bc,$obj);
            return $this->responseOk($data->cod_bc, "Actualizado Exitosamente");
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
    public function delete(Request $request){
        if (!$request->ajax()) return redirect('/login');   
        if (!$this->userValid()) return $this->responseVal("Usuario no Valido");
        DB::beginTransaction();
        try {
            $data = Z_Banco_Cuenta_B::findOrFail($request->cod_bc);
            $data->ACTIVO = 0;           
            $data->save();
            DB::commit();
            // $this->LogCreate("z_movimiento_banco_cuenta",$data->cod_mbc, $request->cod_mbc);
            return $this->responseOk($data->cod_bc, "Registro Anulado Correctamente");
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
    
    
}
