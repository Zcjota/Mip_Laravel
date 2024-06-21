<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($data->tipo_si == 2)
            Imprimir Solicitud Ingreso Devoluciones Personal
        @elseif($data->tipo_si == 1)
            Imprimir Solicitud de Ingresos Otros
        @else
            Imprimir Solicitud
        @endif
    </title>
</head>
<style>
    .table-format{
        width: 100%;
    }.td-color-format{
        background-color: #c4bcb5;
    }.td-izquierda-format{
        float: left;
    }.td-centro-format{
        float: center;
    }.thead-tr-format{
        background-color: #304151;
        color: #ffffff;
        height: 25px
    }.body-format{
        font-weight: bold;
        font-size: 10px;
        font-family: arial;
    }.table-border-tr-format{
        border: #000 1px solid;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .tbody-format{
        font-weight: bold;
        font-size: 9px;
        font-family: arial;
        text-transform: uppercase;
    }
</style>
<body class="body-format">
    <div>
        <table class="table-format">
            <tr>
            <td align="center" rowspan="3"><img src="http://mip.isitecnologia.com/img/logoprint.png" style="width:70px;height:40px;"></td>
                <!-- <td align="center" rowspan="3"><img src="img/logoprint.png" style="width:70px;height:40px;"></td> -->
                <td align="center" rowspan="3" colspan="2">SOLICITUD DE
                    @if($data->tipo_si == 2)
                        DEVOLUCIONES PERSONAL-MIP PLAGAS FIFOS
                    @elseif($data->tipo_si == 1)
                         OTROS INGRESOS-MIP PLAGAS FIFOS
                    @else
                        INGRESOS-MIP PLAGAS FIFOS
                    @endif</td>
                <td>Revision: 02</td>
            </tr>
            <tr>
                <td>Fecha: {{now()}}</td>
            </tr>
            <tr>
                <td>PA-05-R04</td>
            </tr>
            <tr>
                <td align="right" class="td-color-format">Area: </td>
                <td>CONTABILIDAD</td>
                <td align="right" class="td-color-format">Nro: </td>
                <td>{{$data->nroCorrelativo}}</td>
            </tr>
            <tr>
                <td align="right" class="td-color-format">Solicitado por:</td>
                <td>{{$data->nombre_usuario}}</td>
                <td align="right" class="td-color-format">Fecha Solicitud: </td>
                <td>{{$data->fecha_si}}</td>
            </tr>
            <tr>
                <td align="right" class="td-color-format">Tipo Pago: </td>
                @if($data->tipo_si=='3')
                    <td>Tipo de Pago: DEVOLUCION/PERSONAL ( X )</td>
                    <td align="right" class="td-color-format">Fecha de Pago:</td>
                    <td>{{$data->fecha_pago}}</td>
                @elseif($data->tipo_pago=='EFECTIVO')
                    <td colspan="3">EFECTIVO( X )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CHEQUE(  )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRANSFERENCIA(  )</td>
                @elseif ($data->tipo_pago=='CHEQUE')
                    <td colspan="3">EFECTIVO(  )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CHEQUE( X )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRANSFERENCIA(  )</td>
                @elseif ($data->tipo_pago=='TRANSFERENCIA')
                    <td colspan="3">EFECTIVO(  )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CHEQUE(  )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRANSFERENCIA( X )</td>
                @endif
            </tr>
            <tr>
                <td align="right" class="td-color-format">Tipo Gasto: </td>
            
                @if($data->tipo_gasto=='F')
                    <td colspan="3">FIJO( X )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EVENTUAL(  )</td>
                @elseif ($data->tipo_gasto=='E')
                    <td colspan="3">FIJO(  )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EVENTUAL( X )</td>
                @endif
            </tr>
            <!-- <tr>
                <td align="right" class="td-color-format">Glosa: </td>
                <td colspan="3" style="text-transform: uppercase; font-size: 9px;">{{$data->glosa}}</td>
            </tr> -->
        </table>
        <br />
        <table class="table-format">
            <thead>
                <tr align="center" class="thead-tr-format">
                    <th>RESPONSABLE</th>
                    <th>PRESUPUESTO</th>
                    <th style="width:200px;">DETALLE</th>
                    <th>BS</th>
                    <th>USD</th>
                    <th>DESTINO</th>
                </tr>
            </thead>
            <tbody class="tbody-format">
                @foreach($detalle as $item)
                <tr>
                    <td>{{$item->nombre_proveedor}}</td>
                    <td>{{$item->cuenta_contable}}</td>
                    <td style="text-transform: uppercase; font-size: 8px; width:200px;">{{$item->detalle}}</td>
                    <td align="right">{{$item->importe_bs}}</td>
                    <td align="right">{{$item->importe_usd}}</td>
                    <td>{{$item->banco_destino}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td align="right" class="td-color-format" colspan="3">TOTAL:</td>
                    <td align="right">{{$data->total_bs}}</td>
                    <td align="right">{{$data->total_usd}}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <br />
        <table  style="border: none" class="table-format">
            <tr>
                <th class="td-color-format">Aprobado</th>
                <th align="left" class="td-color-format">Fecha: </th>
                <th style="border: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th class="td-color-format">Aprobado</th>
                <th align="left" class="td-color-format">Fecha: </th>
            </tr>
            <tr>
                <td></td>
                <td>Firma: </td>
                <td  style="border: none"></td>
                <td></td>
                <td>Firma: </td>
            </tr>
        </table>
        <br />
        <label><span>Nota:</span> En la Aprobacion de esta solicitud bastara solo una firma.</label>
        <table class="table-format">
            <tr>
                <th align="left" style="width: 100px;" class="td-color-format">En caso de tener Observación, detallar: </th>
                <th align="left" style="text-transform: uppercase; font-size: 9px;">{{$data->glosa}}</th>
            </tr>
            <tr>
                <th colspan="2" align="left" class="td-color-format">PAGO DEL {{$data->banco_nombre}}, CTA. CTTE.: {{$data->banco_nro_cuenta}}-{{$data->banco_moneda}}</th>
            </tr>
        </table>
        <br />
        <br/>
        <table style="border: none" class="table-format">
            <tr>
                <td align="center" class="td-color-format">Cerrado por:</td>
                <th style="border: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <td align="center" class="td-color-format">Recibido por:</td>
                <th style="border: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <td align="center" class="td-color-format">Registrado por:</td>
            </tr>
            <tr>
                <td>Nombre: </td>
                <td  style="border: none"></td>
                <td>Nombre: </td>
                <td  style="border: none"></td>
                <td>Nombre: </td>
            </tr>
            <tr>
                <td>Fecha: </td>
                <td  style="border: none"></td>
                <td>Fecha: </td>
                <td  style="border: none"></td>
                <td>Fecha: </td>
            </tr>
        </table>
    </div>
</body>
</html>