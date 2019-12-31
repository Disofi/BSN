<?php

/* ****************** USUARIOS ****************** */

function usuariosListar()
	{
	include('includes/conexion.php');
	$sql = "SELECT usr.ID, usr.Usuario, usr.email,  tipo.tipoUsuario, userSoftland.Nombre , usr.CCosto,ccosto.DescCC
			FROM ".$bd['dsparam'].".[DS_Usuarios] AS usr
			LEFT JOIN ".$bd['dsparam'].".[DS_UsuariosTipos] AS tipo ON usr.TipoUsuario=tipo.ID 
			LEFT JOIN ".$bd['softland'].".wisusuarios  userSoftland on usr.Usuario=userSoftland.Usuario collate SQL_Latin1_General_CP1_CI_AI
			LEFT JOIN ".$bd['softland'].".cwtccos ccosto on usr.CCosto = ccosto.codicc collate SQL_Latin1_General_CP1_CI_AI
			WHERE Tipo.id <> 1 ORDER BY usr.Usuario ASC ";

			//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows( $rec );
	if($num_rows > 0)
		{
		$salida = '
		<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th>Nombre</th>
			<th>Usuario</th>
			<th>Centro de Costo</th>
			<th>Correo</th>
			
			<th nowrap="nowrap">Tipo Usuario</th>
			<th class="no-sortable">&nbsp;</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$row['Nombres'] = ucwords(mb_strtolower($row['nombreUsuario'], 'utf-8'));
			$salida .= '<tr>
			<td nowrap="nowrap">'.$row['Nombre'].'</td>
			<td>'.$row['Usuario'].'</td> 
			<td>'.$row['CCosto'].' - '.$row['DescCC'].'</td> 
			<td>'.$row['email'].'</td> 
			<td nowrap="nowrap">'.$row['tipoUsuario'].'</td>
			<td><div class="acciones float_center">';
			if($_SESSION['dsparam']['id_tipo_usuario'] == 1)
				{
				$salida .= '<a href="index.php?mod=usuarios-form&id='.$row['ID'].'" class="edit icon tooltip_a" title="Editar: '.$row['Nombres'].'">Editar</a>
							<a href="javascript:eliminar_registro(\''.$row['ID'].'\', \'usuarios\', \'ajax.process.usuarios.php\', \'index.php?mod=usuarios\');" class="delete icon tooltip_a" title="Eliminar: '.$row['Nombres'].'">Eliminar</a>';
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

function usuariosSeleccionar($id)
	{
	include('includes/conexion.php');
	$sql =" SELECT TOP 1 usuario.[ID], usuario.[Usuario], usuario.[cliente], usuario.[ccosto], usuario.[email], usuario.[tipoUsuario], ";
	$sql.=" usuario.[CCosto], lista.[codLista], auxiliar.NomAux ";
	$sql.=" FROM ".$bd['dsparam'].".[DS_Usuarios] usuario ";
	$sql.=" LEFT JOIN ".$bd['softland'].".cwtcvcl lista on usuario.Cliente = lista.CodAux collate SQL_Latin1_General_CP1_CI_AI ";
	$sql.=" LEFT JOIN ".$bd['softland'].".cwtauxi auxiliar on usuario.cliente = auxiliar.CodAux collate SQL_Latin1_General_CP1_CI_AI ";
	$sql.=" WHERE usuario.ID=" . $id . " AND usuario.TipoUsuario <> 1";
	//echo $sql;
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0)	{ return 'RELACION_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0) 	{ $row = sqlsrv_fetch_array($rec); return $row; }
	}

function usuariosSelectorTipos($id_seleccionado)
	{
	include('includes/conexion.php');
	$sql = "SELECT [ID], [tipoUsuario] FROM ".$bd['dsparam'].".[DS_UsuariosTipos] WHERE ID > 1 ORDER BY ID ASC";
	//$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	//$salida = '<select name="tipo" id="tipo" onchange="cargar_nombres(this.value, '.$id_seleccionado.');" />
	$salida = '<select name="tipo" id="tipo" />
	<option value="0">-Seleccionar-</option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row['ID'] == $id_seleccionado){ $selected = ' selected="selected"';}
		$salida .= '<option value="'.$row['ID'].'"'.$selected.'>'.$row['tipoUsuario'].'</option>';
		}
	$salida .= '</select>';
	return $salida;
	}

function usuariosSelectorNombres($tipo_usuario, $id_seleccionado)
	{
	include('includes/conexion.php');
	// Cargar listado vendedores (Tabla Softland 'cwtvend')...
	if($tipo_usuario == 2)
		{
		$sql = "SELECT [VenCod],[VenDes] FROM ".$bd['softland'].".[cwtvend] ORDER BY VenDes ASC";
		}
	// Cargar listado usuarios (Tabla Softland 'wisusuarios')...
	if($tipo_usuario == 3)
		{
		$sql = "SELECT [Usuario],[Nombre] FROM ".$bd['softland'].".[wisusuarios] WHERE Usuario != 'softland' ORDER BY Nombre ASC";
		}
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<option value="">-Seleccionar-</option>
	<option value=""></option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row[0] == $id_seleccionado){ $selected = ' selected="selected"';}
		$salida .= '<option value="'.$row[0].'"'.$selected.'>'.ucwords(mb_strtolower($row[1], 'utf-8')).'</option>';
		}
	return $salida;
	}

