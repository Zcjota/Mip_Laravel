<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="">
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h1 style="text-align: center"><b>Listado de Ordenes de Trabajo</b></h1>
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success square" @click="guardarCambios">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <button class="btn btn-primary square" @click="imprimirExcel">
                                <i class="fa fa-file-excel-o"></i> Exportar
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group search-bar">
                                <select v-model="tipoFecha" class="form-control square" style="width: 80px;" @change="validateDate">
                                    <option value="1">Hoy</option>
                                    <option value="2">Mes Actual</option>
                                    <option value="3">Rango de Fechas</option>
                                </select>
                                <input v-model="fechaini" type="date" class="form-control"  placeholder="Fecha Inicio">
                                <input v-model="fechafin" type="date" class="form-control"  placeholder="Fecha Fin">
                                <input type="text" v-model="filter" class="form-control" placeholder="Buscar">
                                <button class="btn btn-primary" @click="buscarDatos">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="overflow-x:auto;">
                        <div class="panel-body table-responsive">
                            <div id="bodyListado">
                                <table id="mainTableSG" class="tabla">
                                    <thead>
                                        <tr>
                                            <th>Nro</th>
                                            <th>Cliente</th>
                                            <th>Precio</th>
                                            <th>Fecha Servicio</th>
                                            <th>Tipo Pago</th>
                                            <th>Plazo</th>
                                            <th>Fecha Estimada</th>
                                            <th>Monto $us</th>
                                            <th>Monto Bs</th>
                                            <th>Dif Pago</th>
                                            <th>Fecha Recibo</th>
                                            <th># Recibo</th>
                                            <th>Contado #Deposito</th>
                                            <th>#Cheque #Deposito</th>
                                            <th>#Transferencia</th>
                                            <th>Fecha Factura</th>
                                            <th>#Factura</th>
                                            <th>Debe</th>
                                            <th>Contacto</th>
                                            <th>Direccion</th>
                                            <th>Telefono</th>
                                            <th>Ejecutivo Ventas</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in items" :key="item.codigo">
                                            <td>{{ item.nro }}</td>
                                            <td>{{ item.cliente }}</td>
                                            <td>{{ item.precio }}</td>
                                            <td>{{ item.fechaser }}</td>
                                            <td>{{ item.tp }}</td>
                                            <td>{{ item.plazo }}</td>
                                            <td>{{ item.fecEstimada }}</td>
                                            <td>{{ item.montopagodolar }}</td>
                                            <td>{{ item.montopagobs }}</td>
                                            <td>{{ item.difpago }}</td>
                                            <td>{{ item.fecha_recibo }}</td>
                                            <td>{{ item.nro_recibo }}</td>
                                            <td>{{ item.contado_nro_deposito }}</td>
                                            <td>{{ item.nro_cheque }}</td>
                                            <td>{{ item.transferencia }}</td>
                                            <td>{{ item.fecha_factura }}</td>
                                            <td>{{ item.nro_factura }}</td>
                                            <td>{{ item.debe }}</td>
                                            <td>{{ item.contacto }}</td>
                                            <td>{{ item.direccion }}</td>
                                            <td>{{ item.telefono }}</td>
                                            <td>{{ item.ejecutivo }}</td>
                                            <td>{{ item.estado }}</td>
                                            <td>
                                                <button class="btn btn-danger" @click="eliminar(item)">Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            tipoFecha: '1',
            fechaini: '',
            fechafin: '',
            filter: '',
            items: [],
            pagination: {
                total: 0,
                current_page: 1,
                per_page: 10,
                last_page: 0,
                from: 0,
                to: 0
            },
            offset: 3,
        };
    },
    computed: {
        isActived() {
            return this.pagination.current_page;
        },
        pagesNumber() {
            if (!this.pagination.to) {
                return [];
            }
            let from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            let to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            let pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    methods: {
        setDefaultDates() {
            const today = new Date().toISOString().substr(0, 10);
            this.fechaini = today;
            this.fechafin = today;
        },
        fetchData(page = 1) {
            axios.post('/api/DSListaOrdenTrabajoTodoGRAJAXVentas', {
                fechaini: this.fechaini,
                fechafin: this.fechafin,
                filter: this.filter,
                page: page,
                per_page: this.pagination.per_page
            }).then(response => {
                this.items = response.data.data;
                this.pagination = {
                    total: response.data.total,
                    current_page: response.data.current_page,
                    per_page: response.data.per_page,
                    last_page: response.data.last_page,
                    from: response.data.from,
                    to: response.data.to
                };
            });
        },
        guardarCambios() {
            axios.post('/api/DSguardarVentasMasivo', { registros: this.items })
                .then(response => {
                    this.fetchData(this.pagination.current_page);
                });
        },
        imprimirExcel() {
            const params = new URLSearchParams({
                fechaini: this.fechaini,
                fechafin: this.fechafin,
                filter: this.filter
            }).toString();
            window.location.href = `/servicesAjax/DSExcelOrdenTrabajoTodoVentas.php?${params}`;
        },
        buscarDatos() {
            this.fetchData(1);
        },
        eliminar(item) {
            if (item.debe === 'NO DEBE') {
                axios.post('/api/DSdesactivarPagoAdmin', {
                    codservicio: item.nro,
                    codnum: item.codnum,
                    codigo: item.codigo
                }).then(response => {
                    this.fetchData(this.pagination.current_page);
                });
            } else {
                alert("Solo puede eliminar registro en estado NO DEBE, caso contrario solo actualice");
            }
        },
        changePage(page) {
            this.pagination.current_page = page;
            this.fetchData(page);
        },
        validateDate() {
            const today = new Date().toISOString().substr(0, 10);
            const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().substr(0, 10);
            const lastDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().substr(0, 10);

            switch (this.tipoFecha) {
                case '1':
                    this.fechaini = today;
                    this.fechafin = today;
                    break;
                case '2':
                    this.fechaini = firstDayOfMonth;
                    this.fechafin = lastDayOfMonth;
                    break;
                case '3':
                    // The dates are manually input by the user, so no need to set them here
                    break;
            }

            this.fetchData(1);
        }
    },
    mounted() {
        this.setDefaultDates();
        this.fetchData();
    }
};
</script>

<style scoped>
.search-bar {
    display: flex;
    gap: 10px;
}

.table {
    width: 100%;
}
</style>
