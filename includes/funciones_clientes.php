<?php
function clientesListar($condicion)
	{
	include('includes/conexion.php');
	$dia = date('w');

	$sql = "SELECT cl.CodAux, Substring(cl.NomAux,1,30) as NomAux, cl.DirAux, Isnull(mc.MtoCre,0) AS MontoCredito, 
			".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux) AS PENDIENTE, 
			case when ([dsparam].dbo.[func_SaldoClienteCW](cl.CodAux)) > 0
			THEN (Isnull(mc.MtoCre,0) - ".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux)) 
			ELSE (Isnull(mc.MtoCre,0) + ".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux)) 
			END AS SALDO, mc.ConVta 
			FROM ".$bd['softland'].".[cwtauxi] AS cl
			LEFT JOIN ".$bd['softland'].".[cwtcvcl] AS mc ON cl.CodAux=mc.CodAux AND cl.ActAux ='S'
			LEFT JOIN ".$bd['softland'].".[CWTAuxVen] cwtv on cl.codaux = cwtv.CodAux";
	$checked = array(0 => 'checked="checked"', 1 => '');
	//$mostrar_todos = 'SI';
	$mostrar_todos = $condicion;
	$salida = null;
	
    

	// Si el tipo de usuario es un vendedor, entonces seleccionar solamente sus clientes...
	if($_SESSION['dsparam']['id_tipo_usuario'] == 2)
		{
		//$mostrar_todos = 'NO';	
		$mostrar_todos = '';
		if(isset($_GET['list']))
			{
			if(trim(strtolower($_GET['list'])) == 'all')
				{
				$checked = array(0 => '', 1 => ' checked="checked"');
				$mostrar_todos = 'all';
				$sql .= " AND cl.ClaCli ='S'";
				}
			}
		}
	if($mostrar_todos == '')
		{
		$sql.= " WHERE cl.Faxaux2 like '%".$dia."%' and mc.ConVta is not null";	
		$sql .= " AND cwtv.vencod='".$_SESSION['dsparam']['cod_usuario']."' AND cl.ClaCli='S'";
		}
	//print $sql;
	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
	if($_SESSION['dsparam']['id_tipo_usuario'] == 2)
		{
		$salida .= '
		<div class="margin_bottom_20">
			<label class="margin_right_20">
				<input type="radio" name="mostrar_clientes" value="user" class="margin_right_5" onclick="ir_listado(this);"'.$checked[0].' />Mostrar s&oacute;lo mis clientes
			</label>
			<label>
				<input type="radio" name="mostrar_clientes" value="all" class="margin_right_5" onclick="ir_listado(this);"'.$checked[1].' />Mostrar todos
			</label>
		</div>';
		}
		
	if($num_rows > 0)
		{
		$salida .= '
		<div class="col-md-12">
		<table class="registros table table-hover " id="dataTable"><thead>
		<tr>
			<th>C&oacute;digo</th>
			<th nowrap="nowrap">Nombre Cliente</th>
			<th>Direcci&oacute;n</th>
			<th>Cr&eacute;dito</th>
			<th nowrap="nowrap">Fact. Pendiente</th>
			<th nowrap="nowrap">Total&nbsp;Cr&eacute;dito</th>
			<th nowrap="no-sortable">&nbsp&nbsp;</th>
		</tr>
		</thead><tbody>';
		$n = 0;
	
			while( $row = sqlsrv_fetch_array( $rec, SQLSRV_FETCH_ASSOC) ) 
			{
			if ($row['SALDO']<>'0')
                {
                $credito = $row['MontoCredito'];
                $salfact = $row['PENDIENTE'];
                $creditoTotal = $row['SALDO'];

                if ($creditoTotal>0)
                    {
                    $verdoc = '<a href="javascript:verificarCredito('.$row['CodAux'].', \'tr_'.$n.'\');" title="Generar Nota de Pedido para el RUT '.$row['RutAux'].'" class="guia icon_acciones tooltip_a btnVerificarNota">Generar Nota de Pedido para el RUT '.$row['RutAux'].'</a>';
					$verdoc.= ' <a href="popUp/documentosPendientes.php?codigo='.$row['CodAux'].'" id="href" class="tooltip_a zoom icon_acciones fancybox fancybox.iframe" data-fancybox-width="1050" data-fancybox-height="500" title="Documentos Pendientes">popUp</a> ';
					$verdoc.= ' <a href="popUp/historialCompras.php?codigo='.$row['CodAux'].'&vencod='.$_SESSION['dsparam']['cod_usuario'].'" id="href" class="zoom tooltip_a icon_acciones fancybox fancybox.iframe" data-fancybox-width="1050" data-fancybox-height="500" title="Historial de Compras">popUp</a> ';
                    }
                else if ($creditoTotal<0)
                    {
                    $verdoc = '<a href="javascript:VerDocumentos(\''.$row['CodAux'].'\')"><img src="images/zoom.png" /></a>';    
                    }
                else { $verdoc=''; } 
                $css_facturas = 'dark_grey';
                if ($salfact < 0) { $css_facturas = 'blue'; $salfact = 0; }
                else { $css_facturas = 'dark_grey'; }
                if ($creditoTotal <= 0) { $css_creditoTotal = 'red'; }
                else { $css_creditoTotal = 'dark_grey'; }
				
                $salida .= '
                <tr id="tr_'.$n.'">
				    <td>'.$row['CodAux'].'</td>
				    <td nowrap="nowrap">'.$row['NomAux'].'</td> 
				    <td nowrap="nowrap">'.$row['DirAux'].'</td>
				    <td class="text_align_right padding_right_10"><strong class="dark_grey">'.substr(formato_precio($credito),0,-1).'</strong></td>
				    <td class="text_align_right padding_right_10"><strong class="'.$css_facturas.'">'.substr(formato_precio($salfact),0,-1).'</strong></td>
				    <td class="text_align_right padding_right_10"><strong class="'.$css_creditoTotal.'">'.substr(formato_precio($creditoTotal),0,-1).'</strong></td>
				    <td nowrap="nowrap" width="100">'.$verdoc.'</td>
                </tr>';
                $n = $n + 1;
                }
			}
		$salida .= '</tbody></table></div>';
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
	return $salida;
	//echo $sql;
	
	}

function clientesSeleccionar($cod_cliente)
	{
	include('includes/conexion.php');
	$sql = "SELECT TOP 1 cl.CodAux AS CodCliente, cl.NomAux AS NomCliente, cl.RutAux AS RutCliente, cl.DirAux, cl.DirNum,
			cl.ComAux AS CodComuna, com.ComDes AS Comuna, cl.CiuAux AS CodCiudad, ciu.CiuDes AS Ciudad,
			cl.ProvAux AS CodProvincia, prv.ProvDes AS Provincia, cl.Region AS CodRegion, reg.Descripcion AS Region,
			cl.EMail, cl.FonAux1 AS Fono, cl.bloqueado, vnd.VenCod AS CodVendedor, nvnd.VenDes AS NomVendedor, mc.ConVta AS CodigoCondVenta,
			cnv.Cvedes AS CondVenta 
			FROM ".$bd['softland'].".[cwtauxi] AS cl
			LEFT JOIN ".$bd['softland'].".[cwtregion]		AS reg	ON cl.Region=reg.id_Region
			LEFT JOIN ".$bd['softland'].".[cwtprovincia]	AS prv	ON cl.ProvAux=prv.ProvCod
			LEFT JOIN ".$bd['softland'].".[cwtciud]			AS ciu	ON cl.CiuAux=ciu.CiuCod
			LEFT JOIN ".$bd['softland'].".[cwtcomu]			AS com	ON cl.ComAux=com.ComCod
			LEFT JOIN ".$bd['softland'].".[cwtcvcl]			AS mc	ON cl.CodAux=mc.CodAux 
			LEFT JOIN ".$bd['softland'].".[cwtconv]			AS cnv	ON mc.ConVta=cnv.CveCod
			LEFT JOIN ".$bd['softland'].".[cwtauxven]		AS vnd	ON vnd.CodAux=cl.CodAux
			LEFT JOIN ".$bd['softland'].".[cwtvend]			AS nvnd	ON nvnd.VenCod=vnd.VenCod 
			WHERE cl.CodAux='".$cod_cliente."' AND cl.ActAux ='S' ";
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	//$rec = sqlsrv_query( $conn, $sql );
	
	$salida = null;
	if (sqlsrv_num_rows($rec) == 0) { $salida = 'CLIENTE_NO_EXISTE'; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }

	return $salida;
	
	//print_r(sqlsrv_fetch_object($rec));
	}

function clientesObtenerCredito($cod_cliente)
	{
	include('includes/conexion.php');
	// Obtener Credito del cliente...
	$sql = "SELECT TOP 1 cl.CodAux, cl.RutAux, cl.NomAux, Isnull(mc.MtoCre,0) AS montoCredito FROM ".$bd['softland'].".[cwtauxi] AS cl
			LEFT JOIN ".$bd['softland'].".[cwtcvcl] AS mc ON cl.CodAux=mc.CodAux AND cl.ActAux ='S' WHERE cl.CodAux='".$cod_cliente."'";
	///$res = sqlsrv_query( $conn, $sql );
	$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	//$row = sqlsrv_fetch_array($rec);
	while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
	{
	$montoCredito =  $row['montoCredito'];	
	}
	//return $row['montoCredito'];
	return $montoCredito;
	}

function clientesObtenerMontoFacturasPendientes($cod_cliente)
	{
	include('includes/conexion.php');
	// Obtener saldo facturas pendientes...
	$sql = "SELECT (SUM(movim.MovDebe)-SUM(movim.MovHaber)) AS saldo FROM ".$bd['softland'].".[cwmovim] AS movim
			LEFT JOIN ".$bd['softland'].".[cwcpbte] pbte ON pbte.CpbAno=movim.CpbAno AND pbte.cpbNum=movim.CpbNum
			LEFT JOIN ".$bd['softland'].".[cwpctas] ctas ON ctas.PCCODI=movim.PctCod
			LEFT JOIN ".$bd['softland'].".[cwtauxi] auxi ON auxi.CodAux=movim.CodAux
			LEFT JOIN ".$bd['softland'].".[iw_gsaen] gsaen ON gsaen.folio=movim.MovNumDocRef
			WHERE pbte.CpbEst='V' AND ctas.PCCODI='1-1-03-02-01' AND movim.CpbAno = YEAR(GETDATE()) AND movim.codaux='".$cod_cliente."'";
	//$rec = sqlsrv_query($conn, $sql);
	//$row = sqlsrv_fetch_array($rec);
		///$rec = sqlsrv_query( $conn, $sql );
		$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if( $rec === false) 
		{
		$status = array('tipo' => 'ERROR', 'mensaje' => 'clientesObtenerMontoFacturasPendientes');
		die( print_r( sqlsrv_errors(), true) );		
		}
	
	$sel1 = "SELECT max(movin.codaux) as codaux, max(auxi.nomaux) as saldox, (Sum(movin.movdebe) - sum(movin.movhaber)) as saldo
			FROM ".$bd['softland'].".[cwmovim] movin 
			LEFT JOIN ".$bd['softland'].".cwcpbte pbte on pbte.cpbano = movin.cpbano and pbte.cpbnum = movin.cpbnum
			LEFT JOIN ".$bd['softland'].".cwtauxi auxi on movin.CodAux = auxi.CodAux
			WHERE pbte.cpbest = 'V' AND movin.codaux='".$cod_cliente."' group by movin.codaux
			Having ((Sum(movin.movdebe) - sum(movin.movhaber)) <> 0) and max(auxi.nomaux) is not null and max(movin.CpbAno) = 2015
			order by max(auxi.nomaux),DATEPART (week,max(movin.MovFv)) asc";
	//$res1 = sqlsrv_query($conn, $sel1);
	///$res1 = sqlsrv_query( $conn, $sel1 );
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	//$row1 = sqlsrv_fetch_array($res1);
	while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) ) 
	{
	
	$saldo  = $row['saldo'] + $row1['saldo'];
	
	}
	return $saldo;	
	}

function clientesSelectoresProductos($tipo, $grupo)
	{
	include('includes/conexion.php');
	if($tipo == 'GRUPO')
		{
		$sql = "SELECT [CodGrupo],[DesGrupo] FROM ".$bd['softland'].".[iw_tgrupo] ORDER BY DesGrupo ASC";
		}
	if($tipo == 'SUBGRUPO')
		{
		$sql = "SELECT tsg.CodSubGr, tsg.DesSubGr FROM ".$bd['softland'].".[iw_tsubgr] AS tsg 
				LEFT JOIN ".$bd['softland'].".[iw_tgrsubgr] AS tgsg ON tsg.CodSubGr=tgsg.CodSubGr 
				WHERE tgsg.CodGrupo = '".$grupo."'"; 
				//* SELECT [CodSubGr],[DesSubGr] FROM ".$bd['softland'].".[iw_tsubgr] ORDER BY DesSubGr ASC"; *//
		}
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$options = null;
	while($row = sqlsrv_fetch_array($rec))
		{
		$options .= '<option value="'.$row[0].'">'.mb_strtoupper($row[1], 'utf-8').'</option>';
		}
	echo '<option value="0">- Seleccionar -</option>';
	echo '<option value="0"></option>';
	echo $options;
	}

function SelPromos($codigoCanal)
    {
		///echo $codigoCanal."<---";
    include('includes/conexion.php');
    $fecha = date('Y-m-d');
	$sel = " SELECT codpromo,despromo FROM ".$bd['dsparam'].".[DS_Promociones] ORDER BY codpromo ASC ";
    ///$res = sqlsrv_query($conn, $sel);
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    $opt = '<select name="sel_promos" id="sel_promos" onblur="MuestraProductos();">';    
    if ($res)
        {
        $opt.= '<option value="0">- Seleccionar -</option><option value="0"></option>';
        while($row = sqlsrv_fetch_array($res))
            {
            $opt.= '<option value="'.$row[0].'">'.$row[1].'</option>';
            }
        }
    else
        {
        $opt .= '<input type="text" name="sel_promos" id="sel_promos" value="0" placeholder="NOP HAY PROMOCIONES DISPONIBLES" />';
        } 
	$opt.='</select>';
    echo $opt;
    }

function clientesSelectoresKIT()
	{
	include('includes/conexion.php');
	$sql = "SELECT desprod, CodProd + '[SEP]' + desprod + '[SEP]' + cast(preciovta as varchar(10)) as codigo 
			FROM ".$bd['softland'].".[iw_tprod] WHERE esconfig = -1"; 
	//$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$options = null;
	while($row = sqlsrv_fetch_array($rec))
		{
		$options .= '<option value="'.$row[1].'">'.mb_strtoupper($row[0], 'utf-8').'</option>';
		}
	echo '<option value="0">- Seleccionar -</option>';
	echo '<option value="0"></option>';
	echo $options;
	}
	
function clientesBuscarProductos($buscar, $grupo, $subgrupo, $lisprecio)
	{
	include('includes/conexion.php');
	
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax) { $user_error = 'No, No, No, Usted no puede estar Aqui'; trigger_error($user_error, E_USER_ERROR); }
	
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $buscar);
	$p = count($partes);
	
	if ($lisprecio == '0')
		{
		$sql = "SELECT distinct tp.CodProd, tp.DesProd, tp.CodGrupo, tp.CodSubGr, pd.valorPct as PrecioVta, pd.CodUmed
 as codumed, detumed.desumed as desumed
				FROM ".$bd['softland'].".[iw_tprod] AS tp LEFT JOIN ".$bd['softland'].".[iw_gmovi] AS gm ON tp
.CodProd=gm.CodProd 
				LEFT JOIN ".$bd['softland'].".[iw_tlprprod] AS pd ON tp.CodProd=pd.CodProd
				LEFT JOIN ".$bd['softland'].".[iw_tlispre] AS lp ON pd.CodLista=lp.CodLista 
				LEFT JOIN ".$bd['softland'].".[iw_tumed] AS detumed on pd.CodUmed = detumed.CodUMed
				WHERE   ";
		}
	else
		{
		$sql = "SELECT distinct tp.CodProd, tp.DesProd, tp.CodGrupo, tp.CodSubGr, pd.valorPct as PrecioVta, pd.CodUmed
 as codumed, detumed.desumed as desumed
				FROM ".$bd['softland'].".[iw_tprod] AS tp LEFT JOIN ".$bd['softland'].".[iw_gmovi] AS gm ON tp
.CodProd=gm.CodProd 
				LEFT JOIN ".$bd['softland'].".[iw_tlprprod] AS pd ON tp.CodProd=pd.CodProd
				LEFT JOIN ".$bd['softland'].".[iw_tlispre] AS lp ON pd.CodLista=lp.CodLista 
				LEFT JOIN ".$bd['softland'].".[iw_tumed] AS detumed on pd.CodUmed = detumed.CodUMed
				WHERE   
				";
		}
	for($i = 0; $i < $p; $i++)
		{
		$sql .= "(tp.DesProd LIKE '%".strtoupper($partes[$i])."%' OR tp.CodProd LIKE '%".strtoupper($partes[$i])."%') AND ";
		}
	$sql = substr($sql, 0, -4);
	
	$sql.=" AND tp.CodSubGr ='33' AND lp.CodLista = '".$_REQUEST['codigoLista']."' AND tp.CodGrupo = '".$_REQUEST['CodGrupoRela']."'  GROUP
 BY tp.CodProd, tp.DesProd, tp.CodGrupo, tp.CodSubGr, tp.PrecioVta, pd.ValorPct ,pd.CodUmed,detumed.desumed,
				tp.codumedvta1,tp.codumedvta2, tp.codumed,tp.preciovtaum1,tp.preciovtaum1
 ORDER BY DesProd ASC ";
	/*if($grupo != 0)    { $sql .= " AND tp.CodGrupo='".$grupo."' "; }
	if($subgrupo != 0) { $sql .= " AND tp.CodSubGr='".$subgrupo."' "; }*/
	
	/*if($lisprecio == '0') { $sql .= " AND tp.PrecioVta > '0' AND pd.codlista = '".$lisprecio."' GROUP BY tp.CodProd, tp.DesProd, tp.CodGrupo, tp.CodSubGr, tp.PrecioVta, pd.ValorPct ,pd.CodUmed,detumed.desumed,flete.valorFlete,
				omed.Codprod, omed.Codumed,tp.codumedvta1,tp.codumedvta2, tp.codumed,tp.preciovtaum1,tp.preciovtaum1 ORDER BY DesProd ASC "; }
	else 				  { $sql .= " AND tp.PrecioVta > '0' AND pd.codlista = '".$lisprecio."' GROUP BY tp.CodProd, tp.DesProd, tp.CodGrupo, tp.CodSubGr, tp.PrecioVta, pd.ValorPct ,pd.CodUmed,detumed.desumed,flete.valorFlete,
				omed.Codprod, omed.Codumed,tp.codumedvta1,tp.codumedvta2, tp.codumed,tp.preciovtaum1,tp.preciovtaum1 ORDER BY DesProd ASC ";  }
	*/
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);
	$c = 0;
	//print $sql;
	if($num_rows > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$valorFlete = $row['valorFlete'];
			if($valorFlete == '')
			{
				$valorFlete = 0;
			}
			/*
				if ($lisprecio == '0' )	{ $precio = $row['PrecioVta']; }
				else 					{ $precio = $row['PrecioVta']; }	
			*/
			$a_json_row['value']  = $row['CodProd'] . ' - ' . $row['DesProd'];
			$a_json_row['codigo'] = $row['CodProd'];
			$a_json_row['nombre'] = $row['DesProd'];
			//$a_json_row['precio'] = $precio;
			$a_json_row['stock']  = $row['Stock'];
			$a_json_row['codumed']  = $row['codumed'];
			$a_json_row['desumed']  = $row['desumed'];
			$a_json_row['codigoProd']  = $row['CodProd'];
			$a_json_row['valorFlete']  = $valorFlete;
			
			//echo $row['precioFinal']." - ".$row['omed_codprod']."///   ";
			
			if($row['omed_codprod'] == '' || $row['omed_codprod'] == null)
			{
				$a_json_row['precio'] = $row['PrecioVta'];	
			}
			else
			{
				$a_json_row['precio'] = $row['precioFinal'];	
			}
			
			
			
			array_push($a_json, $a_json_row);
			$c = $c + 1;
			}
		}
	$json = json_encode($a_json);
	print $json;
	//print $sql;
	}

