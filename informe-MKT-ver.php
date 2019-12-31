<?php
ini_set('memory_limit', '2000M');
ini_set('max_execution_time', 9999);
include('includes/funciones.php');
include('includes/funciones_informes.php');
$fecdes   = $_REQUEST['fecdes'];
$fechas   = $_REQUEST['fechas'];

?>
<div>


<div class="row col-sm-12">
<div class="titulo_pagina"><h2 class="col-md-12">Informes &gt; MKT</h2></div>
</div>
<div class="row col-sm-12">
    <div class="col-sm-2" style="text-align:right">Rango Fechas</div>
    <div class="col-sm-1">:</div>
    <div class="col-sm-4"><?php echo $fecdes.' - '.$fechas; ?></div>
	<br></br>
</div>
</div>
<?php 
	echo informeMKT($fecdes,$fechas);
?>