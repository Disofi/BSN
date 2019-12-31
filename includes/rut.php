<?php
include('conexion.php');
$rut = $_GET['ruta']; 
$sql = "SELECT isNull(COUNT(*),0) AS resultado FROM ".$bd['softland'].".[cwtauxi] WHERE CodAux='".$rut."'";
$rs = sqlsrv_query($conn, $sql);
while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
	$existe = $row['resultado'];
	}
print $existe;
?>