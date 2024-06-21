<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-body">
                        <button type="button" @click="imprimirEXCEL()" class="btn btn-success colorVerde">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                        </button> 
                        <button type="button" @click="imprimirPDF()" class="btn btn-primary">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
                        </button> 
               
                                    <div class="form-group row">
                        <div class="col-md-12">
                            <h1 class="centro">REPORTE ORDEN DE TRABAJO</h1>
                            <hr>
                        </div>
                        </div>

                        <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                    <input type="text" v-model="buscar" @keyup="listadoOt(1,fechaInicio,fechaFin,buscar)"  class="form-control" placeholder="Buscar">
                                </div>
                            </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                    
                                    <input type="date" v-model="fechaInicio" class="form-control" placeholder="Fecha Inicio">
                                    <input type="date" v-model="fechaFin" class="form-control" placeholder="Fecha Fin">
                                    <button type="submit" class="btn btn-primary" @click="listadoOt(1,fechaInicio,fechaFin,buscar)"><i class="fa fa-search"></i> Buscar</button>
                                    <!--<button type="submit" class="btn btn-success colorVerde" @click="listadoOt(1,'','','')"><i class="fa fa-refresh"></i> Cargar</button>-->
                                </div>
                        </div>
                        
                        </div>
                        <div style="overflow-x:auto;height:500px;">
                        <table id="tablaPrincipalReporteEje" class="tabla" style="width:100%">
                            <thead>
                                <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                    <th style="width:5%;">Permiso Modificar</th>
                                    <th style="width:3%;">Nro</th>
                                    <th style="width:20%;">Cliente</th>
                                    <th style="width:10%;">Contacto</th>
                                    <th style="width:20%;">Direccion</th>
                                    <th >Telefono</th>
                                    <th style="width:5%;">Precio</th>
                                    <th>Fecha Servicio</th>
                                    <th style="width:5%;">Hora</th>
                                    <th>Ejecutivo Ventas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr style="height:40px" v-for="ot in arrayOt" :key="ot.COD_ORDEN_TRABAJO" @click="asignarIdOT(ot.COD_ORDEN_TRABAJO,'tablaPrincipalReporteEje')">
                                    <td v-text="ot.ESTADO_PM"></td>
                                    <td v-text="ot.NRO_ORDEN"></td>

                                    <td v-if="(ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMCLIENTE).length>35" v-text="(ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMCLIENTE).substring(0, 35)+'...'"  v-bind:title="ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMCLIENTE"></td>
                                    <td v-else v-text="ot.AP_CLIENTE+' '+ot.AM_CLIENTE+' '+ot.NOMCLIENTE"></td>

                                    <td v-if="ot.CONTACTO.length>30"  v-text="ot.CONTACTO.substring(0, 30)+' ...'" v-bind:title="ot.CONTACTO"></td>
                                    <td v-else v-text="ot.CONTACTO"></td>

                                    <td v-if="ot.DIRECCION.length>35" v-text="ot.DIRECCION.substring(0, 35)+' ...'"  v-bind:title="ot.DIRECCION"></td>
                                    <td v-else v-text="ot.DIRECCION"></td>

                                    <td v-text="ot.TELEFONO"></td>
                                    <td v-text="ot.PRECIO"></td>
                                    <td v-text="ot.FECHAS"></td>
                                    <td v-text="ot.HORA_SERVICIO"></td>
                                    <td v-text="ot.AP_PATERNO+' '+ot.AP_MATERNO+' '+ot.NOMBRE"></td>
                                    <td>
                                        <!--
                                        <div v-if="ot.SUSPENDIDA">
                                            <span class="badge badge-danger colorRojo">SUSPENDIDA</span>
                                        </div>
                                        <div v-else-if="ot.REPROGRAMADA">
                                            <span class="badge badge-success colorVerde">REPROGRAMADA</span>
                                        </div>
                                        <div v-else-if="ot.APROBADA">
                                            APROBADA
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-primary colorAzul">SIN APROBAR</span>
                                        </div>
                                        -->
                                        <div v-if="ot.COBRADA">
                                             <span class="badge badge-danger colorVerde">COBRADA</span>
                                        </div>
                                        <div v-else-if="ot.APROBADA">
                                             <span class="badge badge-danger colorRojo">APROBADA</span>
                                        </div>
                                        <div v-else-if="ot.ANULADA">
                                             <span class="badge badge-danger colorCeleste">ANULADA</span>
                                        </div>
                                        <div v-else-if="ot.SUSPENDIDA">
                                            <span class="badge badge-danger colorAmarillo">SUSPENDIDA</span>
                                        </div>
                                        <div v-else-if="ot.REPROGRAMADA">
                                            <span class="badge badge-success colorAzul">REPROGRAMADA</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-success colorBlanco">SIN APROBAR</span>
                                            
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
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
         
            <!--modal De asignacion OT -->
            <div class="modal fade" :class="{'mostrar':modal}" aria-hidden="true">
                    <div class="modal-dialog modal-success" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <!-- v-text="tituloModal" -->
                                <h4 class="modal-title" >MENSAJE</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                                <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body" style="height:100px;">
                                <h4>Debe seleccionar una OT</h4>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
            </div>
        <!--fin -->
        
        </main>


        
