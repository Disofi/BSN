<?php

function conceptosListar()
	{
	include('includes/conexion.php');
	$sql = "SELECT id, concepto FROM ".$bd['dsparam'].".[DS_Conceptos] ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows( $rec );
	if($num_rows > 0)
		{
		$salida = '
		<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th>ID</th>
			<th>Concepto</th>
			<th class="no-sortable">Acciones</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$row['concepto'] = ucwords(mb_strtolower($row['concepto'], 'utf-8'));
			$salida .= '<tr>
			<td nowrap="nowrap">'.$row['id'].'</td>
			<td>'.$row['concepto'].'</td> 
			<td><div class="acciones float_center">';
				$salida .= '
				<a href="index.php?mod=mantenedor-conceptos-form&id='.$row['id'].'" class="edit icon tooltip_a" title="Editar: '.$row['concepto'].'">Editar</a>
				<a href="javascript:eliminar_registro(\''.$row['id'].'\', \'concepto\', \'ajax.process.conceptos.php\', \'index.php?mod=mantenedor-conceptos\');" class="delete icon tooltip_a" title="Eliminar: '.$row['Nombres'].'">Eliminar</a>';
				

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

function conceptoInsertar($data)
	{
		include('includes/conexion.php');
		//echo $data['codaux']." - ".$data['nomaux'];
		$sqla = "SELECT COUNT(*) AS existe_concepto FROM ".$bd['dsparam'].".[DS_Conceptos] WHERE concepto like '".$data['concepto']."'";
		//echo $sqla."<br><br>";
		$rec = sqlsrv_query( $conn, $sqla , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$row = sqlsrv_fetch_array($rec);	
		$existe_concepto = $row['existe_concepto'];

		if($existe_concepto > 0){ $salida = 'ERROR_EXISTE_CODIGO';}
		if($existe_concepto == 0)
			{
				$sql =" INSERT INTO ".$bd['dsparam'].".[DS_Conceptos] ([concepto]) ";
				$sql.="	VALUES ('".$data['concepto']."') ";			
				//echo $sql;
				$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				if($rec){ $salida = 'OK';}
				if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
			}
		if($salida == null) { $salida = 'ERROR_DESCONOCIDO'; }
		return $salida;
	}
	
function conceptosDatos($id)
	{
		include('includes/conexion.php');
		$sql = "SELECT TOP 1 * FROM ".$bd['dsparam'].".[DS_Conceptos] WHERE id='".$id."'";
		//echo $sql;
		$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if(sqlsrv_num_rows($rec) == 0)	{ return 'SIN_DATOS';}
		if(sqlsrv_num_rows($rec) > 0) 	{ $row = sqlsrv_fetch_array($rec); return $row; }
	}
function conceptoEditar($data, $id)
	{
		include('includes/conexion.php');
		$salida = null;
		$sqlc = "SELECT COUNT(*) AS existe_concepto FROM ".$bd['dsparam'].".[DS_Conceptos] WHERE concepto LIKE '".$data['concepto']."'";
		//echo $sqlc."<br><br>";
		$rec_cc = sqlsrv_query( $conn, $sqlc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$row_cc = sqlsrv_fetch_array($rec_cc);	
		$existe_concepto = $row_cc['existe_concepto'];
		if($existe_concepto > 0){ $salida = 'ERROR_EXISTE_CC';}
		if($existe_cc == '0' || $existe_cc == 0)
		{
			$sql =" UPDATE ".$bd['dsparam'].".[DS_Conceptos] ";
			$sql.=" SET concepto ='".$data['concepto']."' ";
			$sql.=" WHERE id='".$id."'";
			//echo $sql;
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
	
function conceptoEliminar($id)
	{
	include('includes/conexion.php');
	//////////////////////////////////////////////////////////////////////////////////////////
	//									IMPORTANTE!!!!!!!!									//
	//		FALTA VERIFICAR SI TIENE MOVIMIENTOS ASOCIADOS ANTES DE ELIMINAR AL USUARIO		//
	//////////////////////////////////////////////////////////////////////////////////////////
	$sql = "SELECT TOP 1 [id] FROM ".$bd['dsparam'].".[DS_Conceptos] WHERE id='".$id."' ";
	//echo $sql."<br><br>";
	$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0){ return 'CONCEPTO_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0)
		{
			$queryDelete = "DELETE FROM ".$bd['dsparam'].".[DS_Conceptos] WHERE id='".$id."' ";
			//echo $queryDelete;
			$rec2 = sqlsrv_query( $conn, $queryDelete, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			if(!$rec2){ return 'CONCEPTO_ERROR';}
			if($rec2){ return 'CONCEPTO_ELIMINADO_OK';}
		}
	}	
	
?>