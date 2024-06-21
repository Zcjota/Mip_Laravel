<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/main', function () {
    return view('contenido/contenido');
})->name('main');

Route::get('/gasto_solicitud_imprimir', function () {
    return view('report\gasto_solicitud\index');
})->name("gasto_solicitud");



//Equipos
Route::get('/equipos/listarTecnicoEquipo', 'EquiposController@listadoEquipoConTecnicos');
Route::get('/equipos', 'EquiposController@index');
Route::get('/equipos/listadoActivo', 'EquiposController@listadoActivo');
Route::post('/equipos/registrar', 'EquiposController@store');
Route::put('/equipos/actualizar', 'EquiposController@update');
Route::get('/Mip/equipos/listar', 'EquiposController@listadoEquipo');
Route::get('/Mip/equipos/listarOperaciones', 'EquiposController@listadoEquipoOperaciones');

//Reserva
Route::get('/pruebaNro', 'ReservaController@objNroOrden');
Route::put('/reservas/vincularOT', 'ReservaController@vincularOT');
Route::get('/reserva/listarOTSinAsignacion', 'ReservaController@listarOTSinAsignacion');
Route::get('/reserva/obtCodPersonal', 'ReservaController@obtCodPersonal');
Route::get('/Mip/reservas/listarReserva', 'ReservaController@listarReserva');
Route::get('/Mip/reservas/listarReservaMenosOperaciones', 'ReservaController@listarReservaMenosOperaciones');
Route::get('/Mip/reservas/listarReservasOperaciones', 'ReservaController@listarReservasOperaciones');


Route::put('/reserva/Desactivar', 'ReservaController@desactivar');
Route::post('/Reserva/registrarOT', 'ReservaController@guardarOT');
Route::post('/reservas/validar', 'ReservaController@validarReserva');
Route::post('/reservas/validarAdmin', 'ReservaController@validarReservaAdmin');
Route::get('/reserva/verificarTecnicoDiaSig', 'ReservaController@verificarTecnicoDiaSig');
Route::post('/reservas/registrar', 'ReservaController@guardar');
Route::post('/reservas/registrarSinTecnicos', 'ReservaController@registrarSinTecnicos');
Route::post('/reservas/registrarReservaReplicacion', 'ReservaController@registrarReservaReplicacion');
Route::put('/reservas/modificarReserva', 'ReservaController@modificarReserva');


//Emision de Factura ----Ernesto



//

//DetalleReserva
Route::get('/DetalleReserva/listaDetalleReservasFechaActual', 'DetalleReservaController@listaDetalleReservasFechaActual');
Route::get('/listarTrabajadoresModificar', 'DetalleReservaController@listarTrabajadoresModificar');
Route::get('/DetalleReservaModificar', 'DetalleReservaController@listaReservaModificar');

//DetalleReservaMigracion
Route::get('/DetalleReservaMigracion/listaDetalleReservasFechaActualMigracion', 'DetalleReservaMigracionController@listaDetalleReservasFechaActualMigracion');

//Personal
Route::get('/personal', 'PersonalController@index');
Route::get('/personal/listadoActivo', 'PersonalController@listadoActivo');
Route::post('/personal/registrar', 'PersonalController@registrar');
Route::put('/personal/cambiarEstado', 'PersonalController@cambiarEstado');
Route::put('/personal/actualizar', 'PersonalController@actualizar');

//Tecnico
Route::get('/tecnico/listarReservasFechaUnTecnico', 'TecnicoController@listarReservasFechaUnTecnico');
Route::post('/tecnico/registrarReasignacion', 'TecnicoController@registrarReasignacion');
Route::get('/tecnico/listaTecnicoReservas', 'TecnicoController@listadoTecnicoReserva');
Route::get('/listaTecnico', 'TecnicoController@listadoTecnicos');
Route::get('/tecnico', 'TecnicoController@index');
Route::get('/tecnicos/listadoTecnicosFechaActual', 'TecnicoController@listadoTecnicosFechaActual');
Route::get('/tecnicos/listadoTecnicosFechaActualAdmin', 'TecnicoController@listadoTecnicosFechaActualAdmin');
Route::get('/tecnicos/listadoTecnicosFechaActualAdminAmbos', 'TecnicoController@listadoTecnicosFechaActualAdminAmbos');
Route::get('/tecnicos/listarOEF', 'TecnicoController@listarOEF');


