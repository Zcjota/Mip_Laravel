    <template>
      <main class="main">
        <!-- Breadcrumb -->
        <ol class=""></ol>
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h1 style="text-align: center"><b>Control de Presupuestado Global</b></h1>
              <br />
              <div class="row">
                <div class="col-md-6">

                  <!-- Botones -->
                  <button type="button" @click="imprimirEXCEL()" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                  </button>

                  <!-- Filtros -->
                </div>
                <div class="col-md-8">
                  <div class="input-group">
                    <select v-model="mes" class="form-control square" style="width: 70px" v-on:change="validateDate">
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
                      <option value="11">NOVIENBRE</option>
                      <option value="12">DICIEMBRE</option>
                    </select>
                    <!-- <select v-model="tipoMovimiento" class="form-control square" style="width: 70px"
                      v-on:change="validateDate">
                      <option value="2015">2015</option>
                      <option value="2016">2016</option>
                      <option value="2017">2017</option>
                      <option value="2019">2019</option>
                      <option value="2020">2020</option>
                      <option value="2021">2021</option>
                      <option value="2022">2022</option>
                      <option value="2023">2023</option>
                      <option value="2024">2024</option>
                      <option value="2025">2025</option>
                      <option value="2026">2026</option>
                      <option value="2027">2027</option>
                      <option value="2028">2028</option>
                      <option value="2029">2029</option>
                    </select> -->
                    <!-- Busqueda -->
                    <input type="text" v-model="buscar" class="form-control" placeholder="Buscar" />
                    <button class="btn btn-icon square btn-primary"
                      v-on:click="listData(tipoMovimiento, mes, 1, buscar)">
                      <i class="fa fa-search" aria-hidden="true"></i> Buscar
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div style="overflow-x: auto">
                <div class="panel-body table-responsive">
                  <div class="x_title"></div>
                  <!-- <div v-if="tipoMovimiento !== '3'" id="bodyListado"> -->
                  <div id="bodyListado">
                    <table id="mainTableSG" class="tabla">
                      <thead>
                        <tr>
                          <th class="encabezadoFijo" style="width: 40px"></th>
                          <th>FONDO</th>
                          <th>NRO.CUENTA</th>
                          <th>CUENTA</th>
                          <th>PRESUPUESTO</th>
                          <th>MONTO EXCEDIDO</th>
                          <th>GASTO TOTAL</th>
                          <th>SALDO</th>
                          <th>OBSERVACIONES</th>
                          <th>PORCENTAJE</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="data in aData" :key="data.codigo" style="height: 50px">
                          <td>
                            <button type="button" class="btn btn-warning" @click="openWindow('visualizar', data)"
                              title="VER">
                              <i class="fa fa-eye"></i>
                            </button>
                          </td>
                          <td>{{ data.fondo }}</td>
                          <td>{{ data.nrocuenta }}</td>
                          <td>{{ data.descripcion }}</td>
                          <td>{{ data.montoPresupuesto }}</td>
                          <td>{{ data.MONTO_EXCEDIDO }}</td>
                          <td>{{ data.montoGastoF }}</td>
                          <td>{{ data.saldoActual }}</td>
                          <td>{{ data.observacion }}</td>
                          <td style="padding: 10px; vertical-align: middle; position: relative;">
                            <div class="progress-bar-container">
                              <div class="progress-bar" role="progressbar" :style="getProgressBarStyle(data.porcentaje)"
                                :aria-valuenow="data.porcentaje" aria-valuemin="0" aria-valuemax="100">
                                <!-- <span v-if="data.porcentaje > 40" style="color: #fff;">{{ data.porcentaje.toFixed(1) }}%</span> -->
                              </div>
                              <span v-if="data.porcentaje <= 9999" class="progress-text-outside">{{
                                data.porcentaje.toFixed(1) }}%</span>
                            </div>
                          </td>

                          <!-- <td>
                            <input type="number" v-model="data.montoPresupuesto" @input="validarPresupuesto(data)" />
                          </td> -->

                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="x_title"></div>
                </div>
              </div>
              <nav>
                <ul class="pagination">
                  <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="
                    changePage(pagination.current_page - 1, searchData)
                  ">Ant</a>
                  </li>
                  <li class="page-item" v-for="page in pagesNumber" :key="page"
                    :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="changePage(page, searchData)">{{ page }}</a>
                  </li>
                  <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                    <a class="page-link" href="#" @click.prevent="
                    changePage(pagination.current_page + 1, searchData)
                  ">Sig</a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>

          <!-- MODAL PRINCIPAL  -->
          <div class="modal fade" id="modal" :class="{ mostrar:modal }" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content modal-content-format">
                <div class="modal-header bg-primary">
                  <h4 class="modal-title titulo">{{ modalTitle }}</h4>
                  <button type="button" class="close" v-on:click="closeWindow" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="col-md-6">

                  <!-- Botones -->
                  <button type="button" @click="imprimirDetalleEXCEL()" class="btn btn-success colorVerde">
                    <i class="fa fa-file-excel-o"></i>&nbsp;Exportar Detalle
                  </button>


                  <!-- Filtros -->
                </div>
                <div class="modal-body modal-body-format">
                  <div class="row div-table-format">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <table class="table table-hover-animation mb-0" style="width: 100%">
                        <thead class="thead-dark">
                          <tr style="
                  background-color: #243648f0;
                  color: white;
                  height: 40px;
                ">
                            <th class="encabezadoFijo" style="width: 40px"></th>
                            <th>DESCRIPCION</th>
                            <th>MONTO GASTADO</th>
                            <th>TIPO DE GASTO</th>
                            <th>TIPO DE PAGO</th>
                            <th>FECHA DE GASTO</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- <tr v-for="data in cuentasTemporal" :key="data.codigo"> -->
                          <tr v-for="data in pData" :key="data.codigod">
                            <td>
                            </td>
                            <td>{{ data.descripcion }}</td>
                            <td>{{ data.montoGasto }}</td>
                            <td>{{ getTipoGastoDescripcion(data.categoria) }}</td>
                            <td>{{ getTipoPagoDescripcion(data.tipoPago) }}</td>
                            <!-- <td>{{ data.categoria }}</td>
                            <td>{{ data.tipoPago }}</td> -->
                            <td>{{ data.fechaGasto }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <nav>
                    <ul class="pagination">
                      <li class="page-item" v-if="detailPagination.current_page > 1">
                        <a class="page-link" href="#"
                          @click.prevent="changePageDetalle(detailPagination.current_page - 1)">Ant</a>
                      </li>
                      <li class="page-item" v-for="page in detailPagesNumber" :key="page"
                        :class="[page == detailIsActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="changePageDetalle(page)">{{ page }}</a>
                      </li>
                      <li class="page-item" v-if="detailPagination.current_page < detailPagination.last_page">
                        <a class="page-link" href="#"
                          @click.prevent="changePageDetalle(detailPagination.current_page + 1)">Sig</a>
                      </li>
                    </ul>
                  </nav>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger colorRojo" @click="closeWindow">
                    Volver
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </main>
    </template>

<script>
import {
  BFormDatepicker,
  BRow,
  BModal,
  VBModal,
  BAvatar,
  BCardTitle,
  BCardBody,
  BCardHeader,
  BCard,
  BDropdown,
  BDropdownItem,
  BButton,
  BFormSelect,
  BCol,
  BFormGroup,
  BFormInput,
  BFormCheckbox,
  BForm,
  BFormText,
  BFormDatalist,
  BBadge,
  BTable,
  BMedia,
  BFormTextarea,
  BInputGroupAppend,
  BInputGroup,
  BOverlay,
  BSpinner,
  BFormValidFeedback,
  BFormInvalidFeedback,
  VBTooltip,
  BPagination,
} from "bootstrap-vue";

export default {
  components: {
    BFormDatepicker,
    BRow,
    BModal,
    VBModal,
    BAvatar,
    BCardTitle,
    BCardBody,
    BCardHeader,
    BCard,
    BDropdown,
    BDropdownItem,
    BButton,
    BFormSelect,
    BCol,
    BFormGroup,
    BFormInput,
    BFormCheckbox,
    BForm,
    BFormText,
    BFormDatalist,
    BBadge,
    BTable,
    BMedia,
    BFormTextarea,
    BInputGroupAppend,
    BInputGroup,
    BOverlay,
    BSpinner,
    BFormValidFeedback,
    BFormInvalidFeedback,
    VBTooltip,
    BPagination,
  },
  data() {
    return {
      codApertura: null,
      terminoBusqueda: "",
      cuentasMostradas: [], // Arreglo para manipulación en el formulario
      cuentasContables: [], // Arreglo original
      cuentaSeleccionada: null,
      nombreCuenta: "",
      messageFailed:
        "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
      tipoMovimiento: new Date().getFullYear(),
      mes: new Date().getMonth() + 1,
      searchData: "",
      buscar: "",
      newDetail: {
        nombreCuenta: "",
        // detalle: '',
        montoPresupuesto: 0,
        // importeUSD: 0,
        searchData: "",
      },
      aperturaMes: new Date().getMonth() + 1, // Mes actual
      aperturaGestion: new Date().getFullYear(), // Año actual
      meses: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ],
      model: {
        detail: [],
        // tipoMovimiento: 2024,
        // mes: 4,
      },
      //Datos de tablas
      pData: [],
      aData: [],
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      offset: 3,

      detailPagination: {
      total: 0,
      current_page: 0,
      per_page: 0,
      last_page: 0,
      from: 0,
      to: 0,
      },
      detailOffset: 3,

      // modal
      modal: 0,
      modalCancel: 0,
      modalTitle: "",
      modalAction: 0,
      modalMessage: "",
      camposRequeridos: [],
    };
  },
  computed: {
    isActived: function () {
      return this.pagination.current_page;
    },
    pagesNumber: function () {
      if (!this.pagination.to) {
        return [];
      }
      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }
      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }
      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
    detailIsActived() {
    return this.detailPagination.current_page;
  },
  detailPagesNumber() {
    if (!this.detailPagination.to) {
      return [];
    }
    var from = this.detailPagination.current_page - this.detailOffset;
    if (from < 1) {
      from = 1;
    }
    var to = from + this.detailOffset * 2;
    if (to >= this.detailPagination.last_page) {
      to = this.detailPagination.last_page;
    }
    var pagesArray = [];
    while (from <= to) {
      pagesArray.push(from);
      from++;
    }
    return pagesArray;
  },
  },
  methods: {
    getProgressBarStyle(porcentaje) {
    let backgroundColor;
    if (porcentaje <= 40) {
      backgroundColor = '#007bff'; // Azul
    } else if (porcentaje <= 80) {
      backgroundColor = '#28a745'; // Verde
    } else if (porcentaje <= 100) {
      backgroundColor = '#ffc107'; // Amarillo
    } else {
      backgroundColor = '#dc3545'; // Rojo
    }
    return {
      width: porcentaje + '%',
      backgroundColor: backgroundColor
    };
  },
  getProgressBarTextColor(porcentaje) {
    if (porcentaje > 80) {
      return '#000'; // Negro
    } else {
      return '#fff'; // Blanco
    }
  },
    mostrarModal() {
    this.$refs.modal.show();
  },
  getDataByID(codigo, tipoMovimiento, mes, page = 1) {
  let me = this;
  axios
    .get(`/api/controlpglobal/list4?codigo=${codigo}&tipoMovimiento=${tipoMovimiento}&mes=${mes}&page=${page}`)
    .then((response) => {
      let data = response.data;
      // console.log(data);
      me.pData = data.data;  // Asigna la respuesta a pData
      me.detailPagination = {
        total: data.total,
        current_page: data.current_page,
        per_page: data.per_page,
        last_page: data.last_page,
        from: data.from,
        to: data.to
      };
    })
    .catch((error) => {
      console.error(error);
      me.bugChecking(error);
    });
}
,
changePageDetalle(page) {
    let me = this;
    me.detailPagination.current_page = page;
    me.getDataByID(me.codigo, me.tipoMovimiento, me.mes, page);
  },

  getTipoGastoDescripcion(tipo) {
    switch(tipo) {
      case 'F':
        return 'FIJO';
      case 'O':
        return 'OTRO';
      case 'E':
        return 'EVENTUAL';
      default:
        return tipo;
    }
  },
  getTipoPagoDescripcion(tipo) {
    switch(tipo.toString()) {
      case '1':
        return 'TRANSFERENCIA';
      case '2':
        return 'EFECTIVO';
      case '3':
        return 'CHEQUE';
      default:
        return tipo;
    }
  },

  
    imprimirEXCEL() {
      const params = new URLSearchParams({
        tipoMovimiento: this.tipoMovimiento || "",
        mes: this.mes || "",
        buscar: this.buscar || "", // Usa valores por defecto si no están definidos
      }).toString();

      const url = `/exportar-presupuestadoG?${params}`;
      window.location.href = url;
    },

    imprimirDetalleEXCEL() {
    const params = new URLSearchParams({
      codigo: this.codigo || "",
      tipoMovimiento: this.tipoMovimiento || "",
      mes: this.mes || ""
    }).toString();

    const url = `/exportar-detalle?${params}`;
    window.location.href = url;
  },
   
    cleanUp() {
      this.camposRequeridos = [];
    },
    // cleanForm() {
    //   let me = this;

    //   me.model = {
    //     // fecha: this.fecha('actual'),
    //   };
    //   me.cuentaSeleccionada = null;
    // },
    initialize() {
      // this.cargarCuentasContables();
      // this.cargarDatosDetail();

      let me = this;
      // this.verificarAperturaInicial();
      me.listData(me.tipoMovimiento, me.mes, 1, " ");
    },


    validateDate() {
      let me = this;
      this.listData(me.tipoMovimiento, me.mes, 1, " ");
    },
    validateMainForm() {
      let me = this;

      return true;
    },

    listData(tipoMovimiento, mes, page, buscar) {
      let me = this;
      // let url = `/api/asignarpresupuesto/list2?tipoMovimiento=${tipoMovimiento}&mes=${mes}&page=${page}&searchData=${searchData}`;
      var url = `/api/controlpglobal/list2?tipoMovimiento=${tipoMovimiento}&mes=${mes}&page=${page}&buscar=${buscar}`;

      // Cambio a arrow function aquí
      axios
        .get(url)
        .then((response) => {
          // console.log(response.data); // Muestra los datos en la consola
          var resp = response.data; // Accede a la respuesta
          me.aData = resp.data; 
          

          if (resp.data.length > 0) {
            me.codApertura = resp.data[0].codApertura; // Ajusta este acceso según la estructura de tu respuesta
          }
          // Configura la paginación basada en la respuesta de la API
          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
        })
        .catch((error) => {
          me.bugChecking(error);
        });
    },
    

    changePage(page, buscar) {
      let me = this;
      me.pagination.current_page = page;
      me.listData(me.tipoMovimiento, me.mes, page, buscar);
    },
    changeRowColor(id) {
      $("#" + id + " tr").click(function (e) {
        $("#" + id + " tr").removeClass("selected");
        $(this).addClass("selected");
      });
    },

    // Modal
    openWindow(action, data = []) {
      let me = this;
      me.cleanUp();
      switch (action) {
        case "visualizar": {
          me.modal = 1;
          me.modalTitle = "Informacion de Gastos";
          me.modalAction = 2;
          me.codigo = data.codigo; 
          me.getDataByID(me.codigo, me.tipoMovimiento, me.mes);
          // me.getDataByID(data.codigo, me.tipoMovimiento, me.mes);
          // me.getDataByID(data.codigo);
          break;
        }
      }
    },

    setForm(data) {
      let me = this;
      for (let i = 0; i < data.length; i++) {}
    },
    closeWindow() {
      this.modal = 0;
      this.cuentaSeleccionada = null;
      this.cuentasTemporal = [];
      this.model.detail = [];
      this.modalCancel = 0;
      this.modalTitle = "";
      this.modalMessage = "";
      $("#modal").modal("hide");
      // this.listData();
      this.listData(this.tipoMovimiento, this.mes, 1, "");
      // this.cleanForm();

      // Asume que listData es el método que carga tu listado principal.
      // Aquí llamas a listData para actualizar el listado principal después de cerrar el modal.
      // Asegúrate de pasar los argumentos correctos a listData si es necesario.
    },
    closeWindowWithoutCleaning() {
      this.modal = 0; // Cerrar el modal.

      this.modalMessage = "";
    },
    
    bugChecking(error) {
      var array = [];
      // Verifica primero si existe error.response
      if (error.response) {
        switch (error.response.status) {
          case 422:
            array = error.response.data.errors;
            break;
          case 419:
            this.cerrarSesion();
            break;
          case 404:
            if (error.response.data && error.response.data.message) {
              mensaje = error.response.data.message;
            }
            // Verificar si hay una acción sugerida
            if (error.response.data && error.response.data.action === 'APERTURAR_MES') {
              this.mostrarModal();
            } else {
              swal("Error", mensaje, "error");
            }
            break;
          default:
            // Manejar otros códigos de mes HTTP aquí si es necesario
            var mensaje = this.messageFailed;
            if (error.response.data && error.response.data.message) {
              mensaje += ": " + error.response.data.message;
            } else {
              var cadena = String(error);
              cadena = cadena.substr(cadena.length - 3, 3);
              mensaje += " " + cadena;
            }
            swal("Error", mensaje, "error");
            break;
        }
      } else {
        // Maneja casos donde error.response no está definido
        console.error("Error: ", error.message || "Unknown error");

        swal(
          "Error",
          "Ocurrió un error inesperado. Por favor, intenta nuevamente.",
          "error"
        );
      }
      return array;
    },
    cerrarSesion() {
      swal({
        title: "La Sesion ha Expirado, Debe Iniciar Sesion Nuevamente.",
        type: "warning",
        confirmButtonColor: "green",
        confirmButtonText: "Aceptar",
        confirmButtonClass: "btn btn-success",
      }).then((result) => {
        if (result.value) {
          location.href = "http://mip2024.isitecnologia.com/";
        }
      });
    },
  },
  mounted() {
    // this.cargarDatosDetail();
    this.initialize();
  },
};
</script>

    <style>
