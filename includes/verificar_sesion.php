<?php
if (!isset($_SESSION['dsparam']['id_usuario']) || trim($_SESSION['dsparam']['id_usuario']) == '')
	{	
	session_unset();
	session_destroy();
	header('location: login.php');
	exit(0);
	}
?>