<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="">
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h1 style="text-align: center"><b>ASIGNAR PRESUPUESTO</b></h1>
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <!-- <button type="button" class="btn btn-success square" v-on:click="openWindow2('registrar2')"
                                title="Nuevo Registro">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                APERTURAR MES
                            </button> -->
                            <button type="button" class="btn btn-primary" @click="abrirFormularioApertura">
                                Aperturar Mes
                            </button>


                            <button type="button" class="btn btn-success square" v-on:click="openWindow2('registrar2')"
                                title="Nuevo Registro">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                MODIFICAR
                            </button>

                            <button type="button" @click="imprimirEXCEL()" class="btn btn-success colorVerde">
                                <i class="fa fa-file-pdf-o"></i>&nbsp;EXCEL
                            </button>

                        </div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <select v-model="estado" class="form-control square" style="width:70px;"
                                    v-on:change="validateDate">
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
                                <select v-model="tipoMovimiento" class="form-control square" style="width:70px;"
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
                                </select>
                                <!-- <select v-model="tipoFecha" class="form-control square" style="width:100px;"
                                    v-on:change="validateDate">
                                    <option value="1">HOY</option>
                                    <option value="2">MES ACTUAL</option>
                                    <option value="3">RANGO FECHAS</option>
                                </select> -->
                                <!-- <input v-model="fechaInicio" class="form-control" type="date" style="width:80px;" />
                                <input v-model="fechaFin" class="form-control" type="date" style="width:80px;" />
                                <input type="text" v-model="searchData" class="form-control" placeholder="Buscar">
                                <button class="btn btn-icon square btn-primary"
                                    v-on:click="listData(1, searchData, fechaInicio, fechaFin, tipoMovimiento,estado)">
                                    <i class="fa fa-search" aria-hidden="true"></i> Buscar
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="overflow-x:auto;">
                        <div class="panel-body table-responsive">
                            <div class="x_title"></div>
                            <!-- <div v-if="tipoMovimiento !== '3'" id="bodyListado"> -->
                            <div id="bodyListado">
                                <table id="mainTableSG" class="tabla">
                                    <thead>
                                        <tr>
                                            <!-- <th>&nbsp;&nbsp;GUARDAR</th> -->
                                            <th>FONDO</th>
                                            <th>NRO.CUENTA</th>
                                            <th>CUENTA</th>
                                            <th>PRESUPUESTO</th>
                                            <th>GASTO ACTUAL</th>
                                            <th>SALDO ACTUAL</th>
                                            <!-- <th>OBSERVACION</th> -->
                                            <th>GUARDAR</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <!-- <tr v-for="(data, codigo) in aData" :key="data.codigo" style="height:50px"> -->
                                        <tr v-for="data in aData" :key="data.codigo" style="height:50px">
                                            <td>{{ data.fondo }}</td>
                                            <td>{{ data.nrocuenta }}</td>
                                            <td>{{ data.descripcion }}</td>
                                            <td>
                                                <input type="number" v-model="data.montoPresupuesto"
                                                    @input="validarPresupuesto(data)">
                                            </td>
                                            <!-- <td><input type="number" v-model="data.montoPresupuesto" /></td> -->
                                            <!-- <td>{{ data.montoPresupuesto }}</td> -->
                                            <td>{{ data.montoGastoF }}</td>
                                            <td>{{ data.saldoActual }}</td>
                                            <!-- <td>{{ data.observacion }}</td> -->
                                            <!-- <button v-if="data.ACTIVO==1 && data.estado=='INI'" type="button" v-on:click="openWindow('aprobar', data)" class="btn btn-success colorVerde">
                                                        <i class="fa fa-check"></i>Aprobar
                                                    </button> -->
                                            <td><button @click="guardarPresupuesto(data)" class="btn btn-success colorVerde">
                                                <i class="fa fa-check"></i>GUARDAR</button></td>

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
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page - 1, searchData)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page, searchData)">{{page}}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page + 1, searchData)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
           <!-- NODAL DE APERTURAR MES -->

        </div>
    </main>
</template>

