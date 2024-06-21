<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class=""></ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h1 style="text-align: center"><b>Flujo de Efectivo</b></h1>
          <br />
          <div class="row">
            <div class="col-md-3">
              <div class="input-group">
                <select
                  v-model="sBancoOrigen"
                  class="form-control square"
                  style="width: 100px"
                >
                  <option value="ALL">TODOS</option>
                  <option
                    v-for="data in aDataBanckOrigin"
                    :key="data.banco_cuenta"
                    :value="data.banco_cuenta"
                  >
                    {{ data.banco_cuenta }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-9">
              <div class="input-group">
                <select
                  v-model="tipoFecha"
                  class="form-control square"
                  style="width: 100px"
                  v-on:change="validateDate"
                >
                  <option value="1">HOY</option>
                  <option value="2">MES ACTUAL</option>
                  <option value="3">RANGO FECHAS</option>
                </select>
                <input
                  v-model="sFechaInicio"
                  class="form-control"
                  type="date"
                  style="width: 80px"
                />
                <input
                  v-model="sFechaFin"
                  class="form-control"
                  type="date"
                  style="width: 80px"
                />
                <button
                  class="btn btn-icon square btn-primary"
                  v-on:click="
                    listData(1, sBancoOrigen, sFechaInicio, sFechaFin)
                  "
                >
                  <i class="fa fa-search" aria-hidden="true"></i> Buscar
                </button>
                <button v-on:click="exportToExcel()" class="btn btn-success">
                  <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                </button>
              </div>
            </div>
          </div>

          <!-- //ernesto  -->
          <div class="row">
            <div class="col-md-6">
              <!-- test -->
              <v-select
                v-model="selectedProveedor"
                :options="optionsProveedor"
                label="title"
                searchable
                placeholder="Seleccionar Persona/Proveedor"
                @input="
                  listDataBusqueda(
                    1,
                    sFechaInicio,
                    sFechaFin,
                    selectedProveedor
                  )
                "
              >
                <template #option="{ title }">
                  <span> {{ title }}</span>
                </template>
              </v-select>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div style="overflow-x: auto">
            <div class="panel-body table-responsive">
              <div class="x_title"></div>
              <div id="bodyListado">
                <!-- Mensaje de carga  -->

                <table id="mainTableSG" class="table align-middle">
                  <thead>
                    <tr>
                      <th>PROCESO</th>
                      <th>FECHA</th>
                      <th>BANCO ORIGEN</th>
                      <th>BANCO DESTINO</th>
                      <th>PROV_PERS</th>
                      <th>RESPONSABLE</th>
                      <th>Nro de Cuenta.</th>
                      <th>INGRESO</th>
                      <th>EGRESO</th>
                      <th>MONEDA</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="data in aDataAux"
                      :key="data.idKey"
                      style="height: 50px"
                    >
                      <td>{{ data.proceso }}</td>
                      <td>{{ data.fecha }}</td>
                      <td>{{ data.banco_origen }}</td>
                      <td>{{ data.banco_destino }}</td>
                      <td>{{ data.prov_pers }}</td>
                      <td>{{ data.responsable }}</td>
                      <td>{{ data.nro_cuenta_presupuesto }}</td>
                      <td v-if="data.importe_bs >= 0">
                        {{ data.importe_bs.toFixed(2) }}
                      </td>
                      <td v-else>-</td>
                      <td v-if="data.importe_bs < 0">
                        {{ data.importe_bs.toFixed(2) }}
                      </td>
                      <td v-else>-</td>

                      <td>
                        {{
                          data.importe_bs !== -0
                            ? "BOB"
                            : data.importe_usd !== -0
                            ? "USD"
                            : ""
                        }}
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="loading" class="text-center mt-3 text-muted">
                  <div class="spinner-border text-danger" role="status"></div>
                  <div>Cargando datos...</div>
                </div>
              </div>
              <div class="x_title"></div>
            </div>
          </div>
          <nav>
            <ul class="pagination">
              <li class="page-item" v-if="pagination.current_page > 1">
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="
                    changePage(pagination.current_page - 1, sBancoOrigen)
                  "
                  >Ant</a
                >
              </li>
              <li
                class="page-item"
                v-for="page in pagesNumber"
                :key="page"
                :class="[page == isActived ? 'active' : '']"
              >
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="changePage(page, sBancoOrigen)"
                  >{{ page }}</a
                >
              </li>
              <li
                class="page-item"
                v-if="pagination.current_page < pagination.last_page"
              >
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="
                    changePage(pagination.current_page + 1, sBancoOrigen)
                  "
                  >Sig</a
                >
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  data() {
    return {
      searchTerm: "",
      selectedProveedor: {
        id: 0,
        title: "Seleccionar Proveedor",
      },
      optionsProveedor: [],
      searchProvider: "",
      pageProvider: 1,
      loading: false,
      messageFailed:
        "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",

      //Datos de tablas
      aData: [],
      aDataAux: [],
      aDataBanckOrigin: [],
      aDataBanckDestiny: [],

      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      offset: 3,

      // filtros Listado SG Contado
      tipoFecha: "1",
      sBancoOrigen: "ALL",
      sFechaInicio: "",
      sFechaFin: "",

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
  },
  methods: {
    cleanUp() {
      this.camposRequeridos = [];
    },
    fecha(opc) {
      var result;
      var fecha = new Date(); //Fecha actual
      var mes = fecha.getMonth() + 1; //obteniendo mes
      var dia = fecha.getDate(); //obteniendo dia
      var año = fecha.getFullYear(); //obteniendo año
      var sFechaFin = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
      var ultimoDia = sFechaFin.getDate();
      if (dia < 10) dia = "0" + dia; //agrega cero si el menor de 10
      if (mes < 10) mes = "0" + mes;
      switch (opc) {
        case "actual":
          result = `${año}-${mes}-${dia}`;
          break;
        case "inicio":
          result = `${año}-${mes}-01`;
          break;
        case "fin":
          result = `${año}-${mes}-${ultimoDia}`;
          break;
      }
      return result;
    },
    initialize() {
      let me = this;
      var fecha = me.fecha("actual");
      me.tipoFecha = "1";
      me.sFechaInicio = fecha;
      me.sFechaFin = fecha;
      me.listDataBankOriginCBX();
      me.cbxCuentaProveedor(me.pageProvider, me.searchProvider);
    },
    validateDate() {
      let me = this;
      var tipo = me.tipoFecha;
      switch (tipo) {
        case "1":
          var fecha_actual = me.fecha("actual");
          me.sFechaInicio = fecha_actual;
          me.sFechaFin = fecha_actual;
          break;
        case "2":
          var fecha_inicio = me.fecha("inicio");
          var fecha_fin = me.fecha("fin");
          me.sFechaInicio = fecha_inicio;
          me.sFechaFin = fecha_fin;
          break;
        case "3":
          break;
      }
    },
    listData(page, sBancoOrigen, sFechaInicio, sFechaFin) {
      let me = this;
      me.aDataAux = [];
      var url =
        "/api/reporte/bancario?page=" +
        page +
        "&bancoOrigen=" +
        sBancoOrigen +
        "&fechaInicio=" +
        sFechaInicio +
        "&fechaFin=" +
        sFechaFin;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data.result;
          me.aData = resp.data;
          for (let i = 0; i < me.aData.length; i++) {
            me.aDataAux.push({
              idKey: i,
              proceso: me.aData[i].proceso,
              mov_banco: me.aData[i].mov_banco,
              fecha: me.aData[i].fecha,
              banco_origen: me.aData[i].banco_origen,
              banco_destino: me.aData[i].banco_destino,
              prov_pers: me.aData[i].prov_pers,
              responsable: me.aData[i].responsable,
              nro_cuenta_presupuesto: me.aData[i].nro_cuenta_presupuesto,
              cuenta_presupuesto: me.aData[i].cuenta_presupuesto,
              importe_bs: me.aData[i].importe_bs,
              importe_usd: me.aData[i].importe_usd,
            });
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
        .catch(function (error) {
          me.bugChecking(error);
        });
    },

    cbxCuentaProveedor(page, buscar) {
      let me = this;
      me.optionsProveedor = [];
      var lista = [];
      var url =
        "/api/ZProv_Pers/ListarCuentaCbx?page=" + page + "&buscar=" + buscar;
      const axios = require("axios");
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result.data;
          for (let i = 0; i < resp.length; i++) {
            lista.push({
              id: resp[i].id,
              title: resp[i].nombre_completo,
            });
          }
          me.optionsProveedor = lista;
        })
        .catch((e) => {
          console.log("cbxProveedor " + e);
        });
    },

    listDataBusqueda(page, sFechaInicio, sFechaFin, selectedProveedor) {
      let me = this;
      me.aDataAux = [];
      me.loading = true;
      me.aData = [];
      this.searchTerm = "";
      this.searchTerm = selectedProveedor.title;
      var url =
        "/api/reporte/listDataBusqueda?buscar=" +
        this.searchTerm +
        "&fechaInicio=" +
        sFechaInicio +
        "&fechaFin=" +
        sFechaFin +
        "&page=" +
        page;
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result;
          me.aData = resp.data;
          for (let i = 0; i < me.aData.length; i++) {
            me.aDataAux.push({
              idKey: i,
              proceso: me.aData[i].proceso,
              mov_banco: me.aData[i].mov_banco,
              fecha: me.aData[i].fecha,
              banco_origen: me.aData[i].banco_origen,
              banco_destino: me.aData[i].banco_destino,
              prov_pers: me.aData[i].prov_pers,
              responsable: me.aData[i].responsable,
              nro_cuenta_presupuesto: me.aData[i].nro_cuenta_presupuesto,
              cuenta_presupuesto: me.aData[i].cuenta_presupuesto,
              importe_bs: me.aData[i].importe_bs,
              importe_usd: me.aData[i].importe_usd,
            });
          }
          me.loading = false;
          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
        })
        .catch(function (error) {
          me.bugChecking(error);
        });
    },

    listDataBankOriginCBX() {
      let me = this;
      axios
        .get("/api/reporte/combo/banco/origen")
        .then(function (reponse) {
          me.aDataBanckOrigin = reponse.data.result;
        })
        .catch(function (error) {
          me.bugChecking(error);
        });
    },
    exportToExcel() {
      let me = this;
      location.href =
        "http://mip2024.isitecnologia.com/api/reporte/bancario/exportar?fechaInicio=" +
        me.sFechaInicio +
        "&fechaFin=" +
        me.sFechaFin +
        "&banco=" +
        me.sBancoOrigen;
      // location.href = "http://127.0.0.1:8000/api/reporte/bancario/exportar?fechaInicio="+me.sFechaInicio+"&fechaFin="+me.sFechaFin+"&banco="+me.sBancoOrigen;
    },
    changePage(page, bancoOrigen) {
      let me = this;
      me.pagination.current_page = page;
      me.listData(page, bancoOrigen, me.sFechaInicio, me.sFechaFin);
    },
    bugChecking(error) {
      var array = [];
      var cadena = String(error);
      if (error.response.status == 422) {
        array = error.response.data.errors;
      } else {
        if (error.response.status == 419) {
          this.cerrarSesion();
        } else {
          var cadena = String(error);
          cadena = cadena.substr(cadena.length - 3, 3);
          swal("Message: ", this.messageFailed + " " + cadena, "error");
        }
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
    this.initialize();
  },
};
</script>

<style>
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

.spinner-border {
  width: 3rem;
  height: 3rem;
  margin-right: 5px;
}
</style>