<?php

/* Sanitizar entradas de datos... */
function sanitize($input)
	{
	if(is_array($input))
		{
		foreach($input as $var=>$val) { $output[$var] = sanitize($val); }
		}
	else
		{
		if (get_magic_quotes_gpc()) { $input = stripslashes($input); }
		$input  = cleanString($input);
		$output = mssql_escape($input);
		}
	return $output;
	}

/* Eliminar etiquetas en cadenas de texto... */
function cleanString($input) 
	{
	$search = array(
		'@<script [^>]*?>.*?@si',
		'@< [/!]*?[^<>]*?>@si',
		'@<style [^>]*?>.*?</style>@siU',
		'@< ![sS]*?--[ tnr]*>@'
		);
	$output = preg_replace($search, '', $input);
	return $output;
	}

/* Escapar cadenas de texto para SQL Server... */
function mssql_escape($str)
	{
    if(get_magic_quotes_gpc()) { $str= stripslashes($str); }
	return str_replace("'", "''", $str);
	}

/* Truncar texto a una cadena de largo N */
function truncateText($string, $limit)
	{
	$string = strip_tags($string);	
	if(strlen($string) <= $limit) { Return $string; }
	else
		{
		$texto = split(' ',$string);
		$string = ''; $c = 0;	
		while($limit >= strlen($string) + strlen($texto[$c]))
			{
			$string .= ' '.$texto[$c];
			$c = $c + 1;
			}
		return $string.'...';
		}
	}

/* Mostrar tamaño de un archivo como una cadena de texto... */
function format_filesize($peso , $decimales = 2)
	{
	$clase = array(' Bytes', ' KB', ' MB', ' GB', ' TB'); 
	return round($peso / pow(1024,($i = floor(log($peso, 1024)))),$decimales ).$clase[$i];
	}

/* Convertir un valor numerico a una cadena con formato Precio (Ej: 99999 => $99.999) */
function formato_precio($valor)
	{
	return '$' . number_format($valor,0,'','.') . '-';
	}
	
function formatoNumero($valor)
	{
	//return number_format($valor,0,'','.');
	return number_format($valor,0,'','.') . '-';
	}
	
function formatoNum($valor)
	{
	//return number_format($valor,0,'','.');
	return number_format($valor,0,'','.');
	}	
	
/* Rellenar con X cantidad de ceros hacia la izquierda... */
function zerofill($valor, $longitud)
	{
	$res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
	return $res;
	}

/* Dar formato a una fecha para guardarla en la BD (31/12/2014 se convierte en 2014/31/12) */

/*function formatoFechaGuardar($fecha, $separador)
	{
	include ('conexion.php');
	$f = explode($separador, $fecha);
	if ($srv == 'SRVDISOFI\SQLEXPRESS')
		{
		$fecha = $f[2] . $separador . $f[0] . $separador . $f[1];	
		}
	else
		{
		$fecha = $f[2] . $separador . $f[1] . $separador . $f[0];
		}
	return $fecha;
	}
*/

function formatoFechaGuardar($fecha, $separador)
{
include ('conexion.php');
$f = explode($separador, $fecha);
if ($srv == 'SRVDISOFI\SQLEXPRESS')
{
$fecha = $f[2] . $separador . $f[1] . $separador . $f[0];
}
else
{
$fecha = $f[0] . $separador . $f[1] . $separador . $f[2];
}
return $fecha;
}

function formatoFechaGuardarRequest($fecha, $separador)
{
include ('conexion.php');
$f = explode($separador, $fecha);
if ($srv == 'SRVDISOFI\SQLEXPRESS')
{
$fecha = $f[2] . $separador . $f[1] . $separador . $f[0];
}
else
{
$fecha = $f[0] . $separador . $f[2] . $separador . $f[1];
}
return $fecha;
}