//login
Route::get('/','Auth\LoginController@showLoginForm');
Route::get('/cerrar', function () {
    return view('principal');
});
//Route::get('/login', 'Auth\LoginController@login')->name('login');
Route::post('/login', 'UsuarioController@verificar')->name('login');
Route::get('/home', 'HomeController@index')->name('home');

//Menu
Route::get('/menu/Usuario', 'MenuController@listarMenu');
Route::get('/subMenu/listarMenus', 'MenuController@listarMenus');
Route::get('/subMenu', 'MenuController@index');
Route::get('/Menu/subMenu1', 'MenuController@listado');

Route::get('/Menu/subMenu10', 'MenuController@subMenu1');
Route::get('/Menu/Menu10', 'MenuController@Menu1');

//Cliente
Route::get('/Mip/Cliente/listadoActivo', 'ClienteController@listadoActivo');

Route::get('/Cliente', 'ClienteController@listado');
Route::get('/Mip/Cliente/listado', 'ClienteController@listaCliente');



Route::get('/Mip/Cliente/listaTipoCliente', 'ClienteController@listaTipoCliente');
Route::post('/cliente/registrar', 'ClienteController@registrar');

Route::get('/Mip/Cliente/ciudad', 'ClienteController@ciudad');
Route::get('/Mip/Cliente/ejecutivo', 'ClienteController@ejecutivo');
Route::put('/cliente/modificar', 'ClienteController@modificar');
Route::put('/cliente/cambiarEstado', 'ClienteController@cambiarEstado');





//Roles
Route::put('/roles/Desactivar', 'RolesController@desactivar');
Route::put('/roles/Activar', 'RolesController@activar');
Route::get('/rol/obtDetalleRoles', 'RolesController@obtDetalleRoles');
Route::get('/roles/listado', 'RolesController@listado');
Route::get('/roles/listadoActivos', 'RolesController@listadoActivos');
Route::post('/roles/registrar', 'RolesController@registrar');


//Detalle Equipo
Route::get('/detalleEquipo/listado', 'DetalleEquipoController@listado');


//Usuario
Route::put('/usuario/Desactivar', 'UsuarioController@desactivar');
Route::put('/usuario/Activar', 'UsuarioController@activar');
Route::get('/usuario/id', 'UsuarioController@obtId');
Route::get('/usuario/listado', 'UsuarioController@listado');
Route::post('/usuario/registrar', 'UsuarioController@registrar');
Route::post('/usuario/cerrarSession', 'UsuarioController@cerrarSession')->name('logout');

//Plagas
Route::get('/Mip/plagas/listar', 'PlagasController@listaPlagas');

//Lugares
Route::get('/Mip/lugares/listar', 'LugaresController@listaLugares');

//Orden Trabajo
Route::get('/Mip/ot/listar1', 'OrdenTrabajoController@listado1');
Route::get('/Mip/ot/listar', 'OrdenTrabajoController@listado');
Route::post('/Mip/ot/registrar', 'OrdenTrabajoController@guardar');
Route::get('/Mip/ot/idUsuario', 'OrdenTrabajoController@idUsuario');
Route::put('/ot/reprogramar', 'OrdenTrabajoController@reprogramar');
Route::put('/ot/suspender', 'OrdenTrabajoController@suspender');
Route::put('/ot/modificar', 'OrdenTrabajoController@modificar');




//reportes
Route::get('/Mip/listaOTEjecutivo', 'ReportesController@listadoOTEjecutivo');
Route::get('/Mip/listaOTEjecutivoFiltro', 'ReportesController@listadoOTEjecutivoFiltro');
Route::get('/Mip/listaFrecuencia', 'ReportesController@listadoFrecuencia');
Route::get('/Reportes/listarReservasMes', 'ReportesController@listarReservasMes');
Route::get('/Reportes/listarReservasMesF2', 'ReportesController@listarReservasMesF2');
Route::get('/Reportes/listarDetalleReservasMes', 'ReportesController@listarDetalleReservasMes');
Route::get('/Reportes/obtMes', 'ReportesController@obtMes');
Route::get('/Reportes/obtDiasMes', 'ReportesController@obtDiasMes');
Route::get('/pru', 'ReportesController@pru');



