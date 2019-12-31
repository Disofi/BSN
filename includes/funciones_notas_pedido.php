<?php
session_start();
function notasPedidoListar()
	{
	include('includes/conexion.php');
	include('includes/funciones_clientes.php');
	$sql = "SELECT np.NVNumero AS NroNota, np.VenCod AS CodVendedor, vnd.VenDes AS NomVendedor, np.CodAux AS CodCliente, cli.NomAux AS NomCliente, 
			cli.RutAux AS RutCliente, np.nvFem AS FechaPedido, np.nvEstado AS Estado, np.nvMonto, np.CodVenWeb, usr.Nombres, np.nvObser As Observacion 
			FROM ".$bd['dsparam'].".[DS_NotasVenta] AS np
			LEFT JOIN ".$bd['softland'].".[cwtvend] AS vnd ON np.VenCod=vnd.VenCod COLLATE Modern_Spanish_CI_AS 
			LEFT JOIN ".$bd['softland'].".[cwtauxi] AS cli ON np.CodAux=cli.CodAux COLLATE Modern_Spanish_CI_AS 
			LEFT JOIN ".$bd['dsparam'].".[DS_Usuarios] as usr ON usr.CodUsuario=np.CodVenWeb COLLATE Modern_Spanish_CI_AS 
			WHERE nvEstado='P'";
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);
	$salida = '<form id="form_pedidos_listar" method="post" action="#">';
	
	if($num_rows > 0)
		{
		$salida .= '<table class="registros table table-hover" id="dataTable"><thead>
		<tr>
			<th nowrap="nowrap">N&ordm; Nota</th>
			<th nowrap="nowrap">&nbsp;</th>
			<th nowrap="nowrap">Observaci&oacute;n</th>
			<th nowrap="nowrap">Rut Cliente</th>
			<th nowrap="nowrap">Cliente</th>
			<th nowrap="nowrap">Fecha Pedido</th>
			<th nowrap="nowrap">Monto</th>
			<th nowrap="nowrap">Cr&eacute;dito Disponible</th>
			<th nowrap="nowrap">Estado</th>
			<th nowrap="nowrap">Ejecutivo Ventas</th>
			<th nowrap="nowrap">Vendedor</th>
		</tr>
		</thead>
		<tbody>';	
		$n = 0;
		while($row = sqlsrv_fetch_array($rec))
			{
			$codCliente = $row['CodCliente'];
			// Verificar si el cliente tiene credito disponible...
			$saldoFacturas = clientesObtenerMontoFacturasPendientes($codCliente);
			$credito = clientesObtenerCredito($codCliente);
			// En caso de que el valor de $saldoFacturas sea negativo, entonces convertir a cero para poder operar...
			if($saldoFacturas < 0){ $saldoFacturas = 0; }
			$creditoCliente = ($credito - $saldoFacturas);
			
			$csscred = 'dark_grey';		
			
			if ($row['nvMonto'] > $creditoCliente) 
				{
				$csscred = 'red';
				$style = 'style="background-color:yellow;"';
				}
			else { $csscred = 'dark_grey'; $style="";}

			if($row['Estado'] == 'P')
				$estado = array('nombre' => 'Pendiente', 'css' => 'est_pendiente');
			if($row['Estado'] == 'A')
				$estado = array('nombre' => 'Aprobado', 'css' => 'est_aprobado');
		
			$salida .= '<tr id="tr_'.$n.'" '.$style.'>
			<td>'.$row['NroNota'].'</td>
			<td>
			<div class="acciones float_center">
				<a href="notas-pedido-detalle.php?np='.$row['NroNota'].'" data-fancybox-width="1050" data-fancybox-height="500" class="zoom fancyboxBasic fancybox.iframe icon" alt="Ver detalle nota de pedido N&ordm; '.$row['NroNota'].'" title="Ver detalle nota de pedido N&ordm; '.$row['NroNota'].'">'.$row['NroNota'].'</a>
				<input type="checkbox" id="'.$row['NroNota'].'" name="checkbox" value="'.$row['NroNota'].'" /><br>
				</div>
			</td>
			<td nowrap="nowrap">'.mb_strtoupper(strtolower($row['Observacion'])).'</td>
			<td nowrap="nowrap">'.$row['RutCliente'].'</td>
			<td nowrap="nowrap">'.mb_strtoupper(strtolower($row['NomCliente'])).'</td>
			<td nowrap="nowrap">'.formatoFechaLeer($row['FechaPedido'], '/').'</td>
			<td class="text_align_right padding_right_10"><strong class="'.$csscred.'">'.substr(formato_precio($row['nvMonto']),0,-1).'</strong></td>
			<td class="text_align_right padding_right_10"><strong class="'.$csscred.'">'.substr(formato_precio($creditoCliente),0,-1).'</strong></td>
			<td nowrap="nowrap"><span class="'.$estado['css'].' icon_text">'.$estado['nombre'].'</span></td>
			<td nowrap="nowrap">'.mb_strtoupper(strtolower($row['NomVendedor'])).'</td>
			<td nowrap="nowrap">'.mb_strtoupper(strtolower($row['Nombres'])).'</td>
			</tr>';
			$n = $n + 1;
			}
		$salida .= '</tbody></table>
		<div class="aprovar"><input type="button" name="aprov" id="aprov" value="Aprobar" onClick="contar();" /></div></form>';
		}
	if($num_rows == 0) { $salida .= '<div class="message_info"><p>Actualmente no existen notas de pedido por aprobar</p></div></form>'; }
	return $salida;
	}