<script>
    export default {
        data(){
            return {
                cuentasAsignadas: [],
                messageFailed: "Se ha Generado una Excepcion en el Sistema, Comunicarse con el Área de Sistemas.",
                selectAllOT: false,
                selectedSC: [],
                tipoMovimiento: 2024,
                estado: 4,
                formularioApertura: {
                    mes: null,
                    gestion: null,
                    // Otros campos que puedas necesitar
                },
                aperturaMes: new Date().getMonth() + 1, // Mes actual
                aperturaGestion: new Date().getFullYear(), // Año actual
                meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                cuentas: [],
                searchTerm: '',
                cuentaSeleccionada: '',
                model:{
                    cod_cr: 0,
                    nro_recibo: 0,
                    tipoMovimiento: 2024,
                    estado: 4,
                    fecha: this.fecha('actual'),
                    detalle: []
                },
                dataDetalle:{
                    cod_cr: 0,
                    ACTIVO: 1,
                },

                //Datos de tablas
                aData :[],

                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,

                // filtros Listado 
                tipoFecha: '1',
                searchData: '',
                fechaInicio: '',
                fechaFin: '',
                fechaActual: this.fecha('actual'),

                // modal
                modal : 0,
                modal2 : 0,
                modalCancel: 0,
                modalTitle : '',
                modalAction : 0,
                modalMessage : '',

                camposRequeridos:[],
            }
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
            }
        },
        methods: {
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
           
            guardarPresupuesto(data) {
                axios.post('/api/asignarpresupuesto/actualizarPresupuesto', {
                    codCuenta: data.codigo,
                    montoPresupuesto: data.montoPresupuesto,
                    codApertura: 89 // Asegúrate de tener este dato disponible
                }).then(response => {
                    // Manejo de la respuesta exitosa
                   swal('Éxito', 'Presupuesto actualizado con éxito.', 'success');
                }).catch(error => {
                    // Manejo del error
                    // console.error('Error al actualizar el presupuesto:', error);
                    swal('Error', 'Error al actualizar el presupuesto.', 'error');
                });
            },

            validarPresupuesto(data) {
                if (data.montoPresupuesto < 0) {
                    // Si tienes una propiedad 'error' en tu objeto puedes usarla, 
                    // si no, puedes agregarla para mostrar un mensaje de error o cambiar el estilo
                    swal('Error', 'El presupuesto no puede ser negativo.', 'error');
                    // Asume que tienes un flag de 'isValid' o similar para controlar si el valor es válido
                    data.isValid = false; // Asume que esta propiedad existe en tus objetos de `aData`
                } else {
                    // Si el valor es válido, asegúrate de resetear/clear cualquier indicación de error
                    data.isValid = true;
                }
            },
        abrirFormularioApertura() {
            axios.get('/api/asignarpresupuesto/verificarApertura').then(response => {
                const aperturaData = response.data;
                this.formularioApertura.mes = aperturaData.mes;
                this.formularioApertura.gestion = aperturaData.gestion;
                // Muestra el modal del formulario con los datos cargados
                $("#modalFormularioApertura").modal('show');
            }).catch(error => {
                console.error('Error al obtener datos de apertura:', error);
                // Manejo del error
            });
        },
        guardarApertura() {
            // Implementar lógica para guardar la apertura
            // Puedes usar axios para enviar una solicitud POST al backend
            console.log(`Guardar apertura para mes: ${this.aperturaMes}, gestión: ${this.aperturaGestion}`);
            // axios.post('/ruta/api/para/guardar/apertura', { mes: this.aperturaMes, gestion: this.aperturaGestion })
            //      .then(response => { /* ... */ })
            //      .catch(error => { /* ... */ });
        },
        verificarAperturaActual() {
            // Lógica para verificar la apertura actual, posiblemente usando una solicitud GET al backend
            axios.get('/api/asignarpresupuesto/verificarApertura', { params: { mes: this.aperturaMes, gestion: this.aperturaGestion }})
                 .then(response => {
                     if(response.data.debeAperturar) {
                         // Actualizar aperturaMes y aperturaGestion con los valores correspondientes
                     }
                 })
                 .catch(error => { /* ... */ });
        },
            buscarCuentas() {
                // Aquí harías la llamada a la API para buscar cuentas basadas en searchTerm
                // Usaré un ejemplo ficticio de cómo podrías hacer la llamada
                axios.get(`/api/asignarpresupuesto/cuentas2?term=${this.searchTerm}`).then(response => {
                    // Suponiendo que tu API retorna un arreglo de cuentas en response.data
                    this.cuentas = response.data;
                }).catch(error => {
                    console.error('Hubo un error al buscar las cuentas:', error);
                    // Manejar el error adecuadamente
                });
            },
  
            verificarAperturaInicial() {
                axios.get(`/api/asignarpresupuesto/verificarApertura?mes=${this.estado}&gestion=${this.tipoMovimiento}`).then(response => {
                    const { mes, gestion } = response.data;
                    if (mes !== this.estado || gestion !== this.tipoMovimiento) {
                        this.estado = mes;
                        this.tipoMovimiento = gestion;
                        swal("Alerta", "No se ha aperturado el mes en curso. Se ha seleccionado el mes y año disponibles más próximos.", "warning");
                    }
                    this.listData(1, this.searchData, '', '', this.tipoMovimiento, this.estado);
                })
                .catch(error => {
                    // Aquí usamos swal directamente para manejar el error
                    swal("Error", "No se pudo verificar la apertura del mes en curso. Por favor, intente nuevamente.", "error");
                });
            },

            formatNumber(nro) {
             
                return 'C-' + String(nro).padStart(4, '0');
            },
            cleanUp(){
                this.camposRequeridos=[];
            },
            cleanForm(){
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
                me.listData(1, me.searchData, me.fechaInicio, me.fechaFin,me.tipoMovimiento,me.estado);
                this.verificarAperturaActual();
                // this.cargarCuentas();
                this.cuentasFiltradas = this.cuentas;
                this.verificarAperturaInicial();
            },
            validateDate() {
                let me = this;
                var tipo = me.tipoFecha;
                switch (tipo) {
                    case "1":
                        var fecha_actual = me.fecha('actual');
                        me.fechaInicio=fecha_actual;
                        me.fechaFin=fecha_actual;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin,me.tipoMovimiento,me.estado);
                        break;
                    case "2":
                        var fecha_inicio = me.fecha('inicio');
                        var fecha_fin = me.fecha('fin');
                        me.fechaInicio=fecha_inicio;
                        me.fechaFin=fecha_fin;
                        me.listData(1, me.searchData, me.fechaInicio, me.fechaFin,me.tipoMovimiento,me.estado);
                        break;
                    case "3":
                        break;
                }
            },
            validateMainForm() {
                let me = this;
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
            },
            listData(page, buscar, fechaInicio, fechaFin, tipoMovimiento, estado) {
                let me = this;
                var url = `/api/asignarpresupuesto/list2?page=${page}&buscar=${buscar}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&tipoMovimiento=${tipoMovimiento}&estado=${estado}`;

                // Cambio a arrow function aquí
                axios.get(url).then((response) => {
                    // console.log(response.data); // Muestra los datos en la consola
                    var resp = response.data; // Accede a la respuesta
                    me.aData = resp.data; // Asigna los datos a 'aData'

                    // Configura la paginación basada en la respuesta de la API
                    me.pagination = {
                        'total': resp.total,
                        'current_page': resp.current_page,
                        'per_page': resp.per_page,
                        'last_page': resp.last_page,
                        'from': resp.from,
                        'to': resp.to,
                    };
                }).catch((error) => {
                    me.bugChecking(error);
                });
            },


            changePage(page, buscar){
                let me = this;
                me.pagination.current_page = page;
                me.listData(page, buscar, me.fechaInicio, me.fechaFin,me.tipoMovimiento,me.estado);
            },
            changeRowColor(id) {
                $('#'+id+' tr').click(function(e) {
                    $('#'+id+' tr').removeClass('selected');
                    $(this).addClass('selected');
                });
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
                        tipo:detalle.tipo,
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
                axios.post('/api/asignarpresupuesto/registrarci', {
                    detalles: detallesParaEnviar,
                    nota: nota,
                })
                    .then(response => {
                        // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
                        swal("Conciliaciones creadas correctamente.");
                        this.closeWindow();
                        this.listData(1, this.searchData, this.fechaInicio, this.fechaFin, this.tipoMovimiento, this.estado);
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
                        tipo:detalle.tipo,
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
                axios.post('/api/asignarpresupuesto/registrarcio', {
                    detalles: detallesParaEnviar,
                    nota: nota,
                })
                    .then(response => {
                        // Aquí manejas la respuesta del servidor, por ejemplo, mostrando un mensaje de éxito.
                        swal("Conciliaciones creadas correctamente.");
                        this.closeWindow();
                        this.listData(1, this.searchData, this.fechaInicio, this.fechaFin, this.tipoMovimiento, this.estado);
                        // alert('Conciliaciones creadas correctamente.');
                    })
                    .catch(error => {
                        // Aquí manejas posibles errores en la solicitud
                        console.error(error);
                    });
            },
            openPDFRecibotoPrint(id){
                var pagina = "http://mip2024.isitecnologia.com/api/asignarpresupuesto/pdf/"+id;
                // var pagina = "http://127.0.0.1:8000/api/asignarpresupuesto/pdf/"+id;
                var opciones = "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=1000, height=800, top=0, left=0";
                window.open(pagina,"",opciones);
            },

            // Modal
            openWindow(action, data = []) {
                let me = this;
                me.cleanUp();
                switch (action) {
                    case "registrar": {
                        if (me.selectedSC.length >= 1) {
                            me.modal = 1;
                            me.modalTitle = 'CONFIRMACION DE CONCILIACION';
                            me.modalAction = 1;
                            // Aquí estableces los valores por defecto basados en el primer detalle seleccionado
                            me.model.fecha_si = me.selectedSC[0].fecha; // Asume que 'fecha' es tu 'fecha_si'
                            me.model.bancoo = me.selectedSC[0].bancoo; // Usar 'bancoo' del detalle seleccionado como 'banco_origen'
                            me.setForm(me.selectedSC);
                        } else {
                            swal("Alerta de Mensaje", "Debe Seleccionar Mínimo un Registro.", "warning");
                        }
                        break;
                    };
                }
            },
            openWindow2(action, data = []) {
                let me = this;
                me.cleanUp();
                switch (action) {
                    case "registrar2": {
                        if (me.selectedSC.length >= 1) {
                            me.modal2 = 1;
                            me.modalTitle = 'CONFIRMACION DE CONCILIACION OBSERVACION';
                            me.modalAction = 1;
                            // Aquí estableces los valores por defecto basados en el primer detalle seleccionado
                            me.model.fecha_si = me.selectedSC[0].fecha; // Asume que 'fecha' es tu 'fecha_si'
                            me.model.bancoo = me.selectedSC[0].bancoo; // Usar 'bancoo' del detalle seleccionado como 'banco_origen'
                            me.setForm(me.selectedSC);
                        } else {
                            swal("Alerta de Mensaje", "Debe Seleccionar Mínimo un Registro.", "warning");
                        }
                        break;
                    };
                }
            },

            setForm(data){
                let me = this;
                for (let i = 0; i < data.length; i++) {
                    // me.model.detalle.push({

                    //     cod_cr: 0,
                    //     ACTIVO: 1,
                    // });
                  
                }
            },
            closeWindow(){
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
            // bugChecking(error) {
            //     var array = [];
            //     // Verifica primero si existe error.response
            //     if (error.response) {
            //         if (error.response.status == 422) {
            //             array = error.response.data.errors;
            //         } else if (error.response.status == 419) {
            //             this.cerrarSesion();
            //         } else {
            //             // Manejar otros códigos de estado HTTP aquí si es necesario
            //             var cadena = String(error);
            //             cadena = cadena.substr(cadena.length - 3, 3);
            //             swal("Message: ", this.messageFailed + " " + cadena, "error");
            //         }
            //     } else {
            //         // Maneja casos donde error.response no está definido
            //         console.error("Error: ", error.message || "Unknown error");
            //         // Aquí podrías mostrar un mensaje genérico al usuario
            //         swal("Error", "Ocurrió un error inesperado. Por favor, intenta nuevamente.", "error");
            //     }
            //     return array;
            // },
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
                // Error cuando el recurso no se encuentra
                swal("Error", "No se pudo verificar la apertura del mes en curso. Por favor, intente nuevamente.", "error");
                // swal("Error", "No se encontró el recurso solicitado. Por favor, verifica la dirección e intenta nuevamente.", "error");
                break;
            default:
                // Manejar otros códigos de estado HTTP aquí si es necesario
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
        
        swal("Error", "Ocurrió un error inesperado. Por favor, intenta nuevamente.", "error");
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
            cerrarSesion(){
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
        mounted() {
            this.initialize();
            // this.verificarAperturaActual();
        }
    }
</script>

<style>
.input-error {
    border-color: red;
}
    .modal-content-format{
        position: absolute !important;
        width: 1000px !important;
        height: auto !important;
    }.model-content-format-2{
        width: 700px !important;
        height: 500px !important;
    }
    .colorRojo {
    background-color: crimson;
    }
    .rojo{
        color: crimson;
    }
    .colorVerde {
    background-color:green;
    }
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #dbdbdb;
    }
    .modal-body {
        position: relative;
        padding: 20px;
        height: auto;
        overflow-y: scroll;
    }
    .selected{
        background-color: red;
    }
    .resetColorFila{
        background-color:aquamarine;
    }.lbl-format{
        font-style: italic;
        color: #000000;
        font-size: 12px;
        font-weight: bold;
        margin: 0;
    }.input-format{
        font-size: 12px;
        font-weight: bold;
    }.modal-body-format{
        background-color: #ebefe3 !important;
        /* background-color: #c4bcb5 !important;  */
    }.select-format{
        font-size: 11px;
    }.textarea-format{
        font-size: 11px;
        text-transform: uppercase;
    }.input-detalle-format{
        width: 80px !important;
        font-weight: bold;
        font-size: 14px;
    }.textarea-detalle-format{
        text-transform: uppercase;
        width: 270px;
        font-style: normal;
        color: #000000;
    }.tfoot-format{
        background-color: #7396a7;
    }.table-thead-td{
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }.table-tbody-td {
        font-size: 10px;
    }.table-tfoot-td{
        font-size: 12px;
        font-weight: bold;
    }.div-table-format{
        height: 330px;
        overflow-y: scroll;
    }
</style>