function FuncionBuscarProductoPromocion($codigo)
	{
	include('includes/conexion.php');
	
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax) { $user_error = 'No, No, No, Usted no puede estar Aqui'; trigger_error($user_error, E_USER_ERROR); }
	
	$a_json = array();
	$a_json_row = array();
	$partes = explode(' ', $codigo);
	$p = count($partes);
	
	$sql = "SELECT distinct tp.CodProd, tp.DesProd, tp.PrecioVta, 
			FROM ".$bd['softland'].".[iw_tprod] AS tp WHERE ";
	for($i = 0; $i < $p; $i++)
		{
		$sql .= "(tp.DesProd LIKE '%".strtoupper($partes[$i])."%' OR tp.CodProd LIKE '%".strtoupper($partes[$i])."%') ";
		}
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);
	$c = 0;
	
	if($num_rows > 0)
		{
		while($row = sqlsrv_fetch_array($rec))
			{
			$precio = $row['PrecioVta']; 
			$a_json_row['value']  = $row['CodProd'] . ' - ' . $row['DesProd'];
			$a_json_row['codigo'] = $row['CodProd'];
			$a_json_row['descri'] = $row['DesProd'];
			$a_json_row['precio'] = $precio;
			array_push($a_json, $a_json_row);
			$c = $c + 1;
			}
		}
	$json = json_encode($a_json);
	print $json;
	//echo $sql;
	}
	