function usuariosInsertar($data)
	{
	include('includes/conexion.php');
	$data['correo'] = trim(strtolower($data['correo']));
	$data['usuario'] = trim($data['usuario']);
	$salida = null;

	// Verificar si nombre de Usuario se encuentra disponible...
	
	$sqlb = "SELECT COUNT(*) AS existe_usuario FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE Usuario='".$data['usuario']."'";
	//echo $sqlb."<br><br>";
	$rec = sqlsrv_query( $conn, $sqlb , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_usuario = $row['existe_usuario'];
	
	
	// Verificar si Correo electronico se encuentra disponible...
	
	$sqlc = "SELECT COUNT(*) AS existe_correo FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE email='".$data['correo']."'";
	//echo $sqlc."<br><br>";
	$rec = sqlsrv_query( $conn, $sqlc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);
	$existe_correo = $row['existe_correo'];
	
	$sqld = "SELECT COUNT(*) AS existe_cc FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE CCosto='".$data['centroCosto']."'";
	//echo $sqld."<br><br>";
	$rec = sqlsrv_query( $conn, $sqld , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);
	$existe_cc = $row['existe_cc'];
	
	$sqle = "SELECT COUNT(*) AS existe_sucursal FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE cliente='".$data['cliente']."'";
	//recho $sqle."<br><br>";
	$rec = sqlsrv_query( $conn, $sqle , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);
	$existe_sucursal = $row['existe_sucursal'];
	
	if($existe_usuario > 0){ $salida = 'ERROR_EXISTE_USUARIO';}
	if($existe_correo > 0){ $salida = 'ERROR_EXISTE_CORREO';}
	if($existe_cc > 0){ $salida = 'ERROR_EXISTE_CC';}
	if($existe_sucursal > 0){ $salida = 'ERROR_EXISTE_SUCURSAL';}
	//echo $existe_usuario.$existe_correo.$existe_cc.$existe_sucursal."<br>";
	if($existe_usuario == 0 && $existe_correo == 0 && $existe_cc == 0 && $existe_sucursal == 0)
		{
		//$resultado = str_replace("(", "A", $data['nombreCliente']);
		
		//$var= $data['nombreCliente'];
		//$var2 = preg_replace("/()/","",$var);
		//echo $var; 
		$sql =" INSERT INTO ".$bd['dsparam'].".[DS_Usuarios] ([Usuario],[Contrasena], [Cliente], [CCosto], [email], [tipoUsuario] ) ";
		$sql.="	VALUES ('".$data['usuario']."', PWDENCRYPT('".$data['contrasena']."'), '".$data['cliente']."', '".$data['centroCosto']."', '".$data['correo']."', ";
		//$sql.=" '".$data['tipoUsuario']."','".$data['nombreUsuario']."' ,'".$data['nombreCliente']."')";
		//$sql.=" '".$data['tipoUsuario']."','".$data['nombreUsuario']."' ,'".$var."')";
		$sql.=" '".$data['tipoUsuario']."')";
		
		//echo $sql;
				
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if($rec){ $salida = 'OK';}
		if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
		}
	if($salida == null) { $salida = 'ERROR_DESCONOCIDO'; }
	return $salida;
	}

function usuariosEditar($data, $id)
	{
	include('includes/conexion.php');
	$data['correo'] = trim(strtolower($data['correo']));
	$data['usuario'] = trim($data['usuario']);
	$salida = null;
	$sqla = "SELECT COUNT(*) AS existe_usuario FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE Usuario='".$data['usuario']."' AND id <> ".$id;
	$sqlb = "SELECT COUNT(*) AS existe_correo FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE email='".$data['correo']."' AND id <> ".$id;
	$sqlc = "SELECT COUNT(*) AS existe_cc FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE CCosto='".$data['centroCosto']."' AND id <> ".$id;
	$sqld = "SELECT COUNT(*) AS existe_cliente FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE Cliente='".$data['cliente']."' AND id <> ".$id;
	//echo $sqla."<br>";
	//echo $sqlb."<br>";
	//echo $sqlc."<br>";
	// Verificar si nombre de Usuario se encuentra disponible...
	///$rec = sqlsrv_query($conn, );
	$rec = sqlsrv_query( $conn, $sqla, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);
	$existe_usuario = $row['existe_usuario'];
	
	
	// Verificar si Correo electronico se encuentra disponible...
	///$rec = sqlsrv_query($conn, );
	$rec = sqlsrv_query( $conn, $sqlb, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_correo = $row['existe_correo'];
	
	$rec = sqlsrv_query( $conn, $sqlc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_cc = $row['existe_cc'];

	$rec = sqlsrv_query( $conn, $sqld, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);	
	$existe_cliente = $row['existe_cliente'];
	
	if($existe_usuario > 0){ $salida = 'ERROR_EXISTE_USUARIO';}
	if($existe_correo > 0){ $salida = 'ERROR_EXISTE_CORREO';}
	if($existe_cc > 0){ $salida = 'ERROR_EXISTE_CC';}
	if($existe_cliente > 0){ $salida = 'ERROR_EXISTE_SUCURSAL';}
	if($existe_usuario == 0 && $existe_correo == 0 && $existe_cc == 0 && $existe_cliente == 0)
		{
		$sql =" UPDATE ".$bd['dsparam'].".[DS_Usuarios] ";
		$sql.=" SET cliente='".$data['cliente']."',  CCosto ='".$data['centroCosto']."', email='".$data['correo']."' ";
		$sql.=" WHERE id=".$id;
		//echo $sql;
		$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		// Si la contrasena fue modificada...
		if($data['contrasena'])
			{
		
			$rec2 = sqlsrv_query( $conn, "UPDATE ".$bd['dsparam'].".[DS_Usuarios] SET Contrasena=PWDENCRYPT('".$data['contrasena']."') WHERE id=".$id, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			}
		if($rec){ $salida = 'OK';}
		if(!$rec){ $salida = 'ERROR_DESCONOCIDO';}
		}
	if($salida == null)
		{
		$salida = 'ERROR_DESCONOCIDO';
		}
	return $salida;
	//echo "asdasdasda";
	}

function usuariosEliminar($id)
	{
	include('includes/conexion.php');
	//////////////////////////////////////////////////////////////////////////////////////////
	//									IMPORTANTE!!!!!!!!									//
	//		FALTA VERIFICAR SI TIENE MOVIMIENTOS ASOCIADOS ANTES DE ELIMINAR AL USUARIO		//
	//////////////////////////////////////////////////////////////////////////////////////////
	$sql = "SELECT TOP 1 [ID] FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE ID=" . $id . " AND tipoUsuario <> 1";
	$rec = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if(sqlsrv_num_rows($rec) == 0){ return 'USUARIO_NO_EXISTE';}
	if(sqlsrv_num_rows($rec) > 0)
		{
		$queryDelete = "DELETE FROM ".$bd['dsparam'].".[DS_Usuarios] WHERE id=".$id;
		$rec2 = sqlsrv_query( $conn, $queryDelete, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if(!$rec2){ return 'USUARIO_ERROR';}
		if($rec2){ return 'USUARIO_ELIMINADO_OK';}
		}
	}
function usuariosSelectNombresSoftland($id,$accion)
	{
	include('includes/conexion.php');
	
	$sql = " select * from ".$bd['softland'].".wisusuarios order by Nombre";
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if($accion == 'edit'){ $disabled = 'disabled';}
	$salida = '<select name="nombres" id="nombres" onchange="completarUsuario(this.value); " '.$disabled.'/>
	<option value="0">-Seleccionar-</option>';
	while($row = sqlsrv_fetch_array($rec))
		{
			if($row['Usuario'] == $id)
			{
				$salida .= '<option value="'.$row['Usuario'].'" selected>'.$row['Nombre'].'</option>';
			}
			else
			{
				$salida .= '<option value="'.$row['Usuario'].'">'.$row['Nombre'].'</option>';
			}
		}
	$salida .= '</select>';
	return $salida;
	}
	
function usuariosCentroCosto($id)
	{
	include('includes/conexion.php');
	$sql = "SELECT  codicc,desccc  FROM ".$bd['softland'].".cwtccos WHERE nivelcc = (SELECT max(nivelcc) FROM ".$bd['softland'].".cwtccos) ";
	//$rec = sqlsrv_query($conn, $sql);
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
	
	

?>