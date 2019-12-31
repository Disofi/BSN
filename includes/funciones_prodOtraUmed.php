<?php
function listarProdOtraUnimed()
	{
	include('includes/conexion.php');
	$sql = " SELECT base.codprod as codProd, base.codumed as codUMed, ";
	$sql.= " detalle.desprod as descripcionProducto, umed.desumed as descripcionMedida ";
	$sql.= " FROM ".$bd['softland'].".[iw_tprod] detalle";
	$sql.= " INNER JOIN  ".$bd['dsparam'].".[DS_prodotraumed] base ON detalle.Codprod = base.codprod ";
	$sql.= " COLLATE Modern_Spanish_CI_AS ";
	$sql.= " INNER JOIN  ".$bd['softland'].".[iw_tumed] umed ON base.codumed = umed.codumed ";
	$sql.= " collate Modern_Spanish_CI_AS ";
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);
	if($num_rows > 0)
		$i = 1;
		{
		$salida = '
		<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th>ID</th>
			<th>Codigo</th>
			<th>Descripci&oacute;n</th>
			<th>Unidad Medida</th>
			<th class="no-sortable">Acci&oacute;n</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$salida .= '<tr>
			<td nowrap="nowrap">'.$i.'</td>
			<td id="codProd_'.$i.'">'.$row['codProd'].'</td>
			<td id="descriProd_'.$i.'">'.$row['descripcionProducto'].'
			<input type="hidden" id="hiddenCod_'.$i.'" name="hiddenCod_'.$i.'" value="'.$row['codProd'].'">
			</td> 
			<td id="codumed_'.$i.'">'.$row['descripcionMedida'].'
			<input type="hidden" id="baseMed_'.$i.'" name="baseMed_'.$i.'" value="'.$row['codUMed'].'">
			</td> 		
			<td><div class="acciones float_center">';
				/*
				$salida .= '<a href="#" class="edit icon tooltip_a" title="Editar" onclick="cargarDatos('.$i.');">Editar</a>
							<a href="#" class="delete icon tooltip_a" title="Eliminar" onclick="eliminarDatos('.$i.');">Eliminar</a>';
				*/
				$salida .= '<a href="#" class="delete icon tooltip_a" title="Eliminar" onclick="eliminarDatos('.$i.');">Eliminar</a>';
			$salida .= '</div></td>
			</tr>';
			$i++;
			}
		$salida .= '</tbody></table>';
		}
	if($num_rows == 0)
		{
		$salida = '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>';
		}
	return $salida;
	//echo $sql;
	}
	
?>