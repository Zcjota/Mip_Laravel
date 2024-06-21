<?php
	include("../lib/conex.php");      

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition:  filename=\"ReporteBancario.XLS\";");

	$buscar = isset($_REQUEST['buscar'])?$_REQUEST['buscar']:'';
	$fechai = isset($_REQUEST['fechai'])?$_REQUEST['fechai']:'';
	$fechaf = isset($_REQUEST['fechaf'])?$_REQUEST['fechaf']:'';
	
	if (VerificaConBD()){	
		echo '{"Success": false, "errors":{"reason": "No se puede conectar con la BD"}}';		
		exit;
	}	

	if ($buscar == "ALL") {
		$sql_buscar = "";
	} else {
		$sql_buscar = " AND (banco_origen like '%$buscar%' OR banco_destino like '%$buscar%')";
	}
	
	$sql =  "SELECT *FROM vi_reporteBancario 
			WHERE fecha between '$fechai' and '$fechaf' $sql_buscar
			ORDER BY fecha DESC";
	$resultado = sqlsrv_query(ConectarConBD(), $sql); 
	$data = array();
	$fecha = date("d/m/Y");
	$Encabezados = "<table border = 1>" .
			  "		<tr>" .
			  "			<th colspan='11'> Reporte Bancario  </th> ".
			  "		</tr>".
			  "		<tr>" .
			  "			<th colspan='11'> Fecha de Reporte: ".  $fecha ." </th> ".
			  "		</tr>".
			  "		<tr>" .			  
			  "			<th> Proceso </th> ".
			  "			<th> Movimiento </th> ".
			  "			<th> Fecha  </th>" .
			  "			<th> Banco Origen  </th>" .
			  "			<th> Banco Destino  </th>" .
			  "			<th> Prov. Pers.  </th>" .
			  "			<th> Responsable  </th>" .
			  "			<th> Nro Cuenta Presupuesto </th>" .
			  "			<th> Cuenta Presupuesto </th>" .
			  "			<th> Importe Bs </th>" .
			  "			<th> Importe USD </th>" .
			  "		</tr>";
	$Registros ='';	
	while ($row = sqlsrv_fetch_array($resultado)){
		$Registros = $Registros .
			  "		<tr>" .
			  "			<td>" . $row['proceso']  . "</td>" .
			  "			<td>" . $row['mov_banco']  . "</td>" .
			  "			<td>" . $row['fecha']  . "</td>" .
			  "			<td>" . $row['banco_origen']  . "</td>" .
			  "			<td>" . $row['banco_destino']  . "</td>" .
			  "			<td>" . $row['prov_pers']  . "</td>" .
			  "			<td>" . $row['responsable']  . "</td>" .
			  "			<td>" . $row['nro_cuenta_presupuesto']  . "</td>" .
			  "			<td>" . $row['cuenta_presupuesto']  . "</td>" .
			  "			<td>" . $row['importe_bs']  . "</td>" .
			  "			<td>" . $row['importe_usd']  . "</td>" .
			  "		</tr>";	
	}
	echo $Encabezados . $Registros . '</table>';