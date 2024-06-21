<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Recibos OT</title>
</head>
<style>
    .table-format{
        width: 100%;
    }.table-format_gr{
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
        font-size: 12px;
        font-family: arial;
    }.table-format_gr td{
        height:30px;
        font-weight: bold;
        font-size: 12px;
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
        font-size: 12px;
        font-family: arial;
        text-transform: uppercase;
    }.tr-empty{
        height: 20px;
    }.span-format{
        font-size: 14px;
        font-style: italic;
    }.div-gral{
        border: 2px solid blue;
        border-radius: 5px;
        padding: 10px;
    }
</style>
<body class="body-format">
    <div class="div-gral">
        <table style="border: none;" class="table-format">
            <tr>
                <td style="border:none; width: 140px;" align="center" rowspan="3"><img src="http://mip2024.isitecnologia.com/img/logoprint.png" style="width:100px;height:70px;"></td>
                <td style="border:none; width: 140px;" align="left" rowspan="3"><img src="http://mip2024.isitecnologia.com/img/infoprint.PNG" style="width:140px;height:70px;"></td>
                <!-- <td style="border: none; width: 120px;" align="left" rowspan="3">&#187;http://www.mip.com.bo/<br />&#9742; 33416001<br />&#9742; +59177007861<br />&#64; info@mip.com.bo</td> -->
                <!-- <td style="border: none;" align="center" rowspan="3"><img src="http://mip2024.isitecnologia.com/img/logoprint.png" style="width:110px;height:65px;"></td> -->
                <!-- <td align="center" rowspan="4"><img src="img/logoprint.png" style="width:70px;height:40px;"></td> -->
                <td align="left" rowspan="3" style="border: none; font-size: 24px;">RECIBO OT</td>
                <td>&nbsp;&nbsp;&nbsp;Tipo Cambio: &nbsp;&nbsp;&nbsp;<span class="span-format">{{$data->tipo_cambio}}<span></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;Nro Recibo: &nbsp;&nbsp;&nbsp;<span class="span-format">{{$data->nro_recibo}}<span></td>
            </tr>
            <tr>
                <td style="border: none;">
                    <table style="border: none;" class="table-format">
                        <tr align="center">
                            <td>Día</td>
                            <td>Mes</td>
                            <td>Año</td>
                        </tr>
                        <tr  align="center">
                            <td>{{date("d",strtotime($data->fecha))}}</td>
                            <td>{{date("m",strtotime($data->fecha))}}</td>
                            <td>{{date("Y",strtotime($data->fecha))}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br />
        <table class="table-format_gr">
            <tr>
                <td colspan="2">&nbsp;&nbsp;&nbsp;Recibí de: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{$data->recibi_de}}</span></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;La Suma de: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{$data->monto_literal_usd}}</span></td>
                <td>&nbsp;&nbsp;&nbsp;TOTAL USD: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{number_format($data->total_usd,2)}}</span></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;Bolivianos: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{number_format($data->importe_bs,2)}}</span></td>
                <td>&nbsp;&nbsp;&nbsp;Dolares: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{number_format($data->importe_usd,2)}}</span></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;Tipo Pago: 
                    @if($data->tipo_pago == 1)
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Efectivo( X )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Cheque( )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Transferencia( )</span>
                    @elseif($data->tipo_pago == 2)
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Efectivo( )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Cheque( X )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Transferencia( )</span>
                    @else
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Efectivo( )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Cheque( )</span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">Transferencia( X )</span>
                    @endif
                </td>
                <td>&nbsp;&nbsp;&nbsp;Cta. Bancaria:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="span-format">{{$data->banco_destino}}</span></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;&nbsp;&nbsp;Por concepto de:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-format">{{$data->por_concepto}}</span></td>
            </tr>
        </table>
        Nro OT: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        @foreach($detalle as $item)
                <span>{{$item->nro_ot}} &nbsp;</span>
        @endforeach
        <br />
        <br />
        <br />
        <br />
        <table style="border: none" class="table-format">
            <tr>
                <td style="border: none;" align="center">_______________________</td>
                <td style="border: none;" align="center">_______________________</td>
            </tr>
            <tr>
                <td style="border: none;" align="center">Entregue Conforme</td>
                <td style="border: none;" align="center">Recibí Conforme</td>
            </tr>
        </table>        
    </div>
</body>
</html>