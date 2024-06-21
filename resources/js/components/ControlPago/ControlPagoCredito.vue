<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h1 style="text-align: center"><b>Control Cuentas por Pagar </b></h1>
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
                                                <th>&nbsp;&nbsp;
                                                    <input type="checkbox" v-model="selectAllDetail" @click="selectAll()">
                                                </th>
                                                <th>FECHA</th>
                                                <th>NRO SOLICITUD</th>
                                                <th>PROVEEDOR</th>
                                                <th>BANCO DESTINO</th>
                                                <th>IMPORTE BS</th>
                                                <th>IMPORTE USD</th>
                                                <th>GLOSA</th>
                                                <th>DETALLE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="data in aData" :key="data.cod_dsg" style="height:50px">
                                                <td>&nbsp;&nbsp;
                                                    <input type="checkbox" :value="data" v-model="selectedDetail">
                                                </td>
                                                <td>{{data.fecha_sg}}</td>
                                                <td>{{data.nroCorrelativo}}</td>
                                                <td>{{data.nombre_proveedor}}</td>
                                                <td>{{data.banco_destino}}</td>
                                                <td>{{data.importe_bs}}</td>
                                                <td>{{data.importe_usd}}</td>
                                                <td>{{data.glosa}}</td>
                                                <td>{{data.detalle}}</td>
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
                                            <label class="lbl-format">Banco origen:</label>
                                            <select v-model="model.cod_bc_origen" class="form-control select-format" >
                                                <option disabled :value="-1">Seleccione..</option>
                                                <!-- <option v-for="data in aDataBanckAcountOrigin" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta.slice(-5)}}-{{data.moneda}}</option> -->
                                                <option v-for="data in aDataBanckAcountOrigin" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta}}-{{data.moneda}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Total BS:</label>
                                            <input disabled type="number" class="form-control input-format" v-model="model.total_bs"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Total USD:</label>
                                            <input disabled type="number" class="form-control input-format" v-model="model.total_usd"/>
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
                selectAllDetail: false,
                selectedDetail: [],
                model:{
                    cod_control: 0,
                    fecha: this.fecha('actual'),
                    cod_bc_origen: -1,
                    total_bs: 0,
                    total_usd: 0,
                    glosa: '',
                    usuario_registro: -1,
                    fecha_registro: '',
                    ACTIVO: 1
                },

                //Datos de tablas
                aData :[],
                aDataBanckAcountOrigin :[],

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
                me.selectAllDetail = false;
                me.model.cod_control = 0;
                me.model.fecha = this.fecha('actual');
                me.model.cod_bc_origen = -1;
                me.model.total_bs = 0;
                me.model.total_usd = 0;
                me.model.glosa = '';
                me.model.usuario_registro = -1;
                me.model.fecha_registro = this.fecha('actual');
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
                if ((me.model.total_bs == "" || me.model.total_bs <= 0)&&(me.model.total_usd == "" || me.model.total_usd <= 0)){
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
                var url = "/api/ZDetalleSolicitudGasto/Buscar?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
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
            selectAll(){
                let me = this;
                me.selectedDetail = [];
                if (!me.selectAllDetail) {
                    for (let i in me.aData) {
                        me.selectedDetail.push(me.aData[i]);
                    }
                }
            },
            calculateDetailAmount(){
                let me = this;
                for (let i = 0; i < me.selectedDetail.length; i++) {
                    me.model.total_bs += me.selectedDetail[i].importe_bs;
                    me.model.total_usd += me.selectedDetail[i].importe_usd;
                }
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
                axios.post('/api/ZControlCredito/registrar',{data:me.model,detalle:me.selectedDetail}).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Control de Pagos", "Registrado Correctamente.", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Control de Pagos", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Control de Pagos", error.response.data.message, error.response.data.status);
                });
            },

            // Modal
            openWindow(action, data = []){
                let me = this;
                me.calculateDetailAmount();
                me.cleanUp();
                switch(action) {
                    case "registrar": {
                        me.modal = 1;
                        me.modalTitle='Nuevo Registro Control de Pagos';
                        me.modalAction=1;
                        break;
                    };
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