function formatoFechaGuardarPicker($fecha, $separador)
{
include ('conexion.php');
$f = explode($separador, $fecha);
	if ($srv == 'SRVDISOFI\SQLEXPRESS')
	{
	$fecha = $f[0] . $separador . $f[1] . $separador . $f[2];
	}
	else
	{
	$fecha = $f[2] . $separador . $f[1] . $separador . $f[0];
}
return $fecha;
}





/* Dar formato a una fecha obtenida desde la BD (2014/31/12 se convierte en 31/12/2014) */
function formatoFechaLeer($fecha, $separador)
	{
	$fecha = date_format($fecha, 'd' . $separador . 'm' . $separador . 'Y');
	return $fecha;
	}

function formatoFechaTexto($fecha, $separador)
	{
	$f = explode($separador, $fecha);
	$mes = $f[1];
	switch ($f[1]) 
		{
		case '01': $mes = 'Enero'; break;
		case '02': $mes = 'Febrero'; break;
		case '03': $mes = 'Marzo'; break;
		case '04': $mes = 'Abril'; break;
		case '05': $mes = 'Mayo'; break;
		case '06': $mes = 'Junio'; break;
		case '07': $mes = 'Julio'; break;
		case '08': $mes = 'Agosto'; break;
		case '09': $mes = 'Septiembre'; break;
		case '10': $mes = 'Octubre'; break;
		case '11': $mes = 'Noviembre'; break;
		case '12': $mes = 'Diciembre'; break;
		default: break;
		}
	$f[1] = $mes;
	$fecha = array('d' => $f[0], 'm' => $f[1], 'a' => $f[2]);
	return $fecha;
	}

/* Convertir un array en una cadena de texto con un separador... */
function arrayToString($array, $separador)
	{
	$string = '';
	for($i=0; $i < count($array); $i++) { $string .= $array[$i] . $separador; }
	$string = substr($string, 0 , - (strlen($separador)));
	return $string;
	}
	
function BuscaProductos($buscar)
	{
	include ('includes/conexion.php');
	
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax)
		{
		$user_error = 'Access denied - not an AJAX request...';
		trigger_error($user_error, E_USER_ERROR);
		}
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $buscar);
	$p = count($partes);
	
	$sql = "SELECT a.CodProd, a.DesProd, a.PrecioVta, a.PrecioVtaUM1, a.PrecioVtaUM2, a.CodUMed, b.DesUMed, 
			isnull(a.CodUMedVta1,'') AS cumv1, CASE WHEN a.CodUMedVta1 IS NULL THEN '' ELSE (SELECT b.DesUMed from ".$bd['softland'].".[iw_tumed] as b where CodUMed=a.CodUMedVta1) END AS dcumv1, 
			isnull(a.CodUMedVta2,'') AS cumv2, CASE WHEN a.CodUMedVta2 IS NULL THEN '' ELSE (SELECT b.DesUMed from ".$bd['softland'].".[iw_tumed] as b where CodUMed=a.CodUMedVta2) END AS dcumv2 
			FROM ".$bd['softland'].".[iw_tprod] as a
			LEFT JOIN ".$bd['softland'].".[iw_tumed] as b ON a.CodUMed=b.CodUMed WHERE ";
	for($i = 0; $i < $p; $i++)
		{
		$sql .= " (CodProd LIKE '%".strtoupper($partes[$i])."%' OR DesProd LIKE '%".strtoupper($partes[$i])."%') AND ";
		}
	$sql = substr($sql, 0 ,-4);
	$sql .= " ORDER by CodProd";
	//$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	$num = sqlsrv_num_rows($rec);
	$c = 0;
	if($num > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$a_json_row['value']	= $row['CodProd'].' - '.$row['DesProd'];
			$a_json_row['codigo']	= $row['CodProd'];
			$a_json_row['nombre']	= $row['CodProd'].' - '.$row['DesProd'];
			$a_json_row['precio1']	= $row['PrecioVta'];
			$a_json_row['precio2']	= $row['PrecioVtaUM1'];
			$a_json_row['precio3']	= $row['PrecioVtaUM2'];
			$a_json_row['codumed']	= $row['CodUMed'];
			$a_json_row['desumed']	= $row['DesUMed'];
			$a_json_row['cumv1']	= $row['cumv1'];
			$a_json_row['dcumv1']	= $row['dcumv1'];
			$a_json_row['cumv2']	= $row['cumv2'];
			$a_json_row['dcumv2']	= $row['dcumv2'];
 			array_push($a_json, $a_json_row);
			$c = $c + 1;
			}
		}
	$json = json_encode($a_json);
	print $json;
	}

