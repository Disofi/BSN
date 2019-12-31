<?php
session_start();
include('includes/conexion.php');
include('includes/funciones.php');
include('includes/funciones_clientes.php');

if(isset($_POST['seccion']))
	{
	$seccion = trim(strtolower($_POST['seccion']));

	if($seccion == 'clientes-notapedido')
		{
		$tipo  = trim($_POST['tipo']);
		$grupo = trim($_POST['grupo']);
		echo clientesSelectoresProductos($tipo, $grupo);
		}
	
	}

?>