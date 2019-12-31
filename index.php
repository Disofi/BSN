<?php
session_start();
include('includes/conexion.php');
if(isset($_GET['mod']))
	{
	if(trim($_GET['mod']) != '')
		{
		$pagina = trim($_GET['mod']).'.php';
		}
	if(trim($_GET['mod']) == '')
		{
		header('Location: '.$url_inicio);
		exit(0);
		}
	}
else
	{
	$url_def = str_replace('index.php?mod=', '', $url_inicio); 
	$url_def .= '.php';
	$pagina = $url_def;
	}
$_SESSION['_pagina'] = $pagina;
include('includes/verificar_sesion.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>BSN - Disofi</title>
<!--<link rel="shortcut icon" href="images/indice.ico">--->
<link rel="stylesheet" href="css/reset.css" type="text/css" />
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="stylesheet" href="css/menu.css" type="text/css" />
<link rel="stylesheet" href="css/mediaQueries.css" type="text/css" />
<link rel="stylesheet" href="css/tooltip.css" type="text/css" />
<link rel="stylesheet" href="css/autocomplete.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.structure.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-miniNotification/jquery.miniNotification.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-datatables/media/css/jquery.dataTables.css" type="text/css" />
<link rel="stylesheet" href="js/bootstrap/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css" type="text/css" />
<link href="fonts/fonts.css?family=league+gothic&subset=latin,latin-ext" rel="stylesheet" type="text/css">
<link href="fonts/fonts.css?family=infinity&subset=latin,latin-ext" rel="stylesheet" type="text/css">
<link href="fonts/fonts.css?family=nexa+lightregular&subset=latin,latin-ext" rel="stylesheet" type="text/css">
<link href="fonts/fonts.css?family=nexa+boldregular&subset=latin,latin-ext" rel="stylesheet" type="text/css">
  
<script type="text/javascript" src="js/jquery-1.11.1.js"></script>
<script type="text/javascript" src="js/jquery.menu.js"></script>
<script type="text/javascript" src="js/jquery.rut.js"></script>
<script type="text/javascript" src="js/jquery.tooltip.js"></script>
<script type="text/javascript" src="js/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-miniNotification/jquery.miniNotification.js"></script>
<script type="text/javascript" src="js/jquery-datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>

<script>
$(document).ready(function()
	{
	// Ventanas comunes
	$('a.zoom, a.print, a.hojpat, a.letrero, a.info1, a.info2, a.info3').fancybox({
		type: 'iframe',
		autoSize : false,
		beforeLoad : function(){         
			this.width  = parseInt(this.element.data('fancybox-width'));
			this.height = parseInt(this.element.data('fancybox-height'));
			this.modal = this.element.data('fancybox-modal');
			},
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600, 
		speedOut		: 400, 
		overlayShow		: false,
		helpers			: { 'title' : null }
		});
	// Ventana modal para 'Guias de Despacho / Recepcion'...
	$('a.detRecep, a.despPreview, a.loadPrinter').fancybox({
		modal: true,
		type: 'iframe',
		autoSize : false,
		beforeLoad : function(){         
			this.width  = parseInt(this.element.data('fancybox-width'));
			this.height = parseInt(this.element.data('fancybox-height'));
			this.modal = this.element.data('fancybox-modal');
			},
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600, 
		speedOut		: 400, 
		overlayShow		: false,
		helpers			: { 'title' : null }
		});
	});
</script>
</head>
<body>
<header>
   	<h1 style="float:left;" class="margin_left_20"><a href="index.php?mod=<?php print $_SESSION['urlInicio']; ?>" title="Alma Brands">Alma Brands</a></h1>

	<div class="user_info float_right">
		<p class="float_right">
		<span>Bienvenido, <strong><?php echo $_SESSION['dsparam']['usuario'];?> - 
			<?php 	
				if($_SESSION['dsparam']['tipo_usuario'] == 'Administrador')
				{
					echo $_SESSION['dsparam']['cliente'];
				}
				else
				{
					echo $_SESSION['dsparam']['nombreUsuario'];
				}
				
			?>
			</strong></span><br />
		<span>Tipo Usuario: <strong><?php echo $_SESSION['dsparam']['tipo_usuario'];?></strong></span><br />
		<span>Versión: <strong>1.0.001</strong></span><br />
		<a href="logout.php" class="logout float_right">Cerrar Sesi&oacute;n</a>
		</p>
	</div>
</header>
<div id="mini-notification"><p></p></div>
<div id="page-loader"><div>&nbsp;</div></div>


<div id="main_content_holder">
	<?php include('includes/menu.php');?>
	<!---<div id="main_content" class="col-sm-10 margin_bottom_100"> -->
	<?php
if($_SESSION['dsparam']['id_tipo_usuario'] == 2){
	echo '<div id="main_content_vendedor" class="col-sm-12 margin_bottom_100">';
}
else
{
	echo '<div id="main_content" class="col-sm-10 margin_bottom_100">';
}
?>
	
	<?php
	if(file_exists($pagina))
		{
		include_once($pagina);
		}
	else
		{
		echo '<script type="text/javascript">window.location.href=\''.$url_inicio.'\';</script>';
		}
	?>
	</div>
</div>	
</body>
</html>