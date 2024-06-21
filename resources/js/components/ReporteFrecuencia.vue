<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class=""></ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-body">
                        <button type="button" @click="imprimirPDF()" class="btn btn-success colorVerde">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                        </button> 
                                    <div class="form-group row">
                        <div class="col-md-12">
                            <h1 class="centro">REPORTE FRECUENCIA EJECUTIVO</h1>
                            <hr>
                        </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                            <div class="input-group">
                                    <input type="text" v-model="buscar" @keyup="listaFrecuencia(1,fechaInicio,fechaFin,buscar)"  class="form-control" placeholder="Buscar">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                        <input type="date" v-model="fechaInicio" class="form-control" placeholder="Fecha Inicio">
                                        <input type="date" v-model="fechaFin" class="form-control" placeholder="Fecha Fin">
                                        <button type="submit" class="btn btn-primary" @click="listaFrecuencia(1,fechaInicio,fechaFin,buscar)"><i class="fa fa-search"></i> Buscar</button>  
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;height:500px;">
                        <table id="tablaPrincipalReporteFre" class="tabla" style="width:100%">
                            <thead>
                                <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                  
                                    <th style="width:4%">Nro</th>
                                    <th style="width:20%">Cliente</th>
                                    <th style="width:20%">Direccion</th>
                                    <th >Telefono</th>
                                    <th >Contacto</th>
                                    <th >Costo del Servicio</th>
                                    <th>Fecha Servicio</th>
                                    <th>Frecuencia Dias</th>
                                    <th>Fecha Limite F.</th>
                                    <th>Dias Pasado F.</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr style="height:40px" v-for="frecuencia in arrayFrecuencia" :key="frecuencia.COD_ORDEN_TRABAJO" @click="cambiarColorFila('tablaPrincipalReporteFre')">
                                    <td v-text="frecuencia.NRO_ORDEN"></td>
                                    

                                    <td v-if="(frecuencia.AP_CLIENTE+' '+frecuencia.AM_CLIENTE+' '+frecuencia.NOMCLIENTE).length>35" v-text="(frecuencia.AP_CLIENTE+' '+frecuencia.AM_CLIENTE+' '+frecuencia.NOMCLIENTE).substring(0, 35)+'...'"  v-bind:title="frecuencia.AP_CLIENTE+' '+frecuencia.AM_CLIENTE+' '+frecuencia.NOMCLIENTE"></td>
                                    <td v-else v-text="frecuencia.AP_CLIENTE+' '+frecuencia.AM_CLIENTE+' '+frecuencia.NOMCLIENTE"></td>

                                    <td v-if="frecuencia.DIRECCION.length>35" v-text="frecuencia.DIRECCION.substring(0, 35)+' ...'"  v-bind:title="frecuencia.DIRECCION"></td>
                                    <td v-else v-text="frecuencia.DIRECCION"></td>

                                    <td v-text="frecuencia.TELEFONO"></td>
                                    <td v-text="frecuencia.CONTACTO"></td>
                                    <td v-text="frecuencia.PRECIO"></td>
                                    
                                    <td v-text="frecuencia.FECHAS"></td>
                                    <td v-text="frecuencia.FRECUENCIA"></td>
                                    <td v-text="frecuencia.fechaFR"></td>
                                    <td v-text="frecuencia.diasPFR"></td>
                                </tr>
                            
                            </tbody>
                        </table>
                        </div>
                        <br>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="listaFrecuencia(pagination.current_page - 1,fechaInicio,fechaFin,buscar)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="listaFrecuencia(page,fechaInicio,fechaFin)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="listaFrecuencia(pagination.current_page + 1,fechaInicio,buscar)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
         

        
        </main>
</template>

<script>
    export default {
        data(){
            return {
                arrayFrecuencia:[],
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
            imprimirPDF(){
                     location.href = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/DSExcelFrecuencia.php?buscar=";
                    
            },
            
            listaFrecuencia(page,fechaInicio,fechaFin,buscar) {
  

            let me = this;
            var url = '/Mip/listaFrecuencia?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
            axios .get(url)
                .then(function(reponse) {
                     var resp =  reponse.data;
                    if(resp=="expiro"){
                        me.cerrarSesion();
                    }
                    else
                    {
                        me.arrayFrecuencia = resp.frecuencia.data;
                        me.pagination = resp.pagination;
                    }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            cambiarPagina(page,fechaInicio,fechaFin,buscar){
                let me = this;
                me.pagination.current_page = page;
                me.listaFrecuencia(page,fechaInicio,fechaFin,buscar);
            },
            cambiarColorFila(id)
            {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
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
            this.listaFrecuencia(1,this.fechaInicio,this.fechaFin,this.buscar);
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
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .colorAzul{
        background-color:#226e8a;
    }


</style>
