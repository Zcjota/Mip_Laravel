<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class=""></ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h1 style="text-align: center"><b>LISTADO DE GASTO AL CONTADO</b></h1>
          <br />
          <div class="row">
            <div class="col-md-3">
              <button
                type="button"
                class="btn btn-success square"
                v-on:click="openWindow('registrar')"
                title="Nuevo Registro"
              >
                <i class="fa fa-plus" aria-hidden="true"></i>
                NUEVO
              </button>
            </div>

            <div class="col-md-9">
              <div class="input-group">
                <select
                  v-model="tipoEstado"
                  class="form-control square"
                  style="width: 100px"
                  v-on:change="validateDate"
                >
                  <option value="ALL">TODOS</option>
                  <option value="INI">INICIAL</option>
                  <option value="APR">APROBADO</option>
                  <option value="ANU">ANULADO</option>
                  <option value="REC">RECHAZADO</option>
                </select>
                <select
                  v-model="tipoGasto"
                  class="form-control square"
                  style="width: 70px"
                  v-on:change="validateDate"
                >
                  <option value="">TODOS</option>
                  <option value="F">FIJO</option>
                  <option value="E">EVENTUAL</option>
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
                      tipoEstado,
                      tipoGasto
                    )
                  "
                >
                  <i class="fa fa-search" aria-hidden="true"></i> Buscar
                </button>
              </div>
            </div>
          </div>
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
                  validaSeleccioCombo(
                    1,
                    fechaInicio,
                    fechaFin,
                    selectedProveedor,
                    selectedOption
                  )
                "
              >
                <template #option="{ title }">
                  <span> {{ title }}</span>
                </template>
              </v-select>
            </div>
            <div class="col-md-6">
              <!-- test -->
              <v-select
                v-model="selectedOption"
                :options="options"
                label="title"
                searchable
                placeholder="Seleccionar Cuenta Contable"
                @input="
                  validaSeleccioCombo(
                    1,
                    fechaInicio,
                    fechaFin,
                    selectedProveedor,
                    selectedOption
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
                <table id="mainTableSG" class="tabla">
                  <thead>
                    <tr>
                      <th style="width: 20px"></th>
                      <th style="width: 20px"></th>
                      <th style="width: 20px"></th>
                      <th>FECHA</th>
                      <th>ESTADO</th>
                      <th>NRO</th>
                      <th>TIPO PAGO</th>
                      <th>TIPO GASTO</th>
                      <th style="width: 60px">TOTAL BS</th>
                      <th style="width: 60px">TOTAL USD</th>
                      <th>GLOSA</th>
                      <!-- <th>FECHA REGISTRO</th> -->
                      <th>REVISADO POR</th>
                      <!-- <th>PROVEEDOR/PERSONAL</th> -->
                      <th>CUENTA CONTABLE</th>
                      <!-- <th>FECHA REVISION</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(data, index) in aData"
                      :key="index"
                      style="height: 50px"
                      v-on:click="changeRowColor('mainTableSG')"
                    >
                      <td>
                        <button
                          v-if="data.ACTIVO == 1 && data.estado == 'INI'"
                          type="button"
                          v-on:click="openWindow('actualizar', data)"
                          class="btn btn-warning"
                        >
                          <i class="icon-pencil"></i>
                        </button>
                        <button
                          v-else
                          disabled
                          type="button"
                          class="btn btn-warning"
                        >
                          <i class="icon-pencil"></i>
                        </button>
                      </td>
                      <td v-if="data.ACTIVO == 1 && data.estado == 'INI'">
                        <button
                          type="button"
                          class="btn btn-danger colorRojo"
                          v-on:click="openWindow('anular', data)"
                        >
                          <i class="fa fa-times"></i>
                        </button>
                      </td>
                      <td v-else>
                        <button disabled type="button" class="btn btn-danger">
                          <i class="fa fa-times"></i>
                        </button>
                      </td>
                      <td>
                        <button
                          v-if="
                            data.ACTIVO == 1 &&
                            (data.estado == 'INI' || data.estado == 'APR')
                          "
                          type="button"
                          v-on:click="openPDFtoPrint(data)"
                          class="btn btn-dark"
                        >
                          <i class="fa fa-print"></i>
                        </button>
                        <button
                          v-else
                          disabled
                          type="button"
                          class="btn btn-darck"
                        >
                          <i class="fa fa-file-pdf-o"></i>
                        </button>
                      </td>
                      <td>{{ data.fecha_sg }}</td>
                      <td>{{ data.estado }}</td>
                      <td>{{ data.nroCorrelativo }}</td>
                      <td>{{ data.tipo_pago }}</td>
                      <td>
                        <span v-if="data.tipo_gasto === 'E'">EVENTUAL</span>
                        <span v-else-if="data.tipo_gasto === 'F'">FIJO</span>
                      </td>
                      <!-- <td>{{data.tipo_gasto}}</td> -->
                      <td>{{ data.total_bs }}</td>
                      <td>{{ data.total_usd }}</td>
                      <td>{{ data.glosa }}</td>
                      <!-- <td>{{ data.fecha_registro }}</td> -->
                      <td>{{ data.revisado_por_usuario }}</td>
                      <!-- <td>{{ data.nombre_completo}}</td> -->
                      <td>{{ data.descripcion }}</td>
                      <!-- <td>{{ data.fecha_revision }}</td> -->
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="x_title"></div>
            </div>
          </div>
          <div v-if="isSpinner" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Cargando {{ textoCarga }} ...</strong>
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
      <!-- MODAL PRINCIPAL DE SOLICITUD -->
      <div
        class="modal fade"
        id="modal"
        :class="{ mostrar: modal }"
        role="dialog"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content modal-content-format">
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
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Fecha Solicitud:</label>
                    <input
                      type="date"
                      class="form-control input-format"
                      v-model="model.fecha_sg"
                    />
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Solicitado:</label>
                    <input
                      v-if="modalAction == 1"
                      disabled
                      type="text"
                      class="form-control input-format"
                      v-model="$root.nombre_usuario"
                    />
                    <input
                      v-else
                      disabled
                      type="text"
                      style="font-size: 14px; font-weight: bold"
                      class="form-control input-format"
                      v-model="nombreUsuario"
                    />
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Tipo Pago:</label>
                    <select
                      class="form-control select-format"
                      v-html="htmlListTipoPago"
                      v-model="model.tipo_pago"
                    ></select>
                  </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Tipo Gasto:</label>
                    <select
                      class="form-control select-format"
                      v-html="htmlListTipoGasto"
                      v-model="model.tipo_gasto"
                    ></select>
                  </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format">Banco origen:</label>
                    <select
                      v-model="model.cod_bc"
                      class="form-control select-format"
                    >
                      <option disabled :value="-1">Seleccione..</option>
                      <option
                        v-for="data in aDataBanckAcount"
                        :key="data.cod_bc"
                        :value="data.cod_bc"
                      >
                        {{ data.sigla }}-{{ data.nro_cuenta.slice(-5) }}-{{
                          data.moneda
                        }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label class="lbl-format"
                      >Aceptar vinculación de crédito:</label
                    >
                    <div>
                      <input
                        type="radio"
                        id="no"
                        value="0"
                        v-model="model.vc"
                        checked
                      />
                      <label for="no">No</label>
                    </div>
                    <div>
                      <input
                        type="radio"
                        id="yes"
                        value="1"
                        v-model="model.vc"
                      />
                      <label for="yes">Sí</label>
                    </div>
                  </div>
                </div>

                <div
                  v-if="modalAction == 2"
                  class="col-lg-1 col-md-1 col-sm-1 col-xs-12"
                >
                  <label class="lbl-format">{{ numeroSolicitud }}</label>
                </div>
              </div>
              <div class="row div-table-format">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <table
                    class="table table-hover-animation mb-0"
                    style="width: 100%"
                  >
                    <thead class="thead-dark">
                      <tr
                        style="
                          background-color: #243648f0;
                          color: white;
                          height: 40px;
                        "
                      >
                        <td class="table-thead-td" style="display: none">ID</td>
                        <td class="table-thead-td">PROVEEDOR</td>
                        <td class="table-thead-td">CTA. CONTABLE</td>
                        <td class="table-thead-td" style="width: 200px">
                          DETALLE
                        </td>
                        <td class="table-thead-td" align="right">BS</td>
                        <td class="table-thead-td" align="right">USD</td>
                        <td class="table-thead-td">BANCO DESTINO</td>
                        <td style="width: 10px"></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="model.detail.length === 0">
                        <td class="has-text-centered" colspan="7">
                          No se han agregado productos
                        </td>
                      </tr>
                      <tr
                        v-else
                        v-for="data in model.detail"
                        :key="data.cod_proveedor"
                      >
                        <td class="table-tbody-td" style="display: none">
                          {{ data.id }}
                        </td>
                        <td class="table-tbody-td">
                          {{ data.nombre_proveedor }}
                        </td>
                        <td class="table-tbody-td">{{ data.nombre_cc }}</td>
                        <td class="table-tbody-td" style="width: 200px">
                          {{ data.detalle.toUpperCase() }}
                        </td>
                        <td class="table-tbody-td" align="right">
                          {{ data.importe_bs.toFixed(2) }}
                        </td>
                        <td class="table-tbody-td" align="right">
                          {{ data.importe_usd.toFixed(2) }}
                        </td>
                        <td class="table-tbody-td">{{ data.nombre_bc }}</td>
                        <td>
                          <button
                            v-on:click="removeDetail(data.id)"
                            type="button"
                            class="btn btn-icon btn-danger"
                          >
                            <i class="fa fa-times" aria-hidden="true"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td class="table-tfoot-td" colspan="3" align="right">
                          TOTAL:
                        </td>
                        <td class="table-tfoot-td" align="right">
                          Bs {{ totalBS.toFixed(2) }}
                        </td>
                        <td class="table-tfoot-td" align="right">
                          USD {{ totalUSD.toFixed(2) }}
                        </td>
                        <td class="table-tfoot-td"></td>
                        <td class="table-tfoot-td"></td>
                      </tr>
                      <tr>
                        <td>
                          <div class="input-group">
                            <input
                              type="text"
                              disabled="disabled"
                              class="form-control"
                              v-model="newDetail.nombreProveedor"
                            />
                            <button
                              type="button"
                              class="btn btn-icon square btn-primary"
                              title="Buscar Responsable/Proveedor"
                              v-on:click="openSearchProvider"
                            >
                              <i class="feather icon-search" aria-hidden="true"
                                >B</i
                              >
                            </button>
                          </div>
                        </td>
                        <td>
                          <div class="input-group">
                            <input
                              type="text"
                              disabled="disabled"
                              class="form-control"
                              v-model="newDetail.nombreCuenta"
                            />
                            <button
                              type="button"
                              class="btn btn-icon square btn-primary"
                              title="Buscar Cuenta Contable"
                              v-on:click="openSearchAcount"
                            >
                              <i class="feather icon-search" aria-hidden="true"
                                >B</i
                              >
                            </button>
                          </div>
                        </td>
                        <td style="width: 200px">
                          <div class="control">
                            <textarea
                              v-model="newDetail.detalle"
                              class="form-control textarea-detalle-format"
                              row="2"
                              placeholder="DETALLE:"
                            ></textarea>
                          </div>
                        </td>
                        <td style="width: 25px">
                          <div class="control">
                            <input
                              v-model="newDetail.importeBs"
                              type="number"
                              class="input-detalle-format"
                              placeholder="Precio BS"
                            />
                          </div>
                        </td>
                        <td style="width: 25px">
                          <div class="control">
                            <input
                              v-model="newDetail.importeUSD"
                              type="number"
                              class="input-detalle-format"
                              placeholder="Precio USD"
                            />
                          </div>
                        </td>
                        <td>
                          <div class="control">
                            <select
                              v-model="newDetail.codCuentaBanco"
                              class="form-control select-format"
                            >
                              <option disabled :value="-1">Seleccione..</option>
                              <option
                                v-for="data in aDataBanckAcountDestiny"
                                :key="data.cod_bc"
                                :value="data.cod_bc"
                              >
                                {{ data.sigla }}-{{
                                  data.nro_cuenta.slice(-5)
                                }}-{{ data.moneda }}
                              </option>
                            </select>
                          </div>
                        </td>
                        <td align="right">
                          <button
                            v-bind:disabled="newDetail.codProveedor == -1"
                            v-on:click="addDetail"
                            class="btn btn-success colorVerde"
                          >
                            <i class="fa fa-plus" aria-hidden="true"></i>
                          </button>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <textarea
                    data-length="256"
                    class="form-control char-textarea textarea-format"
                    placeholder="Glosa / Descripcion:"
                    rows="2"
                    v-model="model.glosa"
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
                v-on:click="closeWindow"
                class="btn btn-danger"
              >
                <i class="fa fa-arrow-circle-left"></i> Cancelar
              </button>
              <!-- <button type="button" v-if="modalAction==1" class="btn btn-success"
                                v-on:click="saveRegister">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button> -->
              <button
                type="button"
                v-if="modalAction == 1"
                class="btn btn-success"
                v-on:click="saveRegister"
                v-bind:disabled="buttonDisabled"
              >
                <i class="fa fa-floppy-o"></i> Guardar
              </button>
              <button
                type="button"
                v-else
                class="btn btn-success"
                v-on:click="updateRegister"
              >
                <i class="fa fa-floppy-o"></i> Actualizar
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal de Busquedas -->
      <!-- PROVEEDOR -->
      <div class="modal fade" :class="{ mostrar: modalProvider }" role="dialog">
        <div
          class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
          role="document"
        >
          <div class="modal-content model-content-format-2">
            <div class="modal-header bg-success">
              <h3 class="modal-title titulo">Personal/Proveedor</h3>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                v-on:click="closeSearchProvider"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="panel-body table-responsive">
                <div class="input-group">
                  <!-- <input type="text" v-model="searchProvider" class="form-control" placeholder="Buscar Cuenta Contable">
                                        <input type="text" v-model="searchProvider" class="form-control" placeholder="Buscar Cuenta Contable" @keyup.enter="listProvider(1, searchProvider)">
                                        <button type="submit" class="btn btn-primary" v-on:click="listProvider(1, searchProvider)">
                                            <i class="fa fa-search"></i> Buscar -->
                  <input
                    type="text"
                    v-model="searchProvider"
                    class="form-control"
                    placeholder="Buscar Cuenta Contable"
                    @input="listProvider(1, searchProvider)"
                  />
                  <button
                    type="submit"
                    class="btn btn-primary"
                    @click="listProvider(1, searchProvider)"
                  >
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
                <table
                  class="table table-striped table-hover-animation mb-0"
                  style="width: 100%"
                >
                  <thead class="thead-dark">
                    <tr>
                      <th style="width: 20px"></th>
                      <th>TIPO</th>
                      <th>NOMBRES</th>
                      <th>FONDO</th>
                      <th>CUENTA</th>
                      <th>BANCO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="aDataProvider.length === 0">
                      <td class="has-text-centered" colspan="4">
                        No se han Encontrado Registros
                      </td>
                    </tr>
                    <tr v-else v-for="data in aDataProvider" :key="data.id">
                      <td>
                        <button
                          v-on:click="selectSearchProvider(data)"
                          type="button"
                          class="btn btn-icon btn-primary"
                        >
                          <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                      </td>
                      <td>{{ data.tipoPersona }}</td>
                      <td>{{ data.nombre_completo }}</td>
                      <td>{{ data.fondoDescripcion }}</td>
                      <td>
                        {{ data.NRO_CUENTA }}-{{ data.cuentaDescripcion }}
                      </td>
                      <td>
                        {{ data.sigla }}-{{
                          data.bancoCuenta_nro_cuenta.slice(-5)
                        }}-{{ data.moneda }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <nav>
              <ul class="pagination">
                <li
                  class="page-item"
                  v-if="paginationProvider.current_page > 1"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      changePageProvider(
                        paginationProvider.current_page - 1,
                        searchProvider
                      )
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
                    @click.prevent="changePageProvider(page, searchProvider)"
                    >{{ page }}</a
                  >
                </li>
                <li
                  class="page-item"
                  v-if="
                    paginationProvider.current_page <
                    paginationProvider.last_page
                  "
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      changePageProvider(
                        paginationProvider.current_page + 1,
                        searchProvider
                      )
                    "
                    >Sig</a
                  >
                </li>
              </ul>
            </nav>
            <div class="modal-footer">
              <button
                type="button"
                v-on:click="closeSearchProvider"
                class="btn btn-danger"
              >
                <i class="fa fa-arrow-circle-left"></i> Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- CUENTA CONTABLE -->
      <div class="modal fade" :class="{ mostrar: modalAcount }" role="dialog">
        <div
          class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
          role="document"
        >
          <div class="modal-content model-content-format-2">
            <div class="modal-header bg-success">
              <h3 class="modal-title titulo">Cuenta Contable</h3>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                v-on:click="closeSearchAcount"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="input-group">
                <input
                  type="text"
                  v-model="searchAcount"
                  class="form-control"
                  placeholder="Buscar Cuenta Contable"
                  @input="listAcount(1, searchAcount)"
                />
                <button
                  type="submit"
                  class="btn btn-primary"
                  @click="listAcount(1, searchAcount)"
                >
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
              <div class="panel-body table-responsive">
                <table
                  class="table table-striped table-hover-animation mb-0"
                  style="width: 100%"
                >
                  <thead class="thead-dark">
                    <tr>
                      <th style="width: 20px"></th>
                      <th>ID</th>
                      <th>FONDO</th>
                      <th>NRO CUENTA</th>
                      <th>CUENTA</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="aDataAcount.length === 0">
                      <td class="has-text-centered" colspan="5">
                        No se han Encontrado Registros
                      </td>
                    </tr>
                    <tr
                      v-else
                      v-for="data in aDataAcount"
                      :key="data.COD_CUENTA"
                    >
                      <td>
                        <button
                          v-on:click="selectSearchAcount(data)"
                          type="button"
                          class="btn btn-icon btn-primary"
                        >
                          <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                      </td>
                      <td>{{ data.COD_CUENTA }}</td>
                      <td>{{ data.fondoDescripcion }}</td>
                      <td>{{ data.NRO_CUENTA }}</td>
                      <td>{{ data.cueDescripcion }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <nav>
              <ul class="pagination">
                <li class="page-item" v-if="paginationAcount.current_page > 1">
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      changePageAcount(
                        paginationAcount.current_page - 1,
                        searchAcount
                      )
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
                    @click.prevent="changePageAcount(page, searchAcount)"
                    >{{ page }}</a
                  >
                </li>
                <li
                  class="page-item"
                  v-if="
                    paginationAcount.current_page < paginationAcount.last_page
                  "
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="
                      changePageAcount(
                        paginationAcount.current_page + 1,
                        searchAcount
                      )
                    "
                    >Sig</a
                  >
                </li>
              </ul>
            </nav>
            <div class="modal-footer">
              <button
                type="button"
                v-on:click="closeSearchAcount"
                class="btn btn-danger"
              >
                <i class="fa fa-arrow-circle-left"></i> Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal de Anulacion -->
      <div
        class="modal fade"
        :class="{ mostrar: modalCancel }"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-danger" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" v-text="modalTitle"></h3>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                v-on:click="closeWindow"
              >
                <span aria-hidden="true">x</span>
              </button>
            </div>
            <div class="modal-body" style="height: 100px">
              <h4 v-text="modalMessage"></h4>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                v-on:click="closeWindow"
                class="btn btn-success"
              >
                <i class="fa fa-arrow-circle-left"></i> Cancelar
              </button>
              <button
                type="button"
                class="btn btn-warning"
                v-on:click="unregister(model.cod_sg)"
              >
                <i class="fa fa-times"></i> Aceptar
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
      isSpinner: false,
      textoCarga: "",
      selectedOption: null,
      options: [],
      selectedProveedor: null,
      optionsProveedor: [],

      modalAction: 1,
      buttonDisabled: false,
      messageFailed:
        "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
      nombreUsuario: "",
      numeroSolicitud: "",
      newDetail: {
        id: 1,
        codCuentaContable: -1,
        nombreCuenta: "",
        codProveedor: -1,
        nombreProveedor: "",
        detalle: "",
        importeBs: 0,
        importeUSD: 0,
        codCuentaBanco: -1,
        nombreCuentaBanco: "",
      },
      model: {
        cod_sg: 0,
        tipo_sg: "CONTADO",
        fecha_sg: this.fecha("actual"),
        fecha_registro: "",
        cod_usuario: -1,
        nro_sg: "",
        gestion: "",
        tipo_pago: "TRANSFERENCIA",
        tipo_gasto: "F",
        vc: 0,
        cod_bc: -1,
        total_bs: 0,
        total_usd: 0,
        fecha_pago: "",
        glosa: "",
        estado: "",
        revisado_por_usuario: -1,
        fecha_revision: "",
        ACTIVO: 1,
        detail: [],
      },

      //Datos de tablas
      aData: [],
      aDataBanckAcount: [],
      aDataBanckAcountDestiny: [],
      aDataProvider: [],
      aDataAcount: [],

      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      paginationProvider: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      paginationAcount: {
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
      searchData: "",
      fechaInicio: "",
      fechaFin: "",
      fechaActual: this.fecha("actual"),
      tipoEstado: "ALL",
      tipoGasto: "F",
      // filtros Cuenta
      searchAcount: "",
      pageAcount: 1,

      // filtros Proveedor
      searchProvider: "",
      pageProvider: 1,

      // modal
      modal: 0,
      modalCancel: 0,
      modalTitle: "",
      modalAction: 0,
      modalMessage: "",

      //modal proveedor
      modalProvider: 0,
      providerSelect: "",
      searchProviderFilter: "",

      //modal cuenta contable
      modalAcount: 0,
      acountSelect: "",
      searchAcountFilter: "",

      htmlListTipoPago: "",
      htmlListTipoGasto: "",
      htmlListBanco: "",
      htmlListBancoCuenta: "",
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
    totalBS() {
      let total = 0;
      this.model.detail.forEach((x) => {
        total += x.importe_bs;
      });
      this.model.total_bs = total;
      return total;
    },
    totalUSD() {
      let total = 0;
      this.model.detail.forEach((x) => {
        total += x.importe_usd;
      });
      this.model.total_usd = total;
      return total;
    },
  },
  created() {},
  methods: {
    cleanUp() {
      this.camposRequeridos = [];
    },
    cleanForm() {
      let me = this;
      me.newDetail.id = 1;
      me.newDetail.codCuentaContable = -1;
      me.newDetail.nombreCuenta = "";
      me.newDetail.codProveedor = -1;
      me.newDetail.nombreProveedor = "";
      me.newDetail.detalle = "";
      me.newDetail.importeBs = 0;
      me.newDetail.importeUSD = 0;
      me.newDetail.codCuentaBanco = -1;
      me.newDetail.nombreCuentaBanco = "";

      me.model.cod_sg = 0;
      me.model.tipo_sg = "CONTADO";
      me.model.fecha_sg = this.fecha("actual");
      me.model.fecha_registro = "";
      me.model.cod_usuario = -1;
      me.model.nro_sg = "";
      me.model.gestion = "";
      me.model.tipo_pago = "TRANSFERENCIA";
      me.model.tipo_gasto = "F";
      me.model.vc = 0;
      me.model.cod_bc = -1;
      me.model.total_bs = 0;
      me.model.total_usd = 0;
      me.model.fecha_pago = "";
      me.model.glosa = "";
      me.model.estado = "";
      me.model.revisado_por_usuario = -1;
      me.model.fecha_revision = "";
      me.model.ACTIVO = 1;
      me.model.detail = [];
      me.nombreUsuario = "";
      me.numeroSolicitud = "";
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
    openPDFtoPrint(data) {
      var pagina =
        "http://mip2024.isitecnologia.com/api/sgContadoPDF/" + data.cod_sg;
      // var pagina = "http://127.0.0.1:8000/api/sgContadoPDF/"+data.cod_sg;
      var opciones =
        "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
      window.open(pagina, "", opciones);
    },
    initialize() {
      let me = this;
      var fecha = me.fecha("actual");
      me.tipoFecha = "1";
      me.fechaInicio = fecha;
      me.fechaFin = fecha;
      me.fechaActual = me.fecha("actual");
      me.listData(
        1,
        me.searchData,
        me.fechaInicio,
        me.fechaFin,
        me.tipoEstado,
        me.tipoGasto
      );
      me.listProvider(me.pageProvider, me.searchProvider);
      me.cbxCuentaProveedor(me.pageProvider, me.searchProvider);
      me.listAcount(me.pageAcount, me.searchAcount);
      me.cbxCuentaContable(me.pageAcount, me.searchAcount);
      me.listDataBankAcount();
      me.listDataBankAcountDestiny();
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
            me.tipoEstado,
            me.tipoGasto
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
            me.tipoEstado,
            me.tipoGasto
          );
          break;
        case "3":
          break;
      }
    },
    validateNewDetail() {
      let me = this;
      if (me.newDetail.codProveedor == -1) {
        swal(
          "Alerta de Mensaje",
          "Busque y Seleccione un Proveedor.",
          "warning"
        );
        return false;
      }
      if (me.newDetail.codCuentaContable == -1) {
        swal(
          "Alerta de Mensaje",
          "Busque y Seleccione una Cuenta Contable.",
          "warning"
        );
        return false;
      }
      if (me.newDetail.detalle == "") {
        swal("Alerta de Mensaje", "Ingrese un detalle en el campo.", "warning");
        return false;
      } else if (me.newDetail.detalle.length < 10) {
        swal(
          "Alerta de Mensaje",
          "El Detalle Debe Tener un Mínimo de 10 Caracteres.",
          "warning"
        );
        return false;
      }
      if (
        (me.newDetail.importeBs == "" || me.newDetail.importeBs <= 0) &&
        (me.newDetail.importeUSD == "" || me.newDetail.importeUSD <= 0)
      ) {
        swal(
          "Alerta de Mensaje",
          "Ingrese un Importe Bs ó importe USD Validos.",
          "warning"
        );
        return false;
      }
      if (me.model.tipo_pago == "TRANSFERENCIA") {
        if (me.newDetail.codCuentaBanco == -1) {
          swal(
            "Alerta de Mensaje",
            "Seleccione una Cuenta de Banco Destino.",
            "warning"
          );
          return false;
        }
      }
      return true;
    },
    validateMainForm() {
      let me = this;
      if (me.model.cod_bc == -1) {
        swal(
          "Alerta de Mensaje",
          "Seleccione un Cuenta Banco Origen.",
          "warning"
        );
        return false;
      }
      if (me.model.glosa == "") {
        swal(
          "Alerta de Mensaje",
          "Ingrese una glosa para la Solicitud.",
          "warning"
        );
        return false;
      } else if (me.model.glosa.length < 15) {
        swal(
          "Alerta de Mensaje",
          "La Glosa Debe Tener un Mínimo de 15 Caracteres.",
          "warning"
        );
        return false;
      }
      if (me.model.total_bs == 0 && me.model.total_usd == 0) {
        swal(
          "Alerta de Mensaje",
          "Ingrese Mínimo un Detalle para la Solicitud.",
          "warning"
        );
        return false;
      }
      return true;
    },

    validaSeleccioCombo(page, fechaInicio, fechaFin, proveedor, cc) {
      if (this.selectedOption != null && this.selectedProveedor != null) {
        this.ProveedorCuentaContable(
          page,
          fechaInicio,
          fechaFin,
          proveedor,
          cc
        );
        console.log("ambos");
      }
      if (this.selectedOption != null && this.selectedProveedor === null) {
        this.TraerProveedorCC(page, fechaInicio, fechaFin, cc);
        console.log("cuenta");
      }
      if (this.selectedOption === null && this.selectedProveedor != null) {
        this.TraerPorproveedor(page, fechaInicio, fechaFin, proveedor);
        console.log("proveedor");
      }
    },

    listData(page, buscar, fechaInicio, fechaFin, estado, tipoGasto, vc) {
      let me = this;
      var tipoSolicitud = "CONTADO";
      me.aData = [];
      this.selectedProveedor = null;
      this.selectedOption = null;
      me.isSpinner = true;
      this.textoCarga = "Datos Generales";
      me.aData = [];
      var url =
        "/api/ZSolicitudGasto/Buscar?page=" +
        page +
        "&buscar=" +
        buscar +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin +
        "&estado=" +
        estado +
        "&tipo_sg=" +
        tipoSolicitud +
        "&tipo_gasto=" +
        tipoGasto +
        "&vc=" +
        vc;
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result;
          me.aData = resp.data;

          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
          me.isSpinner = false;
        })
        .catch(function (error) {
          me.isSpinner = false;
          console.log(error);
        });
    },

    listProvider(page, buscar) {
      let me = this;
      var url =
        "/api/ZProv_Pers/ListarCuentaCbx?page=" + page + "&buscar=" + buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data.result;
          me.aDataProvider = resp.data;
          me.paginationProvider = {
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
    cbxCuentaContable(page, buscar) {
      let me = this;
      me.options = [];
      var lista = [];
      var url = "/api/Cuenta/Buscar?page=" + page + "&buscar=" + buscar;
      const axios = require("axios");
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result.data;
          for (let i = 0; i < resp.length; i++) {
            lista.push({
              id: resp[i].COD_CUENTA,
              title: resp[i].cueDescripcion,
              icon: "ListIcon",
            });
          }
          me.options = lista;
        })
        .catch((e) => {
          console.log("cbxMarca " + e);
        });
    },
    ProveedorCuentaContable(page, fechaInicio, fechaFin, proveedor, cc) {
      let me = this;

      var lista = [];
      me.aData = [];
      this.isSpinner = true;
      this.textoCarga = "Datos Proveedor - Cuenta Contable";
      var url =
        "/api/ZSolicitudGasto/ProveedorCuentaContable?page=" +
        page +
        "&cod_cc=" +
        cc.id +
        "&cod_proveedor=" +
        proveedor.id +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin;

      const axios = require("axios");
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result;
          me.aData = resp.data;
          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
          this.isSpinner = false;
        })
        .catch((e) => {
          this.isSpinner = false;
          console.log("Tabla " + e);
        });
    },
    TraerProveedorCC(page, fechaInicio, fechaFin, cc) {
      let me = this;
      var lista = [];
      me.aData = [];
      this.isSpinner = true;
      this.textoCarga = "Datos por Cuenta Contable";
      var url =
        "/api/ZSolicitudGasto/TraerProveedorCC?page=" +
        page +
        "&cod_cc=" +
        cc.id +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin;

      const axios = require("axios");
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result;
          me.aData = resp.data;
          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
          this.isSpinner = false;
        })
        .catch((e) => {
          this.isSpinner = false;
          console.log("Tabla " + e);
        });
    },
    TraerPorproveedor(page, fechaInicio, fechaFin, proveedor) {
      let me = this;

      var lista = [];
      me.aData = [];
      this.isSpinner = true;
      this.textoCarga = "Datos por Proveedor";
      var url =
        "/api/ZSolicitudGasto/TraerPorProveedor?page=" +
        page +
        "&cod_proveedor=" +
        proveedor.id +
        "&fechaInicio=" +
        fechaInicio +
        "&fechaFin=" +
        fechaFin;

      const axios = require("axios");
      axios
        .get(url)
        .then(function (response) {
          var resp = response.data.result;
          me.aData = resp.data;
          me.pagination = {
            total: resp.total,
            current_page: resp.current_page,
            per_page: resp.per_page,
            last_page: resp.last_page,
            from: resp.from,
            to: resp.to,
          };
          this.isSpinner = false;
        })
        .catch((e) => {
          this.isSpinner = false;
          console.log("Tabla " + e);
        });
    },
    listAcount(page, buscar) {
      let me = this;
      var url = "/api/Cuenta/Buscar?page=" + page + "&buscar=" + buscar;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data.result;
          me.aDataAcount = resp.data;

          me.paginationAcount = {
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
    listDataBankAcount() {
      let me = this;
      axios
        .get("/api/ZBancoCuenta/ListarCbx")
        .then(function (reponse) {
          me.aDataBanckAcount = reponse.data.result;
          // me.htmlListBancoCuenta="<option value='000'>Seleccione un Registro</option>";
          for (var i = 0; i < me.aDataBanckAcount.length; i++) {
            me.htmlListBancoCuenta =
              "<option value='" +
              me.aDataBanckAcount[i].cod_bc +
              "'>" +
              me.aDataBanckAcount[i].sigla +
              "-" +
              me.aDataBanckAcount[i].nro_cuenta +
              "</option>";
          }
        })
        .catch(function (error) {
          me.bugChecking(error);
        });
    },
    listDataBankAcountDestiny() {
      let me = this;
      axios
        .get("/api/ZBancoCuentaDestiny/ListarCbx")
        .then(function (reponse) {
          me.aDataBanckAcountDestiny = reponse.data.result;
        })
        .catch(function (error) {
          me.bugChecking(error);
        });
    },
    listTipoPago() {
      let me = this;
      me.htmlListTipoPago = "<option value='EFECTIVO'>EFECTIVO</option>";
      me.htmlListTipoPago += "<option value='CHEQUE'>CHEQUE</option>";
      me.htmlListTipoPago +=
        "<option value='TRANSFERENCIA'>TRANSFERENCIA</option>";
    },
    listTipoGasto() {
      let me = this;
      me.htmlListTipoGasto = "<option value='E'>EVENTUAL</option>";
      me.htmlListTipoGasto += "<option value='F'>FIJO</option>";
    },
    changePage(page, buscar) {
      let me = this;
      me.pagination.current_page = page;
      me.listData(
        page,
        buscar,
        me.fechaInicio,
        me.fechaFin,
        me.tipoEstado,
        me.tipoGasto
      );
    },
    changePageProvider(page, buscar) {
      let me = this;
      me.paginationProvider.current_page = page;
      me.listProvider(page, buscar);
    },
    changePageAcount(page, buscar) {
      let me = this;
      me.paginationAcount.current_page = page;
      me.listAcount(page, buscar);
    },
    changeRowColor(id) {
      $("#" + id + " tr").click(function (e) {
        $("#" + id + " tr").removeClass("selected");
        $(this).addClass("selected");
      });
    },
    addDetail() {
      let me = this;
      if (me.newDetail.codProveedor != -1) {
        if (me.model.detail.some((x) => x.id === me.newDetail.id)) return;
        if (!me.validateNewDetail()) return;
        // let Acount = me.aDataAcount.find(x => x.COD_CUENTA === me.newDetail.codCuentaContable);
        let Provider = me.aDataProvider.find(
          (x) => x.id === me.newDetail.codProveedor
        );
        let BankAcount = me.aDataBanckAcountDestiny.find(
          (x) => x.cod_bc === me.newDetail.codCuentaBanco
        );
        me.model.detail.push({
          id: me.newDetail.id,
          cod_sg: 0,
          cod_proveedor: Provider.id,
          nombre_proveedor: me.newDetail.nombreProveedor,
          cod_cc: me.newDetail.codCuentaContable,
          nombre_cc: me.newDetail.nombreCuenta,
          detalle: me.newDetail.detalle,
          importe_bs: parseFloat(me.newDetail.importeBs),
          importe_usd: parseFloat(me.newDetail.importeUSD),
          cod_bc: BankAcount.cod_bc,
          nombre_bc: me.newDetail.nombreCuentaBanco,
        });
        me.newDetail.id += 1;
        me.newDetail.importeBs = 0;
        me.newDetail.importeUSD = 0;
        me.newDetail.detalle = "";
      }
    },
    removeDetail(id) {
      this.model.detail = this.model.detail.filter((x) => x.id != id);
    },
    saveRegister() {
      if (!this.buttonDisabled) {
        this.buttonDisabled = true; // Desactivar el botón para evitar clics adicionales
        // Lógica para guardar la solicitud

        // Agregar un retraso de 1 segundo antes de habilitar el botón nuevamente
        setTimeout(() => {
          this.buttonDisabled = false;
        }, 1000);
      }
      let me = this;
      if (!me.validateMainForm()) return;
      axios
        .post("/api/ZSolicitudGasto", {
          data: me.model,
          detalle: me.model.detail,
        })
        .then(function (reponse) {
          if (reponse.data.status == "success") {
            me.closeWindow();
            me.cleanForm();
            swal(
              "Solicitud de Gasto al Contado",
              "Registrado Correctamente.",
              "success"
            );
            me.listData(
              1,
              me.searchData,
              me.fechaInicio,
              me.fechaFin,
              me.tipoEstado,
              me.tipoGasto
            );
          } else {
            swal(
              "Solicitud de Gasto al Contado",
              reponse.data.message,
              reponse.data.status
            );
          }
        })
        .catch(function (error) {
          swal(
            "Solicitud de Gasto al Contado",
            error.response.data.message,
            error.response.data.status
          );
        });
    },
    getDataByID(id_sg) {
      let me = this;
      var url = "/api/ZSolicitudGasto/" + id_sg;
      axios
        .get(url)
        .then(function (reponse) {
          var resp = reponse.data.result;
          me.model.cod_sg = resp.data.cod_sg;
          me.model.tipo_sg = resp.data.tipo_sg;
          me.model.fecha_sg = resp.data.fecha_sg;
          me.model.fecha_registro = resp.data.fecha_registro;
          me.model.cod_usuario = resp.data.cod_usuario;
          me.model.nro_sg = resp.data.nro_sg;
          me.model.gestion = resp.data.gestion;
          me.model.tipo_pago = resp.data.tipo_pago;
          me.model.tipo_gasto = resp.data.tipo_gasto;
          me.model.vc = resp.data.vc;
          me.model.cod_bc = resp.data.cod_bc;
          me.model.total_bs = resp.data.total_bs;
          me.model.total_usd = resp.data.total_usd;
          me.model.fecha_pago = resp.data.fecha_pago;
          me.model.glosa = resp.data.glosa;
          me.model.estado = resp.data.estado;
          me.nombreUsuario = resp.data.nombre_usuario;
          me.numeroSolicitud = resp.data.nroCorrelativo;
          for (var i = 0; i < resp.detalle.length; i++) {
            // let Provider = me.aDataProvider.find(x => x.id === resp.detalle[i].cod_proveedor);
            // let Acount = me.aDataAcount.find(x => x.COD_CUENTA === resp.detalle[i].cod_cc);
            // let Bank = me.aDataBanckAcount.find(x => x.cod_bc === resp.detalle[i].cod_bc);
            me.model.detail.push({
              id: i,
              cod_sg: 1,
              cod_proveedor: resp.detalle[i].cod_proveedor,
              nombre_proveedor: resp.detalle[i].nombre_proveedor,
              cod_cc: resp.detalle[i].cod_cc,
              nombre_cc: resp.detalle[i].cuenta_contable,
              detalle: resp.detalle[i].detalle,
              importe_bs: parseFloat(resp.detalle[i].importe_bs),
              importe_usd: parseFloat(resp.detalle[i].importe_usd),
              cod_bc: resp.detalle[i].cod_bc,
              nombre_bc: resp.detalle[i].banco_destino,
              // nombre_bc : resp.detalle[i].banco_sigla+"-"+resp.detalle[i].banco_nro_cuenta.slice(-5)+"-"+resp.detalle[i].banco_moneda
            });
            me.newDetail.id = i + 1;
          }
        })
        .catch(function (error) {
          me.bugChecking(error);
          // alert(error.response.data.message);
        });
    },
    updateRegister() {
      let me = this;
      if (!me.validateMainForm()) return;
      axios
        .put("/api/ZSolicitudGasto", {
          data: me.model,
          detalle: me.model.detail,
        })
        .then(function (reponse) {
          if (reponse.data.status == "success") {
            me.closeWindow();
            me.cleanForm();
            swal(
              "Solicitud de Gasto al Contado ",
              "Registro Actualizado Correctamente.",
              "success"
            );
            me.listData(
              1,
              me.searchData,
              me.fechaInicio,
              me.fechaFin,
              me.tipoEstado,
              me.tipoGasto
            );
          } else {
            swal(
              "Solicitud de Gasto al Contado",
              reponse.data.message,
              reponse.data.status
            );
          }
        })
        .catch(function (error) {
          swal(
            "Solicitud de Gasto al Contado",
            error.response.data.message,
            error.response.data.status
          );
        });
    },
    unregister(id_sg) {
      let me = this;
      axios
        .put("/api/ZSolicitudGasto/updateStatus", {
          cod_sg: id_sg,
          estado: "ANU",
        })
        .then(function (reponse) {
          if (reponse.data.status == "success") {
            me.closeWindow();
            me.cleanForm();
            swal(
              "Solicitud de Gasto al Contado",
              "Registro Anulado Correctamente",
              "success"
            );
            me.listData(
              1,
              me.searchData,
              me.fechaInicio,
              me.fechaFin,
              me.tipoEstado,
              me.tipoGasto
            );
          } else {
            swal(
              "Solicitud de Gasto al Contado",
              reponse.data.message,
              reponse.data.status
            );
          }
        })
        .catch(function (error) {
          swal(
            "Solicitud de Gasto al Contado",
            error.response.data.message,
            error.response.data.status
          );
        });
    },

    // Modal
    openWindow(action, data = []) {
      let me = this;
      me.listTipoPago();
      me.listTipoGasto();
      me.cleanUp();
      switch (action) {
        case "registrar": {
          me.modal = 1;
          me.modalTitle = "Nueva Solicitud de Gasto al Contado";
          me.modalAction = 1;
          break;
        }
        case "actualizar": {
          me.modal = 1;
          me.modalTitle = "Actualizar Registro Solicitud de Gasto al Contado";
          me.modalAction = 2;
          me.getDataByID(data.cod_sg);
          break;
        }
        case "anular": {
          me.model.cod_sg = data.cod_sg;
          me.modalCancel = 1;
          me.modalTitle = "Anular Registro";
          me.modalAction = 1;
          me.modalMessage = "Está Seguro de Anular el registro Seleccionado.?";
          break;
        }
      }
    },
    closeWindow() {
      this.modal = 0;
      this.modalCancel = 0;
      this.modalTitle = "";
      this.modalMessage = "";
      this.cleanForm();
    },
    openSearchProvider() {
      this.modalProvider = 1;
    },
    selectSearchProvider(provider) {
      let me = this;
      me.newDetail.codProveedor = provider.id;
      me.newDetail.nombreProveedor = provider.nombre_completo;
      me.newDetail.codCuentaContable = provider.cuenta_COD_CUENTA;
      me.newDetail.nombreCuenta = `${provider.fondoDescripcion}-${provider.NRO_CUENTA}-${provider.cuentaDescripcion}`;
      me.newDetail.codCuentaBanco = provider.cod_bc;
      me.newDetail.nombreCuentaBanco =
        provider.sigla +
        "-" +
        provider.bancoCuenta_nro_cuenta.slice(-5) +
        "-" +
        provider.moneda;
      me.closeSearchProvider();
    },
    closeSearchProvider() {
      this.modalProvider = 0;
      this.searchProviderFilter = "";
    },
    openSearchAcount() {
      this.modalAcount = 1;
    },
    selectSearchAcount(acount) {
      let me = this;
      me.newDetail.codCuentaContable = acount.COD_CUENTA;
      me.newDetail.nombreCuenta =
        acount.fondoDescripcion +
        "-" +
        acount.NRO_CUENTA +
        "-" +
        acount.cueDescripcion;
      me.closeSearchAcount();
    },
    closeSearchAcount() {
      this.modalAcount = 0;
      this.searchAcountFilter = "";
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
    alert(this.selectedProveedor.id);
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
.mostrar {
  display: list-item !important;
  /* position: absolute !important; */
  opacity: 1 !important;
  background-color: #3c29297a !important;
  height: auto;
  opacity: 1 !important;
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
</style>