/* Generar nuevo numero de guia... */
function clientesGenerarNumeroNota()
{
	include('includes/conexion.php');
	$nro_guia = null;
	$sql = "SELECT ISNULL(MAX(NVNumero) + 1, 1) AS NroGuia FROM ".$bd['dsparam'].".[DS_NotasVenta]";
	///$rs = sqlsrv_query($conn, $sql);
	$rs = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row = sqlsrv_fetch_array($rs)) { $nro_guia = $row['NroGuia']; }
	return $nro_guia;
}

function clientesIngresarNota($data)
	{
	include('includes/conexion.php');
	$salida = null;
	$data['nro_nota_pedido'] = clientesGenerarNumeroNota();

	// Verificar si existe numero de Nota de Pedido...
	$sel = "SELECT COUNT(NVNumero) AS existeNroNota FROM ".$bd['dsparam'].".[DS_NotasVenta] WHERE NVNumero='".$data['nro_nota_pedido']."'";
	///$rec = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($rec);
	$existeNroNota = $row['existeNroNota'];

	if($existeNroNota > 0)
		{
		$salida = 'ERROR_NUMERO_NOTA';
		}
	if($existeNroNota == 0)
		{
		$data['fecha_pedido'] = formatoFechaGuardar($data['fecha_pedido'], '/');
		$cont_exec = 0;
		// Si el cliente no tiene un vendedor asociado, entonces la venta sera realizada con mi codigo de vendedor...
		if(trim($data['cod_vendedor']) == '' && $_SESSION['dsparam']['id_tipo_usuario'] == 2)
			{
			$data['cod_vendedor'] = $_SESSION['dsparam']['cod_usuario'];
			}
		for($p=0; $p < count($data['prod_codigo']); $p++)
			{
			$proc = "EXECUTE ".$bd['dsparam'].".[insertarNv] '".$data['nro_nota_pedido']."', '".$data['fecha_pedido']."', '".$data['prod_codigo'][$p]."', 
					'".$data['prod_cantidad'][$p]."', ".$data['prod_precio_unit'][$p].", 0, 0, 0, 0, 0, 0, '".$data['prod_descripcion'][$p]."', NULL,
					'P', '".$data['cod_cliente']."', '".$data['cod_vendedor']."', NULL, 'SOFTLAND', '".$data['observacion']."', 
					'".$_SESSION['dsparam']['cod_usuario']."','".$data['Contacto']."','".$data['CondVenta']."','".$data['NumOC']."'";
			///$exec = sqlsrv_query($conn, $proc);
			$exec = sqlsrv_query( $conn, $proc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			if(!$exec) 
				{ 
				$cont_exec++; 
				}
			}
		if($cont_exec > 0)
			{
			if( ($cont_exec = sqlsrv_errors() ) != null) 
				{
				foreach( $cont_exec as $cont_exec ) 
					{
					$salida = "SQLSTATE: ".$cont_exec[ 'SQLSTATE']."<br />";
					$salida .="code: ".$cont_exec[ 'code']."<br />";
					$salida .="message: ".$cont_exec[ 'message']."<br />";
					$salida .=$proc;
					}
				}
			}
		if($cont_exec == 0)
			{
			$sql = "INSERT INTO ".$bd['dsparam'].".[DS_NotasVentaDespacho]  ([CodAxD],[NomDch],[DirDch],[ComDch],[CiuDch],[PaiDch],[Fon1Dch],[Fon2Dch],
					[Fon3Dch],[FaxDch],[AteDch],[ProviDch],[RegionDch],[CodPostalDch],[Usuario],[Proceso],[FechaUlMod],[Sistema],[CodGLN]) 
					VALUES (@CodAux, @nvCodDespacho, @nvDireccionDesp, @nvComunaDesp, @nvCiudadDesp, @nvPaisDesp, NULL, NULL, NULL, NULL, NULL, 
					@nvProvDesp, @nvRegionDesp, NULL, NULL, NULL, NULL, NULL, NULL)";
			$salida = 'OK';
			}
		}
	return $salida;
	}

function SelLisPrecios()
	{
	include('includes/conexion.php');
	$hoy = date('d/m/Y');
	
	$sel1 = "SELECT CodLista, DesLista FROM ".$bd['softland'].".[iw_tlispre] WHERE (FechaDesde<='".$hoy."' and FechaHasta>='".$hoy."')";
	///$res1 = sqlsrv_query($conn, $sel1, array(), array('Scrollable' => 'buffered'));
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num1 = sqlsrv_num_rows($res1);
	if ($num1 > 0)
		{
		$salida = "<option value='0'>Lista Base</option>";
		while ($row1 = sqlsrv_fetch_array($res1))
			{
			$salida .= "<option value='".$row1['CodLista']."'>".$row1['DesLista']."</option>";
			}
		}
	else
		{
		$salida = "<option value='0'>Lista Base</option>";
		}
	echo $salida;
	}

function SelCondVenta($CondVenta)
	{
	include('includes/conexion.php');

	
	$sel1 = "SELECT cvecod, cvedes FROM ".$bd['softland'].".[cwtconv]  
	where cvecod ='".$CondVenta."'" ;

	///$res1 = sqlsrv_query($conn, $sel1, array(), array('Scrollable' => 'buffered'));
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num1 = sqlsrv_num_rows($res1);
	if ($num1 > 0)
		{
		while ($row1 = sqlsrv_fetch_array($res1))
			{
				$salida = "<option value='".$row1['cvecod']."'>".$row1['cvedes']."</option>";
			}	
		
		$sel2 = "SELECT cvecod, cvedes FROM ".$bd['softland'].".[cwtconv] order by cvecod ";
		///$res2 = sqlsrv_query($conn, $sel2, array(), array('Scrollable' => 'buffered'));
		$res2 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$num2 = sqlsrv_num_rows($res2);
		 while ($row2 = sqlsrv_fetch_array($res2))
			 {
				 if ($row2['cvecod'] <> $CondVenta)
				 {
					$salida .= "<option value='".$row2['cvecod']."'>".$row2['cvedes']."</option>";
				 }
			 }
		
		}
	else
		{
		$sel2 = "SELECT cvecod, cvedes FROM ".$bd['softland'].".[cwtconv] order by cvecod ";
		///$res2 = sqlsrv_query($conn, $sel2, array(), array('Scrollable' => 'buffered'));
		$res2 = sqlsrv_query( $conn, $sel2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$num2 = sqlsrv_num_rows($res2);
		 while ($row2 = sqlsrv_fetch_array($res2))
			 {
				$salida .= "<option value='".$row2['cvecod']."'>".$row2['cvedes']."</option>";
			 }
		}
	echo $salida;
	}
	
function MaxDesc()
	{
	include('includes/conexion.php');
	
	$sel1 = "SELECT MIN(DESDE) as DESDE FROM ".$bd['dsparam'].".[parametros]";
	///$res1 = sqlsrv_query($conn, $sel1);
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row1 = sqlsrv_fetch_array($res1)) { $max = $row1['DESDE']; }
	return $max;
	}