//Certificado Ejecutivo
Route::get('/Certificado/listar', 'CertificadoEjecutivoController@listadoCertificado');
Route::get('/Certificado/listadoOTCertificado', 'CertificadoEjecutivoController@listadoOTCertificado');
Route::post('/Certificado/registrar', 'CertificadoEjecutivoController@registrar');


//MAESTRO OEF - OPERACIONES EMPRESAS FIJAS
Route::get('/oef/listarDatos', 'MaestroOEFController@listarDatos');
Route::get('/oef/listar', 'MaestroOEFController@listaReservasPorGestion');
Route::get('/oef/listarTecnicosReserva', 'MaestroOEFController@listarTecnicosReserva');
Route::post('/oef/registrar', 'MaestroOEFController@registrar');
Route::get('/oef/listarDetalle', 'MaestroOEFController@listarDetalle');

//CUENTA

Route::get('/api/CuentaContable/listacuenta', 'Cuenta\ZCuentacontController@listacuenta');
Route::put('/CuentaContable/cambiarEstado', 'Cuenta\ZCuentacontController@cambiarEstado');
Route::get('/api/CuentaContable/Fondo','Cuenta\ZCuentacontController@selectFondo');
Route::get('/api/CuentaContable/NroCuenta','Cuenta\ZCuentacontController@selectNroCuenta');
Route::get('/api/CuentaContable/Cuenta','Cuenta\ZCuentacontController@selectCuenta');
Route::post('/CuentaContable/registrar', 'Cuenta\ZCuentacontController@registrar');
Route::put('/CuentaContable/modificar', 'Cuenta\ZCuentacontController@modificar');

//EXCEL
Route::get('/exportar-Cuentacontable', 'Cuenta\ZCuentacontController@exportarExcel')->name('exportar.Cuentacontable');

//Proveedor Personal
Route::get('/Mip/ProveedorPersonal/listado', 'ProveedorPersonalController@listaProveedorPersonal');
Route::put('/ProveedorPersonal/cambiarEstado', 'ProveedorPersonalController@cambiarEstado');
Route::get('/Mip/ProveedorPersonal/Fondo','ProveedorPersonalController@selectFondo');
Route::get('/Mip/ProveedorPersonal/NroCuenta','ProveedorPersonalController@selectNroCuenta');
Route::get('/Mip/ProveedorPersonal/Cuenta','ProveedorPersonalController@selectCuenta');
Route::post('/ProveedorPersonal/registrar', 'ProveedorPersonalController@registrar');
Route::put('/ProveedorPersonal/modificar', 'ProveedorPersonalController@modificar');
//EXCEL
Route::get('/exportar-personalProveedor', 'ProveedorPersonalController@exportarExcel')->name('exportar.personalProveedor');

//Combo 
#Banco
Route::get('/api/ZBanco/ListarCbx','Comunes\Combo\Z_BancoController@ListarTodo');
Route::get('/api/Cuenta/Buscar','Comunes\Paginate\CuentaController@Buscar');
Route::get('/api/ZBancoCuenta/ListarCbx','Comunes\Combo\Z_Banco_CuentaController@ListarTodo');
Route::get('/api/ZBancoCuentaDestiny/ListarCbx','Comunes\Combo\Z_Banco_CuentaController@ListarDestino');
Route::get('/api/ZBancoCuentaDestinyzprov/ListarCbxzprov','Comunes\Combo\Z_Banco_CuentaController@ListarDestinozpers');
Route::get('/api/ZProv_Pers/ListarCuentaCbx','Comunes\Combo\Z_Prov_PersController@ListarCuenta');
//Solicitud Gasto
Route::get('/api/ZSolicitudGasto/Buscar','Gastos\Z_Solicitud_GastoController@index');



// Route::get('/api/ZSolicitudGasto/TraerPorCuentaContable','Gastos\Z_Solicitud_GastoController@TraerPorCuentaContable');
Route::get('/api/ZSolicitudGasto/{cod_sg}','Gastos\Z_Solicitud_GastoController@get');
Route::put('/api/ZSolicitudGasto','Gastos\Z_Solicitud_GastoController@update');
Route::post('/api/ZSolicitudGasto','Gastos\Z_Solicitud_GastoController@store');
Route::delete('/api/ZSolicitudGasto/{cod_sg}','Gastos\Z_Solicitud_GastoController@delete');
Route::put('/api/ZSolicitudGasto/updateStatus','Gastos\Z_Solicitud_GastoController@updateEstado');
 
