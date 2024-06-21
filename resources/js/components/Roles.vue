<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Roles   
                        <button type="button" @click="abrirModal('rol','registrar')" class="btn btn-success colorVerde">
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
                                        <th style="width:40px;">Editar</th>
                                        <th style="width:40px;">Eliminar</th>
                                        <th>Nombre</th>
                                        <th>Descripcion</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="roles in arrayRoles" :key="roles.id">
                                        <td>
                                            <button type="button" @click="abrirModal('rol','actualizar',roles)" class="btn btn-warning">
                                            <i class="icon-pencil"></i>
                                            </button> &nbsp;
                                        </td>
                                        <td v-if="roles.condicion">
                                            <button type="button" class="btn btn-danger colorRojo" @click="cambiarEstado(roles.id,'Desactivar','Desactivado')">
                                            <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                        <td v-else>
                                            <button type="button" class="btn btn-success colorVerde"  @click="cambiarEstado(roles.id,'Activar','Activado')">
                                            <i class="fa fa-check"></i>
                                            </button>
                                        </td>
                                        <td v-text="roles.nombre"></td>
                                        <td v-text="roles.descripcion"></td>
                                        <td>
                                            <div v-if="roles.condicion">
                                                <span class="badge badge-success colorVerde">Activado</span>
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company">Nombre <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="nombre">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company">Descripcion </label>
                                            <textarea rows="4" cols="50" class="form-control" v-model="descripcion">
                                          
                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Accesos</h5>
                                    </div>
                                </div>

                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th>Sub Menu</th>
                                 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="roles in arrayMenu" :key="roles.codMen">
                                            <td v-text="roles.nombre"></td>
                                            <td>
                                                <ul v-for="ro in arraySubMenu" :key="ro.codSubMen">
                                                    <div v-if="ro.codMen==roles.codMen">
                                                        <input type="checkbox"    :value="ro.codSubMen"  v-model="arrayAccesos" > {{ro.nomSubMen}}
                                                    </div>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button" v-if="tipoAccion==1" @click="registrarRoles()" class="btn btn-success colorVerde"><i class="fa fa-floppy-o"></i> Guardar</button>
                            <button type="button" v-if="tipoAccion==2"  class="btn btn-success colorVerde"><i class="icon-pencil"></i> Actualizar</button>
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
                </div>
            </div>
            <!-- Fin del modal Eliminar -->
        </main>
</template>

<script>
    export default {
        data(){
            return {
                nombre:'',
                descripcion:'',
                arrayRoles :[],
                arrayMenu :[],
                arraySubMenu :[],
                arrayAccesos:[],
                arrayDetalle:[],
                modal: 0,
                tituloModal:'',
                tipoAccion:0,
                criterio : 'nombre',
                buscar : '',
            }
        },
        methods: {
            cambiarEstado(idRol,desactivar,desactivado){     
                swal({
                title: 'Esta seguro de a el Rol?',
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

                    axios.put('/roles/'+desactivar,{
                        'idRol': idRol
                    }).then(function (response) {
                        me.listarRoles();
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
            listarRoles(){
                let me =this;
                axios.get('/roles/listado').then(function (reponse){
                    me.arrayRoles = reponse.data;
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            listarMenu(){
                let me =this;
                axios.get('/Menu/subMenu1').then(function (reponse){
                    me.arrayMenu = reponse.data;
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            listarSubMenu(page,buscar,criterio){
                let me=this;
                var url= '/subMenu?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arraySubMenu = respuesta.subMenus.data;
                
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registrarRoles(){     
                let me = this;
                axios.post('/roles/registrar',{
                    'nombre':this.nombre,
                    'descripcion':this.descripcion,
                    'detalles':this.arrayAccesos,
                }).then(function (reponse){              
                    me.cerrarModal();
                    me.listarRoles();
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "rol":
                    {
                        switch(accion)
                        {
                            case "registrar":
                            { 
                                this.modal=1;
                                this.descripcion='';
                                this.tituloModal='Registrar Roles';
                                this.tipoAccion=1;
                                break;
                            }
                            case "actualizar":{
                                this.obtDetalleRoles(data['id']);
                                this.modal=1;
                                this.tituloModal='Actualizar Roles';
                                this.tipoAccion=2;
                                this.nombre= data['nombre'];
                                this.descripcion= data['descripcion'];
                                break;
                            }
                        }
                    }
                }
            },
            obtDetalleRoles(idRol){
                let me =this;
                var url = "/rol/obtDetalleRoles?page=1&idRol="+idRol;
                axios.get(url).then(function(response) 
                {
                    console.log(response.data);
                    me.arrayAccesos = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
            }
        },
        mounted() {
            
            this.listarMenu();
            this.listarSubMenu(1,this.buscar,this.criterio);
            this.listarRoles();
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
