  <template>
      <main class="main">
          <ol class=""> </ol>
          <div class="container-fluid">
              <div class="card">
                  <div class="card-body">
                      <div class="form-group row">
                      <div class="col-md-12">
                          <h1 class="centro">REPORTE DE RESERVA OPERACIONES1</h1>
                      </div>
                      </div>
                      <div class="form-group row">
                        <button @click="tableToExcel('table', 'Reporte Reserva')" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</button>

                        <!--
                          <button @click="imprimirEXCEL()" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel 2 </button>
                          -->
                      <div class="col-md-3">
                          <div class="input-group">            
                              <input class="form-control" type="date" v-model="fechaReserva" @change="filtrarFechaTecnicoReserva()"  placeholder="Filtro de Fecha">
                          </div>
                      </div> 
                      </div>
                      <div class="row" style="height:500px;overflow-x:auto;">
                          <div class="col-md-12" style="padding: 0px;width:100px;">
                            <!--table table-responsive-sm table-bordered table-striped table-hover -->
                              <table ref="table" class="tablaReserva" border="1" style="width:100%">
                              <thead class="color">
                                  <tr>
                                    <th class="encabezadoFijo centro" v-for="tecnico in vectorTecnico" :key="tecnico.COD_PERSONAL" >
                                      <!--<input type="checkbox">--><br> {{tecnico.NOMBRE +' '+tecnico.AP_PATERNO+' '+tecnico.AP_MATERNO}}
                                    </th>
                                  </tr>
                                  
                              </thead>
                              <tbody v-html="filaBodyReserva"></tbody>
                              </table>

                          </div>
                        
                      </div>


                  </div>
              </div>
          </div>
      </main>
  </template>








