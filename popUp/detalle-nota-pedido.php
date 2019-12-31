<?php
error_reporting(E_ERROR);
include('../includes/funciones.php');
include('../includes/funciones_clientes.php');
include('../includes/conexion.php');
$id = $_REQUEST['id'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>...:: Tavelli ::...</title>
<link rel="shortcut icon" href="../images/fav_ico.png">
<link rel="stylesheet" href="../css/reset.css" type="text/css" />
<link rel="stylesheet" href="../css/styles.css" type="text/css" />
<link rel="stylesheet" href="../css/menu.css" type="text/css" />
<link rel="stylesheet" href="../css/mediaQueries.css" type="text/css" />
<link rel="stylesheet" href="../css/tooltip.css" type="text/css" />
<link rel="stylesheet" href="../css/autocomplete.css" type="text/css" />
<link rel="stylesheet" href="../js/jquery-ui/jquery-ui.css" type="text/css" />
<link rel="stylesheet" href="../js/jquery-ui/jquery-ui.structure.css" type="text/css" />
<link rel="stylesheet" href="../js/jquery-miniNotification/jquery.miniNotification.css" type="text/css" />
<link rel="stylesheet" href="../js/jquery-datatables/media/css/jquery.dataTables.css" type="text/css" />
<link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="../js/fancybox/source/jquery.fancybox.css" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.js"></script>
<script type="text/javascript" src="../js/jquery.menu.js"></script>
<script type="text/javascript" src="../js/jquery.rut.js"></script>
<script type="text/javascript" src="../js/jquery.tooltip.js"></script>
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery-miniNotification/jquery.miniNotification.js"></script>
<script type="text/javascript" src="../js/jquery-datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="../js/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="../js/fancybox/source/jquery.fancybox.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
</head>
		<style type="text/css">
			body { width:900px; margin:20px auto; font-size:14px; font-family:Verdana, Geneva, sans-serif; text-transform: uppercase !important; }
			.titulo { background: url(images/products_24x24.png) 6px 6px no-repeat; padding: 10px 6px 10px 40px !important;	border-bottom:2px solid #c4dcff; }
			.row { width:900px; float:right; border-bottom:1px dotted #999999; }
			.col1 { width:170px; float:right; color:#999; padding:5px 15px; }
			.col2 { width:20px;  float:right; color:#999; padding:5px 15px; }
			.col3 { width:620px; float:right; padding:5px 15px; }
			table { width:900px; border-collapse:collapse;  }
			table thead { background:#333; color:#FFF; border:1px solid #999; }
			table td, table th { border:1px solid #999; padding:10px; font-size:10px; }
			.td1 { width:40px; }
			.td2 { width:60px; }
			.td3 { width:360px; }
			.td4 { width:60px; }
			.td5 { width:60px; }
			.td6 { width:60px; }
			.td7 { width:60px; }
			.td8 { width:100px; }
			.ta-r { text-align:right; }
			.highlighted_01 { background:#FFFFCC; }
			table tr.totales td { border:0px none !important; font-size:12px; font-weight:bold; }
			div.table-responsive, h3 { width:100% !important;}
			.clearing { clear:both; margin-bottom:30px; }
		@media print { .printButton { display:none} }

		</style>
<div class="titulo_pagina">
	<h2 class="col-md-10">Ver detalle nota de pedido :	<?php echo $id; ?> </h2>
	<div class="col-md-2"></div>
	
</div>
<?php 
$datosEncabezado = encabezadoDetallePopUp($id);
?>

<div class="row col-md-10">
	<div class="col-md-6">
		<table class="registros registrosLight registros_1 col-md-5">
			<tr>
				<th nowrap="nowrap">Nota Pedido N&ordm;</th>
				<td><?php echo $id; ?></td>
			</tr>
			<tr>
				<th nowrap="nowrap">Vendedor</th>
				<td>
					<?php	echo $datosEncabezado['VenCod']." - ".$datosEncabezado['VenDes']; ?>
				</td>
			</tr>
			<tr>
				<th nowrap="nowrap">Cond. Venta</th>
				<td>
				<?php echo $datosEncabezado['CveCod']." - ".$datosEncabezado['CveDes']; ?>
				
				</td>	
			</tr>
			<tr>
				<th nowrap="nowrap">Fecha Pedido</th>
				<td><?php echo date_format($datosEncabezado['nvFem'], 'd-m-Y'); ?></td>
			</tr>
			<tr>
				<th nowrap="nowrap">Fecha Entrega</th>
				<td><?php echo date_format($datosEncabezado['nvFeEnt'], 'd-m-Y'); ?></td>
			</tr>			
			<tr>
				<th nowrap="nowrap">Lista de Precio</th>
				<td>
					<?php echo $datosEncabezado['CodLista']." - ".$datosEncabezado['DesLista']; ?>
				</td>
			</tr>
			<tr>
				<th nowrap="nowrap">Cod. Cliente</th>
				<td>
					<?php echo $datosEncabezado['CodAux']." - ".$datosEncabezado['NomAux']; ?>
				</td>
				
			</tr>
			<tr>
				<th nowrap="nowrap">Contacto</th>
				<td>
					<?php echo $datosEncabezado['NomCon']; ?>
				</td>
			</tr>
			<tr>
				<th nowrap="nowrap">Centro de Costo</th>
				<td>
					<?php echo $datosEncabezado['CodiCC']." - ".$datosEncabezado['DescCC']; ?>
				</td>
			</tr>	
			<tr>
				<th nowrap="nowrap">Observaci&oacute;n</th>
				<td>
				<?php echo $datosEncabezado['nvObser']; ?>
				</td>
			</tr>			
		</table>
	</div>
</div>
	<div class="clearing"></div> 
<div class="table-responsive">
	<?php echo listasNotasDeVentasDetallePopUp($id); ?>
</div>
		<div class="clearing"></div> 
		<input type="button" name="imprimir" value="Imprimir Detalle" onclick="window.print();" class="printButton">