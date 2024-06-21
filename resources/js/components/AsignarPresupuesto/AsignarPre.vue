    <template>
      <main class="main">
        <!-- Breadcrumb -->
        <ol class=""></ol>
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h1 style="text-align: center"><b>ASIGNAR PRESUPUESTOS GLOBALES</b></h1>
              <br />
              <div class="row">
                <div class="col-md-6">
                  <!-- <button type="button" class="btn btn-success square" v-on:click="openWindow('registrar')"
                    title="Apertura de Mes">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    APERTURAR MES
                  </button> -->
                  <button type="button" class="btn btn-success square" v-on:click="openWindow('registrar')"
                    title="Modificar Mes Aperturado">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    MODIFICAR
                  </button>

                  <button type="button" @click="imprimirEXCEL()" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                  </button>
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
                    <select v-model="anio" class="form-control square" style="width: 70px" v-on:change="validateDate">
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
                    </select>

                    <input type="text" v-model="buscar" class="form-control" placeholder="Buscar" />
                    <button class="btn btn-icon square btn-primary" v-on:click="listData(anio, mes, 1, buscar)">
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

                  <div id="bodyListado">
                    <table id="mainTableSG" class="tabla">
                      <thead>
                        <tr>
                          <th class="encabezadoFijo" style="width: 40px"></th>
                          <!-- <th class="encabezadoFijo" style="width: 40px"></th> -->
                          <th class="header-cell" style="padding: 10px;">FONDO</th>
                          <th class="header-cell" style="padding: 10px;">NRO.<br>CUENTA</th>
                          <th class="header-cell" style="padding: 10px;">CUENTA</th>
                          <th class="header-cell" style="padding: 10px;">PRESUPUESTO</th>
                          <th class="header-cell" style="padding: 10px;">MONTO<br>EXCEDIDO</th>
                          <th class="header-cell" style="padding: 10px;">GASTO<br>ACTUAL</th>
                          <th class="header-cell" style="padding: 10px;">SALDO<br>ACTUAL</th>
                          <th class="header-cell" style="padding: 10px;">OBSERVACION</th>
                          <th class="header-cell" style="padding: 10px;">PORCENTAJE %</th>

                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="data in aData" :key="data.codigo" style="height: 50px">
                          <td>
                            <button type="button" class="btn btn-warning" @click="openEditModal(data)" title="EDITAR">
                              <i class="icon-pencil"></i>
                            </button>
                          </td>
                          <!-- <td>
                            <button type="button" class="btn btn-warning" @click="guardarPresupuesto(data)"
                              title="GUARDAR">
                              <i class="icon-pencil"></i>
                            </button>
                          </td> -->
                          <!-- <td>
                            <button type="button" class="btn btn-danger colorRojo" @click="
                            quitarCuenta(data, 'Desactivar', 'Desactivado', 0)
                          " title="Desactivar">
                              <i class="fa fa-times"></i>
                            </button>
                          </td> -->
                          <td style="padding: 10px;">{{ data.fondo }}</td>
                          <td style="padding: 10px;">{{ data.nrocuenta }}</td>
                          <td style="padding: 10px;">{{ data.descripcion }}</td>
                          <td style="padding: 10px;">{{ data.montoPresupuesto }}</td>
                          <td style="padding: 10px;">{{ data.MONTO_EXCEDIDO }}</td>
                          <td style="padding: 10px;">{{ data.montoGastoF }}</td>
                          <td style="padding: 10px;" :class="{ 'rojo': data.saldoActual < 0 }">{{ data.saldoActual }}
                          </td>
                          <td style="padding: 10px;">{{ data.observacion }}</td>
                          <td style="padding: 10px; vertical-align: middle; position: relative;">
  <div class="progress-bar-container">
    <div class="progress-bar" 
         role="progressbar" 
         :style="getProgressBarStyle(data.porcentaje)" 
         :aria-valuenow="data.porcentaje" 
         aria-valuemin="0" 
         aria-valuemax="100">
      <!-- <span v-if="data.porcentaje > 40" style="color: #fff;">{{ data.porcentaje.toFixed(1) }}%</span> -->
    </div>
    <span v-if="data.porcentaje <= 9999" class="progress-text-outside">{{ data.porcentaje.toFixed(1) }}%</span>
  </div>
