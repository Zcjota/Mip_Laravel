
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.$ = window.jQuery = require('jquery');
window.Vue = require('vue');




import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'


import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { BootstrapVue, IconsPlugin, VBTogglePlugin } from 'bootstrap-vue';

window.Vue = require('vue');


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
Vue.component('v-select', vSelect);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VBTogglePlugin);
//en loss nombres, no se aceptar mayusculas
Vue.component('reserva', require('./components/Reserva.vue').default);
Vue.component('reserva1', require('./components/Reserva1.vue').default);
Vue.component('reservaadmin', require('./components/ReservaAdmin.vue').default);
Vue.component('reservaadmin2', require('./components/ReservaAdmin2.vue').default);
Vue.component('cliente', require('./components/Cliente.vue').default);

Vue.component('otvista', require('./components/OTVista.vue').default);
Vue.component('construccion', require('./components/Construccion.vue').default);
Vue.component('principal', require('./components/Principal.vue').default);
Vue.component('reporteejecutivo', require('./components/ReporteOTEjecutivo.vue').default);
Vue.component('reportefrecuencia', require('./components/ReporteFrecuencia.vue').default);
Vue.component('certificadoejecutivo', require('./components/CertificadoEjecutivo.vue').default);
Vue.component('reportereservaoperaciones', require('./components/ReporteReservaOperaciones.vue').default);
Vue.component('reportereservaoperacionesmigracion', require('./components/ReporteReservaOperacionesMigracion.vue').default);
Vue.component('reportereservaventa', require('./components/ReporteReservaVenta.vue').default);
Vue.component('personal', require('./components/Personal.vue').default);
Vue.component('roles', require('./components/Roles.vue').default);
Vue.component('reasignaciontecnico', require('./components/ReasignacionTecnico.vue').default);
Vue.component('cargando', require('./components/Cargando.vue').default);
Vue.component('asignartecnico', require('./components/ReservaAsignarTecnicosOperaciones.vue').default);
Vue.component('oef', require('./components/OEF.vue').default);
Vue.component('proveedorpersonal', require('./components/ProveedorPersonal.vue').default);

//reportes
Vue.component('reserva_mes', require('./components/reportes/ReservasMes.vue').default);
Vue.component('itinerarios-servicios', require('./components/reportes/Itinerarios.vue').default);
Vue.component('reserva_mes_f2', require('./components/reportes/ReservaMesF2.vue').default);
Vue.component('reporte_bancario', require('./components/reportes/ReporteBancario.vue').default);
Vue.component('reporte_estado_resultado', require('./components/reportes/ReporteEstadoResultado.vue').default);




//Reportes nuevo Ernesto 
Vue.component('emision_factura', require('./components/ReporteFacturacion/EmisionFactura.vue').default);



//Gastos Contado-Credito
Vue.component('solicitud_gasto_contado', require('./components/Gastos/SGContado.vue').default);
Vue.component('solicitud_gasto_credito', require('./components/Gastos/SGCredito.vue').default);
Vue.component('aprobacion_solicitud', require('./components/Gastos/AprobacionSG.vue').default);

//Ingreso Otros ingresos - Devolucion Personal
Vue.component('solicitud_ingreso', require('./components/Ingresos/SIIngreso.vue').default);
Vue.component('solicitud_ingreso_otros', require('./components/Ingresos/SIOtroingreso.vue').default);
Vue.component('aprobacion_solicitud_ingreso', require('./components/Ingresos/AprobacionSI.vue').default);

//Transferencia
Vue.component('movimiento_cuenta_banco', require('./components/Transferencias/MovimientoCuentaBanco.vue').default);

//Banco Cuenta
Vue.component('banco_cuenta', require('./components/Banco/BancoCuenta.vue').default);

//Cuenta Contables
Vue.component('cuenta_cont', require('./components/Cuenta/CuentasCont.vue').default);

//Control de  Pago
Vue.component('control_pago_credito', require('./components/ControlPago/ControlPagoCredito.vue').default);


// Recibos OT
Vue.component('control_recibos_ot', require('./components/RecibosOT/RecibosOT.vue').default);
Vue.component('recibos_ot', require('./components/RecibosOT/RecibosPDF.vue').default);

//Anular Recibos --Ernesto Noco
Vue.component('recibos_anular', require('./components/RecibosOT/RecibosAnular.vue').default);

// Ingresos Prestamos y aprobacion de prestamos

Vue.component('solicitud_prestamo', require('./components/Prestamos/SolicitudPrestamo.vue').default);
Vue.component('aprobacion_prestamo', require('./components/Prestamos/AprobacionPrestamo.vue').default);

// Conciliaciones

Vue.component('conciliacion_s', require('./components/Conciliacion/ConciliacionS.vue').default);
Vue.component('conciliacion_si', require('./components/Conciliacion/ConciliacionSI.vue').default);
Vue.component('reporte_ci', require('./components/Conciliacion/ReporteEgresoIngreso.vue').default);

//Asignar Cuenta

Vue.component('asignar_cuenta', require('./components/AsignarPresupuesto/AsignarPre.vue').default);

// Control Presupuestado Global

Vue.component('controlp-global', require('./components/ControlPresupuesto/ControlPGlobal.vue').default);

// Registro Ventas Adm
Vue.component('registro-ventasadm', require('./components/RegistroVentas/RegistroVentasAdm.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data() {
        return {
            panel: '',
            offset: 3,
            criterio: 'nombre',
            buscar: '',
            arraySubMenu: [],
            arrayMenu: [],
            arrayUsuarioMenu: [],
            codTipoUsu: 0,
            menu: 0,
            ok: 0,
            cod_usuario: "",
            nombre_usuario: "",
        }
    },

    methods: {

        obtIdUsuario() {
            let me = this;
            var url = '/usuario/id';

            axios.get(url).then(function (response) {
                me.codTipoUsu = response.data;
                localStorage.setItem("codTipoUsu", me.codTipoUsu);
            })
                .catch(function (error) {
                    //console.log(error);
                });
        },

        listarUsuario() {

            let me = this;
            var url = "/menu/Usuario";
            axios.get(url).then(function (response) {
                me.arrayUsuarioMenu = response.data;

                var vectorNombre = [];
                var vectorId = [];
                for (var v = 0; v < me.arrayUsuarioMenu.length; v++) {
                    if (!vectorNombre.includes(me.arrayUsuarioMenu[v].nombreMenu)) {
                        vectorNombre.push(me.arrayUsuarioMenu[v].nombreMenu);
                        vectorId.push(me.arrayUsuarioMenu[v].COD_MENU);
                        me.ok = parseInt(me.ok) + 1;
                    }
                }

                for (var i = 0; i < vectorNombre.length; i++) {
                    var car = { nombre: vectorNombre[i], codMen: vectorId[i] };
                    me.arrayMenu.push(car);
                }
            })
                .catch(function (error) {
                    console.log(error);
                });

        },
        userLogin() {
            let me = this;
            var url = '/api/userLogin';
            axios.get(url).then(function (response) {
                me.cod_usuario = response.data.codigo;
                me.nombre_usuario = response.data.nombre;
            })
                .catch(function (error) {
                    //console.log(error);
                });
        }
    },

    mounted() {
        //this.listarUsuario();
        this.userLogin();
        this.obtIdUsuario();
    }
});