@extends('principal')
@section('contenido')

<template v-if="menu==0">
    <principal></principal>
</template>

<template v-else-if="menu==1">
    <cliente></cliente>
</template>

<template v-else-if="menu==81">
    <certificadoejecutivo></certificadoejecutivo>
</template>

<template v-else-if="menu==62">
    <reporteejecutivo></reporteejecutivo>
</template>

<template v-else-if="menu==58">
    <reportefrecuencia></reportefrecuencia>
</template>

<template v-else-if="menu==95">
    <reserva></reserva>
</template>

<template v-else-if="menu==98">
    <reservaadmin></reservaadmin>
</template>

<template v-else-if="menu==100">
    <reportereservaoperaciones></reportereservaoperaciones>
</template>

<template v-else-if="menu==101">
    <reservaadmin2></reservaadmin2>
</template>

<template v-else-if="menu==102">
    <reserva1></reserva1>
</template>

<template v-else-if="menu==103">
    <reportereservaventa></reportereservaventa>
</template>

<template v-else-if="menu==97">
    <cliente></cliente>
</template>

<template v-else-if="menu==96">
    <otvista></otvista>
</template>

<template v-else-if="menu==122">
    <personal></personal>
</template>

<template v-else-if="menu==123">
    <reasignaciontecnico></reasignaciontecnico>
</template>

<template v-else-if="menu==125">
    <asignartecnico></asignartecnico>
</template>

<template v-else-if="menu==126">
    <reportereservaoperacionesmigracion></reportereservaoperacionesmigracion>
</template>

<template v-else-if="menu==127">
    <reserva_mes></reserva_mes>
</template>

<template v-else-if="menu==128">
    <reserva_mes_f2></reserva_mes_f2>
</template>

<template v-else-if="menu==129">
    <oef></oef>
</template>

<template v-else-if="menu==130">
    <itinerarios-servicios></itinerarios-servicios>
</template>

<template v-else-if="menu==131">
    <proveedorpersonal></proveedorpersonal>
</template> 

<!-- BPJ0 Gastos Contado-Credito -->
<template v-else-if="menu==201">
    <solicitud_gasto_contado></solicitud_gasto_contado>
</template>

<!--   JR18 Ingresos -->
<template v-else-if="menu==212">
    <solicitud_ingreso></solicitud_ingreso>
</template>

<!--   JR18 Ingresos -->
<template v-else-if="menu==213">
    <solicitud_ingreso_otros></solicitud_ingreso_otros>
</template>

<!--   JR18 Ingresos -->
<template v-else-if="menu==214">
    <aprobacion_solicitud_ingreso></aprobacion_solicitud_ingreso>
</template>

<!-- JR18 Banco cuenta  -->
<template v-else-if="menu==215">
    <banco_cuenta></banco_cuenta>
</template>

<!-- JR18 Cuenta Cont  -->
<template v-else-if="menu==59">
    <cuenta_cont></cuenta_cont>
</template>


<template v-else-if="menu==202">
    <solicitud_gasto_credito></solicitud_gasto_credito>
</template>

<template v-else-if="menu==203">
    <aprobacion_solicitud></aprobacion_solicitud>
</template>

<template v-else-if="menu==204">
    <movimiento_cuenta_banco></movimiento_cuenta_banco>
</template>

<template v-else-if="menu==205">
    <control_pago_credito></control_pago_credito>
</template>
 
<template v-else-if="menu==206">
    <control_recibos_ot></control_recibos_ot>
</template>

<template v-else-if="menu==207">
    <recibos_ot></recibos_ot>
</template>

<template v-else-if="menu==208">
    <reporte_bancario></reporte_bancario>
</template>

<template v-else-if="menu==209">
    <reporte_estado_resultado></reporte_estado_resultado>
</template>

<template v-else-if="menu==210">
    <solicitud_prestamo></solicitud_prestamo>
</template>

<template v-else-if="menu==211">
    <aprobacion_prestamo></aprobacion_prestamo>
</template>

<!-- JR18 Conciliacion  -->
<template v-else-if="menu==216">
    <conciliacion_s></conciliacion_s>
</template>

<template v-else-if="menu==217">
    <conciliacion_si></conciliacion_si>
</template>

<template v-else-if="menu==218">
    <reporte_ci></reporte_ci>
</template>

<template v-else-if="menu==11">
    <asignar_cuenta></asignar_cuenta>
</template>

<!-- Ernesto Noco -->
<template v-else-if="menu==500">
<!-- emision_factura -->
<emision_factura></emision_factura>
</template>

<template v-else-if="menu==501">
    <recibos_anular></recibos_anular>
</template>

<template v-else-if="menu==219">
    <controlp-global></controlp-global>
</template>

<template v-else-if="menu==99">
    <registro-ventasadm></registro-ventasadm>
</template>


<template v-else>
    <construccion></construccion>
</template>

@endsection