</template>

<script>
    export default {
        data(){
            return {
                mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",
                arrayOt:[],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                fechaInicio:'',
                fechaFin:'',
                codOT:0,
                modal:0,
                buscar:''
                
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
          
            imprimirEXCEL(){
                //location.href = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/DSExcelOrdenTrabajoTodoEjecutivo.php?buscar=&fechai=2019-05-14&fechaf=2019-05-14";
                     location.href = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/DSExcelOrdenTrabajoTodoEjecutivo.php?buscar=&fechai="+this.fechaInicio+"&fechaf="+this.fechaFin;                                                                                 
            },
            cambiarColorFila(id)
            {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            asignarIdOT(codigoOT,id){
                this.codOT = codigoOT;
                this.cambiarColorFila(id);
            },
            imprimirPDF() {
              if(this.codOT==0)
                this.modal=1;
                else
                {
                    //DSforms/DSimprimirOTAjax.php?codigo=
                    var pagina = "http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo="+ this.codOT;
				    var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                    window.open(pagina,"",opciones);
                    this.codOT=0;

                }

            },
            cerrarModal(){
                this.modal=0;
            },
            listadoOt(page,fechaInicio,fechaFin,buscar) {  
            let me = this;
            var url = '/Mip/listaOTEjecutivo?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
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
            listadoOtFiltro(page,buscar){
                let me = this;
                var fechaInicio = me.fechaInicio;
                var fechaFin = me.fechaFin;

                var url = '/Mip/listaOTEjecutivoFiltro?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
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
            cambiarPagina(page,fechaInicio,fechaFin,buscar){
                let me = this;
                me.pagination.current_page = page;
                me.listadoOt(page,fechaInicio,fechaFin,buscar);
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
                    var cadena = String(error);
                    cadena = cadena.substr(cadena.length-3,3)
                    swal("Mensaje ",this.mensajeError +" "+cadena, "error");
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
      
            this.listadoOt(1,this.fechaInicio,this.fechaFin,this.buscar);
        }
    }
</script>

<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }

    

    .colorRojo {
        /* aprobada */
        background-color: crimson;
    }

    .colorVerde {
        /* cobrada */
        background-color:green;
    }
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .colorCeleste{
        /* anulada */
        background-color:rgba(0, 255, 255, 0.904);
        color: black;
    }
    .colorAzul{
        /* reprogramada */
        background-color:rgba(142, 167, 235, 0.904);
    }
    .colorAmarillo{
        /* suspendido */
        background-color:darkgoldenrod; 
    }
     .colorBlanco{
        /* sin aprobar */
        background-color:cornsilk;
        color: black;
    }


</style>
