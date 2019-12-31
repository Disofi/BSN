<?php

/*  MOSTRAR MENU SEGÚN PERFILES DE USUARIO  */
/* 1 = Administrador                        */
/* 2 = Vendedor                             */
/* 3 = Aprobador                            */

$tipuser = $_SESSION['dsparam']['id_tipo_usuario'];

$menuPage = trim(substr($_SESSION['_pagina'], 0, -4));
$cssMenu = array
	(
	'usuarios' => '',
	'clientes' => '',
	'notas_pedido' => '',
	'mantenedor' => ''
	);



$html_menu = '
<nav class="navbar navbar-default col-sm-2" role="navigation">
 
<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		<span class="sr-only">Desplegar men&uacute;</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
</div>

<div class="collapse navbar-collapse navbar-ex1-collapse">
	<div id="menu" class="nav navbar-nav">
		<ul>';

// MANTENEDOR PARAMETROS
if ($tipuser == 5)
	{
	$cssMenu['mantenedor'] = array('linkText' => '', 'menu' => '', 'subMenu' => array(0 => '', 1 => '', 2 => '', 3 => ''));
	if($menuPage == 'usuarios' || $menuPage == 'usuarios-form' || $menuPage == 'mantenedor-relacion' || $menuPage == 'mantenedor-relacion-form' || $menuPage == 'mantenedor-sucursal'
								|| $menuPage == 'mantenedor-sucursal-form' )
		{
		$cssMenu['mantenedor']['linkText'] = ' open';
		$cssMenu['mantenedor']['menu'] = ' style="display:block;"';
		if($menuPage == 'usuarios') 					{ $cssMenu['mantenedor']['subMenu'][0] = ' class="active"'; }
		if($menuPage == 'usuarios-form') 				{ $cssMenu['mantenedor']['subMenu'][0] = ' class="active"'; }
		if($menuPage == 'mantenedor-relacion')			{ $cssMenu['mantenedor']['subMenu'][1] = ' class="active"'; }
		if($menuPage == 'mantenedor-relacion-form')		{ $cssMenu['mantenedor']['subMenu'][1] = ' class="active"'; }
		if($menuPage == 'mantenedor-sucursal')			{ $cssMenu['mantenedor']['subMenu'][2] = ' class="active"'; }
		if($menuPage == 'mantenedor-sucursal-form')		{ $cssMenu['mantenedor']['subMenu'][2] = ' class="active"'; }

		}
	$html_menu .= '
	<li class="has-sub'.$cssMenu['mantenedor']['linkText'].'"><a href="#"><span class="strong">Mantenedores</span></a>
		<ul'.$cssMenu['mantenedor']['menu'].'>
			<li'.$cssMenu['mantenedor']['subMenu'][0].'><a href="index.php?mod=usuarios">Usuarios</a></li>
			<li'.$cssMenu['mantenedor']['subMenu'][1].'><a href="index.php?mod=mantenedor-relacion">Asociación Vendedor, Bodega, Grupo</a></li>
		</ul>
	</li>';
//<li'.$cssMenu['mantenedor']['subMenu'][2].'><a href="index.php?mod=mantenedor-sucursal">Sucursales</a></li>
	

	$cssMenu['notasVenta'] = array('linkText' => '', 'menu' => '', 'subMenu' => array(0 => '', 1 => '', 2 => '', 3 => ''));
	if($menuPage == 'list-notas-pedido' || $menuPage == 'list-notas-pedido-detalle' || $menuPage == 'agrupar-notas-pedido')
		{
			$cssMenu['notasVenta']['linkText'] = ' open';
			$cssMenu['notasVenta']['menu'] = ' style="display:block;"';
			if($menuPage == 'list-notas-pedido') 				{ $cssMenu['notasVenta']['subMenu'][0] = ' class="active"'; }
			if($menuPage == 'list-notas-pedido-detalle')		{ $cssMenu['notasVenta']['subMenu'][0] = ' class="active"'; }
			if($menuPage == 'agrupar-notas-pedido')				{ $cssMenu['notasVenta']['subMenu'][1] = ' class="active"'; }
		}
	$html_menu.= '
	<li class="has-sub'.$cssMenu['notasVenta']['linkText'].'"><a href="#"><span class="strong">Notas de Pedido</span></a>
		<ul'.$cssMenu['notasVenta']['menu'].'>
			<li'.$cssMenu['notasVenta']['subMenu'][0].'><a href="index.php?mod=list-notas-pedido">Listas Notas de Pedido</a></li>
			<li'.$cssMenu['notasVenta']['subMenu'][1].'><a href="index.php?mod=agrupar-notas-pedido">Agrupar Notas de Pedido</a></li>
		</ul>
	</li>';

		
		
		
		
	/*$html_menu.= '
	<li class="has-sub'.$cssMenu['informe']['linkText'].'"><a href="#"><span class="strong">Informes</span></a>
		<ul'.$cssMenu['informe']['menu'].'>
			<li'.$cssMenu['informe']['subMenu'][0].'><a href="index.php?mod=informe-guias">Informe Guias</a></li>
			
		</ul>
	</li>';*/
//<li'.$cssMenu['informe']['subMenu'][1].'><a href="index.php?mod=agrupar-notas-pedido">Informe Producci&oacute;n</a></li>
	}
	
