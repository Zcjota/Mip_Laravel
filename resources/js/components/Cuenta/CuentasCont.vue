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
                    <button type="button" @click="imprimirEXCEL()" class="btn btn-success colorVerde">
                        <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h1 class="centro">Lista De Cuentas Presupuesto</h1> 
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">

                                <input type="text" v-model="buscar" class="form-control" placeholder="Buscar">
                                <button type="submit" class="btn btn-primary"
                                    @click="listarCuentaContable(1,buscar)"><i class="fa fa-search"></i>
                                    Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <!-- table-striped-->
                        <table id="tablaPrincipalProveedorCliente" class="tabla" style="width:100%">
                            <thead>
                                <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                    <th style="width:50px;"></th>
                                    <th style="width:50px;"></th>
                                    <th>FONDO</th>
                                    <th>NRO.CUENTA</th>
                                    <th>CUENTA PRESUPUESTO</th>
                                    <th>ESTADO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr v-for="(cliente,index) in arrayCliente" :key="cliente.COD_CLIENTE" @click="selectedRow(index)"> -->
                                <tr v-for="(cuenta_contable) in arrayProveedorPersonal" :key="cuenta_contable.id"
                                    @click="cambiarColorFila('tablaPrincipalProveedorCliente')">
                                    <td>
                                        <button type="button" @click="abrirModal('actualizar',cuenta_contable)"
                                            class="btn btn-warning">
                                            <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td v-if="cuenta_contable.activo">
                                        <button type="button" class="btn btn-danger colorRojo"
                                            @click="abrirModal('desactivar',cuenta_contable)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                    <td v-else>
                                        <button type="button" class="btn btn-success colorVerde"
                                            @click="abrirModal('activar',cuenta_contable)">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                    <!-- <td v-if="cuenta_contable.tipo == 1">PERSONAL</td>
                                    <td v-else>PROVEEDOR</td> -->
                                    <td v-text="cuenta_contable.nombre_fondo"></td>
                                    <td v-text="cuenta_contable.nombre_nro_cuenta"></td>
                                    <td v-text="cuenta_contable.nombre_cuenta"></td>
                                    <!-- <td v-text="cuenta_contable.nombre_cuenta"></td>
                                    <td>{{cuenta_contable.b_sigla}}-{{cuenta_contable.bc_cuenta}}-{{cuenta_contable.bc_moneda}}
                                    </td> -->
                                    <td>
                                        <div v-if="cuenta_contable.activo == 1">
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
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page - 1,buscar)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar)"
                                    v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page + 1,buscar)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel"
            style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-success " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height:400px;">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company">Nombre <span style="color:red">(*)</span></label>
                                        <input type="text" class="form-control" style="text-transform:uppercase;"
                                            v-model="strNombre" placeholder="Nombre Cuenta Contable">
                                        <span v-if="erroProveedorPersonal.strNombre"
                                            class="error rojo">{{erroProveedorPersonal.strNombre[0]}}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company">Fondo <span style="color:red">(*)</span></label>
                                        <select class="form-control" v-model="idFondo">
                                            <option value="0" disabled>Seleccione</option>
                                            <option v-for="fondo in arrayFondo" :key="fondo.id" :value="fondo.COD_FONDO"
                                                v-text="fondo.DESCRIPCION">
                                            </option>

                                        </select>
                                        <span v-if="erroProveedorPersonal.idFondo" class="error rojo">{{
                                                    erroProveedorPersonal.idFondo[0] }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="company">Numero Cuenta <span style="color:red">(*)</span></label>
                                        <select class="form-control" v-model="idNroCuenta">
                                            <option value="0" disabled>Seleccione</option>
                                            <option v-for="nrocuenta in arrayNroCuenta" :key="nrocuenta.CODNUM"
                                                :value="nrocuenta.CODNUM" v-text="nrocuenta.DESCRIPCION"></option>

                                        </select>
                                        <span v-if="erroProveedorPersonal.idNroCuenta"
                                            class="error rojo">{{erroProveedorPersonal.idNroCuenta[0]}}</span>
                                    </div>

                                </div>
                            </div>

                            <!-- <div v-if="intTipo == 2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                Asignar Cuenta
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="company">Fondo <span
                                                                    style="color:red">(*)</span></label>
                                                            <select class="form-control" v-model="idFondo">
                                                                <option value="0" disabled>Seleccione</option>
                                                                <option v-for="fondo in arrayFondo" :key="fondo.id"
                                                                    :value="fondo.COD_FONDO" v-text="fondo.DESCRIPCION">
                                                                </option>

                                                            </select>
                                                            <span v-if="erroProveedorPersonal.idFondo"
                                                                class="error rojo">{{erroProveedorPersonal.idFondo[0]}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="company">Numero Cuenta <span
                                                                    style="color:red">(*)</span></label>
                                                            <select class="form-control" v-model="idNroCuenta">
                                                                <option value="0" disabled>Seleccione</option>
                                                                <option v-for="nrocuenta in arrayNroCuenta"
                                                                    :key="nrocuenta.CODNUM" :value="nrocuenta.CODNUM"
                                                                    v-text="nrocuenta.DESCRIPCION"></option>

                                                            </select>
                                                            <span v-if="erroProveedorPersonal.idNroCuenta"
                                                                class="error rojo">{{erroProveedorPersonal.idNroCuenta[0]}}</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="company">Cuenta <span
                                                                    style="color:red">(*)</span></label>
                                                            <select class="form-control" v-model="idCuenta">
                                                                <option value="0" disabled>Seleccione</option>
                                                                <option v-for="cuenta in arrayCuenta"
                                                                    :key="cuenta.COD_CUENTA" :value="cuenta.COD_CUENTA"
                                                                    v-text="cuenta.DESCRIPCION"></option>

                                                            </select>
                                                            <span v-if="erroProveedorPersonal.idCuenta"
                                                                class="error rojo">{{erroProveedorPersonal.idCuenta[0]}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div> -->
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <label for="company">Banco Cuenta <span style="color:red">(*)</span></label>
                                    <input type="text" class="form-control" v-model="searchTerm"
                                        @input="filterBancoCuenta" placeholder="Buscar...">
                                    <select class="form-control" v-model="cod_bc">
                                        <option value="-1" disabled>Seleccione</option>
                                        <option v-for="item in filteredBancoCuenta" :key="item.cod_bc"
                                            :value="item.cod_bc">{{ item.sigla }}-{{ item.nro_cuenta }}</option>
                                    </select>
                                </div>
                            </div> -->
                            <!-- 
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="company">Banco Cuenta <span style="color:red">(*)</span></label>
                                        <select class="form-control" v-model="cod_bc">
                                            <option value="-1" disabled>Seleccione</option>             
                                            <option v-for="item in arrayBancoCuenta" :key="item.cod_bc" :value="item.cod_bc">{{item.sigla}}-{{item.nro_cuenta}}</option>
                                        </select>
                                    </div>
                                </div> -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo">
                            <i class="fa fa-arrow-circle-left"></i> Cancelar
                        </button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde"
                            @click="registrarProveedorPersonal()">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button type="button" v-else class="btn btn-success colorVerde"
                            @click="modificarProveedorPersonal()">
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            @click="cerrarModal()">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height:100px;">
                        <div>
                            <h4 v-text="tituloMensaje"></h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cerrarModal()" class="btn btn-danger colorRojo"><i
                                class="fa fa-arrow-circle-left"></i> Cancelar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-success colorVerde"
                            @click="cambiarEstado(0,'Desactivado')">
                            <i class="fa fa-times"></i> Desactivar
                        </button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-success colorVerde"
                            @click="cambiarEstado(1,'Activado')">
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
                id:"",
                intTipo:"",
                strNombre:"",
                intFondo:"",
                intNro_cuenta:"",
                intCuenta:"",
                searchTerm: '',
                erroProveedorPersonal: [],

                arrayProveedorPersonal: [],
                idFondo: "",
                idNroCuenta: "",
                idCuenta: "",
                cod_bc: -1,
                arrayFondo:[],
                arrayNroCuenta:[],
                arrayCuenta:[],
                arrayBancoCuenta: [],
               

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
            filteredBancoCuenta() {
        if (this.searchTerm.trim() === '') {
            return this.arrayBancoCuenta;
        } else {
            return this.arrayBancoCuenta.filter(item =>
                item.sigla.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                item.nro_cuenta.includes(this.searchTerm)
            );
        }
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
            imprimirEXCEL() {
                const params = new URLSearchParams({
                    buscar: this.buscar || '', // Usa valores por defecto si no están definidos
                    // tipoMovimiento: this.tipoMovimiento || '',
                    // estado: this.estado || '',
                    // fechaInicio: this.fechaInicio || '',
                    // fechaFin: this.fechaFin || ''
                }).toString();

                const url = `/exportar-Cuentacontable?${params}`;
                window.location.href = url;
            },
            filterBancoCuenta() {
                // No es necesario hacer nada aquí, la lista filtrada se actualiza automáticamente gracias a la computación 'filteredBancoCuenta'
            },
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
           
            cambiarEstado(estado,titulo){
                let me = this;
                axios.put('/CuentaContable/cambiarEstado',{
                    'id' : this.id,
                    'estado' : estado,
                }).then(function (response) {
                    me.listarCuentaContable(1,'');
                    me.cerrarModal();
                    swal("CUENTA CONTABLE "+titulo+"!", ""+titulo+" Correctamente.", "success");
                }).catch(function (error) {
                    console.log(error);
                });
            },
            modificarProveedorPersonal(){
                let me = this;
                axios.put('/CuentaContable/modificar',{
                    'id' : this.id,
                    'tipo':me.intTipo,
                    'nombre':me.strNombre,
                    'nro_cuenta':me.idNroCuenta,
                    'cuenta':me.idCuenta,
                    'fondo':me.idFondo,
                    'cod_bc': me.cod_bc
    
                    
                }).then(function (response) {
                    me.cerrarModal();
                    swal("Cuenta Contable Actualizado!", "Actualizado Correctamente.", "success");
                    me.listarCuentaContable(1,'');
                }).catch(function (error) {
                    if(error.response.status==422)
                    {
                            me.erroProveedorPersonal  = error.response.data.errors;
                    }else
                    swal("No se Actualizo!", "Hubo un problema la Actualizar.", "error");
                });
            },
            limpiar(){
                this.strNombre="";
                this.intTipo="";
                this.arrayFondo=[]; 
                this.arrayCuenta=[];
                this.arrayNroCuenta=[];
                this.erroProveedorPersonal=[];
                this.searchTerm = '';
            },
            registrarProveedorPersonal(){
               
                let me = this;
                    axios.post('/CuentaContable/registrar',{
                            'tipo':me.intTipo,
                            'nombre':me.strNombre,
                            'nro_cuenta':me.idNroCuenta,
                            'cuenta':me.idCuenta,
                            'fondo':me.idFondo,
                            'cod_bc': me.cod_bc
    
                        }).then(function (reponse){    
                            me.cerrarModal();   
                            me.limpiar();       
                            swal("Cuenta Contable Registrada!", "Guardado Correctamente.", "success");
                            me.listarCuentaContable(1,'');               
                        })
                        .catch(function(error){
                            if(error.response.status==422){
                            me.erroProveedorPersonal  = error.response.data.errors;
                            }
                        });
            },
            listarCuentaContable(page,buscar){
                let me =this;
                var url = '/api/CuentaContable/listacuenta?page='+page+"&buscar="+buscar;
                axios.get(url).then(function (reponse){
                    var resp =  reponse.data;
                    me.arrayProveedorPersonal = resp.cuenta_contable.data;
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
                me.listarCuentaContable (page,buscar);
            },
            
            
            
            selectFondo(){
                 let me = this;
                    axios .get("/api/CuentaContable/Fondo")
                    .then(function(reponse) {
                    var respuesta= reponse.data;
                        me.arrayFondo=respuesta.fondo;
                    
                    })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            selectNroCuenta(){
                 let me = this;
                    axios .get("/api/CuentaContable/NroCuenta")
                    .then(function(reponse) {
                    var respuesta= reponse.data;
                        me.arrayNroCuenta=respuesta.nro_cuenta;
                    
                    })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            selectCuenta(){
                 let me = this;
                    axios .get("/api/CuentaContable/Cuenta")
                    .then(function(reponse) {
                    var respuesta= reponse.data;
                        me.arrayCuenta=respuesta.cuenta;
                    
                    })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            listDataBankAcount(){
                let me = this;
                axios.get('/api/ZBancoCuentaDestinyzprov/ListarCbxzprov').then(function (reponse){
                    me.arrayBancoCuenta = reponse.data.result;
                }).catch(function(error){
                    me.bugChecking(error);
                });
            },
            abrirModal(accion, data = []){
                this.selectFondo();
                this.selectNroCuenta();
                this.selectCuenta();
                this.listDataBankAcount();
                switch(accion)
                {
                    case "registrar":
                    { 
                        this.modal=1;
                        this.tituloModal='REGISTRAR CUENTA CONTABLE';
                        this.tipoAccion=1;
                        this.intTipo="";
                        this.strNombre="";
                        this.idFondo="";
                        this.idCuenta="";
                        this.idNroCuenta="";
                        this.cod_bc=-1;
                       
                        break;
                    }
                    case "actualizar":{
                        
                        this.id=data.id;
                        this.strNombre=data.nombre_cuenta;
                        // this.intTipo=data.tipo;
                        this.idFondo=data.cod_fondo;
                        this.idNroCuenta=data.cod_cuentas;
                        // this.idCuenta=data.cuenta;
                        // this.cod_bc = data.cod_bc
                        
                        this.modal=1;
                        this.tituloModal='ACTUALIZAR CUENTA CONTABLE';
                        this.tipoAccion=2;
                        break;
                    }
                    case "desactivar":
                    {   
                        this.id=data.id;
                        this.modalParametros=1;
                        this.tituloModal='DESACTIVAR CUENTA CONTABLE';
                        this.tipoAccion=1;
                        this.tituloMensaje="Estas seguro de Desactivar el CUENTA CONTABLE?";
                        break;
                    }
                    case "activar":
                    { 
                        this.id=data.id;
                        this.modalParametros=1;
                        this.tituloModal='ACTIVAR CUENTA CONTABLE';
                        this.tipoAccion=2;
                        this.tituloMensaje="Estas seguro de Activar el Estado?";
                        break;
                    }
                }
            },
                
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.strNombre="";
                this.intTipo="";
                this.modalParametros=0;
                this.arrayFondo=[];
                this.arrayNroCuenta=[];
                this.arrayCuenta=[];
                this.idFondo="";
                this.idCuenta="";
                this.idNroCuenta="";
                this.cod_bc=-1;
                this.limpiar();
                this.erroProveedorPersonal=[];
                //this.clickCertificado=0;
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
            
            this.listarCuentaContable(1,this.buscar);
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
