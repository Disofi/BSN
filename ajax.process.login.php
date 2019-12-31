<?php
session_start();
include('includes/conexion.php');
include('includes/funciones.php');

/* --------------- INICIO SESION DE USUARIOS --------------- */

if($_POST['accion'] == 'login')
	{
	
	$usuario = sanitize($_POST['usuario']);
	$passwd = sanitize($_POST['passwd']);
	//$sql = " SELECT TOP 1 usr.ID, usr.Usuario, usr.NombreUsuario, tipo.tipoUsuario,usr.email, usr.tipoUsuario as Tipo, tipo.urlInicio, usr.Cliente,usr.nombreCliente, usr.CCosto FROM ".$bd['dsparam'].".[DS_Usuarios] AS usr  ";
	//$sql.= " INNER JOIN ".$bd['dsparam'].".[DS_UsuariosTipos] AS tipo ON usr.Tipousuario=tipo.ID WHERE usr.Usuario='".$usuario."' AND PWDCOMPARE('".$passwd."',usr.Contrasena) = 1 ";
	$sql = " SELECT TOP 1 usr.ID, usr.Usuario, tipo.tipoUsuario,usr.email, usr.tipoUsuario as Tipo, tipo.urlInicio, ";
	$sql.=" usr.Cliente, usr.CCosto ";
	$sql.=" FROM ".$bd['dsparam'].".[DS_Usuarios] AS usr  ";
	$sql.=" LEFT JOIN ".$bd['dsparam'].".[DS_UsuariosTipos] AS tipo ON usr.Tipousuario=tipo.ID  ";
	//$sql.=" LEFT JOIN ".$bd['softland'].".wisusuarios  userSoftland on usr.Usuario=userSoftland.Usuario collate SQL_Latin1_General_CP1_CI_AI ";
	//$sql.=" LEFT JOIN ".$bd['softland'].".cwtauxi auxiliar on usr.Cliente=auxiliar.CodAux collate SQL_Latin1_General_CP1_CI_AI ";
	$sql.=" WHERE usr.Usuario='".$usuario."' AND PWDCOMPARE('".$passwd."',usr.Contrasena) = 1 ";
	
	
	//echo $sql;
	$res = sqlsrv_query( $conn, $sql , array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if($res === false) 
		{
		$status = array('tipo' => 'ERROR', 'mensaje' => 'Usuario/Contrase&ntilde;a incorrectos, Por favor reintente... RES = '.$sel.' = ', 'campo' => '#passwd');
		}
	while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
		{
			$_SESSION['dsparam']['nombre'] = $row['Nombres'];
			$_SESSION['dsparam']['id_usuario'] = $row['ID'];
			$_SESSION['dsparam']['id_tipo_usuario'] = $row['Tipo'];
			$_SESSION['dsparam']['tipo_usuario'] = $row['tipoUsuario'];
			$_SESSION['dsparam']['cod_usuario'] = $row['CodUsuario'];
			$_SESSION['dsparam']['centroCosto']		= $row['CCosto'];
			$_SESSION['urlInicio'] = $row['urlInicio'];
			$_SESSION['dsparam']['usuario'] = $row['Usuario'];
			$_SESSION['dsparam']['nombreUsuario'] = $row['NombreUsuario'];
			$_SESSION['dsparam']['cliente']		= $row['Cliente'];
			$_SESSION['dsparam']['nombreCliente'] = $row['nombreCliente'];
			$_SESSION['dsparam']['correo'] = $row['email'];
			$status = array('tipo' => 'LOGIN_OK', 'url' => $row['urlInicio']);
		}
		

	$json_data = json_encode($status);  
	//$json_data = $sql;  
	echo $json_data;	
	}
else if($_POST['accion'] == 'login_cliente')
{
		$data = $_POST['cliente'];
		$datx = explode("[||]",$data);
		$_SESSION['cliente']['CodAux'] = $datx[0];
		$_SESSION['cliente']['NomAux'] = $datx[1];
		
		
		$sql =" SELECT TOP 1 sucursal.Codicc AS codicc , ccosto.DescCC AS desccc FROM ".$bd['dsparam'].".[DS_Sucursales] sucursal ";
		$sql.=" LEFT JOIN ".$bd['softland'].".cwtccos ccosto ON sucursal.Codicc = ccosto.CodiCC COLLATE SQL_Latin1_General_CP1_CI_AI ";
		$sql.=" WHERE sucursal.CodAux = '".$datx[0]."' ";
		$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
		{
			$_SESSION['cliente']['codicc'] = $row['codicc'];
			$_SESSION['cliente']['desccc'] = $row['desccc'];
		}	
		//$_SESSION['emp']['bd'] = $datx[0].".softland";
		if (isset($_SESSION['cliente']['CodAux']))
			{
			$status = array('tipo' => 'OK', 'url' => 'menuNotaPedido');
			}
		else
			{
			$status = array('tipo' => 'ERROR', 'mensaje' => 'No hay datos validos');
			}
	$json_data = json_encode($status);  
	//$json_data = $sql;  
	echo $json_data;				
}
?>