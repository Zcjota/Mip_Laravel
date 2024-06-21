<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExceptionValidate;
use App\Http\Controllers\Controller;
use App\Model\Z_Logs;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function user()
    {
        return [
            "codigo" => session()->get('codigo'),
            "nombre" => session()->get('nombre'),
        ];
    }
    public function userId()
    {
        // return 2;
        return session()->get('codigo');
    }
    public function userValid()
    {
        if (session()->get('codigo') == null) return false;
        return true;
    }
    public function responseOk($result, string $message = null)
    {
        Log::info('Info Log MIP: User=>' . session()->get('codigo') . ' ::Result=>' . json_encode($result) . ' ::Message=> ' . $message);

        $response = [
            'result' => $result,
            'message' => $message,
            'status' => 'success',
        ];
        return response()->json($response, 200);
    }
    public function responseVal(string $message, ExceptionValidate $ex = null, int $code = 404)
    {
        $errorMessages = null;
        if ($ex != null) {
            $code = 422;
            $errorMessages = $ex->error();
            Log::warning('Warning Log MIP: User=>' . session()->get('codigo') . ' ::Message=>' . $message .
                ' ::Error Messages=> ' . json_encode($errorMessages) .
                ' ::ExceptionMessage=>' . $ex->getMessage() . ' ::Trace=>' . $ex->getTraceAsString());
        } else {
            Log::warning('Warning Log MIP: User=>' . session()->get('codigo') . ' ::Message=>' . $message);
        }
        $response = [
            'message' => $message,
            'errorMessages' => $errorMessages,
            'status' => 'warning',
        ];
        return response()->json($response, $code);
    }
    public function responseEx(Exception $ex, string $message = "Ups. un error ha ocurrido", int $code = 404)
    {
        Log::error('Error Log MIP: User=>' . session()->get('codigo') .
            ' ::Message=>' . $message .
            ' ::ExceptionMessage=>' . $ex->getMessage() . ' ::Trace=>' . $ex->getTraceAsString());
        $response = [
            'message' => $message,
            'error' => $ex->getMessage(),
            'status' => 'error',
        ];
        return response()->json($response, $code);
    }

    public function LogTxn(string $tabla, string $codigo, array $response, string $estado_txn, string $descripcion = "Sin Comentarios")
    {
        $data = new Z_Logs();
        $data->log_tabla = $tabla;
        $data->log_codigo = $codigo;
        $data->log_json = json_encode($response);
        $data->log_estado = $estado_txn;
        $data->log_descripcion = $descripcion;
        $data->log_fecha_registro = Carbon::now()->toDateTimeString();
        $data->log_cod_usuario = $this->userId();
        $data->save();
    }
    public function LogCreate(string $tabla, string $codigo, array $response, string $descripcion = "Sin Comentarios")
    {
        $this->LogTxn($tabla,$codigo,$response,"C",$descripcion);
    } 
    public function LogUpdate(string $tabla, string $codigo, array $response, string $descripcion = "Sin Comentarios")
    {
        $this->LogTxn($tabla,$codigo,$response,"U",$descripcion);
    }
    public function LogDelete(string $tabla, string $codigo, array $response, string $descripcion = "Sin Comentarios")
    {
        $this->LogTxn($tabla,$codigo,$response,"D",$descripcion);
    }
}
