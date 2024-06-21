<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Equipos
                        <button type="button" @click="abrirModal('equipos','registrar')" class="btn btn-success">
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
                                    <th style="width:40px">Editar</th>
                                    <th>Eliminar</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="equipos in arrayEquipo" :key="equipos.COD_EQUIPO">
                                    <td>
                                        <button type="button" @click="abrirModal('equipos','actualizar',equipos)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                        </button>
                                    </td>
                                    <td v-text="equipos.DESCRIPCION"></td>
                                    <td>
                                        <div v-if="equipos.ACTIVO">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Desactivado</span>
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
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Descripcion</label>
                                    <div class="col-md-9">
                                        <input type="text"  v-model="descripcion" class="form-control" placeholder="Descripcion">
                                        
                                    </div>
                                </div>
        
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" @click="registrarEquipos()" class="btn btn-success">Guardar</button>
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
                descripcion:'',
                arrayEquipo :[],
                modal: 0,
                tituloModal:'',
                tipoAccion:0
            }
        },
        methods: {
            listarEquipos(){
                let me =this;
                axios.get('/equipos').then(function (reponse){
                    me.arrayEquipo = reponse.data;
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            registrarEquipos(){               
                let me = this;
                axios.post('/equipos/registrar',{
                    'descripcion':this.descripcion
                }).then(function (reponse){              
                    me.cerrarModal();
                    me.listarEquipos();
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "equipos":
                    {
                        switch(accion)
                        {
                            case "registrar":
                            { 
                                this.modal=1;
                                this.descripcion='';
                                this.tituloModal='Registrar Equipo';
                                this.tipoAccion=1;
                                break;
                            }
                            case "actualizar":{

                            }
                        }
                    }
                }
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
            }
        },
        mounted() {
            this.listarEquipos();
        }
    }
</script>

<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }

    
</style>