function TraeUMed()
	{
	include('includes/conexion.php');
	
	$sql = "SELECT distinct CodUMed FROM ".$bd['softland'].".[iw_tprod] WHERE CodUMed is not null";
	///$res = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$tum = '<select name="umed" id="umed" tabindex="11"><option value="00"> -- </option>';
	while ($row = sqlsrv_fetch_array($res))
		{
		$tum.='<option value="'.$row['CodUMed'].'">'.$row['CodUMed'].'</option>';
		}		
	$tum.='</select>';
	echo $tum;
	}

function TraeProductos()
    {
    include('includes/conexion.php');

    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if(!$isAjax) 
        {
        $user_error = 'Access denied - not an AJAX request...';
        trigger_error($user_error, E_USER_ERROR);
        }
    $a_json = array();
    $a_json_row = array();

    $sel = "SELECT a.codprod, b.desprod, a.codumed, c.desumed, a.cantidad, a.preciovta 
            FROM ".$bd['dsparam'].".[DS_PromocionesDet] AS a 
            LEFT JOIN ".$bd['softland'].".[iw_tprod] AS b ON a.codprod=b.CodProd COLLATE Modern_Spanish_CI_AS 
            LEFT JOIN ".$bd['softland'].".[iw_tumed] AS c ON a.codumed=c.CodUMed COLLATE Modern_Spanish_CI_AS 
            WHERE codpromo='".$data['codigo']."'";
    ///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    while ($row = sqlsrv_fetch_array($res))
        {
        $a_json_row['codprod']   = $row['codprod'];
        $a_json_row['desprod']   = $row['desprod'];
        $a_json_row['codumed']   = $row['codumed'];
        $a_json_row['desumed']   = $row['desumed'];
        $a_json_row['cantidad']  = $row['cantidad'];
        $a_json_row['preciovta'] = $row['preciovta'];
        array_push($a_json, $a_json_row);
        }
    $json = json_encode($a_json);
    echo $json;
    }
	
// 1 SUCURSAL -- 1 CC
function selectCentroCosto($centroCosto)
	{
	include('includes/conexion.php');
	$sql = " SELECT codicc,desccc FROM ".$bd['softland'].".cwtccos ";
	$sql.= " WHERE nivelcc = (SELECT max(nivelcc) FROM ".$bd['softland'].".cwtccos) AND activo ='S' ";
	$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if( $res === false) 
		{
		$status = array('tipo' => 'ERROR', 'mensaje' => 'selectCentroCosto');
		die( print_r( sqlsrv_errors(), true) );		
		}
	$tum = '<select name="ccosto" id="ccosto" tabindex="11" disabled>
	<option value=""> -- Seleccione Centro Costo -- </option>';
	//while ($row = sqlsrv_fetch_array($res))
	while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
		{
			if($row['codicc'] == $centroCosto)
			{
				$tum.='<option value="'.$row['codicc'].'" selected>'.$row['codicc'].' - '.$row['desccc'].' </option>';
			}
			else
			{
				$tum.='<option value="'.$row['codicc'].'">'.$row['codicc'].' - '.$row['desccc'].' </option>';
			}
		}		
	$tum.='</select>';
	echo $tum;

	}

