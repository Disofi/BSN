<?php
$condicion = $_REQUEST['condicion'];


if($condicion == 'impuestos')
{
	include 'conexion.php';
	$codigos = $_REQUEST['codigos'];
	$subtotal = $_REQUEST['subtotal'];
	$codigosSeparado = explode(',', $codigos);
	$subTotalSeparado = explode(',', $subtotal);
	$countCodigo = count($codigosSeparado);
	$fecha_hoy = date("d/m/Y");
	$precioImpuesto = 0;
	$total = 0;
	$sumaSubTotal = 0;
	for($c =0; $c < $countCodigo; $c++)
	{
		$query = " SELECT iwti.codprod, iwti.codimpto,iwtv.valpctini, ".$subTotalSeparado[$c]." as afectoimpto,((iwtv.valpctini * ".$subTotalSeparado[$c].")/100) as impto ";
		$query.= " FROM ".$bd['softland'].".[iw_timprod] iwti ";
		$query.= " LEFT JOIN ".$bd['softland'].".[iw_timpval] iwtv on iwti.codimpto = iwtv.codimpto ";
		$query.= " WHERE iwti.codprod = '".$codigosSeparado[$c]."' ";
		$query.= " AND convert(datetime,'".$fecha_hoy."',103) >iwtv.fecinivig AND convert(datetime,'".$fecha_hoy."',103) < iwtv.fecfinvig ";
		//echo $query;
			//$res = sqlsrv_query($conn, $query, array(), array('Scrollable' => 'buffered'));
			$res = sqlsrv_query( $conn, $query , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$num = sqlsrv_num_rows($res);
			//echo $query."<br>";
			while($row = sqlsrv_fetch_array($res))
				{
					$precioImpuesto += $row['impto'];
					$sumaSubTotal += $subTotalSeparado[$c];
				}	
		
	}
	//echo $precioImpuesto;	

	
	$total = ($sumaSubTotal + $precioImpuesto);
	$linkspon["impuesto"]= $precioImpuesto;
    $linkspon["subtotal"]= $sumaSubTotal;
	$linkspon["total"] = $total;
	
    print json_encode($linkspon);
	
	
}
?>