if ($tipuser == 1)
{
	if($menuPage == 'informe-guias' || $menuPage == 'informe-guias-ver'  || $menuPage == 'informe-produccion' || $menuPage == 'informe-produccion-ver'
		|| $menuPage == 'informe-saldo-cliente'|| $menuPage == 'informe-saldo-cliente-ver' || $menuPage == 'informe-ventas'  || $menuPage == 'informe-ventas-ver'
		|| $menuPage == 'informe-ventas-detalles'  || $menuPage == 'informe-ventas-detalles-ver'
        || $menuPage == 'informe-trazabilidad-guias'  || $menuPage == 'informe-trazabilidad-guias-ver'
		|| $menuPage == 'informe-seguimiento'  || $menuPage == 'informe-seguimiento-ver' )
		{
			$cssMenu['informe']['linkText'] = ' open';
			$cssMenu['informe']['menu'] = ' style="display:block;"';
			
			//if($menuPage == 'informe-guias') 		              { $cssMenu['informe']['subMenu'][0] = ' class="active"'; }
			//if($menuPage == 'informe-guias-ver')		          { $cssMenu['informe']['subMenu'][0] = ' class="active"'; }
			if($menuPage == 'informe-ventas')		              { $cssMenu['informe']['subMenu'][1] = ' class="active"'; }
			if($menuPage == 'informe-ventas-ver')		          { $cssMenu['informe']['subMenu'][1] = ' class="active"'; }
			//if($menuPage == 'informe-ventas-detalles')	          { $cssMenu['informe']['subMenu'][2] = ' class="active"'; }
			//if($menuPage == 'informe-ventas-detalles-ver')		  { $cssMenu['informe']['subMenu'][2] = ' class="active"'; }
			//if($menuPage == 'informe-trazabilidad-guias')	      { $cssMenu['informe']['subMenu'][3] = ' class="active"'; }
			//if($menuPage == 'informe-trazabilidad-guias-ver')	  { $cssMenu['informe']['subMenu'][3] = ' class="active"'; }        
			//if($menuPage == 'informe-seguimiento')	      		  { $cssMenu['informe']['subMenu'][4] = ' class="active"'; }
			//if($menuPage == 'informe-seguimiento-ver')	      	  { $cssMenu['informe']['subMenu'][4] = ' class="active"'; }


		}
		
		$html_menu.= '
	<li class="has-sub'.$cssMenu['informe']['linkText'].'"><a href="#"><span class="strong">Informes</span></a>
		<ul'.$cssMenu['informe']['menu'].'>
			
			<li'.$cssMenu['informe']['subMenu'][1].'><a href="index.php?mod=informe-ventas">Informe Ventas</a></li>
			
		</ul>
	</li>';
	/*
	<li'.$cssMenu['informe']['subMenu'][0].'><a href="index.php?mod=informe-guias">Informe Guias</a></li>
	<li'.$cssMenu['informe']['subMenu'][2].'><a href="index.php?mod=informe-ventas-detalles">Informe Ventas Detalles</a></li>
    <li'.$cssMenu['informe']['subMenu'][3].'><a href="index.php?mod=informe-trazabilidad-guias">Informe Trazabilidad Guias</a></li>
	<li'.$cssMenu['informe']['subMenu'][4].'><a href="index.php?mod=informe-seguimiento">Informe Seguimiento</a></li>
	*/
	
}	
	
	
// CLIENTES...
if (($tipuser == 2) or ($tipuser == 1))
	{
	}
	
		$html_menu .= '
		</ul>
		</li></ul>
	</div>
</div>
</nav>';

if($_SESSION['dsparam']['id_tipo_usuario'] == 2){
	echo "<br><br>";
}
else
{
	echo $html_menu;	
}


?>