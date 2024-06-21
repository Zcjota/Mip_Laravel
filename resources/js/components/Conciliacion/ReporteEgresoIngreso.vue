<template>

    <main class="main">
        <div class="container-fluid">
            <b-card>
                <b-card-header>
                    <BCardTitle>
                        REPORTE DE INGRESOS Y EGRESOS
                    </BCardTitle>
                    <div class="card-header">
                        <br />
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" @click="imprimirEXCEL()" class="btn btn-success colorVerde">
                                    <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL INGRESOS
                                </button>
                                <button type="button" @click="imprimirEXCEL2()" class="btn btn-success colorVerde">
                                    <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL COBRANZA
                                </button>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <select v-model="estado" class="form-control square" style="width:70px;"
                                        v-on:change="validateDate">
                                        <option value="">TODOS</option>
                                        <option value="0">POR REVISAR</option>
                                        <option value="2">OBSERVACION</option>
                                        <option value="1">CONCILIADO</option>
                                    </select>
                                    <select v-model="tipoMovimiento" class="form-control square" style="width:70px;"
                                        v-on:change="validateDate">
                                        <!-- <option value="">TODOS</option> -->
                                        <option value="1">INGRESO</option>
                                        <option value="2">EGRESOS</option>
                                        <option value="3">COBRANZA</option>
                                    </select>
                                    <select v-model="tipoFecha" class="form-control square" style="width:100px;"
                                        v-on:change="validateDate">
                                        <option value="1">HOY</option>
                                        <option value="2">MES ACTUAL</option>
                                        <option value="3">RANGO FECHAS</option>
                                    </select>
                                    <input v-model="fechaInicio" class="form-control" type="date" style="width:80px;" />
                                    <input v-model="fechaFin" class="form-control" type="date" style="width:80px;" />
                                    <input type="text" v-model="searchData" class="form-control" placeholder="Buscar">
                                    <button class="btn btn-icon square btn-primary"
                                        v-on:click="lisDataConciliacion(perPage, searchData, fechaInicio, fechaFin, tipoMovimiento, estado)">
                                        <i class="fa fa-search" aria-hidden="true"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card-header>
                <b-card-body>
                    <b-row>
                        <!-- Tabla para Ingresos -->
                        <b-col>

                            <b-table v-if="tipoMovimiento === '1'" id="tabla-lista-ventas" v-model="items"
                                :items="items" :fields="fields" @filtered="onFiltered" :bordered="true" :busy="isBusy"
                                outlined stacked="sm" small :style="{ fontSize: fontSize }"
                                :sticky-header="stickyHeader">


                                <!-- aqui son para diseñar los datos de los grupos  -->
                                <template #cell(Ver_detalle)="row">
                                    <b-form-checkbox v-b-toggle="'details-' + row.index" v-model="row.detailsShowing"
                                        @change="row.toggleDetails">
                                        Ver
                                    </b-form-checkbox>


                                </template>


                                <!-- aqui diseñamos para mostrar en el detalle -->
                                <template #row-details="row">
                                    <!-- Aquí diseñamos el detalle de cada grupo -->
                                    <b-card v-if="row.item.details.length">
                                        <b-card>
                                            <b-table :items="row.item.details" :fields="detailFields" hover>
                                                <template #cell(totalbs)="detail">
                                                    {{ detail.value }} <!-- Aquí puedes aplicar el formato necesario -->
                                                </template>
                                            </b-table>
                                        </b-card>

                                    </b-card>
                                </template>
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Cargando Ingresos...</strong>
                                    </div>
                                </template>
                            </b-table>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Tabla para Egresos -->
                        <b-col>
                            <b-table v-if="tipoMovimiento === '2'" id="tabla-lista-ventas" v-model="items"
                                :items="items" :fields="fields" @filtered="onFiltered" :bordered="true" :busy="isBusy"
                                outlined stacked="sm" small :style="{ fontSize: fontSize }"
                                :sticky-header="stickyHeader">


                                <!-- aqui son para diseñar los datos de los grupos  -->
                                <template #cell(Ver_detalle)="row">
                                    <b-form-checkbox v-model="row.detailsShowing" @change="row.toggleDetails">
                                        Ver
                                    </b-form-checkbox>
                                </template>


                                <!-- aqui diseñamos para mostrar en el detalle -->
                                <template #row-details="row">
                                    <!-- Aquí diseñamos el detalle de cada grupo -->
                                    <b-card v-if="row.item.details.length">
                                        <b-card>
                                            <b-table :items="row.item.details" :fields="detailFields" hover>
                                                <template #cell(totalbs)="detail">
                                                    {{ detail.value }} <!-- Aquí puedes aplicar el formato necesario -->
                                                </template>
                                            </b-table>
                                        </b-card>

                                    </b-card>
                                </template>
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Cargando Egresos...</strong>
                                    </div>
                                </template>
                            </b-table>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Tabla Para Cobranzas -->
                        <b-col>
                            <b-table v-if="tipoMovimiento === '3'" id="tabla-lista-ventas" v-model="items"
                                :items="items" :fields="fields" @filtered="onFiltered" hover :bordered="true"
                                :busy="isBusy" outlined stacked="sm" small :style="{ fontSize: fontSize }"
                                :sticky-header="stickyHeader">
                                <template #cell(Ver_detalle)="row">

                                    <!-- As `row.showDetails` is one-way, we call the toggleDetails function on @change -->
                                    <b-form-checkbox v-model="row.detailsShowing" @change="row.toggleDetails">
                                        Ver
                                    </b-form-checkbox>

                                </template>

                                <template #row-details="row">

                                    <!-- Aqui diseñamos el detalle de cada grupo  -->
                                    <b-card>
                                        <b-row class="mb-2">
                                            <b-col sm="3" class="text-sm-right"><b>Age:</b></b-col>
                                            <b-col>{{ row.item.age }}</b-col>
                                        </b-row>

                                        <b-row class="mb-2">
                                            <b-col sm="3" class="text-sm-right"><b>Is Active:</b></b-col>
                                            <b-col>{{ row.item.isActive }}</b-col>
                                        </b-row>

                                        <b-button size="sm" @click="row.toggleDetails">Hide Details</b-button>
                                    </b-card>
                                </template>
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Cargando Cobranzas...</strong>
                                    </div>
                                </template>
                            </b-table>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-pagination v-model="currentPage" :per-page="perPage" :total-rows="totalRows" size="lg"
                                class="mt-2" align="center">
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
    BFormDatepicker, BRow, BModal, VBModal, BAvatar, BCardTitle, BCardBody, BCardHeader, BCard, BDropdown, BDropdownItem, BButton,
    BFormSelect, BCol, BFormGroup, BFormInput, BFormCheckbox, BForm, BFormText, BFormDatalist, BBadge, BTable, BMedia, BFormTextarea,
    BInputGroupAppend, BInputGroup, BOverlay, BSpinner, BFormValidFeedback, BFormInvalidFeedback, VBTooltip, BPagination
} from "bootstrap-vue";