Route::get('/api/userLogin','Api\ApiController@user');

//Solicitud Ingreso
Route::get('/api/ZSolicitudIngreso/Buscar','Ingreso\Z_Solicitud_IngresoController@index');
Route::get('/api/ZSolicitudIngreso/{cod_si}','Ingreso\Z_Solicitud_IngresoController@get');
Route::put('/api/ZSolicitudIngreso','Ingreso\Z_Solicitud_IngresoController@update');
Route::post('/api/ZSolicitudIngreso','Ingreso\Z_Solicitud_IngresoController@store');
Route::delete('/api/ZSolicitudIngreso/{cod_si}','Ingreso\Z_Solicitud_IngresoController@delete');
Route::put('/api/ZSolicitudIngreso/updateStatus','Ingreso\Z_Solicitud_IngresoController@updateEstado');

Route::get('/api/userLogin','Api\ApiController@user');

//COMBO PARA OTRO INGRESO
Route::get('/api/Z_Persona/ListarCuentaCbxP','Comunes\Combo\Z_PersonaController@ListarCuenta');

//APROBACION INGRESOS
Route::get('/api/ZSolicitudIngreso/aprobacion/Buscar','Ingreso\Z_Solicitud_IngresoController@getDataforApproved');
Route::put('/api/ZSolicitudIngreso/aprobacion/aprobar','Ingreso\Z_Solicitud_IngresoController@ApprovedRejectRegister');
Route::put('/api/ZSolicitudIngreso/aprobacion/rechazar','Ingreso\Z_Solicitud_IngresoController@ApprovedRejectRegister');

//PDF
Route::get('/api/siIngresoPDF/{cod_si}','Ingreso\Z_Solicitud_IngresoController@getDataForPdfByID');
Route::get('/api/siIngresoDPDF/{cod_si}','Ingreso\Z_Solicitud_IngresoController@getDataForPdfByID');
// //PDF
// Route::get('/api/sgContadoPDF/{cod_si}','Ingreso\Z_Solicitud_IngresoController@getDataForPdfByID');
// Route::get('/api/sgCreditoPDF/{cod_si}','Ingreso\Z_Solicitud_IngresoController@getDataForPdfByID');

//Detalle de Ingreso
// Route::get('/api/ZDetalleSolicitudGasto/Buscar','Gastos\Z_Detalle_Solicitud_IngresoController@index');


//PDF
Route::get('/api/sgContadoPDF/{cod_sg}','Gastos\Z_Solicitud_GastoController@getDataForPdfByID');
Route::get('/api/sgCreditoPDF/{cod_sg}','Gastos\Z_Solicitud_GastoController@getDataForPdfByID');

//APROBACION 
Route::get('/api/ZSolicitudGasto/aprobacion/Buscar','Gastos\Z_Solicitud_GastoController@getDataforApproved');
Route::put('/api/ZSolicitudGasto/aprobacion/aprobar','Gastos\Z_Solicitud_GastoController@ApprovedRejectRegister');
Route::put('/api/ZSolicitudGasto/aprobacion/rechazar','Gastos\Z_Solicitud_GastoController@ApprovedRejectRegister');

// TRANSFERENCIAS
Route::get('/api/transferencias','Comunes\Combo\Z_Banco_CuentaController@getAll');
Route::get('/api/transferencias/{cod_mbc}','Comunes\Combo\Z_Banco_CuentaController@getByID');
Route::post('/api/transferencias/registrar','Comunes\Combo\Z_Banco_CuentaController@create');
Route::put('/api/transferencias/actualizar','Comunes\Combo\Z_Banco_CuentaController@update');
Route::put('/api/transferencias/anular','Comunes\Combo\Z_Banco_CuentaController@delete');

// BANCO CUENTA
Route::get('/api/bancocuenta','Banco\Z_Banco_CuentaController@getAll');
Route::get('/api/bancocuenta/{cod_bc}','Banco\Z_Banco_CuentaController@getByID');
Route::post('/api/bancocuenta/registrar','Banco\Z_Banco_CuentaController@create');
Route::put('/api/bancocuenta/actualizar','Banco\Z_Banco_CuentaController@update');
Route::put('/api/bancocuenta/anular','Banco\Z_Banco_CuentaController@delete');

