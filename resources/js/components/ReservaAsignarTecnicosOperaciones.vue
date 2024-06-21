<template>
  <main class="main">
    <ol class>

    </ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header" v-if="listado">
   
          <button type="button" @click="imprimirPDF()" class="btn btn-success colorVerde">
            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
          </button>
        </div>
        <!--Inicio Panel Listado Reserva -->
        <template v-if="listado">
          <div class="card-body">
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">LISTA DE RESERVAS SIN TECNICOS</h1>
              </div>
            </div>

             <div class="form-group row">
              <div class="col-md-6">
                  <div class="input-group">
                    <input type="text" v-model="buscar" @keyup="listarReservas(1,fechaInicio,fechaFin,buscar)" class="form-control" placeholder="Buscar">
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="input-group">            
                      <input type="date" v-model="fechaInicio" class="form-control" placeholder="Fecha Inicio">
                      <input type="date" v-model="fechaFin" class="form-control" placeholder="Fecha Fin">
                      <button type="submit" class="btn btn-primary" @click="listarReservas(1,fechaInicio,fechaFin,buscar)"><i class="fa fa-search"></i> Buscar</button>
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
            
            <div style="overflow-x:auto; height:600px" >
              <!--class="table table-bordered table-striped table-sm table-hover" -->
              <table id="tablaPrincipalReserva1" class="tabla" style="width:100%">
                <thead>
                  <!--#29363d -->
                  <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                    <th class="encabezadoFijo" style="width:40px;"></th>
                    <th class="encabezadoFijo" style="width:40px;"></th>
                    <th class="encabezadoFijo" style="width:70px;">Nro OT</th>
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
        
                    <td v-text="reserva.NRO_ORDEN"></td>
                    <td v-text="reserva.fecha"></td>
                    <td v-if="reserva.NOMB_PERSONAL!=null" v-text="reserva.NOMB_PERSONAL+' '+reserva.APP_PERSONAL"></td>
                    <td v-else></td>
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
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,fechaInicio,fechaFin,buscar)">Ant</a>
                  </li>
                  <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,fechaInicio,fechaFin,buscar)" v-text="page"></a>
                  </li>
                  <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                      <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,fechaInicio,fechaFin,buscar)">Sig</a>
                  </li>
              </ul>
            </nav>
          </div>
        </template>
        <!--Fin Panel Listado Reserva -->
      </div>
    </div>




    <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" :class="{'mostrar':modal}" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg " style="max-width: 80%">>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="nombreCliente"></h4>
                            <button type="button" class="close" @click="cerrarModalModificar()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:600px;">
                          <div class="row" >
                              <div class="col-md-12">
                              <ul class="nav nav-tabs" role="tablist" >
                                  <li class="nav-item">
                                  <a class="nav-link active show" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Reservas</a>
                                  </li>  
                              </ul>
                              <div class="tab-content" style="background-color:#f0f3f5">
                                  <div class="tab-pane active show" id="home" role="tabpanel">
                                        <div class="row">
                                          <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <label for="company">
                                                  Fecha <span style="color:red">(*)</span>
                                                </label>
                                                <div class="input-group">
                                                  <input disabled class="form-control" type="date" v-model="fechaReservaModificar" @change="listarTrabajadoresModificar(codReserva,fechaReservaModificar)"  placeholder="fecha" style="height:35px;">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                              <div class="form-group">
                                                <label for="company">
                                                    Hora <span style="color:red">(*)</span>
                                                </label>
                                                <select disabled class="form-control" v-model="horaInicioModificar">
                                                    <option v-for="horario in vectorHorario" :key="horario.value" v-bind:value="horario.value" v-text="horario.horario">
                                                    </option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <label for="company">
                                                    Tiempo de Servicio
                                                    <span style="color:red">(*)</span>
                                                </label>
                                                <select disabled class="form-control" id="timSe" name="timSe" v-model="tiempoServicioModificar">
                                                    <option v-for="tiempo in vectorTiempo" :key="tiempo.value" v-bind:value="tiempo.value" v-text="tiempo.text">
                                                    </option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <label for="company">
                                                    Tiempo de Transporte
                                                    <span style="color:red">(*)</span>
                                                </label>
                                                <select disabled class="form-control" v-model="tiempoTransporteModificar">
                                                    <option v-for="tiempo in vectorTiempo" :key="tiempo.value" v-bind:value="tiempo.value" v-text="tiempo.text">
                                                    </option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <label for="company">
                                                    Equipo Tecnico
                                                    <span style="color:red">(*)</span>
                                                </label>
                                                 
                                                
                                                  <select class="form-control" v-model="equipoTecnico" @change="listarTecnicosEquipoModificar()">
                                                    <option v-for="equipo in arrayEquipo" :key="equipo.COD_EQUIPO" v-bind:value="equipo.COD_EQUIPO" v-text="equipo.DESCRIPCION">
                                                    </option>
                                                  </select>
                                                
                                                </div>
                                            </div>
                                             <div class="col-md-1"></div>
                                        </div>
                                       
                                       <div class="row" style="height:400px;overflow-x:auto;">
                                          <div class="col-md-12" style="padding: 0px;width:100px;">
                                              <table class="tabla" border="1" style="width:100%">
                                              <thead class="color">
                                                  <tr>
                                                    <th class="encabezadoFijo centro" v-for="tecnico in listaTecnico" :key="tecnico.COD_PERSONAL" >
                                                    <input type="checkbox" :value="tecnico.COD_PERSONAL" v-model="selectedModificar"><br> {{tecnico.NOMBRE +' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO}}
                                                    </th>
                                                  </tr>
                                              </thead>
                                              <tbody v-html="filaBodyReservaModificar"></tbody>
                                              </table>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              </div>
                          </div>         
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModalModificar()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button"  class="btn btn-success colorVerde"  @click="actualizarReserva()">
                              <i class="icon-pencil"></i> Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
  </main>
</template>

<script>

export default {
  data() {
      
    return {
      
      codPersonal:0,
      mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",

      ////////////////////////////////////// - FILTRO -///////////////////////////////////////////////////////
      fechaInicio:'',
      fechaFin:'',
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
      nombreCliente:'',
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

      fechaReservaModificar:"",
      horaInicioModificar:"",
      fechaReservaModificarCambio:"",
      horaInicioModificarCambio:"",
      tiempoServicioModificar:"",
      tiempoTransporteModificar:"",
      listadoReservaModificar:[],
      selectedModificar:[],
      listaTecnicoModificar: [],
      filaBodyReservaModificar:'',
      selectAllModificar:false,
      codReserva:"",
      modal: 0,

      //reserva = guardar los datos de reserva
      horarioNocturnoR:0,
      cambiarFechaR:0,
      contadorNocturnoVR:0,
  
      vectorTiempo: [
        { text: '30 Minutos  |  Media Hora', value: '30' },
        { text: '60 Minutos  |  1 Hora', value: '60' },
        { text: '90 Minutos  |  1 Hora y Media', value: '90' },
        { text: '120 Minutos |  2 Horas', value: '120' },
        { text: '150 Minutos |  2 Horas y Media', value: '150' },
        { text: '180 Minutos |  3 Horas', value: '180' },

        { text: '210 Minutos  |  3 Horas y Media', value: '210' },
        { text: '240 Minutos  |  4 Horas', value: '240' },
        { text: '270 Minutos  |  4 Horas y Media', value: '270' },
        { text: '300 Minutos  |  5 Horas', value: '300' },
        { text: '330 Minutos  |  5 Horas y Media', value: '330' },
        { text: '360 Minutos  |  6 Horas', value: '360' },

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
      estadoReserva:0,
      datoReserva:[],


      /////////////////////////-  EQUIPO TECNICO  -////////////////////////////////////
      arrayEquipo:[],
      arrayEquipoGenerado:"",
     
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
        },
  
  methods: {

    obtCodPersonal() {
        
        let me = this;
        axios .get("/reserva/obtCodPersonal")
        .then(function(reponse) {
          me.codPersonal = reponse.data;
        })
        .catch(function(error) {
        me.controlError(error);
        });
    },
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////

    actualizarReserva(){
      
      let me = this;

      if(me.selectedModificar.length>0)
      {
          me.validarReserva(3,me.fechaReservaModificar,me.horaInicioModificar,me.tiempoTransporte,me.tiempoServicioModificar,me.listadoReservaModificar,me.selectedModificar,me.listaTecnicoModificar);
      }
      else
        swal("Advertencia!", "Debe seleccionar Tecnicos.", "warning");
    },
    //para operaciones: registra las reservas, sin tecnicos asociados
    validarReservasSinTecnicos(accion,horaInicio,tiempoTransporte,tiempoServicio){
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

        
        switch(accion)
        {
          case 1: this.ConfirmacionGuardarReservaSinTecnicos(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
          case 2: this.guardarReservaYReplicacionSinTecnicos(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
          case 3: this.ConfirmacionGuardarModificarSinTecnicos(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);break;
        }
    
    },
    validarReserva(accion,fechaReserva,horaInicio,tiempoTransporte,tiempoServicio,listadoDetalleReserva,selected,listaTecnico){
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
    ConfirmacionGuardarReserva(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV){
      
      var mensaje='';
      if(arrayValidacionesTecnicos.length > 0) 
      {
        for(var z=0;z<arrayValidacionesTecnicos.length;z++){
          mensaje+=arrayValidacionesTecnicos[z].nombre+' | '+arrayValidacionesTecnicos[z].mensaje+'<br />';
        }
          swal(
            "Mensaje",
            '<b>'+mensaje+'</b>',
            "warning"
          );
      } 
      else
      {
       if(this.fechaReservaReplicacion=="")
        {
          swal({
            title: 'Desea guardar la reserva sin Replicacion?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: ' Si',
            cancelButtonText: 'No ',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
                        
            reverseButtons: true
            })
            .then((result) => 
            {
              if (result.value) 
              {
                //this.guardarSinReplicacionVerificar(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);
                this.guardarSinReplicacion(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV)
              } 
              else
              {
                (result.dismiss === swal.DismissReason.cancel) 
                this.validarFechaRep=1;

                //almacena los datos de la reserva, para guardar reserva y reaplicacion
                this.horarioNocturnoR=horarioNocturno;
                this.cambiarFechaR=cambiarFecha;
                this.contadorNocturnoVR=contadorNocturnoV.length;
              }
            }) 
        }
        else
        {
          let me=this;
          me.validarFechaRep=0;
          if (me.selectedReplicacion.length > 0)
          {
            //almacena los datos de la reserva, para guardar reserva y reaplicacion
                this.horarioNocturnoR=horarioNocturno;
                this.cambiarFechaR=cambiarFecha;
                this.contadorNocturnoVR=contadorNocturnoV.length;
            me.validarReserva(2,me.fechaReservaReplicacion,me.horaInicioReplicacion,me.tiempoTransporte,me.tiempoServicioReplicacion,me.listadoDetalleReservaReplicacion,me.selectedReplicacion,me.listaTecnicoReplicacion);
          } 
          else
          {
            swal("Advertencia!", "Debe seleccionar Tecnicos.", "warning");
          }
        }
      }
      
    },
    ConfirmacionGuardarModificar(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV){
        var mensaje='';
      if(arrayValidacionesTecnicos.length > 0) 
      {
        for(var z=0;z<arrayValidacionesTecnicos.length;z++){
          mensaje+=arrayValidacionesTecnicos[z].nombre+' | '+arrayValidacionesTecnicos[z].mensaje+'<br />';
        }
          swal(
            "Mensaje",
            '<b>'+mensaje+'</b>',
            "warning"
          );
      } 
      else
      {
        this.modificarReserva();
      }
    },
    modificarReserva(){
      
      //para determinar si, cambio la fecha o hora
      var cambio=0;
      if(this.fechaReservaModificar!=this.fechaReservaModificarCambio || this.horaInicioModificar!=this.horaInicioModificarCambio)
      {
          cambio=1;
      }

        let me = this;
        axios.put('/reservas/modificarReserva',{
                        'fechaReserva':this.fechaReservaModificar,
                        'horaInicio':this.horaInicioModificar.substring(0, 5),
                        'tiempoServicio':this.tiempoServicioModificar,
                        "tiempoTransporte":this.tiempoTransporteModificar,
                        "idReserva":this.codReserva,
                        "trabajadores":me.selectedModificar,
                        "COD_ORDEN_TRABAJO":this.codOTReprogramacion,
                        "cambio":cambio
                    }).then(function (response) {
                        swal("Reserva Actualizada!", "Modificado Correctamente.", "success");
                        me.listarTecnico();
                        me.equipoTecnico='';
                        me.listarReservas(1,'','',me.buscar);
                        me.cerrarModalModificar();

                    }).catch(function (error) {
                        me.controlError(error);
                    });
                  
    },
    ConfirmacionGuardarReplicacion(arrayValidacionesTecnicos,horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV){
      var mensaje='';
      if(arrayValidacionesTecnicos.length > 0) 
      {
        for(var z=0;z<arrayValidacionesTecnicos.length;z++){
          mensaje+=arrayValidacionesTecnicos[z].nombre+' | '+arrayValidacionesTecnicos[z].mensaje+'<br />';
        }
          swal(
            "Mensaje",
            '<b>'+mensaje+'</b>',
            "warning"
          );
      } 
      else
      {
        this.guardarReservaYReplicacion(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV);
      }
    },
    guardarReservaYReplicacion(horarioNocturnoReaplicacion,cambiarFecha,contadorNocturno,contadorNocturnoVReaplicacion){
        let me = this;
        var fechaVencimientoReserva=this.fechaReserva;
        if(me.horarioNocturnoR==1)
        { 
          if(me.cambiarFechaR==1)
          {
            var fechaR = this.fechaReserva;
            var new_dateR = moment(fechaR, "YYYY-MM-DD");
            fechaVencimientoReserva = new_dateR.add(1, 'days').format('YYYY-MM-DD');
          }
        }
        var fechaVencimientoReaplicacion=this.fechaReservaReplicacion;
        if(horarioNocturnoReaplicacion==1)
        { 
          if(cambiarFecha==1)
          {
            var fecha = this.fechaReservaReplicacion;
            var new_date = moment(fecha, "YYYY-MM-DD");
            fechaVencimientoReaplicacion = new_date.add(1, 'days').format('YYYY-MM-DD');
          }
        }
 
            axios.post('/reservas/registrarReservaReplicacion',{
                    'cliente':this.cliente,
                    'tiempoTransporte':this.tiempoTransporte,
                    "estadoOperacion":0,
                    'fechaReserva':this.fechaReserva,
                    'horaInicio':this.horaInicio,
                    'tiempoServicio':this.tiempoServicio,
                    'fechaFinalizacionReserva':fechaVencimientoReserva,
                    'horarioNocturnoReserva':me.horarioNocturnoR,
                    'contadorNocturnoVReserva':me.contadorNocturnoVR,
                    "data":this.selected,

                    'fechaReservaReplicacion':this.fechaReservaReplicacion,
                    'horaInicioReplicacion':this.horaInicioReplicacion,
                    'tiempoServicioReplicacion':this.tiempoServicioReplicacion,
                    'fechaFinalizacionReaplicacion':fechaVencimientoReaplicacion,
                    'horarioNocturnoReaplicacion':horarioNocturnoReaplicacion,
                    'contadorNocturnoVReaplicacion':contadorNocturnoVReaplicacion.length,
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
    },
    guardarReservaYReplicacionSinTecnicos(horarioNocturnoReaplicacion,cambiarFecha,contadorNocturno,contadorNocturnoVReaplicacion){
        let me = this;
        var fechaVencimientoReserva=this.fechaReserva;
        if(me.horarioNocturnoR==1)
        { 
          if(me.cambiarFechaR==1)
          {
            var fechaR = this.fechaReserva;
            var new_dateR = moment(fechaR, "YYYY-MM-DD");
            fechaVencimientoReserva = new_dateR.add(1, 'days').format('YYYY-MM-DD');
          }
        }
        var fechaVencimientoReaplicacion=this.fechaReservaReplicacion;
        if(horarioNocturnoReaplicacion==1)
        { 
          if(cambiarFecha==1)
          {
            var fecha = this.fechaReservaReplicacion;
            var new_date = moment(fecha, "YYYY-MM-DD");
            fechaVencimientoReaplicacion = new_date.add(1, 'days').format('YYYY-MM-DD');
          }
        }
 
            axios.post('/reservas/registrarReservaReplicacion',{
                    'cliente':this.cliente,
                    'tiempoTransporte':this.tiempoTransporte,
                    "estadoOperacion":1,
                    'fechaReserva':this.fechaReserva,
                    'horaInicio':this.horaInicio,
                    'tiempoServicio':this.tiempoServicio,
                    'fechaFinalizacionReserva':fechaVencimientoReserva,
                    'horarioNocturnoReserva':me.horarioNocturnoR,
                    'contadorNocturnoVReserva':me.contadorNocturnoVR,
                    "data":this.selected,

                    'fechaReservaReplicacion':this.fechaReservaReplicacion,
                    'horaInicioReplicacion':this.horaInicioReplicacion,
                    'tiempoServicioReplicacion':this.tiempoServicioReplicacion,
                    'fechaFinalizacionReaplicacion':fechaVencimientoReaplicacion,
                    'horarioNocturnoReaplicacion':horarioNocturnoReaplicacion,
                    'contadorNocturnoVReaplicacion':contadorNocturnoVReaplicacion.length,
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
    },
    obtHora(){
      this.horaInicioReplicacion = this.horaInicio;
    },
    obtTiempoServicio() {
      this.tiempoServicioReplicacion = this.tiempoServicio;
    },
    guardarSinReplicacionSinTecnicos(horarioNocturno,cambiarFecha,contadorNocturnoV)
    {
      var fechaVencimiento=this.fechaReserva;
        if(horarioNocturno==1)
        { 
          if(cambiarFecha==1)
          {
            var fecha = this.fechaReserva;
            var new_date = moment(fecha, "YYYY-MM-DD");
            fechaVencimiento = new_date.add(1, 'days').format('YYYY-MM-DD');
          }
        }
          let me = this;
            axios.post('/reservas/registrar',{
                    'cliente':this.cliente,
                    'tiempoTransporte':this.tiempoTransporte,
                    'fechaReserva':this.fechaReserva,
                    'horaInicio':this.horaInicio,
                    'tiempoServicio':this.tiempoServicio,
                    'fechaFinalizacion':fechaVencimiento,
                    'horarioNocturno':horarioNocturno,
                    'contadorNocturno':contadorNocturnoV.length,
                    "estadoOperacion":1
                }).then(function (reponse){              
                    swal("Reserva Registrada!", "Guardado Correctamente.", "success");
                    me.cambiarPanel(1);
                    me.listarReservas(1,'','','');
                    me.listarTecnico();
                })
                .catch(function(error){
                    me.controlError(error);
                });
          
    },
    guardarSinReplicacion(horarioNocturno,cambiarFecha,contadorNocturno,contadorNocturnoV)
    {
       var fechaVencimiento=this.fechaReserva;
        if(horarioNocturno==1)
        { 
          if(cambiarFecha==1)
          {
            var fecha = this.fechaReserva;
            var new_date = moment(fecha, "YYYY-MM-DD");
            fechaVencimiento = new_date.add(1, 'days').format('YYYY-MM-DD');
          }
        }
          let me = this;
            axios.post('/reservas/registrar',{
                    'cliente':this.cliente,
                    'tiempoTransporte':this.tiempoTransporte,
                    'fechaReserva':this.fechaReserva,
                    'horaInicio':this.horaInicio,
                    'tiempoServicio':this.tiempoServicio,
                    'fechaFinalizacion':fechaVencimiento,
                    'horarioNocturno':horarioNocturno,
                    'contadorNocturno':contadorNocturnoV.length,
                    "data":this.selected,
                    "estadoOperacion":0
                }).then(function (reponse){              
                    swal("Reserva Registrada!", "Guardado Correctamente.", "success");
                    me.cambiarPanel(1);
                    me.listarReservas(1,'','','');
                    me.listarTecnico();
                })
                .catch(function(error){
                    me.controlError(error);
                });
            
    },
    listarReservas(page,fechaInicio,fechaFin,buscar) 
    {
      if(fechaInicio!=''){
        if(fechaFin=='')
        fechaFin = fechaInicio;
      }

      if(fechaFin!=''){
        if(fechaInicio=='')
          fechaInicio = fechaFin;
      }
      
      let me = this;
      //solo operaciones 
      //ignorar el nombre =  listarReservaMenosOperaciones
      var url = 'Mip/reservas/listarReservasOperaciones?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
      axios.get(url).then(function(reponse) {
          var resp =  reponse.data;
          console.log(resp.reserva.data);
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    listarReservasReset(page,buscar) {
      let me = this;
      me.buscar="";
      var url = 'Mip/reservas/listarReservaMenosOperaciones?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
      axios.get(url).then(function(reponse) {
          var resp =  reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    cambiarPagina(page,fechaInicio,fechaFin,buscar){
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarReservas(page,fechaInicio,fechaFin,buscar);
    },
   
    limpiarDatos(){
      let me = this;
      me.selected=[];
      me.cliente="";
      me.nombreSeleccionado="";
      me.tiempoTransporte="";
      me.fechaReserva="";
      me.horaInicio="";
      me.tiempoServicio="";
      me.nombre="";
      me.selectedReplicacion=[];
      me.fechaReservaReplicacion="";
      me.horaInicioReplicacion="";
      me.tiempoServicioReplicacion="";
      me.buscar="";
      me.errorReserva=[];


      me.horarioNocturnoR=0;
      me.cambiarFechaR=0;
      me.contadorNocturnoVR=0;
      me.equipoTecnico="";
      
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
      var url = '/tecnicos/listadoTecnicosFechaActualAdminAmbos?fecha='+me.fechaReserva;
      axios.get(url)
        .then(function(reponse) 
        {  
          me.listaTecnico = reponse.data;
          me.listaTecnicoReplicacion=reponse.data;
          me.listarDetalleReservasFechaActual(me.listaTecnico,me.fechaReserva);
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
      //obtiene las reservas de los tecnicos de la fecha actual
    listarDetalleReservasFechaActual(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           //me.listadoDetalleReserva = reponse.data;
           me.listadoDetalleReserva = me.obtNuevosDatos(reponse.data,listaTecnico);
           me.listadoDetalleReservaReplicacion = reponse.data;
           me.filaBodyReserva = me.bodyFila1(listaTecnico,me.listadoDetalleReserva);
           me.filaBodyReservaReplicacion = me.filaBodyReserva;
           

        })
        .catch(function(error) {
          me.controlError(error);
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
          me.controlError(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReserva(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           me.listadoDetalleReserva = me.obtNuevosDatos(reponse.data,listaTecnico);
           me.filaBodyReserva = me.bodyFila1(listaTecnico,me.listadoDetalleReserva);
        })
        .catch(function(error) {
          me.controlError(error);
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
          me.controlError(error);
        });
    },
    listarDetalleReservasFechaSeleccionadaReplicacion_1(listaTecnico,fechaReserva) {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
          
           me.listadoDetalleReservaReplicacion = me.obtNuevosDatos(reponse.data,listaTecnico);
           me.filaBodyReservaReplicacion = me.bodyFila1(listaTecnico,me.listadoDetalleReservaReplicacion);
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    //Fin Filtros de Replicacion 1



    modificar(reserva){
      let me =this;
      me.equipoTecnico='';

        //me.selectedModificar=[];
        this.codReserva = reserva.codReserva;

        me.listarTrabajadoresModificar(reserva.codReserva,reserva.fecha);
        //me.filtrarFechaTecnicoModificar(reserva.codReserva,reserva.fecha);
        //me.listarDetalleReservaModificar(reserva.codReserva,reserva.fecha);
        this.nombreCliente="ACTUALIZAR RESERVA | "+reserva.NOMBRE+' '+reserva.AP_CLIENTE+' '+reserva.AM_CLIENTE;
        this.fechaReservaModificar = reserva.fecha;
        this.horaInicioModificar = reserva.horainicio;

        this.fechaReservaModificarCambio=reserva.fecha,
        this.horaInicioModificarCambio=reserva.horainicio;

        this.tiempoServicioModificar = reserva.tiempoServicio;
        this.tiempoTransporteModificar = reserva.tiempoTransporte;
        
        this.codOTReprogramacion=reserva.codOT;
        this.modal=1;
      
    },
    //Trae los trabajadores de la reserva ha modificar
    listarTrabajadoresModificar(idReserva,fecha) {
      let me = this;
      me.selectedModificar=[];
      axios.get("/listarTrabajadoresModificar?page=1&idReserva="+idReserva).then(function(reponse) {
          var objeto = reponse.data;
          for(var v=0;v<objeto.length;v++){
              me.selectedModificar.push(objeto[v].codTrabajador);
          }
          me.filtrarFechaTecnicoModificar(idReserva,fecha);
        })
        .catch(function(error) {
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
    filtrarFechaTecnicoModificar(idReserva,fecha){
      let me = this;
      var url = '/tecnicos/listadoTecnicosFechaActualAdmin?fecha='+fecha;
      axios.get(url)
        .then(function(reponse) {  
          me.listaTecnicoModificar = reponse.data;
          me.listarDetalleReservaModificar(me.listaTecnicoModificar,idReserva,fecha);
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    listarDetalleReservaModificar(listaTecnico,idReserva,fecha) {
      let me = this;
     // var idReserva=80;
      axios.get("/DetalleReservaModificar?page=1&idReserva="+idReserva+"&fecha="+fecha).then(function(reponse) {
          //me.listadoReservaModificar = reponse.data;
          me.listadoReservaModificar = me.obtNuevosDatos(reponse.data,listaTecnico);
          me.filaBodyReservaModificar = me.bodyFila1(listaTecnico,me.listadoReservaModificar);
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    cerrarModalModificar(){
      let me=this;
      this.modal=0;
    },
    obtPosicionVector(horaInicio)
    {
      var posicion;
        for (var i = 0; i < this.vectorHorario.length; i++)
        {
            if(this.vectorHorario[i].value==horaInicio){
                posicion=i;
            }
        }
        return posicion;
    }
    ,
    obtNuevosDatos(listadoDetalleReserva,vectorTecnico)
    {
      var vector=[];
      
        for (var z = 0; z < vectorTecnico.length; z++)
        {
            var con=0;
            if(vectorTecnico[z].activo==1)
            {
              
                
                for(var f = 0; f < vectorTecnico[z].incremento; f++)
                {
                  var fecha="FECHA: ";
                  var titulo=fecha+vectorTecnico[z].fechaFinalizacion+"\n"+
                            //"OT: "+'\n'+
                            'CLIENTE: \n';
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
                  obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                  
                  vector.push(obj); 
                }
              
            }
        }  
        

        for (var i = 0; i < listadoDetalleReserva.length; i++)
        {
            var posicion = this.obtPosicionVector(listadoDetalleReserva[i].horaInicio);
            //posicion++;
            var tiempoServicio = listadoDetalleReserva[i].tiempoServicio;
            tiempoServicio = tiempoServicio/30;
            var tiempoTransporte = listadoDetalleReserva[i].tiempoTransporte;
            tiempoTransporte = tiempoTransporte/30;
         
            for (var v = 0; v < tiempoServicio+1; v++)
            {
              if(posicion<=47)
              {
                if(listadoDetalleReserva[i].NRO_ORDEN==null)
                {
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: SIN OT"+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                else{
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: "+listadoDetalleReserva[i].NRO_ORDEN+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
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
                
                obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';

                vector.push(obj); 
              }
              
            }
           
            for (var t = 0; t < tiempoTransporte-1; t++)
            {
              if(posicion<=47)
              {
                  if(listadoDetalleReserva[i].NRO_ORDEN==null)
                {
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: SIN OT"+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                else{
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: "+listadoDetalleReserva[i].NRO_ORDEN+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
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


                  obj.tipo = '<td class="centroTransporte" title="TRANPORTE"><b>'+hora+'</b><br>'+'TRANSPORTE</td>';
                  /*
                  if(t!=tiempoTransporte-1)
                  {
                    
                    obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                  }
                  else
                  {
                    if(tiempoTransporte>1){
                        obj.tipo = '<td class="centroTransporte" title="TRANPORTE"><b>'+hora+'</b><br>'+'TRANSPORTE</td>';
                    }
                    else{
                        obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                    }
                    
                  }
                  */
                  vector.push(obj);
              }
               
            }

           
        }
      
        return vector;
    },
    
    bodyFila1(listaTecnico,listadoDetalleReserva) 
    {
      
     
      let fila = "";
       var ok=0;
        
        for (var v = 0; v < this.vectorHorario.length; v++) 
        {
         
            fila += '<tr style="height: 60px;">';
             //fila +='<td><button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;width:60px;" type="button" >'+this.vectorHorario[v].horario+'</button></td>';
            
            for (var i = 0; i < listaTecnico.length; i++) 
            {
               
                var ok=0;
                for (var z = 0;z < listadoDetalleReserva.length;z++) 
                {
                    var horaInicio = listadoDetalleReserva[z].horaInicio.substring(0, 5);
                   
                    
                    var nombreTec = listaTecnico[i].NOMBRE+' '+listaTecnico[i].AP_PATERNO+' '+listaTecnico[i].AP_MATERNO;
                    var nombreTecDetReserva = listadoDetalleReserva[z].NOMBRE+' '+listadoDetalleReserva[z].AP_PATERNO+' '+listadoDetalleReserva[z].AP_MATERNO;
                    
                    if (this.vectorHorario[v].horario == horaInicio && nombreTec == nombreTecDetReserva)
                    {
                        ok=1;
                        fila +=listadoDetalleReserva[z].tipo;


                    }
                    
                }
                if(ok==0)
                fila +='<td class="centroHorario" title="SIN RESERVA"><b>'+this.vectorHorario[v].horario+'</b></td>';
                
                
            }
            fila += "</tr>";
        }
        
      return fila;
    },
    bodyFila(listaTecnico,listadoDetalleReserva) {
      
      let fila = "";
      for (var i = 0; i < listaTecnico.length; i++) {
        fila += '<tr style="height: 60px;">';
        fila +='<td style="display: none"><button class="btn btn-square btn-success btn-block" type="button">' + listaTecnico[i].NOMBRE +" " +listaTecnico[i].AP_PATERNO +" " +listaTecnico[i].AP_MATERNO +"</button></td>";
        var ok=0;
        for (var v = 0; v < this.vectorHorario.length; v++) 
        {
          let temporal = "";
          var opc=0;
          
          if(listaTecnico[i].activo==1)
          {
            if(ok==0 && listaTecnico[i].incremento!=0)
            {
              for (var inc = 0; inc < listaTecnico[i].incremento; inc++) 
              {
                //</br>0001
                temporal +='<td class="centro"><a class="badge badge-danger colorRojo" href="#">Reservado</a></td>';
                v++;
              }
              v--;
              ok=1;
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
                me.listarReservas(1,'','',me.buscar);
                
                swal(Activado+'!','La reserva ha sido '+Activado+' con éxito.','success' )
            }).catch(function (error) {
                me.controlError(error);
            });
                    
          } else if (
              result.dismiss === swal.DismissReason.cancel
          ) {
          }
          }) 
    },


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - EQUIPO TECNICO -////////////////////////////////////////////////////////////// 
    listarEquipos(){
      let me = this;
      var url = '/Mip/equipos/listarOperaciones';
      axios.get(url)
        .then(function(reponse) {  
          //arrayEquipoGenerado
          me.arrayEquipo=reponse.data;
          for (var i = 0; i < me.arrayEquipo.length; i++) {
            me.arrayEquipoGenerado +='<option value="' +me.arrayEquipo[i].COD_EQUIPO +'">' +me.arrayEquipo[i].DESCRIPCION+"</option>";
          }
          me.arrayEquipoGenerado +='<option value="888">COORDINACION OPERACIONES</option>';
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    listarTecnicosEquipo(){
      let me = this;
      if(me.equipoTecnico!='888')
      {
      me.selected=[];
      me.selectedReplicacion=[];
      var url = '/equipos/listarTecnicoEquipo?codEquipo='+me.equipoTecnico;
      axios.get(url)
        .then(function(reponse) 
        {  
          var objeto = reponse.data;
          for(var v=0;v<objeto.length;v++){
              me.selected.push(objeto[v].COD_PERSONAL);
              me.selectedReplicacion.push(objeto[v].COD_PERSONAL);
          }
        })
        .catch(function(error) {
          me.controlError(error);
        });
        }
      else{
        me.selected=[];
        me.selectedReplicacion=[];
      }
      
    },
    listarTecnicosEquipoModificar()
    {
      let me = this;
        me.selectedModificar=[];
        var url = '/equipos/listarTecnicoEquipo?codEquipo='+me.equipoTecnico;
        axios.get(url)
          .then(function(reponse) 
          {  
            var objeto = reponse.data;
            for(var v=0;v<objeto.length;v++){
                me.selectedModificar.push(objeto[v].COD_PERSONAL);
            }
          })
          .catch(function(error) {
            me.controlError(error);
          });

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
            }


    },
  mounted() {
  
    this.obtCodPersonal();
    this.listarReservas(1,this.fechaInicio,this.fechaFin,this.buscar);

    this.listarTecnico();
    this.listarEquipos();
  }
};
</script>
