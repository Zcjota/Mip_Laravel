<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="">
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h1 style="text-align: center"><b>Control Orden Trabajo </b></h1>
                    <br />
                    <div class="row">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success square" v-on:click="openWindow('registrar')"
                                title="Nuevo Registro">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                NUEVO
                            </button>
                        </div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <select v-model="tipoFecha" class="form-control square" style="width:100px;"
                                    v-on:change="validateDate">
                                    <option value="1">HOY</option>
                                    <option value="2">MES ACTUAL</option>
                                    <option value="3">RANGO FECHAS</option>
                                </select>
                                <input v-model="fechaInicio" class="form-control" type="date" style="width:80px;" />
                                <input v-model="fechaFin" class="form-control" type="date" style="width:80px;" />
                                <input type="text" v-model="searchData" class="form-control" placeholder="Buscar">
                                <button class="btn btn-icon square btn-primary"
                                    v-on:click="listData(1, searchData, fechaInicio, fechaFin)">
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
                                            <th>&nbsp;&nbsp;Sel</th>
                                            <th>NRO</th>
                                            <th>CLIENTE</th>
                                            <th>IMPORTE</th>
                                            <th>FECHA SERVICIO</th>
                                            <th>TIPO PAGO</th>
                                            <th>PLAZO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="data in aData" :key="data.COD_ORDEN_TRABAJO" style="height:50px">
                                            <td>&nbsp;&nbsp;
                                                <input type="checkbox" :value="data" v-model="selectedOT">
                                            </td>
                                            <td>{{data.NRO_ORDEN}}</td>
                                            <td>{{data.CLIENTE}}</td>
                                            <td v-if="data.MONEDA == 1">{{data.PRECIO}} BOB</td>
                                            <td v-else>{{data.PRECIO}} USD</td>
                                            <td>{{data.FECHA_SERVICIO}}</td>
                                            <td v-if="data.TIPO_PAGO == 1">CONTADO</td>
                                            <td v-else>CREDITO</td>
                                            <td>{{data.PLAZO}} DIAS</td>
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
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page - 1, searchData)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page, searchData)">{{page}}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page + 1, searchData)">Sig</a>
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
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Fecha:</label>
                                        <input type="date" class="form-control input-format" v-model="model.fecha" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Realizado por:</label>
                                        <input disabled type="text" style="font-size:14px; font-weight: bold"
                                            class="form-control input-format" v-model="$root.nombre_usuario" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Recibi de:</label>
                                        <input type="text" style="font-size:14px; font-weight: bold"
                                            class="form-control input-format" v-model="model.recibi_de" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Tipo Pago:</label>
                                        <select class="form-control select-format" v-html="htmlListTipoPago"
                                            v-model="model.tipo_pago"></select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Banco Destino:</label>
                                        <select v-model="model.cod_bc_destino" class="form-control select-format">
                                            <option disabled :value="-1">Seleccione..</option>
                                            <!-- <option v-for="data in aDataBanckAcountDestiny" :key="data.cod_bc" :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta.slice(-5)}}-{{data.moneda}}</option> -->
                                            <option v-for="data in aDataBanckAcountDestiny" :key="data.cod_bc"
                                                :value="data.cod_bc">{{data.sigla}}-{{data.nro_cuenta}}-{{data.moneda}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Tipo de Cambio:</label>
                                        <input type="number" class="form-control input-format"
                                            v-model="model.tipo_cambio" @keyup="calculateCurrencyExchangePrice()" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Importe BS:</label>
                                        <input type="number" class="form-control input-format"
                                            v-model="model.importe_bs" @keyup="calculateCurrencyExchangePrice()" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Importe USD:</label>
                                        <input type="number" class="form-control input-format"
                                            v-model="model.importe_usd" @keyup="calculateCurrencyExchangePrice()" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Total OT USD:</label>
                                        <input disabled type="number" class="form-control input-format"
                                            v-model="model.total_usd" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Importe Total Bs:
                                            <span>{{subTotalImporte.toFixed(2)}}</span></label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Total OT Bs:
                                            <span>{{model.total_bs.toFixed(2)}}</span></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <!-- <h4>OT Seleccionadas:</h4> -->
                                        <p>
                                            {{ selectedOT.map(ot => `${ot.NRO_ORDEN}, ${ot.PRECIO} ${ot.MONEDA == 1 ?
                                            'BOB' : 'USD'}`).join(', ') }}
                                        </p>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="col-lg-12">
                                      
                                        <ul>
                                            <li v-for="ot in selectedOT" :key="ot.COD_ORDEN_TRABAJO">
                                                NRO: {{ ot.NRO_ORDEN }} - Cliente: {{ ot.CLIENTE }} - Fecha Servicio: {{
                                                ot.FECHA_SERVICIO }}
                                            </li>
                                        </ul>
                                    </div>
                                </div> -->

                                <!-- RECIBOS NR DE OT JR -->
                                <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                       <div class="form-group">
                                            <label class="lbl-format">Diferencia:</label>
                                            <input type="text" class="form-control input-format" v-model="model.dif_ot" readonly/>
                                      </div>
                                 </div>
                                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" v-if="model.dif_ot !== 0">
                                <div class="form-group">
                                            <label class="lbl-format">Aplicar Diferencia:</label>
                                    <div>
                                             <button type="button" 
                                               class="btn" 
                                               :class="{'btn-success': model.dif === 1, 'btn-outline-success': model.dif !== 1}" 
                                             @click="model.dif = 1">Sí
                                             </button>
                                              <button type="button" 
                                               class="btn" 
                                               :class="{'btn-danger': model.dif === 0, 'btn-outline-danger': model.dif !== 0}" 
                                            @click="model.dif = 0">No</button>
                                   </div>
                               </div>
                           </div> -->

                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <textarea data-length=256 class="form-control char-textarea textarea-format"
                                        placeholder="Glosa / Descripcion:" rows="2" v-model="model.glosa"></textarea>
                                    <small class=" btn-primary counter-value float-right"><span
                                            class="char-count">0</span> / 255 </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" v-on:click="closeWindowWithoutCleaning" class="btn btn-secondary">
                                <i class="fa fa-times-circle"></i> Volver
                            </button>
                            <button type="button" v-on:click="closeWindow" class="btn btn-danger">
                                <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <!-- <button type="button" v-if="modalAction == 1" class="btn btn-success"
                                v-on:click="saveRegister" v-bind:disabled="buttonDisabled">
                                <i class="fa fa-floppy-o"></i> Generar Recibo
                            </button> -->
                            <button type="button" v-if="modalAction==1" class="btn btn-success"
                                v-on:click="saveRegister" v-bind:disabled="buttonDisabled">
                                <i class="fa fa-print"></i> Generar Recibo
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
                modalAction: 1,
                buttonDisabled: false,
                messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
                selectAllOT: false,
                selectedOT: [],
                htmlListTipoPago: '',
                subTotalImporte: 0,
                model:{
                    cod_cr: 0,
                    nro_recibo: 0,
                    fecha: this.fecha('actual'),
                    recibi_de: '',
                    tipo_pago: 3,
                    cod_bc_destino: -1,
                    tipo_cambio: 6.96,
                    importe_bs: 0,
                    importe_usd: 0,
                    // dif_ot: 0,
                    // dif: 0,
                    total_bs: 0,
                    total_usd: 0,
                    monto_literal_usd: '',
                    glosa: '',
                    usuario_registro: 0,
                    fecha_registro: '',
                    detalle: []
                },
                dataDetalle:{
                    cod_cr: 0,
                    cod_ot: 0,
                    nro_ot: 0,
                    cod_cliente_ot: 0,
                    razon_social_ot: '',
                    precio_ot: 0,
                    moneda_ot: 0,
                    fecha_servicio_ot: '',
                    ACTIVO: 1,
                },

                //Datos de tablas
                aData :[],
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
                me.selectAllOT = false;
                me.selectedOT = [];
                me.model = {
                    cod_cr: 0,
                    fecha: this.fecha('actual'),
                    cod_cliente: -1,
                    recibi_de: '',
                    tipo_pago: 3,
                    cod_bc_destino: -1,
                    tipo_cambio: 6.96,
                    importe_bs: 0,
                    importe_usd: 0,
                    total_usd: 0,
                    total_bs: 0,
                    // dif : 0,
                    monto_literal_usd: '',
                    glosa: '',
                    detalle: []
                };
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
                let totalBsFormatted = parseFloat(me.model.total_bs.toFixed(2));
                let subTotalImporteFormatted = parseFloat(me.subTotalImporte.toFixed(2));
                if(me.model.cod_bc_destino == -1){
                    swal("Alerta de Mensaje", "Seleccione un Cuenta Banco Destino.", "warning");
                    return false;
                }

                // Realiza la comparación usando los valores formateados
                if (totalBsFormatted !== subTotalImporteFormatted){
                  swal("Alerta de Mensaje", "Los Importes Ingresados no igualan al Importe Total OT", "warning");
                 return false;
                 }

                // if ( me.subTotalImporte > me.model.total_bs){
                //   swal("Alerta de Mensaje", "El Importe ingresado es mayor al Importe Total OT", "warning");
                //  return false;
                //  }
                // if ((me.model.total_bs != me.subTotalImporte)){
                //     swal("Alerta de Mensaje", "Los Importes Ingresados no igualan al Importe Total OT", "warning");
                //     return false;
                // }
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
                var url = "/api/recibosOT?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
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
                me.htmlListTipoPago = "<option value=1>EFECTIVO</option>";
                me.htmlListTipoPago += "<option value=2>CHEQUE</option>";
                me.htmlListTipoPago += "<option value=3>TRANSFERENCIA</option>";
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
            calculateCurrencyExchangePrice(){
                let me = this;
                try {
                    var tipoCambio_ = this.model.tipo_cambio;
                    var totalOTUsd_ = me.model.total_usd.toFixed(2);
                    if(tipoCambio_ != "" && tipoCambio_ != "0"){
                        me.model.total_bs  = parseFloat((totalOTUsd_ * tipoCambio_));
                    } else{
                        me.model.total_bs = 0;
                    }
                    var importeBs_ = this.model.importe_bs;
                    var importeUsd_ = this.model.importe_usd;
                    me.subTotalImporte = 0;
                    if(importeBs_ != "" && importeBs_!="0"){
                        me.subTotalImporte += parseFloat(importeBs_); 
                    }
                    if(importeUsd_ != "" && importeUsd_ != "0"){
                        if(tipoCambio_ != "" && tipoCambio_ != "0")
                            me.subTotalImporte += parseFloat((importeUsd_ * tipoCambio_));
                    }
                    // Calcula la diferencia RECIBOS NR DE OT JR
                    // let totalBs = parseFloat(me.model.total_bs.toFixed(2));
                    // let subTotalImporte = parseFloat(me.subTotalImporte.toFixed(2));
                    // me.model.dif_ot = (totalBs - subTotalImporte).toFixed(2); // Aquí se redondea la diferencia a 2 decimales
                    // me.model.dif_ot = totalBs - subTotalImporte;
                    
                } catch (error) {
                    return;
                }
            },
            saveRegister(){
                if (!this.buttonDisabled) {
                    this.buttonDisabled = true; // Desactivar el botón para evitar clics adicionales
                    // Lógica para guardar la solicitud

                    // Agregar un retraso de 1 segundo antes de habilitar el botón nuevamente
                    setTimeout(() => {
                        this.buttonDisabled = false;
                    }, 1000);
                }
                let me = this;
                if(!me.validateMainForm())
                    return;
                axios.post('/api/recibosOT/registrar',{data: me.model}).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.openPDFRecibotoPrint(reponse.data.result);
                        me.closeWindow();
                        me.closeWindowWithoutCleaning();
                        me.cleanForm();
                        swal("Control Recibos OT", "Registrado Correctamente.", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
                    } else {
                        swal("Control Recibos OT", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Control Recibos OT", error.response.data.message, error.response.data.status);
                });
            },
            openPDFRecibotoPrint(id){
                var pagina = "http://mip2024.isitecnologia.com/api/recibosOT/pdf/"+id;
                // var pagina = "http://127.0.0.1:8000/api/recibosOT/pdf/"+id;
                var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                window.open(pagina,"",opciones);
            },

            // Modal
            openWindow(action, data = []){
                let me = this;
                me.listTipoPago();
                me.cleanUp();
                me.model.importe_bs = 0;
                me.model.importe_usd = 0;
                me.model.total_bs = 0;
                me.model.total_usd = 0;
                me.subTotalImporte = 0;
                switch(action) {
                    case "registrar": {
                        if(me.selectedOT.length >= 1){
                            me.modal = 1;
                            me.modalTitle='Nuevo Registro Control de Recibos OT';
                            me.modalAction=1;
                            me.setForm(me.selectedOT);
                        } else{
                            swal("Alerta de Mensaje", "Debe Seleccionar Mínimo un Registro.", "warning");
                        }
                        break;
                    };
                }
            },
            setForm(data){
                let me = this;
                for (let i = 0; i < data.length; i++) {
                    me.model.detalle.push({
                        cod_cr: 0,
                        cod_ot: data[i].COD_ORDEN_TRABAJO,
                        nro_ot: data[i].NRO_ORDEN,
                        cod_cliente_ot: data[i].COD_CLIENTE,
                        razon_social_ot: data[i].RAZON_SOCIAL,
                        precio_ot: data[i].PRECIO,
                        moneda_ot: data[i].MONEDA,
                        fecha_servicio_ot: data[i].FECHA_SERVICIO,
                        ACTIVO: 1,
                    });
                    me.model.recibi_de = data[i].CLIENTE;
                    me.model.total_usd += data[i].PRECIO;
                }
            },
            closeWindow(){
                this.modal = 0;
                this.modalCancel = 0;
                this.modalTitle = '';
                this.modalMessage = '';
                this.cleanForm();
            },
            closeWindowWithoutCleaning() {
                this.modal = 0; // Cerrar el modal.
                this.modalMessage = '';
                this.model.total_bs = 0;
                this.subTotalImporte = 0;
                // this.model.dif_ot = 0; //RECIBOS NR DE OT JR
                // this.model.dif = 0;
                this.model.detalle = [];  
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
            // alert("Usuario Logueado-"+localStorage.getItem("codTipoUsu"));
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