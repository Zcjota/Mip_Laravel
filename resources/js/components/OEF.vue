<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="">
                
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card" v-if="panel==1">
                    <div class="card-header">
                        <button type="button" @click="cambiarPanel(2)" class="btn btn-success colorVerde">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h1 class="centro">LISTA DE OEF</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">        
                                    <input type="text"  class="form-control" placeholder="Buscar">
                                    <button type="submit" class="btn btn-primary" ><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                            <!-- table-striped-->
                            <table id="tablaPrincipalCliente" class="tabla" style="width:100%">
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th style="width:40px;"></th>
                                        <th style="width:40px;"></th>
                                        <th>Fecha</th>
                                        <th>Mes</th>
                                        <th>Gestion</th>
                                        <th>Cantidad Clientes</th>
                                        <th>Cantidad Tecnicos</th>
                                        <th>Activo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(objeto) in arrayListaPrincipal" :key="objeto.codigo" style="height:40px;">
                                        <td>
                                            <button type="button" class="btn btn-warning" @click="modificar(objeto)" title="Editar Reserva">
                                                <i class="icon-pencil"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning" @click="modificar(reserva)" title="Editar Reserva">
                                                <i class="icon-pencil"></i>
                                            </button>
                                        </td>
                                        <td v-text="objeto.fechaCreacion"></td>
                                        <td v-text="obtMes(objeto.mes)"></td>
                                        <td v-text="objeto.gestion"></td>
                                        <td v-text="objeto.cantidadClientes"></td>
                                        <td v-text="objeto.cantidadTecnicos"></td>
                                        <td v-if="objeto.activo">
                                            <span class="badge badge-success colorVerde">Activo</span>
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


                <div class="card" v-else-if="panel==2">
                    <div class="card-header">
                        <button type="button" @click="cambiarPanel(1)"   class="btn btn-danger colorRojo">
                            <i class="icon-plus"></i>&nbsp;Atras
                        </button>
                        <button type="button" class="btn btn-success colorVerde" @click="registrar()">
                            <i class="icon-plus"></i>&nbsp;Registrar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h1 class="centro">REGISTRAR OEF</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label>Mes <span style="color:red">(*)</span></label>
                                <select class="form-control" v-model="mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Gestion <span style="color:red">(*)</span></label>
                                <div class="input-group">  
                                    <select class="form-control" v-model="gestion">
                                        <option>2019</option>
                                        <option>2020</option>
                                        <option>2021</option>
                                    </select>
                                    <button type="button"   class="btn btn-primary" @click="listarDetalleReservasMes()">
                                        <i class="icon-plus"></i>&nbsp;Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                            <!-- table-striped-->
                            <table class="tabla" style="width:100%">
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Hora Inicio</th>
                                        <th>tiempo Servicio</th>
                                        <th>tiempo Tranporte</th>
                                        <th>Tecnico 1</th>
                                        <th>Tecnico 2</th>
                                        <th>Tecnico 3</th>
                                        <th>Tecnico 4</th>
                                        <th>Tecnico 5</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    
                                    <tr v-for="(objeto) in arrayListaRegistrar" :key="objeto.codigo" style="height:40px;">
                                        <td v-text="objeto.fecha"></td>
                                        <td v-text="objeto.cliente"></td>
                                        <td v-text="objeto.horaInicio"></td>
                                        <td v-text="objeto.tiempoServicio"></td>
                                        <td v-text="objeto.tiempoTransporte"></td>
                                        <td v-text="objeto.Tecnico1"></td>
                                        <td v-text="objeto.Tecnico2"></td>
                                        <td v-text="objeto.Tecnico3"></td>
                                        <td v-text="objeto.Tecnico4"></td>
                                        <td v-text="objeto.Tecnico5"></td>
                                       
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <!--
                            <table id="tablaPrincipalCliente" class="tabla" style="width:100%" v-else>
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Hora Inicio</th>
                                        <th>tiempo Servicio</th>
                                        <th>tiempo Tranporte</th>
                                        <th>Tecnico 1</th>
                                        <th>Tecnico 2</th>
                                        <th>Tecnico 3</th>
                                        <th>Tecnico 4</th>
                                        <th>Tecnico 5</th>
                                    </tr>
                                </thead>
                                <tbody >

                                </tbody>
                            </table>
                            -->
                        </div>

                    </div>
                </div>







                <div class="card" v-else>
                    <div class="card-header">
                        <button type="button" @click="cambiarPanel(1)"   class="btn btn-danger colorRojo">
                            <i class="icon-plus"></i>&nbsp;Atras
                        </button>
                        <button type="button" class="btn btn-success colorVerde" @click="registrarReserva()">
                            <i class="icon-plus"></i>&nbsp;Registrar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h1 class="centro">REGISTRAR RESERVA</h1>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label>Mes <span style="color:red">(*)</span></label>
                                <select class="form-control" v-model="mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Gestion <span style="color:red">(*)</span></label>
                                <div class="input-group">  
                                    <select class="form-control" v-model="gestion">
                                        <option>2019</option>
                                        <option>2020</option>
                                        <option>2021</option>
                                    </select>
                                    <button type="button"   class="btn btn-primary" @click="listarDetalleReservasMes()">
                                        <i class="icon-plus"></i>&nbsp;Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div style="overflow-x:auto;">
                            <table id="tablaReserva" class="tabla" style="width:100%" >
                                <thead>
                                    <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                                        <th class="ancho">Fecha</th>
                                        <th>Cliente</th>
                                        <th>Hora</th>
                                        <th>Servicio</th>
                                        <th>Tranporte</th>
                                        <th>Tecnico 1</th>
                                        <th>Tecnico 2</th>
                                        <th>Tecnico 3</th>
                                        <th>Tecnico 4</th>
                                        <th>Tecnico 5</th>
                                    </tr> 
                                </thead>
                                <tbody v-html="htmlEditar">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
     


        </main>
