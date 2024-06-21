<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::put('/api/ZSolicitudGasto','Gastos\Z_Solicitud_GastoController@update');
// Route::post('/api/ZSolicitudGasto','Gastos\Z_Solicitud_GastoController@store');

// Route::delete('/api/ZSolicitudGasto/{cod_sg}','Gastos\Z_Solicitud_GastoController@delete');

Route::post('/reservas/facturasPorEmitir', 'ReservaController@facturasPorEmitir');


// Route::get('/api/ZSolicitudGasto/TraerPorProveedor', 'Gastos\Z_Solicitud_GastoController@TraerPorProveedor');