/*
//1 a muchos
function selectCentroCosto($centroCosto)
	{
	include('includes/conexion.php');
	//$sql = " SELECT codicc,desccc FROM ".$bd['softland'].".cwtccos ";
	//$sql.= " WHERE nivelcc = (SELECT max(nivelcc) FROM ".$bd['softland'].".cwtccos) AND activo ='S' ";
	$sql = " SELECT codicc,desccc FROM ".$bd['softland'].".cwtccos WHERE Codicc = '".$_SESSION['cliente']['codicc']."' AND activo ='S' ";
	//echo $sql;
	$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if( $res === false) 
		{
		$status = array('tipo' => 'ERROR', 'mensaje' => 'selectCentroCosto');
		die( print_r( sqlsrv_errors(), true) );		
		}
	$tum = '<select name="ccosto" id="ccosto" tabindex="11" disabled>
	<option value=""> -- Seleccione Centro Costo -- </option>';
	//while ($row = sqlsrv_fetch_array($res))
	while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
		{
			//if($row['codicc'] == $centroCosto)
			//{
				$tum.='<option value="'.$row['codicc'].'" selected>'.$row['codicc'].' - '.$row['desccc'].' </option>';
			//}
			//else
			//{
			//	$tum.='<option value="'.$row['codicc'].'">'.$row['codicc'].' - '.$row['desccc'].' </option>';
			//}
		}		
	$tum.='</select>';
	echo $tum;

	}	
*/	
	
	//RR BUSCAR PRODUCTOS EXCLUIDOS
	
//function BuscaProductosExcluidos($buscar,$codigos)
function BuscaProductosExcluidos($buscar)
	{

	include ('includes/conexion.php');
	//$codigosArray = explode(',', $codigos);
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax)
		{
		$user_error = 'Access denied - not an AJAX request...';
		trigger_error($user_error, E_USER_ERROR);
		}
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $buscar);
	$p = count($partes);
	
	$sql = "SELECT a.CodProd, a.DesProd, a.PrecioVta, a.PrecioVtaUM1, a.PrecioVtaUM2, a.CodUMed, b.DesUMed, 
			isnull(a.CodUMedVta1,'') AS cumv1, CASE WHEN a.CodUMedVta1 IS NULL THEN '' ELSE (SELECT b.DesUMed from ".$bd['softland'].".[iw_tumed] as b where CodUMed=a.CodUMedVta1) END AS dcumv1, 
			isnull(a.CodUMedVta2,'') AS cumv2, CASE WHEN a.CodUMedVta2 IS NULL THEN '' ELSE (SELECT b.DesUMed from ".$bd['softland'].".[iw_tumed] as b where CodUMed=a.CodUMedVta2) END AS dcumv2 
			FROM ".$bd['softland'].".[iw_tprod] as a
			LEFT JOIN ".$bd['softland'].".[iw_tumed] as b ON a.CodUMed=b.CodUMed WHERE ";
	for($i = 0; $i < $p; $i++)
		{
		$sql .= " (CodProd LIKE '%".strtoupper($partes[$i])."%' OR DesProd LIKE '%".strtoupper($partes[$i])."%') AND ";
		}
	$sql = substr($sql, 0 ,-4);
	
	/*if($codigos == '')
	{
		
	}
	else
	{
		for($f=0; $f < count($codigosArray); $f++)
		{
			$sql.= " AND a.CodProd NOT IN ('".$codigosArray[$f]."') ";
		}
	}
	*/
	$sql.= " AND a.CodProd NOT IN (SELECT CodProd collate Modern_Spanish_CI_AS FROM ".$bd['dsparam'].".DS_FletesUmed )   ";
	$sql.= " ORDER by CodProd";
	//$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num = sqlsrv_num_rows($rec);
	
	if($num > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$a_json_row['value']	= $row['CodProd'].' - '.$row['DesProd'];
			$a_json_row['codigo']	= $row['CodProd'];
			$a_json_row['nombre']	= $row['CodProd'].' - '.$row['DesProd'];
			$a_json_row['precio1']	= $row['PrecioVta'];
			$a_json_row['precio2']	= $row['PrecioVtaUM1'];
			$a_json_row['precio3']	= $row['PrecioVtaUM2'];
			$a_json_row['codumed']	= $row['CodUMed'];
			$a_json_row['desumed']	= $row['DesUMed'];
			$a_json_row['cumv1']	= $row['cumv1'];
			$a_json_row['dcumv1']	= $row['dcumv1'];
			$a_json_row['cumv2']	= $row['cumv2'];
			$a_json_row['dcumv2']	= $row['dcumv2'];
 			array_push($a_json, $a_json_row);
			
			}
		}
	$json = json_encode($a_json);
	print $json;
	//echo $sql;
	}
	// FIN RR PRODUCTOS EXCLUIDOS
	
	
	
