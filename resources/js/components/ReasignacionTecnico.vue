<template>
    <main class="main">
        <ol class>

    </ol>
    
    <div class="container-fluid">
      <div class="card">
          
        <div class="card-header" v-if="listado">
          
        </div>
        <div class="card-header" v-else>
          <button class="btn btn-danger colorRojo" type="button" @click="limpiar()">
                  <i class="fa fa-arrow-circle-left"></i>&nbsp;
                  Atras
          </button>
            <button class="btn btn-success colorVerde" type="button" @click="validarReasignacion()" >
                  <i class="fa fa-floppy-o"></i>&nbsp;
                  {{textoBoton}}
            </button>
        </div>
        <!--Inicio Panel Listado Reserva -->
        <template v-if="listado">
          <div class="card-body">
              
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">LISTA DE TECNICOS</h1>
              </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="input-group">
                        <!-- @keyup="listarPersonal(1,buscar)"  -->
                        <input type="text" v-model="buscar" @keyup="listarTecnico(1,buscar)" class="form-control" placeholder="Buscar">
                    </div>
                </div>
            </div>
            <div style="overflow-x:auto; height:600px">
            
              <table id="tablaPrincipal" class="tabla" style="width:100%">
                <thead>
                  <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                    <th style="width:40px;"></th>
                    <th style="width:70px;"></th>
                    <th>Tecnico</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th>Grupo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(tecnico) in arrayTecnico" :key="tecnico.COD_PERSONAL">
                    <td>
                      <button type="button" class="btn btn-warning" @click="reasignacion('R',1,'',tecnico)" title="Reasignacion Tecnico">
                        <i class="icon-pencil"></i>
                      </button>
                    </td>
                    <td>
                      <button type="button" class="btn btn-primary colorAzul" @click="reasignacion('B',1,'',tecnico)" title="Dar de Baja">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </button>
                    </td>
                    <td v-text="tecnico.NOMBRE+' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO"></td>
                    <td v-text="tecnico.TELEFONO"></td>
                    <td v-text="tecnico.DIRECCION"></td>
                    <td v-text="tecnico.DESCRIPCION"></td>
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
           
            
          <div class="card-body">
              
            <div class="form-group row">
              <div class="col-md-12">
                <h1 v-if="textoBoton=='Reasignacion'" class="centro">REGISTRO DE REASIGNACION</h1>
                <h1 v-else class="centro">REGISTRO DAR DE BAJA</h1>
                <h4 class="centro" v-text="titulo"></h4>
              </div>
            </div>
            <div class="row">
                <div class="col-md-6" v-if="textoBoton=='Reasignacion'">
                    <div class="form-group">
                    <label for="company">
                    Reemplazar por <span style="color:red">(*)</span>
                    </label>
                    <div class="input-group">
                        <input class="form-control colorBloqueado" type="text" v-model="tecnicoB" placeholder="Buscar Tecnico" disabled="true">
                        <button class="btn btn-primary" title="Buscar Clientes" type="button" @click="ModalTecnico()">
                          <i class="fa fa-search" aria-hidden="true"></i> Buscar
                        </button>
                    </div>
                    <span v-if="camposRequeridos.Tecnico" class="error rojo">{{camposRequeridos.Tecnico[0]}}</span>
                    </div>
                </div>
            </div>
            <br>
            <div>
                <h2 v-if="textoBoton=='Reasignacion'" class="centro">SELECCIONAR LAS RESERVAS QUE SE VAN A REASIGNAR</h2>
                <h2 v-else class="centro">SELECCIONAR LAS RESERVAS QUE SE DARAN DE BAJA</h2>
                <div style="overflow-x:auto; height:600px">
                <table id="tablaPrincipal" class="tabla" style="width:100%">
                    <thead>
                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                        <th>&nbsp;
                            <input type="checkbox" v-model="selectedTecnicoTodos" @click="MarcarTodos()">
                        </th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Tiempo Servicio</th>
                        <th>Tiempo Transporte</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="height:40px" v-for="(tecnicoReserva) in arrayTecnicoReservas" :key="tecnicoReserva.codDetReserva">
                        <td>&nbsp;
                            <input type="checkbox" :value="tecnicoReserva" v-model="selectedTecnico">
                        </td>
                        <td v-text="tecnicoReserva.NOMBRE+' '+tecnicoReserva.AP_CLIENTE"></td>
                        <td v-text="tecnicoReserva.fecha"></td>
                        <td v-text="tecnicoReserva.horainicio"></td>
                        <td v-text="tecnicoReserva.tiempoServicio"></td>
                        <td v-text="tecnicoReserva.tiempoTransporte"></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="paginationTecnicoReserva.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPaginaTecnicoReserva(paginationTecnicoReserva.current_page - 1,buscar)">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumberTecnicoReservas" :key="page" :class="[page == isActivedTecnicoReservas ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPaginaTecnicoReserva(page,buscar)" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="paginationTecnicoReserva.current_page < paginationTecnicoReserva.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPaginaTecnicoReserva(paginationTecnicoReserva.current_page + 1,buscar)">Sig</a>
                    </li>
                </ul>
                </nav>
            </div>
          </div>
      
        </template>
        <!--Fin Panel Registro Reserva -->
      </div>
    </div>



            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">LISTA DE TECNICOS</h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        
                        <div class="modal-body" style="height:650px;">
                            <div class="row container-fluid">
                                <input type="text" v-model="buscarModal" @keyup="listarTecnico(1,buscarModal)" class="form-control" placeholder="Buscar">
                            </div>
                            <br>
                            <div style="overflow-x:auto; height:500px">
                                <table id="tablaPrincipal" class="tabla" style="width:100%">
                                    <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th style="width:60px;"></th>
                                        <th>Tecnico</th>
                                        <th>Telefono</th>
                                        <th>Direccion</th>
                                        <th>Grupo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(tecnico) in arrayTecnico" :key="tecnico.COD_PERSONAL">
                                        <td>
                                        <button type="button" class="btn btn-primary colorAzul" @click="seleccionarTecnico(tecnico)" title="Seleccionar Tecnico">
                                            <i class="fa fa-mouse-pointer"></i>
                                        </button>
                                        </td>
                                        <td v-text="' '+tecnico.NOMBRE+' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO"></td>
                                        <td v-text="tecnico.TELEFONO"></td>
                                        <td v-text="tecnico.DIRECCION"></td>
                                        <td v-text="tecnico.DESCRIPCION"></td>
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
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->


            <div class="modal fade" :class="{'mostrar':modalParametros}" aria-hidden="true">
                <div class="modal-dialog modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                          
                            <h4 class="modal-title">MENSAJE</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:100px;">
                            <div v-if="tipoMensajeMosal==1">
                                <h4>DEBE SELECCIONAR LAS RESERVAS</h4>
                            </div>
                            <div v-if="tipoMensajeMosal==2">
                                <h4>DEBE SELECCIONAR OTRO TECNICO</h4>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
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
        data(){
            return {
                mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",
                ///selectGrupo:'',
                idPersonal:0,
                nombre:'',
                apellidoP:'',
                apellidoM:'',
                telefono:'',
                tipoPersonal:'',
                grupo:'',
                descripcion:'',
                arrayTecnico :[],
                arrayEquipo :[],
                modal: 0,
                tituloModal:'',
                tipoAccion:0,
                listaEquipo:'',

           

                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },

                paginationTecnicoReserva : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                buscar:'',
                camposRequeridos:[],


                //////////-    -//////////////
                tecnicoA:'',
                tecnicoB:'',
                codTecnicoA:0,
                codTecnicoB:'',
                listado:1,
                arrayTecnicoReservas :[],
                selectedTecnicoTodos:false,
                selectedTecnico:[],
                modalParametros:0,
                tipoMensajeMosal:0,
                textoBoton:'',
                titulo:'',
                buscarModal:'',
                ClickBoton:0,
                arrayHorarioTecnico:[],
                arrayReservasTecnicoA:[],
                arrayReservasTecnicoB:[],
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

            },
            isActivedTecnicoReservas: function(){
                return this.paginationTecnicoReserva.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumberTecnicoReservas: function() {
                if(!this.paginationTecnicoReserva.to) {
                    return [];
                }
                
                var from = this.paginationTecnicoReserva.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.paginationTecnicoReserva.last_page){
                    to = this.paginationTecnicoReserva.last_page;
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
            cambiarPanel(panel) {
            //this.limpiarDatos();
            this.listado = panel;
            },
            buscarCliente(){
                
            },
            mensajeCorrecto(mensaje){
                swal(mensaje,"","success" ); 
            },
            mensajeAdvertencia(mensaje){
                swal(mensaje,"","warning" ); 
            },
            listarTecnico(page,buscar)
            {
                let me =this;
                var arr=[];
                var url = '/listaTecnico?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data;
                    me.arrayTecnico = resp.personal.data;
                    arr=resp.personal.data;
                    me.pagination = resp.pagination;
                })
                .catch(function(error){
                    me.controlError(error);
                });
                
            },
            cambiarPagina(page,buscar){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarTecnico (page,buscar);
            },
            cambiarPaginaTecnicoReserva(page,buscar,tecnico){
                let me = this;
                //Actualiza la página actual
                me.paginationTecnicoReserva.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.reasignacion(page,buscar,tecnico);
            },
            reasignacion(accion,page,buscar,tecnico)
            {
                let me =this;
                //R = Reasignacion
                //B = Baja
                if(accion=='R')
                me.textoBoton='Reasignacion';
                else
                me.textoBoton='Baja';
              
                me.titulo='TECNICO: '+tecnico.NOMBRE+' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO;
                var url = '/tecnico/listaTecnicoReservas?codTecnico='+tecnico.COD_PERSONAL;
                axios.get(url).then(function (reponse)
                {
                    me.cambiarPanel(0);
                    me.codTecnicoA = tecnico.COD_PERSONAL;
                    me.tecnicoA=tecnico.NOMBRE+' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO;
                    var resp = reponse.data;
                    me.arrayTecnicoReservas = resp.Tecnico.data;
                    
                    me.paginationTecnicoReserva = resp.pagination;
                })
                .catch(function(error){
                    me.controlError(error);
                });
            },
            MarcarTodos(){
                this.selectedTecnico = [];
                if (!this.selectedTecnicoTodos) {
                    for (let i in this.arrayTecnicoReservas) {
                    this.selectedTecnico.push(this.arrayTecnicoReservas[i]);
                    }
                }
            },
            ModalTecnico()
            {
                let me =this;
                if(me.selectedTecnico.length>0)
                {  
                this.modal=1;
                }
                else{
                    me.tipoMensajeMosal=1;
                    me.modalParametros=1;
                }
            },
            cerrarModal(){
                this.modal=0;
                this.modalParametros=0;
            },

            validarReasignacion()
            {
                let me =this; 
                me.arrayReservasTecnicoB=[];
                me.arrayReservasTecnicoA=[];
                //me.prueba();
            

                
                ///////////////////////- LAS RESERVAS DEL TECNICO A | TECNICO QUE DEJARA LA EMPRESA  -////////////////////////////////////
                for(var v=0;v<me.selectedTecnico.length;v++)
                {
                    me.horarioSeleccionado(v,me.selectedTecnico,me.arrayReservasTecnicoA);
                }
                //console.log(me.arrayReservasTecnicoA);
    	    
                
                ///////////////////////- LAS RESERVAS DEL TECNICO B | TECNICO QUE REEMPLAZARA, AL TECNICO A  -////////////////////////////////////
                for(var v=0;v<me.arrayHorarioTecnico.length;v++)
                {
                    me.horarioSeleccionado(v,me.arrayHorarioTecnico,me.arrayReservasTecnicoB);
                }
                //console.log(me.arrayReservasTecnicoB);



                var arrayNoDesponibleFecha=[];
                var arrayNoDesponibleCliente=[];
                for(var v=0;v<me.arrayReservasTecnicoA.length;v++)
                {
                    if(!arrayNoDesponibleFecha.includes(me.arrayReservasTecnicoA[v].fecha) && !arrayNoDesponibleCliente.includes(me.arrayReservasTecnicoA[v].cliente))
                    {
                        for(var z=0;z<me.arrayReservasTecnicoB.length;z++)
                        {
                            if(me.arrayReservasTecnicoA[v].fecha == me.arrayReservasTecnicoB[z].fecha 
                            && me.arrayReservasTecnicoA[v].horario == me.arrayReservasTecnicoB[z].horario)
                            {
                                arrayNoDesponibleFecha.push(me.arrayReservasTecnicoA[v].fecha);
                                arrayNoDesponibleCliente.push(me.arrayReservasTecnicoB[z].cliente);
                            }
                        }
                    }
                }
                //console.log(arrayNoDesponibleFecha);
                //console.log(arrayNoDesponibleCliente);
                
                if(arrayNoDesponibleFecha.length>0)
                {
                    //mensaje
                    var mensaje='';
                    for(var v=0;v<arrayNoDesponibleCliente.length;v++)
                    {
                        mensaje+='Tiene servicio el '+arrayNoDesponibleFecha[v]+' con '+arrayNoDesponibleCliente[v] +'<br />';
                    }

                    swal(
                        "Mensaje",
                        '<b>'+mensaje+'</b>',
                        "warning"
                    );
                }
                else
                {
                    me.registrarReasignacion();
                    //swal("Mensaje");
                }
               /*
                var array =[];
                for(var v=0;v<me.arrayTecnicoReservas.length;v++){
                    if(!array.includes(me.arrayTecnicoReservas[v].fecha))
                    {
                        array.push(me.arrayTecnicoReservas[v].fecha);
                
                      }
                    }
                for(var v=0;v<array.length;v++){
                     for(var z=0;z<me.arrayReservasTecnicoA.length;z++)
                     {
                          if(me.arrayReservasTecnicoA[z].fecha==array[v]);
                          {
                             // console.log(v+' - '+me.arrayReservasTecnicoA[z].fecha+' - '+array[v]);
                          }
                     }
                }
                */
            },
            
            prueba(){
                
                let me =this;
              
                //var array=me.arrayTecnicoReservas;
                var array =[];
                for(var c=0;c<me.arrayTecnicoReservas.length;c++)
                {
                    if(!array.includes(me.arrayTecnicoReservas[c].fecha))
                    {
                        array.push(me.arrayTecnicoReservas[c].fecha);
                        me.buscarReservasFechaTecnico(me.codTecnicoB,me.arrayTecnicoReservas[c].fecha);

                    }
                }
               
            },
            buscarReservasFechaTecnico(codTecnico,fecha)
            {
                let me =this;

            
                me.arrayHorarioTecnico=[];
                var url = '/tecnico/listarReservasFechaUnTecnico?codTecnico='+codTecnico+'&fecha='+fecha;
                axios.get(url).then(function (reponse){
                        
                    var obj = reponse.data;
                    for(var v=0;v<obj.length;v++){
                        me.arrayHorarioTecnico.push(obj[v]);
                    }
                     
                })
                .catch(function(error){
                    me.controlError(error);
                });
            
            },
            horarioSeleccionado(indice,vector,arrayReservasTecnico)
            {
                let me =this;
              
                var horaInicioCliente = (vector[indice].horainicio).substring(0, 5);
                var tiempoServicioCliente = vector[indice].tiempoServicio / 30;
                var tiempoTransporteCliente = vector[indice].tiempoTransporte / 30;
                        
                var sumaHorario = tiempoTransporteCliente + tiempoServicioCliente;
                //var arrayHorarioClienteReservaIndividual=[];
                arrayReservasTecnico.push({horario: horaInicioCliente,fecha:vector[indice].fecha,cliente:vector[indice].NOMBRE});

                var contadorNocturno=-1;
                     
                    
                for (var t = 0; t < me.vectorHorario.length; t++) 
                {
                    if (me.vectorHorario[t].horario == horaInicioCliente) 
                    {
                        for (var x = 0; x < sumaHorario-1; x++) 
                        {
                                    //t=47 : es el horario 23:30
                            if(t==47)
                            {
                                contadorNocturno++;
                                arrayReservasTecnico.push({horario: me.vectorHorario[contadorNocturno].horario,fecha:vector[indice].fecha,cliente:vector[indice].NOMBRE});
                            }
                            else
                            {
                                t++;
                                arrayReservasTecnico.push({horario: me.vectorHorario[t].horario,fecha:vector[indice].fecha,cliente:vector[indice].NOMBRE});
                            }
                        } 
                    }
                }    
  
            }
            ,
            registrarReasignacion()
            {
                let me =this;
                if(me.ClickBoton==0)
                {
                    if(me.selectedTecnico.length>0)
                    {
                        me.ClickBoton=1;
                        axios.post('/tecnico/registrarReasignacion',{
                        'codTecnicoA':me.codTecnicoA,
                        'Tecnico':me.codTecnicoB,
                        'selectTecnicoReserva':me.selectedTecnico,
                        'ReasignacionObaja':me.textoBoton
                        }).then(function (reponse){     
                            me.ClickBoton=0;     
                            swal("Servicio Actualizado", "Actualizado Correctamente.", "success");
                            me.limpiar();
                        })
                        .catch(function(error)
                        {
                            me.ClickBoton=0;
                            me.camposRequeridos = me.controlError(error);
                           
                        });
                    
                    }
                    else{
                        me.tipoMensajeMosal=1;
                        me.modalParametros=1;
                    }   
                }
                
                
            },
            limpiar(){
                this.cambiarPanel(1);
                this.codTecnicoB='';
                this.tecnicoB='';
                this.selectedTecnicoTodos=false;
                this.selectedTecnico=[];
                this.camposRequeridos=[];
                this.buscarModal='';
                this.listarTecnico(1,'');
            },
            seleccionarTecnico(tecnico){
            
                let me =this;
                me.tecnicoB = tecnico.NOMBRE+' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO;
                me.codTecnicoB = tecnico.COD_PERSONAL;
                me.prueba();
                me.cerrarModal();
            
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
        cerrarSesion()
        {
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
            this.listarTecnico(1,this.buscar);
        }
    }
</script>
