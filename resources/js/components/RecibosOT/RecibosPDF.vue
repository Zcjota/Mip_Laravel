<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h1 style="text-align: center"><b>Recibos Oden de Trabajo</b></h1>
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
                                    <table id="mainTableSG" class="tabla">
                                        <thead>
                                            <tr>
                                                <th style="width:30px;"></th>
                                                <th align="center">NRO</th>
                                                <th>FECHA</th>
                                                <th>CLIENTE</th>
                                                <th>TIPO PAGO</th>
                                                <th>BS</th>
                                                <th>USD</th>
                                                <th>CONCEPTO</th>
                                                <th>CONCILIADO</th>
                                                <!-- <th>APLICA DIFERENCIA</th>
                                                <th>DIFERENCIA</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- <tr v-for="data in aData" :key="data.cod_cr" :class="{ 'fila-amarilla': esFilaAmarilla(data) }"> -->
                                            <tr v-for="data in aData" :key="data.cod_cr" style="height:50px">
                                                <td>
                                                    <button type="button" class="btn btn-dark" v-on:click="openPDFtoPrint(data.cod_cr)">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </button>
                                                </td>
                                                <td align="center">{{data.nro_recibo_format}}</td>
                                                <td>{{data.fecha}}</td>
                                                <td>{{data.recibi_de}}</td>
                                                <td v-if="data.tipo_pago==1">EFECTIVO</td><td v-else-if="data.tipo_pago==2">CHEQUE</td><td v-else>TRANSFERENCIA</td>
                                                <td>{{data.importe_bs.toFixed(2)}} BOB</td>
                                                <td>{{data.importe_usd.toFixed(2)}} USD</td>
                                                <td>{{data.glosa}}</td>
                                                <!-- <td>{{data.bandera}}</td> -->
                                                <td>
                                                <span v-if="data.bandera === null">PEND.</span>
                                                <span v-if="data.bandera === 0">PEND.</span>
                                                <span v-if="data.bandera === 1">✔️</span>
                                                <button v-else-if="data.bandera === 2" class="btn-warning"
                                                    @click="handleWarning(data)">
                                                    ⚠️
                                                </button>
                                                <span v-else>{{ data.bandera }}</span>
                                            </td>
                                                <!-- <td>{{ data.dif === 1 || data.dif === '1' ? 'Sí' : 'No' }}</td>
                                                <td>{{ (data.dif_ot != null ? data.dif_ot : 0).toFixed(2) }} BOB</td> -->
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
            </div>
        </main>
</template>

<script>
    export default {
        data(){
            return {
                messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
                
                //Datos de tablas
                aData :[],

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
            // esFilaAmarilla(data) {
            //     return data.dif === 1 || data.dif === '1';
            // },
            initialize() {
                let me = this;
                var fecha = me.fecha('actual');
                me.tipoFecha = "1";
                me.fechaInicio = fecha;
                me.fechaFin = fecha;
                me.fechaActual = me.fecha('actual');
                me.listData(1, me.searchData, me.fechaInicio, me.fechaFin);
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
            listData(page, buscar, fechaInicio, fechaFin) {
                let me =this;
                var url = "/api/recibosOT/impresiones?page="+page+"&buscar="+buscar+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data.result;
                    me.aData = resp.data;
                    // me.aData = [...resp.data];
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
            openPDFtoPrint(id){
                var pagina = "http://mip2024.isitecnologia.com/api/recibosOT/pdf/"+id;
                // var pagina = "http://127.0.0.1:8000/api/recibosOT/pdf/"+id;
                var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                window.open(pagina,"",opciones);
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
            // console.log(this.aData);
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
    }.fila-amarilla {
    background-color: yellow;
    }


</style>