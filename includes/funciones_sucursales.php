<?php
function sucursalesListar()
	{
	include('includes/conexion.php');
	//$sql = "SELECT CodAux, NomAux FROM ".$bd['dsparam'].".[DS_SUCURSALES] ";
	$sql =" SELECT a.CodAux, b.NoFAux AS NomAux FROM ".$bd['dsparam'].".DS_Sucursales a ";
	$sql.="	LEFT JOIN ".$bd['softland'].".cwtauxi b ON b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS ";
	$sql.=" WHERE 		b.codaux COLLATE Modern_Spanish_CI_AS  = a.codaux COLLATE Modern_Spanish_CI_AS ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows( $rec );
	if($num_rows > 0)
		{
		$salida = '
		<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th>Cod Cliente</th>
			<th>Nombre Cliente</th>
			<th class="no-sortable">&nbsp;</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$row['NomAux'] = ucwords(mb_strtolower($row['NomAux'], 'utf-8'));
			$salida .= '<tr>
			<td nowrap="nowrap">'.$row['CodAux'].'</td>
			<td>'.$row['NomAux'].'</td> 
			<td><div class="acciones float_center">';
			if($_SESSION['dsparam']['id_tipo_usuario'] == 1)
				{
				$salida .= '
				<a href="index.php?mod=mantenedor-sucursal-form&id='.$row['CodAux'].'" class="edit icon tooltip_a" title="Editar: '.$row['NomAux'].'">Editar</a>
				<a href="javascript:eliminar_registro(\''.$row['CodAux'].'\', \'sucursal\', \'ajax.process.sucursal.php\', \'index.php?mod=mantenedor-sucursal\');" class="delete icon tooltip_a" title="Eliminar: '.$row['Nombres'].'">Eliminar</a>';
				}
			else
				{
				$salida .= '<span class="delete2 icon">&nbsp;</span>';
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
	
	
function sucursalInsertar($data)
	{
	include('includes/conexion.php');
	//echo $data['codaux']." - ".$data['nomaux'];
	$sqla = "SELECT COUNT(*) AS existe_sucursal FROM ".$bd['dsparam'].".[DS_Sucursales] WHERE codaux='".$data['codaux']."'";
	//echo $sqla."<br><br>";
	$rec = sqlsrv_query( $conn, $sqla , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_sucursal = $row['existe_sucursal'];
	
	
	$sqlb = "SELECT COUNT(*) AS existe_nombre FROM ".$bd['dsparam'].".[DS_Sucursales] WHERE nomaux like '".$data['nomaux']."'";
	//echo $sqlb."<br><br>";
	$rec = sqlsrv_query( $conn, $sqlb , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_nombre = $row['existe_nombre'];
	
	$sqlc = "SELECT COUNT(*) AS existe_cc FROM ".$bd['dsparam'].".[DS_Sucursales] WHERE Codicc = '".$data['Codicc']."'";
	//echo $sqlc."<br><br>";
	$rec = sqlsrv_query( $conn, $sqlc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_cc = $row['existe_cc'];
	
	
	if($existe_sucursal > 0){ $salida = 'ERROR_EXISTE_CODIGO';}
	if($existe_nombre > 0){ $salida = 'ERROR_EXISTE_NOMBRE';}
	if($existe_cc > 0){ $salida = 'ERROR_EXISTE_CC';}
	
	//echo $existe_sucursal."  - ".$existe_nombre;
	if($existe_sucursal == 0 && $existe_nombre == 0 && $existe_cc == 0)
		{

		$sql =" INSERT INTO ".$bd['dsparam'].".[DS_Sucursales] ([CodAux],[NomAux],[Codicc]) ";
		$sql.="	VALUES ('".$data['codaux']."','".$data['nomaux']."','".$data['Codicc']."') ";			
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if($rec){ $salida = 'OK';}
		if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
		}
	if($salida == null) { $salida = 'ERROR_DESCONOCIDO'; }
	return $salida;
	}

function sucursalEditar($data, $id)
	{
	include('includes/conexion.php');
	$salida = null;
	
	$sqlc = "SELECT COUNT(*) AS existe_cc FROM ".$bd['dsparam'].".[DS_Sucursales] WHERE Codicc = '".$data['Codicc']."'";
	//echo $sqlc."<br><br>";
	$rec_cc = sqlsrv_query( $conn, $sqlc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row_cc = sqlsrv_fetch_array($rec_cc);	
	$existe_cc = $row_cc['existe_cc'];
	//echo $existe_cc;
	if($existe_cc > 0){ $salida = 'ERROR_EXISTE_CC';}
	//echo $existe_cc;
	if($existe_cc == '0' || $existe_cc == 0)
	{
		$sql =" UPDATE ".$bd['dsparam'].".[DS_Sucursales] ";
		$sql.=" SET Codicc ='".$data['Codicc']."' ";
		$sql.=" WHERE CodAux='".$id."'";
	//	echo $sql;
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			if($rec){ $salida = 'OK';}
			if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
	}
	// Si la contrasena fue modificada...


	if($salida == null)
		{
		$salida = 'ERROR_DESCONOCIDO';
		}
	return $salida;
	}

	
function sucursalEliminar($id)
	{
	include('includes/conexion.php');
	//////////////////////////////////////////////////////////////////////////////////////////
	//									IMPORTANTE!!!!!!!!									//
	//		FALTA VERIFICAR SI TIENE MOVIMIENTOS ASOCIADOS ANTES DE ELIMINAR AL USUARIO		//
	//////////////////////////////////////////////////////////////////////////////////////////
	$sql = "SELECT TOP 1 [CodAux] FROM ".$bd['dsparam'].".[DS_SUCURSALES] WHERE CodAux='".$id."' ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0){ return 'SUCURSAL_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0)
		{
		$queryDelete = "DELETE FROM ".$bd['dsparam'].".[DS_SUCURSALES] WHERE CodAux='".$id."' ";
		//echo $queryDelete;
		$rec2 = sqlsrv_query( $conn, $queryDelete, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if(!$rec2){ return 'SUCURSAL_ERROR';}
		if($rec2){ return 'SUCURSAL_ELIMINADO_OK';}
		}
	}
	
function usuariosCentroCosto($id)
	{
	include('includes/conexion.php');
	$sql = "SELECT  codicc,desccc  FROM ".$bd['softland'].".cwtccos WHERE nivelcc = (SELECT max(nivelcc) FROM ".$bd['softland'].".cwtccos) AND activo = 'S'";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<select name="centroCosto" id="centroCosto" />
	<option value="0">-Seleccionar-</option>';
	while($row = sqlsrv_fetch_array($rec))
		{
			if($row['codicc'] == $id)
			{
				$salida .= '<option value="'.$row['codicc'].'" selected >'.$row['codicc'].' - '.$row['desccc'].'</option>';
			}
			else
			{
				$salida .= '<option value="'.$row['codicc'].'">'.$row['codicc'].' - '.$row['desccc'].'</option>';
			}
		}
	$salida .= '</select>';
	return $salida;
	}		
	
function sucursalDatos($id)
	{
	include('includes/conexion.php');
	$sql = "SELECT TOP 1 * FROM ".$bd['dsparam'].".[DS_Sucursales] WHERE CodAux='".$id."'";
	//echo $sql;
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0)	{ return 'RELACION_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0) 	{ $row = sqlsrv_fetch_array($rec); return $row; }
	}	
?>