</td>


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
          <!-- NODAL DE APERTURAR MES -->
          <!-- <div style="overflow-x: auto">
            <b-modal ref="modal" title="¿Está seguro?" centered hide-footer>
              <div class="modal-body">
                <p>El mes actual no está aperturado. ¿Desea aperturarlo?</p>
              </div>
              <div class="modal-footer">
                <b-button class="btn btn-danger colorRojo" @click="irUltimaApertura">Ir a última apertura</b-button>
                <b-button class="btn btn-success square" @click="aperturarMes">Aperturar un nuevo mes</b-button>
              </div>
            </b-modal>
          </div> -->
          <div style="overflow-x: auto">
            <BModal ref="modal" title="¿Está seguro?" centered hide-footer no-close-on-backdrop no-close-on-esc
              hide-header-close>
              <div class="modal-body">
                <p>El mes actual no está aperturado. ¿Desea aperturarlo?</p>
              </div>
              <div class="modal-footer">
                <b-button class="btn btn-danger colorRojo" @click="irUltimaApertura">Ir a última apertura</b-button>
                <b-button class="btn btn-success square" @click="aperturarMes">Aperturar un nuevo mes</b-button>
              </div>
            </BModal>
          </div>

          <!-- NODAL DE EDICION DE PRESUPUESTO Y EXCEDIDO LISTADO PRINCIPAL -->
          <!-- <template> -->
          <b-modal id="editModal" ref="editModal" centered hide-footer>
            <!-- <template v-slot:modal-header> -->
            <div class="modal-header bg-primary text-white">
              <h4 class="modal-title">Editar Presupuesto</h4>
              <!-- <button type="button" class="close text-white" @click="closeEditModal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
            </div>
            <!-- </template> -->

            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="mes">Mes</label>
                  <input type="text" id="mes" v-model="mesNombre" class="form-control" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="gestion">Gestión</label>
                  <input type="text" id="anio" v-model="anio" class="form-control" disabled>
                </div>
              </div>
              <div class="form-group">
                <label for="fondo">Fondo</label>
                <input type="text" id="fondo" v-model="editData.fondo" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="nrocuenta">Nro. Cuenta</label>
                <input type="text" id="nrocuenta" v-model="editData.nrocuenta" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="descripcion">Cuenta</label>
                <input type="text" id="descripcion" v-model="editData.descripcion" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="montoPresupuesto">Presupuesto</label>
                <input type="text" id="montoPresupuesto" v-model="editData.montoPresupuesto" class="form-control"
                  @input="validarPresupuesto(editData)">
              </div>
              <div class="form-group">
                <label for="montoExcedido">Monto Excedido</label>
                <input type="text" id="montoExcedido" v-model="editData.MONTO_EXCEDIDO" class="form-control"
                  @input="validarPresupuesto(editData)">
              </div>
            </div>
            <div class="modal-footer">
              <b-button class="btn btn-danger colorRojo" @click="closeEditModal">Cancelar</b-button>
              <b-button class="btn btn-success square" @click="saveEdit">Guardar</b-button>
            </div>
          </b-modal>
          <!-- </template> -->



          <!-- 
          <b-modal id="editModal" ref="editModal" title="Editar Presupuesto" hide-footer>
            <div class="custom-modal-body">
              <div class="form-group">
                <label for="fondo">Fondo</label>
                <input type="text" id="fondo" v-model="editData.fondo" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="nrocuenta">Nro. Cuenta</label>
                <input type="text" id="nrocuenta" v-model="editData.nrocuenta" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="descripcion">Cuenta</label>
                <input type="text" id="descripcion" v-model="editData.descripcion" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label for="montoPresupuesto">Presupuesto</label>
                <input type="number" id="montoPresupuesto" v-model="editData.montoPresupuesto" class="form-control">
              </div>
              <div class="form-group">
                <label for="montoExcedido">Monto Excedido</label>
                <input type="text" id="montoExcedido" v-model="editData.MONTO_EXCEDIDO" class="form-control" disabled>
              </div>
            </div>
            <div class="custom-modal-footer">
              <b-button variant="secondary" @click="closeEditModal">Cancelar</b-button>
              <b-button variant="primary" @click="saveEdit">Guardar</b-button>
            </div>
          </b-modal> -->



          <!-- MODAL PRINCIPAL DE MODIFICAR -->
          <div class="modal fade" id="modal" :class="{ modal }" role="dialog">
            <div class="modal-dialog modal-xl">
              <div class="modal-content ">
                <div class="modal-header bg-primary">
                  <h4 class="modal-title titulo">{{ modalTitle }}</h4>
                  <button type="button" class="close" v-on:click="closeWindow" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body modal-body-format">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                      <div class="form-group">
                        <label class="lbl-format">MES:</label>
                        <select class="form-control select-format" v-model="model.mes" disabled>
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
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                      <div class="form-group">
                        <label class="lbl-format">AÑO:</label>
                        <select class="form-control select-format" v-model="model.anio" disabled>
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
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="cuentaSelect">Cuentas Contables:</label>
                        <v-select v-model="cuentaSeleccionada" :options="cuentasContables" label="formattedDescription"
                          searchable placeholder="Seleccionar Cuenta Contable" :reduce="cuenta => cuenta.id">
                          <template #option="{ fondo, nrocuenta, DESCRIPCION }">
                            <span>{{ fondo }}/{{ nrocuenta }}/{{ DESCRIPCION }}</span>
                          </template>
                        </v-select>

                      </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                      <div class="form-group">
                        <button @click="anadirCuentaApertura()" class="btn btn-primary">
                          Añadir Cuenta
                        </button>
                      </div>
                    </div>
                  </div>
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
                            <th class="header-cell" style="padding: 10px;">FONDO</th>
                            <th class="header-cell" style="padding: 10px;">NRO.<br>CUENTA</th>
                            <th class="header-cell" style="padding: 10px;">CUENTA</th>
                            <th class="header-cell" style="padding: 10px;">PRESUPUESTO</th>
                            <th class="header-cell" style="padding: 10px;">MONTO<br>EXCEDIDO</th>
                            <th class="header-cell" style="padding: 10px;">OBSERVACION</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="data in cuentasTemporal" :key="data.codigo">
                            <td>
                              <button type="button" class="btn btn-danger colorRojo" @click="quitarCuenta(data)">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                            <td style="padding: 10px;">{{ data.fondo }}</td>
                            <td style="padding: 10px;">{{ data.nrocuenta }}</td>
                            <td style="padding: 10px;">{{ data.descripcion }}</td>
                            <td>
                              <input type="text" v-model="data.montoPresupuesto" @input="validarPresupuesto(data)"
                                style="padding: 5px; width: 100%;" />
                            </td>
                            <td>
                              <input type="text" v-model="data.MONTO_EXCEDIDO" @input="validarPresupuesto(data)"
                                style="padding: 5px; width: 100%;" />
                            </td>
                            <td>
                              <input type="text" v-model="data.observacion" @input="validarObservacion(data)"
                                style="padding: 5px; width: 100%;" />
                            </td>

                            <!-- <td>
                              <input type="text" v-model="data.montoPresupuesto" @input="validarPresupuesto(data)" />
                            </td>
                            <td>
                              <input type="text" v-model="data.MONTO_EXCEDIDO" @input="validarPresupuesto(data)" />
                            </td>
                            <td>
                              <input type="text" v-model="data.observacion" @input="validarObservacion(data)" />
                            </td> -->
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger colorRojo" @click="closeWindow">
                    Cancelar
                  </button>
                  <button type="button" @click="saveRegister" class="btn btn-success colorVerde">
                    Guardar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal de confirmación -->
          <!-- <b-modal ref="confirmModal" title="Confirmación" hide-footer modal-class="custom-confirm-modal">
  <div class="modal-body">
    <p>¿Está seguro de que desea eliminar esta cuenta?</p>
  </div>
  <div class="modal-footer">
    <b-button variant="secondary" @click="cancelarEliminacion">No</b-button>
    <b-button variant="danger" @click="quitarCuentaConfirmada">Sí</b-button>
  </div>
