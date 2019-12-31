<?php
include('includes/conexion.php');

$codigo = $_GET['codigo'];

$sel = "SELECT a.codprod, b.DesProd, a.cantidad, a.preciovta, (a.cantidad * b.PrecioVta) as Subtot 
		FROM ".$bd['dsparam'].".DS_PromocionesDet as a
		LEFT JOIN ".$bd['softland'].".iw_tprod as b ON a.codprod=b.Codprod COLLATE Modern_Spanish_CI_AS
		WHERE Codpromo='".$codigo."'";
$res = sqlsrv_query($conn, $sel);

if ($res)
	{
	$i=0;
	while($row = sqlsrv_fetch_array($res))
		{
		$data[0][$i] = $row['DesProd'];
		$data[1][$i] = $row['codprod'];
		$data[2][$i] = $row['cantidad'];
		$data[3][$i] = $row['preciovta'];
		$data[4][$i] = $row['Subtot'];
		$i++;
        } 
	}
echo json_encode($data); 
?>