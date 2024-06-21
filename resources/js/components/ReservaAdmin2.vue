<template>
  <main class="main">
    <ol class></ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header" v-if="listado">
          <button
            type="button"
            @click="cambiarPanel(0)"
            class="btn btn-success colorVerde"
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button
            type="button"
            @click="imprimirPDF()"
            class="btn btn-success colorVerde"
          >
            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="imprimirExcel(fechaInicio)"
          >
            <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
          </button>
        </div>
        <div class="card-header" v-else>
          <button
            type="button"
            @click="cambiarPanel(1)"
            class="btn btn-danger"
            style="display: none"
          >
            <i class="fa fa-arrow-circle-left"></i>&nbsp;Atras
          </button>

          <button
            class="btn btn-danger colorRojo"
            type="button"
            @click="cambiarPanel(1)"
          >
            <i class="fa fa-arrow-circle-left"></i>&nbsp; Atras
          </button>
          <button
            class="btn btn-success colorVerde"
            type="button"
            @click="registrarReserva()"
          >
            <i class="fa fa-floppy-o"></i>&nbsp; Guardar
          </button>
        </div>
        <!--Inicio Panel Listado Reserva -->
        <template v-if="listado">
          <div class="card-body">
           
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">LISTA DE RESERVAS</h1>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <div class="input-group">
                  <input
                    type="text"
                    v-model="buscar"
                    @keyup="
                      listarReservasSinCarga(1, fechaInicio, fechaFin, buscar)
                    "
                    class="form-control"
                    placeholder="Buscar"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group">
                  <input
                    type="date"
                    v-model="fechaInicio"
                    class="form-control"
                    placeholder="Fecha Inicio"
                  />
                  <input
                    type="date"
                    v-model="fechaFin"
                    class="form-control"
                    placeholder="Fecha Fin"
                  />
                  <button
                    type="submit"
                    class="btn btn-primary"
                    @click="listarReservas(1, fechaInicio, fechaFin, buscar)"
                  >
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>

            <!--
            <div class="form-group row">
              <div class="col-md-6">
                  <div class="input-group">
                      <input type="text" v-model="buscar" @keyup="listarReservas(1,buscar)" class="form-control" placeholder="Buscar">
                      <button type="submit" class="btn btn-primary" @click="listarReservasReset(1,'')"><i class="fa fa-refresh"></i> Cargar</button>
                   </div>
              </div>
            </div>
            -->

            <div style="overflow-x: auto; height: 600px">
              <table id="tablaPrincipal" class="tabla" style="width: 100%">
                <thead>
                  <!--#29363d -->
                  <tr
                    style="
                      background-color: #243648f0;
                      color: #ecf0f1;
                      height: 40px;
                    "
                  >
                    <th class="encabezadoFijo" style="width: 40px"></th>
                    <th class="encabezadoFijo" style="width: 40px"></th>
                    <th class="encabezadoFijo" style="width: 40px"></th>
                    <th class="encabezadoFijo" style="width: 70px">Nro OT</th>
                    <th class="encabezadoFijo">Fecha</th>
                    <th class="encabezadoFijo">Creado Por</th>
                    <th class="encabezadoFijo">Cliente</th>
                    <th class="encabezadoFijo">Hora Inicio</th>
                    <th class="encabezadoFijo">Tiempo Servicio</th>
                    <th class="encabezadoFijo">Tiempo Tranporte</th>
                    <th class="encabezadoFijo">Tipo</th>
                    <th class="encabezadoFijo">Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="reserva in arrayReserva"
                    :key="reserva.codReserva"
                    @click="asignarIdOT(reserva.codOT, 'tablaPrincipal')"
                  >
                    <td>
                      <button
                        type="button"
                        class="btn btn-warning"
                        @click="modificar(reserva)"
                        title="Editar Reserva"
                      >
                        <i class="icon-pencil"></i>
                      </button>
                    </td>
                    <td v-if="reserva.estado">
                      <button
                        type="button"
                        class="btn btn-danger colorRojo"
                        @click="
                          cambiarEstado(reserva, 'Desactivar', 'Desactivado', 0)
                        "
                        title="Desactivar"
                      >
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                    <td v-else>
                      <button
                        type="button"
                        class="btn btn-success colorVerde"
                        @click="
                          cambiarEstado(reserva, 'Activar', 'Activado', 1)
                        "
                        title="Activar"
                      >
                        <i class="fa fa-check"></i>
                      </button>
                    </td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-primary colorAzul"
                        @click="ModalOT(1, reserva)"
                        title="Asignar OT"
                      >
                        <i class="fa fa-link" aria-hidden="true"></i>
                      </button>
                    </td>
                    <td v-text="reserva.NRO_ORDEN"></td>
                    <td v-text="reserva.fecha"></td>
                    <td
                      v-if="reserva.NOMB_PERSONAL != null"
                      v-text="
                        reserva.NOMB_PERSONAL + ' ' + reserva.APP_PERSONAL
                      "
                    ></td>
                    <td v-else></td>
                    <td
                      v-text="
                        reserva.NOMBRE +
                        ' ' +
                        reserva.AP_CLIENTE +
                        ' ' +
                        reserva.AM_CLIENTE
                      "
                    ></td>
                    <td v-text="reserva.horainicio"></td>
                    <td v-text="reserva.tiempoServicio"></td>
                    <td v-text="reserva.tiempoTransporte"></td>
                    <td>
                      <div v-if="reserva.tipo">
                        <span class="badge badge-primary colorAzul"
                          >Replicacion</span
                        >
                      </div>
                      <div v-else>
                        <span class="badge badge-dark">Reserva</span>
                      </div>
                    </td>

                    <td>
                      <div v-if="reserva.estado">
                        <span class="badge badge-success colorVerde"
                          >Activo</span
                        >
                      </div>
                      <div v-else>
                        <span class="badge badge-danger colorRojo"
                          >Desactivado</span
                        >
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <nav>
              <ul class="pagination">
                <li class="page-item" v-if="pagination.current_page > 1">
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      cambiarPagina(
                        pagination.current_page - 1,
                        fechaInicio,
                        fechaFin,
                        buscar
                      )
                    "
                    >Ant</a
                  >
                </li>
                <li
                  class="page-item"
                  v-for="page in pagesNumber"
                  :key="page"
                  :class="[page == isActived ? 'active' : '']"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      cambiarPagina(page, fechaInicio, fechaFin, buscar)
                    "
                    v-text="page"
                  ></a>
                </li>
                <li
                  class="page-item"
                  v-if="pagination.current_page < pagination.last_page"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      cambiarPagina(
                        pagination.current_page + 1,
                        fechaInicio,
                        fechaFin,
                        buscar
                      )
                    "
                    >Sig</a
                  >
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
                <h1 class="centro">REGISTRO DE RESERVAS</h1>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <!-- Fecha1-->
                  <label for="company">
                    Fecha de emision de Factura<span style="color: red"
                      >(*)</span
                    >
                  </label>
                  <div class="input-group">
                    <input
                      class="form-control"
                      type="date"
                      v-model="fechaemision"
                      placeholder="fecha"
                      style="height: 35px"
                    />
                  </div>
                  <span v-if="errorReserva.fechaemision" class="error rojo">{{
                    errorReserva.fechaemision[0]
                  }}</span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="company">
                    Cliente<span style="color: red">(*)</span>
                  </label>
                  <div class="input-group">
                    <input
                      class="form-control colorBloqueado"
                      type="text"
                      v-model="nombreSeleccionado"
                      placeholder="Cliente"
                      disabled="true"
                    />
                    <button
                      class="btn btn-primary"
                      title="Buscar Clientes"
                      type="button"
                      @click="buscarCliente()"
                    >
                      <i class="fa fa-search" aria-hidden="true"></i> Buscar
                    </button>
                    <button
                      class="btn btn-success"
                      title="Agregar Clientes"
                      type="button"
                      @click="panelCliente()"
                    >
                      <i class="fa fa-plus" aria-hidden="true"></i> Agregar
                    </button>
                  </div>
                  <span v-if="errorReserva.cliente" class="error rojo">{{
                    errorReserva.cliente[0]
                  }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="company">
                    Tiempo de Transporte
                    <span style="color: red">(*)</span>
                  </label>
                  <select class="form-control" v-model="tiempoTransporte">
                    <option
                      v-for="tiempo in vectorTiempo"
                      :key="tiempo.value"
                      v-bind:value="tiempo.value"
                      v-text="tiempo.text"
                    ></option>
                  </select>
                  <span
                    v-if="errorReserva.tiempoTransporte"
                    class="error rojo"
                    >{{ errorReserva.tiempoTransporte[0] }}</span
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="company"> Equipo Tecnico </label>
                  <select
                    class="form-control"
                    v-model="equipoTecnico"
                    @change="listarTecnicosEquipo()"
                  >
                    <option
                      v-for="equipo in arrayEquipo"
                      :key="equipo.COD_EQUIPO"
                      v-bind:value="equipo.COD_EQUIPO"
                      v-text="equipo.DESCRIPCION"
                    ></option>
                  </select>
                  <span v-if="errorReserva.equipo" class="error rojo">{{
                    errorReserva.equipo[0]
                  }}</span>
                </div>
              </div>
            </div>
            <br />
            <div class="row">
              <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a
                      class="nav-link active show"
                      data-toggle="tab"
                      href="#home"
                      role="tab"
                      aria-controls="home"
                      aria-selected="true"
                    >
                      Reservas</a
                    >
                  </li>
                  <li class="nav-item">
                    <a
                      class="nav-link"
                      data-toggle="tab"
                      href="#profile"
                      role="tab"
                      aria-controls="profile"
                      aria-selected="false"
                      >Replicacion</a
                    >
                  </li>
                </ul>
                <div class="tab-content" style="background-color: #f0f3f5">
                  <div class="tab-pane active show" id="home" role="tabpanel">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <!-- Fecha1-->
                          <label for="company">
                            Fecha<span style="color: red">(*)</span>
                          </label>
                          <div class="input-group">
                            <input
                              class="form-control"
                              type="date"
                              v-model="fechaReserva"
                              @change="filtrarFechaTecnicoReserva()"
                              placeholder="fecha"
                              style="height: 35px"
                            />
                          </div>
                          <span
                            v-if="errorReserva.fechaReserva"
                            class="error rojo"
                            >{{ errorReserva.fechaReserva[0] }}</span
                          >
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="company">
                            Hora <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="horaInicio"
                            @change="obtHora()"
                          >
                            <option
                              v-for="horario in vectorHorario"
                              :key="horario.value"
                              v-bind:value="horario.value"
                              v-text="horario.horario"
                            ></option>
                          </select>
                          <span
                            v-if="errorReserva.horaInicio"
                            class="error rojo"
                            >{{ errorReserva.horaInicio[0] }}</span
                          >
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="company">
                            Tiempo de Servicio
                            <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="tiempoServicio"
                            @change="obtTiempoServicio()"
                          >
                            <option
                              v-for="tiempo in vectorTiempo"
                              :key="tiempo.value"
                              v-bind:value="tiempo.value"
                              v-text="tiempo.text"
                            ></option>
                          </select>
                          <span
                            v-if="errorReserva.tiempoServicio"
                            class="error rojo"
                            >{{ errorReserva.tiempoServicio[0] }}</span
                          >
                        </div>
                      </div>
                    </div>

                    <div class="row" style="height: 480px; overflow-x: auto">
                      <div class="col-md-12" style="padding: 0px; width: 100px">
                        <table class="tabla" border="1" style="width: 100%">
                          <thead class="color">
                            <tr>
                              <th
                                class="encabezadoFijo centro"
                                v-for="tecnico in listaTecnico"
                                :key="tecnico.COD_PERSONAL"
                              >
                                <input
                                  type="checkbox"
                                  :value="tecnico.COD_PERSONAL"
                                  v-model="selected"
                                /><br />
                                {{
                                  tecnico.NOMBRE +
                                  " " +
                                  tecnico.AP_PATERNO +
                                  " " +
                                  tecnico.AP_MATERNO
                                }}
                              </th>
                            </tr>
                          </thead>
                          <tbody v-html="filaBodyReserva"></tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="profile" role="tabpanel">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="company"
                            >Fecha<span style="color: red">(*)</span>
                          </label>
                          <div class="input-group">
                            <input
                              class="form-control"
                              type="date"
                              v-model="fechaReservaReplicacion"
                              @change="filtrarFechaTecnicoReplicacion_1()"
                              placeholder="fecha"
                              style="height: 35px"
                            />
                          </div>
                          <span v-if="validarFechaRep" class="rojo"
                            >Este Campo es Obligatorio</span
                          >
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="company">
                            Hora <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="horaInicioReplicacion"
                          >
                            <option
                              v-for="horario in vectorHorario"
                              :key="horario.value"
                              v-bind:value="horario.value"
                              v-text="horario.horario"
                            ></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="company">
                            Tiempo de Servicio
                            <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="tiempoServicioReplicacion"
                          >
                            <option
                              v-for="tiempo in vectorTiempo"
                              :key="tiempo.value"
                              v-bind:value="tiempo.value"
                              v-text="tiempo.text"
                            ></option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row" style="height: 480px; overflow-x: auto">
                      <div class="col-md-12" style="padding: 0px; width: 100px">
                        <table class="tabla" border="1" style="width: 100%">
                          <thead class="color">
                            <tr>
                              <th
                                class="encabezadoFijo centro"
                                v-for="tecnico in listaTecnicoReplicacion"
                                :key="tecnico.COD_PERSONAL"
                              >
                                <input
                                  type="checkbox"
                                  :value="tecnico.COD_PERSONAL"
                                  v-model="selectedReplicacion"
                                /><br />
                                {{
                                  tecnico.NOMBRE +
                                  " " +
                                  tecnico.AP_PATERNO +
                                  " " +
                                  tecnico.AP_MATERNO
                                }}
                              </th>
                            </tr>
                          </thead>
                          <tbody v-html="filaBodyReservaReplicacion"></tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!--Fin Panel Registro Reserva -->
      </div>
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div
      class="modal fade"
      :class="{ mostrar: modal }"
      role="dialog"
      tabindex="-1"
      aria-labelledby="myModalLabel"
      style="display: none"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-success modal-lg" style="max-width: 80%">
        >
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="nombreCliente"></h4>
            <button
              type="button"
              class="close"
              @click="cerrarModalModificar()"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="height: 600px">
            <div class="row">
              <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a
                      class="nav-link active show"
                      data-toggle="tab"
                      href="#home"
                      role="tab"
                      aria-controls="home"
                      aria-selected="true"
                      >Reservas</a
                    >
                  </li>
                </ul>
                <div class="tab-content" style="background-color: #f0f3f5">
                  <div class="tab-pane active show" id="home" role="tabpanel">
                    <div class="row">
                      <div class="col-md-3">
                        <label for="company">
                          Fecha<span style="color: red">(*)</span>
                        </label>
                        <div class="input-group">
                          <input
                            class="form-control"
                            type="date"
                            v-model="fechaReservaModificar"
                            @change="
                              listarTrabajadoresModificar(
                                codReserva,
                                fechaReservaModificar
                              )
                            "
                            placeholder="fecha"
                            style="height: 35px"
                          />
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="company">
                            Hora <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="horaInicioModificar"
                          >
                            <option
                              v-for="horario in vectorHorario"
                              :key="horario.value"
                              v-bind:value="horario.value"
                              v-text="horario.horario"
                            ></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="company">
                            Tiempo de Servicio
                            <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            id="timSe"
                            name="timSe"
                            v-model="tiempoServicioModificar"
                          >
                            <option
                              v-for="tiempo in vectorTiempo"
                              :key="tiempo.value"
                              v-bind:value="tiempo.value"
                              v-text="tiempo.text"
                            ></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="company">
                            Tiempo de Transporte
                            <span style="color: red">(*)</span>
                          </label>
                          <select
                            class="form-control"
                            v-model="tiempoTransporteModificar"
                          >
                            <option
                              v-for="tiempo in vectorTiempo"
                              :key="tiempo.value"
                              v-bind:value="tiempo.value"
                              v-text="tiempo.text"
                            ></option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row" style="height: 400px; overflow-x: auto">
                      <div class="col-md-12" style="padding: 0px; width: 100px">
                        <table class="tabla" border="1" style="width: 100%">
                          <thead class="color">
                            <tr>
                              <th
                                class="encabezadoFijo centro"
                                v-for="tecnico in listaTecnicoModificar"
                                :key="tecnico.COD_PERSONAL"
                              >
                                <input
                                  type="checkbox"
                                  :value="tecnico.COD_PERSONAL"
                                  v-model="selectedModificar"
                                /><br />
                                {{
                                  tecnico.NOMBRE +
                                  " " +
                                  tecnico.AP_PATERNO +
                                  " " +
                                  tecnico.AP_MATERNO
                                }}
                              </th>
                            </tr>
                          </thead>
                          <tbody v-html="filaBodyReservaModificar"></tbody>
                        </table>
                      </div>
                    </div>

                    <!-- reserva3
                                        <div class="row" style="height:500px;overflow-x:auto;">
                                            <div class="col-md-3" style="padding: 0px;width:150px;">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1">
                                                <thead class="color">
                                                    <tr>
                                                      <th><input type="checkbox" v-model="selectAllModificar" @click="selectModificar()"></th>
                                                      <th class="centro">Tecnicos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="equipos in listaTecnico" :key="equipos.COD_PERSONAL">
                                                      <td>
                                                          <label class="form-checkbox">
                                                          <input type="checkbox" :value="equipos.COD_PERSONAL" v-model="selectedModificar">
                                                            <i class="form-icon"></i>
                                                          </label>
                                                      </td>
                                                      <td>
                                                          <button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;" type="button" v-text="equipos.NOMBRE +' '+equipos.AP_PATERNO+' '+equipos.AP_MATERNO">
                                                          </button>
                                                      </td>
                                                    </tr>
                                                </tbody>
                                                </table>
                                            </div>
                                            </div>
                                            <div class="col-md-9" style="padding: 0px;width:110px;">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-responsive-sm table-bordered table-striped table-hover" border="1" >
                                                <thead class="color">
                                                    <tr>
                                                      <td class="encabezado" v-for="horario in vectorHorario" :key="horario.horario" v-text="horario.horario"></td>
                                                    </tr>
                                                </thead>
                                                <tbody v-html="filaBodyReservaModificar"></tbody>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        -->
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="cerrarModalModificar()"
              class="btn btn-danger colorRojo"
            >
              <i class="fa fa-arrow-circle-left"></i> Cancelar
            </button>
            <button
              type="button"
              class="btn btn-success colorVerde"
              @click="actualizarReserva()"
            >
              <i class="icon-pencil"></i> Actualizar
            </button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

    <!-- Inicio del modal Seleccionar Cliente -->
    <div
      class="modal fade"
      :class="{ mostrar: mostrarModalCliente }"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-success modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">LISTA DE CLIENTES</h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="cerrarModalCliente()"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="height: 500px">
            <div class="form-group row">
              <div class="col-md-12">
                <div class="input-group">
                  <input
                    type="text"
                    v-model="buscar"
                    @keyup="listadoCliente(1, buscar)"
                    class="form-control"
                    placeholder="Buscar Cliente"
                  />
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-12">
                <div style="overflow-x: auto; height: 400px">
                  <!-- table-striped-->
                  <table
                    class="table table-bordered table-striped table-hover table-sm"
                  >
                    <thead>
                      <tr
                        style="
                          background-color: #243648f0;
                          color: #ecf0f1;
                          height: 40px;
                        "
                      >
                        <th class="encabezadoFijo" style="width: 40px"></th>
                        <th class="encabezadoFijo" style="width: 35%">
                          Nombre
                        </th>
                        <th class="encabezadoFijo">Direccion</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- <tr v-for="(cliente,index) in arrayCliente" :key="cliente.COD_CLIENTE" @click="selectedRow(index)"> -->
                      <tr
                        v-for="cliente in arrayCliente"
                        :key="cliente.COD_CLIENTE"
                      >
                        <td>
                          <button
                            type="button"
                            @click="seleccionarCliente(cliente)"
                            class="btn btn-primary"
                          >
                            <i class="fa fa-plus"></i>
                          </button>
                          &nbsp;
                        </td>
                        <td
                          v-if="cliente.AM_CLIENTE == null"
                          v-text="cliente.NOMBRE + ' ' + cliente.AP_CLIENTE"
                        ></td>
                        <td
                          v-else
                          v-text="
                            cliente.NOMBRE +
                            ' ' +
                            cliente.AP_CLIENTE +
                            ' ' +
                            cliente.AM_CLIENTE
                          "
                        ></td>
                        <td v-text="cliente.DIRECCION"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <nav>
                  <ul class="pagination">
                    <li
                      class="page-item"
                      v-if="paginationCliente.current_page > 1"
                    >
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                          cambiarPaginaCliente(
                            paginationCliente.current_page - 1,
                            buscar
                          )
                        "
                        >Ant</a
                      >
                    </li>
                    <li
                      class="page-item"
                      v-for="page in pagesNumberCliente"
                      :key="page"
                      :class="[page == isActivedCliente ? 'active' : '']"
                    >
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="cambiarPaginaCliente(page, buscar)"
                        v-text="page"
                      ></a>
                    </li>
                    <li
                      class="page-item"
                      v-if="
                        paginationCliente.current_page <
                        paginationCliente.last_page
                      "
                    >
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                          cambiarPaginaCliente(
                            paginationCliente.current_page + 1,
                            buscar
                          )
                        "
                        >Sig</a
                      >
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger colorRojo"
              data-dismiss="modal"
              @click="cerrarModalCliente()"
            >
              <i class="fa fa-arrow-circle-left"></i>
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin del modal  Seleccionar Cliente -->

    <!--Inicio del modal Registrar Cliente-->
    <div class="modal fade" :class="{ mostrar: modalCliente }" role="dialog">
      <div class="modal-dialog modal-success modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">REGISTRAR CLIENTE</h4>
            <button
              type="button"
              class="close"
              @click="cerrarModalCliente()"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="height: 450px; overflow-y: scroll">
            <form
              action=""
              method="post"
              enctype="multipart/form-data"
              class="form-horizontal"
            >
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Nombre <span style="color: red">(*)</span></label
                    >
                    <input type="text" class="form-control" v-model="nombre" />
                    <span v-if="errorCliente.nombre" class="error rojo">{{
                      errorCliente.nombre[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Apellido Paterno
                      <span style="color: red">(*)</span></label
                    >
                    <input
                      type="text"
                      class="form-control"
                      v-model="apellidoPaterno"
                    />
                    <span
                      v-if="errorCliente.apellidoPaterno"
                      class="error rojo"
                      >{{ errorCliente.apellidoPaterno[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company">Apellido Materno</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="apellidoMaterno"
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company">Nit </label>
                    <input type="number" class="form-control" v-model="nit" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company">Razon Social</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="razonSocial"
                    />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company">Correo</label>
                    <input type="email" class="form-control" v-model="correo" />
                    <span v-if="errorCliente.correo" class="error rojo">{{
                      errorCliente.correo[0]
                    }}</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Telefono <span style="color: red">(*)</span></label
                    >
                    <input
                      type="number"
                      class="form-control"
                      v-model="telefono"
                    />
                    <span v-if="errorCliente.telefono" class="error rojo">{{
                      errorCliente.telefono[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Contacto <span style="color: red">(*)</span></label
                    >
                    <input
                      type="text"
                      class="form-control"
                      v-model="contacto"
                    />
                    <span v-if="errorCliente.contacto" class="error rojo">{{
                      errorCliente.contacto[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Tipo Cliente <span style="color: red">(*)</span></label
                    >
                    <select class="form-control" v-model="tipoCliente">
                      <option
                        v-for="tipoCliente in arrayTipoCliente"
                        :key="tipoCliente.COD_TIPO_CLIENTE"
                        v-bind:value="tipoCliente.COD_TIPO_CLIENTE"
                        v-text="tipoCliente.DESCRIPCION"
                      ></option>
                    </select>
                    <span v-if="errorCliente.tipoCliente" class="error rojo">{{
                      errorCliente.tipoCliente[0]
                    }}</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Categoria Cliente
                      <span style="color: red">(*)</span></label
                    >
                    <select class="form-control" v-model="categoria">
                      <option value="E">EVENTUAL</option>
                      <option value="F">FIJO</option>
                    </select>
                    <span v-if="errorCliente.categoria" class="error rojo">{{
                      errorCliente.categoria[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Frecuencia del Servicio
                      <span style="color: red">(*)</span></label
                    >
                    <select class="form-control" v-model="frecuencia">
                      <option value="M">MENSUAL</option>
                      <option value="B">BIMESTRAL</option>
                      <option value="T">TRIMESTRAL</option>
                      <option value="S">SEMESTRAL</option>
                      <option value="A">ANUAL</option>
                    </select>
                    <span v-if="errorCliente.frecuencia" class="error rojo">{{
                      errorCliente.frecuencia[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Ciudad <span style="color: red">(*)</span></label
                    >
                    <select class="form-control" v-model="ciudad">
                      <option
                        v-for="ciudad in arrayCiudad"
                        :key="ciudad.COD_CIUDAD"
                        v-bind:value="ciudad.COD_CIUDAD"
                        v-text="ciudad.DESCRIPCION"
                      ></option>
                    </select>
                    <span v-if="errorCliente.ciudad" class="error rojo">{{
                      errorCliente.ciudad[0]
                    }}</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="company">Direccion</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="direccion"
                    />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company"
                      >Ejecutivo <span style="color: red">(*)</span></label
                    >
                    <select class="form-control" v-model="ejecutivo">
                      <option
                        v-for="ejecutivo in arrayEjecutivo"
                        :key="ejecutivo.COD_PERSONAL"
                        v-bind:value="ejecutivo.COD_PERSONAL"
                        v-text="
                          ejecutivo.NOMBRE +
                          ' ' +
                          ejecutivo.AP_PATERNO +
                          ' ' +
                          ejecutivo.AP_MATERNO
                        "
                      ></option>
                    </select>
                    <span v-if="errorCliente.ejecutivo" class="error rojo">{{
                      errorCliente.ejecutivo[0]
                    }}</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <input type="checkbox" @click="certificado()" /> Datos del
                      Certificado
                    </div>
                    <div v-if="clickCertificado">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="company"
                                >Nombre Principal<span style="color: red"
                                  >(*)</span
                                ></label
                              >
                              <input
                                type="text"
                                class="form-control"
                                v-model="nombrePrincipalCertificado"
                              />
                              <span
                                v-if="errorCliente.nombrePrincipalCertificado"
                                class="error rojo"
                                >{{
                                  errorCliente.nombrePrincipalCertificado[0]
                                }}</span
                              >
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="company"
                                >Nombre Secundario<span style="color: red"
                                  >(*)</span
                                ></label
                              >
                              <input
                                type="text"
                                class="form-control"
                                v-model="nombreSecundarioCertificado"
                              />
                              <span
                                v-if="errorCliente.nombreSecundarioCertificado"
                                class="error rojo"
                                >{{
                                  errorCliente.nombreSecundarioCertificado[0]
                                }}</span
                              >
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="company"
                                >Direccion<span style="color: red"
                                  >(*)</span
                                ></label
                              >
                              <textarea
                                name="textarea"
                                class="form-control"
                                rows="2"
                                cols="12"
                                v-model="DireccionCertificado"
                              ></textarea>
                              <span
                                v-if="errorCliente.DireccionCertificado"
                                class="error rojo"
                                >{{
                                  errorCliente.DireccionCertificado[0]
                                }}</span
                              >
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
            <button
              type="button"
              @click="cerrarModalCliente()"
              class="btn btn-danger colorRojo"
            >
              <i class="fa fa-arrow-circle-left"></i> Cancelar
            </button>
            <button
              type="button"
              class="btn btn-success colorVerde"
              @click="registrarCliente()"
            >
              <i class="fa fa-floppy-o"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--Fin del modal Registrar Cliente-->

    <!--Inicio del modal OT-->

    <!-- EMODAL CREACION DE OT -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{ mostrar: modalOT }"
      role="dialog"
    >
      <div class="modal-dialog modal-success modal-lg" role="document">
        <div class="modal-content" style="height: 580px">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal"></h4>
            <button
              type="button"
              class="close"
              @click="ModalOT(0)"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="overflow-y: scroll">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="row">
                    <div class="col-md-4">
                      <label for="company"
                        >Categoria<span style="color: red">(*)</span></label
                      >
                      <select class="form-control" v-model="categoriaOT">
                        <option value="1">A</option>
                        <option value="2">B</option>
                        <option value="3">C</option>
                      </select>
                      <span v-if="errorOT.categoria" class="error rojo">{{
                        errorOT.categoria[0]
                      }}</span>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                      <label for="company"
                        >Especificacion<span style="color: red"
                          >(*)</span
                        ></label
                      >
                      <select class="form-control" v-model="especificacionOT">
                        <option value="1">Inicial</option>
                        <option value="2">Mantenimiento</option>
                        <option value="3">Reaplicacion 1</option>
                        <option value="4">Reaplicacion 2</option>
                        <option value="5">Queja</option>
                      </select>
                      <span v-if="errorOT.especificacion" class="error rojo">{{
                        errorOT.especificacion[0]
                      }}</span>
                    </div>
                  </div>
                  <br />

                  <div class="card-header">Datos Generales</div>
                  <div class="card-body" style="background-color: #f0f3f5">
                    <form action="" method="post">
                      <div class="row">
                        <div class="col-md-4">
                          <label for="company">Cliente</label>
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control"
                              v-model="clienteOT"
                            />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for="company">Telefono</label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="telefonoOT"
                          />
                        </div>
                        <div class="col-md-4">
                          <label for="company">Contacto</label>
                          <input
                            type="text"
                            class="form-control"
                            v-model="contactoOT"
                          />
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-4">
                          <label for="company">Razon Social</label>
                          <input
                            type="text"
                            class="form-control"
                            v-model="razonSocialOT"
                          />
                        </div>
                        <div class="col-md-4">
                          <label for="company">Nit</label>
                          <input
                            type="text"
                            class="form-control"
                            v-model="nitOT"
                          />
                        </div>
                        <div class="col-md-4">
                          <label for="company"
                            >Tipo Pago<span style="color: red">(*)</span></label
                          >
                          <div class="input-group">
                            <select class="form-control" v-model="tipoPagoOT">
                              <option value="1">Contado</option>
                              <option value="2">Credito</option>
                            </select>
                            <input
                              v-if="tipoPagoOT == 2"
                              type="number"
                              v-model="plazo"
                              class="form-control"
                              placeholder="Plazo Dias"
                            />
                          </div>
                          <span v-if="errorOT.tipoPago" class="error rojo">{{
                            errorOT.tipoPago[0]
                          }}</span>
                          <span v-if="errorOT.plazo" class="error rojo">{{
                            errorOT.plazo[0]
                          }}</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label for="company"
                                  >Precio<span style="color: red"
                                    >(*)</span
                                  ></label
                                >
                                <input
                                  type="number"
                                  step="0.25"
                                  class="form-control"
                                  v-model="precioOT"
                                />
                                <span
                                  v-if="errorOT.precio"
                                  class="error rojo"
                                  >{{ errorOT.precio[0] }}</span
                                >
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group" v-if="isFactura">
                                Con Factura
                                <input type="checkbox" v-model="isFactura" />
                              </div>
                              <div class="form-group" v-else>
                                Sin Factura
                                <input type="checkbox" v-model="isFactura" />
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <!-- Fecha1-->
                            <label for="company">
                              Fecha de emision de Factura<span
                                style="color: red"
                                >(*)</span
                              >
                            </label>
                            <div class="input-group">
                              <input
                                class="form-control"
                                type="date"
                                v-model="fechaemision"
                                placeholder="fecha"
                                style="height: 35px"
                              />
                            </div>
                            <span
                              v-if="errorReserva.fechaemision"
                              class="error rojo"
                              >{{ errorReserva.fechaemision[0] }}</span
                            >
                          </div>
                        </div>

                        <div class="col-md-8">
                          <label for="company">Direccion</label>
                          <textarea
                            name="textarea"
                            class="form-control"
                            rows="3"
                            cols="12"
                            v-model="direccionOT"
                          ></textarea>
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
                              <a
                                class="nav-link active show"
                                id="plaga-tab"
                                data-toggle="tab"
                                href="#plaga"
                                role="tab"
                                aria-controls="plaga"
                                aria-selected="false"
                                >Plagas a Tratar</a
                              >
                            </li>
                            <li class="nav-item">
                              <a
                                class="nav-link"
                                id="lugares-tab"
                                data-toggle="tab"
                                href="#lugares"
                                role="tab"
                                aria-controls="lugares"
                                aria-selected="false"
                                >Lugares a Tratar</a
                              >
                            </li>
                            <li class="nav-item">
                              <a
                                class="nav-link"
                                id="observaciones-tab"
                                data-toggle="tab"
                                href="#observaciones"
                                role="tab"
                                aria-controls="observaciones"
                                aria-selected="true"
                                >Observaciones y Otros</a
                              >
                            </li>
                          </ul>
                          <div class="tab-content" id="myTab1Content">
                            <div
                              class="tab-pane fade active show"
                              id="plaga"
                              role="tabpanel"
                              aria-labelledby="plaga-tab"
                            >
                              <div style="overflow-x: auto; height: 290px">
                                <table
                                  class="table table-bordered table-striped"
                                >
                                  <thead>
                                    <tr
                                      style="
                                        background-color: #243648f0;
                                        color: #ecf0f1;
                                        height: 40px;
                                      "
                                    >
                                      <th
                                        class="encabezadoFijo"
                                        style="width: 40px"
                                      >
                                        <input
                                          type="checkbox"
                                          v-model="selectAllPlagaOT"
                                          @click="selectPlagaTodo()"
                                        />
                                      </th>
                                      <th class="encabezadoFijo">
                                        Plaga a Tratar
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr
                                      v-for="plagas in arrayPlagasOT"
                                      :key="plagas.COD_PLAGA"
                                    >
                                      <td>
                                        <label class="form-checkbox">
                                          <input
                                            type="checkbox"
                                            :value="plagas.COD_PLAGA"
                                            v-model="selectPlagaOT"
                                          />
                                          <i class="form-icon"></i>
                                        </label>
                                      </td>
                                      <td v-text="plagas.DESCRIPCION"></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <div
                              class="tab-pane fade"
                              id="lugares"
                              role="tabpanel"
                              aria-labelledby="lugares-tab"
                            >
                              <div style="overflow-x: auto; height: 290px">
                                <table
                                  class="table table-bordered table-striped"
                                >
                                  <thead>
                                    <tr
                                      style="
                                        background-color: #243648f0;
                                        color: #ecf0f1;
                                        height: 40px;
                                      "
                                    >
                                      <th
                                        class="encabezadoFijo"
                                        style="width: 40px"
                                      >
                                        <input
                                          type="checkbox"
                                          v-model="selectAllLugarOT"
                                          @click="selectLugarTodo()"
                                        />
                                      </th>
                                      <th class="encabezadoFijo">
                                        Lugares a Tratar
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr
                                      v-for="lugares in arrayLugaresOT"
                                      :key="lugares.COD_LUGARES"
                                    >
                                      <td>
                                        <label class="form-checkbox">
                                          <input
                                            type="checkbox"
                                            :value="lugares.COD_LUGARES"
                                            v-model="selectLugarOT"
                                          />
                                          <i class="form-icon"></i>
                                        </label>
                                      </td>
                                      <td v-text="lugares.DESCRIPCION"></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <div
                              class="tab-pane fade"
                              id="observaciones"
                              role="tabpanel"
                              aria-labelledby="observaciones-tab"
                              style="background-color: #f0f3f5"
                            >
                              <div class="row">
                                <div class="col-md-6">
                                  <label for="company"
                                    >Nro de Tecnicos<span style="color: red"
                                      >(*)</span
                                    ></label
                                  >
                                  <input
                                    type="number"
                                    class="form-control"
                                    v-model="nroTecnicosOT"
                                  />
                                  <span
                                    v-if="errorOT.nroTecnicos"
                                    class="error rojo"
                                    >{{ errorOT.nroTecnicos[0] }}</span
                                  >
                                </div>
                                <div class="col-md-6">
                                  <label for="company"
                                    >Frecuencia Dias<span style="color: red"
                                      >(*)</span
                                    ></label
                                  >
                                  <input
                                    type="number"
                                    class="form-control"
                                    v-model="frecuenciaOT"
                                  />
                                  <span
                                    v-if="errorOT.frecuencia"
                                    class="error rojo"
                                    >{{ errorOT.frecuencia[0] }}</span
                                  >
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-12">
                                  <label for="company"
                                    >Observaciones<span style="color: red"
                                      >(*)</span
                                    ></label
                                  >
                                  <textarea
                                    name="textarea"
                                    class="form-control"
                                    rows="3"
                                    cols="12"
                                    v-model="observacionesOT"
                                  ></textarea>
                                  <span
                                    v-if="errorOT.observaciones"
                                    class="error rojo"
                                    >{{ errorOT.observaciones[0] }}</span
                                  >
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
            <button
              type="button"
              @click="cerrarModalParametros()"
              class="btn btn-danger colorRojo"
            >
              <i class="fa fa-arrow-circle-left"></i> Cancelar
            </button>
            <button
              type="button"
              @click="registrarOT()"
              class="btn btn-success colorVerde"
            >
              <i class="fa fa-floppy-o"></i>
              Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--Fin del modal-->

    <!--modal De asignacion OT -->
    <div
      class="modal fade"
      :class="{ mostrar: modalParametros }"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-success" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">MENSAJE</h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="cerrarModalParametros()"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="height: 100px">
            <div v-if="accionModal == 0">
              <h4>La reserva ya tiene una OT asignada</h4>
            </div>
            <div class="row" v-else>
              <div class="col-md-6" align="right">
                <button
                  type="button"
                  class="btn btn-primary colorAzul"
                  @click="crearOT()"
                >
                  <i class="fa fa-plus"></i>
                  Crear OT
                </button>
              </div>
              <div class="col-md-6">
                <button
                  type="button"
                  class="btn btn-primary colorAzul"
                  @click="vincularOT()"
                >
                  <i class="fa fa-link"></i>
                  Vincular OT
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="cerrarModalParametros()"
              class="btn btn-danger colorRojo"
            >
              <i class="fa fa-arrow-circle-left"></i> Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--fin -->

    <!-- Inicio del modal Seleccionar Cliente -->
    <div
      class="modal fade"
      :class="{ mostrar: mostrarModalOT }"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-success modal-lg"
        role="document"
        style="max-width: 65%"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">LISTA DE ORDEN DE TRABAJO</h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="cerrarModalParametros()"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="height: 610px">
            <div class="form-group row">
              <div class="col-md-12">
                <div class="input-group">
                  <input
                    type="text"
                    v-model="buscarOT"
                    @keyup="buscarOTFltro(1, buscarOT)"
                    class="form-control"
                    placeholder="Buscar OT"
                  />
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-12">
                <div style="overflow-x: auto; height: 450px">
                  <!-- table-striped-->
                  <table
                    class="table table-bordered table-striped table-hover table-sm"
                  >
                    <thead>
                      <tr
                        style="
                          background-color: #243648f0;
                          color: #ecf0f1;
                          height: 40px;
                        "
                      >
                        <th class="encabezadoFijo" style="width: 40px"></th>
                        <th class="encabezadoFijo" style="width: 90px">
                          Permiso Modificar
                        </th>
                        <th class="encabezadoFijo">Nro</th>
                        <th class="encabezadoFijo">Cliente</th>
                        <th class="encabezadoFijo" style="width: 110px">
                          Fecha
                        </th>
                        <th class="encabezadoFijo">Contacto</th>
                        <th class="encabezadoFijo">Direccion</th>
                        <th class="encabezadoFijo">Telefono</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="ot in arrayOt" :key="ot.COD_ORDEN_TRABAJO">
                        <td>
                          <button
                            type="button"
                            class="btn btn-primary colorAzul"
                            @click="seleccionarOT(ot.COD_ORDEN_TRABAJO)"
                          >
                            <i class="fa fa-plus"></i>
                          </button>
                        </td>
                        <td
                          v-text="ot.ESTADO_PM"
                          title="Solicitud de Modificacion"
                        ></td>
                        <td v-text="ot.NRO_ORDEN"></td>
                        <td
                          v-text="
                            ot.AP_CLIENTE +
                            ' ' +
                            ot.AM_CLIENTE +
                            ' ' +
                            ot.NOMCLIENTE
                          "
                        ></td>
                        <td v-text="ot.FECHA_SERVICIO"></td>
                        <td v-text="ot.CONTACTO"></td>
                        <td v-text="ot.DIRECCION"></td>
                        <td v-text="ot.TELEFONO"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <nav>
                  <ul class="pagination">
                    <li class="page-item" v-if="paginationOT.current_page > 1">
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                          cambiarPaginaOT(
                            paginationOT.current_page - 1,
                            buscarOT
                          )
                        "
                        >Ant</a
                      >
                    </li>
                    <li
                      class="page-item"
                      v-for="page in pagesNumberOT"
                      :key="page"
                      :class="[page == isActivedOT ? 'active' : '']"
                    >
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="cambiarPaginaOT(page, buscarOT)"
                        v-text="page"
                      ></a>
                    </li>
                    <li
                      class="page-item"
                      v-if="paginationOT.current_page < paginationOT.last_page"
                    >
                      <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                          cambiarPaginaOT(
                            paginationOT.current_page + 1,
                            buscarOT
                          )
                        "
                        >Sig</a
                      >
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger colorRojo"
              data-dismiss="modal"
              @click="cerrarModalParametros()"
            >
              <i class="fa fa-arrow-circle-left"></i>
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin del modal  Seleccionar Cliente -->
  </main>
</template>

<script>
export default {
  data() {
    return {
      codPersonal: 0,
      mensajeError:
        "Error de conexion de con el servidor, comunicarse con Sistemas, Error ",

      ////////////////////////////////////// - FILTRO -///////////////////////////////////////////////////////
      fechaInicio: "",
      fechaFin: "",
      ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////
      isFactura: 0,
      arrayReserva: [],
      listado: 1,
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      offset: 3,
      buscar: "",
      nombre: "",
      nombreCliente: "",
      tiempoTransporte: "",
      equipoTecnico: "",
      fechaReserva: "",
      fechaemision: "",
      horaInicio: "",
      tiempoServicio: "",
      filaBodyReserva: "",
      errorReserva: [],
      selected: [],
      listaTecnico: [],
      listadoDetalleReserva: [],
      selectAll: false,

      fechaReservaReplicacion: "",
      horaInicioReplicacion: "",
      tiempoServicioReplicacion: "",
      filaBodyReservaReplicacion: "",
      validarFechaRep: 0,
      selectedReplicacion: [],
      listaTecnicoReplicacion: [],
      listadoDetalleReservaReplicacion: [],
      selectAllReplicacion: false,

      fechaReservaModificar: "",
      horaInicioModificar: "",
      fechaReservaModificarCambio: "",
      horaInicioModificarCambio: "",
      tiempoServicioModificar: "",
      tiempoTransporteModificar: "",
      listadoReservaModificar: [],
      selectedModificar: [],
      listaTecnicoModificar: [],
      filaBodyReservaModificar: "",
      selectAllModificar: false,
      codReserva: "",
      modal: 0,

      //reserva = guardar los datos de reserva
      horarioNocturnoR: 0,
      cambiarFechaR: 0,
      contadorNocturnoVR: 0,

      vectorTiempo: [
        { text: "30 Minutos  |  Media Hora", value: "30" },
        { text: "60 Minutos  |  1 Hora", value: "60" },
        { text: "90 Minutos  |  1 Hora y Media", value: "90" },
        { text: "120 Minutos |  2 Horas", value: "120" },
        { text: "150 Minutos |  2 Horas y Media", value: "150" },
        { text: "180 Minutos |  3 Horas", value: "180" },

        { text: "210 Minutos  |  3 Horas y Media", value: "210" },
        { text: "240 Minutos  |  4 Horas", value: "240" },
        { text: "270 Minutos  |  4 Horas y Media", value: "270" },
        { text: "300 Minutos  |  5 Horas", value: "300" },
        { text: "330 Minutos  |  5 Horas y Media", value: "330" },
        { text: "360 Minutos  |  6 Horas", value: "360" },
      ],
      vectorHorario: [
        { horario: "00:00", value: "00:00:00" },
        { horario: "00:30", value: "00:30:00" },
        { horario: "01:00", value: "01:00:00" },
        { horario: "01:30", value: "01:30:00" },
        { horario: "02:00", value: "02:00:00" },
        { horario: "02:30", value: "02:30:00" },
        { horario: "03:00", value: "03:00:00" },
        { horario: "03:30", value: "03:30:00" },
        { horario: "04:00", value: "04:00:00" },
        { horario: "04:30", value: "04:30:00" },
        { horario: "05:00", value: "05:00:00" },
        { horario: "05:30", value: "05:30:00" },
        { horario: "06:00", value: "06:00:00" },
        { horario: "06:30", value: "06:30:00" },
        { horario: "07:00", value: "07:00:00" },
        { horario: "07:30", value: "07:30:00" },
        { horario: "08:00", value: "08:00:00" },
        { horario: "08:30", value: "08:30:00" },
        { horario: "09:00", value: "09:00:00" },
        { horario: "09:30", value: "09:30:00" },
        { horario: "10:00", value: "10:00:00" },
        { horario: "10:30", value: "10:30:00" },
        { horario: "11:00", value: "11:00:00" },
        { horario: "11:30", value: "11:30:00" },
        { horario: "12:00", value: "12:00:00" },
        { horario: "12:30", value: "12:30:00" },
        { horario: "13:00", value: "13:00:00" },
        { horario: "13:30", value: "13:30:00" },
        { horario: "14:00", value: "14:00:00" },
        { horario: "14:30", value: "14:30:00" },
        { horario: "15:00", value: "15:00:00" },
        { horario: "15:30", value: "15:30:00" },
        { horario: "16:00", value: "16:00:00" },
        { horario: "16:30", value: "16:30:00" },
        { horario: "17:00", value: "17:00:00" },
        { horario: "17:30", value: "17:30:00" },
        { horario: "18:00", value: "18:00:00" },
        { horario: "18:30", value: "18:30:00" },
        { horario: "19:00", value: "19:00:00" },
        { horario: "19:30", value: "19:30:00" },
        { horario: "20:00", value: "20:00:00" },
        { horario: "20:30", value: "20:30:00" },
        { horario: "21:00", value: "21:00:00" },
        { horario: "21:30", value: "21:30:00" },
        { horario: "22:00", value: "22:00:00" },
        { horario: "22:30", value: "22:30:00" },
        { horario: "23:00", value: "23:00:00" },
        { horario: "23:30", value: "23:30:00" },
      ],
      estadoReserva: 0,
      datoReserva: [],

      ////////////////////////////////////// - CLIENTE -///////////////////////////////////////////////////////
      mostrarModalCliente: 0,
      cliente: "", // es el codigo
      nombre: "",
      nombreSeleccionado: "",
      modalCliente: 0,

      apellidoPaterno: "",
      apellidoMaterno: "",
      nit: "",
      razonSocial: "",
      correo: "",
      telefono: "",
      contacto: "",
      tipoCliente: "",
      categoria: "",
      frecuencia: "",
      ciudad: "",
      direccion: "",
      ejecutivo: "",
      clickCertificado: 0,
      nombrePrincipalCertificado: "",
      nombreSecundarioCertificado: "",
      DireccionCertificado: "",
      arrayTipoCliente: [],
      arrayEjecutivo: [],
      arrayCiudad: [],
      errorCliente: [],

      arrayCliente: [],
      paginationCliente: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },

      //////////////////////////////////-     OT    -///////////////////////////////////////
      //OT
      clickOT: 0, //para que permita solo un click al guardar la OT
      idOT: 0,
      modalOT: 0,
      codReservaOT: 0,
      clienteOT: "",
      codClienteOT: 0,
      fechaServicioOT: "",
      telefonoOT: "",
      contactoOT: "",
      horaServicoOT: "",
      precioOT: "",
      razonSocialOT: "",
      tipoPagoOT: "",
      plazo: "",
      nitOT: "",
      direccionOT: "",
      nroTecnicosOT: "",
      tiempoServicioOT: "",
      ejecutivoVentasOT: "",
      frecuenciaOT: "",
      observacionesOT: "",
      categoriaOT: "",
      especificacionOT: "",
      checked: true,
      arrayLugaresOT: [],
      arrayPlagasOT: [],
      selectAllLugarOT: false,
      selectAllPlagaOT: false,
      tituloModal: "",
      selectPlagaOT: [],
      selectLugarOT: [],
      errorOT: [],
      arrayOt: [],
      mostrarModalOT: 0,
      buscarOT: "",
      paginationOT: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },

      modalParametros: 0,
      accionModal: 0, //para los diferentes mensajes dentro del modal

      /////////////////////////-  EQUIPO TECNICO  -////////////////////////////////////
      arrayEquipo: [],
    };
  },
  computed: {
    isActived: function () {
      return this.pagination.current_page;
    },
    pagesNumber: function () {
      if (!this.pagination.to) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
    isActivedCliente: function () {
      return this.paginationCliente.current_page;
    },
    //Calcula los elementos de la paginación
    pagesNumberCliente: function () {
      if (!this.paginationCliente.to) {
        return [];
      }

      var from = this.paginationCliente.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.paginationCliente.last_page) {
        to = this.paginationCliente.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
    isActivedOT: function () {
      return this.paginationOT.current_page;
    },
    //Calcula los elementos de la paginación
    pagesNumberOT: function () {
      if (!this.paginationOT.to) {
        return [];
      }

      var from = this.paginationOT.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.paginationOT.last_page) {
        to = this.paginationOT.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
  },

  methods: {
    obtCodPersonal() {
      let me = this;
      axios
        .get("/reserva/obtCodPersonal")
        .then(function (reponse) {
          me.codPersonal = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////

    registrarReserva() {
      let me = this;
      // $.blockUI();
      axios
        .post("/reservas/validarAdmin", {
          cliente: this.cliente,
          fechaReserva: this.fechaReserva,
          // 'fechaemision': this.fechaemision,
          horaInicio: this.horaInicio,
          tiempoServicio: this.tiempoServicio,
          tiempoTransporte: this.tiempoTransporte,
          equipo: this.equipoTecnico,
        })
        .then(function (reponse) {
          me.errorReserva = [];
          if (me.selected.length > 0) {
            me.validarReserva(
              1,
              me.fechaReserva,
              me.horaInicio,
              me.tiempoTransporte,
              me.tiempoServicio,
              me.listadoDetalleReserva,
              me.selected,
              me.listaTecnico
            );
          } else {
            swal("Advertencia!", "Debe seleccionar Tecnicos.", "warning");
            // $.unblockUI();
          }
        })
        .catch(function (error) {
          // $.unblockUI();
          me.errorReserva = me.controlError(error);
        });
    },
    actualizarReserva() {
      let me = this;

      if (me.selectedModificar.length > 0) {
        me.validarReserva(
          3,
          me.fechaReservaModificar,
          me.horaInicioModificar,
          me.tiempoTransporte,
          me.tiempoServicioModificar,
          me.listadoReservaModificar,
          me.selectedModificar,
          me.listaTecnicoModificar
        );
      } else swal("Advertencia!", "Debe seleccionar Tecnicos.", "warning");
    },
    validarReserva(
      accion,
      fechaReserva,
      horaInicio,
      tiempoTransporte,
      tiempoServicio,
      listadoDetalleReserva,
      selected,
      listaTecnico
    ) {
      //La hora, tiempo de transporte y servicio, que se lecciono para hacer la reserva, se los separa los horario de forma individual
      //this.arrayHorario.length   =  48
      var horaInicioCliente = horaInicio.substring(0, 5);
      var tiempoTransporteCliente = tiempoTransporte / 30;

      var tiempoServicioCliente = tiempoServicio / 30;
      var sumaHorario = tiempoTransporteCliente + tiempoServicioCliente;
      var arrayHorarioClienteReservaIndividual = [];
      arrayHorarioClienteReservaIndividual.push({
        horario: horaInicioCliente,
        nocturno: 0,
      });
      var horarioNocturno = 0;
      var cambiarFecha = 0;
      var contadorNocturno = -1;
      var contadorNocturnoV = [];

      var varCon = 0;
      for (var t = 0; t < this.vectorHorario.length; t++) {
        if (this.vectorHorario[t].horario == horaInicioCliente) {
          for (var x = 0; x < sumaHorario - 1; x++) {
            //t=47 : es el horario 23:30
            if (varCon == 47) {
              contadorNocturno++;
              //console.log("ok1  - "+this.vectorHorario[varCon].horario);
              horarioNocturno = 1;
              contadorNocturnoV.push(1);
              arrayHorarioClienteReservaIndividual.push({
                horario: this.vectorHorario[contadorNocturno].horario,
                nocturno: 1,
              });
              cambiarFecha = 1;
              varCon = 0;
            } else {
              //varCon++;
              arrayHorarioClienteReservaIndividual.push({
                horario: this.vectorHorario[varCon].horario,
                nocturno: 0,
              });
              //console.log("ok2  - "+this.vectorHorario[varCon].horario );
              if (
                this.vectorHorario[varCon].horario == "00:00" ||
                contadorNocturnoV.length > 0
              ) {
                //&& contadorNocturno!=-1
                contadorNocturnoV.push(1);
                horarioNocturno = 1;
                //alert(horarioNocturno)+" "+this.vectorHorario[varCon].horario;
              }
              varCon++;
            }
          }
        }
        varCon++;
      }

      /*
      //var varCon=0;
      for (var t = 0; t < this.vectorHorario.length; t++) 
      {
        if (this.vectorHorario[t].horario == horaInicioCliente) 
        {
          for (var x = 0; x < sumaHorario-1; x++) 
          {
              //t=47 : es el horario 23:30
              if(t==47)
              {
                
                contadorNocturno++;
                console.log("ok1  - "+this.vectorHorario[t].horario);
                //horarioNocturno=1;
                contadorNocturnoV.push(1);
                arrayHorarioClienteReservaIndividual.push({horario: this.vectorHorario[contadorNocturno].horario,nocturno:1});
                cambiarFecha=1;
                //t=0;
              }
              else
              {
                t++;
                arrayHorarioClienteReservaIndividual.push({horario: this.vectorHorario[t].horario,nocturno:0});
                console.log("ok2  - "+this.vectorHorario[t].horario );
                if(this.vectorHorario[t].horario=='00:00' )//&& contadorNocturno!=-1
                {
                  horarioNocturno=1;
                  console.log("ok");
                }
                  
              }
          } 
        }
        //varCon++;
      }
    */
      //console.log(arrayHorarioClienteReservaIndividual);

      //listado de todas las reservas de la fecha seleccionada, y se los separa los horario de forma individual
      var arrayReservaHorarioIndividual = [];
      for (var i = 0; i < listadoDetalleReserva.length; i++) {
        var horaInicio = listadoDetalleReserva[i].horaInicio.substring(0, 5);
        var tiempoServicio = listadoDetalleReserva[i].tiempoServicio;
        var tiempoTransporte = listadoDetalleReserva[i].tiempoTransporte;
        var tiempoTransporte = tiempoTransporte / 30;
        tiempoServicio = tiempoServicio / 30;
        tiempoServicio = tiempoServicio + tiempoTransporte;
        var nom =
          listadoDetalleReserva[i].NOMBRE +
          " " +
          listadoDetalleReserva[i].AP_PATERNO +
          " " +
          listadoDetalleReserva[i].AP_MATERNO;
        arrayReservaHorarioIndividual.push({
          horario: horaInicio,
          idTecnico: listadoDetalleReserva[i].COD_PERSONAL,
          nombre: nom,
          nocturno: 0,
        });
        for (var v = 0; v < this.vectorHorario.length; v++) {
          if (this.vectorHorario[v].horario == horaInicio) {
            var contadorNocturno = -1;
            for (var x = 0; x < tiempoServicio - 1; x++) {
              if (v == 47) {
                contadorNocturno++;
                arrayReservaHorarioIndividual.push({
                  horario: this.vectorHorario[contadorNocturno].horario,
                  idTecnico: listadoDetalleReserva[i].COD_PERSONAL,
                  nombre: nom,
                  nocturno: 1,
                });
              } else {
                v++;
                arrayReservaHorarioIndividual.push({
                  horario: this.vectorHorario[v].horario,
                  idTecnico: listadoDetalleReserva[i].COD_PERSONAL,
                  nombre: nom,
                  nocturno: 0,
                });
              }
            }
          }
        }
      }
      //console.log(arrayReservaHorarioIndividual);

      var arrayTecnicoDatosSeleccionado = [];
      for (var t = 0; t < selected.length; t++) {
        for (var r = 0; r < listaTecnico.length; r++) {
          if (selected[t] == listaTecnico[r].COD_PERSONAL) {
            arrayTecnicoDatosSeleccionado.push({
              id: selected[t],
              nocturno: listaTecnico[r].activo,
            });
          }
        }
      }
      //console.log(arrayTecnicoDatosSeleccionado);

      //verificar
      var arrayValidacionesTecnicos = [];
      var arrayVerificarTecnicos = [];
      var nombreTecnicos = [];
      for (var r = 0; r < arrayTecnicoDatosSeleccionado.length; r++) {
        var okTecnico = "libre";
        for (var f = 0; f < arrayHorarioClienteReservaIndividual.length; f++) {
          if (okTecnico == "libre") {
            for (var g = 0; g < arrayReservaHorarioIndividual.length; g++) {
              if (
                arrayTecnicoDatosSeleccionado[r].id ==
                arrayReservaHorarioIndividual[g].idTecnico
              ) {
                if (
                  arrayHorarioClienteReservaIndividual[f].horario ==
                    arrayReservaHorarioIndividual[g].horario &&
                  arrayHorarioClienteReservaIndividual[f].nocturno ==
                    arrayReservaHorarioIndividual[g].nocturno
                ) {
                  //if (arrayHorarioClienteReservaIndividual[f].horario == arrayReservaHorarioIndividual[g].horario)
                  okTecnico = "reservado";
                  arrayVerificarTecnicos.push(
                    arrayTecnicoDatosSeleccionado[r].id
                  );
                  nombreTecnicos.push(arrayReservaHorarioIndividual[g].nombre);
                  arrayValidacionesTecnicos.push({
                    id: arrayTecnicoDatosSeleccionado[r].id,
                    nombre: arrayReservaHorarioIndividual[g].nombre,
                    mensaje: "El horario esta reservado",
                  });
                }
              }
            }
          }
        }
      }

      ///vectorHorario
      var noc = 0;
      for (var r = 0; r < listaTecnico.length; r++) {
        if (listaTecnico[r].activo == 1) {
          if (selected.includes(listaTecnico[r].COD_PERSONAL)) {
            var ok = 0;
            for (
              var q = 0;
              q < arrayHorarioClienteReservaIndividual.length;
              q++
            ) {
              // < 20: igual a 19, donde 19 es el horario de 9:30
              for (var h = 0; h < 20; h++) {
                //nocturno
                if (
                  arrayHorarioClienteReservaIndividual[q].horario ==
                    this.vectorHorario[h].horario &&
                  arrayHorarioClienteReservaIndividual[q].nocturno == 0
                ) {
                  ok = 1;
                  break;
                }
              }
            }

            if (ok == 1) {
              noc = 1;
              arrayVerificarTecnicos.push(listaTecnico[r].COD_PERSONAL);
              var nombre =
                listaTecnico[r].NOMBRE +
                " " +
                listaTecnico[r].AP_PATERNO +
                " " +
                listaTecnico[r].AP_MATERNO;
              nombreTecnicos.push(nombre);
              arrayValidacionesTecnicos.push({
                id: listaTecnico[r].COD_PERSONAL,
                nombre: nombre,
                mensaje:
                  "No se puede asignar, trabajo hasta o mas de las 00:00 am.",
              });
            }
          }
        }
      }

      //////////////////////////// verificar si el tecnico tiene trabajos en la mañana, para poder hacer reservas en la noche ejm 23:30 ///////////////////

      var arrayTecnicosReservas = [];
      var tecnicosNoDisponible = [];
      if (horarioNocturno == 1) {
        if (contadorNocturnoV.length >= 1) {
          var new_date = moment(fechaReserva, "YYYY-MM-DD");
          var fechaSiguiente = new_date.add(1, "days").format("YYYY-MM-DD");

          let me = this;
          var url = "/reserva/verificarTecnicoDiaSig?fecha=" + fechaSiguiente;
          axios
            .get(url)
            .then(function (reponse) {
              arrayTecnicosReservas = reponse.data;
              var nombreTecnicos = "";

              for (var t = 0; t < selected.length; t++) {
                for (var v = 0; v < arrayTecnicosReservas.length; v++) {
                  if (selected[t] == arrayTecnicosReservas[v].codTrabajador) {
                    nombreTecnicos +=
                      arrayTecnicosReservas[v].NOMBRE + "<br />";
                    arrayValidacionesTecnicos.push({
                      id: arrayTecnicosReservas[v].codTrabajador,
                      nombre: arrayTecnicosReservas[v].NOMBRE,
                      mensaje: "Tiene reserva antes de las 10:am",
                    });
                  }
                }
              }
              switch (accion) {
                case 1:
                  me.ConfirmacionGuardarReserva(
                    arrayValidacionesTecnicos,
                    horarioNocturno,
                    cambiarFecha,
                    contadorNocturno,
                    contadorNocturnoV
                  );
                  break;
                case 2:
                  me.ConfirmacionGuardarReplicacion(
                    arrayValidacionesTecnicos,
                    horarioNocturno,
                    cambiarFecha,
                    contadorNocturno,
                    contadorNocturnoV
                  );
                  break;
                case 3:
                  me.ConfirmacionGuardarModificar(
                    arrayValidacionesTecnicos,
                    horarioNocturno,
                    cambiarFecha,
                    contadorNocturno,
                    contadorNocturnoV
                  );
                  break;
              }
            })
            .catch(function (error) {
              me.controlError(error);
            });
        } else {
          switch (accion) {
            case 1:
              this.ConfirmacionGuardarReserva(
                arrayValidacionesTecnicos,
                horarioNocturno,
                cambiarFecha,
                contadorNocturno,
                contadorNocturnoV
              );
              break;
            case 2:
              this.ConfirmacionGuardarReplicacion(
                arrayValidacionesTecnicos,
                horarioNocturno,
                cambiarFecha,
                contadorNocturno,
                contadorNocturnoV
              );
              break;
            case 3:
              this.ConfirmacionGuardarModificar(
                arrayValidacionesTecnicos,
                horarioNocturno,
                cambiarFecha,
                contadorNocturno,
                contadorNocturnoV
              );
              break;
          }
        }
      } else {
        switch (accion) {
          case 1:
            this.ConfirmacionGuardarReserva(
              arrayValidacionesTecnicos,
              horarioNocturno,
              cambiarFecha,
              contadorNocturno,
              contadorNocturnoV
            );
            break;
          case 2:
            this.ConfirmacionGuardarReplicacion(
              arrayValidacionesTecnicos,
              horarioNocturno,
              cambiarFecha,
              contadorNocturno,
              contadorNocturnoV
            );
            break;
          case 3:
            this.ConfirmacionGuardarModificar(
              arrayValidacionesTecnicos,
              horarioNocturno,
              cambiarFecha,
              contadorNocturno,
              contadorNocturnoV
            );
            break;
        }
      }
    },
    /*
    validarReserva(accion,fechaReserva,horaInicio,tiempoTransporte,tiempoServicio,listadoDetalleReserva,selected,listaTecnico)
    {
      //La hora, tiempo de transporte y servicio, que se lecciono para hacer la reserva, se los separa los horario de forma individual
      //this.arrayHorario.length   =  48
        var horaInicioCliente = horaInicio.substring(0, 5);
        var tiempoTransporteCliente = tiempoTransporte / 30;

        var tiempoServicioCliente = tiempoServicio / 30;
        var sumaHorario = tiempoTransporteCliente + tiempoServicioCliente;
        var arrayHorarioClienteReservaIndividual=[];
        arrayHorarioClienteReservaIndividual.push({horario: horaInicioCliente,nocturno:0});
        var horarioNocturno=0;
        var cambiarFecha=0;
        var contadorNocturno=-1;
        var contadorNocturnoV=[];
       
        for (var t = 0; t < this.vectorHorario.length; t++) 
        {
          if (this.vectorHorario[t].horario == horaInicioCliente) 
          {
            for (var x = 0; x < sumaHorario-1; x++) 
            {
                //t=47 : es el horario 23:30
                if(t==47)
                {
                  contadorNocturno++;
                  horarioNocturno=1;
                  contadorNocturnoV.push(1);
                  arrayHorarioClienteReservaIndividual.push({horario: this.vectorHorario[contadorNocturno].horario,nocturno:1});
                  cambiarFecha=1;
                }
                else
                {
                  t++;
                  arrayHorarioClienteReservaIndividual.push({horario: this.vectorHorario[t].horario,nocturno:0});
                  if(this.vectorHorario[t].horario=="02:00" )//&& contadorNocturno!=-1
                    horarioNocturno=1;
                }
            } 
          }
        }
        //console.log(arrayHorarioClienteReservaIndividual);

        
     
      
      //listado de todas las reservas de la fecha seleccionada, y se los separa los horario de forma individual
      var arrayReservaHorarioIndividual=[];
      for (var i = 0;i < listadoDetalleReserva.length;i++) 
      {
        var horaInicio = listadoDetalleReserva[i].horaInicio.substring(0, 5);
        var tiempoServicio = listadoDetalleReserva[i].tiempoServicio;
        var tiempoTransporte = listadoDetalleReserva[i].tiempoTransporte;
        var tiempoTransporte = tiempoTransporte / 30;
        tiempoServicio = tiempoServicio / 30;
        tiempoServicio = tiempoServicio + tiempoTransporte;
        var nom = listadoDetalleReserva[i].NOMBRE+' '+listadoDetalleReserva[i].AP_PATERNO+' '+
        listadoDetalleReserva[i].AP_MATERNO;
        arrayReservaHorarioIndividual.push({ horario: horaInicio,idTecnico: listadoDetalleReserva[i].COD_PERSONAL,nombre:nom,nocturno:0});
        for (var v = 0; v < this.vectorHorario.length; v++) 
        {
          if (this.vectorHorario[v].horario == horaInicio) 
          {
            var contadorNocturno=-1;
            for (var x = 0; x < tiempoServicio-1; x++) 
            {

             if(v==47)
                {
                  contadorNocturno++;
                  arrayReservaHorarioIndividual.push({horario: this.vectorHorario[contadorNocturno].horario, idTecnico: listadoDetalleReserva[i] .COD_PERSONAL,nombre:nom,nocturno:1});
                }
                else
                {
                  v++;
                  arrayReservaHorarioIndividual.push({horario: this.vectorHorario[v].horario, idTecnico: listadoDetalleReserva[i] .COD_PERSONAL,nombre:nom,nocturno:0});
                }
            }
          }
        }
      }
      //console.log(arrayReservaHorarioIndividual);
      


      
      var arrayTecnicoDatosSeleccionado=[];
      for (var t = 0; t < selected.length; t++) 
      {
        for (var r = 0; r < listaTecnico.length; r++) 
           {
             if(selected[t]==listaTecnico[r].COD_PERSONAL)
             {
               arrayTecnicoDatosSeleccionado.push({id: selected[t],nocturno:listaTecnico[r].activo});
             }
           }
      }
      //console.log(arrayTecnicoDatosSeleccionado);
      

      //verificar
      var arrayValidacionesTecnicos=[];
      var arrayVerificarTecnicos=[];
      var nombreTecnicos=[];
      for (var r = 0; r < arrayTecnicoDatosSeleccionado.length; r++) 
      {
        var okTecnico = "libre";
        for (var f = 0;f < arrayHorarioClienteReservaIndividual.length;f++) 
        {
          if (okTecnico == "libre") 
          {
            for (var g = 0;g < arrayReservaHorarioIndividual.length;g++) 
            {
              if (arrayTecnicoDatosSeleccionado[r].id ==arrayReservaHorarioIndividual[g].idTecnico) 
              {
                if (arrayHorarioClienteReservaIndividual[f].horario == arrayReservaHorarioIndividual[g].horario && arrayHorarioClienteReservaIndividual[f].nocturno==arrayReservaHorarioIndividual[g].nocturno) 
                //if (arrayHorarioClienteReservaIndividual[f].horario == arrayReservaHorarioIndividual[g].horario) 
                {
                  okTecnico = "reservado";
                  arrayVerificarTecnicos.push(arrayTecnicoDatosSeleccionado[r].id);
                  nombreTecnicos.push(arrayReservaHorarioIndividual[g].nombre);
                  arrayValidacionesTecnicos.push({id:arrayTecnicoDatosSeleccionado[r].id,nombre:arrayReservaHorarioIndividual[g].nombre, mensaje:"El horario esta reservado"});

                }
              }
            }
          }
          
        }
        
      }

      

      ///vectorHorario
      var noc=0;
        for (var r = 0; r < listaTecnico.length; r++) 
        {
          if(listaTecnico[r].activo==1)
          {
            if(selected.includes(listaTecnico[r].COD_PERSONAL))
            {
              var ok=0;
              for(var q = 0; q < arrayHorarioClienteReservaIndividual.length; q++) 
              {
                for(var h = 0; h < 20; h++) {//nocturno
                      if(arrayHorarioClienteReservaIndividual[q].horario==this.vectorHorario[h].horario && arrayHorarioClienteReservaIndividual[q].nocturno==0){
                        ok=1;
                        break;
                      }
                  }
              }
              if(ok==1){
                noc=1;
                  arrayVerificarTecnicos.push(listaTecnico[r].COD_PERSONAL);
                  var nombre = listaTecnico[r].NOMBRE+' '+listaTecnico[r].AP_PATERNO+' '+listaTecnico[r].AP_MATERNO;
                  nombreTecnicos.push(nombre);
                  arrayValidacionesTecnicos.push({id:listaTecnico[r].COD_PERSONAL,nombre:nombre, mensaje:"No se puede asignar, trabajo hasta o mas de las 2 am."});

                }
            } 
          }
        }


  //////////////////////////// verificar si el tecnico tiene trabajos en la mañana, para poder hacer reservas en la noche ejm 23:30 ///////////////////

      var arrayTecnicosReservas=[];
      var tecnicosNoDisponible=[];
      if(horarioNocturno==1)
      {
        if(contadorNocturnoV.length >= 5)
        {
          var new_date = moment(fechaReserva, "YYYY-MM-DD");
          var fechaSiguiente = new_date.add(1, 'days').format('YYYY-MM-DD');
          
          let me = this;
            var url = "/reserva/verificarTecnicoDiaSig?fecha=" + fechaSiguiente;
            axios.get(url).then(function(reponse) {
                arrayTecnicosReservas = reponse.data;
                var nombreTecnicos='';
              for(var t = 0; t < selected.length; t++)
              {
                  for(var v = 0; v < arrayTecnicosReservas.length; v++){
                      if(selected[t]==arrayTecnicosReservas[v].codTrabajador)
                      {
                        nombreTecnicos+=arrayTecnicosReservas[v].NOMBRE+'<br />';
                        arrayValidacionesTecnicos.push({id:arrayTecnicosReservas[v].codTrabajador,nombre:arrayTecnicosReservas[v].NOMBRE, mensaje:"Tiene reserva antes de las 10:am"});  
                     }
                  }
              }
              switch(accion){
                case 1: me.ConfirmacionGuardarReserva(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 2: me.ConfirmacionGuardarReplicacion(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 3: me.ConfirmacionGuardarModificar(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
              }
              
              })
              .catch(function(error) {
                me.controlError(error);
              });
        }
        else
        {
          switch(accion){
                case 1: this.ConfirmacionGuardarReserva(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 2: this.ConfirmacionGuardarReplicacion(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 3: this.ConfirmacionGuardarModificar(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
              }
        }
      }
      else
      {
          switch(accion){
                case 1: this.ConfirmacionGuardarReserva(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 2: this.ConfirmacionGuardarReplicacion(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
                case 3: this.ConfirmacionGuardarModificar(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
              }
      }
    },
    */

    ConfirmacionGuardarReserva(
      arrayValidacionesTecnicos,
      horarioNocturno,
      cambiarFecha,
      contadorNocturno,
      contadorNocturnoV
    ) {
      var mensaje = "";
      if (arrayValidacionesTecnicos.length > 0) {
        for (var z = 0; z < arrayValidacionesTecnicos.length; z++) {
          mensaje +=
            arrayValidacionesTecnicos[z].nombre +
            " | " +
            arrayValidacionesTecnicos[z].mensaje +
            "<br />";
        }
        swal("Mensaje", "<b>" + mensaje + "</b>", "warning");
        // $.unblockUI();
      } else {
        if (this.fechaReservaReplicacion == "") {
          swal({
            title: "Desea guardar la reserva sin Replicacion?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "green",
            cancelButtonColor: "#d33",
            confirmButtonText: " Si",
            cancelButtonText: "No ",
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger",

            reverseButtons: true,
          }).then((result) => {
            if (result.value) {
              //this.guardarSinReplicacionVerificar(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);
              this.guardarSinReplicacion(
                horarioNocturno,
                cambiarFecha,
                contadorNocturno,
                contadorNocturnoV
              );
            } else {
              result.dismiss === swal.DismissReason.cancel;
              // $.unblockUI();
              this.validarFechaRep = 1;

              //almacena los datos de la reserva, para guardar reserva y reaplicacion
              this.horarioNocturnoR = horarioNocturno;
              this.cambiarFechaR = cambiarFecha;
              this.contadorNocturnoVR = contadorNocturnoV.length;
            }
          });
        } else {
          let me = this;
          me.validarFechaRep = 0;
          if (me.selectedReplicacion.length > 0) {
            //almacena los datos de la reserva, para guardar reserva y reaplicacion
            this.horarioNocturnoR = horarioNocturno;
            this.cambiarFechaR = cambiarFecha;
            this.contadorNocturnoVR = contadorNocturnoV.length;
            me.validarReserva(
              2,
              me.fechaReservaReplicacion,
              me.horaInicioReplicacion,
              me.tiempoTransporte,
              me.tiempoServicioReplicacion,
              me.listadoDetalleReservaReplicacion,
              me.selectedReplicacion,
              me.listaTecnicoReplicacion
            );
          } else {
            swal("Advertencia!", "Debe seleccionar Tecnicos.", "warning");
          }
        }
      }
    },
    ConfirmacionGuardarModificar(
      arrayValidacionesTecnicos,
      horarioNocturno,
      cambiarFecha,
      contadorNocturno,
      contadorNocturnoV
    ) {
      var mensaje = "";
      if (arrayValidacionesTecnicos.length > 0) {
        for (var z = 0; z < arrayValidacionesTecnicos.length; z++) {
          mensaje +=
            arrayValidacionesTecnicos[z].nombre +
            " | " +
            arrayValidacionesTecnicos[z].mensaje +
            "<br />";
        }
        swal("Mensaje", "<b>" + mensaje + "</b>", "warning");
      } else {
        this.modificarReserva();
      }
    },
    modificarReserva() {
      //para determinar si, cambio la fecha o hora
      var cambio = 0;
      if (
        this.fechaReservaModificar != this.fechaReservaModificarCambio ||
        this.horaInicioModificar != this.horaInicioModificarCambio
      ) {
        cambio = 1;
      }

      let me = this;

      axios
        .put("/reservas/modificarReserva", {
          fechaemision: this.fechaemisionModificar,
          fechaReserva: this.fechaReservaModificar,
          horaInicio: this.horaInicioModificar.substring(0, 5),
          tiempoServicio: this.tiempoServicioModificar,
          tiempoTransporte: this.tiempoTransporteModificar,
          idReserva: this.codReserva,
          trabajadores: me.selectedModificar,
          COD_ORDEN_TRABAJO: this.codOTReprogramacion,
          cambio: cambio,
        })
        .then(function (response) {

          //modificar directo
          //fecha Ot 
          // hora Inicio
          //Estado (reprogramado)
          if (me.codOTReprogramacion != null) {
            me.actualizarOT(
              me.fechaReservaModificar,
              me.horaInicioModificar.substring(0, 5),
              me.codOTReprogramacion
            );
          }
          swal("Reserva Actualizada!", "Modificado Correctamente.", "success");
          me.listarTecnico();
          me.listarReservas(1, "", "", me.buscar);
          me.cerrarModalModificar();
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    actualizarOT(fecha, hora, codigo) {
      
      var pagina =
        "http://isi-app.online/1Servicios/web/mip/ajax/ot.php?op=actualizarOT&fecha=" +
        fecha +
        "&hora=" +
        hora +
        "&codigo=" +
        codigo;
      var opciones =
        "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=10, height=10, top=0, left=0";
      window.open(pagina, "", opciones);
    },
    ConfirmacionGuardarReplicacion(
      arrayValidacionesTecnicos,
      horarioNocturno,
      cambiarFecha,
      contadorNocturno,
      contadorNocturnoV
    ) {
      var mensaje = "";
      if (arrayValidacionesTecnicos.length > 0) {
        for (var z = 0; z < arrayValidacionesTecnicos.length; z++) {
          mensaje +=
            arrayValidacionesTecnicos[z].nombre +
            " | " +
            arrayValidacionesTecnicos[z].mensaje +
            "<br />";
        }
        // $.unblockUI();
        swal("Mensaje", "<b>" + mensaje + "</b>", "warning");
      } else {
        this.guardarReservaYReplicacion(
          horarioNocturno,
          cambiarFecha,
          contadorNocturno,
          contadorNocturnoV
        );
      }
    },
    guardarReservaYReplicacion(
      horarioNocturnoReaplicacion,
      cambiarFecha,
      contadorNocturno,
      contadorNocturnoVReaplicacion
    ) {
      let me = this;
      var fechaVencimientoReserva = this.fechaReserva;
      if (me.horarioNocturnoR == 1) {
        if (me.cambiarFechaR == 1) {
          var fechaR = this.fechaReserva;
          var new_dateR = moment(fechaR, "YYYY-MM-DD");
          fechaVencimientoReserva = new_dateR
            .add(1, "days")
            .format("YYYY-MM-DD");
        }
      }
      var fechaVencimientoReaplicacion = this.fechaReservaReplicacion;
      if (horarioNocturnoReaplicacion == 1) {
        if (cambiarFecha == 1) {
          var fecha = this.fechaReservaReplicacion;
          var new_date = moment(fecha, "YYYY-MM-DD");
          fechaVencimientoReaplicacion = new_date
            .add(1, "days")
            .format("YYYY-MM-DD");
        }
      }
      /*
      console.log(this.cliente);
      console.log(this.tiempoTransporte);
      console.log(this.fechaReserva);
      console.log(this.horaInicio);
      console.log(this.tiempoServicio);
      console.log(fechaVencimientoReserva);
      console.log(me.horarioNocturnoR);
      console.log(me.contadorNocturnoVR);
      console.log(this.selected);
      console.log("---------------------");
      console.log(this.fechaReservaReplicacion);
      console.log(this.horaInicioReplicacion);
      console.log(this.tiempoServicioReplicacion);
      console.log(fechaVencimientoReaplicacion);
      console.log(horarioNocturnoReaplicacion);
      console.log(contadorNocturnoVReaplicacion.length);
      console.log(this.selectedReplicacion);
      */

      axios
        .post("/reservas/registrarReservaReplicacion", {
          cliente: this.cliente,
          tiempoTransporte: this.tiempoTransporte,
          estadoOperacion: 0,
          // 'fechaemision':this.fechaemision,
          fechaReserva: this.fechaReserva,
          horaInicio: this.horaInicio,
          tiempoServicio: this.tiempoServicio,
          fechaFinalizacionReserva: fechaVencimientoReserva,
          horarioNocturnoReserva: me.horarioNocturnoR,
          contadorNocturnoVReserva: me.contadorNocturnoVR,
          data: this.selected,

          fechaReservaReplicacion: this.fechaReservaReplicacion,
          horaInicioReplicacion: this.horaInicioReplicacion,
          tiempoServicioReplicacion: this.tiempoServicioReplicacion,
          fechaFinalizacionReaplicacion: fechaVencimientoReaplicacion,
          horarioNocturnoReaplicacion: horarioNocturnoReaplicacion,
          contadorNocturnoVReaplicacion: contadorNocturnoVReaplicacion.length,
          dataReplicacion: this.selectedReplicacion,
        })
        .then(function (reponse) {
          swal("Reserva Registrada!", "Guardado Correctamente.", "success");
          me.cambiarPanel(1);

          me.listarReservas(1, "", "", "");
          me.listarTecnico();
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });

      /*
      axios.post('/reservas/registrarReservaReplicacion',{
          'cliente':this.cliente,
          'tiempoTransporte':this.tiempoTransporte,
          'fechaReserva':this.fechaReserva,
          'horaInicio':this.horaInicio,
          'tiempoServicio':this.tiempoServicio,
          "data":this.selected,

          'fechaReservaReplicacion':this.fechaReservaReplicacion,
          'horaInicioReplicacion':this.horaInicioReplicacion,
          'tiempoServicioReplicacion':this.tiempoServicioReplicacion,
          "dataReplicacion":this.selectedReplicacion
      }).then(function (reponse){              
          swal("Reserva Registrada!", "Guardado Correctamente.", "success");
          me.cambiarPanel(1);

          me.listarReservas(1,'','','');     
          me.listarTecnico();           
      })
      .catch(function(error){
          me.controlError(error);
      });
      */
    },
    obtHora() {
      this.horaInicioReplicacion = this.horaInicio;
    },
    obtTiempoServicio() {
      this.tiempoServicioReplicacion = this.tiempoServicio;
    },
    guardarSinReplicacion(
      horarioNocturno,
      cambiarFecha,
      contadorNocturno,
      contadorNocturnoV
    ) {
      var fechaVencimiento = this.fechaReserva;

      if (horarioNocturno == 1) {
        if (cambiarFecha == 1) {
          var fecha = this.fechaReserva;
          var new_date = moment(fecha, "YYYY-MM-DD");
          fechaVencimiento = new_date.add(1, "days").format("YYYY-MM-DD");
        }
      }

      /*
      console.log(this.cliente);
      console.log(this.tiempoTransporte);
      console.log(this.fechaReserva);
      console.log(this.horaInicio);
      console.log(this.tiempoServicio);
      console.log(fechaVencimiento);
      console.log(horarioNocturno);
      console.log(contadorNocturnoV.length);
      console.log(this.selected);
      */

      let me = this;
      axios
        .post("/reservas/registrar", {
          cliente: this.cliente,
          tiempoTransporte: this.tiempoTransporte,
          // 'fechaemision':this.fechaemision, EL CAMPO NO ESTA EN LA TABLA RESERVA
          fechaReserva: this.fechaReserva,
          horaInicio: this.horaInicio,
          tiempoServicio: this.tiempoServicio,
          fechaFinalizacion: fechaVencimiento,
          horarioNocturno: horarioNocturno,
          contadorNocturno: contadorNocturnoV.length,
          data: this.selected,
          estadoOperacion: 0,
        })
        .then(function (reponse) {
          // //$.unblockUI();
          swal("Reserva Registrada!", "Guardado Correctamente.", "success");
          me.cambiarPanel(1);
          me.listarReservas(1, "", "", "");
          me.listarTecnico();
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },

    listarReservas(page, fechaInicio, fechaFin, buscar) {
      if (fechaInicio != "") {
        if (fechaFin == "") fechaFin = fechaInicio;
      }

      if (fechaFin != "") {
        if (fechaInicio == "") fechaInicio = fechaFin;
      }

      let me = this;
      // $.blockUI();
      var url =
        "Mip/reservas/listarReserva?page=" +
        page +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin +
        "&buscar=" +
        buscar;
      axios
        .get(url)
        .then(function (reponse) {
          // $.unblockUI();
          var resp = reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
    listarReservasSinCarga(page, fechaInicio, fechaFin, buscar) {
      if (fechaInicio != "") {
        if (fechaFin == "") fechaFin = fechaInicio;
      }

      if (fechaFin != "") {
        if (fechaInicio == "") fechaInicio = fechaFin;
      }

      let me = this;
      var url =
        "Mip/reservas/listarReserva?page=" +
        page +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin +
        "&buscar=" +
        buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listarReservasReset(page, buscar) {
      let me = this;
      me.buscar = "";
      var url =
        "Mip/reservas/listarReserva?page=" +
        page +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin +
        "&buscar=" +
        buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    cambiarPagina(page, fechaInicio, fechaFin, buscar) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarReservas(page, fechaInicio, fechaFin, buscar);
    },
    cambiarPanel(panel) {
      this.limpiarDatos();
      this.listado = panel;
    },
    limpiarDatos() {
      let me = this;
      me.selected = [];
      me.cliente = "";
      me.nombreSeleccionado = "";
      me.tiempoTransporte = "";
      me.fechaReserva = "";
      me.fechaemision = "";
      me.horaInicio = "";
      me.tiempoServicio = "";
      me.nombre = "";
      me.selectedReplicacion = [];
      me.fechaReservaReplicacion = "";
      me.horaInicioReplicacion = "";
      me.tiempoServicioReplicacion = "";
      me.buscar = "";
      me.errorReserva = [];

      me.horarioNocturnoR = 0;
      me.cambiarFechaR = 0;
      me.contadorNocturnoVR = 0;

      me.equipoTecnico = "";
      me.validarFechaRep = 0;
      //click en fila de una tabla, cambiar color
      me.index = -1;
    },

    //Inicio tecnicos de la fecha actual
    listarTecnico() {
      let me = this;
      var url =
        "/tecnicos/listadoTecnicosFechaActualAdminAmbos?fecha=" +
        me.fechaReserva;
      axios
        .get(url)
        .then(function (reponse) {
          me.listaTecnico = reponse.data;
          me.listaTecnicoReplicacion = reponse.data;
          me.listarDetalleReservasFechaActual(me.listaTecnico, me.fechaReserva);
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    //obtiene las reservas de los tecnicos de la fecha actual
    listarDetalleReservasFechaActual(listaTecnico, fechaReserva) {
      let me = this;
      var url =
        "/DetalleReserva/listaDetalleReservasFechaActual?fecha=" + fechaReserva;
      axios
        .get(url)
        .then(function (reponse) {
          //me.listadoDetalleReserva = reponse.data;

          me.listadoDetalleReserva = me.obtNuevosDatos(
            reponse.data,
            listaTecnico
          );
          me.listadoDetalleReservaReplicacion = reponse.data;
          me.filaBodyReserva = me.bodyFila1(
            listaTecnico,
            me.listadoDetalleReserva
          );
          me.filaBodyReservaReplicacion = me.filaBodyReserva;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    //Fin tecnicos de la fecha actual

    //Inicio Filtros de Reservas
    filtrarFechaTecnicoReserva() {
      let me = this;
      // $.blockUI();
      var url =
        "/tecnicos/listadoTecnicosFechaActualAdminAmbos?fecha=" +
        me.fechaReserva;
      axios
        .get(url)
        .then(function (reponse) {
          me.listaTecnico = reponse.data;
          me.listarDetalleReservasFechaSeleccionadaReserva(
            me.listaTecnico,
            me.fechaReserva
          );
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReserva(listaTecnico, fechaReserva) {
      let me = this;
      var url =
        "/DetalleReserva/listaDetalleReservasFechaActual?fecha=" + fechaReserva;
      axios
        .get(url)
        .then(function (reponse) {
          me.listadoDetalleReserva = me.obtNuevosDatos(
            reponse.data,
            listaTecnico
          );
          me.filaBodyReserva = me.bodyFila1(
            listaTecnico,
            me.listadoDetalleReserva
          );
          // $.unblockUI();
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
    //Fin Filtros de Reservas

    //Inicio Filtros de Replicacion 1
    filtrarFechaTecnicoReplicacion_1() {
      let me = this;
      // $.blockUI();
      var url =
        "/tecnicos/listadoTecnicosFechaActualAdminAmbos?fecha=" +
        me.fechaReservaReplicacion;
      axios
        .get(url)
        .then(function (reponse) {
          me.listaTecnicoReplicacion = reponse.data;
          me.listarDetalleReservasFechaSeleccionadaReplicacion_1(
            me.listaTecnicoReplicacion,
            me.fechaReservaReplicacion
          );
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReplicacion_1(
      listaTecnico,
      fechaReserva
    ) {
      let me = this;
      var url =
        "/DetalleReserva/listaDetalleReservasFechaActual?fecha=" + fechaReserva;
      axios
        .get(url)
        .then(function (reponse) {
          me.listadoDetalleReservaReplicacion = me.obtNuevosDatos(
            reponse.data,
            listaTecnico
          );
          me.filaBodyReservaReplicacion = me.bodyFila1(
            listaTecnico,
            me.listadoDetalleReservaReplicacion
          );
          //  $.unblockUI();
        })
        .catch(function (error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
    //Fin Filtros de Replicacion 1

    modificar(reserva) {
      let me = this;

      //me.selectedModificar=[];
      this.codReserva = reserva.codReserva;

      me.listarTrabajadoresModificar(reserva.codReserva, reserva.fecha);
      //me.filtrarFechaTecnicoModificar(reserva.codReserva,reserva.fecha);
      //me.listarDetalleReservaModificar(reserva.codReserva,reserva.fecha);
      this.nombreCliente =
        "ACTUALIZAR RESERVA | " +
        reserva.NOMBRE +
        " " +
        reserva.AP_CLIENTE +
        " " +
        reserva.AM_CLIENTE;
      this.fechaReservaModificar = reserva.fecha;
      this.horaInicioModificar = reserva.horainicio;

      (this.fechaReservaModificarCambio = reserva.fecha),
        (this.horaInicioModificarCambio = reserva.horainicio);

      this.tiempoServicioModificar = reserva.tiempoServicio;
      this.tiempoTransporteModificar = reserva.tiempoTransporte;

      this.codOTReprogramacion = reserva.codOT;
      this.modal = 1;
      
    },
    //Trae los trabajadores de la reserva ha modificar
    listarTrabajadoresModificar(idReserva, fecha) {
      let me = this;

      me.selectedModificar = [];
      axios
        .get("/listarTrabajadoresModificar?page=1&idReserva=" + idReserva)
        .then(function (reponse) {
          var objeto = reponse.data;
          for (var v = 0; v < objeto.length; v++) {
            me.selectedModificar.push(objeto[v].codTrabajador);
          }
          me.filtrarFechaTecnicoModificar(idReserva, fecha);
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    selectModificar() {
      this.selectedModificar = [];
      if (!this.selectAllModificar) {
        for (let i in this.listaTecnico) {
          this.selectedModificar.push(this.listaTecnico[i].COD_PERSONAL);
        }
      }
    },
    filtrarFechaTecnicoModificar(idReserva, fecha) {
      let me = this;
      var url = "/tecnicos/listadoTecnicosFechaActualAdmin?fecha=" + fecha;
      axios
        .get(url)
        .then(function (reponse) {
          me.listaTecnicoModificar = reponse.data;
          me.listarDetalleReservaModificar(
            me.listaTecnicoModificar,
            idReserva,
            fecha
          );
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listarDetalleReservaModificar(listaTecnico, idReserva, fecha) {
      let me = this;
      axios
        .get(
          "/DetalleReservaModificar?page=1&idReserva=" +
            idReserva +
            "&fecha=" +
            fecha
        )
        .then(function (reponse) {
          //me.listadoReservaModificar = reponse.data;
          me.listadoReservaModificar = me.obtNuevosDatos(
            reponse.data,
            listaTecnico
          );
          me.filaBodyReservaModificar = me.bodyFila1(
            listaTecnico,
            me.listadoReservaModificar
          );
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    cerrarModalModificar() {
      let me = this;
      this.modal = 0;
    },
    obtPosicionVector(horaInicio) {
      var posicion;
      for (var i = 0; i < this.vectorHorario.length; i++) {
        if (this.vectorHorario[i].value == horaInicio) {
          posicion = i;
        }
      }
      return posicion;
    },
    obtNuevosDatos(listadoDetalleReserva, vectorTecnico) {
      var vector = [];
      for (var z = 0; z < vectorTecnico.length; z++) {
        var con = 0;
        if (vectorTecnico[z].activo == 1) {
          for (var f = 0; f < vectorTecnico[z].incremento; f++) {
            var fecha = "FECHA: ";
            var titulo =
              fecha +
              vectorTecnico[z].fechaFinalizacion +
              "\n" +
              //"OT: "+'\n'+
              "CLIENTE: \n";
            //'DIRECCION: ';
            var obj = new Object();
            obj.AP_MATERNO = vectorTecnico[z].AP_MATERNO;
            obj.AP_PATERNO = vectorTecnico[z].AP_PATERNO;
            obj.COD_PERSONAL = vectorTecnico[z].COD_PERSONAL;
            obj.NOMBRE = vectorTecnico[z].NOMBRE;
            obj.codReserva = vectorTecnico[z].codReserva;
            obj.fecha = vectorTecnico[z].fechaFinalizacion;
            var hora = this.vectorHorario[f].horario;
            obj.horaInicio = this.vectorHorario[f].value;
            obj.tipo =
              '<td class="centroReserva" title="' +
              titulo +
              '"><b>' +
              hora +
              "</b><br>" +
              "RESERVADO</td>";

            vector.push(obj);
          }
        }
      }

      for (var i = 0; i < listadoDetalleReserva.length; i++) {
        var posicion = this.obtPosicionVector(
          listadoDetalleReserva[i].horaInicio
        );
        //posicion++;
        var tiempoServicio = listadoDetalleReserva[i].tiempoServicio;
        tiempoServicio = tiempoServicio / 30;
        var tiempoTransporte = listadoDetalleReserva[i].tiempoTransporte;
        tiempoTransporte = tiempoTransporte / 30;

        for (var v = 0; v < tiempoServicio + 1; v++) {
          if (posicion <= 47) {
            if (listadoDetalleReserva[i].NRO_ORDEN == null) {
              var titulo =
                "FECHA: " +
                listadoDetalleReserva[i].fecha +
                "\n" +
                "OT: SIN OT" +
                "\n" +
                "CLIENTE: " +
                listadoDetalleReserva[i].C_NOMBRE +
                " " +
                listadoDetalleReserva[i].C_AP_CLIENTE +
                " " +
                listadoDetalleReserva[i].C_AM_CLIENTE +
                "\n" +
                "DIRECCION: " +
                listadoDetalleReserva[i].DIRECCION;
            } else {
              var titulo =
                "FECHA: " +
                listadoDetalleReserva[i].fecha +
                "\n" +
                "OT: " +
                listadoDetalleReserva[i].NRO_ORDEN +
                "\n" +
                "CLIENTE: " +
                listadoDetalleReserva[i].C_NOMBRE +
                " " +
                listadoDetalleReserva[i].C_AP_CLIENTE +
                " " +
                listadoDetalleReserva[i].C_AM_CLIENTE +
                "\n" +
                "DIRECCION: " +
                listadoDetalleReserva[i].DIRECCION;
            }

            var obj = new Object();
            obj.AP_MATERNO = listadoDetalleReserva[i].AP_MATERNO;
            obj.AP_PATERNO = listadoDetalleReserva[i].AP_PATERNO;
            obj.COD_PERSONAL = listadoDetalleReserva[i].COD_PERSONAL;
            obj.NOMBRE = listadoDetalleReserva[i].NOMBRE;
            obj.codReserva = listadoDetalleReserva[i].codReserva;
            obj.fecha = listadoDetalleReserva[i].fecha;
            var hora = this.vectorHorario[posicion].horario;
            obj.horaInicio = this.vectorHorario[posicion++].value;

            obj.tipo =
              '<td class="centroReserva" title="' +
              titulo +
              '"><b>' +
              hora +
              "</b><br>" +
              "RESERVADO</td>";

            vector.push(obj);
          }
        }
        //console.log(tiempoTransporte);
        for (var t = 0; t < tiempoTransporte - 1; t++) {
          if (posicion <= 47) {
            if (listadoDetalleReserva[i].NRO_ORDEN == null) {
              var titulo =
                "FECHA: " +
                listadoDetalleReserva[i].fecha +
                "\n" +
                "OT: SIN OT" +
                "\n" +
                "CLIENTE: " +
                listadoDetalleReserva[i].C_NOMBRE +
                " " +
                listadoDetalleReserva[i].C_AP_CLIENTE +
                " " +
                listadoDetalleReserva[i].C_AM_CLIENTE +
                "\n" +
                "DIRECCION: " +
                listadoDetalleReserva[i].DIRECCION;
            } else {
              var titulo =
                "FECHA: " +
                listadoDetalleReserva[i].fecha +
                "\n" +
                "OT: " +
                listadoDetalleReserva[i].NRO_ORDEN +
                "\n" +
                "CLIENTE: " +
                listadoDetalleReserva[i].C_NOMBRE +
                " " +
                listadoDetalleReserva[i].C_AP_CLIENTE +
                " " +
                listadoDetalleReserva[i].C_AM_CLIENTE +
                "\n" +
                "DIRECCION: " +
                listadoDetalleReserva[i].DIRECCION;
            }

            var obj = new Object();
            obj.AP_MATERNO = listadoDetalleReserva[i].AP_MATERNO;
            obj.AP_PATERNO = listadoDetalleReserva[i].AP_PATERNO;
            obj.COD_PERSONAL = listadoDetalleReserva[i].COD_PERSONAL;
            obj.NOMBRE = listadoDetalleReserva[i].NOMBRE;
            obj.codReserva = listadoDetalleReserva[i].codReserva;
            obj.fecha = listadoDetalleReserva[i].fecha;
            var hora = this.vectorHorario[posicion].horario;
            obj.horaInicio = this.vectorHorario[posicion++].value;
            obj.tipo =
              '<td class="centroTransporte" title="TRANPORTE"><b>' +
              hora +
              "</b><br>" +
              "TRANSPORTE</td>";
            vector.push(obj);
          }
        }
      }

      return vector;
    },
    mo10() {
      alert(1);
    },
    bodyFila1(listaTecnico, listadoDetalleReserva) {
      let fila = "";
      var ok = 0;
      for (var v = 0; v < this.vectorHorario.length; v++) {
        fila += '<tr  style="height: 60px;">';
        //fila +='<td><button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;width:60px;" type="button" >'+this.vectorHorario[v].horario+'</button></td>';
        for (var i = 0; i < listaTecnico.length; i++) {
          var ok = 0;
          var tipo = "";
          for (var z = 0; z < listadoDetalleReserva.length; z++) {
            var horaInicio = listadoDetalleReserva[z].horaInicio.substring(
              0,
              5
            );
            var nombreTec =
              listaTecnico[i].NOMBRE +
              " " +
              listaTecnico[i].AP_PATERNO +
              " " +
              listaTecnico[i].AP_MATERNO;
            var nombreTecDetReserva =
              listadoDetalleReserva[z].NOMBRE +
              " " +
              listadoDetalleReserva[z].AP_PATERNO +
              " " +
              listadoDetalleReserva[z].AP_MATERNO;
            if (
              this.vectorHorario[v].horario == horaInicio &&
              nombreTec == nombreTecDetReserva
            ) {
              ok = 1;
              tipo = listadoDetalleReserva[z].tipo;
              //fila +=listadoDetalleReserva[z].tipo;
            }
          }
          if (ok == 0)
            fila +=
              '<td class="centroHorario" title="SIN RESERVA"><b>' +
              this.vectorHorario[v].horario +
              "</b></td>";
          else fila += tipo;
        }
        fila += "</tr>";
      }

      return fila;
    },
    bodyFila(listaTecnico, listadoDetalleReserva) {
      let fila = "";
      for (var i = 0; i < listaTecnico.length; i++) {
        fila += '<tr style="height: 60px;">';
        fila +=
          '<td style="display: none"><button class="btn btn-square btn-success btn-block" type="button">' +
          listaTecnico[i].NOMBRE +
          " " +
          listaTecnico[i].AP_PATERNO +
          " " +
          listaTecnico[i].AP_MATERNO +
          "</button></td>";
        var ok = 0;
        for (var v = 0; v < this.vectorHorario.length; v++) {
          let temporal = "";
          var opc = 0;

          if (listaTecnico[i].activo == 1) {
            if (ok == 0 && listaTecnico[i].incremento != 0) {
              for (var inc = 0; inc < listaTecnico[i].incremento; inc++) {
                //</br>0001
                temporal +=
                  '<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
                v++;
              }
              v--;
              ok = 1;
            }
          }

          /*
          if(listaTecnico[i].activo==1)
          {
            if(ok==0)
            {
              for(var w = 0; w < listaTecnico[i].codReserva; w++)
              {
                if(w==listaTecnico[i].codReserva-1)
                {
                  temporal +='<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado5</a></td>';
                }
                
                else
                {
                  temporal +='<td class="centro"><a class="badge badge-danger  colorRojo" href="#">Reservado6</a></td>';
                  v++;
                }
                
              }
              ok=1;
            }
          }
          */

          for (var z = 0; z < listadoDetalleReserva.length; z++) {
            var horaInicio = listadoDetalleReserva[z].horaInicio.substring(
              0,
              5
            );
            var tiempoServicio = listadoDetalleReserva[z].tiempoServicio;
            var tiempoTransporte = listadoDetalleReserva[z].tiempoTransporte;
            var tiempoTransporte = tiempoTransporte / 30;
            tiempoServicio = tiempoServicio / 30;

            //tiempoServicio = tiempoServicio + tiempoTransporte;

            var nombreTec =
              listaTecnico[i].NOMBRE +
              " " +
              listaTecnico[i].AP_PATERNO +
              " " +
              listaTecnico[i].AP_MATERNO;
            var nombreTecDetReserva =
              listadoDetalleReserva[z].NOMBRE +
              " " +
              listadoDetalleReserva[z].AP_PATERNO +
              " " +
              listadoDetalleReserva[z].AP_MATERNO;

            if (
              this.vectorHorario[v].horario == horaInicio &&
              nombreTec == nombreTecDetReserva
            ) {
              temporal +=
                '<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
              for (var x = 0; x < tiempoServicio; x++) {
                if (v != 47) {
                  v++;
                  temporal +=
                    '<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
                }
              }
              for (var t = 0; t < tiempoTransporte - 1; t++) {
                if (v != 47) {
                  v++;
                  temporal +=
                    '<td class="centro"><a class="badge badge-warning colorAmarrillo" href="#">Transporte</a></td>';
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
    cambiarEstado(data, Activar, Activado, estado) {
      swal({
        title: "Esta seguro de " + Activar + " la Reserva?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "green",
        cancelButtonColor: "#d33",
        confirmButtonText: "Aceptar!",
        cancelButtonText: "Cancelar",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",

        reverseButtons: true,
      }).then((result) => {
        if (result.value) {
          let me = this;
          axios
            .put("/reserva/Desactivar", {
              id: data.codReserva,
              estado: estado,
              COD_ORDEN_TRABAJO: data.codOT,
            })
            .then(function (response) {
              me.listarReservas(1, "", "", me.buscar);

              swal(
                Activado + "!",
                "La reserva ha sido " + Activado + " con éxito.",
                "success"
              );
            })
            .catch(function (error) {
              me.controlError(error);
            });
        } else if (result.dismiss === swal.DismissReason.cancel) {
        }
      });
    },
    imprimirExcel(fecha) {
      location.href =
        "http://isi-app.online/1Servicios/web/mipLaravel/ajax/reserva.php?op=excel&fecha=" +
        fecha;
    },

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////// - CLIENTE -///////////////////////////////////////////////////////
    buscarCliente() {
      this.mostrarModalCliente = 1;
    },
    cerrarModalCliente() {
      this.mostrarModalCliente = 0;
      this.modalCliente = 0;
      this.errorCliente = [];
      this.buscar = "";
    },
    listadoCliente(page, buscar) {
      let me = this;
      var url = "/Mip/Cliente/listadoActivo?page=" + page + "&buscar=" + buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data;
          me.arrayCliente = resp.cliente.data;
          me.paginationCliente = resp.pagination;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    cambiarPaginaCliente(page, buscar) {
      let me = this;
      //Actualiza la página actual
      me.paginationCliente.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listadoCliente(page, buscar);
    },
    seleccionarCliente(cliente) {
      let me = this;
      me.cliente = cliente.COD_CLIENTE;
      me.nombreSeleccionado =
        cliente.NOMBRE + " " + cliente.AP_CLIENTE + " " + cliente.AM_CLIENTE;
      me.cerrarModalCliente();
    },
    panelCliente() {
      this.modalCliente = 1;
      this.listaTipoCliente();
      this.listaEjecutivo();
      this.listaCiudad();
    },
    listaTipoCliente() {
      let me = this;
      axios
        .get("/Mip/Cliente/listaTipoCliente")
        .then(function (reponse) {
          me.arrayTipoCliente = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listaEjecutivo() {
      let me = this;
      axios
        .get("/Mip/Cliente/ejecutivo")
        .then(function (reponse) {
          me.arrayEjecutivo = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listaCiudad() {
      let me = this;
      axios
        .get("/Mip/Cliente/ciudad")
        .then(function (reponse) {
          me.arrayCiudad = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    certificado() {
      if (this.clickCertificado == 0) this.clickCertificado = 1;
      else this.clickCertificado = 0;
    },
    limpiar() {
      this.nombre = "";
      this.apellidoPaterno = "";
      this.apellidoMaterno = "";
      this.nit = "";
      this.razonSocial = "";
      this.correo = "";
      this.telefono = "";
      this.contacto = "";
      this.tipoCliente = "";
      this.categoria = "";
      this.frecuencia = "";
      this.ciudad = "";
      this.direccion = "";
      this.ejecutivo = "";
      this.errorCliente = [];
    },
    registrarCliente() {
      let me = this;
      axios
        .post("/cliente/registrar", {
          nombre: this.nombre,
          apellidoPaterno: this.apellidoPaterno,
          apellidoMaterno: this.apellidoMaterno,
          nit: this.nit,
          razonSocial: this.razonSocial,
          correo: this.correo,
          telefono: this.telefono,
          contacto: this.contacto,
          tipoCliente: this.tipoCliente,
          categoria: this.categoria,
          frecuencia: this.frecuencia,
          ciudad: this.ciudad,
          direccion: this.direccion,
          ejecutivo: this.ejecutivo,
          clickCertificado: this.clickCertificado,
          nombrePrincipalCertificado: this.nombrePrincipalCertificado,
          nombreSecundarioCertificado: this.nombreSecundarioCertificado,
          DireccionCertificado: this.DireccionCertificado,
        })
        .then(function (reponse) {
          me.limpiar();
          me.cerrarModalCliente();
          swal("Cliente Registrado!", "Guardado Correctamente.", "success");
          me.listadoCliente(1, "");
        })
        .catch(function (error) {
          me.errorCliente = me.controlError(error);
        });
    },

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - OT -///////////////////////////////////////////////////////
    cambiarPaginaOT(page, buscar) {
      let me = this;
      //Actualiza la página actual
      me.paginationOT.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.buscarOTFltro(page, buscar);
    },
    seleccionarOT(codOT) {
      let me = this;
      swal({
        title: "Desea guardar vincular esta OT a la Reserva?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "green",
        cancelButtonColor: "#d33",
        confirmButtonText: " Si",
        cancelButtonText: "No ",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",

        reverseButtons: true,
      }).then((result) => {
        if (result.value) {
          me.cerrarModalParametros();
          axios
            .put("/reservas/vincularOT", {
              codReserva: me.datoReserva.codReserva,
              COD_ORDEN_TRABAJO: codOT,
            })
            .then(function (response) {
              me.listarReservas(1, "", "", me.buscar);
              me.buscarOT = "";
              me.buscarOTFltro(1, "");
              swal(
                "Reserva Actualizada!",
                "Vinculado Correctamente",
                "success"
              );
            })
            .catch(function (error) {
              me.controlError(error);
            });
        } else {
          result.dismiss === swal.DismissReason.cancel;
        }
      });
    },
    buscarOTFltro(page, buscar) {
      let me = this;
      var url =
        "/reserva/listarOTSinAsignacion?page=" + page + "&buscar=" + buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data;
          me.arrayOt = resp.reserva.data;
          me.paginationOT = resp.pagination;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    limpiarOt() {
      this.modalParametros = 0;
      this.modalOT = 0;
      this.clickOT = 0;
      this.clienteOT = "";
      this.codReservaOT = "";
      this.codClienteOT = "";
      this.telefonoOT = "";
      this.fechaServicioOT = "";
      this.horaServicoOT = "";
      this.razonSocialOT = "";
      this.nitOT = "";
      this.tipoPagoOT = "";
      this.precioOT = "";
      this.observacionesOT = "";
      this.nroTecnicosOT = "";
      this.tiempoServicioOT = "";
      this.frecuenciaOT = "";
      this.selectPlagaOT = [];
      this.selectLugarOT = [];
      this.categoriaOT = "";
      this.especificacionOT = "";
      this.errorOT = [];
      this.isFactura = 0;
    },
    ModalOT(estado, datosReserva = []) {
      if (datosReserva.codOT == null) {
        this.accionModal = 1;
        this.modalParametros = 1;

        this.estadoReserva = estado;
        this.datoReserva = datosReserva;
      } else {
        this.accionModal = 0;
        this.modalParametros = 1;
      }
      /*
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
        this.accionModal=0;
        this.modalParametros=1;
      }
      */
    },

    // Ernesto CreaOT
    crearOT() {
      this.modalParametros = 0;
      this.limpiarOt();

      if (this.datoReserva.codOT == null) {
        if (this.estadoReserva == 1) {
          this.tituloModal =
            "ORDEN DE OT | " +
            this.datoReserva.NOMBRE +
            " " +
            this.datoReserva.AP_CLIENTE +
            " " +
            this.datoReserva.AM_CLIENTE;
          this.clienteOT =
            this.datoReserva.NOMBRE +
            " " +
            this.datoReserva.AP_CLIENTE +
            " " +
            this.datoReserva.AM_CLIENTE;
          this.fechaServicioOT = this.datoReserva.fecha;
          this.horaServicoOT = this.datoReserva.horainicio;
          this.tiempoServicioOT = this.datoReserva.tiempoServicio;
          this.telefonoOT = this.datoReserva.TELEFONO;
          this.contactoOT = this.datoReserva.CONTACTO;
          this.razonSocialOT = this.datoReserva.RAZON_SOCIAL;
          this.nitOT = this.datoReserva.CI_NIT;
          this.direccionOT = this.datoReserva.DIRECCION;
          this.codClienteOT = this.datoReserva.codCliente;
          this.codReservaOT = this.datoReserva.codReserva;
        }

        this.modalOT = this.estadoReserva;
      } else {
        this.accionModal = 0;
        this.modalParametros = 1;
      }
    },
    vincularOT() {
      this.mostrarModalOT = 1;
    },
    listadoPlagas() {
      let me = this;
      axios
        .get("/Mip/plagas/listar")
        .then(function (reponse) {
          me.arrayPlagasOT = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listadoLugares() {
      let me = this;
      axios
        .get("/Mip/lugares/listar")
        .then(function (reponse) {
          me.arrayLugaresOT = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    selectPlagaTodo() {
      this.selectPlagaOT = [];
      if (!this.selectAllPlagaOT) {
        for (let i in this.arrayPlagasOT) {
          this.selectPlagaOT.push(this.arrayPlagasOT[i].COD_PLAGA);
        }
      }
    },
    selectLugarTodo() {
      this.selectLugarOT = [];
      if (!this.selectAllLugarOT) {
        for (let i in this.arrayLugaresOT) {
          this.selectLugarOT.push(this.arrayLugaresOT[i].COD_LUGARES);
        }
      }
    },

    // Ernesto guardaOt

    registrarOT() {
      var plaga = this.selectPlagaOT.length;
      var lugar = this.selectLugarOT.length;

      if (plaga > 0 && lugar > 0) {
        this.validarOT();
      } else
        swal(
          "Advertencia!",
          "Debe seleccionar al menos un lugar a tratar y una Plaga a tratar.",
          "warning"
        );
    },
    validarOT() {
      let me = this;
      if (this.isFactura === 0) {
        this.fechaemision = null;
      }

      if (me.clickOT == 0) {
        me.clickOT = 1;
        axios
          .post("/Reserva/registrarOT", {
            cliente: this.clienteOT,
            codReserva: this.codReservaOT,
            codCliente: this.codClienteOT,
            telefono: this.telefonoOT,
            fechaServicio: this.fechaServicioOT,
            FECHA_EMISION_FC: this.fechaemision,
            horaServico: this.horaServicoOT.substring(0, 5),
            razonSocial: this.razonSocialOT,
            nit: this.nitOT,
            tipoPago: this.tipoPagoOT,
            plazo: this.plazo,
            precio: this.precioOT,
            observaciones: this.observacionesOT,
            nroTecnicos: this.nroTecnicosOT,
            tiempoServicio: this.tiempoServicioOT,
            ejecutivoVentas: 24,
            equipo: 0,
            reponsable: "",
            factura: this.isFactura,
            categoria: this.categoriaOT,
            especificacion: this.especificacionOT,
            frecuencia: this.frecuenciaOT,
            dataPlaga: this.selectPlagaOT,
            dataLugar: this.selectLugarOT,
          })
          .then(function (reponse) {
            // Capturar el mensaje de éxito y otros datos de la respuesta del servidor
            let dato = reponse.data;
            let mensaje = reponse.data.mensaje;
            let otrosDatos = reponse.data.COD_ORDEN_TRABAJO;

            // Mostrar mensaje de éxito y otros datos usando SweetAlert o cualquier otra forma de visualización que prefieras
            if (reponse.status === 201) {
              swal("Mip", mensaje, "success");
            }

            console.log("OCOD_ORDEN_TRABAJO:", otrosDatos);

            me.limpiarOt();
            me.listarReservas(1, "", "", me.buscar);

            var pagina =
              "http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo=" +
              otrosDatos;
            var opciones =
              "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
            window.open(pagina, "", opciones);
          })
          .catch((error) => {
            if (
              error.response &&
              error.response.data &&
              error.response.data.errors
            ) {
              const errores = error.response.data.errors;
              let mensajeError = "";
              // Iterar sobre los errores y construir el mensaje de error
              Object.values(errores).forEach((mensajes) => {
                mensajes.forEach((mensaje) => {
                  mensajeError += `${mensaje}\n`;
                });
              });
              // Mostrar el mensaje de error usando SweetAlert
              swal("Error de validación", mensajeError, "error");
              me.clickOT = 0;
            } else {
              // Si hay otro tipo de error, simplemente mostrarlo
              swal("Error", error.message, "error");
              me.clickOT = 0;
            }
          });
      }
    },
    cerrarModalParametros() {
      this.modalParametros = 0;
      this.modalOT = 0;
      this.mostrarModalOT = 0;
    },

    asignarIdOT(codigo, id) {
      this.idOT = codigo;

      this.cambiarColorFila(id);
    },
    cambiarColorFila(id) {
      $("#" + id + " tr").click(function (e) {
        $("#" + id + " tr").removeClass("selected");
        $(this).addClass("selected");
      });
    },
    imprimirPDF() {
      if (this.idOT > 0) {
        var pagina =
          "http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo=" +
          this.idOT;
        var opciones =
          "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
        window.open(pagina, "", opciones);
        this.idOT = 0;
      } else {
        if (this.idOT == null || this.idOT == "null")
          swal("Mensaje", "La Reserva no tiene una OT Vinculada.", "warning");
        else swal("Mensaje", "Debe seleccionar una OT.", "warning");
      }
    },

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - EQUIPO TECNICO -//////////////////////////////////////////////////////////////
    listarEquipos() {
      let me = this;
      var url = "/Mip/equipos/listar";
      axios
        .get(url)
        .then(function (reponse) {
          me.arrayEquipo = reponse.data;
        })
        .catch(function (error) {
          me.controlError(error);
        });
    },
    listarTecnicosEquipo() {
      let me = this;
      // $.blockUI();

      me.selected = [];
      me.selectedReplicacion = [];
      var url = "/equipos/listarTecnicoEquipo?codEquipo=" + me.equipoTecnico;
      axios
        .get(url)
        .then(function (reponse) {
          // $.unblockUI();
          var objeto = reponse.data;
          for (var v = 0; v < objeto.length; v++) {
            me.selected.push(objeto[v].COD_PERSONAL);
            me.selectedReplicacion.push(objeto[v].COD_PERSONAL);
          }
        })
        .catch(function (error) {
          // // $.unblockUI();
          me.controlError(error);
        });
    },

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////// TECNICO ////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - CONTROL DE EXEPCIONES -///////////////////////////////////////////////////////
    controlError(error) {
      var array = [];
      var cadena = String(error);
      if (error.response.status == 422) {
        array = error.response.data.errors;
      } else {
        if (error.response.status == 419) {
          this.cerrarSesion();
        } else {
          var cadena = String(error);
          cadena = cadena.substr(cadena.length - 3, 3);
          swal("Mensaje ", this.mensajeError + " " + cadena, "error");
        }
      }
      return array;
    },
    cerrarSesion() {
      swal({
        title: "La sesion ha Expirado, Debe iniciar sesion nuevamente?",
        type: "warning",
        confirmButtonColor: "green",
        confirmButtonText: "Aceptar",
        confirmButtonClass: "btn btn-success",
      }).then((result) => {
        if (result.value) {
          location.href = "http://mip.isi-app.online/";
        }
      });
    },
  },
  mounted() {
    //this.obtCodPersonal();
    this.listarReservas(1, this.fechaInicio, this.fechaFin, this.buscar);
    this.listarTecnico();
    this.listarEquipos();
    this.listadoPlagas();
    this.listadoLugares();

    /* this.listadoPlagas();
     this.listadoLugares();
     this.listarDetalleReservasCompleto();
     this.listarDetalleReservasVisual();
     this.listarTecnico('');*/
  },
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

.rojo {
  color: crimson;
}

.colorRojo {
  background-color: rgb(197, 63, 90);
  color: white;
}

.colorAmarrillo {
  background-color: yellow;
  color: black;
}

.colorVerde {
  background-color: green;
}

.colorAzul {
  background-color: rgb(49, 83, 184);
}

.colorVerde1 {
  background-color: lightseagreen;
}

.ancho1 {
  width: 1000px;
}

.table-hover tbody tr:hover td,
.table-hover tbody tr:hover th {
  background-color: #dbdbdb;
}

.bloqueado {
  background-color: #20c997;
}

.colorBorde {
  border-color: #ce0808;
}

.colorBloqueado {
  background-color: #ffebeb;
}

.encabezado {
  text-align: center;
  font-weight: bold;
}
</style>
