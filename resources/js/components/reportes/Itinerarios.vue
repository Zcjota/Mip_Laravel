<template>
  <main class="main">
    <ol class>

    </ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">   
          <button type="button" @click="imprimirPDF()" class="btn btn-success colorVerde">
            <i class="fa fa-file-pdf-o"></i>&nbsp;PDF
          </button>
          <button type="button"  class="btn btn-primary" @click="imprimirExcel(fechaInicio)">
            <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
          </button>
          
        </div>

        <!--Inicio Panel Listado Reserva -->
        <template >
          <div class="card-body">
            <div class="form-group row">
              <div class="col-md-12">
                <h1 class="centro">REPORTE DE ITINERARIO DE SERVICIOS</h1>
              </div>
            </div>

             <div class="form-group row">
                 <!--
              <div class="col-md-6">
                  <div class="input-group">
                    <input type="text" v-model="buscar" @keyup="listarReservasSinCarga(1,fechaInicio,fechaFin,buscar)" class="form-control" placeholder="Buscar">
                  </div>
              </div>
              -->
              <div class="col-md-6">
                  <div class="input-group">            
                      <input type="date" v-model="fechaInicio" class="form-control" placeholder="Fecha Inicio">
                  
                      <button type="submit" class="btn btn-primary" @click="listarReservas(1,fechaInicio,fechaFin,buscar)"><i class="fa fa-search"></i> Buscar</button>
                      &nbsp;
                      <button type="button"  class="btn btn-primary" @click="limpiar()">
                            <i class="fa fa-refresh"></i>&nbsp;Recargar
                        </button>
                  </div>
              </div> 
            </div>
           
            <div style="overflow-x:auto; height:600px" >
              <table id="tablaPrincipal" class="tabla" style="width:100%">
                <thead>
                  <!--#29363d -->
                  <tr style="background-color:#243648f0;color:#ECF0F1;height:40px">
                    <th class="encabezadoFijo" style="width:70px;">Nro OT</th>
                    <th class="encabezadoFijo" style="width:130px;">Fecha</th>
                    <th class="encabezadoFijo" style="width:160px;">Creado Por</th>
                    <th class="encabezadoFijo">Cliente</th>
                    <th class="encabezadoFijo">Hora Inicio</th>
                    <th class="encabezadoFijo">Tiempo Servicio</th>
                    <th class="encabezadoFijo">Tiempo Tranporte</th>
                    <th class="encabezadoFijo">Direccion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(reserva) in arrayReserva" :key="reserva.codReserva" style="height:50px;"  @click="asignarIdOT(reserva.codOT,'tablaPrincipal')">
                    <td v-text="reserva.NRO_ORDEN"></td>
                    <td style="width:130px;" v-text="reserva.fecha"></td>
                    <td style="width:160px;" v-if="reserva.NOMB_PERSONAL!=null" v-text="reserva.NOMB_PERSONAL+' '+reserva.APP_PERSONAL"></td>
                    <td style="width:160px;" v-else ></td>
                    <td v-text="reserva.NOMBRE+' '+reserva.AP_CLIENTE+' '+reserva.AM_CLIENTE"></td>
                    <td v-text="reserva.horainicio"></td>
                    <td v-text="reserva.tiempoServicio"></td>
                    <td v-text="reserva.tiempoTransporte"></td>
                    <td v-text="reserva.DIRECCION"></td>
                  </tr>
                </tbody>
              </table>
            </div>
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
        </template>
      </div>
    </div>






    



    


  </main>
</template>

<script>
export default {
  data() {
      
    return {
      
      codPersonal:0,
      mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",

      ////////////////////////////////////// - FILTRO -///////////////////////////////////////////////////////
      fechaInicio:'',
      fechaFin:'',
      ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////
      arrayReserva:[],
      idOT:0,
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
    
    };
  }, 
  computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
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

        },
  
  methods: {
   listarReservasSinCarga(page,fechaInicio,fechaFin,buscar) 
    {
      
      if(fechaInicio!=''){
        if(fechaFin=='')
        fechaFin = fechaInicio;
      }

      if(fechaFin!=''){
        if(fechaInicio=='')
          fechaInicio = fechaFin;
      }
      
      let me = this;
      var url = 'Mip/reservas/listarReserva?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
      axios.get(url).then(function(reponse) {
          var resp =  reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    listarReservas(page,fechaInicio,fechaFin,buscar) 
    {
      
      if(fechaInicio!=''){
        if(fechaFin=='')
        fechaFin = fechaInicio;
      }

      if(fechaFin!=''){
        if(fechaInicio=='')
          fechaInicio = fechaFin;
      }
      
      let me = this;
      // $.blockUI();
      var url = 'Mip/reservas/listarReserva?page='+page+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&buscar="+buscar;
      axios.get(url).then(function(reponse) {
          // $.unblockUI();
          var resp =  reponse.data;
          me.arrayReserva = resp.reserva.data;
          me.pagination = resp.pagination;
        })
        .catch(function(error) {
          // $.unblockUI();
          me.controlError(error);
        });
    },
   cambiarPagina(page,fechaInicio,fechaFin,buscar){
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarReservas(page,fechaInicio,fechaFin,buscar);
    },
    imprimirExcel(fecha){

      location.href='http://isi-app.online/1Servicios/web/mipLaravel/ajax/reserva.php?op=excel&fecha='+fecha;
      
    },
    imprimirPDF()
    {
 
      if(this.idOT>0)
      {
        var pagina = "http://solucionesintecruz.com.bo/MIP/PLAGAS/DSforms/DSimprimirOTAjax.php?codigo="+ this.idOT;
        var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
        window.open(pagina,"",opciones);
        this.idOT=0;
      }
      else{
        if(this.idOT==null || this.idOT=="null")
        swal("Mensaje", "La Reserva no tiene una OT Vinculada.", "warning");
        else
        swal("Mensaje", "Debe seleccionar una OT.", "warning");
      }
    },
    asignarIdOT(codigo,id)
    {
        this.idOT=codigo;
    
        this.cambiarColorFila(id);

    },
    cambiarColorFila(id)
    {
        $('#'+id+' tr').click(function(e) {
            $('#'+id+' tr').removeClass('selected');
            $(this).addClass('selected');
        });
    },
    limpiar(){

        this.fechaInicio='';
        this.fechaFin='';
        this.buscar='';
        this.listarReservas(1,this.fechaInicio,this.fechaFin,this.buscar);
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
    this.listarReservas(1,this.fechaInicio,this.fechaFin,this.buscar);
  
  }
};
</script>


<style>
.centro {
  text-align: center;
}
.ancho {
  width: 50px;
}
.color {
  background-color: #20c997;
}
.rojo{
        color: crimson;
}
.colorRojo {
  background-color: rgb(197, 63, 90);
  color: white;
}
.colorAmarrillo {
  background-color: yellow;
  color: black;
}
.colorVerde {
  background-color:green;
}
.colorAzul {
  background-color:rgb(49, 83, 184);
}
.colorVerde1 {
  background-color:lightseagreen;
}
.ancho1 {
  width: 1000px;
}
.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
  background-color: #dbdbdb;
}
.bloqueado{
  background-color: #20c997;
}
.colorBorde{
  border-color: #ce0808;
}
.colorBloqueado{
  background-color: #ffebeb;
}
.encabezado{
  text-align: center;
  font-weight: bold;
}





</style>
