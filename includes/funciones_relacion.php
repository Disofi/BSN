<?php

/* ****************** USUARIOS ****************** */

function relacionesListar()
	{
	include('includes/conexion.php');
			
$sql =" SELECT  rela.id, rela.CodVen,vendedor.VenDes, rela.CodBode, bodega.desbode, rela.CodGrupo, grupo.desgrupo ";
$sql.=" FROM ".$bd['dsparam'].".DS_PARAMRELA rela ";
$sql.=" LEFT JOIN ".$bd['softland'].".cwtvend vendedor ON vendedor.vencod = rela.CodVen COLLATE Modern_Spanish_CI_AS ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tbode bodega ON bodega.codbode = rela.CodBode COLLATE Modern_Spanish_CI_AS ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tgrupo grupo ON grupo.codgrupo = rela.CodGrupo COLLATE Modern_Spanish_CI_AS ";	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows( $rec );
	if($num_rows > 0)
		{
		$salida = '
		<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th>Vendedor</th>
			<th>Bodega</th>
			<th>Grupo</th>
			<th class="no-sortable">&nbsp;</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$salida .= '<tr>
			<td nowrap="nowrap">'.$row['VenDes'].'</td>
			<td>'.$row['desbode'].'</td> 
			<td>'.$row['desgrupo'].'</td> 
			<td><div class="acciones float_center">';
			if($_SESSION['dsparam']['id_tipo_usuario'] == 1)
				{
					//mod=mantenedor-relacion-form&id=1
				$salida .= '<a href="index.php?mod=mantenedor-relacion-form&id='.$row['id'].'" class="edit icon tooltip_a" title="Editar: '.$row['VenDes'].'">Editar</a>
							<a href="javascript:eliminar_registro(\''.$row['id'].'\', \'relacion\', \'ajax.process.relacion.php\', \'index.php?mod=mantenedor-relacion\');" class="delete icon tooltip_a" title="Eliminar: '.$row['Nombres'].'">Eliminar</a>';
				}
			else
				{
				$salida .= '<span class="edit2 icon">&nbsp;</span><span class="delete2 icon">&nbsp;</span>';
				}
			$salida .= '</div></td>
			</tr>';
			}
		$salida .= '</tbody></table>';
		}
	if($num_rows == 0)
		{
		$salida = '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>';
		}
	return $salida;
	}
function relacionVendedor($id)
	{
	include('includes/conexion.php');
	$sql = " SELECT VenCod, VenDes FROM ".$bd['softland'].".cwtvend ";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<select name="vendedor" id="vendedor" />
	<option value="0">-Seleccionar-</option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row['VenCod'] == $id){ $selected = 'selected="selected"';}
		$salida .= '<option value="'.$row['VenCod'].'"'.$selected.'>'.$row['VenCod'].' - '.$row['VenDes'].'</option>';
		}
	$salida .= '</select>';
	return $salida;
	}
function relacionBodega($id)
	{
	include('includes/conexion.php');
	$sql = " SELECT codbode, desbode FROM ".$bd['softland'].".iw_tbode ";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<select name="bodega" id="bodega" />
	<option value="0">-Seleccionar-</option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row['codbode'] == $id){ $selected = 'selected="selected"';}
		$salida .= '<option value="'.$row['codbode'].'"'.$selected.'>'.$row['codbode'].' - '.$row['desbode'].'</option>';
		}
	$salida .= '</select>';
	return $salida;
	}	
function relacionGrupo($grupo)
	{
	include('includes/conexion.php');
	$sql = " SELECT CodGrupo, DesGrupo FROM ".$bd['softland'].".iw_tgrupo ";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<select name="grupo" id="grupo" />
	<option value="0">-Seleccionar-</option>';
	
	while($row = sqlsrv_fetch_array($rec))
		{
			$selected = '';

if( $row[0] == intval($grupo)){ $selected = 'selected="selected"';}
		
		$salida .= '<option value="'.$row['CodGrupo'].'"'.$selected.'>'.$row['CodGrupo'].' - '.$row['DesGrupo'].'</option>';
		}
	$salida .= '</select>';
	return $salida;
	}

function relacionSeleccionar($id)
	{
	include('includes/conexion.php');
	$sql = "SELECT TOP 1 [ID], [CodVen], [CodBode], [CodGrupo] FROM ".$bd['dsparam'].".[DS_PARAMRELA] WHERE ID=" . $id;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0)	{ return 'RELACION_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0) 	{ $row = sqlsrv_fetch_array($rec); return $row; }
	}	

function relacionInsertar($data)
	{
	include('includes/conexion.php');
	$data['correo'] = trim(strtolower($data['correo']));
	$data['usuario'] = trim($data['usuario']);
	$salida = null;
/*
	// Verificar si nombre de Usuario se encuentra disponible...
	
	$sqlb = "SELECT COUNT(*) AS existe_usuario FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE Usuario='".$data['usuario']."'";
	$rec = sqlsrv_query( $conn, $sqlb , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_usuario = $row['existe_usuario'];
	
*/
		
		$sql =" INSERT INTO ".$bd['dsparam'].".[DS_PARAMRELA] ([CodVen],[CodBode], [CodGrupo] ) ";
		$sql.="	VALUES ('".$data['vendedor']."', '".$data['bodega']."', '".$data['grupo']."')";
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if($rec){ $salida = 'OK';}
		if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
		
	return $salida;
	}

function relacionEditar($data, $id)
	{
	include('includes/conexion.php');
	$salida = null;
	
		$sql =" UPDATE ".$bd['dsparam'].".[DS_PARAMRELA] ";
		$sql.=" SET CodVen='".$data['vendedor']."', CodBode='".$data['bodega']."' , CodGrupo ='".$data['grupo']."' ";
		$sql.=" WHERE id=".$id;
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		// Si la contrasena fue modificada...

		if($rec){ $salida = 'OK';}
		if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
	if($salida == null)
		{
		$salida = 'ERROR_DESCONOCIDO';
		}
	return $salida;
	}	
	
function relacionEliminar($id)
	{
	include('includes/conexion.php');
	$sql = "SELECT TOP 1 [ID] FROM ".$bd['dsparam'].".[DS_PARAMRELA] WHERE ID=".$id;
	$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0){ return 'USUARIO_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0)
		{
		$queryDelete = "DELETE FROM ".$bd['dsparam'].".[DS_PARAMRELA] WHERE id=".$id;
		$rec2 = sqlsrv_query( $conn, $queryDelete, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if(!$rec2){ return 'USUARIO_ERROR';}
		if($rec2){ return 'USUARIO_ELIMINADO_OK';}
		}
	}
		
?>