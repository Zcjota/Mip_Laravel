<template>
  <main class="main">
    <ol class>

    </ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header" v-if="listado">
          <button type="button" @click="cambiarPanel(0)" class="btn btn-success colorVerde">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>

        <div class="card-header" v-else>
          <button type="button" @click="cambiarPanel(1)" class="btn btn-danger" style="display:none">
            <i class="fa fa-arrow-circle-left"></i>&nbsp;Atras
          </button>
          
          <button class="btn btn-danger colorRojo" type="button" @click="cambiarPanel(1)">
                  <i class="fa fa-arrow-circle-left"></i>&nbsp;
                  Atras
          </button>
          <button class="btn btn-success colorVerde" type="button" @click="registrarReserva()" >
                  <i class="fa fa-floppy-o"></i>&nbsp;
                  Guardar
            </button>
        </div>
        <!--Inicio Panel Listado Reserva -->
        <template v-if="listado">
          <!--
          <div class="card-header">
            <button type="button" @click="cambiarPanel(0)" class="btn btn-success colorVerde">
              <i class="icon-plus"></i>&nbsp;Nuevo
            </button>
          </div>
          -->
          <div class="card-body">
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">Lista de Reservas</h1>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                  <div class="input-group">
                      <input type="text" v-model="buscar" @keyup="listarReservas(1,buscar)" class="form-control" placeholder="Buscar Cliente">
                      <button type="submit" class="btn btn-primary" @click="listarReservas(1,buscar)"><i class="fa fa-search"></i> Buscar</button>
                   </div>
              </div>
            </div>
            <div style="overflow-x:auto;">
              <table class="table table-bordered table-striped table-sm table-hover">
                <thead>
                  <!--#29363d -->
                  <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                    <th style="width:40px;">Ver</th>
                    <th style="width:40px;">Estado</th>
                    <th style="width:40px;">OT</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Hora Inicio</th>
                    <th>Tiempo Servicio</th>
                    <th>Tiempo Tranporte</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="reserva in arrayReserva" :key="reserva.codReserva">
                    <td>
                      <button type="button" class="btn btn-warning" @click="modificar(reserva)" title="Editar Reserva">
                        <i class="icon-pencil"></i>
                      </button>
                    </td>
                    <td v-if="reserva.estado">
                        <button type="button" class="btn btn-danger colorRojo" @click="cambiarEstado(reserva,'Desactivar','Desactivado',0)" title="Desactivar">
                          <i class="fa fa-times"></i>
                        </button>
                    </td>
                    <td v-else>
                        <button type="button" class="btn btn-success colorVerde" @click="cambiarEstado(reserva,'Activar','Activado',1)" title="Activar">
                            <i class="fa fa-check"></i>
                        </button>
                    </td>
                    <td>
                      <button type="button" class="btn btn-primary colorAzul" @click="ModalOT(1,reserva)" title="Asignar OT">
                        <i class="fa fa-link" aria-hidden="true"></i>
                      </button>
                    </td>
                    <td v-text="reserva.fecha"></td>
                    <td v-text="reserva.NOMBRE+' '+reserva.AP_CLIENTE+' '+reserva.AM_CLIENTE"></td>
                    <td v-text="reserva.horainicio"></td>
                    <td v-text="reserva.tiempoServicio"></td>
                    <td v-text="reserva.tiempoTransporte"></td>
                    <td>
                      <div v-if="reserva.tipo">
                        <span class="badge badge-primary colorAzul">Replicacion</span>
                      </div>
                      <div v-else>
                        <span class="badge badge-dark">Reserva</span>
                      </div>
                    </td>

                    <td>
                      <div v-if="reserva.estado">
                        <span class="badge badge-success colorVerde">Activo</span>
                      </div>
                      <div v-else>
                        <span class="badge badge-danger colorRojo">Desactivado</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <nav>
              <ul class="pagination">
                  <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar)">Ant</a>
                  </li>
                  <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar)" v-text="page"></a>
                  </li>
                  <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                      <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar)">Sig</a>
                  </li>
              </ul>
            </nav>
          </div>
        </template>
        <!--Fin Panel Listado Reserva -->


        <!--Inicio Panel Registro Reserva -->
        <template v-else>
          <!--<div class="card-header">
              <button type="button" @click="cambiarPanel(1)" class="btn btn-danger" style="display:none">
                <i class="fa fa-arrow-circle-left"></i>&nbsp;Atras
              </button>
              <button class="btn btn-danger colorRojo" type="button" @click="cambiarPanel(1)">
                  <i class="fa fa-arrow-circle-left"></i>&nbsp;Atras
              </button>
              <button class="btn btn-success colorVerde" type="button" @click="registrarReserva()" >
                  <i class="fa fa-floppy-o"></i>&nbsp;Guardar
              </button>
          </div>-->
          <div class="card-body">
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">Registro de Reserva</h1>
              </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="company">
                    Cliente<span style="color:red">(*)</span>
                    </label>
                    <div class="input-group">
                        <input class="form-control colorBloqueado" type="text" v-model="nombreSeleccionado" placeholder="Cliente" disabled="true">
                        <button class="btn btn-primary" title="Buscar Clientes" type="button" @click="buscarCliente()">
                          <i class="fa fa-search" aria-hidden="true"></i> Buscar
                        </button>
                        <button class="btn btn-success" title="Agregar Clientes" type="button" @click="panelCliente()">
                          <i class="fa fa-plus" aria-hidden="true"></i> Agregar
                        </button>
                    </div>
                    <span v-if="errorReserva.cliente" class="error rojo">{{errorReserva.cliente[0]}}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="company">
                        Tiempo de Transporte
                        <span style="color:red">(*)</span>
                    </label>
                    <select class="form-control" v-model="tiempoTransporte">
                        <option v-for="tiempo in vectorTiempo" :key="tiempo.value" v-bind:value="tiempo.value" v-text="tiempo.text">
                        </option>
                    </select>
                    <span v-if="errorReserva.tiempoTransporte" class="error rojo">{{errorReserva.tiempoTransporte[0]}}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="company">
                        Equipo Tecnico
                        <span style="color:red">(*)</span>
                    </label>
                      <select class="form-control" v-model="equipoTecnico">
                      </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
              <ul class="nav nav-tabs" role="tablist" >
                <li class="nav-item">
                  <a class="nav-link active show" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                  Reservas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"
                  >Replicacion</a>
                </li>
              </ul>
              <div class="tab-content"  style="background-color:#f0f3f5">
                <div class="tab-pane active show" id="home" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="company"> FechaR <span style="color:red">(*)</span>
                              </label>
                              <div class="input-group">
                                <input class="form-control" type="date" v-model="fechaReserva" @change="filtrarFechaTecnicoReserva()"  placeholder="fecha" style="height:35px;">
                              </div>
                               <span v-if="errorReserva.fechaReserva" class="error rojo">{{errorReserva.fechaReserva[0]}}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                          <label for="company">
                              HoraR <span style="color:red">(*)</span>
                          </label>
                          <select class="form-control" v-model="horaInicio">
                              <option v-for="horario in vectorHorario" :key="horario.value" v-bind:value="horario.value" v-text="horario.horario">
                              </option>
                          </select>
                          <span v-if="errorReserva.horaInicio" class="error rojo">{{errorReserva.horaInicio[0]}}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="company">
                                Tiempo de ServicioR
                                <span style="color:red">(*)</span>
                            </label>
                            <select class="form-control" v-model="tiempoServicio" @change="obtTiempoServicio()">
                                <option v-for="tiempo in vectorTiempo" :key="tiempo.value" v-bind:value="tiempo.value" v-text="tiempo.text">
                                </option>
                            </select>
                            <span v-if="errorReserva.tiempoServicio" class="error rojo">{{errorReserva.tiempoServicio[0]}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="height:450px;overflow-x:auto;">
                        <div class="col-md-3" style="padding: 0px;width:100px;">
                        
                            <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1" style="width:100%">
                            <thead class="color">
                                <tr>
                                  <th><input type="checkbox" v-model="selectAll" @click="select()"></th>
                                  <th class="centro">Tecnicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tecnico in listaTecnico" :key="tecnico.COD_PERSONAL">
                                  <td>
                                      <label class="form-checkbox">
                                        <input type="checkbox" :value="tecnico.COD_PERSONAL" v-model="selected">
                                        <i class="form-icon"></i>
                                      </label>
                                  </td>
                                  <td>
                                      <button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;" type="button" v-text="tecnico.NOMBRE +' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO">
                                      </button>
                                  </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <div class="col-md-9" style="padding: 0px;width:110px;overflow-y: scroll;">
                            <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1" style="width:100%">
                            <thead class="color">
                                <tr>
                                  <td class="encabezado" v-for="horario in vectorHorario" :key="horario.horario" v-text="horario.horario"></td>
                                </tr>
                            </thead>
                            <tbody v-html="filaBodyReserva"></tbody>
                            </table>
                        <!--</div>-->
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="profile" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="company"> Fecha<span style="color:red">(*)</span>
                              </label>
                              <div class="input-group">
                                <input class="form-control" type="date" v-model="fechaReservaReplicacion" @change="filtrarFechaTecnicoReplicacion_1()"  placeholder="fecha" style="height:35px;">
                              </div>
                               <span v-if="validarFechaRep" class="rojo">Este Campo es Obligatorio</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                          <label for="company">
                              Hora <span style="color:red">(*)</span>
                          </label>
                          <select class="form-control" v-model="horaInicioReplicacion">
                              <option v-for="horario in vectorHorario" :key="horario.value" v-bind:value="horario.value" v-text="horario.horario">
                              </option>
                          </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="company">
                                Tiempo de Servicio
                                <span style="color:red">(*)</span>
                            </label>
                            <select class="form-control" v-model="tiempoServicioReplicacion">
                                <option v-for="tiempo in vectorTiempo" :key="tiempo.value" v-bind:value="tiempo.value" v-text="tiempo.text">
                                </option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="height:450px;overflow-x:auto;">
                        <div class="col-md-3" style="padding: 0px;width:100px;">
                        
                            <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1" style="width:100%">
                            <thead class="color">
                                <tr>
                                  <th><input type="checkbox" v-model="selectAllReplicacion" @click="selectReplicacion()"></th>
                                  <th class="centro">Tecnicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tecnico in listaTecnico" :key="tecnico.COD_PERSONAL">
                                  <td>
                                      <label class="form-checkbox">
                                        <input type="checkbox" :value="tecnico.COD_PERSONAL" v-model="selectedReplicacion">
                                        <i class="form-icon"></i>
                                      </label>
                                  </td>
                                  <td>
                                      <button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;" type="button" v-text="tecnico.NOMBRE +' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO">
                                      </button>
                                  </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <div class="col-md-9" style="padding: 0px;width:110px;overflow-y: scroll;">
                            <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1" style="width:100%">
                            <thead class="color">
                                <tr>
                                  <td class="encabezado" v-for="horario in vectorHorario" :key="horario.horario" v-text="horario.horario"></td>
                                </tr>
                            </thead>
                            <tbody v-html="filaBodyReservaReplicacion"></tbody>
                            </table>
                        <!--</div>-->
                        </div>
                    </div>
                </div> 
              </div>  
              </div>
            </div>
          </div>
        </template>
        <!--Fin Panel Registro Reserva -->















        <!-- Inicio del modal Seleccionar Cliente -->
            <div class="modal fade" :class="{'mostrar':mostrarModalCliente}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">LISTA DE CLIENTES</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModalCliente()">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:500px;">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" v-model="buscar" @keyup="listadoCliente(1,buscar)" class="form-control" placeholder="Buscar Cliente">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div style="overflow-x:auto;">
                                      <!-- table-striped-->
                                      <table class="table table-bordered table-striped table-hover table-sm">
                                          <thead>
                                              <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                                  <th style="width:40px;"></th>
                                                  <th>Nombre</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              <!-- <tr v-for="(cliente,index) in arrayCliente" :key="cliente.COD_CLIENTE" @click="selectedRow(index)"> -->
                                              <tr v-for="(cliente) in arrayCliente" :key="cliente.COD_CLIENTE" >
                                                  <td>
                                                      <button type="button" @click="seleccionarCliente(cliente)" class="btn btn-primary">
                                                      <i class="fa fa-plus"></i>
                                                      </button> &nbsp;
                                                  </td>
                                                  <td v-text="cliente.NOMBRE+' '+cliente.AP_CLIENTE+' '+cliente.AM_CLIENTE"></td>
                                              </tr>
                                          </tbody>
                                      </table>
                                    </div>
                                    <nav>
                                      <ul class="pagination">
                                          <li class="page-item" v-if="paginationCliente.current_page > 1">
                                              <a class="page-link" href="#" @click.prevent="cambiarPaginaCliente(paginationCliente.current_page - 1,buscar)">Ant</a>
                                          </li>
                                          <li class="page-item" v-for="page in pagesNumberCliente" :key="page" :class="[page == isActivedCliente ? 'active' : '']">
                                              <a class="page-link" href="#" @click.prevent="cambiarPaginaCliente(page,buscar)" v-text="page"></a>
                                          </li>
                                          <li class="page-item" v-if="paginationCliente.current_page < paginationCliente.last_page">
                                              <a class="page-link" href="#" @click.prevent="cambiarPaginaCliente(paginationCliente.current_page + 1,buscar)">Sig</a>
                                          </li>
                                      </ul>
                                  </nav>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger colorRojo" data-dismiss="modal" @click="cerrarModalCliente()">
                              <i class="fa fa-arrow-circle-left"></i>
                              Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin del modal  Seleccionar Cliente -->




      
            <!--Inicio del modal Registrar Cliente-->
            <div class="modal fade" :class="{'mostrar':modalCliente}" role="dialog" >
                <div class="modal-dialog modal-success modal-lg ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">REGISTRAR CLIENTE</h4>
                            <button type="button" class="close" @click="cerrarModalCliente()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:450px;overflow-y: scroll;">
                               <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Nombre <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="nombre">
                                            <span v-if="errorCliente.nombre" class="error rojo">{{errorCliente.nombre[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Paterno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoPaterno">
                                            <span v-if="errorCliente.apellidoPaterno" class="error rojo">{{errorCliente.apellidoPaterno[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Materno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoMaterno">
                                            <span v-if="errorCliente.apellidoMaterno" class="error rojo">{{errorCliente.apellidoMaterno[0]}}</span>
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
                                            <span v-if="errorCliente.razonSocial" class="error rojo">{{errorCliente.razonSocial[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Correo <span style="color:red">(*)</span></label>
                                            <input type="email" class="form-control" v-model="correo">
                                            <span v-if="errorCliente.correo" class="error rojo">{{errorCliente.correo[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Telefono <span style="color:red">(*)</span></label>
                                            <input type="number" class="form-control" v-model="telefono">
                                            <span v-if="errorCliente.telefono" class="error rojo">{{errorCliente.telefono[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Contacto <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="contacto">
                                            <span v-if="errorCliente.contacto" class="error rojo">{{errorCliente.contacto[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Tipo Cliente <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="tipoCliente">
                                              <option v-for="tipoCliente in arrayTipoCliente" :key="tipoCliente.COD_TIPO_CLIENTE" v-bind:value="tipoCliente.COD_TIPO_CLIENTE" v-text="tipoCliente.DESCRIPCION"></option>
                                            </select>
                                            <span v-if="errorCliente.tipoCliente" class="error rojo">{{errorCliente.tipoCliente[0]}}</span>
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
                                            <span v-if="errorCliente.categoria" class="error rojo">{{errorCliente.categoria[0]}}</span>
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
                                            <span v-if="errorCliente.frecuencia" class="error rojo">{{errorCliente.frecuencia[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Ciudad <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="ciudad">
                                              <option v-for="ciudad in arrayCiudad" :key="ciudad.COD_CIUDAD" v-bind:value="ciudad.COD_CIUDAD" v-text="ciudad.DESCRIPCION"></option>
                                            </select>
                                            <span v-if="errorCliente.ciudad" class="error rojo">{{errorCliente.ciudad[0]}}</span>
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
                                            <select class="form-control" v-model="ejecutivo">
                                                <option v-for="ejecutivo in arrayEjecutivo" :key="ejecutivo.COD_PERSONAL" v-bind:value="ejecutivo.COD_PERSONAL" v-text="ejecutivo.NOMBRE+' '+ejecutivo.AP_PATERNO+' '+ejecutivo.AP_MATERNO"></option>
                                            </select>
                                            <span v-if="errorCliente.ejecutivo" class="error rojo">{{errorCliente.ejecutivo[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <input type="checkbox" @click="certificado()"> Datos del Certificado
                                            </div>
                                            <div v-if="clickCertificado">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="company">Nombre Principal<span style="color:red">(*)</span></label>
                                                                <input type="text" class="form-control" v-model="nombrePrincipalCertificado">
                                                                <span v-if="errorCliente.nombrePrincipalCertificado" class="error rojo">{{errorCliente.nombrePrincipalCertificado[0]}}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="company">Nombre Secundario<span style="color:red">(*)</span></label>
                                                                <input type="text" class="form-control" v-model="nombreSecundarioCertificado">
                                                                <span v-if="errorCliente.nombreSecundarioCertificado" class="error rojo">{{errorCliente.nombreSecundarioCertificado[0]}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="company">Direccion<span style="color:red">(*)</span></label>
                                                                <textarea name="textarea" class="form-control" rows="2" cols="12" v-model="DireccionCertificado"></textarea>
                                                                <span v-if="errorCliente.DireccionCertificado" class="error rojo">{{errorCliente.DireccionCertificado[0]}}</span>
                                                            </div>
                                                        </div>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
        
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModalCliente()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button"  class="btn btn-success colorVerde" @click="registrarCliente()">
                              <i class="fa fa-floppy-o"></i> Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal Registrar Cliente-->


            



            <!--Inicio del modal OT-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modalOT}" role="dialog" >
                <div class="modal-dialog modal-success modal-lg" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="ModalOT(0)" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="overflow-y: scroll;">
                            <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Categoria<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="categoriaOT">
                                                    <option value="1">A</option>
                                                    <option value="2">B</option>
                                                    <option value="3">C</option>
                                                </select>
                                                <span v-if="errorOT.categoria" class="error rojo">{{errorOT.categoria[0]}}</span>
                                            </div>
                                            <div class="col-md-4">
                                            
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Especificacion<span style="color:red">(*)</span></label>
                                                <select class="form-control" v-model="especificacionOT">
                                                    <option value="1">Inicial</option>
                                                    <option value="2">Mantenimiento</option>
                                                    <option value="3">Reaplicacion 1</option>
                                                    <option value="4">Reaplicacion 2</option>
                                                    <option value="5">Queja</option>
                                                </select>
                                                <span v-if="errorOT.especificacion" class="error rojo">{{errorOT.especificacion[0]}}</span>
                                            </div>
                                    </div><br>  




                                    <div class="card-header">Datos Generales</div>
                                    <div class="card-body" style="background-color:#f0f3f5;">
                                    <form action="" method="post" >
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Cliente</label>
                                                <div class="input-group">
                                                <input type="text" class="form-control colorBloqueado" v-model="clienteOT" disabled="true">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Telefono</label>
                                                <input type="number" class="form-control colorBloqueado" v-model="telefonoOT" disabled="true">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Contacto</label>
                                                <input type="text" class="form-control colorBloqueado" v-model="contactoOT" disabled="true">
                                            </div>
                                        </div>

                                        <div class="row">
                                            
                                            <div class="col-md-4">
                                                <label for="company">Razon Social</label>
                                                <input type="text" class="form-control colorBloqueado" v-model="razonSocialOT" disabled="true">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Nit</label>
                                                <input type="text" class="form-control colorBloqueado" v-model="nitOT" disabled="true">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="company">Tipo Pago<span style="color:red">(*)</span></label>
                                                <div class="input-group">
                                                <select class="form-control" v-model="tipoPagoOT">
                                                    <option value="1">Contado</option>
                                                    <option value="2">Credito</option>
                                                </select>
                                                <input v-if="tipoPagoOT==2" type="number" v-model="plazo" class="form-control" placeholder="Plazo Dias" >
                                                </div>                                      
                                                <span v-if="errorOT.tipoPago" class="error rojo">{{errorOT.tipoPago[0]}}</span>
                                                <span v-if="errorOT.plazo" class="error rojo">{{errorOT.plazo[0]}}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="company">Precio<span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="precioOT">
                                                <span v-if="errorOT.precio" class="error rojo">{{errorOT.precio[0]}}</span>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="company">Direccion</label>
                                                <textarea name="textarea" class="form-control colorBloqueado" rows="2" cols="12" v-model="direccionOT" disabled="true"></textarea>
                                            </div>
                                        </div>
                                    </form>
                                    </div>




                                    <div class="row">
                                      <div class="col">
                                      <div class="card">
                                      <div class="card-body">
                                      <ul class="nav nav-tabs" id="myTab1" role="tablist">
                                      <li class="nav-item">
                                      <a class="nav-link active show" id="plaga-tab" data-toggle="tab" href="#plaga" role="tab" aria-controls="plaga" aria-selected="false">Plagas a Tratar</a>
                                      </li>
                                      <li class="nav-item">
                                      <a class="nav-link" id="lugares-tab" data-toggle="tab" href="#lugares" role="tab" aria-controls="lugares" aria-selected="false">Lugares a Tratar</a>
                                      </li>
                                      <li class="nav-item">
                                      <a class="nav-link " id="observaciones-tab" data-toggle="tab" href="#observaciones" role="tab" aria-controls="observaciones" aria-selected="true">Observaciones y Otros</a>
                                      </li>
                                      </ul>
                                      <div class="tab-content" id="myTab1Content">
                                      <div class="tab-pane fade active show" id="plaga" role="tabpanel" aria-labelledby="plaga-tab">
                                        <div style="overflow-x:auto;height:290px;">
                                        <table class="table table-bordered table-striped" >
                                              <thead>
                                                  <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                                      <th style="width:40px;"><input type="checkbox" v-model="selectAllPlagaOT" @click="selectPlagaTodo()"></th>
                                                      <th>Plaga a Tratar</th>
                                                  </tr>
                                              </thead>
                                              <tbody >
                                                  <tr v-for="plagas in arrayPlagasOT" :key="plagas.COD_PLAGA">
                                                      <td>
                                                          <label class="form-checkbox">
                                                          <input type="checkbox" :value="plagas.COD_PLAGA" v-model="selectPlagaOT">
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
                                      <div class="tab-pane fade" id="lugares" role="tabpanel" aria-labelledby="lugares-tab">
                                      <div style="overflow-x:auto;height:290px;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                                    <th style="width:40px;"><input type="checkbox" v-model="selectAllLugarOT" @click="selectLugarTodo()"></th>
                                                    <th>Lugares a Tratar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="lugares in arrayLugaresOT" :key="lugares.COD_LUGARES">
                                                    <td>
                                                        <label class="form-checkbox">
                                                        <input type="checkbox" :value="lugares.COD_LUGARES" v-model="selectLugarOT">
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
                                      <div class="tab-pane fade" id="observaciones" role="tabpanel" aria-labelledby="observaciones-tab" style="background-color:#f0f3f5;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="company">Nro de Tecnicos<span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="nroTecnicosOT">
                                                <span v-if="errorOT.nroTecnicos" class="error rojo">{{errorOT.nroTecnicos[0]}}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company">Frecuencia <span style="color:red">(*)</span></label>
                                                <input type="number" class="form-control" v-model="frecuenciaOT">
                                                <span v-if="errorOT.frecuencia" class="error rojo">{{errorOT.frecuencia[0]}}</span>
                                            </div>
                                        </div>
                                   
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="company">Observaciones<span style="color:red">(*)</span></label>
                                                <textarea name="textarea" class="form-control" rows="3" cols="12" v-model="observacionesOT"></textarea>
                                                <span v-if="errorOT.observaciones" class="error rojo">{{errorOT.observaciones[0]}}</span>
                                            </div>
                                        </div>
                                      </div>
                                      </div>
                          
                                      </div>
                                      </div>
                                      </div>
                            </div>
                                </div>
                            </div>
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="ModalOT(0)" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button"  @click="registrarOT()" class="btn btn-success colorVerde">
                                <i class="fa fa-floppy-o"></i>
                                Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->
      </div>

    </div>


  </main>
</template>

<script>

export default {
  data() {
    return {
      ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////
      arrayReserva:[],
      listado:1,
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
      nombre:'',

      tiempoTransporte: '',
      equipoTecnico:'',
      fechaReserva:'',
      horaInicio:'',
      tiempoServicio:'',  
      filaBodyReserva:'',
      errorReserva:[],
      selected: [],
      listaTecnico: [],
      listadoDetalleReserva: [],
      selectAll: false,

      fechaReservaReplicacion:'',
      horaInicioReplicacion:'',
      tiempoServicioReplicacion:'',
      filaBodyReservaReplicacion:'',
      validarFechaRep:0,
      selectedReplicacion:[],
      listaTecnicoReplicacion: [],
      listadoDetalleReservaReplicacion: [],
      selectAllReplicacion:false,
  
      vectorTiempo: [
        { text: '30 Minutos  |  Media Hora', value: '30' },
        { text: '60 Minutos  |  1 Hora', value: '60' },
        { text: '90 Minutos  |  Hora y media', value: '90' },
        { text: '120 Minutos |  2 Horas', value: '120' },
        { text: '150 Minutos |  2 y media Horas', value: '150' },
        { text: '180 Minutos |  3 Horas', value: '180' }
      ],
      vectorHorario: [
        { horario: '00:00', value:'00:00:00'},
        { horario: '00:30', value:'00:30:00'},
        { horario: '01:00', value:'01:00:00'},
        { horario: '01:30', value:'01:30:00'},
        { horario: '02:00', value:'02:00:00'},
        { horario: '02:30', value:'02:30:00'},
        { horario: '03:00', value:'03:00:00'},
        { horario: '03:30', value:'03:30:00'},
        { horario: '04:00', value:'04:00:00'},
        { horario: '04:30', value:'04:30:00'},
        { horario: '05:00', value:'05:00:00'},
        { horario: '05:30', value:'05:30:00'},
        { horario: '06:00', value:'06:00:00'},
        { horario: '06:30', value:'06:30:00'},
        { horario: '07:00', value:'07:00:00'},
        { horario: '07:30', value:'07:30:00'},
        { horario: '08:00', value:'08:00:00'},
        { horario: '08:30', value:'08:30:00'},
        { horario: '09:00', value:'09:00:00'},
        { horario: '09:30', value:'09:30:00'},
        { horario: '10:00', value:'10:00:00'},
        { horario: '10:30', value:'10:30:00'},
        { horario: '11:00', value:'11:00:00'},
        { horario: '11:30', value:'11:30:00'},
        { horario: '12:00', value:'12:00:00'},
        { horario: '12:30', value:'12:30:00'},
        { horario: '13:00', value:'13:00:00'},
        { horario: '13:30', value:'13:30:00'},
        { horario: '14:00', value:'14:00:00'},
        { horario: '14:30', value:'14:30:00'},
        { horario: '15:00', value:'15:00:00'},
        { horario: '15:30', value:'15:30:00'},
        { horario: '16:00', value:'16:00:00'},
        { horario: '16:30', value:'16:30:00'},
        { horario: '17:00', value:'17:00:00'},
        { horario: '17:30', value:'17:30:00'},
        { horario: '18:00', value:'18:00:00'},
        { horario: '18:30', value:'18:30:00'},
        { horario: '19:00', value:'19:00:00'},
        { horario: '19:30', value:'19:30:00'},
        { horario: '20:00', value:'20:00:00'},
        { horario: '20:30', value:'20:30:00'},
        { horario: '21:00', value:'21:00:00'},
        { horario: '21:30', value:'21:30:00'},
        { horario: '22:00', value:'22:00:00'},
        { horario: '22:30', value:'22:30:00'},
        { horario: '23:00', value:'23:00:00'},
        { horario: '23:30', value:'23:30:00'},
      ],



      ////////////////////////////////////// - CLIENTE -///////////////////////////////////////////////////////
      mostrarModalCliente:0,
      cliente:'', // es el codigo
      nombre:'',
      nombreSeleccionado:'',
      modalCliente:0,

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
      clickCertificado:0,
      nombrePrincipalCertificado:"",
      nombreSecundarioCertificado:"",
      DireccionCertificado:"",
      arrayTipoCliente:[],
      arrayEjecutivo:[],
      arrayCiudad:[],
      errorCliente:[],

      arrayCliente :[],
      paginationCliente : {
        'total' : 0,
        'current_page' : 0,
        'per_page' : 0,
        'last_page' : 0,
        'from' : 0,
        'to' : 0,
      },



      //////////////////////////////////-     OT    -///////////////////////////////////////
      //OT
      modalOT:0,
      codReservaOT:0,
      clienteOT:"",
      codClienteOT:0,
      fechaServicioOT:"",
      telefonoOT:"",
      contactoOT:"",
      horaServicoOT:"",
      precioOT:"",
      razonSocialOT:"",
      tipoPagoOT:"",
      plazo:"",
      nitOT:"",
      direccionOT:"",
      nroTecnicosOT:"",
      tiempoServicioOT:"",
      ejecutivoVentasOT:"",
      frecuenciaOT:"",
      observacionesOT:"",
      categoriaOT:"",
      especificacionOT:"",
      arrayLugaresOT:[],
      arrayPlagasOT:[],
      selectAllLugarOT:false,
      selectAllPlagaOT:false,
      tituloModal:"",
      selectPlagaOT:[],
      selectLugarOT:[],
      errorOT:[],
    };
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
            isActivedCliente: function(){
                return this.paginationCliente.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumberCliente: function() {
                if(!this.paginationCliente.to) {
                    return [];
                }
                
                var from = this.paginationCliente.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.paginationCliente.last_page){
                    to = this.paginationCliente.last_page;
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
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////
    listarReservas(page,buscar) {
      let me = this;
      var url = 'Mip/reservas/listarReserva?page='+page+"&buscar="+buscar;
      axios.get(url).then(function(reponse) {
       
          var resp =  reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cambiarPagina(page,buscar){
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarReservas(page,buscar);
    },
    cambiarPanel(panel) {
      //this.limpiarDatos();
      this.listado = panel;
    },
    select() {
      this.selected = [];
      if (!this.selectAll) {
        for (let i in this.listaTecnico) {
          this.selected.push(this.listaTecnico[i].COD_PERSONAL);
        }
      }
    },
    selectReplicacion() {
      this.selectedReplicacion = [];
      if (!this.selectAllReplicacion) {
        for (let i in this.listaTecnico) {
          this.selectedReplicacion.push(this.listaTecnico[i].COD_PERSONAL);
        }
      }
    },
    //Inicio tecnicos de la fecha actual
    listarTecnico() {
      let me = this;
      var url = '/tecnicos/listadoTecnicosFechaActual?fecha='+me.fechaReserva;
      axios.get(url)
        .then(function(reponse) {  
          me.listaTecnico = reponse.data;
          me.listaTecnicoReplicacion=reponse.data;
          me.listarDetalleReservasFechaActual(me.listaTecnico,me.fechaReserva);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
      //obtiene las reservas de los tecnicos de la fecha actual
    listarDetalleReservasFechaActual(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           me.listadoDetalleReserva = reponse.data;
           me.listadoDetalleReservaReplicacion = reponse.data;
           me.filaBodyReserva = me.bodyFila(listaTecnico,me.listadoDetalleReserva);
           me.filaBodyReservaReplicacion = me.filaBodyReserva;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //Fin tecnicos de la fecha actual



    //Inicio Filtros de Reservas
    filtrarFechaTecnicoReserva(){
      let me = this;
      var url = '/tecnicos/listadoTecnicosFechaActual?fecha='+me.fechaReserva;
      axios.get(url)
        .then(function(reponse) {  
          me.listaTecnico = reponse.data;
          me.listarDetalleReservasFechaSeleccionadaReserva(me.listaTecnico,me.fechaReserva);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReserva(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           me.listadoDetalleReserva = reponse.data;
           me.filaBodyReserva = me.bodyFila(listaTecnico,me.listadoDetalleReserva);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //Fin Filtros de Reservas



    //Inicio Filtros de Replicacion 1
    filtrarFechaTecnicoReplicacion_1(){
      let me = this;
      var url = '/tecnicos/listadoTecnicosFechaActual?fecha='+me.fechaReservaReplicacion;
      axios.get(url)
        .then(function(reponse) {  
          me.listaTecnicoReplicacion=reponse.data;
          me.listarDetalleReservasFechaSeleccionadaReplicacion_1(me.listaTecnicoReplicacion,me.fechaReservaReplicacion);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReplicacion_1(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           me.listadoDetalleReservaReplicacion = reponse.data;
           me.filaBodyReservaReplicacion = me.bodyFila(listaTecnico,me.listadoDetalleReservaReplicacion);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //Fin Filtros de Replicacion 1

    bodyFila(listaTecnico,listadoDetalleReserva) {
      let fila = "";
      for (var i = 0; i < listaTecnico.length; i++) {
        fila += '<tr style="height: 60px;">';
        fila +='<td style="display: none"><button class="btn btn-square btn-success btn-block" type="button">' +
          listaTecnico[i].NOMBRE +" " +listaTecnico[i].AP_PATERNO +" " +listaTecnico[i].AP_MATERNO +
          "</button></td>";
        var ok=0;
        for (var v = 0; v < this.vectorHorario.length; v++) 
        {
          let temporal = "";
          var opc=0;
          
          if(listaTecnico[i].activo==1)
          {
            if(ok==0)
            {
              for(var w = 0; w < listaTecnico[i].codReserva; w++)
              {
                if(w==listaTecnico[i].codReserva-1)
                {
                  temporal +='<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
                }
                else
                {
                  temporal +='<td class="centro"><a class="badge badge-danger  colorRojo" href="#">Reservado</a></td>';
                  v++;
                }
              }
              ok=1;
            }
          }
          for (var z = 0;z < listadoDetalleReserva.length;z++) 
          {
            var horaInicio = listadoDetalleReserva[z].horaInicio.substring(0, 5);
            var tiempoServicio = listadoDetalleReserva[z].tiempoServicio;
            var tiempoTransporte = listadoDetalleReserva[z].tiempoTransporte;
            var tiempoTransporte = tiempoTransporte / 30;
            tiempoServicio = tiempoServicio / 30;
            
            //tiempoServicio = tiempoServicio + tiempoTransporte;
            
            var nombreTec = listaTecnico[i].NOMBRE+' '+listaTecnico[i].AP_PATERNO+' '+listaTecnico[i].AP_MATERNO;
            var nombreTecDetReserva = listadoDetalleReserva[z].NOMBRE+' '+listadoDetalleReserva[z].AP_PATERNO+' '+listadoDetalleReserva[z].AP_MATERNO;
            
            if (this.vectorHorario[v].horario == horaInicio && nombreTec == nombreTecDetReserva)
            {

              temporal +='<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
              for (var x = 0; x < tiempoServicio; x++) {
                  if(v!=47)
                {
                v++;
                temporal +='<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
                }
              }
              for (var t = 0; t < tiempoTransporte-1; t++) {
                if(v!=47)
                {
                v++;
                temporal +='<td class="centro"><a class="badge badge-warning colorAmarrillo" href="#">Transporte</a></td>';
                }
              }

            }
          }
          if (temporal == "") fila += '<td class="centro"></td>';
          else {
            fila += temporal;
          }
        }
        fila += "</tr>";
      }
      return fila;
    },
    cambiarEstado(data,Activar,Activado,estado){
        swal({
          title: 'Esta seguro de '+ Activar +' la Reserva?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: 'green',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Aceptar!',
          cancelButtonText: 'Cancelar',
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
            
          reverseButtons: true
          }).then((result) => {
          if (result.value) 
          {
            let me = this;
            axios.put('/reserva/Desactivar',{
                'id': data.codReserva,
                'estado':estado,
                'COD_ORDEN_TRABAJO':data.codOT
            }).then(function (response) {
                me.listarReservas(1,'');
                swal(Activado+'!','La reserva ha sido '+Activado+' con éxito.','success' )
            }).catch(function (error) {
                console.log(error);
            });
                    
          } else if (
              result.dismiss === swal.DismissReason.cancel
          ) {
          }
          }) 
    },


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////// - CLIENTE -///////////////////////////////////////////////////////
    buscarCliente(){
      this.mostrarModalCliente=1;
    },
    cerrarModalCliente(){
      this.mostrarModalCliente=0;
      this.modalCliente=0;
      this.errorCliente=[];
      this.buscar='';

    },
    listadoCliente(page,buscar) {
      let me = this;
      var url = '/Mip/Cliente/listadoActivo?page='+page+"&buscar="+buscar;
      axios .get(url)
        .then(function(reponse) {
          var resp =  reponse.data;
          me.arrayCliente = resp.cliente.data;
          me.paginationCliente = resp.pagination;
        })
        .catch(function(error) {
          console.log(error); 
        });
    },
    seleccionarCliente(cliente){
        let me=this;
        me.cliente = cliente.COD_CLIENTE;
        me.nombreSeleccionado= cliente.NOMBRE+" "+cliente.AP_CLIENTE+" "+cliente.AM_CLIENTE;
        me.cerrarModalCliente();
    },
    panelCliente(){
      this.modalCliente=1;
      this.listaTipoCliente();
      this.listaEjecutivo();
      this.listaCiudad();
    },
    listaTipoCliente() {
        let me = this;
        axios .get("/Mip/Cliente/listaTipoCliente")
        .then(function(reponse) {
          me.arrayTipoCliente = reponse.data;
        })
        .catch(function(error) {
        console.log(error);
        });
    },
    listaEjecutivo() {
        let me = this;
        axios .get("/Mip/Cliente/ejecutivo")
        .then(function(reponse) {
          me.arrayEjecutivo = reponse.data;
        })
        .catch(function(error) {
        console.log(error);
        });
    },
    listaCiudad() {
      let me = this;
      axios .get("/Mip/Cliente/ciudad")
      .then(function(reponse) {
        me.arrayCiudad = reponse.data;
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    certificado(){
        if(this.clickCertificado==0)
            this.clickCertificado=1;
        else
            this.clickCertificado=0;
    },
    limpiar(){
      this.nombre="";
      this.apellidoPaterno="";
      this.apellidoMaterno="";
      this.nit="";
      this.razonSocial="";
      this.correo="";
      this.telefono="";
      this.contacto="";
      this.tipoCliente="";
      this.categoria="";
      this.frecuencia="";
      this.ciudad="";
      this.direccion="";
      this.ejecutivo="";
      this.errorCliente=[];
    },
    registrarCliente() 
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
          'clickCertificado':this.clickCertificado,
          'nombrePrincipalCertificado':this.nombrePrincipalCertificado,
          'nombreSecundarioCertificado':this.nombreSecundarioCertificado,
          'DireccionCertificado':this.DireccionCertificado
      }).then(function (reponse){   
          me.limpiar(); 
          me.cerrarModalCliente();          
          swal("Cliente Registrado!", "Guardado Correctamente.", "success");
          me.listadoCliente(1,'');               
      })
      .catch(function(error){
          if(error.response.status==422){
          me.errorCliente  = error.response.data.errors;
      }
      });
    },


    



     //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - OT -///////////////////////////////////////////////////////
    limpiarOt(){
        this.clienteOT='';
        this.codReservaOT='';
        this.codClienteOT='';
        this.telefonoOT='';
        this.fechaServicioOT='';
        this.horaServicoOT='';
        this.razonSocialOT='';
        this.nitOT='';
        this.tipoPagoOT='';
        this.precioOT='';
        this.observacionesOT='';
        this.nroTecnicosOT='';
        this.tiempoServicioOT='';
        this.frecuenciaOT='';
        this.selectPlagaOT=[];
        this.selectLugarOT=[];
        this.categoriaOT='';
        this.especificacionOT='';
        this.errorOT=[];
    },
    ModalOT(estado,datosReserva=[]){
      this.limpiarOt();
    
      if(datosReserva.codOT==null)
      {
        if(estado==1)
        {
            this.tituloModal="ORDEN DE OT | "+datosReserva.NOMBRE+' '+datosReserva.AP_CLIENTE+' '+datosReserva.AM_CLIENTE;
            this.clienteOT=datosReserva.NOMBRE+' '+datosReserva.AP_CLIENTE+' '+datosReserva.AM_CLIENTE;
            this.fechaServicioOT=datosReserva.fecha;
            this.horaServicoOT=datosReserva.horainicio;
            this.tiempoServicioOT=datosReserva.tiempoServicio;
            this.telefonoOT=datosReserva.TELEFONO;
            this.contactoOT =datosReserva.CONTACTO;
            this.razonSocialOT = datosReserva.RAZON_SOCIAL;
            this.nitOT=datosReserva.CI_NIT;
            this.direccionOT = datosReserva.DIRECCION;
            this.codClienteOT = datosReserva.codCliente;
            this.codReservaOT = datosReserva.codReserva;
        }

        this.modalOT=estado;
      }
      else
      {
        this.modalParametros=1;
      }
    },
    listadoPlagas() {
        let me = this;
        axios .get("/Mip/plagas/listar")
        .then(function(reponse) {
        me.arrayPlagasOT = reponse.data;
        })
        .catch(function(error) {
        console.log(error);
        });
    },
    listadoLugares() {
      let me = this;
      axios .get("/Mip/lugares/listar")
      .then(function(reponse) {
      me.arrayLugaresOT = reponse.data;
      })
      .catch(function(error) {
      console.log(error);
      });
    },






    },
  mounted() {
    this.listarReservas(1,this.buscar);
    this.listadoPlagas();
    this.listadoLugares();
    //this.listarTecnico();





   /* this.listadoPlagas();
    this.listadoLugares();
    this.listarDetalleReservasCompleto();
    this.listarDetalleReservasVisual();
    this.listarTecnico('');*/
    
  
  }
};
</script>

<style>
.centro {
  text-align: center;
}
.ancho {
  width: 50px;
}
.color {
  background-color: #20c997;
}
.rojo{
        color: crimson;
}
.colorRojo {
  background-color: crimson;
}
.colorAmarrillo {
  background-color: yellow;
  color: black;
}
.colorVerde {
  background-color:green;
}
.colorAzul {
  background-color:rgb(49, 83, 184);
}
.colorVerde1 {
  background-color:lightseagreen;
}
.ancho1 {
  width: 1000px;
}
.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
  background-color: #dbdbdb;
}
.bloqueado{
  background-color: #20c997;
}
.colorBorde{
  border-color: #ce0808;
}
.colorBloqueado{
  background-color: #ffebeb;
}
.encabezado{
  text-align: center;
  font-weight: bold;
}

</style>