function DocumentosImpagos()
    {
    include('includes/conexion.php');

    $sql = "SELECT cl.CodAux, Substring(cl.NomAux,1,30) as NomAux, cl.DirAux, Isnull(mc.MtoCre,0) AS MontoCredito, 
            ".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux) AS PENDIENTE, 
            case when ([dsparam].dbo.[func_SaldoClienteCW](cl.CodAux)) > 0
            THEN (Isnull(mc.MtoCre,0) - ".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux)) 
            ELSE (Isnull(mc.MtoCre,0) + ".$bd['dsparam'].".[func_SaldoClienteCW](cl.CodAux)) 
            END AS SALDO, mc.ConVta 
            FROM ".$bd['softland'].".[cwtauxi] AS cl
            LEFT JOIN ".$bd['softland'].".[cwtcvcl] AS mc ON cl.CodAux=mc.CodAux AND cl.ActAux ='S'
            LEFT JOIN ".$bd['softland'].".[CWTAuxVen] cwtv on cl.codaux = cwtv.CodAux";
    $checked = array(0 => 'checked="checked"', 1 => '');
    $mostrar_todos = 'SI';
    $salida = null;
    
    $sql .= " WHERE cl.Faxaux2 like '%".$dia."%' and mc.ConVta is not null";
    
    // Si el tipo de usuario es un vendedor, entonces seleccionar solamente sus clientes...
    if($_SESSION['dsparam']['id_tipo_usuario'] == 2)
        {
        $mostrar_todos = 'NO';  
        if(isset($_GET['list']))
            {
            if(trim(strtolower($_GET['list'])) == 'all')
                {
                $checked = array(0 => '', 1 => ' checked="checked"');
                $mostrar_todos = 'SI';
                $sql .= " AND cl.ClaCli ='S'";
                }
            }
        }
    if($mostrar_todos == 'NO')
        {
        $sql .= " AND cwtv.vencod='".$_SESSION['dsparam']['cod_usuario']."' AND cl.ClaCli='S'";
        }
    ///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
    $num_rows = sqlsrv_num_rows($rec);
    if($_SESSION['dsparam']['id_tipo_usuario'] == 2)
        {
        $salida .= '
        <div class="margin_bottom_20">
            <label class="margin_right_20">
                <input type="radio" name="mostrar_clientes" value="user" class="margin_right_5" onclick="ir_listado(this);"'.$checked[0].' />Mostrar s&oacute;lo mis clientes
            </label>
            <label>
                <input type="radio" name="mostrar_clientes" value="all" class="margin_right_5" onclick="ir_listado(this);"'.$checked[1].' />Mostrar todos
            </label>
        </div>';
        }
    if($num_rows > 0)
        {
        $salida .= '<table class="registros table table-hover" id="dataTable"><thead>
        <tr>
            <th>C&oacute;digo</th>
            <th nowrap="nowrap">Nombre Cliente</th>
            <th>Direcci&oacute;n</th>
            <th>Cr&eacute;dito</th>
            <th nowrap="nowrap">Fact. Pendiente</th>
            <th nowrap="nowrap">Total&nbsp;Cr&eacute;dito</th>
            <th nowrap="no-sortable">&nbsp&nbsp;</th>
        </tr>
        </thead><tbody>';
        $n = 0;
    
        while($row = sqlsrv_fetch_array($rec))
            {
            if ($row['SALDO']<>'0')
                {
                $credito = $row['MontoCredito'];
                $salfact = $row['PENDIENTE'];
                $creditoTotal = $row['SALDO'];

                if ($creditoTotal>0)
                    {
                    $verdoc = '<a href="javascript:verificarCredito('.$row['CodAux'].', \'tr_'.$n.'\');" title="Generar Nota de Pedido para el RUT '.$row['RutAux'].'" class="guia icon tooltip_a btnVerificarNota">Generar Nota de Pedido para el RUT '.$row['RutAux'].'</a>';
                    }
                else if ($creditoTotal<0)
                    {
                    $verdoc = '<a href="javascript:VerDocumentos(\''.$row['CodAux'].'\')"><img src="images/zoom.png" /></a>';    
                    }
                else { $verdoc=''; } 
                $css_facturas = 'dark_grey';
                if ($salfact < 0) { $css_facturas = 'blue'; $salfact = 0; }
                else { $css_facturas = 'dark_grey'; }
                if ($creditoTotal <= 0) { $css_creditoTotal = 'red'; }
                else { $css_creditoTotal = 'dark_grey'; }
                
                $salida .= '
                <tr id="tr_'.$n.'">
                    <td>'.$row['CodAux'].'</td>
                    <td nowrap="nowrap">'.$row['NomAux'].'</td> 
                    <td nowrap="nowrap">'.$row['DirAux'].'</td>
                    <td class="text_align_right padding_right_10"><strong class="dark_grey">'.substr(formato_precio($credito),0,-1).'</strong></td>
                    <td class="text_align_right padding_right_10"><strong class="'.$css_facturas.'">'.substr(formato_precio($salfact),0,-1).'</strong></td>
                    <td class="text_align_right padding_right_10"><strong class="'.$css_creditoTotal.'">'.substr(formato_precio($creditoTotal),0,-1).'</strong></td>
                    <td nowrap="nowrap" width="100">'.$verdoc.'</td>
                </tr>';
                $n = $n + 1;
                }
            }
        $salida .= '</tbody></table>';
        }
    if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
    return $salida;
    }

function CanalLista($codaux)
	{
	include('includes/conexion.php');
	
	$sel1 = "SELECT CodCan, CodLista FROM ".$bd['softland'].".[cwtcvcl] WHERE CodAux='".$codaux."'";
	///$res1 = sqlsrv_query($conn, $sel1);
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row1 = sqlsrv_fetch_array($res1)) 
		{
		$codcan = $row1['CodCan'];
		$codlis = $row1['CodLista'];
		
		$max = $codcan."[||]".$codlis;
		}
	return $max;
	}
	
function DesCanal($codcan)
	{
	include('includes/conexion.php');
	
	$sel1 = "SELECT CanDes FROM ".$bd['softland'].".[cwtcana] WHERE CanCod='".$codcan."'";
	///$res1 = sqlsrv_query($conn, $sel1);
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row1 = sqlsrv_fetch_array($res1)) 
		{
		$candes = $row1['CanDes'];
		}
	echo $candes;
	}

function DesLista($codlis)
	{
	include('includes/conexion.php');
	
	$sel1 = "SELECT DesLista FROM ".$bd['softland'].".[iw_tlispre] WHERE CodLista='".$codlis."'";
	///$res1 = sqlsrv_query($conn, $sel1);
	$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row1 = sqlsrv_fetch_array($res1)) 
		{
		$candes = $row1['DesLista'];
		}
	echo $candes;
	}

	
if($_REQUEST['proceso'] == 'genera')	{
include('conexion.php');
$fecha = $_REQUEST['fecha'];
$excluir = $_REQUEST['excluir'];
//$codigoPromoExcluir = substr($excluir, 0, -1);
//$codigosArray = explode(',', $_REQUEST['salidaCodigo']);
$excluirArray = explode(',', $excluir);


$canalVenta = $_REQUEST['canalVenta'];
	$sel = " SELECT codpromo,despromo FROM ".$bd['dsparam'].".[DS_Promociones] WHERE '".$fecha."'  >= fechaini AND '".$fecha."' <=fechafin  ";
	$sel.= " AND cancod = '".$canalVenta."' ORDER BY codpromo ASC ";
	
	if($excluir == '')
	{
		
	}
	else
	{
		for($z=0; $z < count($excluirArray); $z++){
			$sel.= " AND codpromo NOT IN ('".$excluirArray[$z]."') ";		
			}
	}
	
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$cont = sqlsrv_num_rows($res);
	if($cont > 0){

		$opt = '<label class="col-md-2">Seleccione Promoci&oacute;n</label> ';
		$opt.= '<div class="col-md-3">';
		$opt.= '<select name="sel_promos" id="sel_promos" >'; 
		$opt.= '<option value="">- Seleccionar Promoci&oacute;n -</option>';
        while($row = sqlsrv_fetch_array($res))
            {
            $opt.= ' <option value="'.$row[0].'">'.$row[0].' - '.$row[1].'</option> ';
            }
		$opt.='</select>';
		$opt.= ' </div> ';
		$opt.= ' <div class="col-md-1">&nbsp;</div>  ';
		$opt.= '  <label class="col-md-1">Cantidad</label> ';
		$opt.= '  <div class="col-md-1"><input type="text" name="promoCantidad" id="promoCantidad" tabindex="1" size="40"></div> ';
		$opt.= '  <div class="col-md-1">&nbsp;</div> ';
		$opt.= ' <div class="col-md-2">
			<input type="button" name="boton" id="boton" value=" (+) " tabindex="5" onclick="cargarTabla();"/>
		 </div>';
	
	}
    else
        {
			$opt = '<label class="col-md-2">Seleccione Promoci&oacute;n</label> ';
			$opt.= '<div class="col-md-3">';
			$opt.= '<select name="sel_promos" id="sel_promos" onblur="MuestraProductos();" disabled>';
			$opt.= '</select>';
			$opt.= ' </div> ';
			$opt.= ' <div class="col-md-1">&nbsp;</div>  ';
			$opt.= '  <label class="col-md-1">Cantidad</label> ';
			$opt.= '  <div class="col-md-1"><input type="text" name="" id="" tabindex="1" size="40" disabled></div> ';
			$opt.= '  <div class="col-md-1">&nbsp;</div> ';
			$opt.= ' <div class="col-md-2">
						<input type="button" name="promo_agregar" id="promo_agregar" value=" (+) " tabindex="5" disabled/>
					 </div>';
			
        } 		
    echo $opt;
	//echo $sel;
  
}

