<?php

function promocioneslistar()
	{
	include('includes/conexion.php');
	
	$sel = "select DS.codpromo, DS.despromo, Ds.cancod, DS.fechaini, DS.fechafin, DSD.codprod, IWP.desprod, DSD.codumed, DSD.cantidad, 
			ROW_NUMBER() OVER(PARTITION BY DS.codpromo ORDER BY DS.codpromo ASC) AS Row
            from ".$bd['dsparam'].".[DS_Promociones] DS
			left join ".$bd['dsparam'].".[DS_PromocionesDet] DSD on DS.codpromo = DSD.codpromo 
			left join ".$bd['softland'].".iw_tprod IWP on IWP.codprod COLLATE Modern_Spanish_CI_AS = DSD.codprod COLLATE Modern_Spanish_CI_AS
			order by ds.codpromo asc";
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num = sqlsrv_num_rows($res);
    
    if ($num > 0)
        {
        $campos='
        <table class="registros table table-hover" id="dataTable"><thead>
        <tr>
            <th>Cod.Promoci&oacute;n</th>
            <th>Nombre</th>
            <th>Canal de Venta</th>
            <th>Fecha Vigencia Desde</th>
            <th>Fecha Vigencia Hasta</th>
            <th>Producto</th>
            <th>Descripci&oacute;n</th>
            <th>U.M.</th>
            <th>Cantidad</th>
            <th class="no-sortable">&nbsp;</th>
        </tr>
        </thead><tbody>';

        while ($row = sqlsrv_fetch_array($res))
            {
            $row['fechaini'] = date_format($row['fechaini'],'d/m/Y');
            $row['fechafin'] = date_format($row['fechafin'],'d/m/Y');
            $row['codpromo'] = ucwords(mb_strtolower($row['codpromo'], 'utf-8'));
          
            $campos .= '
            <tr>
                <td nowrap="nowrap">'.$row['codpromo'].'</td>
                <td>'.$row['despromo'].'</td> 
                <td>'.$row['cancod'].'</td> 
                <td>'.$row['fechaini'].'</td> 
                <td>'.$row['fechafin'].'</td> 
                <td>'.$row['codprod'].'</td> 
                <td>'.$row['desprod'].'</td> 
                <td>'.$row['codumed'].'</td> 
                <td>'.$row['cantidad'].'</td>'; 
			if ($row['Row']=='1')
				{
				$campos .= '	
                <td>
                	<a href="javascript:eliminar_parametro(\''.$row['codpromo'].'\',\'promociones\',\'ajax.process.mantenedores.php\',\'index.php?mod=mantenedor-promociones\');" class="delete icon">Eliminar</a>
                	<a href="index.php?mod=mantenedor-promociones-form&id='.$row['codpromo'].'" class="edit icon">Modificar</a>
                </td>';
				}
			else 
				{
				$campos .= '<td>&nbsp;</td>';
				}
            $campos .= '</tr>';
            }
        $campos .= '</tbody></table>';
        }
    else
		{
		$campos = '
		<div class="row col-md-12 table-responsive">
			<table class="registros table table-hover" id="DynamicRowsTable">
				<thead>
					<tr id="table_head">
					<th>Cod.Promoci&oacute;n</th>
					<th>Nombre</th>
					<th>Canal de Venta</th>
					<th>Fecha Vigencia Desde</th>
					<th>Fecha Vigencia Hasta</th>
					<th>Producto</th>
					<th>Descripci&oacute;n</th>
					<th>U.M.</th>
					<th>Cantidad</th>
					</tr>
				</thead>
				<tbody>
					<tr class="fila_inicio">
						<td colspan="8" class="padding_top_20 padding_left_20">
							<div class="message_info"><p class="padding_left_100">A&uacute;n no se han seleccionado productos para esta nota de pedido</p></div>
						</td>
					</tr>
				</tbody>
			</table>
			<input name="numRows" type="hidden" id="numRows" value="0" />
		</div>';
		}
    print $campos;
	}