Route::get('/api/bancocuenta/ListarTodo','Banco\Z_Banco_CuentaController@ListarTodo');
Route::post('/api/bancocuenta/Check2','Banco\Z_Banco_CuentaController@checkAccountNumberExists');


Route::get('/api/ZDetalleSolicitudGasto/Buscar','Gastos\Z_Detalle_Solicitud_GastoController@index');
Route::post('/api/ZControlCredito/registrar','ControlCredito\ZControlCreditoController@create');


// ASIGNAR PRESUPUESTO


Route::get('/api/asignarpresupuesto/list2','AsignarPresupuesto\AsignarPresupuestoController@listaAsignarP');
Route::post('/api/asignarpresupuesto/actualizarPresupuesto','AsignarPresupuesto\AsignarPresupuestoController@actualizarPresupuesto');
Route::post('/api/asignarpresupuesto/crearApertura','AsignarPresupuesto\AsignarPresupuestoController@crearApertura');
Route::get('/api/asignarpresupuesto/verificarApertura','AsignarPresupuesto\AsignarPresupuestoController@verificarApertura');


Route::get('/api/asignarpresupuesto/aperturarmes','AsignarPresupuesto\AsignarPresupuestoController@aperturarmes');
Route::get('/api/asignarpresupuesto/irUltimaApertura','AsignarPresupuesto\AsignarPresupuestoController@irUltimaApertura');


Route::get('/api/asignarpresupuesto/list3','AsignarPresupuesto\AsignarPresupuestoController@listaAsignarP2');

Route::get('/api/asignar-presupuesto/cuentas', 'AsignarPresupuesto\AsignarPresupuestoController@cuentas');
Route::post('/api/asignar-presupuesto/anadir-cuenta', 'AsignarPresupuesto\AsignarPresupuestoController@anadirCuentaApertura');
Route::post('/api/asignar-presupuesto/quitar-cuenta', 'AsignarPresupuesto\AsignarPresupuestoController@quitarCuentaDeApertura');
Route::post('/api/asignar-presupuesto/guardar-masivo', 'AsignarPresupuesto\AsignarPresupuestoController@guardarMasivo');
Route::post('/api/asignar-presupuesto/quitar-cuenta', 'AsignarPresupuesto\AsignarPresupuestoController@quitarCuenta');

Route::get('/exportar-asignarpresupuestadoG', 'AsignarPresupuesto\AsignarPresupuestoController@exportarExcel')->name('exportar.asignarpresupuestadoG');


// Control Presupuestado Global
Route::get('/api/controlpglobal/list2','ControlPresupuestado\ControlPresupuestadoGlobalController@listaPGlobal');
Route::get('/api/controlpglobal/list4','ControlPresupuestado\ControlPresupuestadoGlobalController@listaPGlobalDetalle');

// Reporte de Presupuestado Global
Route::get('/exportar-presupuestadoG', 'ControlPresupuestado\ControlPresupuestadoGlobalController@exportarExcel')->name('exportar.presupuestadoG');
Route::get('/exportar-detalle', 'ControlPresupuestado\ControlPresupuestadoGlobalController@exportarDetalle')->name('exportar.detalle');


//Registro Ventas

Route::get('/api/registroventas/Buscar','RegistroVentasAdm\RegistroVentasAdmController@BuscarRegistroVentas');
Route::get('/api/registroventas/{COD_ORDEN_TRABAJO}','RegistroVentasAdm\RegistroVentasAdmController@EditarRegistroVentas');
Route::post('/api/registroventas/guardar', 'RegistroVentasAdm\RegistroVentasAdmController@guardar');
Route::post('/api/registroventas/export', 'RegistroVentasAdm\RegistroVentasAdmController@exportRegistroVentas');
Route::post('/api/registroventas/desactivar', 'RegistroVentasAdm\RegistroVentasAdmController@desactivarRegistro');



// CONCILIACION
Route::get('/api/conciliacionS','Conciliacion\ZConciliacionController@getDataOTForReceipts');
Route::get('/api/conciliacionS/impresiones','Conciliacion\ZConciliacionController@getDataReceipts');
Route::post('/api/conciliacionS/registrar','Conciliacion\ZConciliacionController@create');
Route::get('/api/conciliacionS/pdf/{cod_ot}','Conciliacion\ZConciliacionController@getDataForPdfByID');
Route::get('/api/common/literal','Comunes\ConversorController@getLiteralNumber');