if($_REQUEST['proceso'] == 'generaTabla')	
{
	include('conexion.php');
	include('funciones.php');
	$promocion = $_REQUEST['selectedPromos'];
	$cantidad  = $_REQUEST['promoCantidad'];
	$literasPromo = $_REQUEST['selectedPromosText'];
	$registrosTabla = $_REQUEST['registrosTabla'];
	
	$query = " SELECT Promo.codpromo, Promo.despromo, Promo.cancod, PromoDet.codprod, PromoDet.codumed, PromoDet.cantidad, PromoDet.preciovta, iwt.desprod, med.desumed, ";
	$query.= " ROW_NUMBER() OVER(PARTITION BY Promo.codpromo ORDER BY Promo.codpromo asc) AS Row_id , fletes.valorflete ";
	$query.= " FROM ".$bd['dsparam'].".[DS_Promociones] Promo ";
	$query.= " LEFT JOIN ".$bd['dsparam'].".[DS_PromocionesDet] PromoDet ";
	$query.= " LEFT JOIN ".$bd['softland'].".iw_tprod iwt on iwt.codprod = promodet.codprod collate Modern_Spanish_CI_AS ";
	$query.= " ON Promo.codpromo = PromoDet.codpromo ";
	$query.= " LEFT JOIN ".$bd['softland'].".iw_tumed MED ";
	$query.= " ON MED.codumed = PromoDet.codumed collate Modern_Spanish_CI_AS ";
	$query.= " LEFT JOIN [DSPARAM].[dbo].DS_FletesUmed fletes ON fletes.Codprod = PromoDet.codprod ";
	$query.= " WHERE Promo.codpromo = '".$promocion."' ";
	//echo $query;
	
	///$res = sqlsrv_query($conn, $query, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $query , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$cont = sqlsrv_num_rows($res);
		$contador = $registrosTabla +1;
		$subTotal = "";
	if($cont > 0)
	{
        while($row = sqlsrv_fetch_array($res))
            {
			$tabla.= ' <tr class="promocion_'.$row['codpromo'].'"> ';
			$tabla.= ' <td class="indice"><input type="text" name="indice[]" id="indice[]"  value="'.$contador.'" readonly="readonly" class="itemTh itemCelda"></td> ';
			$tabla.= ' <td><input type="text" name="codigoExcluir[]" id="codigo[]"  class="codigo codigoTh" value="'.$row['codprod'].'" readonly="readonly">
						   ';
			if($row['valorflete'] >0)
			{
			$tabla.= '<input type="hidden" class="valorFlete" name="valorFlete" id="'.($row['valorflete'] * $cantidad).'" value="'.($row['valorflete'] * $cantidad).'">';	
			}
			$tabla.= ' </td> ';
			$tabla.= ' <td><input type="text" class="descripcionProducto descripcionTh" name="descripcion[]" id="descripcion[]"  value="'.$row['desprod'].'" readonly="readonly"></td> ';
			$tabla.= ' <td><input type="text" name="cantidad[]" id="cantidad[]" class="cantidadTh cantidad cantidad_'.$contador.'" value="'.substr(formatoNumero($row['cantidad'] * $cantidad),0,-1).'" readonly="readonly" style="text-align:right;"></td>' ;
			$tabla.= ' <td><input type="text" name="precio[]" id="precio[]" class="precioTh precio precio_'.$contador.'" value="'.substr(formato_precio($row['preciovta']),0,-1).'" readonly="readonly" style="text-align:right;"></td>';
			$tabla.= ' <td><input type="text" name="subTotal[]" id="subTotal[]" class="subTotal subTotalTh" value="'.substr(formato_precio($row['preciovta'] * ($row['cantidad'] * $cantidad)),0,-1).'" readonly="readonly" style="text-align:right;"></td>';
			$tabla.= ' <td> ';
			$tabla.= ' <input type="text" class="codigoPromoTh" name="literalPromo" id="literalPromo" value= "'.$literasPromo.'" readonly="readonly"> ';
			$tabla.= ' </td> ';
			$tabla.= '<td><input type="text" name="unidadmedida[]" class="unimedTh" id="unidadmedida[]"  value="'.$row['codumed'].' - '.$row['desumed'].'" readonly="readonly">
							<input type="hidden" name="codumed" id="codumed"  class="unimed" value="'.$row['codumed'].'" readonly="readonly">
						</td>';
			if($row['Row_id'] == '1')
			{
				$tabla.= '<td><input type="button" id="promocion_'.$row['codpromo'].'" value="eliminar" onclick="eliminarFilas(this.id);"></td> ';
			}
			else
			{
			$tabla.= ' <td></td> ';
			}
			$tabla.= ' </tr>';
            $contador++;
			$subTotal = $subTotal + ($row['preciovta'] * ($row['cantidad'] * $cantidad));
			
			
			
            }	
	}
	else
		{
			echo "Sin registros";
		} 	

	echo $tabla;
		/*echo "	<script type='text/javascript'>
			subTotal(".$subTotal.");
			</script>
			
	";
	*/
}

if($_REQUEST['proceso'] == 'tabla')	{
	
	$hasta = count($_REQUEST['indice']);
	
	$indice 		= $_REQUEST['indice'];
	$codigo 		= $_REQUEST['codigo'];
	$descripcion 	= $_REQUEST['descripcion'];
	$precio 		= $_REQUEST['precio'];
	$subTotal 		= $_REQUEST['subTotal'];
	
	for($a=0; $a < $hasta; $a++) 
	{
		echo $indice[$a]." - ".$codigo[$a]." - ".$descripcion[$a]." - ".$precio[$a]." - ".$subTotal[$a]."\r\n";
	}	
}

if($_REQUEST['proceso'] == 'formatoMoneda')	{
	$valor = $_REQUEST['moneda'];
	if($valor < 1){
		echo $valor;
	}
	else if($_REQUEST['simbolo'] == 'a'){
		echo '$'.number_format($valor,0,'','.');	
	}
	else if($_REQUEST['simbolo'] == 'b'){
		echo number_format($valor,0,'','.');

	}
	
	
}

function despachoCliente($codigoCliente)
	{
	include('includes/conexion.php');
	$sql = "SELECT CodAxD, NomDch  FROM ".$bd['softland'].".[cwtauxd] WHERE CodAxD = '".$codigoCliente."'";
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$options = null;
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row[0] == $seleccionado)
			$selected = ' selected="selected"';
		$linea .= '<option value="'.$row[0].'"'.$selected.'>'.mb_strtoupper($row[1], 'utf-8').'</option>';
		}
	echo '<option value="">-Seleccionar-</option>';
	echo '<option value=""></option>';
	echo $linea;
	}
/* ***** GENERALES ***** */

function selectGiroSII($seleccionado)
	{
	include('includes/conexion.php');
	$sql = "SELECT [GirCod],[GirDes] FROM ".$bd['softland'].".[cwtgiro] ORDER BY GirDes ASC";
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$linea = '<select name="cod_giro_sii" id="cod_giro_sii" /><option value=""></option>';
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row['GirCod'] == $seleccionado)
			$selected = ' selected="selected"';
		$linea .= '<option value="'.$row['GirCod'].'"'.$selected.'>'.mb_strtoupper($row['GirDes'], 'utf-8').'</option>';
		}
	$linea .= '</select>';
	return $linea;
	}

function selectOptionsClientes($tipo, $data01, $data02, $data03)
	{
	include('includes/conexion.php');
	if($tipo == 'REGIONES')
		{
		$sql = "SELECT [id_Region],[Descripcion] FROM ".$bd['softland'].".[cwtregion] ORDER BY id_Region ASC";
		$seleccionado = $data01;
		}
	if($tipo == 'CIUDADES')
		{
		$sql = "SELECT [CiuCod],[CiuDes],[Id_Region] FROM ".$bd['softland'].".[cwtciud] WHERE Id_Region='".$data01."' ORDER BY CiuDes ASC";
		$seleccionado = $data02;
		}
	if($tipo == 'COMUNAS')
		{
		$sql = "SELECT [ComCod],[ComDes],[Id_Region] FROM ".$bd['softland'].".[cwtcomu] WHERE Id_Region='".$data01."' ORDER BY ComDes ASC";
		$seleccionado = $data03;
		}
	if($tipo == 'MANDANTES')
		{
		$sql = "SELECT [CodAux],[NomAux] FROM ".$bd['softland'].".[cwtauxi] WHERE ClaSoc='S' ORDER BY [NomAux] ASC";
		$seleccionado = $data01;
		}
	print $sql;
	///$rec = sqlsrv_query($conn, $sql);
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$linea = null;
	while($row = sqlsrv_fetch_array($rec))
		{
		$selected = '';
		if($row[0] == $seleccionado)
			$selected = ' selected="selected"';
		$linea .= '<option value="'.$row[0].'"'.$selected.'>'.mb_strtoupper($row[1], 'utf-8').'</option>';
		}
	echo '<option value="">-Seleccionar-</option>';
	echo '<option value=""></option>';
	echo $linea;
	}


	
	function ClientesContactosListar($codigo)
	{
	$search  = array('[REMP1]', '[REMP2]', '[REMP3]', '[REMP4]', '[REMP5]');
	$fila = '
	<tr id="fila_'.$search[0].'">
		<td><input name="cnt_nombre[]" type="text" class="requerido" value="'.$search[1].'" maxlength="30" size="30" /></td>
		<td><input name="cnt_telefono_01[]" type="text" class="requerido" value="'.$search[2].'" maxlength="15" size="8" /></td>
		<td><input name="cnt_telefono_02[]" type="text" value="'.$search[3].'" maxlength="15" size="8" /></td>
		<td><input name="cnt_correo[]" type="email" class="requerido" value="'.$search[4].'" placeholder="ejemplo@ejemplo.cl" maxlength="100" size="30" /></td>
		<td><div class="acciones float_center"><img src="images/no_icon.png" width="16" height="16" /></div></td>
	</tr>';
	$n = 0;
	$salida = '<table class="registros table table-hover" id="DynamicRowsTable">
	<tr id="table_head">
		<th>Nombre</th>
		<th>Tel&eacute;fono 1</th>
		<th>Tel&eacute;fono 2</th>
		<th>Correo Electr&oacute;nico</th>
		<th>&nbsp;</th>
	</tr>';
	if($codigo == ''){ $salida .= str_replace($search, array('0', '', '', '', ''), $fila); $n = 1;}
	if($codigo != '')
		{
		include('includes/conexion.php');
		$sql = "SELECT [CodAuc],[NomCon],[FonCon],[FonCon2],[Email] FROM ".$bd['softland'].".[cwtaxco] WHERE CodAuc='".$codigo."' ORDER BY [NomCon] ASC";
		///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
		$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$num_rows = sqlsrv_num_rows($rec);
		if($num_rows == 0){ $salida .= str_replace($search, array('0', '', '', '', ''), $fila); $n = 1;}
		if($num_rows > 0)
			{
			while($row = sqlsrv_fetch_array($rec))
				{
				$prev_salida = str_replace($search, array($n, $row['NomCon'], $row['FonCon'], $row['FonCon2'], $row['Email']), $fila);
				if($n > 0)
					{
					$prev_salida = str_replace('<img src="images/no_icon.png" width="16" height="16" />',
					'<a href="javascript:deleteDynamicRow(\'#fila_'.$n.'\');" class="delete icon" title="Eliminar Contacto" alt="Eliminar Contacto">Eliminar</a>', $prev_salida);
					}
				$salida .= $prev_salida;
				$n = $n + 1;
				}
			}
		}
	echo '<option value="0">- Seleccionar -</option>';
	echo $options;
	}