function VerNotaPedido($np)
	{
	include('includes/conexion.php');
	
	/*$sel = "SELECT np.nvFem, np.CodAux, np.VenCod, vnd.VenDes, np.nvSubTotal, np.NvMonto, np.NvNetoExento, np.nvNetoAfecto, np.nvNetoExento, 
			np.FechaHoraCreacion, np.TotalBoleta, np.nvObser, cli.NomAux, cli.RutAux, cli.DirAux, cli.DirNum, cr.Descripcion, cc.CiuDes, cm.ComDes
			,np.NumOC
			FROM ".$bd['dsparam'].".[DS_NotasVenta] AS np 
			LEFT JOIN ".$bd['softland'].".[cwtvend] AS vnd ON np.VenCod=vnd.VenCod COLLATE Modern_Spanish_CI_AS 
			LEFT JOIN ".$bd['softland'].".[cwtauxi] AS cli ON np.CodAux=cli.CodAux COLLATE Modern_Spanish_CI_AS
			LEFT JOIN ".$bd['softland'].".[cwtcomu] AS cm ON cli.ComAux=cm.ComCod 
			LEFT JOIN ".$bd['softland'].".[cwtciud] AS cc ON cli.CiuAux=cc.CiuCod
			LEFT JOIN ".$bd['softland'].".[cwtregion] AS cr ON cli.Region=cr.id_Region
			WHERE np.NVNumero='$np'";*/
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$row = sqlsrv_fetch_array($res);
	
	print '
	<h3 class="titulo">Nota de Pedido</h3>
	<div class="row">
		<div class="col1">N&ordm; Nota Pedido</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.$np.'</strong></div>
	</div>
	<div class="row">
		<div class="col1">Cliente</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.$row['NomAux'].'</strong></div>
	</div>
	<div class="row">
		<div class="col1">Rut Cliente</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.$row['RutAux'].'</strong></div>
	</div>
	<div class="row">
		<div class="col1">Direcci&oacute;n</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.$row['DirAux'].' '.$row['ComDes'].' '.$row['CiuDes'].' '.$row['Descripcion'].'</strong></div>
	</div>
	<div class="row">
		<div class="col1">Ejecutivo de Ventas</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.$row['VenDes'].'</strong></div>
	</div>
	<div class="row">
		<div class="col1">Fecha Pedido</div>
		<div class="col2">:</div>
		<div class="col3"><strong>'.date_format($row['nvFem'], 'd-m-Y').'</strong></div>
	</div>
	<div class="row">
		 <div class="col1">N&uacute;mero O/C</div>
		 <div class="col2">:</div>
		 <div class="col3"><strong>'.$row['NumOC'].'</strong></div>
	</div>
		
	<div class="row">
		<div class="col4">Observaciones</div>
		<div class="col5">:</div>
		<div class="col6"><strong><textarea name="obser" id="obser">'.$row['nvObser'].'</textarea></strong></div>
	</div>
	
	<div class="clearing">&nbsp;</div>
	<div class="tabla">
		<div class="head-fila">
			<div class="colu0">Fila</div>
			<div class="colu1">C&oacute;digo</div>
			<div class="colu2">Descripci&oacute;n</div>
			<div class="colu3">Cantidad</div>
			<div class="colu4">Valor Unitario</div>
			<div class="colu5">Total</div>
		</div>
	';

	$sel1 = "SELECT * FROM ".$bd['dsparam'].".[DS_NotasVentaDetalle] WHERE NVNumero='$np' ORDER BY nvLinea";
	///$res1 = sqlsrv_query($conn, $sel1, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num1 = sqlsrv_num_rows($res1);
	
	$acum = 0;
	$prods = 0;
	
	while ($row1 = sqlsrv_fetch_array($res1))
		{
		$prods = $prods + $row1['nvCant'];
		$acum = $acum + $row1['nvSubTotal'];
		print '
		<div class="fila">
			<div class="colu0">'.$row1['nvLinea'].'</div>
			<div class="colu1">'.$row1['CodProd'].'</div>
			<div class="colu2">'.$row1['DetProd'].'</div>
			<div class="colu3">'.$row1['nvCant'].'</div>
			<div class="colu4">'.number_format($row1['nvPrecio'],2,',','.').'</div>
			<div class="colu5">'.number_format($row1['nvSubTotal'],0,',','.').'</div>
		</div>
		';
		}
	print '
		<div class="foot-fila">
			<div class="colu0"><b>TOTAL</b></div>
			<div class="colu1">&nbsp;</div>
			<div class="colu2">&nbsp;</div>
			<div class="colu3">'.number_format($prods,0,',','.').'</div>
			<div class="colu4">&nbsp;</div>
			<div class="colu5">'.number_format($acum,0,',','.').'</div>
		</div>
	
	</div>';
	}