export default {
    components: {
        BFormDatepicker, BRow, BModal, VBModal, BAvatar, BCardTitle, BCardBody, BCardHeader, BCard, BDropdown, BDropdownItem, BButton,
        BFormSelect, BCol, BFormGroup, BFormInput, BFormCheckbox, BForm, BFormText, BFormDatalist, BBadge, BTable, BMedia, BFormTextarea,
        BInputGroupAppend, BInputGroup, BOverlay, BSpinner, BFormValidFeedback, BFormInvalidFeedback, VBTooltip, BPagination
    },

    data() {
        return {
            // fields: [
            //     // {key:"id_solicitud",label:"id_solicitud"},
            //     // {key:"id_detalle_solicitud",label:"id_detalle_solicitud",},
            //     // {key:"cod_detalle",label:"cod_detalle"},
            //     "Ver_detalle",
            //     { key: "fecha", label: "fecha", },
            //     { key: "tipo", label: "tipo", },
            //     { key: "nro", label: "nro", },
            //     { key: "tipo_resp", label: "tipo_resp", },
            //     { key: "responsable", label: "responsable", },
            //     { key: "cuenta", label: "cuenta", },
            //     { key: "detalle", label: "detalle", },
            //     { key: "totalbs", label: "totalbs", },
            //     { key: "usd", label: "usd" },
            //     { key: "bancoo", label: "bancoo" },
            //     { key: "bancod", label: "bancod" },
            //     { key: "bancoorigen", label: "bancoorigen" },
            //     { key: "bancodestino", label: "bancodestino" },
            //     { key: "bandera", label: "bandera", },
            // ],

            fields: [
                "Ver_detalle",
                { key: 'mainData.fecha', label: 'Fecha' },
                { key: 'mainData.nro', label: 'Número' },
                { key: 'mainData.tipo_resp', label: 'Tipo Responsable' },
                { key: 'mainData.totalbsSum', label: 'Total Totalbs' },
            ],
            items: [],

            stickyHeader: true,
            currentPage: 1,
            perPage: 10,  // Número de elementos por página
            totalRows: 1,

            fontSize: "",
            // datos de el otro componente 
            messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
            selectAllOT: false,
            selectedSC: [],
            tipoMovimiento: 2,
            estado: '',
            model: {
                cod_cr: 0,
                nro_recibo: 0,
                tipoMovimiento: 2,
                estado: '',
                fecha: this.fecha('actual'),
                detalle: []
            },
            dataDetalle: {
                cod_cr: 0,
                ACTIVO: 1,
            },
            isBusy: false,
            //Datos de tablas
            aData: [],

            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,
            },
            offset: 3,

            // filtros Listado 
            tipoFecha: '1',
            searchData: '',
            fechaInicio: '',
            fechaFin: '',
            fechaActual: this.fecha('actual'),

            // modal
            modal: 0,
            modal2: 0,
            modalCancel: 0,
            modalTitle: '',
            modalAction: 0,
            modalMessage: '',

            camposRequeridos: [],
        };
    },
    computed: {
        totalRows() {
            this.totalRows = this.items.length
        },
    },
    created() {
        this.sizeTablas()
    },
    watch: {


        currentPage: function (newPage) {
            let me = this;
            this.currentPage = newPage
            console.log("variable  " + this.currentPage)
            this.lisDataConciliacion(this.currentPage, me.searchData, me.fechaInicio, me.fechaFin, me.tipoMovimiento, me.estado);
        },
    },
    mounted() {
        this.initialize()
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
        },
        imprimirEXCEL() {
            const params = new URLSearchParams({
                buscar: this.buscar || '', // Usa valores por defecto si no están definidos
                tipoMovimiento: this.tipoMovimiento || '',
                estado: this.estado || '',
                fechaInicio: this.fechaInicio || '',
                fechaFin: this.fechaFin || ''
            }).toString();

            const url = `/exportar-ingresos?${params}`;
            window.location.href = url;
        },
        imprimirEXCEL2() {
            const params = new URLSearchParams({
                buscar: this.buscar || '', // Usa valores por defecto si no están definidos
                // tipoMovimiento: this.tipoMovimiento || '',
                // estado: this.estado || '',
                fechaInicio: this.fechaInicio || '',
                fechaFin: this.fechaFin || ''
            }).toString();

            const url = `/exportar-cobranzas?${params}`;
            window.location.href = url;
        },
        formatNumber(nro) {

            return 'C-' + String(nro).padStart(4, '0');
        },
        cleanUp() {
            this.camposRequeridos = [];
        },
        cleanForm() {
            let me = this;
            me.selectAllOT = false;
            me.selectedSC = [];
            me.model = {
                cod_cr: 0
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
            if (dia < 10)
                dia = '0' + dia; //agrega cero si el menor de 10
            if (mes < 10)
                mes = '0' + mes
            switch (opc) {
                case 'actual':
                    result = `${año}-${mes}-${dia}`;
                    break;
                case 'inicio':
                    result = `${año}-${mes}-01`;
                    break;
                case 'fin':
                    result = `${año}-${mes}-${ultimoDia}`;
                    break;
            }
            return result;
        },
        initialize() {
            let me = this;
            var fecha = me.fecha('actual');
            me.tipoFecha = "1";
            me.fechaInicio = fecha;
            me.fechaFin = fecha;
            me.fechaActual = me.fecha('actual');
            me.lisDataConciliacion(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoMovimiento, me.estado);
        },
        validateDate() {
            let me = this;
            var tipo = me.tipoFecha;
            switch (tipo) {
                case "1":
                    var fecha_actual = me.fecha('actual');
                    me.fechaInicio = fecha_actual;
                    me.fechaFin = fecha_actual;
                    me.lisDataConciliacion(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoMovimiento, me.estado);
                    break;
                case "2":
                    var fecha_inicio = me.fecha('inicio');
                    var fecha_fin = me.fecha('fin');
                    me.fechaInicio = fecha_inicio;
                    me.fechaFin = fecha_fin;
                    me.lisDataConciliacion(1, me.searchData, me.fechaInicio, me.fechaFin, me.tipoMovimiento, me.estado);
                    break;
                case "3":
                    break;
            }
        },
        validateMainForm() {
            let me = this;


            // Verifica que todos los detalles seleccionados tengan el mismo banco de origen y la misma fecha de solicitud
            const bancoOrigenUniforme = this.selectedSC.every((detalle, _, array) => detalle.bancoo === array[0].bancoo);
            const fechaSolicitudUniforme = this.selectedSC.every((detalle, _, array) => detalle.fecha === array[0].fecha);

            if (!bancoOrigenUniforme) {
                swal("Alerta de Mensaje", "Todos los detalles seleccionados deben tener el mismo banco de origen y fecha", "warning");
                return false;
            }
            if (!fechaSolicitudUniforme) {
                swal("Alerta de Mensaje", "Todos los detalles seleccionados deben tener la misma fecha de solicitud.", "warning");
                return false;
            }

            return true;
        }
        ,

        listData(page, buscar, fechaInicio, fechaFin, tipoMovimiento, estado) {
            let me = this;
            var url = `/api/conciliacionS/list?page=${page}&buscar=${buscar}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&tipoMovimiento=${tipoMovimiento}&estado=${estado}`;
            axios.get(url).then(function (response) {
                // Ya no buscamos 'response.data.result', sino que accedemos directamente a 'response.data'
                var resp = response.data; // Accedemos directamente a la respuesta que esperamos.
                me.aData = resp.data; // Asignamos los datos a 'aData'.

                // Configuramos la paginación basada en la respuesta de la API
                me.pagination = {
                    'total': resp.total,
                    'current_page': resp.current_page,
                    'per_page': resp.per_page,
                    'last_page': resp.last_page,
                    'from': resp.from,
                    'to': resp.to,
                };
            }).catch(function (error) {
                me.bugChecking(error);
            });
        },


        //Metodos Nuevos 
        sizeTablas() {
            const anchoVentana = window.innerWidth;

            if (anchoVentana <= 576) {
                // Dispositivo móvil pequeño
                this.fontSize = 'xx-small';
            } else if (anchoVentana <= 768) {
                // Dispositivo móvil o tableta
                this.fontSize = 'small';
            } else if (anchoVentana <= 992) {
                // Tableta o dispositivo de tamaño medio
                this.fontSize = 'medium';
            } else {
                // Pantalla de escritorio
                this.fontSize = 'mediun';
            }
        },
        async lisDataConciliacion(page, buscar, fechaInicio, fechaFin, tipoMovimiento, estado) {
            try {

                this.isBusy = true;
                const url = `/api/conciliacionS/list?page=${page}&buscar=${buscar}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&tipoMovimiento=${tipoMovimiento}&estado=${estado}`;
                const response = await axios.get(url);
                const rawData = response.data.data;

                // Agrupar datos por id_solicitud
                const groupedData = {};
                rawData.forEach(item => {
                    const id_solicitud = item.id_solicitud;
                    if (!groupedData[id_solicitud]) {
                        groupedData[id_solicitud] = { mainData: {}, details: [] };
                    }
                    if (Object.keys(groupedData[id_solicitud].mainData).length === 0) {


                        // Guardar los datos principales en el primer elemento del array details
                        groupedData[id_solicitud].mainData = {
                            fecha: item.fecha,
                            nro: item.nro,
                            tipo_resp: item.tipo_resp,

                        };
                    }
                    // Guardar los detalles en el array details
                    groupedData[id_solicitud].details.push({

                        responsable: item.responsable,
                        cuenta: item.cuenta,
                        detalle: item.detalle,
                        totalbs: item.totalbs,
                    });
                });

                // Calcular la suma total de totalbs para cada grupo y agregarla a mainData
                Object.values(groupedData).forEach(group => {
                    const sumTotalbs = group.details.reduce((acc, detail) => acc + detail.totalbs, 0);
                    group.mainData.totalbsSum = sumTotalbs;
                });

                // Convertir el objeto en un array de objetos
                this.items = Object.values(groupedData);
                this.totalRows = response.data.total;
                this.currentPage = response.data.current_page;
                this.isBusy = false;
            } catch (error) {
                console.error("Error al obtener los datos", error);
                alert("Error al obtener los datos" + error);
                this.isBusy = false;
            }
        },



        saveRegister() {
            if (!this.validateMainForm()) {
                // Si la validación falla, detiene la ejecución del método
                return;
            }
            // Recolectar los detalles seleccionados
            let detallesParaEnviar = this.selectedSC.map(detalle => {
                return {
                    id_solicitud: detalle.id_solicitud,
                    id_detalle_solicitud: detalle.id_detalle_solicitud,
                    tipo: detalle.tipo,
                    // cod_detalle: detalle.cod_detalle,
                    fecha_si: detalle.fecha,
                    bancod: detalle.bancodestino,
                    bancoo: detalle.bancoorigen,
                    totalbs: detalle.totalbs,
                    cod_si: detalle.cod_si,
                };
            });

            // Añadir la nota del formulario
            let nota = this.model.nota;

            // Enviar los datos al backend
            axios.post('/api/conciliacionS/registrarci', {
                detalles: detallesParaEnviar,
                nota: nota,
            })
                .then(response => {
                    // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
                    swal("Conciliaciones creadas correctamente.");
                    this.closeWindow();
                    this.lisDataConciliacion(1, this.searchData, this.fechaInicio, this.fechaFin, this.tipoMovimiento, this.estado);
                    // alert('Conciliaciones creadas correctamente.');
                })
                .catch(error => {
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
            let detallesParaEnviar = this.selectedSC.map(detalle => {
                return {
                    id_solicitud: detalle.id_solicitud,
                    id_detalle_solicitud: detalle.id_detalle_solicitud,
                    // cod_detalle: detalle.cod_detalle,
                    tipo: detalle.tipo,
                    fecha_si: detalle.fecha,
                    bancod: detalle.bancodestino,
                    bancoo: detalle.bancoorigen,
                    totalbs: detalle.totalbs,
                    cod_si: detalle.cod_si,
                };
            });

            // Añadir la nota del formulario
            let nota = this.model.nota;

            // Enviar los datos al backend
            axios.post('/api/conciliacionS/registrarcio', {
                detalles: detallesParaEnviar,
                nota: nota,
            })
                .then(response => {
                    // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
                    swal("Conciliaciones creadas correctamente.");
                    this.closeWindow();
                    this.lisDataConciliacion(1, this.searchData, this.fechaInicio, this.fechaFin, this.tipoMovimiento, this.estado);
                    // alert('Conciliaciones creadas correctamente.');
                })
                .catch(error => {
                    // Aquí manejas posibles errores en la solicitud
                    console.error(error);
                });
        },
        openPDFRecibotoPrint(id) {
            var pagina = "http://mip2024.isitecnologia.com/api/conciliacionS/pdf/" + id;
            // var pagina = "http://127.0.0.1:8000/api/conciliacionS/pdf/"+id;
            var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
            window.open(pagina, "", opciones);
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
            this.modalTitle = '';
            this.modalMessage = '';
            this.cleanForm();
        },
        closeWindowWithoutCleaning() {
            this.modal = 0; // Cerrar el modal.
            this.modal2 = 0;
            this.modalMessage = '';
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
                swal("Error", "Ocurrió un error inesperado. Por favor, intenta nuevamente.", "error");
            }
            return array;
        }
        ,
        cerrarSesion() {
            swal({
                title: 'La Sesion ha Expirado, Debe Iniciar Sesion Nuevamente.',
                type: 'warning',
                confirmButtonColor: 'green',
                confirmButtonText: 'Aceptar',
                confirmButtonClass: 'btn btn-success',
            }).then((result) => {
                if (result.value) {
                    location.href = "http://mip2024.isitecnologia.com/";
                }
            })
        }
    },
};

</script>
<style></style>