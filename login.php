<?php
session_start();
include('includes/conexion.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>BSN - Disofi</title>
<!--<link rel="shortcut icon" href="images/indice.ico">-->
<link rel="stylesheet" href="css/reset.css" type="text/css" />
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="stylesheet" href="css/menu.css" type="text/css" />
<link rel="stylesheet" href="css/mediaQueries.css" type="text/css" />
<link rel="stylesheet" href="css/tooltip.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.structure.css" type="text/css" />
<link rel="stylesheet" href="js/jquery-miniNotification/jquery.miniNotification.css" type="text/css" />
<link rel="stylesheet" href="js/bootstrap/css/bootstrap.css" type="text/css">
<script type="text/javascript" src="js/jquery-1.11.1.js"></script>
<script type="text/javascript" src="js/jquery.menu.js"></script>
<script type="text/javascript" src="js/jquery.tooltip.js"></script>
<script type="text/javascript" src="js/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-miniNotification/jquery.miniNotification.js"></script>
<script type="text/javascript" src="js/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
$(document).on('click', 'div#login form button#enviar', function()
	{
	var usuario = $('#usuario').val();
	var passwd = $('#passwd').val();
	if($.trim(usuario) == '')
		{
		showMessage('div#mini-notification', '#usuario', 'error', 'Ingrese nombre de usuario');
		return false;
		}
	if($.trim(passwd) == '')
		{
		showMessage('div#mini-notification', '#passwd', 'error', 'Ingrese su contrase&ntilde;a');
		return false;
		}
	showMessage('div#mini-notification', '', 'loading', 'Enviando, Espere...');
	var parametros = {
		'usuario' : usuario,
		'passwd' : passwd,
		'accion' : 'login'
		};
	$.ajax({
		data:  parametros,
		url:   'ajax.process.login.php',
		type:  'post',					
		success:  function(response){
			//alert(response);
		var json = eval('(' + response + ')');
		//alert(json);
		//alert(json.tipo);
		/*if(json.tipo == 'ERROR')
			{
			showMessage('div#mini-notification', json.campo, 'error', json.mensaje);
			}
		*/
		if(response == 'null')
			{
			showMessage('div#mini-notification', '#passwd', 'error', 'Usuario/Contrase&ntilde;a incorrectos, Por favor reintente');
			}
		else if (json.tipo == 'LOGIN_OK')
			{
			showMessage('div#mini-notification', '', 'ok', 'Iniciando sesi&oacute;n, Espere...');
			$('div#mini-notification').css('display', 'block');
			setTimeout(function(){ $(location).attr('href', 'index.php?mod=' + json.url);}, 2000);
			}
			
			
			console.log(response);
			
		}
	});
return false;
});
</script>
</head>
<body>
<header><h1 class="float_center"><a href="./" title="BSN Medical">BSN Medical</a></h1></header>
		
		<div id="mini-notification">
			<p></p>
		</div>
		
		<div id="login">
			<form name="form" class="float_center" id="form" method="post">
				<h2 class="gris2 borde_gris text_align_center margin_bottom_20">Ingreso Usuarios</h2>
				<fieldset id="inputs">
					<input id="usuario" class="witdh_100 margin_bottom_10" name="usuario" type="text" placeholder="Usuario" autofocus />   
					<input id="passwd" class="witdh_100 margin_bottom_10" name="passwd" type="password" placeholder="Password" />
				</fieldset>
				<fieldset id="actions">
					<button name="seccion" type="submit" class="float_right" id="enviar" value="login">Ingresar</button>
				</fieldset>
				<center><br><span>Versión: <strong>1.0.001</strong></span></center>
			</form>
		</div>

	</body>
	</html>