function inputContacto($codauc)
{
	include ('includes/conexion.php');
	$sql = " select nomcon from ".$bd['softland'].".cwtaxco where CodAuc ='".$codauc."' ";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while($row = sqlsrv_fetch_array($rec))
	{
		echo '<input type="text" name="Contacto" id="Contacto" maxlength="30" tabindex="-1" value="'.$row['nomcon'].'"/>';
	}
}

// BUSCAR CLIENTES

function buscarClientesText($buscar)
	{
	include ('includes/conexion.php');
	//$codigosArray = explode(',', $codigos);
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax)
		{
		$user_error = 'Access denied - not an AJAX request...';
		trigger_error($user_error, E_USER_ERROR);
		}
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $buscar);
	$p = count($partes);
	
	//SELECT a.codaux ,a.nomaux, b.codlista FROM [TAVELLI1].[softland].cwtauxi a
	//LEFT JOIN [TAVELLI1].[softland].cwtcvcl b on a.codaux=b.codaux 
	//WHERE  (a.codaux LIKE '%%' OR a.nomaux LIKE '%%')  ORDER by nomaux
	
	$sql = "SELECT a.codaux ,a.nomaux, b.codlista FROM ".$bd['softland'].".cwtauxi a
			LEFT JOIN ".$bd['softland'].".cwtcvcl b on a.codaux=b.codaux
			WHERE ";		
	for($i = 0; $i < $p; $i++)
		{
		$sql .= " (a.codaux LIKE '%".strtoupper($partes[$i])."%' OR a.nomaux LIKE '%".strtoupper($partes[$i])."%') AND ";
		}
	$sql = substr($sql, 0 ,-4);
	$sql.= " ORDER by nomaux";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num = sqlsrv_num_rows($rec);
	//echo $num;
	if($num > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$a_json_row['value']	= $row['codaux'].' - '.$row['nomaux'];
			$a_json_row['codigo']	= $row['codaux'];
			$a_json_row['nombre']	= $row['nomaux'];
			$a_json_row['codlista']	= $row['codlista'];
			array_push($a_json, $a_json_row);
			}
		}
	$json = json_encode($a_json);
	print $json;
	//echo $sql;
	}

