<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <button type="button" @click="abrirModal()" class="btn btn-success colorVerde">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button type="button" @click="imprimirPDF()" class="btn btn-success colorVerde">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
                        </button> 
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h1 class="centro">LISTA DE CERTIFICADOS</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    
                                    <input type="text" v-model="buscar" @keyup="listarCertificado(1,buscar)" class="form-control" placeholder="Buscar">
                                    <button type="submit" class="btn btn-primary" @click="listarCertificado(1,buscar)"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                            <!-- table-striped-->
                            <table id="tablaPrincipalCertificado" class="tabla" style="width:100%">
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th>Nro</th>
                                        <th>Creado el</th>
                                        <th>Tratamiento</th>
                                        <th>Vencimiento</th>
                                        <th>OT</th>
                                        <th>Cliente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="height:40px;" v-for="(certificado) in arrayCertificado" :key="certificado.COD_CERTIFICADO" @click="obtIDCertificado(certificado.COD_CERTIFICADO,'tablaPrincipalCertificado')">
                                        <td v-text="certificado.NRO"></td>
                                        <td v-text="certificado.CREADO"></td>
                                        <td v-text="certificado.FECHA_TRAT"></td>
                                        <td v-text="certificado.FECHA_VENC"></td>
                                        <td v-text="certificado.NRO_ORDEN"></td>
                                        <td v-text="certificado.NOMBRE+' '+certificado.AP_CLIENTE+' '+certificado.AM_CLIENTE"></td>
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
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>





            <!--Inicio del modal Agregar Certificado-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar':modalCertificado}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">REGISTRAR CERTIFICADO</h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:450px;">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-12">
                                            <label for="company"> OT | Fecha Servicio | Cliente<span style="color:red">(*)</span>
                                            </label>
                                        <div class="input-group">
                                            <input class="form-control colorBloqueado" type="text" v-model="nombreOT" placeholder="Seleccionar OT" disabled="true">
                                            <button class="btn btn-primary" title="Buscar Clientes" type="button" @click="modalOTBuscar()">
                                            <i class="fa fa-search" aria-hidden="true"></i> Buscar
                                            </button>
                                        </div>
                                        <span v-if="errorCertificado.nombreOT" class="error rojo">{{errorCertificado.nombreOT[0]}}</span>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                            <label for="company">Nombre Principal<span style="color:red">(*)</span>
                                            </label>
                                            <input class="form-control" type="text" v-model="nombreCertificadoPrin">
                                            <span v-if="errorCertificado.certificadoPrincipal" class="error rojo">{{errorCertificado.certificadoPrincipal[0]}}</span>
                                    </div>
                                    <div class="col-md-6">
                                            <label for="company"> Nombre Secundario<span style="color:red">(*)</span>
                                            </label>
                                            <input class="form-control" type="text" v-model="nombreCertificadoSec">
                                            <span v-if="errorCertificado.certificadoSecundario" class="error rojo">{{errorCertificado.certificadoSecundario[0]}}</span>
                                    </div>
                                </div>
                                 <br>
                                <div class="row">
                                    <div class="col-md-6">
                                            <label for="company">Ubicada en<span style="color:red">(*)</span>
                                            </label>
                                            <textarea name="textarea" class="form-control" rows="2" cols="12" v-model="direccion"></textarea>
                                            <span v-if="errorCertificado.direccion" class="error rojo">{{errorCertificado.direccion[0]}}</span>
                                    </div>
                                    <div class="col-md-6">
                                            <label for="company">Se ah realizado el servicio de<span style="color:red">(*)</span>
                                            </label>
                                            <textarea name="textarea" class="form-control" rows="2" cols="12" v-model="descripcion"></textarea>
                                            <span v-if="errorCertificado.descripcion" class="error rojo">{{errorCertificado.descripcion[0]}}</span>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="company">Quimicos Utilizados<span style="color:red">(*)</span>
                                        </label>
                                        <select class="form-control" autocomplete v-model="quimicos"  placeholder="Seleccione una Opcion">
                                            <option value="1">OPCION 1</option>
                                            <!--Los quimicos utilizados tanto para el control de Plagas Comunes(Pyretroides Sintetico) como para el de Roedores(Cebo, Parafinado Anticuagulante) son de baja toxicidad para el ser humano y otros animales. -->
                                        </select>
                                        <span v-if="errorCertificado.quimicos" class="error rojo">{{errorCertificado.quimicos[0]}}</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-success colorVerde" @click="registrarCertificado()">
                              <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->


            <div class="modal fade" tabindex="-1" :class="{'mostrar':modalOT}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-success modal-lg" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Listado de OT</h4>
                            <button type="button" class="close" @click="cerrarModalOT()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:500px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" v-model="buscarOT" autofocus   class="form-control" placeholder="Buscar OT" @keyup="listarOT(1,buscarOT)">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <table class="table table-bordered table-striped table-hover table-sm" id="tablaCliente">
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th style="width:40px;"></th>
                                        <th>OT</th>
                                        <th>FECHA SERVICIO</th>
                                        <th>CLIENTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(certificado) in arrayOT" :key="certificado.COD_ORDEN_TRABAJO" >
                                        <td>
                                            <button type="button" @click="seleccionarOT(certificado)" class="btn btn-primary">
                                            <i class="fa fa-plus"></i>
                                            </button> &nbsp;
                                        </td>
                                        <td v-text="certificado.NRO_ORDEN"></td>
                                        <td v-text="certificado.FECHA_SERVICIO"></td>
                                        <td v-text="certificado.NOMBRE+' '+certificado.AP_CLIENTE+' '+certificado.AM_CLIENTE"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="paginationOT.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaOT(paginationOT.current_page - 1,buscarOT)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumberOT" :key="page" :class="[page == isActivedOT ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaOT(page,buscarOT)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="paginationOT.current_page < paginationOT.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPaginaOT(paginationOT.current_page + 1,buscarOT)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModalOT()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-success colorVerde" @click="registrarCertificado()">
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
                idCertificado:0,
                modalCertificado:0,
                arrayCertificado:[],
                arrayOT:[],
                errorCertificado:[],
                nombreCertificadoPrin:'',
                nombreCertificadoSec:'',
                direccion:'',
                descripcion:'',
                quimicos:1,
                codCliente:'',
                //OT
                nombreOT:'',
                codOT:0,
                modalOT:0,
                fechaOT:'',
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
                buscarOT:'',
                paginationOT : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
            
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

            isActivedOT: function(){
                return this.paginationOT.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumberOT: function() {
                if(!this.paginationOT.to) {
                    return [];
                }
                
                var from = this.paginationOT.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.paginationOT.last_page){
                    to = this.paginationOT.last_page;
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
            obtIDCertificado(idCertificado,id){
                this.idCertificado = idCertificado;
                this.cambiarColorFila(id);
            },
            cambiarColorFila(id)
            {   
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
            imprimirPDF(){
                  if(this.idCertificado>0){
                    //http://solucionesintecruz.com.bo/MIP/TESTAPP/servicesAjax/PDFcertificadoV.2.php?op=1&codigo=1293
                    //http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo=
                    var pagina = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/PDFcertificadoV.3.php?op=1&codigo="+ this.idCertificado;
                    var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                    window.open(pagina,"",opciones);
                    this.idCertificado=0;
                }
                else
                swal("Mensaje", "Debe seleccionar una OT.", "warning");

            },
            listarCertificado(page,buscar){
            
                let me =this;
                var url = '/Certificado/listar?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp =  reponse.data;
                    me.arrayCertificado = resp.certificado.data;
                    me.pagination = resp.pagination;
                })
                .catch(function(error){
                    console.log(error);
                });
            },

            listarOT(page,buscar){
                let me =this;
                var url = '/Certificado/listadoOTCertificado?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp =  reponse.data;
                    me.arrayOT = resp.OT.data;
                    me.paginationOT = resp.pagination;
                })
                .catch(function(error){
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarCertificado(page,buscar);
            },
            cambiarPaginaOT(page,buscar){
                let me = this;
                //Actualiza la página actual
                me.paginationOT.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarOT(page,buscar);
            },
            registrarCertificado(){
                    var fecha = this.fechaOT;
                    var new_date = moment(fecha, "YYYY-MM-DD");

                    var fechaVencimiento = new_date.add(90, 'days').format('YYYY-MM-DD');
           
                let me = this;
                    axios.post('/Certificado/registrar',{
                        'codOT':this.codOT,
                        'certificadoPrincipal':this.nombreCertificadoPrin,
                        'certificadoSecundario':this.nombreCertificadoSec,
                        'quimicos':this.quimicos,
                        'fechaServicio':this.fechaOT,
                        'fechaVencimiento':fechaVencimiento,
                        'descripcion':this.descripcion,
                        'nombreOT':this.nombreOT,
                        'direccion':this.direccion,
                        'codCliente':this.codCliente,
                      
                        }).then(function (reponse){ 
                            
                            me.listarOT(1,'');   
                            me.cerrarModal();   
                            me.limpiar();       
                            swal("Certificado Registrado!", "Guardado Correctamente.", "success");
                            me.listarCertificado(1,'');      
                            

                            //var pagina = '../servicesAjax/PDFcertificadoV.2.php?op=1&codigo=' + Ext.dsdata.storeCertificado.getAt(indice).get('codigo');
                            var pagina = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/PDFcertificadoV.3.php?codigo="+ reponse.data;
							var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                            window.open(pagina,"",opciones);
                          
                        })
                        .catch(function(error){
                            if(error.response.status==422){
                            me.errorCertificado  = error.response.data.errors;
                            }
                        });
                    
            },
            modalOTBuscar(){
                this.modalOT=1; 
            },
            seleccionarOT(ot){
                this.nombreOT = ot.NOMBRE+" "+ot.AP_CLIENTE+" "+ot.AM_CLIENTE;
                this.modalOT=0;
                this.codOT=ot.COD_ORDEN_TRABAJO;
                this.fechaOT = ot.FECHA_SERVICIO;
                this.nombreCertificadoPrin = ot.C_NOMB_P;
                this.nombreCertificadoSec = ot.C_NOMB_S;
                this.direccion = ot.C_DIRECCION;
                this.codCliente = ot.COD_CLIENTE;
            },
            limpiar(){
                this.nombreCertificadoPrin='';
                this.nombreCertificadoSec='';
                this.direccion='';
                this.descripcion='';
                this.quimicos='';
                this.nombreOT='';
                this.errorCertificado=[];
                this.buscarOT='';
            },
            abrirModal(){
                this.modalCertificado=1;
            },
            cerrarModal(){
                this.modalCertificado=0;
                this.limpiar();
            },
            cerrarModalOT(){
                this.modalOT=0;
                this.buscarOT='';
            }
        },
        mounted() {
            this.listarCertificado(1,this.buscar);
            this.listarOT(1,this.buscarOT);
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
    .rojo{
        color: crimson;
    }
    .colorVerde {
    background-color:green;
    }
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .modal-body {
        position: relative;
        padding: 20px;
        height: 600px;
        overflow-y: scroll;
    }
   
    .selected{
        background-color: red;
    }
    .resetColorFila{
        background-color:aquamarine;
    }
</style>