Route::get('/api/conciliacionS/ingresos','Conciliacion\ZConciliacionController@getDataOTForReceiptsCI');
Route::post('/api/conciliacionS/registrarci','Conciliacion\ZConciliacionController@createCI');

//CONCILIACION REPORTE
Route::get('/api/conciliacionS/list','Conciliacion\ZConciliacionController@getDataOTForReceiptsRCI');

//CONCILIACION OBSERVACION
Route::post('/api/conciliacionS/registrarciog','Conciliacion\ZConciliacionController@createCIog');
Route::post('/api/conciliacionS/registrarcio','Conciliacion\ZConciliacionController@createCIo');

Route::get('/api/numeroLetras', 'NumeroLetrasController@getCovertNumberToLiteral');

//REPORTE DE CONCILIACION
Route::get('/exportar-gastos', 'Conciliacion\ZConciliacionController@exportarExcel')->name('exportar.gastos');
Route::get('/exportar-ingresos', 'Conciliacion\ZConciliacionController@exportarExcel2')->name('exportar.ingresos');
Route::get('/exportar-cobranzas', 'Conciliacion\ZConciliacionController@exportarExcel3')->name('exportar.cobranzas');

// Route::get('/api/conciliacionS','RecibosOT\ZConciliacionController@getDataOTForReceipts');
// Route::get('/api/conciliacionS/impresiones','RecibosOT\ZConciliacionController@getDataReceipts');
// Route::post('/api/conciliacionS/registrar','RecibosOT\ZConciliacionController@create');
// Route::get('/api/conciliacionS/pdf/{cod_ot}','RecibosOT\ZConciliacionController@getDataForPdfByID');
// Route::get('/api/common/literal','Comunes\ConversorController@getLiteralNumber');

// Route::get('/api/numeroLetras', 'NumeroLetrasController@getCovertNumberToLiteral');

// RECIBOS
Route::get('/api/recibosOT','RecibosOT\ZRecibosOTController@getDataOTForReceipts');
Route::get('/api/recibosOT/impresiones','RecibosOT\ZRecibosOTController@getDataReceipts');
Route::post('/api/recibosOT/registrar','RecibosOT\ZRecibosOTController@create');
Route::get('/api/recibosOT/pdf/{cod_ot}','RecibosOT\ZRecibosOTController@getDataForPdfByID');
Route::get('/api/common/literal','Comunes\ConversorController@getLiteralNumber');

Route::get('/api/numeroLetras', 'NumeroLetrasController@getCovertNumberToLiteral');


Route::get('/api/listadoRecibosAnular', 'NumeroLetrasController@listadoRecibosAnular');

//  REPORTES
Route::get('/api/reporte/bancario','Reports\ReportsController@GetDataForBankReport');
Route::get('/api/reporte/resultado','Reports\ReportsController@GetDataForResultStatusReport');
Route::get('/api/reporte/combo/banco/origen','Reports\ReportsController@ListaBancoOrigen');

//  EXPORTS
Route::get('/api/reporte/bancario/exportar','Reports\ReportsController@ExportarToExcelDataBank');
Route::get('/api/reporte/resultado/exportar','Reports\ReportsController@ExportarToExcelStatusResult');

// SOLICITUD PRESTAMOS
Route::get('/api/prestamos','Prestamos\ZSolicitudPrestamosController@getAll');
Route::get('/api/prestamos/{cod_sp}','Prestamos\ZSolicitudPrestamosController@getByID');
Route::post('/api/prestamos/registrar','Prestamos\ZSolicitudPrestamosController@create');
Route::put('/api/prestamos/actualizar','Prestamos\ZSolicitudPrestamosController@update');
Route::put('/api/prestamos/anular','Prestamos\ZSolicitudPrestamosController@delete');

//APROBACION SOLICITUD DE PRESTAMOS
Route::get('/api/aprobacion/prestamos','Prestamos\ZSolicitudPrestamosController@getDataforApproved');
Route::put('/api/aprobacion/prestamos/aprobar','Prestamos\ZSolicitudPrestamosController@ApprovedRejectRegister');
Route::put('/api/aprobacion/prestamos/rechazar','Prestamos\ZSolicitudPrestamosController@ApprovedRejectRegister');