//funciones POPUP

function documentosPendientes()
	{
	include('../includes/conexion.php');
	$codaux = $_REQUEST['codigo'];
	$fechaSql = date("Y/d/m");
	$sql = " SELECT gs.tipo,gs.Folio,gs.fecha,gs.fechavenc,gs.total,sum(movi.MovDebe) -sum(movi.MovHaber) as saldoCW ";
	$sql.= " FROM ".$bd['softland'].".iw_gsaen gs ";
	$sql.= " LEFT JOIN ".$bd['softland'].".cwmovim movi on gs.ttdcod = movi.MovTipDocRef and gs.Folio = movi.MovNumDocRef ";
	$sql.= " WHERE gs.tipo in ('F','B','N','D') and gs.fechavenc > '".$fechaSql."' ";
	//$sql.= " WHERE gs.tipo in ('F','B','N','D') and gs.fechavenc > '2010/01/01' ";
	$sql.= " AND gs.codaux ='".$codaux."' ";
	$sql.= " GROUP BY gs.tipo,gs.Folio,gs.fecha,gs.fechavenc,gs.total  ";

	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
	//echo $sql;
	
	if($num_rows > 0)
	{
	?>
	<table class="nota" id="DynamicRowsTable">
		<thead>
			<tr>
				<th>Tipo</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>Fecha Venc</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
		while($row = sqlsrv_fetch_array($rec))
		{
			
			echo "<tr>";
			echo "<td>".$row['tipo']."</td>";
			echo "<td>".$row['Folio']."</td>";
			echo "<td>".date_format($row['fecha'], 'Y/m/d')."</td>";
			echo "<td>".date_format($row['fechavenc'], 'Y/m/d')."</td>";
			echo "<td>".$row['total']."</td>";
			echo "</tr>";
			
			
		}
		
		?>
		</tbody>
	</table>

		<?php
		
		
	}
	else
	{
		echo "Sin informaci&oacute;n";
	}
	
	}
function historialCompras()
	{
	include('../includes/conexion.php');
	$codaux = $_REQUEST['codigo'];
	$fechaSql = date("Y/d/m");
	//$fechaSql = date("d/m/Y");
/*	
$sql = " SELECT gs.tipo,gs.Folio,gs.fecha,gs.fechavenc,gs.total,sum(movi.MovDebe) -sum(movi.MovHaber) as saldoCW  ";
$sql.= " FROM ".$bd['softland'].".iw_gsaen gs ";
$sql.= " LEFT JOIN alma.softland.iw_gmovi gm on gs.tipo = gm.tipo and gs.nroint =gm.nroint ";
$sql.= " where gs.tipo in ('F','B','N','D') and gs.fecha > convert(datetime,datediff(day,9999, '".$fechaSql."'),103) ";
$sql.= " and gs.codaux ='".$codaux."' ";
*/


$sql = " SELECT distinct gs.Tipo, gs.folio,gs.fecha,gm.linea,gm.codprod,Cast(gm.detprod as varchar(60)) as detprod, gm.CantFacturada,gm.PreUnimb, gm.totlinea ";
$sql.= " FROM alma.softland.iw_gsaen gs ";
$sql.= " LEFT JOIN alma.softland.iw_gmovi gm on gs.tipo = gm.tipo and gs.nroint =gm.nroint ";
$sql.= " WHERE gs.tipo in ('F','B','N','D') and  Convert(datetime,datediff(day,30,convert(datetime,'".$fechaSql."',103)),103) > gs.fecha and gs.codaux ='".$codaux."' ";

	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
	//echo $sql;
	if($num_rows > 0)
	{
	?>
	<table class="nota_popup" id="DynamicRowsTable">
		<thead>
			<tr>
				<th>Tipo</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>Linea</th>
				<th>C&oacute;digo Producto</th>
				<th>Descripci&oacute;n Producto</th>
				<th>Cantidad Facturada</th>
				<th>Precio Unitario</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
		while($row = sqlsrv_fetch_array($rec))
		{
			if($row['CantFacturada'] <2)
			{
				$cantidad = $row['CantFacturada'];
			}
			else if($row['CantFacturada'] > 999)
			{
				$cantidad = $row['CantFacturada'];
			}
			else
			{
				$cantidad = formatoNum($row['CantFacturada']);
			}
			//'.substr(formato_precio($credito),0,-1).'
			echo "<tr>";
			echo "<td>".$row['Tipo']."</td>";
			echo "<td>".$row['folio']."</td>";
			echo "<td>".date_format($row['fecha'], 'Y/m/d')."</td>";
			echo "<td>".$row['linea']."</td>";
			echo "<td>".$row['codprod']."</td>";
			echo "<td>".$row['detprod']."</td>";
			echo "<td style='text-align:right'>".$cantidad."</td>";
			echo "<td style='text-align:right'>".substr(formato_precio($row['PreUnimb']),0,-1)."</td>";
			echo "<td style='text-align:right'>".substr(formato_precio($row['totlinea']),0,-1)."</td>";
			echo "</tr>";
		}
		
		?>
		</tbody>
	</table>

		<?php
		
		
	}
	else
	{
		echo "Sin informaci&oacute;n";
	}
	}
	
function obtenerDatosAuxiliar($codigoAuxiliar)
{
	//echo $codigoAuxiliar."<br>";
	include('includes/conexion.php');
	$sql =" SELECT a.codaux,a.nomaux,b.codlista FROM ".$bd['softland'].".cwtauxi a ";
	$sql.=" LEFT JOIN ".$bd['softland'].".cwtcvcl b on a.codaux=b.codaux where a.CodAux='".$codigoAuxiliar."' ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = null;
	if (sqlsrv_num_rows($rec) == 0) { $salida = 'SIN_DATOS'; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }

	return $salida;
}


function obtenerDatosListaPrecio($codLista)
{
	include('includes/conexion.php');
	$sql =" SELECT * FROM  ".$bd['softland'].".iw_tlispre WHERE codlista ='".$codLista."' ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	$salida = null;
	if (sqlsrv_num_rows($rec) == 0) { $salida = 'SIN_DATOS'; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }

	return $salida;
}


function EnviaMail()
	{
	include('includes/config.php');
	include('includes/conexion.php');
	
	require_once('includes/phpMailer/class.phpmailer.php');
	require_once('includes/phpMailer/class.smtp.php');
	
	$FecHoy = date('d-m-Y H:i:s');
	$docrel = $doc;
	
	/* CABECERA DEL MAIL */
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'ssl';
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->Username = 'sistema.op.dsc@gmail.com';
	$mail->Password = 'op.DSC.2015';
	$mail->SetFrom('sistema.op.dsc@gmail.com', 'DSC S.A.');
	
	// Tipos de Envio
	// 1.- Envio de Mail con Codigo de Guia Despacho para Anulación

		$msj ="MENSAJE DE CORREO,<br /><br />";
		$asunto = "ASUNTO";
		
	/*$mail->addAddress('nicolas.fabres@dscsa.cl','Nicolas Fabres');
	$mail->addCC('freddy.diaz@dscsa.cl','Freddy Diaz');	
	$mail->addCC('anahi.leon@dscsa.cl' ,'Anahi Leon');	
	$mail->addCC('rsoto@disofi.cl','El Mejor');	
*/
$mail->addAddress('rodrigoretamal@outlook.com','Rodrigo Retamal');
$mail->addCC('rodrigortml@gmail.com','Rodrigo Retamal Gmail');	
	$mail->Subject = $asunto;
	$mail->Body = $msj;
	$mail->MsgHTML($msj);
	$mail->CharSet = 'UTF-8';
	$mail->Send();
	}