</template>

<script>
    export default {
        data(){
            return {
                panel:1,
                arrayListaPrincipal:[],
                arrayLista:[],
                arrayTecnicos:[],
                arrayListaRegistrar:[],
                camposObligatorios:[],
                arrayTecnicos:[],
                htmlEditar:'',
                htmlCombo:'',
                mes:0,
                gestion:0,
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
             cambiarPanel(panel){
                 this.panel=panel;
             },
            listarDetalleReservasMes() 
            {
                let me = this;
                // $.blockUI();
                var url = '/oef/listar?mes='+me.mes+" &gestion="+me.gestion;
                axios.get(url).then(function(reponse) 
                {
                    me.arrayLista = reponse.data;
                    me.listarTecnicosReservaMes();
                })
                .catch(function(error) {
                    // $.unblockUI();
                    me.controlError(error);
                });   
            },
            listarTecnicosReservaMes() 
            {
                let me = this;
                var url = '/oef/listarTecnicosReserva?mes='+me.mes+" &gestion="+me.gestion;
                axios.get(url).then(function(reponse) 
                {
                    me.arrayTecnicos = reponse.data;
                    me.construirTabla();
                })
                .catch(function(error) {
                    // $.unblockUI();
                    me.controlError(error);
                });   
            },
            construirTabla(){
                let me = this;
                for (var i = 0; i < me.arrayLista.length; i++) 
                {
                    var miObjeto = new Object();

                    miObjeto.codTecnico1=null;
                    miObjeto.Tecnico1=null;
                    miObjeto.codTecnico2=null;
                    miObjeto.Tecnico2=null;
                    miObjeto.codTecnico3=null;
                    miObjeto.Tecnico3=null;
                    miObjeto.codTecnico4=null;
                    miObjeto.Tecnico4=null;
                    miObjeto.codTecnico5=null;
                    miObjeto.Tecnico5=null;
                    miObjeto.codigo=i+1;
                    miObjeto.fecha=me.arrayLista[i].fecha;
                    miObjeto.codCliente=me.arrayLista[i].codCliente;
                    miObjeto.cliente=me.arrayLista[i].cliente;
                    miObjeto.horaInicio=me.arrayLista[i].horaInicio;
                    miObjeto.tiempoServicio=me.arrayLista[i].tiempoServicio;
                    miObjeto.tiempoTransporte=me.arrayLista[i].tiempoTransporte;

                    var con=0;
                    for (var v = 0; v < me.arrayTecnicos.length; v++) 
                    {
                        if(me.arrayLista[i].codReserva==me.arrayTecnicos[v].codReserva)
                        {
                            con++;

                            switch(con){
                                case 1: 
                                    miObjeto.codTecnico1=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico1=me.arrayTecnicos[v].tecnico;
                                    break;
                                case 2: 
                                    miObjeto.codTecnico2=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico2=me.arrayTecnicos[v].tecnico;
                                    break;
                                case 3: 
                                    miObjeto.codTecnico2=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico2=me.arrayTecnicos[v].tecnico;
                                    break;
                                case 4: 
                                    miObjeto.codTecnico3=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico3=me.arrayTecnicos[v].tecnico;
                                    break;
                                case 5: 
                                    miObjeto.codTecnico4=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico4=me.arrayTecnicos[v].tecnico;
                                    break;
                                case 6: 
                                    miObjeto.codTecnico5=me.arrayTecnicos[v].codTrabajador;
                                    miObjeto.Tecnico5=me.arrayTecnicos[v].tecnico;
                                    break;
                            }
                        }
                    }

                    me.arrayListaRegistrar.push(miObjeto);
                }  
                // $.unblockUI();
                
            },
            registrar(){
                let me = this;
                // $.blockUI();
                axios.post('/oef/registrar',{
                    'datos':me.arrayListaRegistrar,
                    'mes':me.mes,
                    'gestion':me.gestion,
                }).
                then(function(reponse) 
                {
                    me.cambiarPanel(1);
                    swal("Registrado Correctamente", "", "success");
                    me.listar('r');
                })
                .catch(function(error) {
                    // $.unblockUI();
                    //me.controlError(error);
                });  
            },
            listar(opcion) {
                let me = this;

                //if(opcion!='r')
                //  //   $.blockUI();
                var url = '/oef/listarDatos';
                axios.get(url)
                    .then(function(reponse) {  
                        // $.unblockUI();
                         var resp =  reponse.data;
                        me.arrayListaPrincipal = resp.oef.data;
                        me.pagination = resp.pagination;
                    })
                    .catch(function(error) {
                        // $.unblockUI();
                        me.controlError(error);
                    });
            },
            obtMes(mes){
                var literal='';
                switch(mes){
                    case 1: literal='Enero';break;
                    case 2: literal='Febrero';break;
                    case 3: literal='Marzo';break;
                    case 4: literal='Abril';break;
                    case 5: literal='Mayo';break;
                    case 6: literal='Junio';break;
                    case 7: literal='Julio';break;
                    case 8: literal='Agosto';break;
                    case 9: literal='Septiembre';break;
                    case 10: literal='Octubre';break;
                    case 11: literal='Noviembre';break;
                    case 12: literal='Diciembre';break;
                }

                return literal;
            },
            limpiar(){
                this.htmlEditar="";
            },
            modificar(objeto){
                let me = this;
                me.limpiar();
                me.cambiarPanel(3);
    
                // $.blockUI();
                var url = '/oef/listarDetalle?codigo='+objeto.codigo;
                axios.get(url)
                    .then(function(reponse) {  
                        // $.unblockUI();
                        var array=[];
                        array = reponse.data;

                        for (var v = 0; v < array.length; v++) 
                        {
                            me.htmlEditar+="<tr>";
                            me.htmlEditar+="<td class='ancho'><input type='date' class='form-control fecha'></input></td>";
                            me.htmlEditar+="<td><input type='text' class='codCliente anchoOculto'></input></td>";
                            me.htmlEditar+="<td class='inicio'>"+array[v].horaInicio+"</td>";
                            me.htmlEditar+="<td class='servicio'>"+array[v].tiempoServicio+"</td>";
                            me.htmlEditar+="<td class='transporte'>"+array[v].tiempoTransporte+"</td>";
                            /*
                            me.htmlEditar+="<td>"+array[v].codTecnico1+"</td>";
                            me.htmlEditar+="<td>"+array[v].codTecnico2+"</td>";
                            */
                            me.htmlEditar+="<td class='codTecnico1'>"+me.comboTecnico(array[v].codTecnico1)+"</td>";
                            me.htmlEditar+="<td class='codTecnico2'>"+me.comboTecnico(array[v].codTecnico2)+"</td>";
                            me.htmlEditar+="<td class='codTecnico3'>"+me.comboTecnico(array[v].codTecnico3)+"</td>";
                            me.htmlEditar+="<td class='codTecnico4'>"+me.comboTecnico(array[v].codTecnico4)+"</td>";
                            me.htmlEditar+="<td class='codTecnico5'>"+me.comboTecnico(array[v].codTecnico5)+"</td>";
                            me.htmlEditar+="</tr>";
                        }
                    })
                    .catch(function(error) {
                        // $.unblockUI();
                        me.controlError(error);
                    }); 
            },
            listarTecnicos() {
                let me = this;
                var url = '/tecnicos/listarOEF';
                axios.get(url)
                    .then(function(reponse) {  
                        
                        me.arrayTecnicos = reponse.data;
                       
                    })
                    .catch(function(error) {
                        me.controlError(error);
                    });
            },
            comboTecnico(codTecnico){

                var htmlCombo='<select class="form-control">';
                htmlCombo+='<option>-- Seleccione --</option>';
                for (var v = 0; v < this.arrayTecnicos.length; v++) 
                {
                    if(this.arrayTecnicos[v].COD_PERSONAL==codTecnico)
                    htmlCombo+='<option value="'+this.arrayTecnicos[v].COD_PERSONAL+'" selected>'+this.arrayTecnicos[v].NOMBRE+'</option>';
                    else
                    htmlCombo+='<option value="'+this.arrayTecnicos[v].COD_PERSONAL+'">'+this.arrayTecnicos[v].NOMBRE+'</option>';
                }
                htmlCombo+='</select>';

                return htmlCombo;
            },
            registrarReserva(){
                $('#tablaReserva tbody tr').each(function () {
                    var columna1 = $(this).find("td").eq(0);
                    var fecha = columna1.find(".fecha").val();

                    var columna2 = $(this).find("td").eq(1);
                    var fecha = columna2.find(".fecha").val();
                    alert(PorcentajeParcial);
                });
            },
            
        },
        mounted() {
            this.listar('l');
            this.listarTecnicos();
           
        }
    }
</script>

<style>
.ancho{
    width: 70px;
}

.anchoOculto{
    width: 5px;
}

</style>
