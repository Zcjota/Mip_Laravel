<template>
  <main class="main">
    <div class="container-fluid">
      <b-card>
        <b-card-header>
          <BCardTitle> Reporte de Emisión de Factura </BCardTitle>
          <b-row>
            <b-col md="9">
              <div class="input-group">
                <input
                  v-model="fechaInicio"
                  class="form-control"
                  type="date"
                  style="width: 80px"
                />
                <input
                  v-model="fechaFin"
                  class="form-control"
                  type="date"
                  style="width: 80px"
                />
                <input
                  type="text"
                  v-model="busqueda"
                  class="form-control"
                  v-on:input="facturasPorEmitir()"
                  placeholder="Buscar"
                />
                <button
                  class="btn btn-icon square btn-primary"
                  v-on:click="facturasPorEmitir()"
                >
                  <i class="fa fa-search" aria-hidden="true"></i> Buscar
                </button>
              </div>
            </b-col>
          </b-row>
        </b-card-header>
        <b-card-body>
          <b-row>
            <b-col>
              <!-- Tabla -->
              <!-- Listado -->
              <b-table
                id="tabla-lista-ventas"
                v-model="items"
                :items="items"
                :fields="fields"
                :filter="filter"
                @filtered="onFiltered"
                hover
                :bordered="true"
                :busy="isBusy"
                outlined
                stacked="sm"
                small
                :style="{ fontSize: fontSize }"
                :sticky-header="stickyHeader"
              >
                <template #table-busy>
                  <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Cargando...</strong>
                  </div>
                </template>
              </b-table>
              <b-pagination
                v-model="currentPage"
                :per-page="perPage"
                :total-rows="totalRows"
                size="lg"
                class="mt-2"
                align="center"
              >
                <template #first-text>
                  <span class="text-success">Primero</span>
                </template>
                <template #prev-text>
                  <span class="text-warning">Anterior</span>
                </template>
                <template #next-text>
                  <span class="text-info">Próximo</span>
                </template>
                <template #last-text>
                  <span class="text-danger">Ultimo</span>
                </template>
                <template #ellipsis-text>
                  <div>
                    <b-spinner small type="grow" />
                    <b-spinner small type="grow" />
                  </div>
                </template>
                <template #page="{ page, active }">
                  <b v-if="active">{{ page }}</b>
                  <i v-else>{{ page }}</i>
                </template>
              </b-pagination>
            </b-col>
          </b-row>
        </b-card-body>
      </b-card>
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
      fechaInicio: null,
      currentPage: 1,
      perPage: 5, // Número de elementos por página
      totalRows: 1,
      nVenta: 0,
      cliente: "",
      fontSize: "",
      dateDefault: null,
      txtCantidad: 0,
      TipoAccion: null,
      stickyHeader: true,
      transProps: {
        // Transition name
        name: "flip-list",
      },
      txtCodigoInterno: 0,
      totalVentas: 0,
      shows: true,
      isBusy: false,
      loaded: false,
      filter: "",
      filterOn: [],
      fecha: 0,
      fechaRegistro: "",
      Loading: "",
      estado: "",
      items: [],
      fields: [
        { key: "FECHA", label: "Fecha OT" },
        { key: "FECHA_EMISION_FC", label: "Fecha de Emisión de Factura" },
        { key: "NRO_ORDEN", label: "Número de OT" },
        { key: "cliente_nombre", label: "Nombre Cliente" },
        { key: "RAZON_SOCIAL", label: "Razón Social" },
        { key: "NIT", label: "NIT" },
        // { key: 'TIPO_PAGO', label: 'Tipo de Pago' },
        { key: "PRECIO", label: "COSTO" },
      ],

      show: false,
      variant: "dark",
      opacity: 0.85,
      blur: "2px",
      // selected: [],

      stickyHeader: true,

      perPage: 10, // Número de elementos por página
      totalRows: 1,

      fontSize: "",
      // datos de el otro componente

      isBusy: false,
      //Datos de tablas

      offset: 3,

      // filtros Listado
      tipoFecha: "1",
      busqueda: "",
      fechaInicio: "",
      fechaFin: "",
    };
  },
  computed: {
    totalRows() {
      this.totalRows = this.items.length;
    },
  },
  created() {
    this.sizeTablas();
  },
  watch: {
    currentPage: function (newPage) {
      if (!newPage || newPage === "") {
        newPage = 1;
      }
      this.currentPage = newPage;
    },
  },
  mounted() {
    this.facturasPorEmitir();
    this.fechaInicio = this.obtenerFechaActual();
    this.fechaFin = this.obtenerFechaActual();
  },
  methods: {
    obtenerFechaActual() {
      // Obtén la fecha actual en el formato YYYY-MM-DD
      const now = new Date();
      const year = now.getFullYear();
      let month = now.getMonth() + 1;
      if (month < 10) {
        month = `0${month}`; // Agrega un cero al mes si es menor que 10
      }
      let day = now.getDate();
      if (day < 10) {
        day = `0${day}`; // Agrega un cero al día si es menor que 10
      }
      return `${year}-${month}-${day}`;
    },
    onFiltered(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRows = filteredItems.length;
    },

    //Metodos Nuevos  Ernesto

    sizeTablas() {
      const anchoVentana = window.innerWidth;

      if (anchoVentana <= 576) {
        // Dispositivo móvil pequeño
        this.fontSize = "xx-small";
      } else if (anchoVentana <= 768) {
        // Dispositivo móvil o tableta
        this.fontSize = "small";
      } else if (anchoVentana <= 992) {
        // Tableta o dispositivo de tamaño medio
        this.fontSize = "medium";
      } else {
        // Pantalla de escritorio
        this.fontSize = "mediun";
      }
    },
    facturasPorEmitir() {
      let me = this;
      const axios = require("axios").default;
      me.items = [];
      me.isBusy = true;
      var url = "api/reservas/facturasPorEmitir";
      me.loaded = false;
      var lista = [];

      const formData = new FormData();
      formData.append("page", me.currentPage);
      formData.append("perPage", me.perPage);
      formData.append("fechaInicio", me.fechaInicio);
      formData.append("fechaFin", me.fechaFin);
      formData.append("busqueda", me.busqueda);
      axios
        .post(url, formData)
        .then(function (response) {
          var resp = response.data.data; // Acceder directamente a response.data
          me.totalRows = response.data.total;
          me.currentPage = response.data.current_page;
          //  me.lastPage = response.data.last_page;
          var badge = null;
          if (response.status === 201) {
            me.loaded = false;
            me.isBusy = false;
            return alert(response.data.mensaje);
          }

          for (let i = 0; i < resp.length; i++) {
            // Acceder a resp.data

            lista.push({
              COD_ORDEN_TRABAJO: resp[i].COD_ORDEN_TRABAJO,
              FECHA: resp[i].FECHA,
              FECHA_EMISION_FC: resp[i].FECHA_EMISION_FC,
              NRO_ORDEN: resp[i].NRO_ORDEN,
              cliente_nombre: resp[i].cliente_nombre,
              RAZON_SOCIAL: resp[i].RAZON_SOCIAL,
              NIT: resp[i].NIT,
              TIPO_PAGO: resp[i].TIPO_PAGO,
              PRECIO: resp[i].PRECIO,
            });
          }

          me.items = lista;
          me.isBusy = false;
          me.loaded = true;
        })
        .catch((e) => {
          // this.UsuarioAlerta("info", e.response.data.mensaje)
          if (e.response && e.response.data && e.response.data.mensaje) {
            // Muestra el mensaje de error enviado desde el servidor
            alert("Error al obtener los datos: " + e.response.data.mensaje);
          } else {
            // Muestra un mensaje genérico en caso de que no haya un mensaje específico del servidor
            alert(
              "Error al obtener los datos. Detalles del error: " + e.message
            );
          }
        });
    },
  },
};
</script>
<style></style>