/*
function listasNotasDeVentas($condicion,$vencod)
	{
	//echo $condicion." : Condicion";
	//echo $_SESSION['dsparam']['id_tipo_usuario'];
	//echo $_SESSION['dsparam']['cliente'];
	include('includes/conexion.php');
	$dia = date('w');
	if($condicion == '')
	{
	$sql =" SELECT NVNumero, NVFem, nvFeEnt, nota.CodAux, auxi.NomAux, NVObser,nvMonto FROM ".$bd['dsparam'].".[DS_NotasVenta] nota ";
	$sql.=" LEFT JOIN ".$bd['softland'].".[cwtauxi] auxi ON auxi.CodAux = nota.codaux collate Modern_Spanish_CI_AS ";
	if($_SESSION['dsparam']['id_tipo_usuario'] == 2 || $_SESSION['dsparam']['id_tipo_usuario'] == '2')
	{
		$sql.=" WHERE nota.CodAux = '".$_SESSION['dsparam']['cliente']."' ";
	}
	else
	{
		$sql.="  ";
	}
	//$checked = array(0 => 'checked="checked"', 1 => '');
	}
	else
	{
		$sql =" SELECT NVNumero, NVFem, nvFeEnt, nota.CodAux, auxi.NomAux, NVObser,nvMonto FROM ".$bd['dsparam'].".[DS_NotasVenta] nota ";
		$sql.=" LEFT JOIN ".$bd['softland'].".[cwtauxi] auxi ON auxi.CodAux = nota.codaux collate Modern_Spanish_CI_AS ";
		$sql.=" WHERE nvFeEnt = CONVERT(datetime,'".$condicion."',103) AND estadoNP = 'P' AND vencod = '".$vencod."'";
	}
			
	
	//echo $sql;
	
	//$mostrar_todos = 'SI';
	//$mostrar_todos = $condicion;
	$salida = null;	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
		
	if($num_rows > 0)
		{
		$salida .= '
		<div class="table-responsive">
		<div class="col-md-12">
		<table class="registros table table-hover " id="dataTable"><thead>
		<tr>
			<th nowrap="nowrap">Nota Pedido</th>
			<th>Fecha Emision</th>
			<th>Fecha Entrega</th>
			<th>Auxiliar</th>
			<th nowrap="nowrap">Observaci&oacute;n</th>
			<th nowrap="nowrap">Total Nota Pedido</th>
			<th nowrap="no-sortable">&nbsp&nbsp;</th>
		</tr>
		</thead><tbody>';
		$n = 0;
	
			while( $row = sqlsrv_fetch_array( $rec, SQLSRV_FETCH_ASSOC) ) 
			{
			
                $salida .= '
                <tr id="tr_'.$n.'">
				    <td>'.$row['NVNumero'].'</td>
				    <td nowrap="nowrap">'.date_format($row['NVFem'], 'd/m/Y').'</td> 
					<td nowrap="nowrap">'.date_format($row['nvFeEnt'], 'd/m/Y').'</td> 
				    <td nowrap="nowrap">'.$row['CodAux']." - ".$row['NomAux'].'</td>
					<td nowrap="nowrap">'.$row['NVObser'].'</td>
				    <td class="text_align_right padding_right_10"><strong class="'.$css_facturas.'">'.substr(formato_precio($row['nvMonto']),0,-1).'</strong>
					</td>
				    <td width="100">
					';
				$salida.='<a href="popUp/detalle-nota-pedido.php?id='.$row['NVNumero'].'" data-fancybox-width="1050" data-fancybox-height="650" class="zoom fancybox fancybox.iframe icon" alt="Ver detalle nota de pedido '.$row['NVNumero'].'" title="Ver detalle nota de pedido '.$row['NVNumero'].'">'.$row['NVNumero'].'</a>';
				//$salida.='<a href="index.php?mod=list-notas-pedido-detalle&nv='.$row['NVNumero'].'" class="zoom_lupa tooltip_a icon_acciones" title="Ver detalle nota de pedido">popUp</a>';
				$salida.='</td>
                </tr>';
                $n = $n + 1;
                
			}
		if($condicion <> '')
		{
			$salida .= '</tbody></table></div></div> <div class="row">
	<div class="col-sm-11">
		<input type="button" id="agrupar" class="float_right margin_top_10" value="Agrupar" onclick="agruparNotaVenta();"/>
	</div>
</div>	';
		}
		else
		{
		$salida .= '</tbody></table></div></div>';
		}
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
	//echo $sql;
	return $salida;
	
	
	}	
	
	*/
	
	function listasNotasDeVentas($condicion,$vencod)
	{
	//echo $condicion." : Condicion";
	//echo $_SESSION['dsparam']['id_tipo_usuario'];
	//echo $_SESSION['dsparam']['cliente'];
	include('includes/conexion.php');
	$dia = date('w');
	if($condicion == '')
	{
	$sql =" SELECT NVNumero, NVFem, nvFeEnt, nota.CodAux, auxi.NomAux, NVObser,nvMonto,nota.codvenweb, usuario.usuario,  ";
	$sql.="	CASE ";
    $sql.=" WHEN nota.estadoNP =  'P' THEN 'Pendiente' ";
    $sql.=" ELSE 'Agrupado' ";
	$sql.=" END as estadoNP  FROM ".$bd['dsparam'].".[DS_NotasVenta] nota ";
	$sql.=" LEFT JOIN ".$bd['softland'].".[cwtauxi] auxi ON auxi.CodAux = nota.codaux collate Modern_Spanish_CI_AS ";
	$sql.=" LEFT JOIN ".$bd['dsparam'].".[DS_Usuarios] usuario ON nota.codvenweb = usuario.id ";

		if($_SESSION['dsparam']['id_tipo_usuario'] == 2 || $_SESSION['dsparam']['id_tipo_usuario'] == '2')
		{
			$sql.=" WHERE nota.CodAux = '".$_SESSION['dsparam']['cliente']."' ";
		}
		else
		{
			$sql.="  ";
		}
	//$checked = array(0 => 'checked="checked"', 1 => '');
	}
	else
	{
		$sql =" SELECT NVNumero, NVFem, nvFeEnt, nota.CodAux, auxi.NomAux, NVObser,nvMonto,nota.codvenweb,usuario.usuario,  ";
		$sql.=" CASE ";
		$sql.="	WHEN nota.estadoNP =  'P' THEN 'Pendiente' ";
		$sql.=" ELSE 'Agrupado' ";
		$sql.="	END as estadoNP  FROM ".$bd['dsparam'].".[DS_NotasVenta] nota ";
		$sql.=" LEFT JOIN ".$bd['softland'].".[cwtauxi] auxi ON auxi.CodAux = nota.codaux collate Modern_Spanish_CI_AS ";
		$sql.=" LEFT JOIN ".$bd['dsparam'].".[DS_Usuarios] usuario ON nota.codvenweb = usuario.id ";
		//$sql.=" WHERE nvFeEnt = CONVERT(datetime,'".$condicion."',103) AND estadoNP = 'P' AND vencod = '".$vencod."'";
		$sql.=" WHERE CONVERT(datetime,CONVERT(varchar(10), nvFeEnt, 103),103)=CONVERT(datetime, '".$condicion."', 103) ";
		$sql.=" AND estadoNP = 'P' AND vencod = '".$vencod."' ";
	}
	//echo $sql;
	
	//$mostrar_todos = 'SI';
	//$mostrar_todos = $condicion;
	$salida = null;	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
		
	if($num_rows > 0)
		{
			if($vencod == '' && $condicion == '')
			{
				$salida .= '
					<div class="table-responsive">
					<div class="col-md-12">
					<table class="registros table table-hover " id="dataTable"><thead>
					<tr>
						<th nowrap="nowrap">Nota Pedido</th>
						<th>Fecha Emision</th>
						<th>Fecha Entrega</th>
						<th>Auxiliar</th>
						<th nowrap="nowrap">Observaci&oacute;n</th>
						<th nowrap="nowrap">Total Nota Pedido</th>
						<th nowrap="nowrap">Estado NP</th>
						<th nowrap="nowrap">Usuario Agrupador</th>
						
						<th nowrap="no-sortable">&nbsp&nbsp;</th>
					</tr>
					</thead><tbody>';
			}
			else
			{
				$salida .= '
					<div class="table-responsive">
					<div class="col-md-12">
					<table class="registros table table-hover " id="dataTable"><thead>
					<tr>
						<th nowrap="nowrap">Nota Pedido</th>
						<th>Fecha Emision</th>
						<th>Fecha Entrega</th>
						<th>Auxiliar</th>
						<th nowrap="nowrap">Observaci&oacute;n</th>
						<th nowrap="nowrap">Total Nota Pedido</th>
						<th nowrap="nowrap">Estado NP</th>
						
						<th nowrap="no-sortable">&nbsp&nbsp;</th>
					</tr>
					</thead><tbody>';
			}
		
		$n = 0;
	
			while( $row = sqlsrv_fetch_array( $rec, SQLSRV_FETCH_ASSOC) ) 
			{
               
				if($vencod == '' && $condicion == '')
				{
				   $salida.= '
						<tr id="tr_'.$n.'">
							<td>'.$row['NVNumero'].'</td>
							<td nowrap="nowrap">'.date_format($row['NVFem'], 'd/m/Y').'</td> 
							<td nowrap="nowrap">'.date_format($row['nvFeEnt'], 'd/m/Y').'</td> 
							<td nowrap="nowrap">'.$row['NomAux'].'</td>
							<td nowrap="nowrap">'.$row['NVObser'].'</td>
							<td class="text_align_right padding_right_10"><strong class="'.$css_facturas.'">'.substr(formato_precio($row['nvMonto']),0,-1).'</strong>
							</td>
							<td nowrap="nowrap">'.$row['estadoNP'].'</td>
							<td nowrap="nowrap">'.$row['usuario'].'</td>
							<td width="100">
							';
				}
				else
				{
					$salida.= '
					<tr id="tr_'.$n.'">
						<td>'.$row['NVNumero'].'</td>
						<td nowrap="nowrap">'.date_format($row['NVFem'], 'd/m/Y').'</td> 
						<td nowrap="nowrap">'.date_format($row['nvFeEnt'], 'd/m/Y').'</td> 
						<td nowrap="nowrap">'.$row['CodAux']." - ".$row['NomAux'].'</td>
						<td nowrap="nowrap">'.$row['NVObser'].'</td>
						<td class="text_align_right padding_right_10">
							<strong class="'.$css_facturas.'">'.substr(formato_precio($row['nvMonto']),0,-1).'</strong>
						</td>
						<td nowrap="nowrap">'.$row['estadoNP'].'</td>
						<td width="100">
						';
				}
				$salida.='<a href="popUp/detalle-nota-pedido.php?id='.$row['NVNumero'].'" data-fancybox-width="1050" data-fancybox-height="650" class="zoom fancybox fancybox.iframe icon" alt="Ver detalle nota de pedido '.$row['NVNumero'].'" title="Ver detalle nota de pedido '.$row['NVNumero'].'">'.$row['NVNumero'].'</a>';
				//$salida.='<a href="index.php?mod=list-notas-pedido-detalle&nv='.$row['NVNumero'].'" class="zoom_lupa tooltip_a icon_acciones" title="Ver detalle nota de pedido">popUp</a>';
				$salida.='</td>
                </tr>';
                $n = $n + 1;
                
			}
		if($condicion <> '')
		{
			$salida .= '</tbody></table></div></div> <div class="row">
							<div class="col-sm-11">
								<input type="button" id="agrupar" class="float_right margin_top_10" value="Agrupar" onclick="agruparNotaVenta();"/>
							</div>
						</div>';
		}
		else
		{
			$salida .= '</tbody></table></div></div>';
		}
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
	//echo $sql;
	return $salida;
	
	
	}	
	
	
	
	
	
function listasNotasDeVentasDetalle($id)
	{
	include('includes/conexion.php');
	$dia = date('w');
	
	$sql =" SELECT CodProd, DetProd, nvFecCompr, nvCant, nvPrecio, nvSubTotal, CodUMed FROM ".$bd['dsparam'].".[DS_NotasVentaDetalle] ";
	$sql.=" WHERE NVNumero = '".$id."' ";
	//echo $sql;
	$checked = array(0 => 'checked="checked"', 1 => '');
	//$mostrar_todos = 'SI';
	$salida = null;	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
		
	if($num_rows > 0)
		{
		$salida .= '
		<div class="col-md-12">
		<table class="registros table table-hover " id="dataTable"><thead>
		<tr>
			<th nowrap="nowrap">Codigo Producto</th>
			<th>Detalle Producto</th>
			<th>Fecha</th>
			<th>Cantidad</th>
			<th>Unidad Medida</th>
			<th>Precio</th>
			<th>Sub Total</th>
		</tr>
		</thead><tbody>';
		$n = 0;
		
			while( $row = sqlsrv_fetch_array( $rec, SQLSRV_FETCH_ASSOC) ) 
			{
			
                $salida .= '
                <tr id="tr_'.$n.'">
				    <td>'.$row['CodProd'].'</td>
				    <td nowrap="nowrap">'.$row['DetProd'].'</td>
					<td nowrap="nowrap">'.date_format($row['nvFecCompr'], 'd/m/Y').'</td> 
					<td nowrap="nowrap" align="right">'.substr(formatoNumero($row['nvCant']),0,-1).'</td>
					<td nowrap="nowrap" align="right">'.$row['CodUMed'].'</td>
					<td nowrap="nowrap" align="right">'.substr(formato_precio($row['nvPrecio']),0,-1).'</td>
				    <td class="text_align_right padding_right_10"><strong>'.substr(formato_precio($row['nvSubTotal']),0,-1).'</strong>
					</td>
                </tr>';
				$total += $row['nvSubTotal'];
                
			}
		$salida .= '
		</tbody>
		<tbody><tr>
		<td colspan="6" align="right"><strong>Total</strong></td>
		<td class="text_align_right padding_right_10"><strong>'.substr(formato_precio($total),0,-1).'</td>
		</tr></tbody></table></div>';
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
	return $salida;
	//echo $sql;
	
	}	
function encabezadoDetallePopUp($notaPedido)
{

	//echo $codigoAuxiliar."<br>";
	include('../includes/conexion.php');
	//$sql =" SELECT * FROM ".$bd['dsparam'].".DS_NotasVenta ";
	$sql = " SELECT np.VenCod,vendedor.VenDes, np.CveCod, condicion.CveDes, np.CodLista, listaPrecio.DesLista, np.CodiCC, ccosto.DescCC, ";
	$sql.= " np.nvFem, np.nvFeEnt, np.CodAux, auxiliar.NomAux, np.NomCon, np.nvObser";
	$sql.= " FROM ".$bd['dsparam'].".ds_notasVenta np ";
	$sql.= " LEFT JOIN ".$bd['softland'].".cwtvend vendedor ON np.VenCod = vendedor.VenCod COLLATE SQL_Latin1_General_CP1_CI_AI ";
	$sql.= " LEFT JOIN ".$bd['softland'].".[cwtconv] condicion ON np.CveCod = condicion.CveCod COLLATE SQL_Latin1_General_CP1_CI_AI ";
	$sql.= " LEFT JOIN ".$bd['softland'].".iw_tlispre listaPrecio ON np.CodLista = listaPrecio.CodLista COLLATE SQL_Latin1_General_CP1_CI_AI ";
	$sql.= " LEFT JOIN ".$bd['softland'].".cwtccos ccosto ON np.CodiCC = ccosto.CodiCC  COLLATE SQL_Latin1_General_CP1_CI_AI ";
	$sql.= " LEFT JOIN ".$bd['softland'].".cwtauxi auxiliar  ON np.CodAux = auxiliar.CodAux  COLLATE SQL_Latin1_General_CP1_CI_AI ";
	$sql.= " WHERE np.NVnumero = '".$notaPedido."' ";
		//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = null;
	if (sqlsrv_num_rows($rec) == 0) { $salida = 'SIN_DATOS'; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }
	return $salida;
}
function listasNotasDeVentasDetallePopUp($id)
	{
	include('../includes/conexion.php');
	$dia = date('w');
	
	//$sql =" SELECT CodProd, DetProd, nvFecCompr, nvCant, nvPrecio, nvSubTotal, CodUMed FROM ".$bd['dsparam'].".[DS_NotasVentaDetalle] ";
	//$sql.=" WHERE NVNumero = '".$id."' ";
	$sql = " SELECT detalle.CodProd, detalle.DetProd, detalle.nvFecCompr, detalle.nvCant, detalle.nvPrecio, detalle.nvSubTotal, detalle.CodUMed, encabezado.nvFeEnt ";
	$sql.=" FROM ".$bd['dsparam'].".[DS_NotasVentaDetalle] detalle ";
	$sql.=" LEFT JOIN ".$bd['dsparam'].".[DS_NotasVenta] encabezado ON encabezado.nvNumero = detalle.nvNumero ";
	$sql.=" WHERE detalle.NVNumero = '".$id."'  ";
	//echo $sql;
	$checked = array(0 => 'checked="checked"', 1 => '');
	//$mostrar_todos = 'SI';
	$salida = null;	
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);	
		
	if($num_rows > 0)
		{
		$salida .= '
		<div class="col-md-12">
		<table class="registros table table-hover " id="dataTablePopUp"><thead>
		<tr>
		<td>ID</td>
			<th nowrap="nowrap">Codigo Producto</th>
			<th>Detalle Producto</th>
			<th>Fecha Emisi&oacute;n</th>
			<th>Fecha Entrega</th>
			<th>Cantidad</th>
			<th>U Medida</th>
			<th>Precio</th>
			<th>Sub Total</th>
		</tr>
		</thead><tbody>';
		$n = 1;
		
			while( $row = sqlsrv_fetch_array( $rec, SQLSRV_FETCH_ASSOC) ) 
			{
			
                $salida .= '
                <tr id="tr_'.$n.'">
					<td>'.$n.'</td>
				    <td>'.$row['CodProd'].'</td>
				    <td nowrap="nowrap">'.$row['DetProd'].'</td>
					<td nowrap="nowrap">'.date_format($row['nvFecCompr'], 'd/m/Y').'</td> 
					<td nowrap="nowrap">'.date_format($row['nvFeEnt'], 'd/m/Y').'</td> 
					<td nowrap="nowrap" class="ta-r">'.substr(formatoNumero($row['nvCant']),0,-1).'</td>
					<td nowrap="nowrap" class="ta-r">'.$row['CodUMed'].'</td>
					<td nowrap="nowrap" class="ta-r">'.substr(formato_precio($row['nvPrecio']),0,-1).'</td>
				    <td class="text_align_right padding_right_10"><strong>'.substr(formato_precio($row['nvSubTotal']),0,-1).'</strong>
					</td>
                </tr>';
				$total += $row['nvSubTotal'];
				$n++;
                
			}
		$salida .= '
		</tbody>
		<tbody><tr>
		<td colspan="8" align="right"><strong>Total</strong></td>
		<td class="text_align_right padding_right_10"><strong>'.substr(formato_precio($total),0,-1).'</td>
		</tr></tbody></table></div>';
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>No se han encontrado elementos en esta secci&oacute;n</p></div>'; }
	return $salida;
	//echo $sql;
	
	}
	
function obtenerDatosCondVenta($codAux)
{
	include('includes/conexion.php');
	$sql =" SELECT a.CodAux, a.ConVta, b.CveDes FROM ".$bd['softland'].".cwtcvcl a ";
	$sql.="  LEFT JOIN ".$bd['softland'].".cwtconv b ON a.ConVta = b.CveCod ";
	$sql.=" WHERE a.CodAux ='".$codAux."' ";
	//echo $sql;
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	//$rec = sqlsrv_query( $conn, $sql );
	
	$salida = null;
	//echo sqlsrv_num_rows($rec);
	if (sqlsrv_num_rows($rec) == 0) { $salida = ''; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }
	return $salida;
}	

function obtenerDatosRelacion($seleccion)
{
include('includes/conexion.php');
$sql = " SELECT rela.CodVen,ven.VenDes, rela.CodBode, bode.desbode, rela.CodGrupo, grupo.DesGrupo FROM ".$bd['dsparam'].".[DS_PARAMRELA] rela ";
$sql.= " LEFT JOIN ".$bd['softland'].".cwtvend ven ON ven.VenCod = rela.CodVen collate Modern_Spanish_CI_AS ";
$sql.= " LEFT JOIN ".$bd['softland'].".iw_tbode bode ON bode.CodBode = rela.CodBode collate Modern_Spanish_CI_AS ";
$sql.= " LEFT JOIN ".$bd['softland'].".iw_tgrupo grupo ON grupo.CodGrupo = rela.CodGrupo collate Modern_Spanish_CI_AS ";
$sql.= " WHERE rela.CodVen = '".$seleccion."' ";
//echo $sql;

	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$salida = null;
	if (sqlsrv_num_rows($rec) == 0) { $salida = ''; }
	if (sqlsrv_num_rows($rec) > 0)  { $salida = sqlsrv_fetch_array($rec); }
	return $salida;
}	

?>