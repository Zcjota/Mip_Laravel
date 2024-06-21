<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class=""></ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h1 style="text-align: center">
            <b>CONCILIACION DE INGRESOS</b>
          </h1>
          <br />
          <div class="row">
            <div class="col-md-3">
              <button
                type="button"
                @click="imprimirEXCEL()"
                class="btn btn-primary"
              >
                <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
              </button>
            </div>
            <div class="col-md-9">
              <div class="input-group">
                <select
                  v-model="estado"
                  class="form-control square"
                  style="width: 70px"
                  v-on:change="validateDate"
                >
                  <option value="">TODOS</option>
                  <option value="0">POR REVISAR</option>
                  <option value="2">OBSERVACION</option>
                </select>

                <!-- Selecciona tipo ingresos -->
                <select
                  v-model="tipoMovimiento"
                  class="form-control square"
                  style="width: 70px"
                  v-on:change="validateDate"
                >
                  <!-- <option value="">SELLECIONAR</option> -->
                  <option value="0">TODOS</option>
                  <option value="1">OTROS INGRESOS</option>
                  <option value="2">DEVOLUCIONES PERSONAL</option>
                  <option value="3">COBRANZA</option>
                  <option value="4">TRASPASO</option>
                </select>

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
                  v-model="searchData"
                  class="form-control"
                  placeholder="Buscar"
                />
                <button
                  class="btn btn-icon square btn-primary"
                  v-on:click="
                    listData(
                      1,
                      searchData,
                      fechaInicio,
                      fechaFin,
                      tipoMovimiento,
                      estado
                    )
                  "
                >
                  <i class="fa fa-search" aria-hidden="true"></i> Buscar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="panel-body table-responsive">
            <div class="container">
              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h1>DATOS GENERALES</h1>
                  </div>
                  <div class="panel-body">
                    <div id="bodyListado">
                      <table
                        id="mainTableSG"
                        class="table table-condensed table-striped"
                      >
                        <thead>
                          <tr>
                            <th>DOCUMENTO</th>
                            <th>FECHA</th>
                            <th>ESTADO</th>
                            <th>NRO.</th>
                            <!-- <th>RESPONSABLE</th> -->
                            <!-- <th>CTA.CONTABLE</th> -->

                            <th>IMPORTE EN BS</th>
                            <th>USD</th>
                            <!-- <th>BANCO ORIGEN</th> -->
                            <th>BANCO DESTINO</th>
                            <th>DETALLE</th>
                            <th>VER DETALLE</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="data in aData"
                            :key="'principal' + data.id_detalle_solicitud"
                            style="height: 50px"
                          >
                            <td>{{ data.tipo }}</td>
                            <td>{{ data.fecha }}</td>
                            <td>
                              <span v-if="data.bandera === null">PEND.</span>
                              <span v-if="data.bandera === 0">PEND.</span>
                              <button
                                v-else-if="data.bandera === 2"
                                class="btn-warning"
                                @click="handleWarning(data)"
                              >
                                ⚠️
                              </button>
                              <span v-else>{{ data.bandera }}</span>
                            </td>

                            <td style="height: 25px">{{ data.nro }}</td>
                            <!-- <td>{{ data.responsable }}</td> -->
                            <!-- <td>{{ data.cuenta }}</td> -->

                            <td>{{ data.totalbs | formatNumber }}</td>
                            <td>{{ data.usd }}</td>
                            <!-- <td>{{ data.bancoo }}</td> -->
                            <td>{{ data.banco_destino }}</td>
                            <td>{{ data.detalle }}</td>
                            <td>
                              <input
                                type="checkbox"
                                v-model="selectedSCD"
                                :value="data.id_detalle_solicitud"
                                data-toggle="collapse"
                                class="accordion-toggle"
                                :data-target="
                                  '#detalle' + data.id_detalle_solicitud
                                "
                                @change="
                                  scrollToDetailTable(data.id_detalle_solicitud)
                                "
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- detalle otros ingresos -->
          <div class="card border-info">
            <div class="row">
              <div class="col-md-12">
                <div
                  v-for="data in aData"
                  :key="'ingresos' + data.id_detalle_solicitud"
                  class="accordion"
                >
                  <div
                    :id="'detalle' + data.id_detalle_solicitud"
                    class="collapse"
                    :aria-labelledby="
                      'headingDetail' + data.id_detalle_solicitud
                    "
                    data-parent="#app"
                  >
                    <div class="panel-heading">
                      <div class="row">
                        <div
                          class="col-md-3 col-xs-12 col-sm-3 col-lg-3 col-xl-3"
                        >
                          <button
                            type="button"
                            class="btn btn-success colorVerde"
                            v-on:click="openWindow('registrar')"
                            title="Nuevo Registro"
                          >
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            NUEVO
                          </button>
                          <button
                            type="button"
                            class="btn btn-success colorVerde"
                            v-on:click="openWindow2('registrar2')"
                            title="Nuevo Registro"
                          >
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            OBSERVAR
                          </button>
                        </div>
                        <div
                          class="col-md-9 col-xs-12 col-sm-9 col-lg-9 col-xl-9"
                        >
                          <!-- Información -->
                          <h6 style="display: inline-block; margin-right: 30px">
                            <strong> FECHA : </strong>
                            <span style="color: #6c757d">
                              {{ data.fecha }}</span
                            >
                          </h6>
                          <h6 style="display: inline-block; margin-right: 30px">
                            <strong> NRO. : </strong>
                            <span style="color: #6c757d"> {{ data.nro }}</span>
                          </h6>
                          <h6 style="display: inline-block; margin-right: 30px">
                            <strong> FACTURA :</strong>
                            <span style="color: #6c757d">
                              {{ data.fact }}
                            </span>
                          </h6>
                          <h6 style="display: inline-block; margin-right: 30px">
                            <strong> IMPORTE EN BS : </strong>
                            <span style="color: #6c757d">
                              {{ data.totalbs | formatNumber }}</span
                            >
                          </h6>
                          <h6 style="display: inline-block">
                            <strong> IMPORTE EN USD :</strong>
                            <span style="color: #6c757d"> {{ data.usd }} </span>
                          </h6>
                        </div>
                      </div>
                    </div>
                    <!-- Contenido del detalle -->
                    <div
                      class="table-responsive table-responsive-sm table-responsive-lg table-responsive-xl table-responsive-xxl table-responsive-md"
                    >
                      <table id="mainTableDetalle" class="table">
                        <thead>
                          <tr class="info">
                            <th>&nbsp;&nbsp;SELECCIÓN</th>
                            <th>ESTADO</th>
                            <th>BENEFICIARIO/PROVEEDOR</th>
                            <th>CTA.CUENTA</th>
                            <th>BANCO ORIGEN</th>
                            <th>BANCO DESTINO</th>
                            <th>DETALLE</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <input
                                type="checkbox"
                                :value="data"
                                v-model="selectedSC"
                              />
                            </td>
                            <td>
                              <span
                                v-if="
                                  data.bandera === null || data.bandera === 0
                                "
                                >PEND.</span
                              >
                              <button
                                v-else-if="data.bandera === 2"
                                class="btn-warning"
                                @click="handleWarning(data)"
                              >
                                ⚠️
                              </button>
                              <span v-else>{{ data.bandera }}</span>
                            </td>
                            <td>{{ data.responsable.toUpperCase() }}</td>
                            <td>{{ data.cuenta }}</td>
                            <td>{{ data.banco_origen }}</td>
                            <td>{{ data.banco_destino.toUpperCase() }}</td>
                            <td>{{ data.detalle.toUpperCase() }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <nav>
            <ul class="pagination">
              <li class="page-item" v-if="pagination.current_page > 1">
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="
                    changePage(pagination.current_page - 1, searchData)
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
                  @click.prevent="changePage(page, searchData)"
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
                    changePage(pagination.current_page + 1, searchData)
                  "
                  >Sig</a
                >
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- MODAL PRINCIPAL DE CONFIRMACION DE CONCILIACION-->
      <div
        class="modal fade"
        id="modal"
        :class="{ mostrar: modal }"
        role="dialog"
      >
        <div class="modal-dialog modal-lg-12">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h4 class="modal-title titulo">{{ modalTitle }}</h4>
              <button
                type="button"
                class="close"
                v-on:click="closeWindow"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body modal-body-format">
              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Fecha de Banco:</label>
                    <input
                      type="date"
                      class="form-control input-format"
                      v-model="model.fecha_si"
                      readonly
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Banco de Origen:</label>
                    <input
                      type="text"
                      class="form-control input-format"
                      v-model="model.bancoo"
                      readonly
                    />
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <label class="lbl-format">Nota:</label>
                  <textarea
                    data-length="256"
                    class="form-control char-textarea textarea-format"
                    placeholder="Glosa / Descripcion:"
                    rows="2"
                    v-model="model.nota"
                  ></textarea>
                  <small class="btn-primary counter-value float-right"
                    ><span class="char-count">0</span> / 255
                  </small>
                </div>

                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <label class="lbl-format">Monto Banco:</label>
                  <textarea
                    data-length="20"
                    class="form-control char-textarea textarea-format"
                    placeholder="Monto:"
                    rows="1"
                    @input="calcularDiferencia()"
                    v-model="model.montobanco"
                  ></textarea>
                </div>

                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <label class="lbl-format">Diferencia:</label>
                  <textarea
                    data-length="20"
                    class="form-control char-textarea textarea-format"
                    :class="{
                      rojo: diferenciaBanco < 0,
                      verde: diferenciaBanco >= 0,
                    }"
                    placeholder="Diferencia:"
                    rows="1"
                    v-model="diferenciaBanco"
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                v-on:click="closeWindowWithoutCleaning"
                class="btn btn-secondary"
              >
                <i class="fa fa-times-circle"></i> Volver
              </button>
              <button
                type="button"
                v-on:click="closeWindow"
                class="btn btn-danger"
              >
                <i class="fa fa-arrow-circle-left"></i> Cancelar
              </button>
              <button
                type="button"
                v-if="modalAction == 1"
                class="btn btn-success"
                v-on:click="saveRegister"
              >
                <i class="fa fa-print"></i> Generar Conciliacion
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- MODAL PRINCIPAL DE CONCILIACIÓN OBSERVADA -->
      <div
        class="modal fade"
        id="modal"
        :class="{ mostrar: modal2 }"
        role="dialog"
      >
        <div class="modal-dialog modal-lg-8">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h4 class="modal-title titulo">{{ modalTitle }}</h4>
              <button
                type="button"
                class="close"
                v-on:click="closeWindow"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body modal-body-format">
              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Fecha de Banco:</label>
                    <input
                      type="date"
                      class="form-control input-format"
                      v-model="model.fecha_si"
                      readonly
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Banco de Origen:</label>
                    <input
                      type="text"
                      class="form-control input-format"
                      v-model="model.bancoo"
                      readonly
                    />
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                  <label class="lbl-format">Nota:</label>
                  <textarea
                    data-length="256"
                    class="form-control char-textarea textarea-format"
                    placeholder="Glosa / Descripcion:"
                    rows="2"
                    v-model="model.nota"
                  ></textarea>
                  <small class="btn-primary counter-value float-right"
                    ><span class="char-count">0</span> / 255
                  </small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                v-on:click="closeWindowWithoutCleaning"
                class="btn btn-secondary"
              >
                <i class="fa fa-times-circle"></i> Volver
              </button>
              <button
                type="button"
                v-on:click="closeWindow"
                class="btn btn-danger"
              >
                <i class="fa fa-arrow-circle-left"></i> Cancelar
              </button>
              <button
                type="button"
                v-if="modalAction == 1"
                class="btn btn-success"
                v-on:click="saveRegister2"
              >
                <i class="fa fa-print"></i> Generar Conciliacion en observacion
              </button>
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
      messageFailed:
        "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
      selectAllOT: false,
      selectedSC: [],
      selectedSCD: [],
      tipoMovimiento: 0,

      diferenciaBanco: 0,
      estado: "",
      model: {
        cod_cr: 0,
        nro_recibo: 0,
        tipoMovimiento: 1,
        estado: "",
        fecha: this.fecha("actual"),
        detalle: [],
      },
      dataDetalle: {
        cod_cr: 0,
        ACTIVO: 1,
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

      // filtros Listado
      tipoFecha: "1",
      searchData: "",
      fechaInicio: "",
      fechaFin: "",
      fechaActual: this.fecha("actual"),

      // modal
      modal: 0,
      modal2: 0,
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
    scrollToDetailTable(index) {
      // Obtener el ID del detalle asociado al índice
      //   const detailId = "#detalle" + index;

      const detailId = $(`#detalle${index}`);
      // Verificar si el elemento con el ID existe
      const $detailTable = $(detailId);

      if ($detailTable.length === 0) {
        // Si el elemento no existe, desplazarse al inicio de la página o a la tabla principal
        $("html, body").animate(
          {
            scrollTop: 0, // Desplazarse al inicio de la página
          },
          500
        ); // Duración de la animación en milisegundos
        return;
      }

      // Si el elemento existe, calcular la posición del detalle y desplazarse hacia él
      const detailTablePosition = $detailTable.offset().top;
      $("html, body").animate(
        {
          scrollTop: detailTablePosition,
        },
        500
      ); // Duración de la animación en milisegundos
    },

    imprimirEXCEL() {
      const params = new URLSearchParams({
        buscar: this.buscar || "", // Usa valores por defecto si no están definidos
        tipoMovimiento: this.tipoMovimiento || "",
        estado: this.estado || "",
        fechaInicio: this.fechaInicio || "",
        fechaFin: this.fechaFin || "",
      }).toString();

      const url = `/exportar-ingresos?${params}`;
      window.location.href = url;
    },
    imprimirEXCEL2() {
      const params = new URLSearchParams({
        buscar: this.buscar || "", // Usa valores por defecto si no están definidos
        // tipoMovimiento: this.tipoMovimiento || '',
        // estado: this.estado || '',
        fechaInicio: this.fechaInicio || "",
        fechaFin: this.fechaFin || "",
      }).toString();

      const url = `/exportar-cobranzas?${params}`;
      window.location.href = url;
    },

    cleanUp() {
      this.camposRequeridos = [];
    },
    cleanForm() {
      let me = this;
      me.selectAllOT = false;
      me.selectedSC = [];
      me.model = {
        cod_cr: 0,
        // fecha: this.fecha('actual'),
      };
    },
    fecha(opc) {
      var result;
      var fecha = new Date(); //Fecha actual
      var mes = fecha.getMonth() + 1; //obteniendo mes
      var dia = fecha.getDate(); //obteniendo dia
      var año = fecha.getFullYear(); //obteniendo año
      var fechaFin = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
      var ultimoDia = fechaFin.getDate();
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
      me.fechaInicio = fecha;
      me.fechaFin = fecha;
      me.fechaActual = me.fecha("actual");
      me.selectedSC = [];
      me.selectedSCD = [];
      me.listData(
        1,
        me.searchData,
        me.fechaInicio,
        me.fechaFin,
        me.tipoMovimiento,
        me.estado
      );
    },
    validateDate() {
      let me = this;
      var tipo = me.tipoFecha;
      switch (tipo) {
        case "1":
          var fecha_actual = me.fecha("actual");
          me.fechaInicio = fecha_actual;
          me.fechaFin = fecha_actual;
          me.listData(
            1,
            me.searchData,
            me.fechaInicio,
            me.fechaFin,
            me.tipoMovimiento,
            me.estado
          );
          break;
        case "2":
          var fecha_inicio = me.fecha("inicio");
          var fecha_fin = me.fecha("fin");
          me.fechaInicio = fecha_inicio;
          me.fechaFin = fecha_fin;
          me.listData(
            1,
            me.searchData,
            me.fechaInicio,
            me.fechaFin,
            me.tipoMovimiento,
            me.estado
          );
          break;
        case "3":
          break;
      }
    },

    validateMainForm() {
      let me = this;

      // Verifica que todos los detalles seleccionados tengan el mismo banco de origen y la misma fecha de solicitud
      const bancoOrigenUniforme = this.selectedSC.every(
        (detalle, _, array) => detalle.banco_origen === array[0].banco_origen
      );
      const fechaSolicitudUniforme = this.selectedSC.every(
        (detalle, _, array) => detalle.fecha === array[0].fecha
      );

      if (!bancoOrigenUniforme) {
        swal(
          "Alerta de Mensaje",
          "Todos los detalles seleccionados deben tener el mismo banco de origen y fecha",
          "warning"
        );
        return false;
      }
      if (!fechaSolicitudUniforme) {
        swal(
          "Alerta de Mensaje",
          "Todos los detalles seleccionados deben tener la misma fecha de solicitud.",
          "warning"
        );
        return false;
      }

      return true;
    },

    listData(page, buscar, fechaInicio, fechaFin, tipoMovimiento, estado) {
      let me = this;
      me.aData = [];
      var url = `/api/conciliacionS/ingresos?page=${page}&buscar=${buscar}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&tipoMovimiento=${tipoMovimiento}&estado=${estado}`;
      axios
        .get(url)
        .then(function (response) {
          // Ya no buscamos 'response.data.result', sino que accedemos directamente a 'response.data'
          var resp = response.data; // Accedemos directamente a la respuesta que esperamos.

          me.aData = resp.data; // Asignamos los datos a 'aData'.

          // Configuramos la paginación basada en la respuesta de la API
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

    changePage(page, buscar) {
      let me = this;
      me.pagination.current_page = page;
      me.listData(
        page,
        buscar,
        me.fechaInicio,
        me.fechaFin,
        me.tipoMovimiento,
        me.estado
      );
    },
    changeRowColor(id) {
      $("#" + id + " tr").click(function (e) {
        $("#" + id + " tr").removeClass("selected");
        $(this).addClass("selected");
      });
    },
    saveRegister() {
      if (!this.validateMainForm()) {
        // Si la validación falla, detiene la ejecución del método
        return;
      }
      // Recolectar los detalles seleccionados
      let detallesParaEnviar = this.selectedSC.map((detalle) => {
        return {
          id_solicitud: detalle.id_solicitud,
          id_detalle_solicitud: detalle.id_detalle_solicitud,
          tipo: detalle.tipo,
          // cod_detalle: detalle.cod_detalle,
          fecha_si: detalle.fecha,
          bancod: detalle.banco_destino,
          bancoo: detalle.banco_origen,
          totalbs: detalle.totalbs,
          cod_si: detalle.cod_si,
        };
      });

      // Añadir la nota del formulario
      let nota = this.model.nota;
      let montobanco = this.model.montobanco;
      // Enviar los datos al backend
      axios
        .post("/api/conciliacionS/registrarci", {
          detalles: detallesParaEnviar,
          nota: nota,
          montobanco: montobanco,
        })
        .then((response) => {
          // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
          swal("Conciliaciones creadas correctamente.");
          this.closeWindow();
          this.listData(
            1,
            this.searchData,
            this.fechaInicio,
            this.fechaFin,
            this.tipoMovimiento,
            this.estado
          );
          this.selectedSCD = [];
          this.selectedSC = [];
          this.scrollToDetailTable(0);

          // alert('Conciliaciones creadas correctamente.');
        })
        .catch((error) => {
          // Aquí manejas posibles errores en la solicitud
          console.error(error);
        });
    },
    saveRegister2() {
      if (!this.validateMainForm()) {
        // Si la validación falla, detiene la ejecución del método
        return;
      }
      // Recolectar los detalles seleccionados
      let detallesParaEnviar = this.selectedSC.map((detalle) => {
        return {
          id_solicitud: detalle.id_solicitud,
          id_detalle_solicitud: detalle.id_detalle_solicitud,
          // cod_detalle: detalle.cod_detalle,
          tipo: detalle.tipo,
          fecha_si: detalle.fecha,
          bancod: detalle.banco_destino,
          bancoo: detalle.banco_origen,
          totalbs: detalle.totalbs,
          cod_si: detalle.cod_si,
        };
      });

      // Añadir la nota del formulario
      let nota = this.model.nota;

      // Enviar los datos al backend
      axios
        .post("/api/conciliacionS/registrarcio", {
          detalles: detallesParaEnviar,
          nota: nota,
        })
        .then((response) => {
          // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
          swal("Conciliaciones creadas correctamente.");
          this.closeWindow();
          this.listData(
            1,
            this.searchData,
            this.fechaInicio,
            this.fechaFin,
            this.tipoMovimiento,
            this.estado
          );
          // alert('Conciliaciones creadas correctamente.');
        })
        .catch((error) => {
          // Aquí manejas posibles errores en la solicitud
          console.error(error);
        });
    },
    openPDFRecibotoPrint(id) {
      var pagina =
        "http://mip2024.isitecnologia.com/api/conciliacionS/pdf/" + id;
      // var pagina = "http://127.0.0.1:8000/api/conciliacionS/pdf/"+id;
      var opciones =
        "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
      window.open(pagina, "", opciones);
    },

    calcularDiferencia() {
      let me = this;
      me.diferenciaBanco = 0;
      let diferencia = 0;
      diferencia =
        parseFloat(this.model.montobanco) -
        parseFloat(me.selectedSC[0].totalbs);
      me.diferenciaBanco =
        diferencia >= 0 ? "+" + diferencia : diferencia.toString();
    },
    // Modal campo
    openWindow(action, data = []) {
      let me = this;
      me.cleanUp();
      switch (action) {
        case "registrar": {
          if (me.selectedSC.length >= 1) {
            me.diferenciaBanco = 0;
            me.modal = 1;
            me.modalTitle = "CONFIRMACION DE CONCILIACION";
            me.modalAction = 1;
            // Aquí estableces los valores por defecto basados en el primer detalle seleccionado
            me.model.fecha_si = me.selectedSC[0].fecha; // Asume que 'fecha' es tu 'fecha_si'
            me.model.bancoo = me.selectedSC[0].bancoo; // Usar 'bancoo' del detalle seleccionado como 'banco_origen'

            me.setForm(me.selectedSC);
          } else {
            swal(
              "Alerta de Mensaje",
              "Debe Seleccionar Mínimo un Registro.",
              "warning"
            );
          }
          break;
        }
      }
    },
    openWindow2(action, data = []) {
      let me = this;
      me.cleanUp();
      switch (action) {
        case "registrar2": {
          if (me.selectedSC.length >= 1) {
            me.modal2 = 1;
            me.modalTitle = "CONFIRMACION DE CONCILIACION OBSERVACION";
            me.modalAction = 1;
            // Aquí estableces los valores por defecto basados en el primer detalle seleccionado
            me.model.fecha_si = me.selectedSC[0].fecha; // Asume que 'fecha' es tu 'fecha_si'
            me.model.bancoo = me.selectedSC[0].bancoo; // Usar 'bancoo' del detalle seleccionado como 'banco_origen'
            me.setForm(me.selectedSC);
          } else {
            swal(
              "Alerta de Mensaje",
              "Debe Seleccionar Mínimo un Registro.",
              "warning"
            );
          }
          break;
        }
      }
    },

    setForm(data) {
      let me = this;
      for (let i = 0; i < data.length; i++) {
        // me.model.detalle.push({
        //     cod_cr: 0,
        //     ACTIVO: 1,
        // });
      }
    },
    closeWindow() {
      this.modal = 0;
      this.modal2 = 0;
      this.modalCancel = 0;
      this.modalTitle = "";
      this.modalMessage = "";
      this.cleanForm();
    },
    closeWindowWithoutCleaning() {
      this.modal = 0; // Cerrar el modal.
      this.modal2 = 0;
      this.modalMessage = "";
    },
    bugChecking(error) {
      var array = [];
      // Verifica primero si existe error.response
      if (error.response) {
        if (error.response.status == 422) {
          array = error.response.data.errors;
        } else if (error.response.status == 419) {
          this.cerrarSesion();
        } else {
          // Manejar otros códigos de estado HTTP aquí si es necesario
          var cadena = String(error);
          cadena = cadena.substr(cadena.length - 3, 3);
          swal("Message: ", this.messageFailed + " " + cadena, "error");
        }
      } else {
        // Maneja casos donde error.response no está definido
        console.error("Error: ", error.message || "Unknown error");
        // Aquí podrías mostrar un mensaje genérico al usuario
        swal(
          "Error",
          "Ocurrió un error inesperado. Por favor, intenta nuevamente.",
          "error"
        );
      }
      return array;
    },
    // bugChecking(error) {
    // var array=[];
    // var cadena = String(error);
    //     if(error.response.status==422){
    //         array  = error.response.data.errors;
    //     } else {
    //         if(error.response.status==419) {
    //             this.cerrarSesion();
    //         } else {
    //             var cadena = String(error);
    //             cadena = cadena.substr(cadena.length-3,3);
    //             swal("Message: ", this.messageFailed +" "+cadena, "error");
    //         }
    //     }
    //     return array;
    // },
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
Vue.filter("formatNumber", function (value) {
  if (!value) return "0";
  return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
});
</script>

<style scoped>
.tabla th {
  padding: 10px 10px;
}

.tabla td {
  padding: 10px 10px;
}
</style>

<style>
/* .tabla th {
  padding: 10px 10px; 
}
.tabla td {
  padding: 10px 10px; 
} */
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

.rojo {
  border-color: red; /* Cambiar el color del borde a rojo */
}

.verde {
  border-color: green; /* Cambiar el color del borde a verde */
}
</style>