.input-error {
  border-color: red;
}
.modal-content-format {
  position: absolute !important;
  width: 1000px !important;
  height: auto !important;
}
.model-content-format-2 {
  width: 700px !important;
  height: 500px !important;
}
.colorRojo {
  background-color: crimson;
}
.rojo {
  color: crimson;
}
.colorVerde {
  background-color: green;
}
.table-hover tbody tr:hover td,
.table-hover tbody tr:hover th {
  background-color: #dbdbdb;
}
.modal-body {
  position: relative;
  padding: 20px;
  height: auto;
  overflow-y: scroll;
}
.selected {
  background-color: red;
}
.resetColorFila {
  background-color: aquamarine;
}
.lbl-format {
  font-style: italic;
  color: #000000;
  font-size: 12px;
  font-weight: bold;
  margin: 0;
}
.input-format {
  font-size: 12px;
  font-weight: bold;
}
.modal-body-format {
  background-color: #ebefe3 !important;
  /* background-color: #c4bcb5 !important;  */
}
.select-format {
  font-size: 11px;
}
.textarea-format {
  font-size: 11px;
  text-transform: uppercase;
}
.input-detalle-format {
  width: 80px !important;
  font-weight: bold;
  font-size: 14px;
}
.textarea-detalle-format {
  text-transform: uppercase;
  width: 270px;
  font-style: normal;
  color: #000000;
}
.tfoot-format {
  background-color: #7396a7;
}
.table-thead-td {
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}
.table-tbody-td {
  font-size: 10px;
}
.table-tfoot-td {
  font-size: 12px;
  font-weight: bold;
}
.div-table-format {
  height: 330px;
  overflow-y: scroll;
}

.progress-bar {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-end; /* Alinea el texto a la derecha */
  transition: width 0.6s ease;
  padding-right: 5px; /* Añade padding para separar el texto del borde */
}

.progress-bar span {
  font-weight: bold;
  white-space: nowrap;
  padding: 0 5px; /* Añade padding interno para el texto */
}

.progress-text-outside {
  position: absolute;
  right: 5px;
  top: 50%;
  transform: translateY(-50%);
  font-weight: bold;
  white-space: nowrap;
  padding: 0 5px;
  color: #000;
}
</style>