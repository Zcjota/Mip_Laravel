<table>
    <thead>
        <tr>
            <th>PROCESO</th>
            <th>TIPO</th>
            <th>FECHA</th>
            <th>BANCO ORIGEN</th>
            <th>BANCO DESTINO</th>
            <th>PROV_PERS</th>
            <th>RESPONSABLE</th>
            <th>PRESUPUESTO</th>
            <th>IMPORTE BS</th>
            <th>IMPORTE USD</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{$item->proceso}}</td>
                <td>{{$item->mov_banco}}</td>
                <td>{{$item->fecha}}</td>
                <td>{{$item->banco_origen}}</td>
                <td>{{$item->banco_destino}}</td>
                <td>{{$item->prov_pers}}</td>
                <td>{{$item->responsable}}</td>
                <td>{{$item->nro_cuenta_presupuesto}}</td>
                <td>{{$item->importe_bs}}</td>
                <td>{{$item->importe_usd}}</td>
            </tr>
        @endforeach
    </tbody>
</table>