<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h1 style="text-align: center"><b>LISTADO DE TRANSFERENCIAS BANCARIAS</b></h1>
                        <br />
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success square" v-on:click="openWindow('registrar')" title="Nuevo Registro">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    NUEVO
                                </button>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <select v-model="tipoFecha" class="form-control square" style="width:100px;" v-on:change="validateDate">
                                        <option value="1">HOY</option>
                                        <option value="2">MES ACTUAL</option>
                                        <option value="3">RANGO FECHAS</option>
                                    </select>
                                    <input v-model="fechaInicio" class="form-control" type="date" style="width:80px;" v-on:click="listData(1, searchData, fechaInicio, fechaFin)"/>
                                    <input v-model="fechaFin" class="form-control" type="date" style="width:80px;" v-on:click="listData(1, searchData, fechaInicio, fechaFin)"/>
                                    <input type="text" v-model="searchData" class="form-control" placeholder="Buscar">
                                    <button class="btn btn-icon square btn-primary" v-on:click="listData(1, searchData, fechaInicio, fechaFin)">
                                        <i class="fa fa-search" aria-hidden="true"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x:auto;">
                            <div class="panel-body table-responsive">
                                <div class="x_title"></div>
                                <div id="bodyListado">
                                    <table id="mainTableSG" class="tabla">
                                        <thead>
                                            <tr>
                                                <th style="width:20px;"></th>
                                                <th style="width:20px;"></th>
                                                <th>FECHA</th>
                                                <th>CTA. ORIGEN</th>
                                                <th>CTA. DESTINO</th>
                                                <th>MONTO BS</th>
                                                <th>MONTO USD</th>
                                                <th>GLOSA</th>
                                                <th>MODIFICADO POR</th>
                                                <th>FECHA MODIFICADO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="data in aData" :key="data.cod_mbc" style="height:50px" v-on:click="changeRowColor('mainTableSG')">
                                                <td>
                                                    <button v-if="data.ACTIVO==1" type="button" v-on:click="openWindow('actualizar', data)" class="btn btn-warning">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                    <button v-else disabled type="button" class="btn btn-warning">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                </td>
                                                <td v-if="data.ACTIVO==1">
                                                    <button type="button" class="btn btn-danger colorRojo" v-on:click="openWindow('anular', data)">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </td>
                                                <td v-else>
                                                    <button disabled type="button" class="btn btn-danger">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </td>
                                                <td>{{data.fecha}}</td>
                                                <td>{{data.bo_sigla}}-{{data.bo_nrocuenta}}-{{data.bo_moneda}}</td>
                                                <td>{{data.bd_sigla}}-{{data.bd_nrocuenta}}-{{data.bd_moneda}}</td>
                                                <td>{{data.importe_bs}}</td>
                                                <td>{{data.importe_usd}}</td>
                                                <td>{{data.glosa}}</td>
                                                <td>{{data.modificado_por}}</td>
                                                <td>{{data.fecha_modificacion}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="x_title"></div>
                            </div>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1, searchData)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="changePage(page, searchData)" >{{page}}</a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1, searchData)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- MODAL PRINCIPAL DE SOLICITUD -->
                <div class="modal fade" id="modal" :class="{'mostrar':modal}" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h4 class="modal-title titulo">{{modalTitle}}</h4>
                                <button type="button" class="close" v-on:click="closeWindow" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body modal-body-format">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Fecha:</label>
                                            <input type="date" class="form-control input-format" v-model="model.fecha"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Tipo Movimiento:</label>
                                            <select class="form-control select-format" v-html="htmlListTipoMovimiento" v-model="model.tipo_movimiento"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Banco origen:</label>
                                            <select v-model="model.cod_bc_origen" class="form-control select-format" >
                                                <option disabled :value="-1">Seleccione..</option>
                                                <!-- <option v-for="data in aDataBanckAcountOrigin" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta.slice(-5)}}-{{data.moneda}}</option> -->
                                                <option v-for="data in aDataBanckAcountOrigin" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta}}-{{data.moneda}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Banco Destino:</label>
                                            <select v-model="model.cod_bc_destino" class="form-control select-format" >
                                                <option disabled :value="-1">Seleccione..</option>
                                                <option v-for="data in aDataBanckAcountDestiny" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta}}-{{data.moneda}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Importe BS:</label>
                                            <input type="number" class="form-control input-format" v-model="model.importe_bs"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Importe USD:</label>
                                            <input type="number" class="form-control input-format" v-model="model.importe_usd"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <textarea data-length=256 class="form-control char-textarea textarea-format" placeholder="Glosa / Descripcion:" rows="2" v-model="model.glosa"></textarea>
                                        <small class=" btn-primary counter-value float-right"><span class="char-count">0</span> / 255 </small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" v-on:click="closeWindow" class="btn btn-danger">
                                    <i class="fa fa-arrow-circle-left"></i> Cancelar
                                </button>
                                <button type="button" v-if="modalAction==1" class="btn btn-success" v-on:click="saveRegister">
                                    <i class="fa fa-floppy-o"></i> Guardar
                                </button>
                                <button type="button" v-else class="btn btn-success" v-on:click="updateRegister">
                                    <i class="fa fa-floppy-o"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Anulacion -->
                <div class="modal fade" :class="{'mostrar':modalCancel}" aria-hidden="true">
                    <div class="modal-dialog modal-danger" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" v-text="modalTitle"></h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-on:click="closeWindow">
                                    <span aria-hidden="true">x</span>
                                </button>
                            </div>
                            <div class="modal-body" style="height:100px;">
                                <h4 v-text="modalMessage"></h4>
                            </div>
                            <div class="modal-footer">
                                <button type="button" v-on:click="closeWindow" class="btn btn-success">
                                    <i class="fa fa-arrow-circle-left"></i> Cancelar
                                </button>
                                <button type="button" class="btn btn-warning" v-on:click="unregister(model.cod_mbc)">
                                    <i class="fa fa-times"></i> Aceptar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</template>

<script>
    export default {
        data(){
            return {
                messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
                model:{
                    cod_mbc: 0,
                    fecha: this.fecha('actual'),
                    tipo_movimiento: 'TRANSFERENCIA',
                    cod_bc_origen: -1,
                    cod_bc_destino: -1,
                    importe_bs: 0,
                    importe_usd: 0,
                    glosa: '',
                    ACTIVO: 1
                },

                //Datos de tablas
                aData :[],
                aDataBanckAcountOrigin :[],
                aDataBanckAcountDestiny :[],

                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,

                // filtros Listado SG Contado
                tipoFecha: '1',
                searchData: '',
                fechaInicio: '',
                fechaFin: '',
                fechaActual: this.fecha('actual'),

                // modal
                modal : 0,
                modalCancel: 0,
                modalTitle : '',
                modalAction : 0,
                modalMessage : '',

                htmlListTipoMovimiento: '',
                camposRequeridos:[],
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                var from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods: {
            cleanUp(){
                this.camposRequeridos=[];
            },
            cleanForm(){
                let me = this;

                me.model.cod_mbc = 0;
                me.model.fecha = this.fecha('actual');
                me.model.tipo_movimiento = 'TRANSFERENCIA';
                me.model.cod_bc_origen = -1;
                me.model.cod_bc_destino = -1;
                me.model.importe_bs = 0;
                me.model.importe_usd = 0;
                me.model.glosa = '';
                me.model.ACTIVO = 1;
            },
            fecha(opc) {
                var result;
                var fecha = new Date(); //Fecha actual
                var mes = fecha.getMonth() + 1; //obteniendo mes
                var dia = fecha.getDate(); //obteniendo dia
                var año = fecha.getFullYear(); //obteniendo año
                var fechaFin = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
                var ultimoDia = fechaFin.getDate();
                if (dia < 10)
                    dia = '0' + dia; //agrega cero si el menor de 10
                if (mes < 10)
                    mes = '0' + mes
                switch (opc) {
                    case 'actual':
                        result = `${año}-${mes}-${dia}`;
                        break;
                    case 'inicio':
                        result = `${año}-${mes}-01`;
                        break;
                    case 'fin':
                        result = `${año}-${mes}-${ultimoDia}`;
                        break;
                }
                return result;
            },
            initialize() {
                let me = this;
                var fecha = me.fecha('actual');
                me.tipoFecha = "1";
                me.fechaInicio = fecha;
                me.fechaFin = fecha;
                me.fechaActual = me.fecha('actual');
                me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                me.listDataBankAcountOrigin();
                me.listDataBankAcountDestiny();
            },
            validateDate() {
                let me = this;
                var tipo = me.tipoFecha;
                switch (tipo) {
                    case "1":
                        var fecha_actual = me.fecha('actual');
                        me.fechaInicio=fecha_actual;
                        me.fechaFin=fecha_actual;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                        break;
                    case "2":
                        var fecha_inicio = me.fecha('inicio');
                        var fecha_fin = me.fecha('fin');
                        me.fechaInicio=fecha_inicio;
                        me.fechaFin=fecha_fin;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                        break;
                    case "3":
                        break;
                }
            },
            validateMainForm(){
                let me = this;
                if(me.model.cod_bc_origen == -1){
                    swal("Alerta de Mensaje", "Seleccione un Cuenta Banco Origen.", "warning");
                    return false;
                }
                if(me.model.cod_bc_destino == -1){
                    swal("Alerta de Mensaje", "Seleccione un Cuenta Banco Destino.", "warning");
                    return false;
                }
                if ((me.model.importe_bs == "" || me.model.importe_bs <= 0)&&(me.model.importe_usd == "" || me.model.importe_usd <= 0)){
                    swal("Alerta de Mensaje", "Ingrese un Importe Bs ó importe USD Validos.", "warning");
                    return false;
                }
                if (me.model.glosa == ""){
                    swal("Alerta de Mensaje", "Ingrese una glosa para la Solicitud.", "warning");
                    return false;
                } else if(me.model.glosa.length < 15){
                    swal("Alerta de Mensaje", "La Glosa Debe Tener un Mínimo de 15 Caracteres.", "warning");
                    return false;
                }
                return true;
            },
            listData(page, buscar, fechaInicio, fechaFin) {
                let me =this;
                var url = "/api/transferencias?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
                // var url = "/api/transferencias";
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.aData = resp.data;
                    me.pagination = {
                        'total' : resp.total,
                        'current_page' : resp.current_page,
                        'per_page' : resp.per_page,
                        'last_page' : resp.last_page,
                        'from' : resp.from,
                        'to' : resp.to,
                    };
                }).catch(function(error){
                    me.bugChecking(error);
                });
            },
            listDataBankAcountOrigin(){
                let me = this;
                axios.get('/api/ZBancoCuenta/ListarCbx').then(function (reponse){
                    me.aDataBanckAcountOrigin = reponse.data.result;
                }).catch(function(error){
                    me.bugChecking(error);
                });
            },
            listDataBankAcountDestiny(){
                let me = this;
                axios.get('/api/ZBancoCuenta/ListarCbx').then(function (reponse){
                    me.aDataBanckAcountDestiny = reponse.data.result;
                }).catch(function(error){
                    me.bugChecking(error);
                });
            },
            listTipoMovimiento() {
                let me = this;
                me.htmlListTipoMovimiento = "<option value='TRANSFERENCIA'>TRANSFERENCIA</option>";
            },
            changePage(page, buscar){
                let me = this;
                me.pagination.current_page = page;
                me.listData(page, buscar, me.fechaInicio, me.fechaFin);
            },
            changeRowColor(id) {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            saveRegister(){
                let me = this;
                if(!me.validateMainForm())
                    return;
                axios.post('/api/transferencias/registrar',{data: me.model}).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Movimiento de Cuentas", "Registrado Correctamente.", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Movimiento de Cuentas", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Movimiento de Cuentas", error.response.data.message, error.response.data.status);
                });
            },
            getDataByID(id){
                let me =this;
                var url = "/api/transferencias/"+id;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.model.cod_mbc = resp.cod_mbc;
                    me.model.fecha = resp.fecha;
                    me.model.tipo_movimiento = resp.tipo_movimiento;
                    me.model.cod_bc_origen = resp.cod_bc_origen;
                    me.model.cod_bc_destino = resp.cod_bc_destino;
                    me.model.importe_bs = resp.importe_bs;
                    me.model.importe_usd = resp.importe_usd;
                    me.model.glosa = resp.glosa;
                }).catch(function(error){
                    alert(error.response.message);
                });
            },
            updateRegister(){
                let me = this;
                if(!me.validateMainForm())
                    return;
                axios.put('/api/transferencias/actualizar',{data:me.model,}).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Movimiento de Cuentas", "Registro Actualizado Correctamente.", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Movimiento de Cuentas", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Movimiento de Cuentas", error.response.data.message, error.response.data.status);
                });
            },
            unregister(id){
                let me = this;
                axios.put('/api/transferencias/anular',{
                    cod_mbc: id
                }).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Movimiento de Cuentas", "Registro Anulado Correctamente", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Movimiento de Cuentas", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Movimiento de Cuentas", error.response.data.message, error.response.data.status);
                });
            },

            // Modal
            openWindow(action, data = []){
                let me = this;
                me.listTipoMovimiento();
                me.cleanUp();
                switch(action) {
                    case "registrar": {
                        me.modal = 1;
                        me.modalTitle='Nuevo Registro Movimiento de Cuentas';
                        me.modalAction=1;
                        break;
                    };
                    case "actualizar": {
                        me.modal = 1;
                        me.modalTitle='Actualizar Registro Movimiento de Cuentas';
                        me.modalAction = 2;
                        me.getDataByID(data.cod_mbc);
                        break;
                    };
                    case "anular": {
                        me.model.cod_mbc = data.cod_mbc;
                        me.modalCancel = 1;
                        me.modalTitle = 'Anular Registro';
                        me.modalAction = 1;
                        me.modalMessage = "Está Seguro de Anular el registro Seleccionado.?";
                        break;
                    }
                }
            },
            closeWindow(){
                this.modal = 0;
                this.modalCancel = 0;
                this.modalTitle = '';
                this.modalMessage = '';
                this.cleanForm();
            },
            bugChecking(error) {
            var array=[];
            var cadena = String(error);
                if(error.response.status==422){
                    array  = error.response.data.errors;
                } else {
                    if(error.response.status==419) {
                        this.cerrarSesion();
                    } else {
                        var cadena = String(error);
                        cadena = cadena.substr(cadena.length-3,3);
                        swal("Message: ", this.messageFailed +" "+cadena, "error");
                    }
                }
                return array;
            },
            cerrarSesion(){
                swal({
                    title: 'La Sesion ha Expirado, Debe Iniciar Sesion Nuevamente.',
                    type: 'warning',
                    confirmButtonColor: 'green',
                    confirmButtonText: 'Aceptar',
                    confirmButtonClass: 'btn btn-success',
                }).then((result) => {
                    if (result.value) {
                        location.href = "http://mip2024.isitecnologia.com/";
                    }
                })
            }
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style>
    .modal-content-format{
        position: absolute !important;
        width: 1000px !important;
        height: auto !important;
    }.model-content-format-2{
        width: 700px !important;
        height: 500px !important;
    }
    .colorRojo {
    background-color: crimson;
    }
    .rojo{
        color: crimson;
    }
    .colorVerde {
    background-color:green;
    }
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .modal-body {
        position: relative;
        padding: 20px;
        height: auto;
        overflow-y: scroll;
    }
    .selected{
        background-color: red;
    }
    .resetColorFila{
        background-color:aquamarine;
    }.lbl-format{
        font-style: italic;
        color: #000000;
        font-size: 12px;
        font-weight: bold;
        margin: 0;
    }.input-format{
        font-size: 12px;
        font-weight: bold;
    }.modal-body-format{
        background-color: #ebefe3 !important;
        /* background-color: #c4bcb5 !important;  */
    }.select-format{
        font-size: 11px;
    }.textarea-format{
        font-size: 11px;
        text-transform: uppercase;
    }.input-detalle-format{
        width: 80px !important;
        font-weight: bold;
        font-size: 14px;
    }.textarea-detalle-format{
        text-transform: uppercase;
        width: 270px;
        font-style: normal;
        color: #000000;
    }.tfoot-format{
        background-color: #7396a7;
    }.table-thead-td{
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }.table-tbody-td {
        font-size: 10px;
    }.table-tfoot-td{
        font-size: 12px;
        font-weight: bold;
    }.div-table-format{
        height: 330px;
        overflow-y: scroll;
    }
</style>