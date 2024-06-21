<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Usuario
                        <button type="button" @click="abrirModal('usuario','registrar')" class="btn btn-success colorVerde">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" id="opcion" name="opcion">
                                      <option value="nombre">Nombre</option>
                                      <option value="descripcion">Descripción</option>
                                    </select>
                                    <input type="text" id="texto" name="texto" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="width:60px">Editar</th>
                                    <th style="width:60px">Eliminar</th>
                                    <th style="width:60px">imagen</th>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="obj in arrayUsuario" :key="obj.COD_EQUIPO">
                                    <td>
                                        <button type="button" @click="abrirModal('usuario','actualizar',obj)" class="btn btn-warning">
                                          <i class="icon-pencil"></i>
                                        </button>
                                    </td>
                                    <td v-if="obj.estado">
                                        <button type="button" class="btn btn-danger colorRojo" @click="cambiarEstado(obj.codUsuario,'Desactivar','Desactivado')">
                                          <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                    <td v-else>
                                        <button type="button" class="btn btn-success colorVerde"  @click="cambiarEstado(obj.codUsuario,'Activar','Activado')">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                    <td>

                                       <img src='imgUsuarios/logo1.png'  width="50px" height="50px">
                                   
                                    </td>
                                    <td v-text="obj.NP_NOMBRE+' '+obj.AP_PATERNO+' '+obj.AP_MATERNO"></td>
                                    <td v-text="obj.nombre"></td>
                                    <td v-text="obj.usuario"></td>
                                    <td>
                                        <div v-if="obj.estado">
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
                                <li class="page-item">
                                    <a class="page-link" href="#">Ant</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">4</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Sig</a>
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
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-6"><label for="company">
                                        Personal
                                        <span style="color: red;">(*)</span></label> 
                                        <div class="input-group">
                                            <select class="form-control" v-html="selectPersonal" v-model="personal">
                                            </select>
                                            <span class="input-group-append">
                                                <button title="Agregar Personal" type="button" class="btn btn-success" @click="abrirModalPersonal()">
                                                    <i aria-hidden="true" class="fa fa-plus"></i> 
                                                        Agregar
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Rol <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-html="selectRol" v-model="rol">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">usuario <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="usuario">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Password <span style="color:red">(*)</span></label>
                                            <input type="password" class="form-control" v-model="password">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Imagen</label>
                                            <input type="file" class="form-control">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6"> 
                                        
                                    </div>
                                </div>
        
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" @click="registrarUsuario()" class="btn btn-success">Guardar</button>
                            <button type="button" v-if="tipoAccion==2"  class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
            <!-- Inicio del modal Eliminar -->
            <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-danger" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Eliminar Categoría</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Estas seguro de eliminar la categoría?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-danger">Eliminar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- Fin del modal Eliminar -->
        </main>
</template>

<script>
    export default {
        data(){
            return {
                personal:'',
                rol:'',
                imagen:'logo1.png',
                usuario:'',
                password:'',
               
                modal: 0,
                tituloModal:'',
                tipoAccion:0,
                arrayUsuario:[],
                arrayPersonal:[],
                arrayRol:[],
                selectPersonal:'',
                selectRol:''
            }
        },
        methods: {
            cambiarEstado(idUsuario,desactivar,desactivado){
                /*alert(idUsuario);
                alert(desactivar);
                alert(desactivado);*/

                swal({
                title: 'Esta seguro de '+ desactivar +'?',
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

                    axios.put('/usuario/Desactivar',{
                        'idUsuario': idUsuario
                    }).then(function (response) {
                        me.listarUsuario();
                        swal(desactivado+'!','El registro ha sido '+desactivado+' con éxito.','success' )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                }
                }) 
            },
            img(){

            },
            listadoPersonal() {
            let me = this;
            axios .get("/personal/listadoActivo")
                .then(function(reponse) {
                me.arrayPersonal = reponse.data;

                for (var i = 0; i < me.arrayPersonal.length; i++) 
                {
                    me.selectPersonal += '<option value="' + me.arrayPersonal[i].COD_PERSONAL +'">' + me.arrayPersonal[i].NP_NOMBRE 
                    + ' '+me.arrayPersonal[i].AP_PATERNO+ ' '+me.arrayPersonal[i].AP_MATERNO+ "</option>";
                }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            listadoRoles() {
            let me = this;
            axios .get("/roles/listadoActivos")
                .then(function(reponse) {
                me.arrayRol = reponse.data;
            

                for (var i = 0; i < me.arrayRol.length; i++) 
                {
                    me.selectRol += '<option value="' + me.arrayRol[i].id +'">' + me.arrayRol[i].nombre+ "</option>";
                }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            listarUsuario(){
                let me =this;
                axios.get('/usuario/listado').then(function (reponse){
                    me.arrayUsuario = reponse.data;
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            registrarUsuario(){   
                let me = this;

                axios.post('/usuario/registrar',{
                    'personal':this.personal,
                    'rol':this.rol,
                    'imagen':this.imagen,
                    'usuario':this.usuario,
                    'password':this.password
                }).then(function (reponse){ 
                    me.mensajeCorrecto("Guardado Correctamente");             
                    me.cerrarModal();
                    me.listarUsuario();
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "usuario":
                    {
                        switch(accion)
                        {
                            case "registrar":
                            { 
                                this.modal=1;
                                this.descripcion='';
                                this.tituloModal='Registrar Usuario';
                                this.tipoAccion=1;
                                break;
                            }
                            case "actualizar":
                            {
                                this.modal=1;
                                this.tituloModal='Actualizar Usuario';
                                this.tipoAccion=2;
                                this.personal = data['codPersonal']; 
                                this.rol = data['codRol'];
                                this.usuario = data['NP_NOMBRE'];
                                this.password = data['password'];
                                break;
                            }
                        }
                    }
                }
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
            },
            mensajeError(mensaje){
                swal(mensaje,"","error"); 
            },
            mensajeCorrecto(mensaje){
                swal(mensaje,"","success" ); 
            },
        },
        mounted() {
            this.listarUsuario();
            this.listadoPersonal();
            this.listadoRoles();
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
</style>
