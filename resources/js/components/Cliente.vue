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
                                <h1 class="centro">LISTA DE CLIENTES</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    
                                    <input type="text" v-model="buscar" @keyup="listarCliente(1,buscar)" class="form-control" placeholder="Buscar">
                                    <button type="submit" class="btn btn-primary" @click="listarCliente(1,buscar)"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                            <!-- table-striped-->
                            <table id="tablaPrincipalCliente" class="tabla" style="width:100%">
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th style="width:50px;"></th>
                                        <th style="width:50px;"></th>
                                        <th>Nombre</th>
                                        <th>Categoria</th>
                                        <th>Telefono</th>
                                        <th>Correo</th>
                                        <th>Direccion</th>
                                        <th>Razon Social</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- <tr v-for="(cliente,index) in arrayCliente" :key="cliente.COD_CLIENTE" @click="selectedRow(index)"> -->
                                    <tr v-for="(cliente) in arrayCliente" :key="cliente.COD_CLIENTE" @click="cambiarColorFila('tablaPrincipalCliente')">
                                        <td>
                                            <button type="button" @click="abrirModal('actualizar',cliente)" class="btn btn-warning">
                                            <i class="icon-pencil"></i>
                                            </button> &nbsp;
                                        </td>
                                        <td v-if="cliente.ACTIVO">
                                            <button type="button" class="btn btn-danger colorRojo" @click="abrirModal('desactivar',cliente)">
                                            <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                        <td v-else>
                                            <button type="button" class="btn btn-success colorVerde"  @click="abrirModal('activar',cliente)">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </td>
                                        <td v-if="cliente.AM_CLIENTE==null" v-text="cliente.NOMBRE+' '+cliente.AP_CLIENTE"></td>
                                        <td v-else v-text="cliente.NOMBRE+' '+cliente.AP_CLIENTE+' '+cliente.AM_CLIENTE"></td>

                                        <td v-if="cliente.CATEGORIA=='F'">Fijo</td>
                                        <td v-else>Eventual</td>

                                        <td v-text="cliente.TELEFONO"></td>
                                        <td v-text="cliente.CORREO"></td>
                                        <td v-text="cliente.DIRECCION"></td>
                                        <td v-text="cliente.RAZON_SOCIAL"></td>

                                        <td>
                                            <div v-if="cliente.ACTIVO">
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
                <div class="modal-dialog modal-success modal-lg" role="document" >
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Nombre <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="nombre">
                                            <span v-if="errorCliente.nombre" class="error rojo">{{errorCliente.nombre[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Paterno <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="apellidoPaterno">
                                            <span v-if="errorCliente.apellidoPaterno" class="error rojo">{{errorCliente.apellidoPaterno[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Apellido Materno</label>
                                            <input type="text" class="form-control" v-model="apellidoMaterno">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Nit </label>
                                            <input type="number" class="form-control" v-model="nit">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Razon Social</label>
                                            <input type="text" class="form-control" v-model="razonSocial">
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Correo</label>
                                            <input type="email" class="form-control" v-model="correo">
                                            <span v-if="errorCliente.correo" class="error rojo">{{errorCliente.correo[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Telefono <span style="color:red">(*)</span></label>
                                            <input type="number" class="form-control" v-model="telefono">
                                            <span v-if="errorCliente.telefono" class="error rojo">{{errorCliente.telefono[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Contacto <span style="color:red">(*)</span></label>
                                            <input type="text" class="form-control" v-model="contacto">
                                            <span v-if="errorCliente.contacto" class="error rojo">{{errorCliente.contacto[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Tipo Cliente <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-html="listadoTipoCliente" v-model="tipoCliente">
                                            </select>
                                            <span v-if="errorCliente.tipoCliente" class="error rojo">{{errorCliente.tipoCliente[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Categoria Cliente <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="categoria">
                                                <option value="E">EVENTUAL</option>
                                                <option value="F">FIJO</option>
                                            </select>
                                            <span v-if="errorCliente.categoria" class="error rojo">{{errorCliente.categoria[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Frecuencia del Servicio <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-model="frecuencia">
                                                <option value="M">MENSUAL</option>
                                                <option value="B">BIMESTRAL</option>
                                                <option value="T">TRIMESTRAL</option>
                                                <option value="S">SEMESTRAL</option>
                                                <option value="A">ANUAL</option>
                                            </select>
                                            <span v-if="errorCliente.frecuencia" class="error rojo">{{errorCliente.frecuencia[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Ciudad <span style="color:red">(*)</span></label>
                                            
                                            <select class="form-control" v-html="listadoCiudad" v-model="ciudad">
                                            </select>
                                            <span v-if="errorCliente.ciudad" class="error rojo">{{errorCliente.ciudad[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="company">Direccion</label>
                                            <input type="text" class="form-control" v-model="direccion">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Ejecutivo <span style="color:red">(*)</span></label>
                                            <select class="form-control" v-html="listadoEjecutivo" v-model="ejecutivo">
                                            </select>
                                            <span v-if="errorCliente.ejecutivo" class="error rojo">{{errorCliente.ejecutivo[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <input type="checkbox" @click="certificado()"> Datos del Certificado
                                            </div>
                                            <div v-if="clickCertificado">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="company">Nombre Principal<span style="color:red">(*)</span></label>
                                                                <input type="text" class="form-control" v-model="nombrePrincipalCertificado">
                                                                <span v-if="errorCliente.nombrePrincipalCertificado" class="error rojo">{{errorCliente.nombrePrincipalCertificado[0]}}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="company">Nombre Secundario<span style="color:red">(*)</span></label>
                                                                <input type="text" class="form-control" v-model="nombreSecundarioCertificado">
                                                                <span v-if="errorCliente.nombreSecundarioCertificado" class="error rojo">{{errorCliente.nombreSecundarioCertificado[0]}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="company">Direccion<span style="color:red">(*)</span></label>
                                                                <textarea name="textarea" class="form-control" rows="2" cols="12" v-model="DireccionCertificado"></textarea>
                                                                <span v-if="errorCliente.DireccionCertificado" class="error rojo">{{errorCliente.DireccionCertificado[0]}}</span>
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
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo">
                              <i class="fa fa-arrow-circle-left"></i> Cancelar
                            </button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde" @click="registrarCliente()">
                              <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                            <button type="button" v-else class="btn btn-success colorVerde" @click="modificarCliente()">
                              <i class="fa fa-floppy-o"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->


            <div class="modal fade" :class="{'mostrar':modalParametros}" aria-hidden="true">
                <div class="modal-dialog modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" style="height:100px;">
                            <div>
                                <h4 v-text="tituloMensaje"></h4>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde" @click="cambiarEstado(0,'Desactivado')">
                                <i class="fa fa-times"></i> Desactivar
                            </button>
                            <button type="button" v-if="tipoAccion==2"  class="btn btn-success colorVerde" @click="cambiarEstado(1,'Activado')">
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
                idCliente:"",
                nombre:"",
                apellidoPaterno:"",
                apellidoMaterno:"",
                nit:"",
                razonSocial:"",
                correo:"",
                telefono:"",
                contacto:"",
                tipoCliente:"",
                categoria:"",
                frecuencia:"",
                ciudad:"",
                direccion:"",
                ejecutivo:"",

                clickCertificado:0,
                nombrePrincipalCertificado:"",
                nombreSecundarioCertificado:"",
                DireccionCertificado:"",
                errorCliente:[],

                arrayCliente :[],
                arrayTipoCliente:[],
                listadoTipoCliente:"",
                arrayCiudad:[],
                listadoCiudad:"",
                arrayEjecutivo:[],
                listadoEjecutivo:"",

                modal: 0,
                tituloModal:'',
                tipoAccion:0,
                modalParametros:0,
                tituloMensaje:'',   
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
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
             selectedRow(index){
               //tablaCliente
               var index,
                    table = document.getElementById("tablaCliente");
               for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         // remove the background from the previous selected row
                        if(typeof index !== undefined){
                           table.rows[index].classList.toggle("selected");
                        }
                
                        // get the selected row index
                        index = this.rowIndex;
                        // add class selected to the row
                        this.classList.toggle("selected");
                     
                     };
                }
                
            },
            certificado(){
                if(this.clickCertificado==0)
                    this.clickCertificado=1;
                else
                    this.clickCertificado=0;
            },
            cambiarEstado(estado,titulo){
                let me = this;
                axios.put('/cliente/cambiarEstado',{
                    'idCliente' : this.idCliente,
                    'estado' : estado,
                }).then(function (response) {
                    me.listarCliente(1,'');
                    me.cerrarModal();
                    swal("Cliente "+titulo+"!", ""+titulo+" Correctamente.", "success");
                }).catch(function (error) {
                    console.log(error);
                });
            },
            modificarCliente(){
                let me = this;
                axios.put('/cliente/modificar',{
                    'idCliente' : this.idCliente,
                    'apellidoPaterno' : this.apellidoPaterno,
                    'nombre' : this.nombre,
                    'apellidoMaterno' : this.apellidoMaterno,
                    'nit' : this.nit,
                    'razonSocial' : this.razonSocial,
                    'correo' : this.correo,
                    'telefono' : this.telefono,
                    'contacto' : this.contacto,
                    'tipoCliente' : this.tipoCliente,
                    'categoria' : this.categoria,
                    'frecuencia' : this.frecuencia,
                    'ciudad' : this.ciudad,
                    'direccion' : this.direccion,
                    'ejecutivo' : this.ejecutivo,
                    'clickCertificado':this.clickCertificado,
                    'nombrePrincipalCertificado':this.nombrePrincipalCertificado,
                    'nombreSecundarioCertificado':this.nombreSecundarioCertificado,
                    'DireccionCertificado':this.DireccionCertificado
                    
                }).then(function (response) {
                    me.cerrarModal();
                    swal("Cliente Actualizado!", "Actualizado Correctamente.", "success");
                    me.listarCliente(1,'');
                }).catch(function (error) {
                    if(error.response.status==422)
                    {
                            me.errorCliente  = error.response.data.errors;
                    }else
                    swal("No se Actualizo!", "Hubo un problema la Actualizar.", "error");
                });
            },
            limpiar(){
                this.nombre="";
                this.apellidoPaterno="";
                this.apellidoMaterno="";
                this.nit="";
                this.razonSocial="";
                this.correo="";
                this.telefono="";
                this.contacto="";
                this.tipoCliente="";
                this.categoria="";
                this.frecuencia="";
                this.ciudad="";
                this.direccion="";
                this.ejecutivo="";
                this.errorCliente=[];
            },
            registrarCliente(){
               
                let me = this;
                    axios.post('/cliente/registrar',{
                            'nombre':this.nombre,
                            'apellidoPaterno':this.apellidoPaterno,
                            'apellidoMaterno':this.apellidoMaterno,
                            'nit':this.nit,
                            'razonSocial':this.razonSocial,
                            "correo":this.correo,
                            'telefono':this.telefono,
                            'contacto':this.contacto,
                            'tipoCliente':this.tipoCliente,
                            "categoria":this.categoria,
                            "frecuencia":this.frecuencia,
                            "ciudad":this.ciudad,
                            "direccion":this.direccion,
                            "ejecutivo":this.ejecutivo,
                            'clickCertificado':this.clickCertificado,
                            'nombrePrincipalCertificado':this.nombrePrincipalCertificado,
                            'nombreSecundarioCertificado':this.nombreSecundarioCertificado,
                            'DireccionCertificado':this.DireccionCertificado
                        }).then(function (reponse){    
                            me.cerrarModal();   
                            me.limpiar();       
                            swal("Cliente Registrado!", "Guardado Correctamente.", "success");
                            me.listarCliente(1,'');               
                        })
                        .catch(function(error){
                            if(error.response.status==422){
                            me.errorCliente  = error.response.data.errors;
                            }
                        });
            },
            listarCliente(page,buscar){
                let me =this;
                var url = '/Mip/Cliente/listado?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp =  reponse.data;
                    me.arrayCliente = resp.cliente.data;
                    me.pagination = resp.pagination;
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
                me.listarCliente (page,buscar);
            },
            listaTipoCliente() {
            let me = this;
            axios .get("/Mip/Cliente/listaTipoCliente")
                .then(function(reponse) {
                me.arrayTipoCliente = reponse.data;
                for (var i = 0; i < me.arrayTipoCliente.length; i++) {
                    me.listadoTipoCliente +='<option value="' +me.arrayTipoCliente[i].COD_TIPO_CLIENTE +'">' +me.arrayTipoCliente[i].DESCRIPCION +"</option>";
                }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            listaEjecutivo() {
            let me = this;
            axios .get("/Mip/Cliente/ejecutivo")
                .then(function(reponse) {
                me.arrayEjecutivo = reponse.data;
                me.listadoEjecutivo="<option value='0'>TODOS LOS EJECUTIVOS DE VENTAS</option>";
                for (var i = 0; i < me.arrayEjecutivo.length; i++) {
                    me.listadoEjecutivo +='<option value="' +me.arrayEjecutivo[i].COD_PERSONAL +'">' +me.arrayEjecutivo[i].NOMBRE +' '+me.arrayEjecutivo[i].AP_PATERNO+' '+me.arrayEjecutivo[i].AP_MATERNO+"</option>";
                }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            listaCiudad() {
            let me = this;
            axios .get("/Mip/Cliente/ciudad")
                .then(function(reponse) {
                me.arrayCiudad = reponse.data;
                for (var i = 0; i < me.arrayCiudad.length; i++) {
                    me.listadoCiudad +='<option value="' +me.arrayCiudad[i].COD_CIUDAD +'">' +me.arrayCiudad[i].DESCRIPCION +"</option>";
                }
                })
                .catch(function(error) {
                console.log(error);
                });
            },
            abrirModal(accion, data = []){
                this.listaTipoCliente();
                this.listaCiudad();
                this.listaEjecutivo();
                switch(accion)
                {
                    case "registrar":
                    { 
                        this.modal=1;
                        this.tituloModal='REGISTRAR CLIENTE';
                        this.tipoAccion=1;
                        break;
                    }
                    case "actualizar":{
                        this.idCliente=data.COD_CLIENTE;
                        this.nombre=data.NOMBRE;
                        this.apellidoPaterno=data.AP_CLIENTE;
                        this.apellidoMaterno=data.AM_CLIENTE;
                        this.nit=data.CI_NIT;
                        this.razonSocial=data.RAZON_SOCIAL;
                        this.correo=data.CORREO;
                        this.telefono=data.TELEFONO;
                        this.contacto=data.CONTACTO;
                        this.tipoCliente=data.COD_TIPO_CLIENTE;
                        this.categoria=data.CATEGORIA;
                        this.frecuencia=data.FRECUENCIA_SERVICIO;
                        this.ciudad=data.CIUDAD;
                        this.direccion=data.DIRECCION;
                        this.ejecutivo=data.COD_EJECUTIVO;
                        this.nombrePrincipalCertificado=data.C_NOMB_P;
                        this.nombreSecundarioCertificado=data.C_NOMB_S,
                        this.DireccionCertificado=data.C_DIRECCION,
                        this.modal=1;
                        this.tituloModal='ACTUALIZAR CLIENTE';
                        this.tipoAccion=2;
                        break;
                    }
                    case "desactivar":
                    {   
                        this.idCliente=data.COD_CLIENTE;
                        this.modalParametros=1;
                        this.tituloModal='DESACTIVAR CLIENTE';
                        this.tipoAccion=1;
                        this.tituloMensaje="Estas seguro de Desactivar el Estado?";
                        break;
                    }
                    case "activar":
                    { 
                        this.idCliente=data.COD_CLIENTE;
                        this.modalParametros=1;
                        this.tituloModal='ACTIVAR CLIENTE';
                        this.tipoAccion=2;
                        this.tituloMensaje="Estas seguro de Activar el Estado?";
                        break;
                    }
                }
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.descripcion='';
                this.modalParametros=0;
                this.limpiar();
                this.errorCliente=[];
                this.clickCertificado=0;
            },
            cambiarColorFila(id)
            {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            },
        },
        mounted() {
            
            this.listarCliente(1,this.buscar);
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
