<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                    
                        <button type="button" @click="abrirModal('registrar')" class="btn btn-success colorVerde">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h1 class="centro">LISTA DEL PERSONAL</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <!--
                                    <select class="form-control" v-model="selectGrupo" @change="listarPersonal(1,'')">
                                        <option>-- SELECCIONE UNA OPCION --</option>
                                        <option value="1">MENSUAL</option>
                                        <option value="2">BIMESTRAL</option>
                                        <option value="3">TRIMESTRAL</option>
                                    </select>
                                    -->
                                    <input type="text" v-model="buscar" @keyup="listarPersonal(1,buscar)" class="form-control" placeholder="Buscar">
                                    
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;height:500px;">
                        <table id="tablaPrincipalPersonal" class="tabla">
                            <thead>
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th style="width:40px;"></th>
                                    <th>Nombre</th>
                                    <th>Telefono</th>
                                    <th>Direccion</th>
                                    <th>Tipo</th>
                                    <th>Equipo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="personal in arrayPersonal" :key="personal.COD_PERSONAL" style="height:50px" @click="cambiarColorFila('tablaPrincipalPersonal')">
                                    <td>
                                        <button type="button"  class="btn btn-warning" title="Editar Personal" @click="abrirModal('actualizar',personal)">
                                          <i class="icon-pencil"></i>
                                        </button>
                                    </td>
                                    <td v-if="personal.ACTIVO">
                                        <button type="button" @click="abrirModalEstado(1,personal.COD_PERSONAL)" class="btn btn-danger colorRojo" title="Desactivar">
                                        <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                    <td v-else>
                                        <button type="button" @click="abrirModalEstado(0,personal.COD_PERSONAL)" class="btn btn-success colorVerde" title="Activar">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                 
                                    <td v-text="personal.NOMBRE+' '+personal.AP_PATERNO+' '+personal.AP_MATERNO"></td>
                                    <td v-text="personal.TELEFONO"></td>
                                    <td v-text="personal.DIRECCION"></td>
                                    <td>
                                        <div v-if="personal.TIPO_PERSONAL==1">
                                            <span class="badge colorAzul">Ejecutivo de Venta</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge colorVerde">Tecnico</span>
                                        </div>
                                    </td>
                                    <td v-text="personal.DESCRIPCION"></td>
                                    <!--<td v-text="personal.DESCRIPCION"></td>-->
                                    <td>
                                        <div v-if="personal.ACTIVO">
                                            <span class="badge colorVerde">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge colorRojo">Desactivado</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <br>
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
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>



            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:500px;">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Nombre <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="nombre">
                                            <span v-if="camposRequeridos.nombre" class="error rojo">{{camposRequeridos.nombre[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Apellido Paterno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoP">
                                            <span v-if="camposRequeridos.apellidoPaterno" class="error rojo">{{camposRequeridos.apellidoPaterno[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Apellido Materno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoM">
                                            <span v-if="camposRequeridos.apellidoMaterno" class="error rojo">{{camposRequeridos.apellidoMaterno[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Telefono <span style="color:red">(*)</span></label>
                                            <input type="number" min="1" max="100" class="form-control" v-model="telefono">
                                            <span v-if="camposRequeridos.telefono" class="error rojo">{{camposRequeridos.telefono[0]}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Tipo Personal <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="tipoPersonal">
                                                <!--
                                                <option value="1">EJECUTIVO DE VENTA</option>
                                                <option value="2">TECNICO C.E</option>
                                                <option value="3">TECNICO C.F</option>
                                                -->


                                                <option value="1">EJECUTIVO DE VENTA</option>
                                                <option value="2">TECNICO CHOFER C.E</option>  <!-- EN LOS EJECUTIVOS SOLO FILTRA ESTE TIPO -->
                                                <option value="3">TECNICO C.F</option>
                                                <option value="4">TECNICO AYUDANTE C.E</option>

                                            </select>
                                            <span v-if="camposRequeridos.tipoPersonal" class="error rojo">{{camposRequeridos.tipoPersonal[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Equipo</label>
                                            <select class="form-control" v-html="listaEquipo" v-model="grupo">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company">Direccion <span style="color:red">(*)</span> </label>
                                            <textarea rows="4" class="form-control" v-model="descripcion">      
                                            </textarea>
                                            <span v-if="camposRequeridos.direccion" class="error rojo">{{camposRequeridos.direccion[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" @click="registrarPersonal()" class="btn btn-success colorVerde"><i class="fa fa-floppy-o"></i> Guardar</button>
                            <button type="button" v-if="tipoAccion==2" @click="actualizarPersonal()" class="btn btn-success colorVerde">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->


            <div class="modal fade" :class="{'mostrar':modalParametros}" aria-hidden="true">
                <div class="modal-dialog modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                          
                            <h4 class="modal-title">CONFIRMAR</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:100px;">
                            <div v-if="tipoAccion==1">
                                <h4>DESEA DESACTIVAR ESTE REGISTRO?</h4>
                            </div>
                            <div v-else>
                                <h4>DESEA ACTIVAR ESTE REGISTRO?</h4>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde" @click="cambiarEstado(0,'Desactivado')">
                                <i class="fa fa-times"></i> Desactivar
                            </button>
                            <button type="button" v-else class="btn btn-success colorVerde" @click="cambiarEstado(1,'Activado')">
                                <i class="fa fa-check"></i> Activar
                            </button>
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
                //selectGrupo:'',
                idPersonal:0,
                nombre:'',
                apellidoP:'',
                apellidoM:'',
                telefono:'',
                tipoPersonal:'',
                grupo:'',
                descripcion:'',
                arrayPersonal :[],
                arrayEquipo :[],
                modal: 0,
                tituloModal:'',
                tipoAccion:0,
                listaEquipo:'',
                //para el modal
                modalParametros:0,
                tipoAccion:0,

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
                camposRequeridos:[],
                clickGuardarOActualizar:0,
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
            
            mensajeCorrecto(mensaje){
                swal(mensaje,"","success" ); 
            },
            limpiar(){
                this.nombre='';
                this.apellidoP='';
                this.apellidoM='';
                this.telefono='';
                this.tipoPersonal='';
                this.grupo='';
                this.descripcion='';
                this.camposRequeridos=[];
            },
            listarPersonal(page,buscar)
            {
                let me =this;
               
                var url = '/personal?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp = reponse.data;
                    me.arrayPersonal = resp.personal.data;
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
                me.listarPersonal (page,buscar);
            },
            listarEquipo(){
                let me =this;
                axios.get('/Mip/equipos/listar').then(function (reponse){
                    me.arrayEquipo = reponse.data;
                    me.listaEquipo +='<option value="">-- NINGUNO --</option>';
                    for (var i = 0; i < me.arrayEquipo.length; i++) {
                        me.listaEquipo +='<option value="' +me.arrayEquipo[i].COD_EQUIPO +'">' +me.arrayEquipo[i].DESCRIPCION +"</option>";
                    }
                })
                .catch(function(error){
                    me.controlError(error);
                });
            },
            registrarPersonal()
            {   
                
                let me = this;
               
                if(me.clickGuardarOActualizar==0)
                {
                    me.clickGuardarOActualizar=1;
                    
                    axios.post('/personal/registrar',{
                    'nombre':this.nombre,
                    'apellidoPaterno':this.apellidoP,
                    'apellidoMaterno':this.apellidoM,
                    'telefono':this.telefono,
                    'tipoPersonal':this.tipoPersonal,
                    'grupo':this.grupo,
                    'direccion':this.descripcion
                    }).then(function (reponse){
                        me.mensajeCorrecto("Guardado Correctamente");
                        me.cerrarModal();
                        me.limpiar();
                        me.listarPersonal(1,'');
                        me.clickGuardarOActualizar=0;
                    })
                        .catch(function(error){
                        me.clickGuardarOActualizar=0;
                        me.camposRequeridos = me.controlError(error);
                    });
                
                }
                
                
            },
            actualizarPersonal(){
                let me = this;
                if(me.clickGuardarOActualizar==0)
                {
                    me.clickGuardarOActualizar=1;
                    axios.put('/personal/actualizar',{
                        'COD_PERSONAL':me.idpersonal,
                        'nombre':me.nombre,
                        'apellidoPaterno':me.apellidoP,
                        'apellidoMaterno':me.apellidoM,
                        'telefono':me.telefono,
                        'tipoPersonal':me.tipoPersonal,
                        'grupo':me.grupo,
                        'direccion':me.descripcion,
                            
                    }).then(function (reponse){
                        me.clickGuardarOActualizar=0;
                        me.mensajeCorrecto("Actualizado Correctamente");
                        me.cerrarModal();
                        me.limpiar();
                        me.listarPersonal(1,'');
                    })
                    .catch(function(error){
                        me.clickGuardarOActualizar=0;
                        me.camposRequeridos = me.controlError(error);
                    });
                }

            },
            abrirModalEstado(accion,idpersonal){
                this.modalParametros=1;
                this.tipoAccion=accion;
                this.idPersonal=idpersonal;
            }
            ,
            abrirModal(accion, data = []){
                this.limpiar();
                switch(accion)
                {
                            case "registrar":
                            { 
                                this.modal=1;
                                this.tituloModal='REGISTRO DE PERSONAL';
                                this.tipoAccion=1;
                          
                                break;
                            }
                            case "actualizar":{
                                this.modal=1;
                                this.tituloModal='ACTUALIZAR PERSONAL';
                                this.tipoAccion=2;

                                this.nombre = data.NOMBRE;
                                this.apellidoP = data.AP_PATERNO;
                                this.apellidoM = data.AP_MATERNO;
                                this.telefono = data.TELEFONO;
                                this.descripcion = data.DIRECCION;
                                this.tipoPersonal = data.TIPO_PERSONAL;
                                this.grupo = data.COD_EQUIPO;
                                this.idpersonal = data.COD_PERSONAL;
                                
                                break;
                            }
                }
            },
            cambiarColorFila(id)
            {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
                this.modalParametros=0;
            },
            cambiarEstado(estado,titulo){
                let me = this;
                axios.put('/personal/cambiarEstado',{
                    'idPersonal' : this.idPersonal,
                    'estado' : estado,
                }).then(function (response) {
                    me.listarPersonal(1,'');
                    me.cerrarModal();
                    swal("Personal "+titulo+"!", ""+titulo+" Correctamente.", "success");
                }).catch(function (error) {
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
            this.listarPersonal(1,this.buscar);
            this.listarEquipo();
        }
    }
</script>