</b-modal> -->

        </div>
      </main>
    </template>

<script>
import Swal from 'sweetalert2';
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
      cuentasContables: [], // Arreglo original
      cuentaSeleccionada: null,
      cuentaSeleccionadaParaEliminar: null,
      nombreCuenta: "",
      messageFailed:
        "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
      anio: new Date().getFullYear(),
      mes: new Date().getMonth() + 1,
      searchData: "",
      buscar: "",
      newDetail: {
        nombreCuenta: "",
        // detalle: '',
        montoPresupuesto: 0,
        MONTO_EXCEDIDO: 0,
        // importeUSD: 0,
        searchData: "",
      },
      cuentasTemporal : [],
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
        // anio: 2024,
        // mes: 4,
      },
      //Datos de tablas
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

      //NODAL DE EDICION PRESUPUESTO Y EXCEDIDO 
      editData: {
      fondo: '',
      gestion: '',
      // mes: '',
      nrocuenta: '',
      descripcion: '',
      montoPresupuesto: 0,
      MONTO_EXCEDIDO: 0,
    },
    originalData: null, // Para mantener una referencia al objeto original

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
    mesNombre() {
      const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ];
      return meses[this.mes - 1];
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
    agregarCuentaSeleccionada() {
      if (this.cuentaSeleccionada) {
        console.log('ID de cuenta seleccionada:', this.cuentaSeleccionada);
        // Realiza las operaciones necesarias con el id de la cuenta
      }
    },
    removeNegativeSign(value) {
    if (isNaN(value) || value === null || value === undefined) {
      return '';
    }
    return Math.abs(value);
  },  
  mostrarModalConfirmacion(data) {
    this.cuentaSeleccionadaParaEliminar = data;
    this.$refs.confirmModal.show();
  },
  // quitarCuenta(data) {
  //   this.cuentaSeleccionadaParaEliminar = data;
  //   this.$refs.confirmModal.show();
  // },
  // quitarCuentaConfirmada() {
  //   const index = this.cuentasTemporal.indexOf(this.cuentaSeleccionadaParaEliminar);
  //   if (index > -1) {
  //     this.cuentasTemporal.splice(index, 1);
  //     swal("Éxito", "Cuenta eliminada de la lista temporal.", "success");
  //   }
  //   this.$refs.confirmModal.hide();
  // },
  // cancelarEliminacion() {
  //   this.$refs.confirmModal.hide();
  // },

  cargarCuentasContables(terminoBusqueda = "") {
    axios
      .get("/api/asignar-presupuesto/cuentas", {
        params: {
          term: terminoBusqueda,
        },
      })
      .then((response) => {
        this.cuentasContables = response.data.map(cuenta => {
          return {
            ...cuenta,
            formattedDescription: `${cuenta.fondo}/${cuenta.nrocuenta}/${cuenta.DESCRIPCION}`
          };
        });
      })
      .catch((error) =>
        console.error("Error al cargar cuentas contables:", error)
      );
  },
  anadirCuentaApertura() {
    if (
        !this.cuentaSeleccionada ||
        !this.model.anio ||
        !this.model.mes
    ) {
        swal("Alerta", "Porfavor Seleccione una Cuenta.", "warning");
        return;
    }

    const cuenta = this.cuentasContables.find(
        (cuenta) => cuenta.id === this.cuentaSeleccionada
    );

    if (!cuenta) {
        swal("Error", "Cuenta seleccionada no encontrada.", "error");
        return;
    }

    const cuentaExistente = this.cuentasTemporal.some(
        (item) => item.codigo === cuenta.id
    );

    if (cuentaExistente) {
        swal("Error", "Esta cuenta ya existe en la lista.", "error");
        return;
    }

    const nuevaCuenta = {
        codigo: cuenta.id,
        descripcion: cuenta.DESCRIPCION,
        fondo: cuenta.fondo,
        nrocuenta: cuenta.nrocuenta,
        montoPresupuesto: 0,
        observacion: "",
        montoGastoF: 0.00,
        saldoActual: 0,
        MONTO_EXCEDIDO: 0,
        TOTAL: 0,
    };

    // Para asegurar la reactividad, crea una nueva referencia al array
    this.cuentasTemporal = [nuevaCuenta, ...this.cuentasTemporal];

    // swal("Éxito", "Cuenta añadida a la lista temporal.", "success");
},


  // anadirCuentaApertura() {
  //   if (
  //     !this.cuentaSeleccionada ||
  //     !this.model.anio ||
  //     !this.model.mes
  //   ) {
  //     swal("Alerta", "Porfavor Seleccione una Cuenta.", "warning");
  //     return;
  //   }

  //   const cuenta = this.cuentasContables.find(
  //     (cuenta) => cuenta.id === this.cuentaSeleccionada
  //   );

  //   if (!cuenta) {
  //     swal("Error", "Cuenta seleccionada no encontrada.", "error");
  //     return;
  //   }

  //   const cuentaExistente = this.cuentasTemporal.some(
  //     (item) => item.codigo === cuenta.id
  //   );

  //   if (cuentaExistente) {
  //     swal("Error", "Esta cuenta ya existe en la lista.", "error");
  //     return;
  //   }
  //   // console.log("datosSeleccionadoCuenta id:" + cuenta.id);
  //   // console.log("datosSeleccionadoCuenta descripcion:" + cuenta.DESCRIPCION);
  //   // console.log("datosSeleccionadoCuenta fondo:" + cuenta.fondo);
  //   // console.log("datosSeleccionadoCuenta nrocuenta:" + cuenta.nrocuenta);
   
  //   const nuevaCuenta = {
  //     codigo: cuenta.id,
  //     descripcion: cuenta.DESCRIPCION,
  //     fondo: cuenta.fondo,  // Asegúrate de que 'cuenta' contiene estos datos
  //     nrocuenta: cuenta.nrocuenta, // Asegúrate de que 'cuenta' contiene estos datos
  //     montoPresupuesto: 0,
  //     observacion: "",
  //     montoGastoF: 0.00,
  //     saldoActual: 0,
  //     MONTO_EXCEDIDO: 0,
  //     TOTAL: 0,
  //   };

  //   // Para asegurar la reactividad, crea una nueva referencia al array
  //   this.cuentasTemporal = [...this.cuentasTemporal, nuevaCuenta];

  //   // swal("Éxito", "Cuenta añadida a la lista temporal.", "success");
  // },

  openEditModal(data) {
    this.editData = {...data}; // Clonar los datos seleccionados para edición
    this.originalData = data; // Mantener una referencia al objeto original
    this.$refs.editModal.show();
  },
  closeEditModal() {
    this.$refs.editModal.hide();
  },
  saveEdit() {
    // Actualizar el objeto original con los datos editados
    Object.assign(this.originalData, this.editData);
    
    this.guardarPresupuesto(this.originalData); // Llamar al método para guardar

    this.$refs.editModal.hide();
  },
   
  //   quitarCuenta(data) {
  //   const index = this.cuentasTemporal.indexOf(data);
  //   if (index > -1) {
  //     this.cuentasTemporal.splice(index, 1);
  //     swal("Éxito", "Cuenta eliminada de la lista temporal.", "success");
  //   }
  // },
  quitarCuenta(data) {
    Swal.fire({
      title: '¿Está seguro?',
      text: "No podrás revertir esto.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.quitarCuentaConfirmada(data);
      }
    });
  },
  quitarCuentaConfirmada(data) {
    const index = this.cuentasTemporal.indexOf(data);
    if (index > -1) {
      this.cuentasTemporal.splice(index, 1);
      Swal.fire('Eliminado', 'La cuenta ha sido eliminada.', 'success');
    }
  },
  
  imprimirEXCEL() {
    // console.log("Año:", this.anio);
    // console.log("Mes:", this.mes);
    // console.log("Buscar:", this.buscar);
    const params = new URLSearchParams({
        tipoMovimiento: this.anio || "", // Verifica que 'this.anio' tenga el valor correcto
        mes: this.mes || "", // Verifica que 'this.mes' tenga el valor correcto
        buscar: this.buscar || "", // Usa valores por defecto si no están definidos
    }).toString();

    const url = `/exportar-asignarpresupuestadoG?${params}`;
    window.location.href = url;
},

