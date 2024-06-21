<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">  
                        <!--       
                        <button type="button" @click="verificarEstado('suspender')" class="btn btn-primary">
                            <i class="icon-plus"></i>&nbsp;Suspender
                        </button>
                        -->
                        <button type="button" @click="imprimirPDF()" class="btn btn-success colorVerde">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
                        </button> 
                        <button type="button" @click="verificarEstado('modificar')" class="btn btn-primary">
                            <i class="icon-pencil"></i>&nbsp;MODIFICAR
                        </button>                        
                    </div>
                    <div class="card-body">
                                    <div class="form-group row">
                        <div class="col-md-12">
                            <h1 class="centro">LISTA DE ORDEN DE TRABAJO</h1>
                        </div>
                        </div>

                        <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                    <input type="text" v-model="buscar" @keyup="listadoOt(1,buscar)" class="form-control" placeholder="Buscar">
                                    <button type="submit" class="btn btn-primary" @click="listadoOt(1,buscar)"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                        </div>
                        </div>
                        <div style="overflow-x:auto;">
                        <table id="tablaPrincipalOT" class="tabla" style="width:100%" border="1">
                            <thead>
                                <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                  
                                    <th style="width:15px;">Permiso Modificar</th>
                                    <th style="width:60px;">Nro</th>
                                    <th>Cliente</th>
                                    <th >Contacto</th>
                                    <th>Direccion</th>
                                    <th >Telefono</th>
                                    <th>Fecha Servicio</th>
                                    <th>Hora</th>
                                    <th>Ejecutivo Ventas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr style="height:40px" v-for="ot in arrayOt" :key="ot.COD_ORDEN_TRABAJO" @click="asignarIdOT(ot.COD_ORDEN_TRABAJO,ot.ESTADO_PM,'tablaPrincipalOT')">
                                    <td v-text="ot.ESTADO_PM" title="Solicitud de Modificacion"></td>
                                    <td v-text="ot.NRO_ORDEN"></td>
                                    <td v-text="ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMCLIENTE"></td>
                                    <td v-text="ot.CONTACTO"></td>
                                    <td v-text="ot.DIRECCION"></td>
                                    <td v-text="ot.TELEFONO"></td>
                                    <td v-text="ot.FECHAS"></td>
                                    <td v-text="ot.HORA_SERVICIO"></td>
                                    <td v-text="ot.AP_PATERNO+' '+ot.AP_MATERNO+' '+ot.NOMBRE"></td>
                                    <td>
                                        <div v-if="ot.SUSPENDIDA">
                                            <span class="badge badge-danger colorRojo">SUSPENDIDA</span>
                                        </div>
                                        <div v-else-if="ot.REPROGRAMADA">
                                            <span class="badge badge-success colorVerde">REPROGRAMADA</span>
                                        </div>
                                        <div v-else-if="ot.APROBADA">
                                            APROBADA
                                        </div>
                                        <div v-else-if="ot.ANULADA">
                                            <span class="badge badge-danger colorRojo">ANULADA</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-primary colorAzul">SIN APROBAR</span>
                                        </div>
                                    </td>
                                </tr>
                             
                                
                                <!--
                               <tr v-for="ot in arrayOt" :key="ot.COD_ORDEN_TRABAJO" @click="asignarIdOT(ot.COD_ORDEN_TRABAJO)">
                                    <td v-text="ot.NRO_ORDEN"></td>
                                    <td v-text="ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMBRE"></td>
                                    <td v-text="ot.CONTACTO"></td>
                                    <td v-text="ot.DIRECCION"></td>
                                    <td v-text="ot.TELEFONO"></td>
                                    <td v-text="ot.FECHA_SERVICIO"></td>
                                    <td v-text="ot.HORA_SERVICIO"></td>
                                    <td v-text="ot.AP_PATERNO+' '+ot.AP_MATERNO+' '+ot.NOMBRE"></td>
                                    <td>
                                        <div v-if="ot.SUSPENDIDA">
                                            SUSPENDIDA
                                        </div>
                                        <div v-else-if="ot.REPROGRAMADA">
                                            REPROGRAMADA
                                        </div>
                                        <div v-else-if="ot.APROBADA">
                                            APROBADA
                                        </div>
                                        <div v-else>
                                            SIN APROBAR
                                        </div>
                                    </td>
                                </tr> 
                                -->
                            </tbody>
                        </table>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="listadoOt(pagination.current_page - 1,buscar)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="listadoOt(page,buscar)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="listadoOt(pagination.current_page + 1,buscar)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;overflow-y: scroll;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document" style="max-width: 55%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="overflow-y: scroll;">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Categoria<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="categoria">
                                                    <option value="1">A</option>
                                                    <option value="2">B</option>
                                                    <option value="3">C</option>
                                             
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                            
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Especificacion<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="especificacion">
                                                    <option value="1">Inicial</option>
                                                    <option value="2">Mantenimiento</option>
                                                    <option value="3">Reaplicacion 1</option>
                                                    <option value="4">Reaplicacion 2</option>
                                                    <option value="5">Queja</option>
                                            
                                                </select>
                                            </div>
                                    </div><br>  
                                    <div class="card-header">Datos Generales</div>
                                    <div class="card-body" style="background-color:#f0f3f5;">
                                    <form action="" method="post" >
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Cliente<span style="color:red">(*)</span></label>
                                                <div class="input-group">
                                            <!--
                                            me.arrayCliente = reponse.data;
                                            for (var i = 0; i < me.arrayCliente.length; i++) {
                                                me.listaCliente +='<option value="' +me.arrayCliente[i].COD_CLIENTE +'">' +me.arrayCliente[i].NOMBRE +"</option>";
                                            }
                                            })
                                            -->


                                                <!--
                                                <select class="form-control" v-model="cliente" @change="obtDatosCliente()">
                                                    <option v-for="(cliente,index) in arrayCliente">{{cliente.NOMBRE}}</option>
                                                </select>
                                                -->

                                                
                                                <select class="form-control" v-model="cliente" v-html="listaCliente" @change="obtDatosCliente()">
                                                    
                                                </select>
                                                
                                                <span class="input-group-append">
                                                    <button class="btn btn-success" title="Agregar Clientes" type="button" @click="abrirModal('cliente')">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Fecha Servicio<span style="color:red">(*)</span></label>
                                                <input type="date" class="form-control" v-model="fechaServicio">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Hora Servicio<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="horaServico">
                                                    <option value="00:00">00:00</option>
                                                    <option value="00:30">00:30</option>
                                                    <option value="01:00">01:00</option>
                                                    <option value="01:30">01:30</option>
                                                    <option value="02:00">02:00</option>
                                                    <option value="02:30">02:30</option>
                                                    <option value="03:00">03:00</option>
                                                    <option value="03:30">03:30</option>
                                                    <option value="04:00">04:00</option>
                                                    <option value="04:30">04:30</option>
                                                    <option value="05:00">05:00</option>
                                                    <option value="05:30">05:30</option>
                                                    <option value="06:00">06:00</option>
                                                    <option value="06:30">06:30</option>
                                                    <option value="07:00">07:00</option>
                                                    <option value="07:30">07:30</option>
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30">08:30</option>
                                                    <option value="09:00">09:00</option>
                                                    <option value="09:30">09:30</option>
                                                    <option value="10:00">10:00</option>
                                                    <option value="10:30">10:30</option>
                                                    <option value="11:00">11:00</option>
                                                    <option value="11:30">11:30</option>
                                                    <option value="12:00">12:00</option>
                                                    <option value="12:30">12:30</option>
                                                    <option value="13:00">13:00</option>
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00">14:00</option>
                                                    <option value="14:30">14:30</option>
                                                    <option value="15:00">15:00</option>
                                                    <option value="15:30">15:30</option>
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30">16:30</option>
                                                    <option value="17:00">17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <option value="18:00">18:00</option>
                                                    <option value="18:30">18:30</option>
                                                    <option value="19:00">19:00</option>
                                                    <option value="19:30">19:30</option>
                                                    <option value="20:00">20:00</option>
                                                    <option value="20:30">20:30</option>
                                                    <option value="21:00">21:00</option>
                                                    <option value="21:30">21:30</option>
                                                    <option value="22:00">22:00</option>
                                                    <option value="22:30">22:30</option>
                                                    <option value="23:00">23:00</option>
                                                    <option value="23:30">23:30</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Telefono<span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="telefono">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Contacto<span style="color:red">(*)</span></label>
                                                <input type="text" class="form-control" v-model="contacto">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Razon Social<span style="color:red">(*)</span></label>
                                                <input type="text" class="form-control" v-model="razonSocial">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Precio $<span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="precio">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Nit<span style="color:red">(*)</span></label>
                                                <input type="text" class="form-control" v-model="nit">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Tipo Pago<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="tipoPago">
                                                    <option value="1">Contado</option>
                                                    <option value="2">Credito</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="company">Direccion<span style="color:red">(*)</span></label>
                                                <textarea name="textarea" class="form-control" rows="2" cols="12" v-model="direccion"></textarea>
                                            </div>
                                        
                                        </div>
                                        
                                    </form>
                                    </div>
                                </div>
                                 <hr>
                            </div>

                           
                            <div class="col-md-12">
                                <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                <a class="nav-link active show" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Plagas a Tratar</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Lugares a Tratar</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Observaciones y Otros</a>
                                </li>
                                </ul>
                                <div class="tab-content" style="height:300px;">
                                <div class="tab-pane active show" id="home" role="tabpanel">
                                   <div style="overflow-x:auto;height:290px;">
                                   <table class="table table-bordered table-striped" >
                                        <thead>
                                            <tr>
                                                <th style="width:40px;"><input type="checkbox" v-model="selectAllPlaga" @click="selectPlagaC"></th>
                                                <th>Plaga a Tratar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="plagas in arrayPlagas" :key="plagas.COD_PLAGA">
                                                <td>
                                                    <label class="form-checkbox">
                                                    <input type="checkbox" :value="plagas.COD_PLAGA" v-model="selectPlaga">
                                                    <i class="form-icon"></i>
                                                    </label>
                                                </td>
                                                <td v-text="plagas.DESCRIPCION">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="profile" role="tabpanel" style="height:300px;">
                                    <div style="overflow-x:auto;height:290px;">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;"><input type="checkbox" v-model="selectAllLugar" @click="selectLugarC"></th>
                                                <th>Lugares a Tratar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="lugares in arrayLugares" :key="lugares.COD_LUGARES">
                                                <td>
                                                    <label class="form-checkbox">
                                                    <input type="checkbox" :value="lugares.COD_LUGARES" v-model="selectLugar">
                                                    <i class="form-icon"></i>
                                                    </label>
                                                </td>
                                                <td v-text="lugares.DESCRIPCION">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="messages" role="tabpanel" style="background-color:#f0f3f5;">  
                                    <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Nro de Tecnicos<span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="nroTecnicos">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Tiempo Servicio<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="tiempoServicio">
                                                    <option value="30">30 Minutos - Media Hora</option>
                                                    <option value="60">60 Minutos - 1 Hora</option>
                                                    <option value="90">90 Minutos - Hora y media</option>
                                                    <option value="120">120 Minutos - 2 Horas</option>
                                                    <option value="150">150 Minutos - 2 y media Horas</option>
                                                    <option value="180">180 Minutos - 3 Horas</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Ejecutivo de Ventas<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="ejecutivoVentas">
                                                    <option value="24">OPERACIONES</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Frecuencia <span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="frecuencia">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Equipo Asignado<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-html="listaEquipo" v-model="equipo">
  
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Responsable<span style="color:red">(*)</span></label>
                                                <input type="text" class="form-control" v-model="reponsable">
                                            </div>
                                        </div>
                                   
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="company">Observaciones<span style="color:red">(*)</span></label>
                                      
                                            <textarea name="textarea" class="form-control" rows="3" cols="12" v-model="observaciones"></textarea>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button" v-if="tipoAccion==1" @click="registrarOT()" class="btn btn-success colorVerde">
                                <i class="fa fa-floppy-o"></i>
                                Guardar</button>
                            <button type="button" v-if="tipoAccion==2"  class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->


            <!-- Inicio del modal Eliminar -->
            <div class="modal fade" :class="{'mostrar':modalParametros}" aria-hidden="true">
                <div class="modal-dialog modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:150px;">
                            <div v-if="tipoAccion==2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="company">Fecha Servicio <span style="color:red">(*)</span></label>
                                        <input type="date" class="form-control" v-model="fechaReprogramar">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company">Hora<span style="color:red">(*)</span></label>
                                        <select class="form-control" v-model="horaReprogramar">
                                                    <option value="00:00">00:00</option>
                                                    <option value="00:30">00:30</option>
                                                    <option value="01:00">01:00</option>
                                                    <option value="01:30">01:30</option>
                                                    <option value="02:00">02:00</option>
                                                    <option value="02:30">02:30</option>
                                                    <option value="03:00">03:00</option>
                                                    <option value="03:30">03:30</option>
                                                    <option value="04:00">04:00</option>
                                                    <option value="04:30">04:30</option>
                                                    <option value="05:00">05:00</option>
                                                    <option value="05:30">05:30</option>
                                                    <option value="06:00">06:00</option>
                                                    <option value="06:30">06:30</option>
                                                    <option value="07:00">07:00</option>
                                                    <option value="07:30">07:30</option>
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30">08:30</option>
                                                    <option value="09:00">09:00</option>
                                                    <option value="09:30">09:30</option>
                                                    <option value="10:00">10:00</option>
                                                    <option value="10:30">10:30</option>
                                                    <option value="11:00">11:00</option>
                                                    <option value="11:30">11:30</option>
                                                    <option value="12:00">12:00</option>
                                                    <option value="12:30">12:30</option>
                                                    <option value="13:00">13:00</option>
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00">14:00</option>
                                                    <option value="14:30">14:30</option>
                                                    <option value="15:00">15:00</option>
                                                    <option value="15:30">15:30</option>
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30">16:30</option>
                                                    <option value="17:00">17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <option value="18:00">18:00</option>
                                                    <option value="18:30">18:30</option>
                                                    <option value="19:00">19:00</option>
                                                    <option value="19:30">19:30</option>
                                                    <option value="20:00">20:00</option>
                                                    <option value="20:30">20:30</option>
                                                    <option value="21:00">21:00</option>
                                                    <option value="21:30">21:30</option>
                                                    <option value="22:00">22:00</option>
                                                    <option value="22:30">22:30</option>
                                                    <option value="23:00">23:00</option>
                                                    <option value="23:30">23:30</option>
                                                </select>
                                    </div>
                                            
                                </div>
                            </div>
                            <div v-else-if="tipoAccion==3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="company">Escriba maximo 500 caracteres por favor: <span style="color:red">(*)</span></label>
                                        <textarea name="textarea" rows="4" class="form-control" v-model="descripcionModificar"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <h4 v-text="tituloMensaje"></h4>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde" @click="btnSuspender()">
                                <i class="fa fa-floppy-o"></i> Suspender
                            </button>
                            <button type="button" v-else-if="tipoAccion==2"  class="btn btn-success colorVerde" @click="btnReprogramar()">
                                <i class="fa fa-floppy-o"></i> Reprogramar
                            </button>
                            <button type="button" v-else-if="tipoAccion==3"  class="btn btn-success colorVerde" @click="btnModificar()">
                                <i class="fa fa-floppy-o"></i> Modificar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin del modal Eliminar -->





            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modalCliente}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-md" role="document" style="max-width: 65%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Nombre <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="nombre">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Paterno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoPaterno">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Materno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoMaterno">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Nit </label>
                                            <input type="number" class="form-control" v-model="nit">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Razon Social <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="razonSocial">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Correo <span style="color:red">(*)</span></label>
                                            <input type="email" class="form-control" v-model="correo">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Telefono <span style="color:red">(*)</span></label>
                                            <input type="number" class="form-control" v-model="telefono">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Contacto <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="contacto">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Tipo Cliente <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-html="listadoTipoCliente" v-model="tipoCliente">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Categoria Cliente <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="categoria">
                                                <option value="E">EVENTUAL</option>
                                                <option value="F">FIJO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Frecuencia del Servicio <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="frecuencia">
                                                <option value="M">MENSUAL</option>
                                                <option value="B">BIMESTRAL</option>
                                                <option value="T">TRIMESTRAL</option>
                                                <option value="S">SEMESTRAL</option>
                                                <option value="A">ANUAL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Ciudad <span style="color:red">(*)</span></label>
                                            
                                            <select class="form-control" v-html="listadoCiudad" v-model="ciudad">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="company">Direccion</label>
                                            <input type="text" class="form-control" v-model="direccion">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Ejecutivo <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-html="listadoEjecutivo" v-model="ejecutivo">
                                            </select>
                                        </div>
                                    </div>
                                </div>
        
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModalCliente()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-success colorVerde" @click="registrarCliente()">
                              <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
    export default {
        data(){
            return {
                mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",
                
                codOTPDF:0,
                cliente:"",
                equipo:"",
                fechaServicio:"",
                telefono:"",
                contacto:"",
                horaServico:"",
                precio:"",
                razonSocial:"",
                tipoPago:"",
                nit:"",
                direccion:"",
                nroTecnicos:"",
                tiempoServicio:"",
                ejecutivoVentas:"",
                frecuencia:"",
                reponsable:"",
                observaciones:"",
                categoria:"",
                especificacion:"",

                fechaReprogramar:"",
                horaReprogramar:"",
                
                modal: 0,
                modalParametros:0,
                tituloMensaje:"",
                tituloModal:'',
                tipoAccion:0,
                selectAllPlaga: false,
                selectPlaga: [],
                selectAllLugar:false,
                selectLugar:[],
                arrayCliente:[],
                listaCliente: "",
                arrayEquipo:[],
                listaEquipo: "",
                arrayPlagas:[],
                arrayLugares:[],
                arrayOt:[],
                idOT:0,
                estadoModificar:'',
                modalCliente:0,

                nombre:"",
                apellidoPaterno:"",
                apellidoMaterno:"",
                nit:"",
                razonSocial:"",
                correo:"",
                telefono:"",
                contacto:"",
                tipoCliente:"",
                categoria:"",
                frecuencia:"",
                ciudad:"",
                direccion:"",
                ejecutivo:"",
                arrayTipoCliente:[],
                listadoTipoCliente:"",
                arrayCiudad:[],
                listadoCiudad:"",
                arrayEjecutivo:[],
                listadoEjecutivo:"",

                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                buscar:'',

                //modificar
                descripcionModificar:""
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
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
            imprimirPDF(){
                  if(this.idOT>0){
                   
                    var pagina = "http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo="+ this.idOT;
                    var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                    window.open(pagina,"",opciones);
                    this.idOT=0;
                }
                else
                swal("Mensaje", "Debe seleccionar una OT.", "warning");

            },
            registrarCliente(){
                
                if(this.nombre!="" && this.apellidoPaterno!="" && this.apellidoMaterno!="" && this.telefono!="" && this.contacto!="" && this.tipoCliente
                && this.categoria !="" && this.frecuencia!="" && this.razonSocial!="" && this.correo!="" && this.ciudad!=""  && this.ejecutivo!="")
               {
                let me = this;
                    axios.post('/cliente/registrar',{
                            'nombre':this.nombre,
                            'apellidoPaterno':this.apellidoPaterno,
                            'apellidoMaterno':this.apellidoMaterno,
                            'nit':this.nit,
                            'razonSocial':this.razonSocial,
                            "correo":this.correo,
                            'telefono':this.telefono,
                            'contacto':this.contacto,
                            'tipoCliente':this.tipoCliente,
                            "categoria":this.categoria,
                            "frecuencia":this.frecuencia,
                            "ciudad":this.ciudad,
                            "direccion":this.direccion,
                            "ejecutivo":this.ejecutivo,
                        }).then(function (reponse){    
                            me.cerrarModalCliente();  
                            me.listadoCliente();        
                            swal("Cliente Registrado!", "Guardado Correctamente.", "success");
                                       
                        })
                        .catch(function(error){
                            me.controlError(error);
                        });
               }
               else
               {
                   swal("Mensaje", "Debe introducir todos los Datos.", "warning");
               }
               
            },
            listaTipoCliente() {
            let me = this;
            axios .get("/Mip/Cliente/listaTipoCliente")
                .then(function(reponse) {
                me.arrayTipoCliente = reponse.data;
                for (var i = 0; i < me.arrayTipoCliente.length; i++) {
                    me.listadoTipoCliente +='<option value="' +me.arrayTipoCliente[i].COD_TIPO_CLIENTE +'">' +me.arrayTipoCliente[i].DESCRIPCION +"</option>";
                }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            listaEjecutivo() {
            let me = this;
            axios .get("/Mip/Cliente/ejecutivo")
                .then(function(reponse) {
                me.arrayEjecutivo = reponse.data;
                for (var i = 0; i < me.arrayEjecutivo.length; i++) {
                    me.listadoEjecutivo +='<option value="' +me.arrayEjecutivo[i].COD_PERSONAL +'">' +me.arrayEjecutivo[i].NOMBRE +' '+me.arrayEjecutivo[i].AP_PATERNO+' '+me.arrayEjecutivo[i].AP_MATERNO+"</option>";
                }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            listaCiudad() {
            let me = this;
            axios .get("/Mip/Cliente/ciudad")
                .then(function(reponse) {
                me.arrayCiudad = reponse.data;
                for (var i = 0; i < me.arrayCiudad.length; i++) {
                    me.listadoCiudad +='<option value="' +me.arrayCiudad[i].COD_CIUDAD +'">' +me.arrayCiudad[i].DESCRIPCION +"</option>";
                }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            cerrarModalCliente(){
                this.modalCliente=0;
            },
            btnSuspender(){
                let me = this;
                axios.put('/ot/suspender',{
                        'idOT': this.idOT,
                    }).then(function (response) {
                        me.cerrarModal();
                        swal('Mensaje','OT Suspendida','success' );
                        me.listadoOt(1,'');
                    }).catch(function (error) {
                        me.controlError(error);
                    });
            },
            btnReprogramar(){
                if(this.fechaReprogramar!="" && this.horaReprogramar!="")
                {
                    let me = this;

                    axios.put('/ot/reprogramar',{
                        'idOT': me.idOT,
                        'fechaReprogramar': me.fechaReprogramar,
                        'horaReprogramar': me.horaReprogramar
                    }).then(function (response) {
                        me.cerrarModal();
                        swal('Mensaje','OT Reprogramada','success' );
                        me.listadoOt(1,'');
                    }).catch(function (error) {
                        me.controlError(error);
                    });
                }
                else
                    swal("Advertencia!", "Debe seleccionar todos los campos", "warning");
            },
            btnModificar(){
                if(this.descripcionModificar!="")
                {
                  
                    let me = this;

                    axios.put('/ot/modificar',{
                        'idOT': me.idOT,
                        'descripcionModificar': me.descripcionModificar,
                    }).then(function (response) 
                    {
                        me.cerrarModal();
                        swal('Mensaje','OT Reprogramada','success' );
                        me.listadoOt(1,'');
                    }).catch(function (error) {
                        me.controlError(error);
                    });
                }
                else
                    swal("Advertencia!", "Debe seleccionar este Campo", "warning");
            },
            
            registrarOT(){    
                var plaga = this.selectPlaga.length;
                var lugar = this.selectLugar.length;

                if(plaga>0 && lugar>0){
                    this.validarOT();
                }
                else
                    swal("Advertencia!", "Debe seleccionar al menos un lugar a tratar y una Plaga a tratar.", "warning");
            },
            validarOT(){
                if(this.cliente!="" && this.fechaServicio!="" && this.horaServico!="" && this.razonSocial!="" && this.nit!=""&& this.tipoPago!="" 
                && this.precio!="" && this.observaciones!="" && this.nroTecnicos!="" && this.tiempoServicio!="" && this.ejecutivoVentas!="" && this.equipo!=""
                && this.reponsable!="" && this.categoria!="" && this.especificacion !="" && this.frecuencia!="")
                {
                    /*
                    alert(this.cliente);
                    //fecha registro
                    alert(this.fechaServicio);
                    alert(this.horaServico);
                    alert(this.razonSocial);
                    alert(this.nit);
                    alert(this.tipoPago);
                    //plazo - null
                    alert(this.precio);
                    //bs-1
                    alert(this.observaciones);
                    alert(this.nroTecnicos);
                    alert(this.tiempoServicio);
                    alert(this.ejecutivoVentas);
                    alert(this.equipo);
                    alert(this.reponsable);
                    //nro orden - ok
                    //NRO_ORDEN_PADRE  = 0
                    //APROBADA = 0
                    //INICIAL = 1
                    //CLIENTE_INICIAL = 0
                    alert(this.categoria);
                    alert(this.especificacion);
                    //cod usuario
                    //fecha alta =  CURDATE()
                    //ACTIVO = 1
                    //ESTADO_FRECUENCIA = 1
                    alert(this.frecuencia);
                    //FACTURA = 0
                    */


                   
                    let me = this;
                    axios.post('/Mip/ot/registrar',{
                        'cliente':this.cliente,
                        'fechaServicio':this.fechaServicio,
                        'horaServico':this.horaServico,
                        'razonSocial':this.razonSocial,
                        'nit':this.nit,
                        "tipoPago":this.tipoPago,

                        'precio':this.precio,
                        'observaciones':this.observaciones,
                        'nroTecnicos':this.nroTecnicos,
                        'tiempoServicio':this.tiempoServicio,
                        'ejecutivoVentas':this.ejecutivoVentas,
                        "equipo":this.equipo,

                        'reponsable':this.reponsable,
                        'categoria':this.categoria,
                        'especificacion':this.especificacion,
                        'frecuencia':this.frecuencia,
                        'dataPlaga':this.selectPlaga,
                        'dataLugar':this.selectLugar,
                    }).then(function (reponse){   
                        
                        swal("OT Registrada!", "Guardado Correctamente.", "success");
                        
                        let me = this;
                        me.listadoOt(1,'');
                        cerrarModal();
                        
                    })
                    .catch(function(error){
                        console.log(error);
                    });
                }
                else
                swal("Advertencia!", "Debe introducir Todos los datos.", "warning");
            },
            obtDatosCliente(){
                for (var v = 0; v < this.arrayCliente.length; v++) 
                {
                    if(this.arrayCliente[v].COD_CLIENTE==this.cliente){
                        this.telefono=this.arrayCliente[v].TELEFONO;
                        this.direccion=this.arrayCliente[v].DIRECCION;
                        this.contacto=this.arrayCliente[v].CONTACTO
                        this.razonSocial=this.arrayCliente[v].RAZON_SOCIAL;
                        this.nit=this.arrayCliente[v].CI_NIT;
                        break;
                    }
                }
            },
            listadoOt(page,buscar) {
            let me = this;
            var url = '/Mip/ot/listar1?page='+page+"&buscar="+buscar;
            axios .get(url)
                .then(function(reponse) {
                    var resp =  reponse.data;
                    if(resp=="expiro"){
                        me.cerrarSesion();
                    }
                    else
                    {
                        me.arrayOt = resp.ot.data;
                        me.pagination = resp.pagination;
                    }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            cambiarPagina(page,buscar){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listadoOt(page,buscar);
            },
            listadoEquipo() {
            let me = this;
            axios .get("/Mip/equipos/listar")
                .then(function(reponse) {
                me.arrayEquipo = reponse.data;
                for (var i = 0; i < me.arrayEquipo.length; i++) {
                    me.listaEquipo +='<option value="' +me.arrayEquipo[i].COD_EQUIPO +'">' +me.arrayEquipo[i].DESCRIPCION +"</option>";
                }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            listadoCliente() {
           
            let me = this;
            axios .get("/Mip/Cliente/listadoActivo")
                .then(function(reponse) {
                me.arrayCliente = reponse.data;
                for (var i = 0; i < me.arrayCliente.length; i++) {
                    me.listaCliente +='<option value="' +me.arrayCliente[i].COD_CLIENTE +'">' +me.arrayCliente[i].NOMBRE +"</option>";
                }
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            listadoPlagas() {
            let me = this;
            axios .get("/Mip/plagas/listar")
                .then(function(reponse) {
                me.arrayPlagas = reponse.data;
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            listadoLugares() {
            let me = this;
            axios .get("/Mip/lugares/listar")
                .then(function(reponse) {
                me.arrayLugares = reponse.data;
                })
                .catch(function(error) {
                me.controlError(error);
                });
            },
            selectPlagaC() {
             
                this.selectPlaga = [];
                if (!this.selectAllPlaga) {
                    for (let i in this.arrayPlagas) {
                    this.selectPlaga.push(this.arrayPlagas[i].COD_PLAGA);
                    }
                }
            },
            selectLugarC() {
                this.selectLugar = [];
                if (!this.selectAllLugar) {
                    for (let i in this.arrayLugares) {
                    this.selectLugar.push(this.arrayLugares[i].COD_LUGARES);
                    }
                }
            },
            asignarIdOT(idOT,estado,id){
                this.idOT=idOT;
                this.estadoModificar = estado;
                this.cambiarColorFila(id);
            },
            cambiarColorFila(id)
            {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            verificarEstado(estado){
                
                if(this.idOT>0)
                {
                    if(this.estadoModificar!="SM")
                    {
                        switch(estado)
                        {
                            case "suspender":   this.abrirModal('suspender');break;
                            case "reprogramar": this.abrirModal('reprogramar');break;
                            case "modificar":   this.abrirModal('modificar');break;
                        } 
                    }
                    else
                    this.abrirModal('otModificada');
                }
                else
                    this.abrirModal('vacio')
            },
            abrirModal(accion, data = []){
                        switch(accion)
                        {
                            case "registrar":
                            { 
                                this.modal=1;
                                this.tituloModal='Orden de Trabajo';
                                this.tipoAccion=1;
                                break;
                            }
                            case "suspender":
                            { 
                                this.modalParametros=1;
                                this.tituloModal='Suspender OT';
                                this.tipoAccion=1;
                                this.tituloMensaje="Estas seguro de Suspender la OT?";
                                break;
                            }
                            case "reprogramar":
                            { 
                                this.modalParametros=1;
                                this.tituloModal='Reprogramar OT';
                                this.tipoAccion=2;
                                this.tituloMensaje="Estas seguro de Reprogramar la OT?";
                                break;
                            }
                            case "vacio":
                            { 
                                this.modalParametros=1;
                                this.tituloModal='MENSAJE';
                                this.tituloMensaje="Debe seleccionar una OT";
                                break;
                            }
                            case "otModificada":{
                                this.modalParametros=1;
                                this.tituloModal='MENSAJE';
                                this.tituloMensaje="Estimado usuario, usted ya hizo una solicitud para MODIFICAR la OT seleccionada.";
                                break;
                            }
                            case "cliente":
                            { 
                                //this.listaCiudad();
                                //this.listaEjecutivo();
                                //this.listaTipoCliente();

                                this.modalCliente=1;
                                this.tituloModal='REGISTRAR CLIENTE';
                                break;
                            }
                            case "modificar":
                            {
                                this.modalParametros=1;
                                this.tituloModal='SOLICITUD DE MODIFICACION OT';
                                this.tipoAccion=3;
                                //this.tituloMensaje="Estas seguro de Reprogramar la OT?";
                                break;
                            }
                            
                        }                    
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
                this.modalParametros=0;
                this.idOT=0;
                this.descripcionModificar='';
                this.tipoAccion=0;
            },
           
            mensajeCorrecto(mensaje){
                swal(mensaje,"","success" ); 
            },
            cerrarSesion(){
                swal({
                        title: 'La sesion ha Expirado, Debe iniciar sesion nuevamente?',
                        type: 'warning',
                        confirmButtonColor: 'green',
                        confirmButtonText: 'Aceptar',
                        confirmButtonClass: 'btn btn-success',
                        })
                        .then((result) => 
                        {
                        if (result.value) 
                        {
                            location.href = "http://mip.isi-app.online/";
                        } 
                        }) 
            },
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////////////// - CONTROL DE EXEPCIONES -///////////////////////////////////////////////////////    
            controlError(error)
            {
            var array=[];
            var cadena = String(error);
                if(error.response.status==422)
                {
                    array  = error.response.data.errors;
                }
                else
                {
                    if(error.response.status==419){
                        this.cerrarSesion();
                    }
                    else
                    {
                        var cadena = String(error);
                        cadena = cadena.substr(cadena.length-3,3)
                        swal("Mensaje ",this.mensajeError +" "+cadena, "error");
                    }
                    
                }
                return array;
            },
        },
        mounted() {
            this.listadoOt(1,this.buscar);
 
        }
    }
</script>

<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }

    

    .colorRojo {
        background-color: crimson;
    }

    .colorVerde {
        background-color:green;
    }
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .colorAzul{
        background-color:#226e8a;
    }
    .modal-body {
        position: relative;
        padding: 20px;
        height: 600px;
        overflow-y: scroll;
    }

</style>