function recepcionSeleccionar($id, $addConds)
	{
	// Esta funcion retorna TODO lo de una recepcion, incluyendo detalle de prodcutos y su conteo...
	include('includes/conexion.php');
	$sql = "SELECT TOP 1 rp.Id,rp.Correlativo,rp.Cliente,cl.NomAux,cl.RutAux,rp.Obra,ob.DesBode,rp.Guias,rp.FecRec,rp.Estado,rp.FecPat,
			rp.Conductor,rp.RutCon,rp.PatCam,rp.PatRam FROM ".$bd['dsc'].".[Recepcion] as rp
			LEFT JOIN ".$bd['softland'].".[cwtauxi] cl ON rp.Cliente=cl.CodAux COLLATE Modern_Spanish_CI_AS  
			LEFT JOIN ".$bd['softland'].".[iw_tbode] ob ON rp.Obra=ob.CodBode COLLATE Modern_Spanish_CI_AS
			WHERE rp.Id='".$id."'".$addConds;
	///$rec = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
	$rec = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num_rows = sqlsrv_num_rows($rec);

	if($num_rows == 0){ $salida = 'NO_EXISTE';}
	else
		{
		$row = sqlsrv_fetch_array($rec);
		$productos = array();
		$sql2 = "SELECT rpd.Fila, rpd.Codprod, pro.DesProd, rpd.Cantprod, rpd.CantGuia FROM ".$bd['dsc'].".[Recepdetalleprod] AS rpd
				LEFT JOIN ".$bd['softland'].".[iw_tprod] pro ON rpd.Codprod=pro.CodProd COLLATE Modern_Spanish_CI_AS WHERE rpd.Id=".$id . " ORDER BY rpd.Fila ASC";
		///$rec2 = sqlsrv_query($conn, $sql2, array(), array('Scrollable' => 'buffered'));
		$rec2 = sqlsrv_query( $conn, $sql2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		if(sqlsrv_num_rows($rec2) > 0)
			{
			$p = 0;
			while($row2 = sqlsrv_fetch_array($rec2))
				{
				$productos['productos'][$p] = array(
					'prod_fila' => $row2['Fila'],
					'prod_codigo' => $row2['Codprod'],
					'prod_descripcion' => $row2['DesProd'],
					'prod_cantidad' => $row2['Cantprod'],
					'prod_cantidad_guia' => $row2['CantGuia']
					);
				$sql3 = "SELECT * FROM ".$bd['dsc'].".[detalleInspeccionVisual] WHERE id=".$id." AND codProducto='".$row2['Codprod']."'";
				///$rec3 = sqlsrv_query($conn, $sql3, array(), array('Scrollable' => 'buffered'));
				$rec3 = sqlsrv_query( $conn, $sql3 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				if(sqlsrv_num_rows($rec3) > 0)
					{
					$c = 0;
					while($row3 = sqlsrv_fetch_array($rec3))
						{
						$productos['productos'][$p]['prod_conteo'][$c] = array(
							'cont_fila' => $row3['numFila'],
							'cont_cantidad' => $row3['numProductos'],
							'cont_prc_dano' => $row3['porcentajeDano'],
							'cont_taller' => $row3['taller'],
							'cont_suc_cliente' => $row3['sucioCliente'],
							'cont_suc_normal' => $row3['sucioNormal'],
							'cont_baja' => $row3['baja'],
							'cont_operativo' => $row3['operativo'],
							'cont_fecha' => $row3['fecha']
							);
						$c = $c + 1;
						}
					}
				$p = $p + 1;
				}
			}
		$salida = array_merge($row, $productos);
		}
	return $salida;
	}

function InformeComisiones($fecdes, $fechas)
	{
	include('includes/conexion.php');
	require('includes/PHPExcel.php');
	
	/* Facturas */
	$sel0 = "SELECT gs.tipo, gs.Folio, gs.Fecha, cx.nomaux, vnd.VenDes, gs.CodMoneda, gs.Total, gm.DetProd, gm.CantFacturada, 
			tp.PrecioVta, gm.preunimb, gm.totlinea 
			FROM ".$bd['softland'].".[iw_gsaen] AS gs 
			LEFT JOIN ".$bd['softland'].".[iw_gmovi] AS gm ON gs.NroInt=gm.NroInt and gs.Tipo=gm.Tipo 
			LEFT JOIN ".$bd['softland'].".[iw_tprod] AS tp ON gm.Codprod=tp.CodProd 
			LEFT JOIN ".$bd['softland'].".[cwtauxi] AS cx ON gs.Codaux= cx.Codaux 
			LEFT JOIN ".$bd['softland'].".[cwtvend] AS vnd ON gs.CodVendedor=vnd.VenCod COLLATE Modern_Spanish_CI_AS 
			WHERE gs.tipo in ('F','D','N','B') and Estado = 'V' and gs.Fecha between convert(datetime, '".$fecdes."',103) 
			and convert(datetime, '".$fechas."', 103) and enmantencion=0";
	///$res0 = sqlsrv_query($conn, $sel0, array(), array('Scrollable' => 'buffered'));
	$res0 = sqlsrv_query( $conn, $sel0 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	$num0 = sqlsrv_num_rows($res0);

	if ($num0 == 0) 
		{ 
		print "No hay datos que mostrar"; 
		exit; 
		}
	else
		{
		$hoy = date('dmY-His');
		$fname = "informes/InformeComisiones-".$hoy.".xls";
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Disofi 2015")
							 ->setLastModifiedBy("Disofi 2015")
							 ->setTitle("Informe de Comisiones")
							 ->setSubject("Informe de Comisiones")
							 ->setDescription("Informe de Comisiones ")
							 ->setKeywords("Office PHPExcel dsparam DISOFI INFORME COMISION COMISIONES")
							 ->setCategory("Informe de Comisiones");
		$i = 1;    
		while ($row0 = sqlsrv_fetch_array($res0)) 
			{
			if ($i == 1 )
				{
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', "Tipo")
							->setCellValue('B1', "Folio")
							->setCellValue('C1', "Fecha")
							->setCellValue('D1', "Cliente")
							->setCellValue('E1', "Vendedor")
							->setCellValue('F1', "Moneda")
							->setCellValue('G1', "Total")
							->setCellValue('H1', "Producto")
							->setCellValue('I1', "Cantidad")
							->setCellValue('J1', "Precio Lista")
							->setCellValue('K1', "Precio Venta")
							->setCellValue('L1', "Total Línea")
							->setCellValue('M1', "Descuento")
							->setCellValue('N1', "% Comisión")
							->setCellValue('O1', "Valor Comisión");
				}
			$j = $i+1;
			$tipo  = $row0['tipo'];
			$folio = $row0['Folio'];
			$fecha = date_format($row0['Fecha'], 'd/m/Y');
			$clien = $row0['nomaux']; 
			$vende = $row0['VenDes'];
			$moned = $row0['CodMoneda'];
			$total = $row0['Total'];
			$produ = $row0['DetProd'];
			$canti = $row0['CantFacturada'];
			$plist = $row0['PrecioVta'];
			$pvent = $row0['preunimb'];
			$totln = $row0['totlinea'];
			$impor = $row0['Total'];

			if ($plist==0) { $plist = ($impor/$canti)+50; }
			//este descuento es para desplegarlo en pantalla con formato porcentaje
			$descu = ((($pvent/$plist)-1) *100);
			//este valor es para realizar el calculo de en que tramo cae el descuento, que es la division entre precio lista y precio de venta	
			$descuPor = (($pvent/$plist)-1) ;

			//print $descuPor;
			$sel2 = "SELECT Comision FROM ".$bd['dsparam'].".[parametros] WHERE Desde>='".$descuPor."' and Hasta<='".$descuPor."'";
			///$res2 = sqlsrv_query($conn, $sel2);	
			$res2 = sqlsrv_query( $conn, $sel2 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$row2 = sqlsrv_fetch_array($res2);
			$comis = $row2['Comision'];
			$valco = ($totln*$comis)/100;

			//$valco = ($impor*$comis)/100;

			// $canti = number_format($canti,0,'.',',');
			// $plist = number_format($plist,2,'.',',');
			// $impor = number_format($impor,0,'.',',');
			// $descu = number_format($descu,4,'.',',');
			// $comis = number_format($comis,2,'.',',');
			// $valco = number_format($valco,2,'.',',');
			// $pvent = number_format($pvent,2,'.',',');
			// $totln = number_format($totln,0,'.',',');
		
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$j, $tipo)
						->setCellValue('B'.$j, $folio)
						->setCellValue('C'.$j, $fecha)
						->setCellValue('D'.$j, $clien)
						->setCellValue('E'.$j, $vende)
						->setCellValue('F'.$j, $moned)
						->setCellValue('G'.$j, $total)
						->setCellValue('H'.$j, $produ)
						->setCellValue('I'.$j, $canti)
						->setCellValue('J'.$j, $plist)
						->setCellValue('K'.$j, $pvent)
						->setCellValue('L'.$j, $totln)
						->setCellValue('M'.$j, $descu)
						->setCellValue('N'.$j, $comis)
						->setCellValue('O'.$j, $valco);
			$i++;
			}
		require_once ('includes/PHPExcel/IOFactory.php');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($fname);
		print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';
		}
	}

function NotasAprobar($np)
	{
	include('includes/conexion.php');	
	$sep = substr_count($np,',') + 1;
	$x   = explode(",",$np);
	for ($i=0;$i<$sep;$i++)
		{
		$upd = "UPDATE ".$bd['dsparam'].".[DS_NotasVenta] SET nvEstado='A' where nvNumero='".$x[$i]."'";
		///$res = sqlsrv_query($conn, $upd);
		$res = sqlsrv_query( $conn, $upd , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
		$proc = "DECLARE @RC int EXECUTE @RC = ".$bd['dsparam'].".[insertNotaVenta] '".$x[$i]."'";
		///$exec = sqlsrv_query($conn, $proc);
		$exec = sqlsrv_query( $conn, $proc , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		$selx = "SELECT np.nvFem, np.CodAux, np.VenCod, vnd.VenDes, np.nvSubTotal, np.NvMonto, np.NvNetoExento, np.nvNetoAfecto, np.nvNetoExento, np.CotNum,
				np.FechaHoraCreacion, np.TotalBoleta, np.nvObser, cli.NomAux, cli.RutAux, cli.DirAux, cli.DirNum, cr.Descripcion, cc.CiuDes, cm.ComDes
				FROM ".$bd['dsparam'].".[DS_NotasVenta] AS np 
				LEFT JOIN ".$bd['softland'].".[cwtvend] AS vnd ON np.VenCod=vnd.VenCod COLLATE SQL_Latin1_General_CP1_CS_AS  
				LEFT JOIN ".$bd['softland'].".[cwtauxi] AS cli ON np.CodAux=cli.CodAux COLLATE SQL_Latin1_General_CP1_CS_AS 
				LEFT JOIN ".$bd['softland'].".[cwtcomu] AS cm ON cli.ComAux=cm.ComCod 
				LEFT JOIN ".$bd['softland'].".[cwtciud] AS cc ON cli.CiuAux=cc.CiuCod 
				LEFT JOIN ".$bd['softland'].".[cwtregion] AS cr ON cli.Region=cr.id_Region 
				WHERE np.NVNumero='".$x[$i]."' COLLATE SQL_Latin1_General_CP1_CS_AS ";
		///$resx = sqlsrv_query($conn, $selx, array(), array('Scrollable' => 'buffered'));
		$resx = sqlsrv_query( $conn, $selx , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$rowx = sqlsrv_fetch_array($resx);
				
		$msj = '
		<html>
		<head>
		<meta charset="utf-8">
		</head>
		<body>
		<pre>		
		<h1>Nota de Pedido</h1>
		<h3>
		N&ordm; Nota Pedido      : <strong>'.$rowx['CotNum'].'</strong>
		Cliente             : <strong>'.utf8_decode($rowx['NomAux']).'</strong>
		Rut Cliente         : <strong>'.utf8_decode($rowx['RutAux']).'</strong>
		Direcci&oacute;n           : <strong>'.utf8_decode($rowx['DirAux']).' '.utf8_decode($rowx['ComDes']).' '.utf8_decode($rowx['CiuDes']).' '.utf8_decode($rowx['Descripcion']).'</strong>
		Ejecutivo de Ventas : <strong>'.utf8_decode($rowx['VenDes']).'</strong>
		Fecha Pedido        : <strong>'.date_format($rowx['nvFem'], 'd-m-Y').'</strong>
		Observaciones       : <strong>'.utf8_decode($rowx['nvObser']).'</strong>
		</h3>
		<table style="width:100%; height:auto; clear:both; font-size:10px;" border:1>
		<tr style="width:100%; background-color:#FFF !important; color:#FFF; border:1px solid #ccc; height:15px; padding:5px;">
		<th style="width:20%; float:left; text-align:left">Fila</th>
		<th style="width:10%; float:left; text-align:left">C&oacute;digo</th>
		<th style="width:45%; float:left; text-align:left">Descripci&oacute;n</th>
		<th style="width:20%; float:left; text-align:right;">Cantidad</th>
		';
		$sel1 = "SELECT * FROM ".$bd['dsparam'].".[DS_NotasVentaDetalle] WHERE NVNumero='".$x[$i]."' ORDER BY nvLinea";
		///$res1 = sqlsrv_query($conn, $sel1, array(), array('Scrollable' => 'buffered'));
		$res1 = sqlsrv_query( $conn, $sel1 , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		$num1 = sqlsrv_num_rows($res1);
		$acum = 0;
		$prods = 0;
		while ($row1 = sqlsrv_fetch_array($res1))
			{
			$prods = $prods + $row1['nvCant'];
			$acum = $acum + $row1['nvSubTotal'];
			$msj .= '
			<tr style="width:100%; height:15px; padding:5px; border:1 border-bottom:1px solid #CCC;">
			<td style="width:20%; float:left; text-align:left">'.$row1['nvLinea'].'</td>
			<td style="width:10%; float:left; text-align:left">'.$row1['CodProd'].'</td>
			<td style="width:45%; float:left; text-align:left">'.utf8_decode($row1['DetProd']).'</td>
			<td style="width:20%; float:left; text-align:right;">'.$row1['nvCant'].'</td>
			</tr>';
			}
		$msj .= '
		<tr style="width:100%; background-color:#FFF !important; color:#FFF; border:1px solid #ccc; height:15px; padding:5px;">
		<td style="width:20%; float:left;"><b>TOTAL</b></td>
		<td style="width:10%; float:left;">&nbsp;</td>
		<td style="width:45%; float:left;">&nbsp;</td>
		<td style="width:20%; float:left; text-align:right;">'.number_format($prods,0,',','.').'</td>
		</tr></table></body></html>';
		
		/* ARCHIVOS DE CLASES ENVIO MAIL*/
		include_once('includes/class.phpmailer.php');
		include_once('includes/class.smtp.php');
		/* CABECERA DEL MAIL */
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->FromName = "Alma Brands - NOTAS DE VENTA";
		$mail->Username = 'renato.soto@gmail.com';
		$mail->Password = 'ANDY1984';
		$mail->AddAddress('renato.soto@gmail.com','Notas de Venta Alma Brands');
		$mail->AddCC('proyectos@disofi.cl','Proyectos Disofi');
		$mail->AddCC('proyectos@disofi.cl','Proyectos Disofi');
		$mail->Subject = "Aprobaciones de nota de ventas";
		$mail->Body = $msj; 
		$mail->MsgHTML($msj);
		$mail->Send();
		print "Se Aprobaron Notas de Pedido Nº:".$x[$i]." Nº Softland: ".$rowx['CotNum']."<br />";
		}
	print "Será redireccionado a la pantalla de aprobación <META HTTP-EQUIV='Refresh' CONTENT='10; URL=index.php?mod=notas-pedido'> ";
	}	
	
if($_REQUEST['condicion'] == 'execute')
{
include 'conexion.php';
$fecha_hoy = date("Y/m/d");	
$codigosArray = explode(',', $_REQUEST['salidaCodigo']);
$subTotalArray = explode(',', $_REQUEST['salidaSubTotal']);
$desc = str_replace('%26', '&', $_REQUEST['salidaDescripcionProd']);
$descripcionPrdArray = explode(',', $desc);
$unidadMedidaArray = explode(',', $_REQUEST['salidaUnidadMedida']);
$cantidadArray = explode(',', $_REQUEST['salidaCantidad']);
$fleteArray = explode(',', $_REQUEST['salidaFletes']);
$precioUnitarioArray = explode(',', $_REQUEST['salidaPrecioUnitario']);


	$sel = " SELECT isnull(max([nvnumero])+1,1) as indice FROM [DSPARAM].[dbo].[DS_NotasVenta] ";
	//echo $sel;
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	        while ($row = sqlsrv_fetch_array($res))
            {
				$indice = $row['indice'];
			}
$totalFlete = 0;
	for($f=0; $f < count($fleteArray); $f++)
	{
		$totalFlete += $fleteArray[$f];		
	}
	

	$subTotalSeparado = explode(',', $_REQUEST['salidaSubTotal']);
	//echo count($subTotalSeparado);
	
	$precioImpuesto = 0;
	for($a = 0; $a < count($subTotalSeparado); $a++)
	{
		$query = " SELECT iwti.codprod, iwti.codimpto,iwtv.valpctini, ".$subTotalSeparado[$a]." as afectoimpto,((iwtv.valpctini * ".$subTotalSeparado[$a].")/100) as impto ";
		$query.= " FROM ".$bd['softland'].".[iw_timprod] iwti ";
		$query.= " LEFT JOIN ".$bd['softland'].".[iw_timpval] iwtv on iwti.codimpto = iwtv.codimpto ";
		$query.= " WHERE iwti.codprod = '".$codigosArray[$a]."' ";
		//$query.= " AND convert(datetime,'".$fecha_hoy."',103) >iwtv.fecinivig AND convert(datetime,'".$fecha_hoy."',103) < iwtv.fecfinvig ";
		$query.= " AND '".$fecha_hoy."' > iwtv.fecinivig AND '".$fecha_hoy."' < iwtv.fecfinvig ";
			//$res = sqlsrv_query($conn, $query, array(), array('Scrollable' => 'buffered'));
			$res = sqlsrv_query( $conn, $query , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$num = sqlsrv_num_rows($res);
			while($row = sqlsrv_fetch_array($res))
				{
					$precioImpuesto += $row['impto'];
				}
	}
	$totalVenta = 0;
	
for($c =0; $c < count($subTotalSeparado) ; $c++)
{
		/*
		$sp = "EXECUTE ".$bd['dsparam'].".[insertarNv] '".$indice."',  '".$fecha_hoy."',  '".$codigosArray[$c]."',  '".$cantidadArray[$c]."',  '".$precioUnitarioArray[$c]."',  
															'',  '',  '',  '', '', '', 
															'".$descripcionPrdArray[$c]."',  '".$unidadMedidaArray[$c]."',  'A',  '".$_REQUEST['codigoAux']."', 
															'".$_REQUEST['cod_vendedor']."',  '".$_REQUEST['codlis']."',  'softland',  '".$_REQUEST['observacion']."',  
															'".$_REQUEST['CondVenta']."', '".$_REQUEST['contacto']."',  '".$_REQUEST['CondVenta']."',  
															'".$_REQUEST['NumOC']."', '".$_REQUEST['ccosto']."',  '".$totalFlete."',  '".$_REQUEST['embalaje']."', 
															'".$_REQUEST['codcan']."'";
		*/
		$sp = "EXECUTE ".$bd['dsparam'].".[insertarNv] '".$indice."',  '".$fecha_hoy."',  '".$codigosArray[$c]."',  '".$cantidadArray[$c]."',  '".$precioUnitarioArray[$c]."',  
															'',  '',  '',  '', '', '', 
															'".$descripcionPrdArray[$c]."',  '".$unidadMedidaArray[$c]."',  'A',  '".$_REQUEST['codigoAux']."', 
															'".$_REQUEST['cod_vendedor']."',  '".$_REQUEST['codlis']."',  'softland',  '".$_REQUEST['observacion']."',  
															'".$_REQUEST['CondVenta']."', '".$_REQUEST['contacto']."',  '".$_REQUEST['CondVenta']."',  
															'".$_REQUEST['NumOC']."', '".$_REQUEST['ccosto']."',  '0',  '0', 
															'0'";
															
										$totalVenta += $subTotalArray[$c];
										
			
		$exec = sqlsrv_query($conn, $sp);

}

$nvMonto = $totalVenta + $precioImpuesto;

 $query_update = " UPDATE ".$bd['dsparam'].".DS_NotasVenta SET nvMonto = '".$nvMonto."', TotalBoleta = '".$nvMonto."'  WHERE nvNumero = '".$indice."' ";
 
 $queryUpdate = sqlsrv_query( $conn, $query_update , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 
	$spFinal = "exec ".$bd['dsparam'].".[insertaNVSoftland]  ".$indice." ";
		$exec = sqlsrv_query($conn, $spFinal);

	if($exec)
	{
		echo $indice;
	}
	else
	{
		echo "ERROR";		
	}

}	
	
if($_REQUEST['condicion'] == 'rangoDescuento'){
	include 'conexion.php';
	$cantidad = $_REQUEST['cantidad'];
	$codlis = $_REQUEST['codlis'];
	
	$sel =" SELECT * FROM ".$bd['dsparam'].".[DS_DXVolumenRela] a ";
	$sel.="	LEFT JOIN 	  ".$bd['dsparam'].".[DS_DXVolumenD] b ON a.coddescto =  b.coddescto ";
	$sel.=" WHERE a.codlista = '".$codlis."'";
	
	///$res = sqlsrv_query($conn, $sel, array(), array('Scrollable' => 'buffered'));
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		while ($row = sqlsrv_fetch_array($res))
		{
			if($cantidad >= $row['Desde'] && $cantidad <= $row['Hasta'])
			{
				$descuento = $row['Descto'];
				break;
			}
			else
			{
				$descuento = 0;
			}
		}
	
	print $descuento;
	//echo $sel;
}	

if($_REQUEST['condicion'] == 'ejecutarSP'){
	include 'conexion.php';
	include 'funciones.php';
	//function formatoFechaGuardar($fecha, $separador)
	$hoy = date("Y-m-d");	
	//$hoy_1 = date('Y-m-d', strtotime($fecha_hoy) + 86400);
	//$hoy_1 = date('Y-m-d', strtotime("$fecha_hoy + 1 day"));
	//$hoy_1 = $_REQUEST['fecha_entrega'];
	//echo $hoy." : fecha sin procesar<br>";
	
	//$fecha_entrega = $_REQUEST['fecha_entrega'];
	$nuevafecha = formatoFechaGuardarPicker($_REQUEST['fecha_entrega'], '-');
	//$nuevafecha = $_REQUEST['fecha_entrega'];
	$fecha_hoy = formatoFechaGuardar($hoy, '-');
	//echo $nuevafecha."<-----<br>";
	
	//echo $nuevafecha." : fecha a utilizar<br>";
	$codigosArray = explode(',', $_REQUEST['salidaCodigo']);
	$cantidadArray = explode(',', $_REQUEST['salidaCantidad']);
	$precioUnitarioArray = explode(',', $_REQUEST['salidaPrecioUnitario']);
	$desc = str_replace('%26', '&', $_REQUEST['salidaDescripcionProd']);
	$descripcionPrdArray = explode(',', $desc);
	$unidadMedidaArray = explode(',', $_REQUEST['salidaUnidadMedida']);
	$totalFinal = $_REQUEST['totalFinal'];
	$totalImpuestos = $_REQUEST['totalImpuestos'];
	//$subTotalSeparado = explode(',', $_REQUEST['salidaSubTotal']);
	$subTotalArray = explode(',', $_REQUEST['salidaSubTotal']);
	
	$sel = " SELECT isnull(max([nvnumero])+1,1) as indice FROM ".$bd['dsparam'].".[DS_NotasVenta] ";
	$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	        while ($row = sqlsrv_fetch_array($res))
            {
				$indice = $row['indice'];
			}
		//echo count($subTotalArray)." : contador<br>";
for($a = 0; $a < count($subTotalArray); $a++)
	{
		$query = " SELECT iwti.codprod, iwti.codimpto,iwtv.valpctini, ".$subTotalArray[$a]." as afectoimpto,((iwtv.valpctini * ".$subTotalArray[$a].")/100) as impto ";
		$query.= " FROM ".$bd['softland'].".[iw_timprod] iwti ";
		$query.= " LEFT JOIN ".$bd['softland'].".[iw_timpval] iwtv on iwti.codimpto = iwtv.codimpto ";
		$query.= " WHERE iwti.codprod = '".$codigosArray[$a]."' ";
		$query.= " AND '".$fecha_hoy."' > iwtv.fecinivig AND '".$fecha_hoy."' < iwtv.fecfinvig ";
			$res = sqlsrv_query( $conn, $query , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$num = sqlsrv_num_rows($res);
			while($row = sqlsrv_fetch_array($res))
				{
					$precioImpuesto += $row['impto'];
					//echo $precioImpuesto."<br>";
				}
	}
	$totalVenta = 0;			

for($c =0; $c < count($unidadMedidaArray) ; $c++)
{
		$sp = "EXECUTE ".$bd['dsparam'].".[insertarNv] '".$indice."',  '".$fecha_hoy."',  '".$codigosArray[$c]."',  '".$cantidadArray[$c]."',  '".$precioUnitarioArray[$c]."', "; 
		$sp.= "													'',  '',  '',  '', '', '',  ";
		$sp.= "													'".$descripcionPrdArray[$c]."',  '".$unidadMedidaArray[$c]."',  'A',  '".$_REQUEST['codigoAux']."', "; 
		$sp.= "													'".$_REQUEST['vencod']."',  '".$_REQUEST['codlis']."',  '".$_SESSION['dsparam']['usuario']."',  '".$_REQUEST['observacion']."', ";  
		$sp.= "													'".$_REQUEST['CondVenta']."', '".$_REQUEST['contacto']."',  '".$_REQUEST['CondVenta']."',   "; 
		$sp.= " '".$_REQUEST['NumOC']."', '".$_REQUEST['ccosto']."',  '0',  '0',  ";
		$sp.= "'', '".$nuevafecha."'";
		
		//echo $sp."<br>";
		$totalVenta += $subTotalArray[$c];							
		$exec = sqlsrv_query($conn, $sp);
		/*if($precioUnitarioArray[$c] == 0 || $precioUnitarioArray[$c] == '')
		{
			$linkspon['codigoProducto'.$a.''] = $codigosArray[$c];
			$linkspon['descripcionProducto'.$a.''] = $descripcionPrdArray[$c];
			$linkspon['cantidad'.$a.''] = $cantidadArray[$c];
			$linkspon['precio'.$a.''] = $precioUnitarioArray[$c];				
			$a++;
		}*/

}
$nvMonto = $totalVenta + $precioImpuesto;
//echo $nvMonto."<--- NVMONTO <br>";

$query_update = " UPDATE ".$bd['dsparam'].".DS_NotasVenta SET nvMonto = '".$nvMonto."', TotalBoleta = '".$nvMonto."'  WHERE nvNumero = '".$indice."' ";
//echo $query_update." : Query Update <br>";
$queryUpdate = sqlsrv_query( $conn, $query_update , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));



	//CORREO$linkspon['registros'] = $a;
	$linkspon['indice'] = $indice;
	//$linkspon['indice'] = 0;
	$linkspon['registros_np'] = $c;
	//CORREOprint json_encode($linkspon);
	print json_encode($linkspon);
	//echo $c;
}	
	
if($_REQUEST['condicion'] == 'agruparSP')
{
	include 'conexion.php';
	include 'funciones.php';
	$date = date_create($_REQUEST['fecha']);
	$hoy = date("Y-m-d");	
	$hoy_1 = date('Y-m-d', strtotime($fecha_hoy) + 86400);
	$hoy_1 = date('Y-m-d', strtotime("$fecha_hoy + 1 day"));
	$fecha = date('d-m-Y');

	$nuevafecha = formatoFechaGuardar($hoy_1, '-');
	$fecha_hoy_sp = formatoFechaGuardar($hoy, '-');
	//echo $nuevafecha." -- ".$fecha_hoy_sp."<br>";
	$fechaRequest = formatoFechaGuardarRequest($_REQUEST['fecha'],'/');
	//2015-12-19 -- 2015-12-18
	//request : 31-12-2015
	//echo $_REQUEST['fecha']."fecha sin procesar <br>";
	//echo $fechaRequest."<-- fecha Request<br>";
	
	$resultado = "";
	$contadorSpSoftland = 0;
	$sql =" SELECT DISTINCT nve.nvFeEnt AS nvFeEnt, nve.codaux AS CodAux,nve.vencod as VenCod, sum(nvd.nvcant) AS nvcant , ";
	$sql.=" nvd.nvprecio AS NVPrecio,nvd.nvequiv,sum(nvd.nvtotlinea) AS nvtotlinea,cast(nvd.detprod AS varchar(500)) AS nombre, ";
	$sql.=" nvd.CodProd AS CodProd, nvd.CodUMed AS CodUMed, nve.CodLista AS CodLista, ";
	$sql.=" nve.Usuario AS Usuario, cast(nvObser AS Varchar(500)) AS nvObser, nve.CveCod AS CveCod,  ";
	$sql.=" nve.NumOC AS NumOC, nve.CodiCC AS CodiCC, nve.NomCon AS NomCon,  ";
	$sql.=" ROW_NUMBER()OVER(Partition By codaux, vencod Order By codaux, vencod,cast(nvd.detprod AS varchar(500)) ASC) AS Numero";
	$sql.=" FROM ".$bd['dsparam'].".ds_notasVenta nve  ";
	$sql.=" LEFT JOIN ".$bd['dsparam'].".DS_NotasVentaDetalle nvd on nve.nvnumero = nvd.nvnumero WHERE nvFeEnt = convert(datetime,'".$_REQUEST['fecha']."',103) AND nve.estadoNP = 'P' AND VenCod = '".$_REQUEST['vencod']."' ";
	$sql.=" GROUP BY nvFeEnt, CodAux,VenCod,CodProd,NVPrecio,nvd.nvequiv,cast(nvd.detprod as varchar(500)),CodUMed, CodLista,Usuario, cast(nvObser as Varchar(500)), cveCod, NumOC, CodiCC, NomCon ";
	//$sql.=" ORDER BY nombre  ";
	$sql.=" ORDER BY codaux, vencod,nombre, Numero ";
	//echo $sql."<br><br>";
	
	//<-----OBTENGO TODOS LOS DATOS AGRUPADOS
	$contador = 0;
	$flag = 0;
	$resb = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	        while ($row2 = sqlsrv_fetch_array($resb))
            {
				/*INICIO IMPUESTOS*/
				
				$query_impto = " SELECT iwti.codprod, iwti.codimpto,iwtv.valpctini, ".$row2['nvtotlinea']." as afectoimpto,((iwtv.valpctini * ".$row2['nvtotlinea'].")/100) as impto ";
				$query_impto.= " FROM ".$bd['softland'].".[iw_timprod] iwti ";
				$query_impto.= " LEFT JOIN ".$bd['softland'].".[iw_timpval] iwtv on iwti.codimpto = iwtv.codimpto ";
				$query_impto.= " WHERE iwti.codprod = '".$row2['CodProd']."' ";
				$query_impto.= " AND '".$hoy."' > iwtv.fecinivig AND '".$hoy."' < iwtv.fecfinvig ";
					//echo $query_impto."<br>";
					$res_impto = sqlsrv_query( $conn, $query_impto , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
					//$num = sqlsrv_num_rows($res);
					while($row_impto = sqlsrv_fetch_array($res_impto))
						{
							$precioImpuesto += $row_impto['impto'];
							//echo $precioImpuesto."<br>";
						}

				/*FIN IMPUESTOS*/
				if($row2['Numero'] == '1')
				{
					if($flag == 1)
					{
						//$nvmonto = $nvmonto - $montoLinea;
						//echo $nvmonto." : flag 1<br>"." indice ->".$indice."<br>";
						$nvmonto = $nvmonto + $precioImpuesto;
						$query_update = " UPDATE ".$bd['dsparam'].".DS_NotasVentaAG SET nvMonto = '".$nvmonto."', TotalBoleta = '".$nvmonto."'  WHERE nvNumero = '".$indice."' ";
						//echo $query_update."<br><br>A";
						//echo $nvmonto." + ".$precioImpuesto."<-----<br>";
						$queryUpdate = sqlsrv_query( $conn, $query_update , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
						$nvmonto = 0;
						$precioImpuesto = 0;
					}
					else
					{
						$flag = 1;
					}
					//$totalSub = 0;
					//$nvmonto = 0;
					//OBTENGO EL ULTIMO NUMERO, LO GUARDO EN VARIABLE INDICE
					$sel = " SELECT isnull(max([nvnumero])+1,1) as indice FROM ".$bd['dsparam'].".[DS_NotasVentaAG] ";
					//echo $sel."<br>";
					$res = sqlsrv_query( $conn, $sel , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
					while ($row = sqlsrv_fetch_array($res))
					{
						$indice = $row['indice'];
					}
				}
				$nvmonto += $row2['nvtotlinea'];
				$montoLinea = $row2['nvtotlinea'];
				//BUSCO SI EL ROW ID ES MULTIPLO DE 60, SI ES MULTIPLO DE 60 INCREMENTO EN 1 EL INDICE PARA GENERAR UNA NUEVA NOTA DE PEDIDO
					if( fmod($row2['Numero'], 61) == 0 )
					{
						//echo $nvmonto." : indice ++<br>"." indice ->".$indice."<br>";
						$nvmonto = $nvmonto - $montoLinea;
						$nvmonto = $nvmonto + $precioImpuesto;
						$query_update = " UPDATE ".$bd['dsparam'].".DS_NotasVentaAG SET nvMonto = '".$nvmonto."', TotalBoleta = '".$nvmonto."'  WHERE nvNumero = '".$indice."' ";
						$queryUpdate = sqlsrv_query( $conn, $query_update , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
						$nvmonto = 0;
						$precioImpuesto=0;
						$nvmonto = $nvmonto + $montoLinea;
						$indice++;
					}	
				$sp = "EXECUTE ".$bd['dsparam'].".[insertarNvAG] '".$indice."',  '".$fecha_hoy_sp."',  '".$row2['CodProd']."',  '".$row2['nvcant']."',  '".$row2['NVPrecio']."',  ";
				$sp.="  '',  '',  '',  '', '', '',  ";
				$sp.=" 	'".strtoupper($row2['nombre'])."',  '".$row2['CodUMed']."',  'A',  '".$row2['CodAux']."',  ";
				$sp.=" 	'".$row2['VenCod']."',  '".$row2['CodLista']."',  '".$row2['Usuario']."',  '".$row2['nvObser']."',   ";
				$sp.=" 	'".$row2['CveCod']."', '".$row2['NomCon']."',  '".$row2['CveCod']."',   ";
				$sp.=" 	'".$row2['NumOC']."', '".$row2['CodiCC']."',  '0',  '0',  ";
				$sp.=" 	'', '".$fechaRequest."', '".$indice."' ";		
					$exec = sqlsrv_query($conn, $sp);
				$queryNVNumero =" SELECT NVNumero FROM ".$bd['dsparam'].".[DS_NotasVenta] WHERE ";
				$queryNVNumero.=" CodAux = '".$row2['CodAux']."' AND VenCod = '".$row2['VenCod']."'  AND nvObser LIKE  '".$row2['nvObser']."' AND CveCod = '".$row2['CveCod']."' ";
				$queryNVNumero.=" AND CodLista = '".$row2['CodLista']."' AND Usuario = '".$row2['Usuario']."' AND EstadoNP = 'P'   ";
					$resc = sqlsrv_query( $conn, $queryNVNumero , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
						while ($row3 = sqlsrv_fetch_array($resc))
						{	
						$queryEstadoNP = "SELECT EstadoNP FROM ".$bd['dsparam'].".[DS_NotasVenta] WHERE NVNumero = '".$row3['NVNumero']."' ";
						//echo $queryEstadoNP."<br>";
						$resd = sqlsrv_query( $conn, $queryEstadoNP , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
							while ($row4 = sqlsrv_fetch_array($resd))
							{
								
								if($row4['EstadoNP'] == 'P' || $row4['EstadoNP'] == 'p')
								{
									$queryUpdateNP = "UPDATE ".$bd['dsparam'].".[DS_NotasVenta] SET EstadoNP = 'A' WHERE NVNumero = '".$row3['NVNumero']."' ";
									//echo $queryEstadoNP."<br>";
										sqlsrv_query( $conn, $queryUpdateNP , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
								}
							}
							$contador++;
						}
						$notas[$contador] = $indice;
						$contador++;
			}
			//if flag == 1
			//echo $nvmonto." : fin ciclo <br>"." indice ->".$indice."<br>";
			$nvmonto = $nvmonto + $precioImpuesto;
			$query_update = " UPDATE ".$bd['dsparam'].".DS_NotasVentaAG SET nvMonto = '".$nvmonto."', TotalBoleta = '".$nvmonto."'  WHERE nvNumero = '".$indice."' ";
				$queryUpdate = sqlsrv_query( $conn, $query_update , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));		
			$resultado = array_values(array_unique($notas));
			for($i = 0; $i < $contador; $i++)
			{
				if($resultado[$i] == '')
				{
					//echo "condicion NULA<br>";
				}
				else
				{
					$spSoftland = "EXECUTE ".$bd['dsparam'].".[insertaNVSoftland] '".$resultado[$i]."'";
					$execSoftland = sqlsrv_query($conn, $spSoftland);
					$contadorSpSoftland++;
					
					$sql_nvnumero = "SELECT NVNumero FROM ".$bd['softland'].".nw_nventa WHERE rutSolicitante = '".$resultado[$i]."' ";
					$resd = sqlsrv_query( $conn, $sql_nvnumero , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
							while ($row5 = sqlsrv_fetch_array($resd))
							{
								$notasDeVenta = $row5['NVNumero'].",".$notasDeVenta;
							}
				}
			}		
$resultNotasDeVenta = substr($notasDeVenta, 0, -1);
	$json['notasGeneradas'] = $contadorSpSoftland;
	$json['NVGeneradas'] = $resultNotasDeVenta;
	print json_encode($json);
}


if($_REQUEST['condicion'] == 'enviarCorreoPrecio')
{
	
	require_once('phpMailer/class.phpmailer.php');
	require_once('phpMailer/class.smtp.php');
	$fecha_hoy = date('d-m-Y H:i:s');	
	$cantidad = $_REQUEST['cantidad'];
	$codigoProducto = $_REQUEST['codigoProducto'];
	$descripcionProducto = $_REQUEST['descripcionProducto'];
	$desumed = $_REQUEST['desumed'];
	$precio = $_REQUEST['precio'];

	/* CABECERA DEL MAIL */
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'ssl';
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->Username = 'sistema.op.tavelli@gmail.com';
	$mail->Password = 'Tavelli.2015';
	$mail->SetFrom('sistema.op.tavelli@gmail.com', 'Tavelli');
	// Tu nueva dirección de correo electrónico es sistema.op.tavelli@gmail.com. 
	// Tipos de Envio
	// 1.- Envio de Mail con Codigo de Guia Despacho para Anulación

		$msj =" ALERTA <br> ";
		$msj.=" Local <b>".$_SESSION['dsparam']['cliente']." </b> - <b>".$_SESSION['dsparam']['nombreCliente']." </b> <br> ";
		$msj.=" Usuario: ".$_SESSION['dsparam']['nombreUsuario']." <br> ";
		$msj.=" Ingreso el producto: <b>".$codigoProducto." - ".$descripcionProducto." </b> <br>";
		$msj.=" Cantidad: <b> ".$cantidad." </b> <br>";
		$msj.=" Precio <b>$0</b> ";
		$asunto = "ALERTA: Nota de Venta Precio $0";

$mail->addAddress('bodega@tavelli.cl','Edwin Ruiz');
$mail->addAddress('despacho@tavelli.cl','Jessica Carvajal');
$mail->addAddress('enavarrete@tavelli.cl','Edith Navarrete');
$mail->addCC('dmontenegro@tavelli.cl','Diego Montenegro');
$mail->addBCC('rodrigoretamal@outlook.com','Rodrigo Retamal');
//$mail->addCC('malvaradozamorano@gmail.com','Marcelo Alvarado');
	$mail->Subject = $asunto;
	$mail->Body = $msj;
	$mail->MsgHTML($msj);
	$mail->CharSet = 'UTF-8';
	$mail->Send();
	
}	
if($_REQUEST['condicion'] == 'contarNotasPedido')
{
	include 'conexion.php';
$fecha = $_REQUEST['fecha'];
$sql = "SELECT COUNT(vencod) AS cantidad,vencod from ".$bd['dsparam'].".[DS_NotasVenta] WHERE estadoNP='P' AND nvFeEnt = convert(datetime,'".$fecha."',103) GROUP BY vencod  ORDER BY vencod";
//echo $sql;
$salida = '<br><br><br><div class="titulo_pagina"></div>  <div class="menu"> ';
$res = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 $registros = sqlsrv_num_rows($res);
if($registros == '0' || $registros == 0)
{
			$salida.= '<div class="message_info"><p>No se han encontrado notas de pedido a agrupar</p></div>'; 
}
					while ($row = sqlsrv_fetch_array($res))
					{
						if($row['vencod'] == '01')
						{
							$imagen = 'bodega';
							$texto = 'Bodega';
						}
						else if($row['vencod'] == '02')
						{
							$imagen = 'helado';
							$texto = 'Pasteler&iacute;a/Salados';
						}
						else if($row['vencod'] == '03')
						{
							$imagen = 'ventas';
							$texto = 'Helados';
						}	
						$salida.='		
		<div class="col-sm-4">
			<div class="item">
				<h2>'.$texto.'</h2>
				<div>
					<a href="#" onclick="enviarDatos('.$row['vencod'].');"><img src="images/notaPedido/'.$imagen.'.png" class="menu-img"/></a>
				</div>
				<h2>Registros: '.$row['cantidad'].'</h2>
				
			</div>
		</div>';
					}
	$salida.= '</div>';
	echo $salida;
}		

		
?>