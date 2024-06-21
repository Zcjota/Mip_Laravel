<template>
    <main class="main">
        <!-- <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner">
                <i class="fa fa-spinner fa-spin"></i> Cargando...
            </div>
        </div> -->
        <!-- Breadcrumb -->
        <ol class="">
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h1 style="text-align: center"><b>REGISTRO VENTAS</b></h1>
                    <br />
                    <div class="row">
                        <div class="col-md-3">

                            <button type="button" class="btn btn-success square" @click="exportToExcel"
                                title="Exportar a Excel">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                EXCEL
                            </button>
                            <!-- <button type="button" class="btn btn-success square" v-on:click="openWindow('guardar')"
                                title="Guardar Registro">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                GUARDAR
                            </button> -->
                        </div>

                        <div class="col-md-9">
                            <div class="input-group">
                                <!-- <select v-model="tipoEstado" class="form-control square" style="width:100px;" v-on:change="validateDate">
                                        <option value="ALL">TODOS</option>
                                        <option value="INI">INICIAL</option>
                                        <option value="APR">APROBADO</option>
                                        <option value="ANU">ANULADO</option>
                                        <option value="REC">RECHAZADO</option>
                                    </select> -->
                                <!-- <select v-model="tipoPago" class="form-control square" style="width:100px;"
                                    v-on:change="validateDate">
                                    <option value="ALL">TODOS</option>
                                    <option value="INI">AL CONTADO</option>
                                    <option value="APR">CREDITO</option>
                                    <option value="ANU">ANULADO</option>
                                    <option value="REC">RECHAZADO</option>
                                </select> -->
                                <select v-model="tipoDebe" class="form-control square" style="width:70px;"
                                    v-on:change="validateDate">
                                    <option value="">TODOS</option>
                                    <option value="DEBE">DEBE</option>
                                    <option value="NO DEBE">NO DEBE</option>
                                </select>
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
                                    v-on:click="listData(1, searchData, fechaInicio, fechaFin, tipoEstado,tipoDebe)">
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
                                <!-- Mostrar el indicador de carga dentro de la tabla -->
                                <div v-if="isLoading" class="loading-overlay">
                                    <div class="loading-spinner">
                                        <i class="fa fa-spinner fa-spin"></i> Cargando...
                                    </div>
                                </div>
                                <table id="mainTableSG" class="tabla">
                                    <thead>
                                        <tr>
                                            <th style="width:20px;"></th>
                                            <th style="width:20px;"></th>
                                            <th style="width:20px;"></th>
                                            <th class="sticky-column-1">Nro</th>
                                            <th style="padding: 50px;" class="sticky-column-2">Cliente</th>
                                            <th style="padding: 10px;" class="sticky-column-3">Precio $</th>
                                            <th style="padding: 10px;" class="sticky-column-4">Fecha Servicio</th>
                                            <th style="width:20px;"></th>
                                            <th style="width:20px;"></th>
                                            <th style="width:20px;"></th>
                                            <th style="padding: 10px;">Tipo Pago</th>
                                            <th style="padding: 10px;">Plazo</th>
                                            <th style="padding: 10px;">Fecha Estimada</th>
                                            <th style="padding: 10px;">Monto $us</th>
                                            <th style="padding: 10px;">Monto Bs</th>
                                            <th style="padding: 10px;">Dif Pago</th>
                                            <th style="padding: 10px;">Fecha Recibo</th>
                                            <th style="padding: 10px;">Nro.Recibo</th>
                                            <th style="padding: 10px;">Contado Deposito</th>
                                            <th style="padding: 10px;">Cheque Deposito</th>
                                            <th style="padding: 10px;">Transferencia</th>
                                            <th style="padding: 10px;">Fecha Factura</th>
                                            <th style="padding: 10px;">Nro.Factura</th>
                                            <th style="padding: 10px;">Debe</th>
                                            <th style="padding: 10px;">Contacto</th>
                                            <th style="padding: 50px;">Direccion</th>
                                            <th style="padding: 10px;">Telefono</th>
                                            <th style="padding: 10px;">Ejecutivo Ventas</th>
                                            <th style="padding: 10px;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr v-for="data in aData" :key="data.cod_sg" style="height:50px" -->
                                        <tr v-for="data in aData" :key="data.COD_ORDEN_TRABAJO" style="height:20px"
                                            v-on:click="changeRowColor('mainTableSG')">
                                            <td>
                                                <button v-if="data.ACTIVO==1" type="button"
                                                    v-on:click="openWindow('actualizar', data)" class="btn btn-warning">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                                <button v-else disabled type="button" class="btn btn-warning">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                            </td>
                                            <td v-if="data.ACTIVO==1">
                                                <!-- <td v-if="data.ACTIVO==1 && data.estado=='INI'"> -->
                                                <button type="button" class="btn btn-danger colorRojo"
                                                    v-on:click="openWindow('anular', data)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                            <td v-else>
                                                <button disabled type="button" class="btn btn-danger">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button v-if="data.ACTIVO==1" type="button"
                                                    v-on:click="openPDFtoPrint(data)" class="btn btn-dark">
                                                    <i class="fa fa-print"></i>
                                                </button>
                                                <button v-else disabled type="button" class="btn btn-darck">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </button>
                                            </td>
                                            <td class="sticky-column-5" :class="getRowClass('NRO_ORDEN', data.estado)"
                                                style="padding: 10px;">{{ data.NRO_ORDEN }}</td>
                                            <td class="sticky-column-6" :class="getRowClass('cliente', data.estado)"
                                                style="padding: 10px;">{{ data.cliente }}</td>
                                            <td class="sticky-column-7" :class="getRowClass('PRECIO', data.estado)"
                                                style="padding: 10px;">{{ data.PRECIO }}</td>
                                            <td class="sticky-column-8" :class="getRowClass('FECHAS', data.estado)"
                                                style="padding: 10px;">{{ data.FECHAS }}</td>
                                            <td :class="getRowClass('S', data.estado)" style="padding: 10px;">{{ data.S
                                                }}</td>
                                            <td :class="getRowClass('S', data.estado)" style="padding: 10px;">{{ data.S
                                                }}</td>
                                            <td :class="getRowClass('S', data.estado)" style="padding: 10px;">{{ data.S
                                                }}</td>
                                            <td :class="getRowClass('TIPO_PAGOS', data.estado)" style="padding: 10px;">
                                                {{ data.TIPO_PAGOS }}</td>
                                            <td :class="getRowClass('PLAZOS', data.estado)" style="padding: 10px;">{{
                                                data.PLAZOS }}</td>
                                            <td :class="getRowClass('fecEstimada', data.estado)" style="padding: 10px;">
                                                {{ data.fecEstimada }}</td>
                                            <td style="padding: 10px;">{{ data.montopagodolar }}</td>
                                            <td style="padding: 10px;">{{ data.BS }}</td>
                                            <td style="padding: 10px;">{{ data.difpago }}</td>
                                            <td style="padding: 10px;">{{ data.fecha_recibo }}</td>
                                            <td style="padding: 10px;">{{ data.nro_recibo }}</td>
                                            <td style="padding: 10px;">{{ data.contado_nro_deposito }}</td>
                                            <td style="padding: 10px;">{{ data.nro_cheque }}</td>
                                            <td style="padding: 10px;">{{ data.transferencia }}</td>
                                            <td style="padding: 10px;">{{ data.fecha_factura }}</td>
                                            <td style="padding: 10px;">{{ data.nro_factura }}</td>
                                            <td style="padding: 10px;">{{ data.debe }}</td>
                                            <td :class="getRowClass('CONTACTO', data.estado)" style="padding: 10px;">{{
                                                data.CONTACTO }}</td>
                                            <td :class="getRowClass('DIRECCION', data.estado)" style="padding: 10px;">{{
                                                data.DIRECCION }}</td>
                                            <td :class="getRowClass('TELEFONO', data.estado)" style="padding: 10px;">{{
                                                data.TELEFONO }}</td>
                                            <td :class="getRowClass('NOMBRE', data.estado)" style="padding: 10px;">{{
                                                data.NOMBRE }}</td>
                                            <td :class="getRowClass('estado', data.estado)" style="padding: 10px;">{{
                                                data.estado }}</td>
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
                    <div class="modal-content modal-content-format">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title titulo">{{modalTitle}}</h4>
                            <button type="button" class="close" v-on:click="closeWindow" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body modal-body-format">
                            <div class="row">
                                <!-- <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                                    <label class="lbl-format">NRO Orden:{{nro_orden}}</label>
                                </div> -->
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">NRO Orden:</label>
                                        <input type="text" class="form-control input-format" v-model="model.nro_orden1"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Cliente:</label>
                                        <input type="text" class="form-control input-format" v-model="model.cliente_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Fecha Servicio:</label>
                                        <input type="date" class="form-control input-format"
                                            v-model="model.fecha_servicio" readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Precio usd:</label>
                                        <input type="text" class="form-control input-format" v-model="model.precio_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Tipo Pago:</label>
                                        <select class="form-control select-format" v-html="htmlListTipoPago"
                                            v-model="model.tipo_pagos_" disabled></select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Plazo:</label>
                                        <input type="text" class="form-control input-format" v-model="model.plazo_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Fecha Estimada:</label>
                                        <input type="date" class="form-control input-format"
                                            v-model="model.fecha_estimada" readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Monto $us:</label>
                                        <input type="text" class="form-control input-format" v-model="model.montosus_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Monto BS:</label>
                                        <input type="text" class="form-control input-format" v-model="model.montobs_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Dif Pago:</label>
                                        <input type="text" class="form-control input-format" v-model="model.difpago_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Contacto:</label>
                                        <input type="text" class="form-control input-format" v-model="model.contacto_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Direccion:</label>
                                        <input type="text" class="form-control input-format" v-model="model.direccion_"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Ejecutivo en Ventas:</label>
                                        <input type="text" class="form-control input-format"
                                            v-model="model.ejecutivoventas_" readonly />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Estado:</label>
                                        <input type="text" class="form-control input-format" v-model="model.estado_"
                                            readonly />
                                    </div>
                                </div>

                                <!-- Modulo de edicio -->

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <h2
                                        style="background-color: #243648; color: white; padding: 10px; text-align: center;">
                                        Módulo de Edición</h2>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Fecha Recibo:</label>
                                        <input type="date" class="form-control input-format"
                                            v-model="model.fecha_recibo" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Numero de Recibo:</label>
                                        <input type="text" class="form-control input-format"
                                            v-model="model.nro_recibo" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Contado Deposito:</label>
                                        <input type="text" class="form-control input-format"
                                            v-model="model.contadodep_" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Cheque Deposito:</label>
                                        <input type="text" class="form-control input-format"
                                            v-model="model.chequedep_" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Transferencia:</label>
                                        <select class="form-control select-format" v-html="htmlListTipoTransferencia"
                                            v-model="model.transferencia_"></select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Fecha Factura:</label>
                                        <!-- <input type="text" class="form-control input-format" v-model="model.fecha_factura_"  /> -->
                                        <input type="date" class="form-control input-format"
                                            v-model="model.fecha_factura_" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Numero de Factura:</label>
                                        <input type="text" class="form-control input-format"
                                            v-model="model.nro_factura" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label class="lbl-format">Debe:</label>
                                        <select class="form-control select-format" v-html="htmlListDebe"
                                            v-model="model.debe_" disabled></select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" v-on:click="closeWindow" class="btn btn-danger">
                                <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <!-- <button type="button" v-if="modalAction == 1" class="btn btn-success"
                                v-on:click="saveRegister" v-bind:disabled="buttonDisabled">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button> -->
                            <button type="button" v-if="modalAction == 1" class="btn btn-success"
                                v-on:click="saveRegister" v-bind:disabled="buttonDisabled">
                                <i class="fa fa-floppy-o"></i> Actualizar
                            </button>
                            <!-- <button type="button" v-if="modalAction==1" class="btn btn-success" v-on:click="saveRegister">
                                    <i class="fa fa-floppy-o"></i> Guardar
                                </button> -->
                            <!-- <button type="button" v-else class="btn btn-success" v-on:click="saveRegister">
                                <i class="fa fa-floppy-o"></i> Actualizar
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de Busquedas -->
            <!-- PROVEEDOR -->

            <!-- Modal de Anulacion -->
            <div class="modal fade" :class="{'mostrar':modalCancel}" aria-hidden="true">
                <div class="modal-dialog modal-danger" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" v-text="modalTitle"></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                v-on:click="closeWindow">
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
                            <button type="button" class="btn btn-warning" v-on:click="unregister(model.cod_sg)">
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
import Swal from 'sweetalert2';
    export default {
        data(){
            return {
                // modalAction: 1,
                isLoading: false,
                buttonDisabled: false,
                messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
                nombreUsuario: "",
                // nro_orden: "",
                
                model: {
                COD_ORDEN_TRABAJO: '',
                nro_orden1: '',
                cliente_: '',
                precio_: '',
                fecha_servicio: '',
                tipo_pagos_: '',
                plazo_: '',
                fecha_estimada: '',
                montosus_: '',
                montobs_: '',
                difpago_: '',
                contacto_: '',
                direccion_: '',
                telefono_: '',
                ejecutivoventas_: '',
                estado_: '',
                fecha_recibo: '',
                nro_recibo: '',
                contadodep_: '',
                chequedep_: '',
                transferencia_: '',
                fecha_factura_: '',
                nro_factura: '',
                debe_: ''
            },

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
                paginationProvider : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                paginationAcount : {
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
                tipoEstado: 'ALL',
                tipoDebe: '',

                // modal
                modal : 0,
                modalCancel: 0,
                modalTitle : '',
                modalAction : 0,
                modalMessage : '',

                htmlListTipoPago: '',
                htmlListTipoGasto: '',
                htmlListTipoTransferencia:'',
                htmlListDebe: '',
                htmlListBanco: '',
                htmlListBancoCuenta: '',
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
            },
        },
        methods: {
            exportToExcel() {
            const params = {
                buscar: this.searchData,
                fechaInicio: this.fechaInicio,
                fechaFin: this.fechaFin,
                estado: this.tipoEstado,
                tipoDebe: this.tipoDebe
            };
            axios.post('/api/registroventas/export', params, { responseType: 'blob' })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'registro_ventas.xlsx');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.error('Error exporting to Excel:', error);
                });
        },

            getRowClass(column, estado) {
        const exceptionColumns = ['montopagodolar', 'BS', 'difpago', 'fecha_recibo', 'nro_recibo', 'contado_nro_deposito', 'nro_cheque', 'transferencia', 'fecha_factura', 'nro_factura', 'debe'];
        if (exceptionColumns.includes(column)) {
            return '';
        }
        switch (estado) {
            case 'SIN APROBAR':
            case 'REPROGRAMADA':
                return 'highlight-yellow';
            case 'APROBADA':
            case 'COBRADA':
                return 'highlight-red';
            default:
                return '';
        }
    },
            cleanUp(){
                
            },
            cleanForm() {
            this.model = {
                COD_ORDEN_TRABAJO: '',
                nro_orden1: '',
                cliente_: '',
                precio_: '',
                fecha_servicio: '',
                tipo_pagos_: '',
                plazo_: '',
                fecha_estimada: '',
                montosus_: '',
                montobs_: '',
                difpago_: '',
                contacto_: '',
                direccion_: '',
                telefono_: '',
                ejecutivoventas_: '',
                estado_: '',
                fecha_recibo: '',
                nro_recibo: '',
                contadodep_: '',
                chequedep_: '',
                transferencia_: '',
                fecha_factura_: '',
                nro_factura: '',
                debe_: ''
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
            openPDFtoPrint(data){
                var pagina = "https://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo="+data.COD_ORDEN_TRABAJO;
                // var pagina = "http://127.0.0.1:8000/api/sgCreditoPDF/"+data.cod_sg;
                var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                window.open(pagina,"",opciones);
            },
            initialize() {
                let me = this;
                var fecha = me.fecha('actual');
                me.tipoFecha = "1";
                me.fechaInicio = fecha;
                me.fechaFin = fecha;
                me.fechaActual = me.fecha('actual');
                // me.listData(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado,me.tipoDebe);
                me.listData(1, me.searchData, me.fechaInicio, me.fechaFin,me.tipoEstado,me.tipoDebe);
               
            },
            validateDate() {
                let me = this;
                var tipo = me.tipoFecha;
                switch (tipo) {
                    case "1":
                        var fecha_actual = me.fecha('actual');
                        me.fechaInicio=fecha_actual;
                        me.fechaFin=fecha_actual;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado,me.tipoDebe);
                        break;
                    case "2":
                        var fecha_inicio = me.fecha('inicio');
                        var fecha_fin = me.fecha('fin');
                        me.fechaInicio=fecha_inicio;
                        me.fechaFin=fecha_fin;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado,me.tipoDebe);
                        break;
                    case "3":

                        break;
                }
            },
            
            validateMainForm(){
                let me = this;
                if (isNaN(me.model.nro_recibo)) {
                    swal("Alerta de Mensaje", "El número de recibo debe ser un valor numérico.", "warning");
                    return false;
                }
                if (isNaN(me.model.chequedep_)) {
                    swal("Alerta de Mensaje", "El cheque de depósito debe ser un valor numérico.", "warning");
                    return false;
                }
                if (isNaN(me.model.nro_factura)) {
                    swal("Alerta de Mensaje", "El número de factura debe ser un valor numérico.", "warning");
                    return false;
                }
                if (isNaN(me.model.contadodep_)) {
                    swal("Alerta de Mensaje", "El contador de depósito debe ser un valor numérico.", "warning");
                    return false;
                }
                return true;
            },
            listData(page, buscar, fechaInicio, fechaFin, estado, tipoDebe) {
        let me = this;
        me.isLoading = true; // Activar el indicador de carga
        var tipoSolicitud = "CREDITO";
        var url = "/api/registroventas/Buscar?page=" + page + "&buscar=" + buscar + "&fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&estado=" + estado + "&tipoDebe=" + tipoDebe;

        axios.get(url).then(function (reponse) {
            var resp = reponse.data.result;
            me.aData = resp.data;
            me.pagination = {
                'total': resp.total,
                'current_page': resp.current_page,
                'per_page': resp.per_page,
                'last_page': resp.last_page,
                'from': resp.from,
                'to': resp.to,
            };
        }).catch(function (error) {
            me.bugChecking(error);
        }).finally(function () {
            me.isLoading = false; // Desactivar el indicador de carga
        });
    },
            listTipoPago() {
                let me = this;
                me.htmlListTipoPago = "<option value='CREDITO'>CREDITO</option>";
                me.htmlListTipoPago += "<option value='AL CONTADO'>AL CONTADO</option>";
                // me.htmlListTipoPago += "<option value='TRANSFERENCIA'>TRANSFERENCIA</option>";
            },
            listTipoTranferencia() {
                let me = this;
                me.htmlListTipoTransferencia = "<option value='SI'>SI</option>";
                me.htmlListTipoTransferencia += "<option value='NO'>NO</option>";
                me.htmlListTipoTransferencia += "<option value=''>SELECCIONAR</option>";
            },
            listDebe() {
                let me = this;
                me.htmlListDebe = "<option value='NO DEBE'>NO DEBE</option>";
                me.htmlListDebe += "<option value='DEBE'>DEBE</option>";
            },
            listTipoGasto() {
                let me = this;
                me.htmlListTipoGasto = "<option value='E'>EVENTUAL</option>";
                me.htmlListTipoGasto += "<option value='F'>FIJO</option>";
            },
            changePage(page, buscar, fechaInicio, fechaFin, estado, tipoDebe){
                let me = this;
                me.pagination.current_page = page;
                // me.listData(page, buscar, fechaInicio, fechaFin, estado, tipoDebe);
                me.listData(page, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado, me.tipoDebe);
                // me.listData(page, buscar, fechaInicio, fechaFin, estado);
            },
            
            
            changeRowColor(id) {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            
            saveRegister() {
        let me = this;
        if (!me.validateMainForm()) return;

        me.buttonDisabled = false; 
        me.isLoading = true; // Activar el indicador de carga

        const dataToSend = {
            COD_ORDEN_TRABAJO: me.model.COD_ORDEN_TRABAJO,
            fecha_recibo: me.model.fecha_recibo,
            nro_recibo: me.model.nro_recibo,
            contadodep_: me.model.contadodep_,
            chequedep_: me.model.chequedep_,
            transferencia_: me.model.transferencia_,
            fecha_factura_: me.model.fecha_factura_,
            nro_factura: me.model.nro_factura,
            debe_: me.model.debe_
        };

        axios.post('/api/registroventas/guardar', dataToSend)
            .then(function (response) {
                if (response.data.status === 'success') {
                    me.closeWindow();
                    me.cleanForm();
                    swal("Registro de Ventas", "Actualizado Correctamente.", "success");
                    me.listData(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado, me.tipoDebe);
                } else {
                    swal("Registro de Ventas", response.data.message, response.data.status);
                    me.isLoading = false; // Desactivar el indicador de carga en caso de error
                }
            })
            .catch(function (error) {
                swal("Registro de Ventas", error.response.data.message, error.response.data.status);
                me.isLoading = false; // Desactivar el indicador de carga en caso de error
            });
    },
            // getDataByID(id_sg){
                getDataByID(COD_ORDEN_TRABAJO) {
            let me = this;
            var url = "/api/registroventas/" + COD_ORDEN_TRABAJO;
            axios.get(url).then(function (response) {
                var resp = response.data.result;

                console.log('Respuesta completa:', resp);

                if (resp && resp.data) {
                    console.log('Datos encontrados:', resp.data);
                    me.model.COD_ORDEN_TRABAJO = resp.data.COD_ORDEN_TRABAJO;
                    me.model.nro_orden1 = resp.data.NRO_ORDEN;
                    me.model.cliente_ = resp.data.cliente;
                    me.model.precio_ = resp.data.PRECIO;
                    me.model.fecha_servicio = resp.data.FECHAS;
                    me.model.tipo_pagos_ = resp.data.TIPO_PAGOS;
                    me.model.plazo_ = resp.data.PLAZOS;
                    me.model.fecha_estimada = resp.data1.fecEstimada;
                    me.model.montosus_ = resp.data1.montopagodolar;
                    me.model.montobs_ = resp.data1.BS;
                    me.model.difpago_ = resp.data1.difpago;
                    me.model.contacto_ = resp.data.CONTACTO;
                    me.model.direccion_ = resp.data.DIRECCION;
                    me.model.telefono_ = resp.data.TELEFONO;
                    me.model.ejecutivoventas_ = resp.data.NOMBRE;
                    me.model.estado_ = resp.data.estado;
                    me.model.fecha_recibo = resp.data1.fecha_recibo_;
                    me.model.nro_recibo = resp.data1.nro_recibo;
                    me.model.contadodep_ = resp.data1.contado_nro_deposito;
                    me.model.chequedep_ = resp.data1.nro_cheque;
                    me.model.transferencia_ = resp.data1.transferencia;
                    me.model.fecha_factura_ = resp.data1.fecha_factura_;
                    me.model.nro_factura = resp.data1.nro_factura;
                    me.model.debe_ = resp.data1.debe;
                } else {
                    console.error('Datos no encontrados en la respuesta:', resp);
                    alert('Datos no encontrados');
                }
            }).catch(function (error) {
                console.error('Error en la solicitud:', error);
                alert(error.response.data.message);
            });
        },

            
            unregister(id_sg){
                let me = this;
                axios.put('/api/ZSolicitudGasto/updateStatus',{
                    cod_sg: id_sg,
                    estado: "ANU"
                }).then(function (reponse){
                    if(reponse.data.status == "success"){
                        me.closeWindow();
                        me.cleanForm();
                        swal("Solicitud de Gasto al Credito", "Registro Anulado Correctamente", "success");
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoEstado,me.tipoDebe);
                    } else {
                        swal("Solicitud de Gasto al Credito", reponse.data.message, reponse.data.status);
                    }
                }).catch(function(error){
                    swal("Solicitud de Gasto al Credito", error.response.data.message, error.response.data.status);
                });
            },

            openWindow(action, data = []) {
            let me = this;
            me.listTipoPago();
            me.listTipoGasto();
            me.listTipoTranferencia();
            me.listDebe();

            switch (action) {
                case "actualizar":
                    me.modal = 1;
                    me.modalTitle = 'Actualizar Registro de Venta';
                    me.modalAction = 1;
                    me.getDataByID(data.COD_ORDEN_TRABAJO);
                    break;
                    case "anular":
                me.anularRegistro(data);
                break;
            }
            
        },
        anularRegistro(data) {
        if (data.DEBE !== "NO DEBE") {
            Swal.fire("Registro de Ventas", "No se puede desactivar un registro que está en estado DEBE.", "error");
            return;
        }

        Swal.fire({
            title: '¿Estás seguro de que deseas desactivar este registro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/api/registroventas/desactivar', {
                    codservicio: data.NRO_ORDEN,
                    codnum: data.CODNUM,
                    codigo: data.COD_ORDEN_TRABAJO
                })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire("Registro de Ventas", "Registro desactivado correctamente.", "success");
                        this.listData(1, this.searchData, this.fechaInicio, this.fechaFin, this.tipoEstado, this.tipoDebe);
                    } else {
                        Swal.fire("Registro de Ventas", response.data.errors.reason, "error");
                    }
                })
                .catch(error => {
                    console.error('Error al desactivar el registro:', error);
                    Swal.fire("Registro de Ventas", "Error al desactivar el registro.", "error");
                });
            }
        });
    },
        closeWindow() {
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
.sticky-column-1,
.sticky-column-2,
.sticky-column-3,
.sticky-column-4 {
    position: sticky;
    left: 0;
    background-color: rgb(44,68,79,255); 
    z-index: 1;
    /* border-right: 1px solid #080808; */
    font-weight: bold; /* Ajusta el estilo de la letra aquí */
    color: #ecf0f0; /* Ajusta el color de la letra aquí */
}

/* Ajusta las posiciones left para cada columna */
.sticky-column-2 {
    left: 50px; /* Ajusta según el ancho de la primera columna */
}

.sticky-column-3 {  
    left: 150px; /* Ajusta según el ancho de las dos primeras columnas */
}

.sticky-column-4 {
    left: 200px; /* Ajusta según el ancho de las tres primeras columnas */
}.sticky-column-5 {
    position: sticky;
    left: 0;
    background-color: rgb(44,68,79,255); 
    z-index: 1;
    /* border-right: 1px solid #080808; */
    font-weight: bold; /* Ajusta el estilo de la letra aquí */
    color: #080808; 
}
.sticky-column-6 {
    position: sticky;
    left: 0;
    background-color: rgb(44,68,79,255); 
    z-index: 1;
    /* border-right: 1px solid #080808; */
    font-weight: bold; /* Ajusta el estilo de la letra aquí */
    color: #080808; 
}
.sticky-column-7 {
    position: sticky;
    left: 0;
    background-color: rgb(44,68,79,255); 
    z-index: 1;
    /* border-right: 1px solid #080808; */
    font-weight: bold; /* Ajusta el estilo de la letra aquí */
    color: #080808; 
}
.sticky-column-8 {
    position: sticky;
    left: 0;
    background-color: rgb(44,68,79,255); 
    z-index: 1;
    /* border-right: 1px solid #080808; */
    font-weight: bold; /* Ajusta el estilo de la letra aquí */
    color: #080808; 
}.sticky-column-6 {
    left: 50px; /* Ajusta según el ancho de la primera columna */
}

.sticky-column-7 {  
    left: 150px; /* Ajusta según el ancho de las dos primeras columnas */
}

.sticky-column-8 {
    left: 200px; /* Ajusta según el ancho de las tres primeras columnas */
}
.highlight-yellow {
    background-color: rgb(240, 240, 175) !important;
}

.highlight-red {
    background-color: rgb(236, 172, 172) !important;
}
.loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        font-size: 24px;
        color: #333;
    }

    .tabla {
        position: relative;
    }

    .table-wrapper {
  overflow-x: auto;
  position: relative;
  width: 100%;
}
</style>