<script>
export default {
  
  data() {
    
    return {
      ////////////////////// EXCEL ////////////////////////////////////
      uri :'data:application/vnd.ms-excel;base64,',
      template:'<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
      base64: function(s){ return window.btoa(unescape(encodeURIComponent(s))) },
      format: function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) },
      codPersonal:0,
      mensajeError:"Error de conexion de con el servidor, comunicarse con Sistemas, Error ",

      ////////////////////////////////////// - FILTRO -///////////////////////////////////////////////////////
      fechaInicio:'',
      fechaFin:'',
      ////////////////////////////////////// - RESERVAS -///////////////////////////////////////////////////////
      arrayReserva:[],
      listado:1,
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


      vectorHorario: [
        { horario: '00:00', value:'00:00:00'},
        { horario: '00:30', value:'00:30:00'},
        { horario: '01:00', value:'01:00:00'},
        { horario: '01:30', value:'01:30:00'},
        { horario: '02:00', value:'02:00:00'},
        { horario: '02:30', value:'02:30:00'},
        { horario: '03:00', value:'03:00:00'},
        { horario: '03:30', value:'03:30:00'},
        { horario: '04:00', value:'04:00:00'},
        { horario: '04:30', value:'04:30:00'},
        { horario: '05:00', value:'05:00:00'},
        { horario: '05:30', value:'05:30:00'},
        { horario: '06:00', value:'06:00:00'},
        { horario: '06:30', value:'06:30:00'},
        { horario: '07:00', value:'07:00:00'},
        { horario: '07:30', value:'07:30:00'},
        { horario: '08:00', value:'08:00:00'},
        { horario: '08:30', value:'08:30:00'},
        { horario: '09:00', value:'09:00:00'},
        { horario: '09:30', value:'09:30:00'},
        { horario: '10:00', value:'10:00:00'},
        { horario: '10:30', value:'10:30:00'},
        { horario: '11:00', value:'11:00:00'},
        { horario: '11:30', value:'11:30:00'},
        { horario: '12:00', value:'12:00:00'},
        { horario: '12:30', value:'12:30:00'},
        { horario: '13:00', value:'13:00:00'},
        { horario: '13:30', value:'13:30:00'},
        { horario: '14:00', value:'14:00:00'},
        { horario: '14:30', value:'14:30:00'},
        { horario: '15:00', value:'15:00:00'},
        { horario: '15:30', value:'15:30:00'},
        { horario: '16:00', value:'16:00:00'},
        { horario: '16:30', value:'16:30:00'},
        { horario: '17:00', value:'17:00:00'},
        { horario: '17:30', value:'17:30:00'},
        { horario: '18:00', value:'18:00:00'},
        { horario: '18:30', value:'18:30:00'},
        { horario: '19:00', value:'19:00:00'},
        { horario: '19:30', value:'19:30:00'},
        { horario: '20:00', value:'20:00:00'},
        { horario: '20:30', value:'20:30:00'},
        { horario: '21:00', value:'21:00:00'},
        { horario: '21:30', value:'21:30:00'},
        { horario: '22:00', value:'22:00:00'},
        { horario: '22:30', value:'22:30:00'},
        { horario: '23:00', value:'23:00:00'},
        { horario: '23:30', value:'23:30:00'},
      ],


    vectorTecnico:[],
    listadoDetalleReserva:[],
    vectorDatosNuevos:[],
    filaBodyReserva:'',
    fechaReserva:''
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
            isActivedCliente: function(){
                return this.paginationCliente.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumberCliente: function() {
                if(!this.paginationCliente.to) {
                    return [];
                }
                
                var from = this.paginationCliente.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.paginationCliente.last_page){
                    to = this.paginationCliente.last_page;
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
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - CONTROL DE EXEPCIONES -///////////////////////////////////////////////////////    
    imprimirEXCEL(){
                //location.href = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/DSExcelOrdenTrabajoTodoEjecutivo.php?buscar=&fechai=2019-05-14&fechaf=2019-05-14";
                     location.href = "../../../reporteReserva.php";                                                                                 
            },
    listarTecnico() {
      let me = this;
      
      var url = '/tecnicos/listadoTecnicosFechaActualAdmin?fecha='+me.fechaReserva;
      
      axios.get(url)
      
        .then(function(reponse) 
        {  
          
          me.vectorTecnico = reponse.data;
          me.listarDetalleReservasFechaActual(me.vectorTecnico,me.fechaReserva);
        })
        .catch(function(error) {
          me.controlError(error);
          
        });
    },
    filtrarFechaTecnicoReserva(){
      let me = this;
      var url = '/tecnicos/listadoTecnicosFechaActualAdmin?fecha='+me.fechaReserva;
      axios.get(url)
        .then(function(reponse) {  
          me.vectorTecnico = reponse.data;
          me.listarDetalleReservasFechaActual(me.vectorTecnico,me.fechaReserva);
        })
        .catch(function(error) {
          me.controlError(error);
        });
    },
    listarDetalleReservasFechaActual(vectorTecnico,fechaReserva) 
    {
      let me = this;
      var url = '/DetalleReserva/listaDetalleReservasFechaActual?fecha='+fechaReserva;
      axios.get(url).then(function(reponse) {
           me.listadoDetalleReserva = reponse.data;
           me.listadoDetalleReserva = me.obtNuevosDatos(reponse.data,vectorTecnico);
 

           me.filaBodyReserva = me.bodyFila1(vectorTecnico,me.listadoDetalleReserva);
           
        })
        .catch(function(error) {
          me.controlError(error);
        });   
    },
    obtPosicionVector(horaInicio)
    {
      var posicion;
        for (var i = 0; i < this.vectorHorario.length; i++)
        {
            if(this.vectorHorario[i].value==horaInicio){
                posicion=i;
            }
        }
        return posicion;
    }
    ,
    obtPosicionVector(horaInicio)
    {
      var posicion;
        for (var i = 0; i < this.vectorHorario.length; i++)
        {
            if(this.vectorHorario[i].value==horaInicio){
                posicion=i;
            }
        }
        return posicion;
    }
    ,
    obtNuevosDatos(listadoDetalleReserva,vectorTecnico)
    {
      var vector=[];
      
        for (var z = 0; z < vectorTecnico.length; z++)
        {
            var con=0;
            if(vectorTecnico[z].activo==1)
            {
              
                
                for(var f = 0; f < vectorTecnico[z].incremento; f++)
                {
                  var fecha="FECHA: ";
                  var titulo=fecha+vectorTecnico[z].fechaFinalizacion+"\n"+
                            //"OT: "+'\n'+
                            'CLIENTE: \n';
                            //'DIRECCION: ';
                  var obj = new Object();
                  obj.AP_MATERNO = vectorTecnico[z].AP_MATERNO;
                  obj.AP_PATERNO = vectorTecnico[z].AP_PATERNO;
                  obj.COD_PERSONAL = vectorTecnico[z].COD_PERSONAL;
                  obj.NOMBRE = vectorTecnico[z].NOMBRE;
                  obj.codReserva = vectorTecnico[z].codReserva;
                  obj.fecha = vectorTecnico[z].fechaFinalizacion;
                  var hora = this.vectorHorario[f].horario;
                  obj.horaInicio = this.vectorHorario[f].value;
                  obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                  
                  vector.push(obj); 
                }
              
            }
        }  
        

        for (var i = 0; i < listadoDetalleReserva.length; i++)
        {
            var posicion = this.obtPosicionVector(listadoDetalleReserva[i].horaInicio);
            //posicion++;
            var tiempoServicio = listadoDetalleReserva[i].tiempoServicio;
            tiempoServicio = tiempoServicio/30;
            var tiempoTransporte = listadoDetalleReserva[i].tiempoTransporte;
            tiempoTransporte = tiempoTransporte/30;
         
            for (var v = 0; v < tiempoServicio+1; v++)
            {
              if(posicion<=47)
              {
                if(listadoDetalleReserva[i].NRO_ORDEN==null)
                {
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: SIN OT"+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                else{
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: "+listadoDetalleReserva[i].NRO_ORDEN+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                
               
                var obj = new Object();
                obj.AP_MATERNO = listadoDetalleReserva[i].AP_MATERNO;
                obj.AP_PATERNO = listadoDetalleReserva[i].AP_PATERNO;
                obj.COD_PERSONAL = listadoDetalleReserva[i].COD_PERSONAL;
                obj.NOMBRE = listadoDetalleReserva[i].NOMBRE;
                obj.codReserva = listadoDetalleReserva[i].codReserva;
                obj.fecha = listadoDetalleReserva[i].fecha;
                var hora = this.vectorHorario[posicion].horario;
                obj.horaInicio = this.vectorHorario[posicion++].value;
                
                obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';

                vector.push(obj); 
              }
              
            }
           
            for (var t = 0; t < tiempoTransporte-1; t++)
            {
              if(posicion<=47)
              {
                  if(listadoDetalleReserva[i].NRO_ORDEN==null)
                {
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: SIN OT"+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                else{
                  var titulo="FECHA: "+listadoDetalleReserva[i].fecha+"\n"+
                  "OT: "+listadoDetalleReserva[i].NRO_ORDEN+'\n'+
                  'CLIENTE: '+listadoDetalleReserva[i].C_NOMBRE+' '+listadoDetalleReserva[i].C_AP_CLIENTE+' '+listadoDetalleReserva[i].C_AM_CLIENTE+'\n'+
                  'DIRECCION: '+listadoDetalleReserva[i].DIRECCION;
                }
                  var obj = new Object();
                  obj.AP_MATERNO = listadoDetalleReserva[i].AP_MATERNO;
                  obj.AP_PATERNO = listadoDetalleReserva[i].AP_PATERNO;
                  obj.COD_PERSONAL = listadoDetalleReserva[i].COD_PERSONAL;
                  obj.NOMBRE = listadoDetalleReserva[i].NOMBRE;
                  obj.codReserva = listadoDetalleReserva[i].codReserva;
                  obj.fecha = listadoDetalleReserva[i].fecha;
                  var hora = this.vectorHorario[posicion].horario;
                  obj.horaInicio = this.vectorHorario[posicion++].value;

                  obj.tipo = '<td class="centroTransporte" title="TRANPORTE"><b>'+hora+'</b><br>'+'TRANSPORTE</td>';
                  /*
                  if(t!=tiempoTransporte-1)
                  {
                    
                    obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                  }
                  else
                  {
                    if(tiempoTransporte>1){
                        obj.tipo = '<td class="centroTransporte" title="TRANPORTE"><b>'+hora+'</b><br>'+'TRANSPORTE</td>';
                    }
                    else{
                        obj.tipo = '<td class="centroReserva" title="'+titulo+'"><b>'+hora+'</b><br>'+'RESERVADO</td>';
                    }
                    
                  }
                  */
                  vector.push(obj);
              }
               
            }

           
        }
      
        return vector;
    }
   
    ,
    bodyFila1(listaTecnico,listadoDetalleReserva) 
    {
      
     
      let fila = "";
        var ok=0;
        
        for (var v = 0; v < this.vectorHorario.length; v++) 
        {
         
            fila += '<tr style="height: 60px;">';
             //fila +='<td><button class="btn btn-square btn-success btn-block" style="color:black;font-weight: bold;width:60px;" type="button" >'+this.vectorHorario[v].horario+'</button></td>';
            
            for (var i = 0; i < listaTecnico.length; i++) 
            {
               
                var ok=0;
                for (var z = 0;z < listadoDetalleReserva.length;z++) 
                {
                    var horaInicio = listadoDetalleReserva[z].horaInicio.substring(0, 5);
                   
                    
                    var nombreTec = listaTecnico[i].NOMBRE+' '+listaTecnico[i].AP_PATERNO+' '+listaTecnico[i].AP_MATERNO;
                    var nombreTecDetReserva = listadoDetalleReserva[z].NOMBRE+' '+listadoDetalleReserva[z].AP_PATERNO+' '+listadoDetalleReserva[z].AP_MATERNO;
                    
                    if (this.vectorHorario[v].horario == horaInicio && nombreTec == nombreTecDetReserva)
                    {
                        ok=1;
                        fila +=listadoDetalleReserva[z].tipo;


                    }
                    
                }
                if(ok==0)
                fila +='<td class="centroHorario" title="SIN RESERVA"><b>'+this.vectorHorario[v].horario+'</b></td>';
                
                
            }
            fila += "</tr>";
        }
      return fila;
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
    tableToExcel(table, name){
      
      if (!table.nodeType) table = this.$refs.table
      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
      window.location.href = this.uri + this.base64(this.format(this.template, ctx))
    }
    },
  mounted() {
    this.listarTecnico();
    
  }
};
 
  
</script>
<style>

    .tablaReserva tr:nth-child(even){
        background-color: #ddd;
    }

.tablaReserva tr:hover td{
    background-color: black ;
    color: white ;
    }


</style>