<div class="sidebar">
    <nav class="sidebar-nav">

        <div align="center">
            <br>
            <img src="{{$imagen}}" alt="..." style="width: 90px;border-radius:150px;" />
        </div>
        <div style="text-align: center;">
            Bienvenido:<br> {{$nombre}}
        </div>
        <div style="text-align: center;">

            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span style="font-size: 25px" class="fa fa-power-off" title="Cerrar Sesión"></span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>

        <hr style="background-color: white">
        <ul class="nav">
            <li @click="menu=0" class="nav-title">
                Menu Principal
            </li>

            <!-- COMERCIAL -->
            <div v-if="codTipoUsu==16">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li> -->
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                        <li @click="menu=209" class="nav-item lista"><a class="nav-link" href="#">Reporte Estado Resultados</a></li>
                    </ul>
                </li>
            </div>
            <!-- OPERACIONES -->
            <div v-if="codTipoUsu==4">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=101" class="nav-item lista"><a class="nav-link" href="#"> Reserva Admin</a></li>
                        <li @click="menu=123" class="nav-item lista"><a class="nav-link" href="#">Reasignacion Masivas</a></li>

                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li> -->
                        <li @click="menu=201" class="nav-item lista"><a class="nav-link" href="#">Gastos Contado</a></li>
                        <!-- <li @click="menu=202" class="nav-item lista"><a class="nav-link" href="#">Gastos Credito</a></li> -->
                    
                    </ul>
                </li>
                   <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li>
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                        <li @click="menu=501" class="nav-item lista"><a class="nav-link" href="#">Anular Recibos</a></li>
                    </ul>
                </li>
               
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=127" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F1</a></li>
                        <li @click="menu=128" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F2</a></li>
                    </ul>
                </li>
            </div>

            <!-- ADMINISTRAR SYSTEMA -->
            <div v-else-if="codTipoUsu==2">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=101" class="nav-item lista"><a class="nav-link" href="#"> Reserva Admin</a></li>
                        <li @click="menu=96" class="nav-item lista"><a class="nav-link" href="#"> Orden Trabajo</a></li>
                        <li @click="menu=1" class="nav-item lista"><a class="nav-link" href="#">Cliente</a></li>
                        <li @click="menu=122" class="nav-item lista"><a class="nav-link" href="#">Personal</a></li>
                        <li @click="menu=123" class="nav-item lista"><a class="nav-link" href="#">Reasignacion Masivas</a></li>
                        <li @click="menu=125" class="nav-item lista"><a class="nav-link" href="#">Coordinacion Operaciones</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">FINANZAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=215" class="nav-item lista"><a class="nav-link" href="#">Cuentas de Banco</a></li>
                        <li @click="menu=59" class="nav-item lista"><a class="nav-link" href="#">Cuentas Presupuesto</a></li>
                        <li @click="menu=11" class="nav-item lista"><a class="nav-link" href="#">Asignar Presupuesto Global</a></li>
                        <li @click="menu=219" class="nav-item lista"><a class="nav-link" href="#">Control de Presupuestado Global</a></li>
                        <li @click="menu=99" class="nav-item lista"><a class="nav-link" href="#">Registrar Ventas ADM</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD PRESTAMOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=210" class="nav-item lista"><a class="nav-link" href="#">Ingreso Prestamos</a></li>
                        <li @click="menu=211" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Prestamos</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li>
                        <li @click="menu=201" class="nav-item lista"><a class="nav-link" href="#">Gastos Contado</a></li>
                        <li @click="menu=202" class="nav-item lista"><a class="nav-link" href="#">Gastos Credito</a></li>
                        <li @click="menu=203" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitudes</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD DE INGRESO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=212" class="nav-item lista"><a class="nav-link" href="#">Otros Ingresos</a></li>
                        <li @click="menu=213" class="nav-item lista"><a class="nav-link" href="#">Devoluciones Personal</a></li>
                        <li @click="menu=214" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitude otros Ingresos y Devoluciones</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">TRASPASO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=204" class="nav-item lista"><a class="nav-link" href="#">Movimiento de Cuentas Mip</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULO PAGOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=205" class="nav-item lista"><a class="nav-link" href="#">Control Pagos a Credito</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li>
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>

                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CONCILIACIONES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=216" class="nav-item lista"><a class="nav-link" href="#">CONCILIACION DE EGRESOS</a></li>
                        <li @click="menu=217" class="nav-item lista"><a class="nav-link" href="#">CONCILIACION DE INGRESO</a></li>
                        <li @click="menu=218" class="nav-item lista"><a class="nav-link" href="#">REPORTE DE CONCILIACIONES</a></li>
                    </ul>
                </li>

                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CERTIFICADO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=81" class="nav-item lista"><a class="nav-link" href="#">Ejecutivos</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=58" class="nav-item lista"><a class="nav-link" href="#">Lista Frecuencia</a></li>
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=100" class="nav-item lista"><a class="nav-link" href="#">Reserva Operaciones</a></li>
                        <li @click="menu=103" class="nav-item lista"><a class="nav-link" href="#">Reserva Venta</a></li>
                        <li @click="menu=126" class="nav-item lista"><a class="nav-link" href="#">Validacion Reserva</a></li>
                        <li @click="menu=127" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F1</a></li>
                        <li @click="menu=128" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F2</a></li>
                        <li @click="menu=130" class="nav-item lista"><a class="nav-link" href="#">Itinerario de Servicios</a></li>
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                        <li @click="menu=209" class="nav-item lista"><a class="nav-link" href="#">Reporte Estado Resultados</a></li>
                    </ul>
                </li>
            </div>




        <!-- Ejecutivo ventas  -->

        <div v-else-if="codTipoUsu==3">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=101" class="nav-item lista"><a class="nav-link" href="#"> Reserva Admin</a></li>
                        <li @click="menu=96" class="nav-item lista"><a class="nav-link" href="#"> Orden Trabajo</a></li>
                        <li @click="menu=1" class="nav-item lista"><a class="nav-link" href="#">Cliente</a></li>
                        <li @click="menu=122" class="nav-item lista"><a class="nav-link" href="#">Personal</a></li>
                        <li @click="menu=123" class="nav-item lista"><a class="nav-link" href="#">Reasignacion Masivas</a></li>
                        <li @click="menu=125" class="nav-item lista"><a class="nav-link" href="#">Coordinacion Operaciones</a></li>
                        <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li>
                    </ul>
                </li>
    
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li>
                        <li @click="menu=201" class="nav-item lista"><a class="nav-link" href="#">Gastos Contado</a></li>
                        <li @click="menu=202" class="nav-item lista"><a class="nav-link" href="#">Gastos Credito</a></li>
                        <li @click="menu=203" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitudes</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD DE INGRESO</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=212" class="nav-item lista"><a class="nav-link" href="#">Otros Ingresos</a></li>
                        <li @click="menu=213" class="nav-item lista"><a class="nav-link" href="#">Devoluciones Personal</a></li> -->
                        <li @click="menu=214" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitud otros Ingresos y Devoluciones Personal</a></li>
                    </ul>
                </li>
             
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li> -->
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=58" class="nav-item lista"><a class="nav-link" href="#">Lista Frecuencia</a></li> -->
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <!-- <li @click="menu=100" class="nav-item lista"><a class="nav-link" href="#">Reserva Operaciones</a></li> -->
                        <li @click="menu=103" class="nav-item lista"><a class="nav-link" href="#">Reserva Venta</a></li>
                        <!-- <li @click="menu=126" class="nav-item lista"><a class="nav-link" href="#">Validacion Reserva</a></li> -->
                        <!-- <li @click="menu=127" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F1</a></li> -->
                        <!-- <li @click="menu=128" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F2</a></li> -->
                        <!-- <li @click="menu=130" class="nav-item lista"><a class="nav-link" href="#">Itinerario de Servicios</a></li> -->
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                        <li @click="menu=209" class="nav-item lista"><a class="nav-link" href="#">Reporte Estado Resultados</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CERTIFICADO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=81" class="nav-item lista">
                            <a class="nav-link" href="#">Ejecutivos</a>
                        </li>
                    </ul>
                </li>
            </div>


            <!-- ADMINISTRADOR 1/ADMINISTRADOR 2 -->
            <div v-else-if="codTipoUsu==7 || codTipoUsu==8">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=101" class="nav-item lista"><a class="nav-link" href="#"> Reserva Admin</a></li>
                        <li @click="menu=96" class="nav-item lista"><a class="nav-link" href="#"> Orden Trabajo</a></li>
                        <li @click="menu=1" class="nav-item lista"><a class="nav-link" href="#">Cliente</a></li>
                        <li @click="menu=122" class="nav-item lista"><a class="nav-link" href="#">Personal</a></li>
                        <li @click="menu=123" class="nav-item lista"><a class="nav-link" href="#">Reasignacion Masivas</a></li>
                        <li @click="menu=125" class="nav-item lista"><a class="nav-link" href="#">Coordinacion Operaciones</a></li>
                        <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">FINANZAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=215" class="nav-item lista"><a class="nav-link" href="#">Cuentas de Banco</a></li>
                        <li @click="menu=59" class="nav-item lista"><a class="nav-link" href="#">Cuentas Presupuesto</a></li>
                        <li @click="menu=11" class="nav-item lista"><a class="nav-link" href="#">Asignar Presupuesto global</a></li>
                        <li @click="menu=219" class="nav-item lista"><a class="nav-link" href="#">Control Presupuestado Global</a></li>
                        <li @click="menu=99" class="nav-item lista"><a class="nav-link" href="#">Registrar Ventas ADM</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD PRESTAMOS</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=210" class="nav-item lista"><a class="nav-link" href="#">Ingreso Prestamos</a></li> -->
                        <li @click="menu=211" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Prestamos</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=131" class="nav-item lista"><a class="nav-link" href="#">Proveedor Personal</a></li>
                        <li @click="menu=201" class="nav-item lista"><a class="nav-link" href="#">Gastos Contado</a></li>
                        <li @click="menu=202" class="nav-item lista"><a class="nav-link" href="#">Gastos Credito</a></li>
                        <li @click="menu=203" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitudes</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD DE INGRESO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=212" class="nav-item lista"><a class="nav-link" href="#">Otros Ingresos</a></li>
                        <li @click="menu=213" class="nav-item lista"><a class="nav-link" href="#">Devoluciones Personal</a></li>
                        <li @click="menu=214" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitud otros Ingresos y Devoluciones Personal</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD PRESTAMOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=210" class="nav-item lista"><a class="nav-link" href="#">Ingreso Prestamos</a></li>
                        <li @click="menu=211" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Prestamos</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CONCILIACIONES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=216" class="nav-item lista"><a class="nav-link" href="#">CONCILIACION DE EGRESOS</a></li>
                        <li @click="menu=217" class="nav-item lista"><a class="nav-link" href="#">CONCILIACION DE INGRESO</a></li>
                        <li @click="menu=218" class="nav-item lista"><a class="nav-link" href="#">REPORTE DE CONCILIACIONES</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li>
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=58" class="nav-item lista"><a class="nav-link" href="#">Lista Frecuencia</a></li> -->
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=100" class="nav-item lista"><a class="nav-link" href="#">Reserva Operaciones</a></li>
                        <li @click="menu=103" class="nav-item lista"><a class="nav-link" href="#">Reserva Venta</a></li>
                        <li @click="menu=126" class="nav-item lista"><a class="nav-link" href="#">Validacion Reserva</a></li>
                        <li @click="menu=127" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F1</a></li>
                        <li @click="menu=128" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F2</a></li>
                        <li @click="menu=130" class="nav-item lista"><a class="nav-link" href="#">Itinerario de Servicios</a></li>
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                        <li @click="menu=209" class="nav-item lista"><a class="nav-link" href="#">Reporte Estado Resultados</a></li>
                        <li @click="menu=500" class="nav-item lista"><a class="nav-link" href="#">Emision de Factura</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CERTIFICADO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=81" class="nav-item lista">
                            <a class="nav-link" href="#">Ejecutivos</a>
                        </li>
                    </ul>
                </li>
            </div>

            <!-- COBRANZA -->
            <div v-else-if="codTipoUsu==12">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=201" class="nav-item lista">
                            <a class="nav-link" href="#">Gastos Contado</a>
                        </li>
                        <li @click="menu=202" class="nav-item lista"><a class="nav-link" href="#">Gastos Credito</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li>
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                    </ul>
                </li>
            </div>

            <!-- CONTABILIDAD SENIOR -->
            <div v-else-if="codTipoUsu==6">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD GASTOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=201" class="nav-item lista">
                            <a class="nav-link" href="#">Gastos Contado</a>
                        </li>
                        <li @click="menu=202" class="nav-item lista">
                            <a class="nav-link" href="#">Gastos Credito</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">SOLICITUD DE INGRESO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=212" class="nav-item lista"><a class="nav-link" href="#">Otros Ingresos</a></li>
                        <li @click="menu=213" class="nav-item lista"><a class="nav-link" href="#">Devoluciones Personal</a></li>
                        <!-- <li @click="menu=214" class="nav-item lista"><a class="nav-link" href="#">Aprobacion de Solicitude otros Ingresos y Devoluciones</a></li> -->
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">TRASPASO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=204" class="nav-item lista"><a class="nav-link" href="#">Movimiento de Cuentas Mip</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULO PAGOS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=205" class="nav-item lista">
                            <a class="nav-link" href="#">Control Pagos a Credito</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">MODULOS OT</a>
                    <ul class="nav-dropdown-items">
                        <!-- <li @click="menu=206" class="nav-item lista"><a class="nav-link" href="#">Control Recibos OT</a></li> -->
                        <li @click="menu=207" class="nav-item lista"><a class="nav-link" href="#">Recibos PDF OT</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=500" class="nav-item lista"><a class="nav-link" href="#">Emision de Factura</a></li>
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                    </ul>
                </li>
            </div>

            <!-- RESERVA ADMIN -->
            <div v-else-if="codTipoUsu==15">
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=101" class="nav-item lista"><a class="nav-link" href="#"> Reserva Admin</a></li>
                        <li @click="menu=96" class="nav-item lista"><a class="nav-link" href="#"> Orden Trabajo</a></li>
                        <li @click="menu=1" class="nav-item lista"><a class="nav-link" href="#">Cliente</a></li>
                        <li @click="menu=122" class="nav-item lista"><a class="nav-link" href="#">Personal</a></li>
                        <li @click="menu=123" class="nav-item lista"><a class="nav-link" href="#">Reasignacion Masivas</a></li>
                        <li @click="menu=125" class="nav-item lista"><a class="nav-link" href="#">Coordinacion Operaciones</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CERTIFICADO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=81" class="nav-item lista"><a class="nav-link" href="#">Ejecutivos</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=58" class="nav-item lista"><a class="nav-link" href="#">Lista Frecuencia</a></li>
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=100" class="nav-item lista"><a class="nav-link" href="#">Reserva Operaciones</a></li>
                        <li @click="menu=103" class="nav-item lista"><a class="nav-link" href="#">Reserva Venta</a></li>
                        <li @click="menu=126" class="nav-item lista"><a class="nav-link" href="#">Validacion Reserva</a></li>
                        <li @click="menu=127" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F1</a></li>
                        <li @click="menu=128" class="nav-item lista"><a class="nav-link" href="#">Reservas Por Mes F2</a></li>
                        <li @click="menu=130" class="nav-item lista"><a class="nav-link" href="#">Itinerario de Servicios</a></li>
                        <li @click="menu=208" class="nav-item lista"><a class="nav-link" href="#">Fujo de Efectivo</a></li>
                        <li @click="menu=209" class="nav-item lista"><a class="nav-link" href="#">Reporte Estado Resultados</a></li>
                    </ul>
                </li>
            </div>
            <!-- <div v-else>
                <li class="nav-item nav-dropdown" >
                    <a class="nav-link nav-dropdown-toggle" href="#"> MODULO VENTAS</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=102" class="nav-item lista"><a class="nav-link" href="#"> Reserva</a></li>
                        <li @click="menu=96" class="nav-item lista"><a class="nav-link" href="#"> Orden Trabajo</a></li>
                        <li @click="menu=1" class="nav-item lista"><a class="nav-link" href="#">Cliente</a></li>                                    
                    </ul>                        
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">REPORTES</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=58" class="nav-item lista"><a class="nav-link" href="#">Lista Frecuencia</a></li>
                        <li @click="menu=62" class="nav-item lista"><a class="nav-link" href="#">Listado OT TODO Ejecutivo</a></li>
                        <li @click="menu=103" class="nav-item lista"><a class="nav-link" href="#">Reserva Venta</a></li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">CERTIFICADO</a>
                    <ul class="nav-dropdown-items">
                        <li @click="menu=81" class="nav-item lista"><a class="nav-link" href="#">Ejecutivos</a></li>
                    </ul>
                </li>
            </div> -->
        </ul>
    </nav>
</div>