function ListarProductos()
    {
    include('includes/conexion.php');
    
    $sel = "select DS.codpromo, DS.despromo, Ds.cancod, DS.fechaini, DS.fechafin, DSD.codprod, IWP.desprod, DSD.codumed, DSD.cantidad 
            from ".$bd['dsparam'].".[DS_Promociones] DS
            left join ".$bd['dsparam'].".[DS_PromocionesDet] DSD on DS.codpromo = DSD.codpromo 
            left join ".$bd['softland'].".iw_tprod IWP on IWP.codprod COLLATE Modern_Spanish_CI_AS = DSD.codprod COLLATE Modern_Spanish_CI_AS
            order by ds.codpromo asc";
    ///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    $num = sqlsrv_num_rows($res);
    
    if ($num > 0)
        {
        $campos='
        <table class="registros table table-hover" id="dataTable"><thead>
        <tr>
            <th>Cod.Promoci&oacute;n</th>
            <th>Nombre</th>
            <th>Canal de Venta</th>
            <th>Fecha Vigencia Desde</th>
            <th>Fecha Vigencia Hasta</th>
            <th>Producto</th>
            <th>Descripci&oacute;n</th>
            <th>U.M.</th>
            <th>Cantidad</th>
            <th class="no-sortable">&nbsp;</th>
        </tr>
        </thead><tbody>';

        while ($row = sqlsrv_fetch_array($res))
            {
            $row['fechaini'] = date_format($row['fechaini'],'d/m/Y');
            $row['fechafin'] = date_format($row['fechafin'],'d/m/Y');
            $row['codpromo'] = ucwords(mb_strtolower($row['codpromo'], 'utf-8'));
          
            $campos .= '
            <tr>
                <td nowrap="nowrap">'.$row['codpromo'].'</td>
                <td>'.$row['despromo'].'</td> 
                <td>'.$row['cancod'].'</td> 
                <td>'.$row['fechaini'].'</td> 
                <td>'.$row['fechafin'].'</td> 
                <td>'.$row['codprod'].'</td> 
                <td>'.$row['desprod'].'</td> 
                <td>'.$row['codumed'].'</td> 
                <td>'.$row['cantidad'].'</td> 
                <td></td>
            </tr>';
            }
        $campos .= '</tbody></table>';
        }
    else
        {
        $campos = '
        <div class="row col-md-12 table-responsive">
            <table class="registros table table-hover" id="DynamicRowsTable">
                <thead>
                    <tr id="table_head">
                    <th>Cod.Promoci&oacute;n</th>
                    <th>Nombre</th>
                    <th>Canal de Venta</th>
                    <th>Fecha Vigencia Desde</th>
                    <th>Fecha Vigencia Hasta</th>
                    <th>Producto</th>
                    <th>Descripci&oacute;n</th>
                    <th>U.M.</th>
                    <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="fila_inicio">
                        <td colspan="8" class="padding_top_20 padding_left_20">
                            <div class="message_info"><p class="padding_left_100">A&uacute;n no se han seleccionado productos para esta nota de pedido</p></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input name="numRows" type="hidden" id="numRows" value="0" />
        </div>';
        }
    print $campos;
    }

function selectorcanales($cancod,$candes)
	{
	include('includes/conexion.php');
	$sql = "SELECT [cancod], [candes] FROM ".$bd['softland'].".[cwtcana]  ORDER BY cancod ASC";
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if ($cancod=='')
		{
		$salida = '<select name="canven" id="canven" tabindex="3">
		<option value="00">Seleccionar Canal</option>
		<option value="00"></option>';
		}
	else
		{
		$salida = '<select name="canven" id="canven" tabindex="3">
		<option value="'.$cancod.'">'.$candes.'</option>
		<option value="00"></option>';
		}
	while($row = sqlsrv_fetch_array($rec))
		{
		$salida .= '<option value="'.$row['cancod'].'"'.$selected.'>'.$row['candes'].'</option>';
		}
	$salida .= '</select>';
	return $salida;
	}
	