guardarPresupuesto(data) {
    axios.post("/api/asignarpresupuesto/actualizarPresupuesto", {
      codCuenta: data.codigo,
      montoPresupuesto: data.montoPresupuesto,
      MONTO_EXCEDIDO: data.MONTO_EXCEDIDO,
      anio: this.anio,
      mes: this.mes,
    })
    .then((response) => {
      swal("Éxito", "Presupuesto actualizado con éxito.", "success");
      this.listData(this.anio, this.mes, this.pagination.current_page, this.buscar);
    })
    .catch((error) => {
      swal("Error", "Error al actualizar el presupuesto.", "error");
    });
  },

  validarNumeroPositivo(value) {
    if (isNaN(value) || value === "") {
      return 0; // Devuelve 0 si no es un número o está vacío
    }
    return Math.abs(parseFloat(value)); // Convierte a número positivo
  },
  validarPresupuesto(data) {
    data.montoPresupuesto = this.validarNumeroPositivo(data.montoPresupuesto);
    data.MONTO_EXCEDIDO = this.validarNumeroPositivo(data.MONTO_EXCEDIDO);
    this.verificarCamposObligatorios();
  },
  verificarCamposObligatorios() {
    let camposCompletos = true;
    this.cuentasTemporal.forEach(data => {
      if (data.montoPresupuesto === " " || data.MONTO_EXCEDIDO === " ") {
        camposCompletos = false;
      }
    });
    return camposCompletos;
  },




    cleanUp() {
      this.camposRequeridos = [];
    },
    cleanForm() {
      let me = this;

      me.model = {
        // fecha: this.fecha('actual'),
      };
      me.cuentaSeleccionada = null;
    },
    initialize() {
      this.cargarCuentasContables();
      this.cargarDatosDetail();

      let me = this;
      // this.verificarAperturaInicial();
      me.listData(me.anio, me.mes, 1, " ");

    },
    

    // Agregar la función para aperturar el mes

    verificarAperturaInicial() {
      axios.get(`/api/asignarpresupuesto/verificarApertura?mes=${this.mes}&gestion=${this.anio}`)
        .then(response => {
          const { mes, gestion } = response.data;
          if (mes !== this.mes || gestion !== this.anio) {
            this.$refs.modal.show();
          } else {
            this.listData(this.anio, this.mes, 1, " ");
          }
        })
        .catch(error => {
          console.error("Error al verificar apertura inicial: ", error);
          swal("Error", "Error al verificar la apertura del mes en curso. Por favor, intente nuevamente.", "error");
        });
    },
    aperturarMes() {
      axios.get(`/api/asignarpresupuesto/aperturarmes?mes=${this.mes}&gestion=${this.anio}`)
        .then(response => {
          swal("¡Éxito!", "El mes ha sido aperturado.", "success");
          this.$refs.modal.hide();
          this.listData(this.anio, this.mes, 1, "");
        })
        .catch(error => {
          swal("¡Atención!", "Se redireccionará a la última apertura.", "info");
          this.$refs.modal.hide();
          this.irUltimaApertura();
        });
    },
    irUltimaApertura() {
      axios.get(`/api/asignarpresupuesto/irUltimaApertura`)
        .then(response => {
          if (response.status === 200) {
            this.anio = response.data.GESTION;
            this.mes = response.data.MES;
            swal("¡Éxito!", "Se han cargado los valores del último mes.", "success");
            this.$refs.modal.hide();
            this.listData(this.anio, this.mes, 1, this.buscar);
          } else {
            swal("Error", "No se encontró la última apertura.", "error");
          }
        })
        .catch(error => {
          swal("Error", "Ocurrió un error al intentar obtener la última apertura. Por favor, intenta nuevamente.", "error");
        });
    },

    
  //   verificarAperturaInicial() {
  //   axios.get(`/api/asignarpresupuesto/verificarApertura?mes=${this.mes}&gestion=${this.anio}`)
  //     .then(response => {
  //       const { mes, gestion } = response.data;
  //       if (mes !== this.mes || gestion !== this.anio) {
          
  //         this.$refs.modal.show();  // Mostrar el modal para aperturar el mes
         
  //       } else {
  //         this.listData(this.anio, this.mes, 1, " ");
  //       }
  //     })
  //     .catch(error => {
  //       console.error("Error al verificar apertura inicial: ", error);
  //       swal("Error", "Error al verificar la apertura del mes en curso. Por favor, intente nuevamente.", "error");
  //     });
  // },

  // // Agregar la función para aperturar el mes
  // aperturarMes() {
  //   axios.get(`/api/asignarpresupuesto/aperturarmes?mes=${this.mes}&gestion=${this.anio}`)
  //     .then(response => {
  //       swal("¡Éxito!", "El mes ha sido aperturado.", "success");
  //       this.$refs.modal.hide();
  //       this.listData(this.anio, this.mes, 1, "");
  //     })
  //     .catch(error => {
  //       swal("¡Atención!", "Se redireccionará a la última apertura.", "info");
  //       this.$refs.modal.hide();
  //       this.irUltimaApertura();  // Redirigir a la última apertura
  //     });
  // },

  // Agregar la función para ir a la última apertura
  // irUltimaApertura() {
  //   axios.get(`/api/asignarpresupuesto/irUltimaApertura`)
  //     .then(response => {
  //       if (response.status === 200) {
  //         this.anio = response.data.GESTION;
  //         this.mes = response.data.MES;
  //         swal("¡Éxito!", "Se han cargado los valores del último mes.", "success");
  //         this.$refs.modal.hide();
  //         this.listData(this.anio, this.mes, 1, this.buscar);
  //       } else {
  //         swal("Error", "No se encontró la última apertura.", "error");
  //       }
  //     })
  //     .catch(error => {
  //       swal("Error", "Ocurrió un error al intentar obtener la última apertura. Por favor, intenta nuevamente.", "error");
  //     });
  // },
    
  
    validateDate() {
      let me = this;
      this.listData(me.anio, me.mes, 1, " ");
    },
    validateMainForm() {
      let me = this;

      return true;
    },

   
    listData(anio, mes, page, buscar) {
  let me = this;
  var url = `/api/asignarpresupuesto/list2?anio=${anio}&mes=${mes}&page=${page}&buscar=${buscar}`;

  axios
    .get(url)
    .then((response) => {
      var resp = response.data; 
      me.aData = resp.data; 

      if (resp.data.length > 0) {
        me.codApertura = resp.data[0].codApertura;
      }
      
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
      me.listData(me.anio, me.mes, page, buscar);
    },
    changeRowColor(id) {
      $("#" + id + " tr").click(function (e) {
        $("#" + id + " tr").removeClass("selected");
        $(this).addClass("selected");
      });
    },
   
  // saveRegister() {
  //   if (this.cuentasTemporal.length === 0) {
  //     swal("Error", "No hay datos para guardar.", "error");
  //     return;
  //   }

  //   const cuentasParaGuardar = [...this.cuentasTemporal];

  //   axios
  //     .post("/api/asignar-presupuesto/guardar-masivo", {
  //       cuentas: cuentasParaGuardar,
  //       anio: this.model.anio,
  //       mes: this.model.mes,
  //     })
  //     .then((response) => {
  //       swal("Éxito", "Presupuestos guardados con éxito.", "success");
  //       this.closeWindow();
  //     })
  //     .catch((error) => {
  //       console.error("Error al guardar los presupuestos:", error);
  //       swal(
  //         "Error",
  //         "Hubo un problema al guardar los presupuestos.",
  //         "error"
  //       );
  //     });
  // },
// CODIGO FUNCIONABLE ANTES DE VALIDAR LOS CAMPOS LLENOS DE PRESUPUESTO Y EXCEDENTE
//   saveRegister() {
//     if (this.cuentasTemporal.length === 0) {
//         swal("Error", "No hay datos para guardar.", "error");
//         return;
//     }

//     const cuentasParaGuardar = [...this.cuentasTemporal];

//     axios
//         .post("/api/asignar-presupuesto/guardar-masivo", {
//             cuentas: cuentasParaGuardar,
//             anio: this.model.anio,
//             mes: this.model.mes,
//         })
//         .then((response) => {
//             swal("Éxito", "Presupuestos guardados con éxito.", "success");
//             this.closeWindow();
//         })
//         .catch((error) => {
//             console.error("Error al guardar los presupuestos:", error);
//             swal(
//                 "Error",
//                 "Hubo un problema al guardar los presupuestos. Ningún cambio fue realizado.",
//                 "error"
//             );
//         });
// },

saveRegister() {
    if (this.cuentasTemporal.length === 0) {
      swal("Error", "No hay datos para guardar.", "error");
      return;
    }

    if (!this.verificarCamposObligatorios()) {
      swal("Error", "El presupuesto o Excedido estan vacios o en cero, porfavor verifique.", "error");
      return;
    }

    const cuentasParaGuardar = [...this.cuentasTemporal];

    axios
      .post("/api/asignar-presupuesto/guardar-masivo", {
        cuentas: cuentasParaGuardar,
        anio: this.model.anio,
        mes: this.model.mes,
      })
      .then((response) => {
        swal("Éxito", "Presupuestos guardados con éxito.", "success");
        this.closeWindow();
      })
      .catch((error) => {
        console.error("Error al guardar los presupuestos:", error);
        swal("Error", "Hubo un problema al guardar los presupuestos. Ningún cambio fue realizado.", "error");
      });
  },


  cargarDatosDetail() {
    let me = this;
    var url = `/api/asignarpresupuesto/list3?page=${me.pagination.current_page}&anio=${me.anio}&mes=${me.mes}&buscar=${me.buscar}`;
    axios
      .get(url)
      .then((respuesta) => {
        this.$set(this.model, "detail", respuesta.data.data);
        this.cuentasTemporal = [...respuesta.data.data]; // Copiamos los datos cargados a la lista temporal
      })
      .catch((error) => {
        console.error("Error al obtener los datos de detalle:", error);
      });
  },

    // Modal
    openWindow(action, data = []) {
      // Obtener el mes y año actuales
    // const currentYear = new Date().getFullYear();
    // const currentMonth = new Date().getMonth() + 1;
    // // Verificar si el mes y año seleccionados son los actuales
    // if (this.anio !== currentYear || this.mes !== currentMonth) {
    //   swal("Advertencia", "Solo se permite modificar el mes en curso.", "warning");
    //   return;
    // }
      let me = this;
      axios
        .get("/api/asignarpresupuesto/verificarApertura")
        .then((response) => {
          const aperturaData = response.data;
          me.cleanUp();
          me.modal = 1;
          me.modalTitle = "Asignacion de Presupuesto";
          me.modalAction = 1;
          me.model.anio = me.anio;
          me.model.mes = me.mes;
          this.cargarDatosDetail();
          // this.cuentasContables();// Llama al método para cargar los datos
          $("#modal").modal("show");
        })
        .catch((error) => {
          console.error("Error al verificar apertura:", error);
        });
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
      this.listData(this.anio, this.mes, 1, "");
      this.cleanForm();

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
    // this.verificarAperturaInicial();
    this.initialize();
  },
};
</script>

    <style>
    .modal-xl {
    max-width: 90% !important; 
  }
  .modal-body-format {
    max-height: 80vh;
    overflow-y: auto;
  }
.input-error {
  border-color: red;
}

.custom-confirm-modal .modal-dialog {
  z-index: 1055 !important; /* Asegúrate de que este valor sea más alto que el z-index del modal existente */
}
.custom-confirm-modal .modal-backdrop {
  z-index: 1054 !important; /* Asegúrate de que este valor sea más alto que el z-index del modal existente */
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
  color: rgb(243, 7, 54);
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
.modal-body .form-group {
  margin-bottom: 15px;
}
.modal-footer {
  display: flex;
  justify-content: space-between;
}
.b-modal .modal-title {
  background-color: #007bff;
  color: white;
  padding: 15px;
  border-radius: 5px 5px 0 0;
}

.progress-bar-container {
  position: relative;
  height: 20px;
  background-color: #e9ecef;
  border-radius: 5px;
  overflow: hidden;
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





/* Diseño par input del presupuesto o excedido */

/* .custom-input {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 8px;
  font-size: 14px;
  width: 100%;
}

.custom-input:focus {
  border-color: #80bdff;
  outline: none;
  box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
} */

</style>