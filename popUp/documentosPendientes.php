<?php
include '../includes/conexion.php';
include '../includes/funciones_clientes.php';
include '../includes/funciones.php';

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>...:: Tavelli::...</title>
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
<div class="titulo_pagina col-sm-12"><h2>Documentos Pendientes</h2></div>
<form name="frmtotales" method="post" class="col-sm-10" id="frmtotales">

<?php   echo documentosPendientes();   ?>







</form>