function selectorcanalesNombres($id_seleccionado)
	{
	include('includes/conexion.php');
		{
		$sql = "SELECT [CanCod],[CanDes] FROM ".$bd['softland'].".[cwtcana] ORDER BY VenDes ASC";
		}
		alert($id_seleccionado);
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = '<option value="">-Seleccionar-</option>
	<option value=""></option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row[0] == $id_seleccionado){ $selected = ' selected="selected"';}
		$salida .= '<option value="'.$row[0].'"'.$selected.'>'.ucwords(mb_strtolower($row[0], 'utf-8')).'</option>';
		}
	return $salida;
	}

function AgregaPromo($data)
	{
	include('includes/conexion.php');

	$sel1 = "SELECT COUNT(*) AS existen FROM ".$bd['dsparam'].".[DS_Promociones] where codpromo='".$data['codpromo']."'";
	///$res1 = sqlsrv_query($conn, $sel1, array(), array('Scrollable' => 'buffered'));
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row1 = sqlsrv_fetch_array($res1);
	$exis = $row1['existen'];
	//echo $sel1;
	if($exis > 0)
		{
        $retorno = 'ERROR_EXISTE_CODIGO';
		}
    else
        {  
        $ins2 = "INSERT INTO ".$bd['dsparam'].".[DS_Promociones] (codpromo, despromo, cancod, fechaini, fechafin) VALUES 
                ('".$data['codpromo']."','".$data['despromo']."','".$data['canven']."','".$data['fechaini']."','".$data['fechafin']."')";
				//echo $ins2;
		$res2 = sqlsrv_query( $conn, $ins2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if ($res2)
            {
            for($c=0; $c < count($data['prod_codigo']); $c++)
                {
                $fila = $c + 1;
                
                $ins3 = "INSERT INTO ".$bd['dsparam'].".[DS_PromocionesDet] (codpromo, codprod, codumed, cantidad, preciovta) VALUES 
                        ('".$data['codpromo']."','".$data['prod_codigo'][$c]."','".$data['prod_unimed'][$c]."','".$data['prod_cantid'][$c]."','".$data['prod_precio'][$c]."')";
                ///$res3 = sqlsrv_query($conn, $ins3);
				//echo $ins3;
				$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                if ($res3) 
                    {
                    $retorno = 'OK';
                    }
                else 
                    {
                    $retorno = 'ERROR_INGRESO_DETALLE'; 
                    }
                }
            }
        else
            {
            $retorno = 'ERROR_INGRESO_PROMOCION';
            }
        }
	return $retorno;
	}	

function EditarPromo($data)
	{
	include('includes/conexion.php');

    $upd = "UPDATE ".$bd['dsparam'].".[DS_Promociones] SET despromo='".$data['despromo']."', cancod='".$data['canven']."', 
			fechaini='".$data['fechaini']."', fechafin='".$data['fechafin']."' WHERE codpromo='".$data['codpromo']."'";
	///$res = sqlsrv_query($conn, $upd);
	$res = sqlsrv_query( $conn, $upd , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
	if ($res)
		{
		$del2 = "DELETE ".$bd['dsparam'].".[DS_PromocionesDet] WHERE codpromo='".$data['codpromo']."'";
		///$res2 = sqlsrv_query($conn, $del2);
		$res2 = sqlsrv_query( $conn, $del2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		for($c=0; $c < count($data['prod_codigo']); $c++)
			{
			$fila = $c + 1;
			$ins3 = "INSERT INTO ".$bd['dsparam'].".[DS_PromocionesDet] (codpromo, codprod, codumed, cantidad, preciovta) VALUES 
					('".$data['codpromo']."','".$data['prod_codigo'][$c]."','".$data['prod_unimed'][$c]."','".$data['prod_cantid'][$c]."','".$data['prod_precio'][$c]."')";
			///$res3 = sqlsrv_query($conn, $ins3);
			$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			if ($res3) 
				{
				$retorno = 'OK';
				}
			else 
				{
				$retorno = 'ERROR_INGRESO_DETALLE'; 
				}
            }
		}
	else
		{
		$retorno = 'ERROR_INGRESO_PROMOCION';
        }
	return $retorno;
	}

function EliminarPromo($data)
	{
	include('includes/conexion.php');

	$del = "DELETE ".$bd['dsparam'].".[DS_Promociones] where codpromo='".$data['id']."'";
	///$res = sqlsrv_query($conn, $del);
	$res = sqlsrv_query( $conn, $del , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if($res)
		{
		$del2 = "DELETE ".$bd['dsparam'].".[DS_PromocionesDet] where codpromo='".$data['id']."'";
		///$res2 = sqlsrv_query($conn, $del2);
		$res2 = sqlsrv_query( $conn, $del2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
        $retorno = 'OK';
		}
    else
        {
        $retorno = 'ERROR';
        }
	return $retorno;
	}	
	
function TraerPromocion($id)
	{
	include('includes/conexion.php');
	
	$sel = "SELECT a.codpromo, a.despromo, a.cancod, b.candes, a.fechaini, a.fechafin 
			FROM ".$bd['dsparam'].".DS_Promociones as a
			LEFT JOIN ".$bd['softland'].".cwtcana as b ON a.cancod=b.cancod collate Modern_Spanish_CI_AS
			WHERE a.codpromo='".$id."'";
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if ($res)
		{
		while ($row=sqlsrv_fetch_array($res))
			{
			$data['codpromo'] = $row['codpromo'];
			$data['despromo'] = $row['despromo'];
			$data['cancod']   = $row['cancod'];
			$data['candes']   = $row['candes'];
			$data['fechaini'] = $row['fechaini'];
			$data['fechafin'] = $row['fechafin'];
			}
		}
	else 
		{
		$data = 'USUARIO_NO_EXISTEAAA';
		}
	return $data;
	}
	
function TraePromoDet($id)
	{
	include('includes/conexion.php');
	
	$sel = "SELECT a.codprod, c.desprod, a.codumed, b.desumed, a.cantidad, a.preciovta 
			FROM ".$bd['dsparam'].".DS_PromocionesDet as a
			LEFT JOIN ".$bd['softland'].".iw_tumed as b ON a.codumed=b.CodUMed collate Modern_Spanish_CI_AS 
			LEFT JOIN ".$bd['softland'].".iw_tprod as c ON a.codprod=c.codprod collate Modern_Spanish_CI_AS
			WHERE codpromo='".$id."'";
			
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if ($res)
		{
		$i=0;
		$j=1;
		while ($row=sqlsrv_fetch_array($res))
			{
			$data.='
			<tr id="fila_'.$i.'">
				<td class="numItem">'.$j.'</td>
				<td><input name="prod_codigo[]"	type="text" size="15" class="AC_Codigo" readonly="readonly" value="'.$row['codprod'].'" /></td>
				<td><input name="prod_descri[]" type="text" size="50" class="AC_Descri" readonly="readonly" value="'.$row['desprod'].'" /></td>
				<td><input name="prod_cantid[]" type="text" size="8" class="AC_Cantid" value="'.$row['cantidad'].'" style="text-align:right;" readonly="readonly" /></td>			
				<td><input name="prod_desume[]"	type="text" size="8" class="AC_Desume" value="'.$row['desumed'].'" readonly="readonly" />
					<input name="prod_unimed[]"	type="hidden" size="8" class="AC_UniMed" value="'.$row['codumed'].'" style="text-align:right;" readonly="readonly" /></td>
				<td><input name="prod_precio[]"	type="text" size="8" class="AC_Precio" value="'.$row['preciovta'].'" style="text-align:right;" readonly="readonly" /></td>				
				<td><div class="acciones float_right"><a href="javascript:deleteDynamicRow(\'#fila_'.$i.'\');" class="delete icon">Eliminar</a></div></td>
				</tr>';
			$i++;
			$j++;
			}
		}
	else 
		{
		$data = 'USUARIO_NO_EXISTE';
		}
	echo $data;
	//echo $sel;
	}	
	
function agregaUniFlete($data)
	{
	include('includes/conexion.php');
	//echo count($data['prod_codigo']);
	///$query_existeCodigo = sqlsrv_query($conn, "	SELECT COUNT(*) AS existe_codigo FROM ".$bd['dsparam'].".[DS_FletesUmed] WHERE CodProd='".$data['prod_codigo'][0]."'");
	$query_existeCodigo = sqlsrv_query( $conn, "SELECT COUNT(*) AS existe_codigo FROM ".$bd['dsparam'].".[DS_FletesUmed] WHERE CodProd='".$data['prod_codigo'][0]."'" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	//echo "SELECT COUNT(*) AS existe_codigo FROM ".$bd['dsparam'].".[DS_FletesUmed] WHERE CodProd='".$data['prod_codigo'][0]."'";
	
		$row = sqlsrv_fetch_array($query_existeCodigo);	
			$exis = $row['existe_codigo'];
	if($exis > 0)
		{
        $retorno = 'ERROR_EXISTE_CODIGO';
		}
    else
        {
        $res2 = 'OK';
        if ($res2 == 'OK')
            {
            for($c=0; $c < count($data['prod_codigo']); $c++)
                {
				if(strlen($data['prod_codigo'][$c]) == 0 ){ $c ++;}
				else
				{
                $fila = $c + 1;
					$original = $data['prod_flete'][$c];
					$buscar= array("$", ".");
					$poner= array("", "");
					$nueva= str_replace($buscar, $poner, $original);
                $ins3 = " INSERT INTO ".$bd['dsparam'].".[DS_FletesUmed] (Codprod,Codumed, valorFlete)  VALUES "; 
                $ins3.= " ('".$data['prod_codigo'][$c]."', '".$data['prod_unimed'][$c]."', ";
				$ins3.= " '".$nueva."') ";
				
				///$res3 = sqlsrv_query($conn, $ins3);
				$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                if ($res3) 
                    {
                    $retorno = 'OK';
                    }
                else 
                    {
                    $retorno = 'ERROR_INGRESO_DETALLE'; 
                    }
				}
                }
            }
        else
            {
            $retorno = 'ERROR_INGRESO_PROMOCION';
            }
        }
	return $retorno;
	}

/* ***** CORRESPONDIENTE A RANGOS DESCUENTO ***** */
function listarRangoDescto()
	{
	include('includes/conexion.php');
	
	$sql = " SELECT CodDescto ,NomDescto FROM ".$bd['dsparam'].".[DS_DXVolumenE] "; 
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
			<th>Codigo Descuento</th>
			<th>Descripci&oacute;n</th>
			<th class="no-sortable">Acci&oacute;n</th>
		</tr>
		</thead><tbody>';
		while($row = sqlsrv_fetch_array($rec))
			{
			$salida .= '<tr>
			<td nowrap="nowrap">'.$i.'</td>
			<td id="CodDescto_'.$i.'">'.$row['CodDescto'].'</td>
			<td id="NomDescto_'.$i.'">'.$row['NomDescto'].'</td> 
			<td><div class="acciones float_center">';
				$salida .= '<a href="index.php?mod=mantenedor-rangodscto-form&id='.$row['CodDescto'].'" class="edit icon" title="Editar" onclick="cargarDatos('.$i.');">Editar</a>
							<a href="javascript:eliminar_rango(\''.$row['CodDescto'].'\',\'rangos\',\'ajax.process.mantenedores.php\',\'index.php?mod=mantenedor-rangodscto\');" class="delete icon" title="Eliminar" />Eliminar</a>';
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
	}

function AgregaRango($data)
	{
	include('includes/conexion.php');

	$sel = "SELECT CodDescto FROM ".$bd['dsparam'].".[DS_DXVolumenE] where CodDescto='".$data['codigo']."'";
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num = sqlsrv_num_rows($res);
	
	if($num == 0)
		{
		$ins2 = "INSERT INTO ".$bd['dsparam'].".[DS_DXVolumenE] (CodDescto,NomDescto) VALUES ('".$data['codigo']."','".$data['descri']."')";
		$res2 = sqlsrv_query( $conn, $ins2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if ($res2)
			{
			for($c=0; $c < count($data['desde']); $c++)
				{
				$fila = $c + 1;
				$ins3 = "INSERT INTO ".$bd['dsparam'].".[DS_DXVolumenD] (CodDescto, IDCorr, Desde, Hasta, Descto) VALUES 
                        ('".$data['codigo']."','".$fila."','".$data['desde'][$c]."','".$data['hasta'][$c]."','".$data['descu'][$c]."')";
                ///$res3 = sqlsrv_query($conn, $ins3);
				$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				if ($res3) 
					{
					$retorno = 'OK';
					}
				else 
					{
					$retorno = 'ERROR_INGRESO_DETALLE'; 
					}
				}
				
				
			for($z=0; $z < count($data['marca']); $z++)
			{
				$ins4 = " INSERT INTO ".$bd['dsparam'].".[DS_DXVolumenRela] (CodDescto, CodLista)  ";
				$ins4.= " VALUES ('".$data['codigo']."','".$data['marca'][$z]."') ";
				//echo $ins4;
                ///$res4 = sqlsrv_query($conn, $ins4);
				$res4 = sqlsrv_query( $conn, $ins4 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				if ($res4) 
					{
					$retorno = 'OK';
					}
				else 
					{
					$retorno = 'ERROR_INGRESO_RELACION'; 
					}
			}	
				
				
			}
		else
            {
            $retorno = 'ERROR_INGRESO_RANGOS';
			$del4 = "DELETE ".$bd['dsparam'].".[DS_DXVolumenE] WHERE CodDescto='".$data['codigo']."'";
			///$res4 = sqlsrv_query($conn, $del4);
			$res4 = sqlsrv_query( $conn, $del4 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
            }
        }
	else 
		{
		$retorno = 'ERROR_EXISTE_RANGO';		
		}
	return $retorno;
	}	

function EditarRango($data)
	{
	include('includes/conexion.php');

    $upd = "UPDATE ".$bd['dsparam'].".[DS_DXVolumenE] SET NomDescto='".$data['descri']."' WHERE CodDescto='".$data['codigo']."'";
	///$res = sqlsrv_query($conn, $upd);
	$res = sqlsrv_query( $conn, $upd , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
	if ($res)
		{
		$del2 = "DELETE ".$bd['dsparam'].".[DS_DXVolumenD] WHERE CodDescto='".$data['codigo']."'";
		///$res2 = sqlsrv_query($conn, $del2);
		$res2 = sqlsrv_query( $conn, $del2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		for($c=0; $c < count($data['desde']); $c++)
			{
			$fila = $c + 1;
			$ins3 = "INSERT INTO ".$bd['dsparam'].".[DS_DXVolumenD] (CodDescto, IDCorr, Desde, Hasta, Descto) VALUES 
					('".$data['codigo']."','".$fila."','".$data['desde'][$c]."','".$data['hasta'][$c]."','".$data['descu'][$c]."')";
			///$res3 = sqlsrv_query($conn, $ins3);
			$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			if ($res3) 
				{
				$retorno = 'OK';
				}
			else 
				{
				$retorno = 'ERROR_INGRESO_DETALLE'; 
				}
            }
		
			for($z=0; $z < count($data['marca']); $z++)
			{
				if($z ==0)
				{
				$delete=" DELETE ".$bd['dsparam'].".[DS_DXVolumenRela] WHERE CodDescto = '".$data['codigo']."' ";
				//echo $delete;
				///$resultadoDelete = sqlsrv_query($conn, $delete);
				$resultadoDelete = sqlsrv_query( $conn, $delete , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				}
				$ins4 = " INSERT INTO ".$bd['dsparam'].".[DS_DXVolumenRela] (CodDescto, CodLista)  ";
				$ins4.= " VALUES ('".$data['codigo']."','".$data['marca'][$z]."') ";
				//echo $ins4;
                ///$res4 = sqlsrv_query($conn, $ins4);
				$res4 = sqlsrv_query( $conn, $ins4 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				if ($res4) 
					{
					$retorno = 'OK';
					}
				else 
					{
					$retorno = 'ERROR_INGRESO_RELACION'; 
					}
			}	
			
			
		}
	else
		{
		$retorno = 'ERROR_INGRESO_PROMOCION';
        }
	return $retorno;
	}

function EliminarRango($data)
	{
	include('includes/conexion.php');

	$del = "DELETE FROM ".$bd['dsparam'].".[DS_DXVolumenE] where CodDescto='".$data['id']."'";
	///$res = sqlsrv_query($conn, $del);
	$res = sqlsrv_query( $conn, $del , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		$del2 = "DELETE FROM ".$bd['dsparam'].".[DS_DXVolumenD] where CodDescto='".$data['id']."'";
		$res2 = sqlsrv_query( $conn, $del2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
		$del3 = "DELETE FROM ".$bd['dsparam'].".[DS_DXVolumenRela] where CodDescto='".$data['id']."'";
		$res3 = sqlsrv_query( $conn, $del3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
        $retorno = 'OK';
	   //echo $del.$del2.$del3;
	
	return $retorno;
	}	

function TraerRangos($id)
	{
	include('includes/conexion.php');
	
	$sel = "SELECT CodDescto,NomDescto FROM ".$bd['dsparam'].".DS_DXVolumenE WHERE CodDescto='".$id."'";
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if ($res)
		{
		while ($row=sqlsrv_fetch_array($res))
			{
			$data['CodDescto'] = $row['CodDescto'];
			$data['NomDescto'] = $row['NomDescto'];
			}
		}
	else 
		{
		$data = 'ERROR_RANGO';
		}
	return $data;
	}
	
function TraerRangosDet($id)
	{
	include('includes/conexion.php');
	
	$sel = "SELECT Desde, Hasta, Descto FROM ".$bd['dsparam'].".DS_DXVolumenD WHERE CodDescto='".$id."' order by IDCorr ASC";
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if ($res)
		{
		$i=0;
		$j=1;
		while ($row=sqlsrv_fetch_array($res))
			{
			$data.='
			<tr id="fila_'.$i.'">
				<td class="numItem">'.$j.'</td>
				<td><input name="desde[]" type="text" size="10" class="AC_Desde" style="text-align:right;" value="'.$row['Desde'].'" /></td>
				<td><input name="hasta[]" type="text" size="10" class="AC_Hasta" style="text-align:right;" value="'.$row['Hasta'].'" /></td>
				<td><input name="descu[]" type="text" size="10" class="AC_Descu" style="text-align:right;" value="'.$row['Descto'].'" /></td>
				<td><div class="acciones float_right">
					<a href="javascript:addfila();" class="add2 icon">Agregar</a>
					<a href="javascript:deleteDynamicRow(\'#fila_'.$i.'\');" class="delete icon">Eliminar</a>
					</div>
				</td>			
			</tr>';
			$i++;
			$j++;
			}
		}
	else 
		{
		$data = 'USUARIO_NO_EXISTE';
		}
	echo $data;
	}	

	//AGREGADA 27/10
function listarDetalleDescuento($codigo)
{
	//echo $codigo."<------";
	include('includes/conexion.php');
	if($codigo == '')
	{
	$sel = " SELECT a.codlista,a.deslista,(SELECT 1  FROM  ".$bd['dsparam'].".[DS_DXVolumenRela] b where b.CodLista COLLATE Modern_Spanish_CI_AS = a.codlista)   as marca";
	$sel.= " FROM ".$bd['softland'].".iw_tlispre a ";
	
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		echo ' <div class="col-md-10"> ';	
		echo	'<table class="registros table table-hover" id="DynamicRowsTable"> ';
		echo		'<thead> ';
		echo			'<tr id="table_head"> ';
		echo			'	<th class="item">Marca</th> ';
		echo			'	<th class="codlist">Cod. lista</th> ';
		echo			'	<th nowrap="nowrap">Descripci&oacute;n</th> ';
		echo			'</tr> ';
			while ($row=sqlsrv_fetch_array($res))
			{
				echo '<tr>';
					//echo '<tr><td>"'.$row[0].'"</td></tr>';
					if($row['marca'] == 1)
					{
						echo '<td>  </td>';
					}
					else
					{
						echo '<td> <input type="checkbox" name="marca[]" value="'.$row['codlista'].'"> </td>';
					}
					
					
					echo '<td>'.$row['codlista'].'</td><td>'.$row['deslista'].'</td>';
				echo '</tr>';
			}
		echo	'</thead> ';
		echo	'<tbody> ';
		echo	'</tbody>   ';
		echo	'</table>  ';
		echo	'</div>	';
	}
	else
	{
	$sel = " SELECT a.codlista,a.deslista,";
	$sel.= " (SELECT 1 FROM ".$bd['dsparam'].".[DS_DXVolumenRela] b where b.CodLista COLLATE Modern_Spanish_CI_AS = a.codlista) AS marca,";
	$sel.= " (SELECT 2 FROM ".$bd['dsparam'].".[DS_DXVolumenRela] b where b.CodLista COLLATE Modern_Spanish_CI_AS = a.codlista AND b.COdDescto = '".$codigo."') as marcaDos";
	$sel.= " FROM ".$bd['softland'].".iw_tlispre a ";
	//echo $sel;
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		echo ' <div class="col-md-10"> ';	
		echo	'<table class="registros table table-hover" id="DynamicRowsTable"> ';
		echo		'<thead> ';
		echo			'<tr id="table_head"> ';
		echo			'	<th class="item">Marca</th> ';
		echo			'	<th class="codlist">Cod. lista</th> ';
		echo			'	<th nowrap="nowrap">Descripci&oacute;n</th> ';
		echo			'</tr> ';
			while ($row=sqlsrv_fetch_array($res))
			{
				echo '<tr>';
					//echo '<tr><td>"'.$row[0].'"</td></tr>';
					if($row['marca'] == 1 && $row['marcaDos'] == 2)
					{
						echo '<td> <input type="checkbox" name="marca[]" value="'.$row['codlista'].'" checked="checked"> </td>';
					}
					else if($row['marca'] == 1)
					{
						echo '<td> </td>';
					}
					else
					{
						echo '<td> <input type="checkbox" name="marca[]" value="'.$row['codlista'].'"> </td>';
					}
					
					
					echo '<td>'.$row['codlista'].'</td><td>'.$row['deslista'].'</td>';
				echo '</tr>';
			}
		echo	'</thead> ';
		echo	'<tbody> ';
		echo	'</tbody>   ';
		echo	'</table>  ';
		echo	'</div>	';
	}
}
/* ***** FIN CORRESPONDIENTE A RANGOS DESCUENTO ***** */	
/************************  AGREGA prodOtraUmed  */
function agregaProdOtraUmed($data)
	{
	include('includes/conexion.php');
	
	$query_existeCodigo = sqlsrv_query( $conn, "SELECT COUNT(*) AS existe_codigo FROM ".$bd['dsparam'].".[DS_prodotraumed] WHERE CodProd='".$data['prod_codigo'][0]."'" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$row = sqlsrv_fetch_array($query_existeCodigo);	
			$exis = $row['existe_codigo'];
	if($exis > 0)
		{
        $retorno = 'ERROR_EXISTE_CODIGO';
		}
    else
        {
        $res2 = 'OK';
        if ($res2 == 'OK')
            {
            for($c=0; $c < count($data['prod_codigo']); $c++)
                {
				if(strlen($data['prod_codigo'][$c]) == 0 ){ $c ++;}
				else
				{
                $fila = $c + 1;
					
                $ins3 = " INSERT INTO ".$bd['dsparam'].".[DS_prodotraumed] (Codprod,Codumed)  VALUES "; 
                $ins3.= " ('".$data['prod_codigo'][$c]."', '".$data['prod_unimed'][$c]."') ";
				//echo $ins3;
				///$res3 = sqlsrv_query($conn, $ins3);
				$res3 = sqlsrv_query( $conn, $ins3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                if ($res3) 
                    {
                    $retorno = 'OK';
                    }
                else 
                    {
                    $retorno = 'ERROR_INGRESO_DETALLE'; 
                    }
				}
                }
            }
        else
            {
            $retorno = 'ERROR_INGRESO_PROMOCION';
            }
        }
	return $retorno;
	
	}







	
?>