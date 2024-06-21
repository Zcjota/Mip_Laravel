  <template>
      <main class="main">
          <ol class=""> </ol>
          <div class="container-fluid">
              <div class="card">
                  <div class="card-body">
                      <div class="form-group row">
                      <div class="col-md-12">
                          <h1 class="centro">REPORTE DE RESERVA POR MES F2</h1>
                      </div>
                      </div>
                      <div class="form-group row">
                        
                        <!--
                          <button @click="imprimirEXCEL()" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel 2 </button>
                          -->
                      <div class="col-md-4">
                            <div class="form-group">
                                <label for="company">
                                MES <span style="color:red">(*)</span>
                                </label>
                                <div class="input-group">
                                    <select class="form-control" v-model="mes" @change="listarDias('2Carga')">
                                        <option value="1">ENERO</option>
                                        <option value="2">FEBRERO</option>
                                        <option value="3">MARZO</option>
                                        <option value="4">ABRIL</option>
                                        <option value="5">MAYO</option>
                                        <option value="6">JUNIO</option>
                                        <option value="7">JULIO</option>
                                        <option value="8">AGOSTO</option>
                                        <option value="9">SEPTIEMBRE</option>
                                        <option value="10">OCTUBRE</option>
                                        <option value="11">NOVIEMBRE</option>
                                        <option value="12">DICIEMBRE</option>
                                    </select>
                                    <!--
                                    <button @click="tableToExcel('table', 'Reporte Reserva')" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</button>
                                    -->
                                    <button @click="obtMes()" class="btn btn-primary"><i class="fa fa-refresh" aria-hidden="true"></i> Cargar</button>
                                </div>
                            </div>
                        </div>
                      </div>
                      <div class="row" style="height:500px;overflow-x:auto;">
                          <div class="col-md-12" style="padding: 0px;width:50px;">
                            <!--table table-responsive-sm table-bordered table-striped table-hover -->
                              <table ref="table" class="tablaReserva" border="1" style="width:250%">
                              <thead class="color">
                                  <tr>
                                    <th class="encabezadoFijo centro" style="width:50px">
                                      EMPRESA
                                    </th>
                                    <th class="encabezadoFijo centro" style="width:50px" v-for="(tecnico,index) in nroDias" :key="tecnico.COD_PERSONAL" >
                                      <!--<input type="checkbox">--><br> {{'Dia '+parseInt(index+1)}}
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
    vectorReservasMes:[],
    vectorDetalleReservasMes:[],
    vectorDatosNuevos:[],
    filaBodyReserva:'',
    fechaReserva:'',
    mes:'',
    nroDias:[],
    };
  },  
  methods: {
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// - CONTROL DE EXEPCIONES -///////////////////////////////////////////////////////    
    imprimirEXCEL(){
                //location.href = "http://solucionesintecruz.com.bo/MIP/PLAGAS/servicesAjax/DSExcelOrdenTrabajoTodoEjecutivo.php?buscar=&fechai=2019-05-14&fechaf=2019-05-14";
                     location.href = "../../../reporteReserva.php";                                                                                 
    },
    obtMes() 
    {
        let me = this; 
        $.blockUI();
        var url = '/Reportes/obtMes';
        axios.get(url).then(function(reponse) 
        {
            var resp = reponse.data;
            me.mes = resp[0].mes;
            me.listarDias('1Carga');
        })
        .catch(function(error) {
           $.unblockUI();
          me.controlError(error);
        });   
    },listarDias(carga) {
      let me = this;
      if(carga=='2Carga')
       $.blockUI();
      var url = 'Reportes/obtDiasMes?mes='+me.mes;
      axios.get(url)
        .then(function(reponse) 
        {  
          me.nroDias = reponse.data;
          me.listarReservasMes();
        })
        .catch(function(error) {
          $.unblockUI();
          me.controlError(error);
        });
    },

    listarReservasMes() 
    {
        let me = this;
        var url = '/Reportes/listarReservasMesF2?mes='+me.mes;
        axios.get(url).then(function(reponse) 
        {
            me.vectorReservasMes = reponse.data;
            me.listarDetalleReservasMes();
           
        })
        .catch(function(error) {
          $.unblockUI();
          me.controlError(error);
        });   
    },
    listarDetalleReservasMes() 
    {
        let me = this;
        var url = '/Reportes/listarDetalleReservasMes?mes='+me.mes;
        axios.get(url).then(function(reponse) 
        {
           me.vectorDetalleReservasMes = reponse.data;
           me.filaBodyReserva = me.bodyFila1(me.vectorReservasMes,me.vectorDetalleReservasMes);
           $.unblockUI();
        })
        .catch(function(error) {
          $.unblockUI();
          me.controlError(error);
        });   
    },
    bodyFila1(listadoReserva,listadoDetalleReserva) 
    {
      let fila = "";
        var ok=0;
        
        for (var v = 0; v < listadoReserva.length; v++) 
        {
            fila += '<tr style="height: 60px;">';
            fila +='<td style="width:50px">'+listadoReserva[v].cliente+'</td>';
            //nroDias
            for (var z = 0; z < this.nroDias.length; z++) 
            {
                var ok=0;
                var Tecnicos="";
                var TecnicosTitulo="   TECNICOS ASIGNADOS: \n \n";
                var horario="";
                var horarioTitulo="    Horarios del Dia: \n";
                var arrayTecnicos=[];
                var arrayHorarios=[];
                for (var i = 0; i < listadoDetalleReserva.length; i++) 
                {
                    if(listadoReserva[v].COD_CLIENTE==listadoDetalleReserva[i].codCliente && this.nroDias[z]==listadoDetalleReserva[i].dia)
                    {
                        ok=1;
                        if(!arrayTecnicos.includes(listadoDetalleReserva[i].codTrabajador))
                        {
                          Tecnicos+=listadoDetalleReserva[i].tecnico+" <br>";
                          TecnicosTitulo+=" * "+listadoDetalleReserva[i].tecnico+" \n";
                          arrayTecnicos.push(listadoDetalleReserva[i].codTrabajador);
                        }
                        if(!arrayHorarios.includes(listadoDetalleReserva[i].horaInicio))
                        {
                          horario+="De "+listadoDetalleReserva[i].horaInicio+" A "+listadoDetalleReserva[i].hora+" <br>";
                          horarioTitulo+="De "+listadoDetalleReserva[i].horaInicio+" A "+listadoDetalleReserva[i].hora+"  \n";
                          arrayHorarios.push(listadoDetalleReserva[i].horaInicio);
                        }
                    }
                }
               
                arrayTecnicos=[];
                if(ok==0)
                fila +='<td class="centro" style="width:50px"></td>';
                else
                fila +='<td class="centro centroReserva" style="width:50px" title="'+TecnicosTitulo+'">'+horario+'</td>';
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
    this.obtMes();
    
    
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