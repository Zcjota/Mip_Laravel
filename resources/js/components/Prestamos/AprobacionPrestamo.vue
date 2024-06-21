<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h1 style="text-align: center"><b>APROBACION DE SOLICITUDES PRESTAMOS</b></h1>
                        <br />
                        <div class="row">
                            <div class="col-md-3">
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
                                    <table id="mainTable" class="tabla">
                                        <thead>
                                            <tr>
                                                <th style="width:20px;"></th>
                                                <th style="width:20px;"></th>
                                                <th style="width:20px;"></th>
                                                <th>PROVEEDOR</th>
                                                <th>BANCO ORIGEN</th>
                                                <th>BANCO DESTINO</th>
                                                <th>DIAS</th>
                                                <th>FECHA PAGO</th>
                                                <th>MONTO BS</th>
                                                <th>MONTO USD</th>
                                                <th>INTERES %</th>
                                                <th>INTERES BS</th>
                                                <th>INTERES USD</th>
                                                <th>ESTADO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="data in aDataAux" :key="data.cod_sp" style="height:50px" v-on:click="changeRowColor('mainTable')">
                                                <td>
                                                    <button type="button" v-on:click="openWindow('aprobar', data)" class="btn btn-success colorVerde">
                                                        <i class="fa fa-check"></i>Aprobar
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger colorRojo" v-on:click="openWindow('rechazar', data)">
                                                        <i class="fa fa-times"></i>Rechazar
                                                    </button>
                                                </td>
                                                <td>
                                                     <button type="button" v-on:click="openWindow('visualizar', data)" class="btn btn-warning">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td>{{data.proveedor}}</td>
                                                <td>{{data.banco_origen}}</td>
                                                <td>{{data.banco_destino}}</td>
                                                <td>{{data.dias}}</td>
                                                <td>{{data.fecha_pago}}</td>
                                                <td>{{data.importe_bs.toFixed(2)}}</td>
                                                <td>{{data.importe_usd.toFixed(2)}}</td>
                                                <td>{{data.interes_porcentaje}}%</td>
                                                <td>{{data.interes_bs.toFixed(2)}}</td>
                                                <td>{{data.interes_usd.toFixed(2)}}</td>
                                                <td>{{data.estado}}</td>
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
                                            <input disabled type="date" class="form-control input-format" v-model="model.fecha_pago"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Solicitado:</label>
                                            <input v-if="modalAction==1" disabled type="text" class="form-control input-format" v-model="$root.nombre_usuario"/>
                                            <input v-else disabled type="text" style="font-size:14px; font-weight: bold" class="form-control input-format" v-model="nombreUsuario"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Tipo Pago:</label>
                                            <select disabled class="form-control select-format" v-html="htmlListTipoPago" v-model="model.tipo_pago"></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Proveedor:</label>
                                            <div class="input-group">
                                                <input disabled type="text" class="form-control" v-model="model.proveedor"/>
                                                <button disabled type="button" class="btn btn-icon square btn-primary" title="Buscar Proveedor" v-on:click="openSearchProvider">
                                                    <i class="feather icon-search" aria-hidden="true">Buscar</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Banco origen:</label>
                                            <select disabled v-model="model.cod_bc_origen" class="form-control select-format" >
                                                <option disabled :value="-1">Seleccione..</option>
                                                <option v-for="data in aDataBanckAcountOrigin" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta}}-{{data.moneda}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Banco Destino:</label>
                                            <select disabled v-model="model.cod_bc_destino" class="form-control select-format" >
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
                                            <input disabled type="number" class="form-control input-format" v-model="model.importe_bs"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Importe USD:</label>
                                            <input disabled type="number" class="form-control input-format" v-model="model.importe_usd"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">% Credito:</label>
                                            <input disabled type="number" class="form-control input-format" v-model="model.interes_porcentaje"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="lbl-format">Dias:</label>
                                            <input disabled type="number" class="form-control input-format" v-model="model.dias"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <textarea disabled data-length=256 class="form-control char-textarea textarea-format" placeholder="Glosa / Descripcion:" rows="2" v-model="model.glosa"></textarea>
                                        <small class=" btn-primary counter-value float-right"><span class="char-count">0</span> / 255 </small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" v-on:click="closeWindow" class="btn btn-warning">
                                    <i class="fa fa-arrow-circle-left"></i> Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Aprobacion -->
                <div class="modal fade" :class="{'mostrar':modalAprobacion}" aria-hidden="true">
                    <div class="modal-dialog modal-success" role="document">
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
                                <button type="button" v-on:click="closeWindow" class="btn btn-danger">
                                    <i class="fa fa-arrow-circle-left"></i> Cancelar
                                </button>
                                <button type="button" class="btn btn-success" v-on:click="ApproveRegister(model.cod_sp)">
                                    <i class="fa fa-times"></i> Aceptar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Rechazo -->
                <div class="modal fade" :class="{'mostrar':modalRechazo}" aria-hidden="true">
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
                                <button type="button" class="btn btn-warning" v-on:click="RejectRegister(model.cod_sp)">
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
                nombreUsuario: '',
                model:{
                    cod_sp: 0,
                    cod_pp: -1,
                    proveedor: '',
                    tipo_pago: 'EFECTIVO',
                    cod_bc_origen: -1,
                    cod_bc_destino: -1,
                    fecha_pago: this.fecha('actual'),
                    importe_bs: 0,
                    importe_usd: 0,
                    interes_porcentaje: 0,
                    dias: 0,
                    glosa: '',
                    estado: '',
                    ACTIVO: 1
                },

                //Datos de tablas
                aData: [],
                aDataAux: [],
                aDataProvider: [],
                aDataBanckAcountOrigin: [],
                aDataBanckAcountDestiny: [],

                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                paginationProvider : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,

                // filtros Listado principal
                tipoFecha: '1',
                tipoEstado: 'ALL',
                fechaInicio: '',
                fechaFin: '',
                searchData: '',

                // filtros Proveedor
                searchProvider: '',
                pageProvider: 1,

                // modal
                modal : 0,
                modalAprobacion: 0,
                modalRechazo: 0,
                modalTitle : '',
                modalAction : 0,
                modalMessage : '',

                //modal proveedor
                modalProvider: 0,
                providerSelect: '',
                searchProviderFilter: '',

                htmlListTipoPago: '',
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

                me.model.cod_sp = 0;
                me.model.cod_pp = -1;
                me.model.proveedor = '';
                me.model.fecha_pago = this.fecha('actual');
                me.model.tipo_pago = 'EFECTIVO';
                me.model.cod_bc_origen = -1;
                me.model.cod_bc_destino = -1;
                me.model.importe_bs = 0;
                me.model.importe_usd = 0;
                me.model.interes_porcentaje = 0,
                me.model.dias = 0;
                me.model.glosa = '';
                me.model.estado = '';
                me.model.ACTIVO = 1;
                me.nombreUsuario = '';
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
                me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                me.listDataProvider(me.pageProvider, me.searchProvider);
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
                if(me.model.cod_pp == -1){
                    swal("Alerta de Mensaje", "Realize la Búsqueda y Seleccione un Proveedor", "warning");
                    return false;
                }
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
                if(me.model.interes_porcentaje == "" || me.model.interes_porcentaje <= 0){
                    swal("Alerta de Mensaje", "Ingrese un Porcentaje de Interes.", "warning");
                    return false;
                }
                if(me.model.dias == "" || me.model.dias <= 0){
                    swal("Alerta de Mensaje", "Ingrese un valor en el campo dias.", "warning");
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
                me.aDataAux = [];
                var url = "/api/aprobacion/prestamos?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.aData = resp.data;
                    for (let i = 0; i < me.aData.length; i++) {
                        me.aDataAux.push({
                            cod_sp: me.aData[i].cod_sp,
                            cod_pp: me.aData[i].cod_pp,
                            proveedor: me.aData[i].proveedor,
                            tipo_pago: me.aData[i].tipo_pago,
                            banco_origen: me.aData[i].bo_sigla+"-"+me.aData[i].bo_nrocuenta+"-"+me.aData[i].bo_moneda,
                            banco_destino: me.aData[i].bd_sigla+"-"+me.aData[i].bd_nrocuenta+"-"+me.aData[i].bd_moneda,
                            dias: me.aData[i].dias,
                            fecha_pago: me.aData[i].fecha_pago,
                            importe_bs: me.aData[i].importe_bs,
                            importe_usd: me.aData[i].importe_usd,
                            interes_porcentaje: me.aData[i].interes_porcentaje,
                            interes_bs: (me.aData[i].interes_porcentaje * me.aData[i].importe_bs)/100,
                            interes_usd: (me.aData[i].interes_porcentaje * me.aData[i].importe_usd)/100,
                            glosa: me.aData[i].glosa,
                            estado: me.aData[i].estado,
                            ACTIVO: me.aData[i].ACTIVO,
                        });
                    }
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
            listDataProvider(page, buscar) {
                let me =this;
                var url = '/api/ZProv_Pers/ListarCuentaCbx?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.aDataProvider = resp.data;
                    me.paginationProvider = {
                        'total' : resp.total,
                        'current_page' : resp.current_page,
                        'per_page' : resp.per_page,
                        'last_page' : resp.last_page,
                        'from' : resp.from,
                        'to' : resp.to,
                    }
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
            listTipoPago() {
                let me = this;
                me.htmlListTipoPago = "<option value='EFECTIVO'>EFECTIVO</option>";
                me.htmlListTipoPago += "<option value='CHEQUE'>CHEQUE</option>";
                me.htmlListTipoPago += "<option value='TRANSFERENCIA'>TRANSFERENCIA</option>";
            },
            changePage(page, buscar){
                let me = this;
                me.pagination.current_page = page;
                me.listData(page, buscar, me.fechaInicio, me.fechaFin);
            },
            changePageProvider(page, buscar){
                let me = this;
                me.paginationProvider.current_page = page;
                me.listDataProvider(page, buscar);
            },
            changeRowColor(id) {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            getDataByID(id){
                let me =this;
                var url = "/api/prestamos/"+id;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.model.cod_sp = resp.cod_sp;
                    me.model.cod_pp = resp.cod_pp;
                    me.model.proveedor = resp.proveedor;
                    me.model.fecha_pago = resp.fecha_pago;
                    me.model.tipo_pago = resp.tipo_pago;
                    me.model.cod_bc_origen = resp.cod_bc_origen;
                    me.model.cod_bc_destino = resp.cod_bc_destino;
                    me.model.importe_bs = resp.importe_bs;
                    me.model.importe_usd = resp.importe_usd;
                    me.model.interes_porcentaje = resp.interes_porcentaje;
                    me.model.dias = resp.dias;
                    me.model.glosa = resp.glosa;
                    me.nombreUsuario = resp.nombre_usuario;
                }).catch(function(error){
                    alert(error.response.message);
                });
            },
            // Modal
            ApproveRegister(id_sg){
                let me = this;
                axios.put('/api/aprobacion/prestamos/aprobar',{
                    cod_sp: id_sg,
                    estado: "APR"
                }).then(function (reponse){ 
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Aprobacion Solicitud Prestamo", "Registro Aprobado Correctamente", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Aprobacion Solicitud Prestamo", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Aprobacion Solicitud Prestamo", error.response.data.message, error.response.data.status);
                });
            },
            RejectRegister(id_sg){
                let me = this;
                axios.put('/api/aprobacion/prestamos/rechazar',{
                    cod_sp: id_sg,
                    estado: "REC"
                }).then(function (reponse){ 
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Aprobacion Solicitud Prestamo", "Registro Rechazado Correctamente", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Aprobacion Solicitud Prestamo", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Aprobacion Solicitud Prestamo", error.response.data.message, error.response.data.status);
                });
            },
            
            // Modal
            openWindow(action, data = []){
                let me = this;
                me.listTipoPago();
                me.cleanUp();
                switch(action) {
                    case "aprobar": {
                        me.model.cod_sp = data.cod_sp;
                        me.modalAprobacion = 1;
                        me.modalTitle = 'Aprobar Registro';
                        me.modalAction = 1;
                        me.modalMessage = "Está Seguro de Aprobar el Registro Seleccionado.?";
                        break;
                    };
                    case "rechazar": {
                        me.model.cod_sp = data.cod_sp;
                        me.modalRechazo = 1;
                        me.modalTitle = 'Rechazar Registro';
                        me.modalAction = 1;
                        me.modalMessage = "Está Seguro de Rechazar el Registro Seleccionado.?";
                        break;
                    };
                    case "visualizar": {
                        me.modal = 1;
                        me.modalTitle='Informacion de la Solicitud de Prestamo';
                        me.modalAction = 2;
                        me.getDataByID(data.cod_sp);
                        break;
                    }
                }
            },
            closeWindow(){
                this.modal = 0;
                this.modalAprobacion = 0;
                this.modalRechazo = 0;
                this.modalTitle = '';
                this.modalMessage = '';
                this.cleanForm();
            },
            openSearchProvider(){
                this.modalProvider = 1;
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