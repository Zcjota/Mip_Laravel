<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h1 style="text-align: center"><b>LISTADO DE CUENTAS BANCARIAS</b></h1>
                        <br />
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success square" v-on:click="openWindow('registrar')" title="Nuevo Registro">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    NUEVO
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" v-model="searchData" class="form-control" placeholder="Buscar">
                                        <button class="btn btn-icon square btn-primary" v-on:click="listData(1, searchData)">
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
                                                <th>BANCO</th>
                                                <th>NRO.CUENTA</th>
                                                <th>PROVEEDOR O PERSONAL</th>
                                                <th>MONEDA</th>
                                                <th>TIPO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="data in aData" :key="data.cod_bc" style="height:50px" v-on:click="changeRowColor('mainTableSG')">
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
                                                <td>{{data.nombre}}</td>
                                                <td>{{data.nro_cuenta}}</td>
                                                <td>{{data.nombre_cuenta}}</td>
                                                <td>{{data.moneda}}</td>
                                                <!-- <td>{{data.tipo}}</td> -->
                                                <td>
                                                    {{ data.tipo == 1 ? 'CUENTAS MIP' : data.tipo == 2 ? 'CUENTAS DESTINOS' : '' }}
                                                </td>
                                                
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
                                           <label class="lbl-format">Nro de Cuenta:</label>
                                           <input type="text" class="form-control input-format" v-model="model.nro_cuenta"/>
                                    </div>
                               </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Tipo:</label>
                                            <select class="form-control select-format" v-html="htmlListTipoMovimiento" v-model="model.tipo"></select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Moneda:</label>
                                            <select class="form-control select-format" v-html="htmlListMoneda" v-model="model.moneda"></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">BANCO:</label>
                                            <select class="form-control" v-model="model.cod_b">
                                            <option value="-1" disabled>Seleccione</option>             
                                            <option v-for="(item, index) in arrayBancoCuenta" :key="`option-${item.cod_b}-${index}`" :value="item.cod_b">{{item.sigla}}-{{item.nombre}}</option>
                                            <!-- <option v-for="item in arrayBancoCuenta" :key="item.cod_b" :value="item.cod_b">{{item.sigla}}-{{item.nombre}}</option> -->
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                       
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        
                                    </div>
                                </div>
                                <div class="row">
                                   
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" v-on:click="closeWindow" class="btn btn-danger">
                                    <i class="fa fa-arrow-circle-left"></i> Cancelar
                                </button>
                                <!-- <button type="button" v-if="modalAction==1" class="btn btn-success" v-on:click="checkAccountNumberAndSave">
                                    <i class="fa fa-floppy-o"></i> Guardar
                                </button> -->
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
                                <button type="button" class="btn btn-warning" v-on:click="unregister(model.cod_bc)">
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
                    cod_bc: 0,
                    cod_b: null,
                    moneda :"M/N", 
                    tipo: 1,
                    ACTIVO: 1
                },

                //Datos de tablas
                aData :[],
                arrayBancoCuenta: [],
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
                htmlListMoneda: '',
                // htmlListBanco: [],
                // htmlListBanco: '',
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
                me.model.cod_bc = 0;
                me.model.cod_b = 0;
                me.model.nro_cuenta =" ";
                // me.model.moneda = -1;
                // me.model.tipo= -1
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
                if(me.model.cod_b === null){
                    swal("Alerta de Mensaje", "Seleccione un Cuenta Banco.", "warning");
                    return false;
                }
                if (!me.model.nro_cuenta) {
                    swal("Alerta de Mensaje", "Ingrese el número de cuenta.", "warning");
                    return false;
                 }
                return true;
            },
            listData(page, buscar, fechaInicio, fechaFin) {
                let me =this;
                // var url = "/api/bancocuenta?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
                var url = "/api/bancocuenta?page="+page+"&buscar="+buscar;
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
                me.htmlListTipoMovimiento = "<option value='1'>CUENTAS MIP</option><option value='2'>CUENTAS DESTINOS</option>";
            },
            listMoneda() {
                let me = this;
                // me.htmlListMoneda = "<option value='M/N'>M/N</option>";
                me.htmlListMoneda = "<option value='M/N'>M/N</option><option value='M/E'>M/E</option>";
            },
            // listBanco() {
            //     let me = this;
            //     me.htmlListBanco = "<option value='1'>BANCO ECONOMICO S.A.</option>";
            // },
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
            checkAccountNumberAndSave(){
    let me = this;
    // Asegúrate de que el número de cuenta no esté vacío
    if (!me.model.nro_cuenta) {
        swal("Alerta de Mensaje", "Ingrese el número de cuenta.", "warning");
        return;
    }
    // Llamar a la API para verificar si el número de cuenta existe
    axios.post('/api/bancocuenta/Check2', { nro_cuenta: me.model.nro_cuenta }).then(function (response){
        if(response.data.exists){
            // Si el número de cuenta ya existe, mostrar un mensaje
            swal("Error", "El número de cuenta ya existe.", "error");
        } else {
            // Si el número de cuenta no existe, proceder a guardar
            me.saveRegister(); // O me.updateRegister(); dependiendo del caso
        }
    }).catch(function(error){
        console.log(error);
        swal("Error", "Hubo un problema al verificar el número de cuenta.", "error");
    });
},
            saveRegister(){
                let me = this;
                if(!me.validateMainForm())
                    return;
                axios.post('/api/bancocuenta/registrar',{data: me.model}).then(function (reponse){
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
                var url = "/api/bancocuenta/"+id;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.model.cod_bc = resp.cod_bc;
                    me.model.cod_b = resp.cod_b;
                    me.model.nro_cuenta = resp.nro_cuenta;
                    me.model.moneda = resp.moneda;
                    me.model.tipo = resp.tipo;
                }).catch(function(error){
                    alert(error.response.message);
                });
            },
            updateRegister(){
                let me = this;
                if(!me.validateMainForm())
                    return;
                axios.put('/api/bancocuenta/actualizar',{data:me.model,}).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Movimiento de Cuentas", "Registro Actualizado Correctamente.", "success");
                        // me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                        me.listData(1, me.searchData);
                    } else {
                        swal("Movimiento de Cuentas", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Movimiento de Cuentas", error.response.data.message, error.response.data.status);
                });
            },
            unregister(id){
                let me = this;
                axios.put('/api/bancocuenta/anular',{
                    cod_bc: id
                }).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Movimiento de Cuentas", "Registro Anulado Correctamente", "success");
                        me.listData(1, me.searchData);
                    } else {
                        swal("Movimiento de Cuentas", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Movimiento de Cuentas", error.response.data.message, error.response.data.status);
                });
            },
            listDataBankAcount(){
    let me = this;
    axios.get('/api/ZBancoCuentaDestiny/ListarCbx2').then(function (response) {
        const results = response.data.result;
        const uniqueResults = [];
        const map = new Map();
        for (const item of results) {
            if(!map.has(item.cod_b)){
                map.set(item.cod_b, true);    // set any value to Map
                uniqueResults.push({
                    cod_b: item.cod_b,
                    sigla: item.sigla,
                    nombre: item.nombre
                });
            }
        }
        me.arrayBancoCuenta = uniqueResults;
    }).catch(function(error) {
        me.bugChecking(error);
    });
},
            // Modal
            openWindow(action, data = []){
                let me = this;
                me.listTipoMovimiento();
                me.listMoneda();
                me.listDataBankAcount();
                me.cleanUp();
                switch(action) {
                    case "registrar": {
                        me.modal = 1;
                        me.modalTitle='Nuevo Registro de Cuentas';
                        me.modalAction=1;
                        break;
                    };
                    case "actualizar": {
                        me.modal = 1;
                        me.modalTitle='Actualizar Registro Movimiento de Cuentas';
                        me.modalAction = 2;
                        me.getDataByID(data.cod_bc);
                        break;
                    };
                    case "anular": {
                        me.model.cod_bc = data.cod_bc;
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