function buscarClientesSucursal($buscar)
	{

	include ('includes/conexion.php');
	//$codigosArray = explode(',', $codigos);
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax)
		{
		$user_error = 'Access denied - not an AJAX request...';
		trigger_error($user_error, E_USER_ERROR);
		}
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $buscar);
	$p = count($partes);
	
	//SELECT a.codaux ,a.nomaux, b.codlista FROM [TAVELLI1].[softland].cwtauxi a
	//LEFT JOIN [TAVELLI1].[softland].cwtcvcl b on a.codaux=b.codaux 
	//WHERE  (a.codaux LIKE '%%' OR a.nomaux LIKE '%%')  ORDER by nomaux
	
	/*
	$sql = "SELECT a.codaux ,a.nomaux, b.codlista FROM ".$bd['softland'].".cwtauxi a
			LEFT JOIN ".$bd['softland'].".cwtcvcl b on a.codaux=b.codaux
			WHERE ";		
			*/
	/*$sql = "SELECT a.CodAux, b.NoFAux AS NomAux FROM ".$bd['dsparam'].".DS_Sucursales a
			LEFT JOIN ".$bd['softland'].".cwtauxi b ON b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS 
			WHERE 		b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS AND	";*/
	$sql =" SELECT a.codaux ,a.NoFAux AS nomaux, b.codlista FROM ".$bd['softland'].".cwtauxi a ";
	$sql.=" LEFT JOIN ".$bd['softland'].".cwtcvcl b on a.codaux=b.codaux ";
	$sql.=" WHERE  ";
	
	
	for($i = 0; $i < $p; $i++)
		{
		$sql .= " (a.codaux LIKE '%".strtoupper($partes[$i])."%' OR a.nomaux LIKE '%".strtoupper($partes[$i])."%') AND ";
		}
	$sql = substr($sql, 0 ,-4);
	$sql.= " ORDER by nomaux";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num = sqlsrv_num_rows($rec);
	
	if($num > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$a_json_row['value']	= $row['codaux'].' - '.$row['nomaux'];
			$a_json_row['codigo']	= $row['codaux'];
			$a_json_row['nombre']	= $row['nomaux'];
			$a_json_row['codlista']	= $row['codlista'];
 			array_push($a_json, $a_json_row);
			
			}
		}
	$json = json_encode($a_json);
	print $json;
	//echo $sql;
	}
function SelEmpresa()
	{
	include('conexion.php');

	$sel = "SELECT a.CodAux, b.NoFAux AS NomAux FROM ".$bd['dsparam'].".DS_Sucursales a
			LEFT JOIN ".$bd['softland'].".cwtauxi b ON b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS 
			WHERE 		b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS	";
	//echo $sel;
	$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$num = sqlsrv_num_rows($res);
	$salida = '<option value="00">Seleccione Sucursal</option><option value="00"> -- </option>';
	if ($num > 0)
		{
		while ($row=sqlsrv_fetch_array($res))
			{
			$salida .= '<option value="'.$row['CodAux'].'[||]'.$row['NomAux'].'">'.$row['CodAux'].' - '.$row['NomAux'].'</option>';
			$j++;
			}
		}
	echo $salida;
	}

function nombreMes($mes)
{
	
	if($mes == '01' || $mes == '1')
	{
		$nombre = 'ENERO';
	}
	if($mes == '02' || $mes == '2')
	{
		$nombre = 'FEBRERO';
	}
	if($mes == '03' || $mes == '3')
	{
		$nombre = 'MARZO';
	}
	if($mes == '04' || $mes == '4')
	{
		$nombre = 'ABRIL';
	}
	if($mes == '05' || $mes == '5')
	{
		$nombre = 'MAYO';
	}
	if($mes == '06' || $mes == '6')
	{
		$nombre = 'JUNIO';
	}
	if($mes == '07' || $mes == '7')
	{
		$nombre = 'JULIO';
	}
	if($mes == '08' || $mes == '8')
	{
		$nombre = 'AGOSTO';
	}
	if($mes == '09' || $mes == '9')
	{
		$nombre = 'SEPTIEMBRE';
	}
	if($mes == '010' || $mes == '10')
	{
		$nombre = 'OCTUBRE';
	}
	if($mes == '011' || $mes == '11')
	{
		$nombre = 'NOVIEMBRE';
	}
	if($mes == '012' || $mes == '12')
	{
		$nombre = 'DICIEMBRE';
	}
	return $nombre;
}

function trimestreMes($mes)
{
	if($mes >= 1 && $mes <= 3)
	{
		$trimestre = "Q1";
	}
	else if($mes >= 4 && $mes <= 6)
	{
		$trimestre = "Q2";
	}
	else if($mes >= 7 && $mes <= 9)
	{
		$trimestre = "Q3";
	}
	else if($mes >= 10 && $mes <= 12)
	{
		$trimestre = "Q4";
	}	
	else
	{
		$trimestre = "ERROR";
	}
		
	return $trimestre;
	
}



	
?>