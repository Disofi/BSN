<?php
function informeGuias($fecdes, $fechas)
{
//echo $fecdes."  --  ".$fechas."<br>";
include('includes/conexion.php');
require_once('includes/PHPExcel.php');
$registro_acumulado = 0;
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-guias-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()
        ->setCreator("Disofi 2015")
        ->setLastModifiedBy("Disofi 2015")
        ->setTitle("Informe Guias")
        ->setSubject("Informe Guias")
        ->setDescription("Informe Guias")
        ->setKeywords("Office PHPExcel Tavelli")
        ->setCategory("Informe Guias");
	$salida.= '<table class="registros table table-hover" id="dataTable">
     <thead>
     <tr>
        <th>Auxiliar</th>
        <th>Fecha</th>
        <th>Producto</th>
		<th>Tipo</th>
		<th>Folio</th>
		<th>Cantidad Ingresada</th>
		<th>Cantidad Despachada</th>
		<th>Total</th>
     </tr>
     </thead>
     <tbody>';
	 
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', "Cod Auxiliar")
	->setCellValue('B1', "Nom Auxiliar")
	->setCellValue('C1', "Rut Auxiliar")
	->setCellValue('D1', "Cod Bodega")
	->setCellValue('E1', "Cod Producto")
	->setCellValue('F1', "Desc Producto")
	->setCellValue('G1', "Tipo")
	->setCellValue('H1', "Estado")
	->setCellValue('I1', "Fecha")
	->setCellValue('J1', "Folio")
	->setCellValue('K1', "Cant Ingresada")
	->setCellValue('L1', "Cant Despachada")
	->setCellValue('M1', "Precio Unitario")
	->setCellValue('N1', "Total Linea")
	->setCellValue('O1', "Concepto")
	->setCellValue('P1', "Glosa")
	->setCellValue('Q1', "Partida")	
	->setCellValue('R1', "Pieza")
	->setCellValue('S1', "Fec.Vencto.");	
	
	
		
$sql =" SELECT a.codaux,b.nomaux,a.RutAux,a.CodBode,d.codprod,c.DesProd,a.Tipo,a.Estado,a.Fecha,a.folio,d.CantIngresada, ";
$sql.=" d.CantDespachada,d.PreUniMB,d.TotLinea,a.Concepto AS Conceptos,a.Glosa,d.partida,d.pieza,d.fechavencto ";
$sql.=" FROM ".$bd['softland'].".iw_gmovi d ";
$sql.=" left join ".$bd['softland'].".iw_gsaen a on a.nroint = d.NroInt AND a.tipo =d.Tipo ";
$sql.=" left join ".$bd['softland'].".cwtauxi b on a.CodAux = b.CodAux ";
$sql.=" left join ".$bd['softland'].".iw_tprod c on c.codprod = d.CodProd ";
$sql.=" WHERE a.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
$sql.=" AND (a.tipo ='E' OR a.tipo='S' )order by tipo, folio asc ";
//echo $sql."<br><br>";
		//$res_a = sqlsrv_query($conn, $sql, array(), array('Scrollable' => 'buffered'));
		$res_a = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$j=2;
			while ($row_a = sqlsrv_fetch_array($res_a))
			{
				$salida.= '<tr>
							<td>'.$row_a['codaux'].' - '.$row_a['nomaux'].'</td>
							<td>'.date_format($row_a['Fecha'], 'd/m/Y').'</td>
							<td>'.$row_a['codprod'].' - '.$row_a['DesProd'].'</td>
							<td>'.$row_a['Tipo'].'</td>
							<td>'.$row_a['folio'].'</td>
							<td>'.$row_a['CantIngresada'].'</td>
							<td>'.$row_a['CantDespachada'].'</td>
							<td>$'.$row_a['TotLinea'].'</td>			
						 </tr>';	
					$objPHPExcel->setActiveSheetIndex(0)					
						->setCellValue('A'.$j, $row_a['codaux'])
						->setCellValue('B'.$j, $row_a['nomaux'])
						->setCellValue('C'.$j, $row_a['RutAux'])
						->setCellValue('D'.$j, $row_a['CodBode'])
						->setCellValue('E'.$j, $row_a['codprod'])
						->setCellValue('F'.$j, $row_a['DesProd'])
						->setCellValue('G'.$j, $row_a['Tipo'])
						->setCellValue('H'.$j, $row_a['Estado'])
						->setCellValue('I'.$j, date_format($row_a['Fecha'], 'd/m/Y'))
						->setCellValue('J'.$j, $row_a['folio'])
						->setCellValue('K'.$j, $row_a['CantIngresada'])
						->setCellValue('L'.$j, $row_a['CantDespachada'])
						->setCellValue('M'.$j, $row_a['PreUniMB'])
						->setCellValue('N'.$j, $row_a['TotLinea'])
						->setCellValue('O'.$j, $row_a['Conceptos'])
						->setCellValue('P'.$j, $row_a['Glosa'])
						->setCellValue('Q'.$j, $row_a['partida'])
						->setCellValue('R'.$j, $row_a['pieza'])
						->setCellValue('S'.$j, Utf8_encode(date_format($row_a['fechavencto'], 'd/m/Y')));	

						
					$j++;
			}
					$salida.= "</tbody></table>";
					require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';		
					//print "funcion finalizada";
}	

function informeProduccion($fecdes, $fechas)
{
	$salida = "";
	$a = 0;
	$b = 2;
	$c = 2;
	$d = 2;
	$posicion_b = 2;
	$posicion_c = 2;
	$posicion_d = 2;
	$posicion_e = 2;
	$posicion_f = 2;
	$hoy = date('d-m-Y');
	
	$datos = "";
	
	
	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');
		$fechaExcel   = date('dmY-His');
		$fname = "informes/Informe-produccion-".$fechaExcel.".xls";
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setCreator("Disofi 2015")
			->setLastModifiedBy("Disofi 2015")
			->setTitle("Informe Produccion")
			->setSubject("Informe Produccion")
			->setDescription("Informe Produccion")
			->setKeywords("Office PHPExcel Tavelli")
			->setCategory("Informe Produccion");
	
	
	$query_cabecera =" SELECT  dworproprod.orden, dworproprod.codprod, dwordenf.GlosaOrden, dworproprod.EsProcInterno, ";
	//$query_cabecera =" SELECT  dworproprod.orden, dworproprod.codprod, dwordenf.GlosaOrden, dworproprod.EsProcInterno, ";
	$query_cabecera.=" CASE WHEN dworproprod.EsProcInterno = -1 then 'Interna' else 'Externa' END as DescEsProcInterno, ";
	$query_cabecera.=" case when dworproprod.EstaTerminado = -1 then 'Terminada' else 'Pendiente' END as Estado ";
	$query_cabecera.=" ,dwordenf.nomsolic,dwordprodu.FecIniProd,dworproprod.EstaTerminado,dwordprodu.CantFab,iw_tprod.desprod  ";
	$query_cabecera.=" FROM tavelli1.softland.dworproprod dworproprod ";
	$query_cabecera.=" LEFT JOIN tavelli1.softland.iw_tprod iw_tprod ON iw_tprod.codprod = dworproprod.CodProd ";
	$query_cabecera.=" LEFT JOIN tavelli1.softland.dwordenf dwordenf ON dwordenf.Orden = dworproprod.Orden ";
	$query_cabecera.=" LEFT JOIN tavelli1.softland.dwordprodu dwordprodu ON dwordprodu.orden = dworproprod.Orden ";
	$query_cabecera.=" WHERE dwordprodu.FecIniProd between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
	$query_cabecera.= " ORDER BY orden ASC ";
	
	//echo $query_cabecera."<br><br>";
		$salida.= '<table class="registros table table-hover" id="dataTable">
     <thead>
     <tr>
        <th>OP</th>
        <th>C&oacute;digo</th>
        <th>Item</th>
		<th>Glosa</th>
		<th>Tipo</th>
		<th>Solicitante</th>
		<th>Fecha Inicio</th>
		<th>Estado</th>
		<th>Cantidad Producci&oacute;n</th>
     </tr>
     </thead>
     <tbody>';
	
		$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', "OP")
	->setCellValue('B1', "Codigo")
	->setCellValue('C1', "Item")
	->setCellValue('D1', "Glosa")
	->setCellValue('E1', "Tipo")
	->setCellValue('F1', "Solicitante")
	->setCellValue('G1', "Fecha Inicio")
	->setCellValue('H1', "Estado")
	->setCellValue('I1', "Cantidad OP")
	->setCellValue('J1', "Cantidad Avance")
	->setCellValue('K1', "Consumo")
	->setCellValue('L1', "Fecha Consumo")
	->setCellValue('M1', "Avance")
	->setCellValue('N1', "Fecha Avance")
	->setCellValue('O1', "Transferencia")
	->setCellValue('P1', "Fecha Transferencia")
	->setCellValue('Q1', "Guia Transferencia")
	->setCellValue('R1', "Bodega Destino")
	->setCellValue('S1', "Guia Bodega")
	->setCellValue('T1', "Stock Despacho")
	->setCellValue('U1', "Glosa")
	->setCellValue('V1', "N Guia")
	->setCellValue('W1', "Bodega Destino");	
	
			$res_cabecera = sqlsrv_query( $conn, $query_cabecera , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			
			while ($row_cabecera = sqlsrv_fetch_array($res_cabecera))
			{
								$salida.= '<tr>
							<td>'.$row_cabecera['orden'].'</td>
							<td>'.$row_cabecera['codprod'].'</td>
							<td>'.$row_cabecera['desprod'].'</td>
							<td>'.$row_cabecera['GlosaOrden'].'</td>
							<td>'.$row_cabecera['DescEsProcInterno'].'</td>
							<td>'.$row_cabecera['nomsolic'].'</td>
							<td>'.date_format($row_cabecera['FecIniProd'],'d-m-Y').'</td>	
							<td>'.$row_cabecera['Estado'].'</td>
							<td>'.$row_cabecera['CantFab'].'</td>
						 </tr>';	

				$query_b = " SELECT  total,Fecha,orden FROM tavelli1.softland.iw_gsaen WHERE  Concepto='09' and tipo ='S' and orden = '".$row_cabecera['orden']."'";
				
				$query_c =" SELECT CantProd,FecAvPro, CASE WHEN EstaTerminado = 0 THEN 'SI' ELSE 'Terminado' END AS EstaTerminado  ";
				$query_c.=" FROM tavelli1.softland.dwavpro WHERE orden = '".$row_cabecera['orden']."' ";
				
				//case when dworproprod.EstaTerminado = -1 then 'Terminada' else 'Pendiente' END as Estado
				$query_d =" SELECT iw_gsaen.Fecha,iw_gsaen.Folio,iw_gsaen.CodBod, iw_gmovi.CantIngresada, iw_gsaen.orden ";
				$query_d.=" FROM tavelli1.softland.iw_gsaen iw_gsaen ";
				$query_d.=" LEFT JOIN tavelli1.softland.iw_gmovi iw_gmovi on iw_gmovi.Orden = iw_gsaen.Orden AND iw_gmovi.NroInt = iw_gsaen.NroInt AND iw_gmovi.Tipo = iw_gsaen.Tipo ";
				$query_d.=" where iw_gsaen.orden = '".$row_cabecera['orden']."' AND Concepto='10' and iw_gsaen.tipo ='E' ";
				
				$query_e = "select glosa, folio,codbod from tavelli1.softland.iw_gsaen where glosa like  '%#".$row_cabecera['orden']."#%' AND concepto = '06' AND tipo = 'S' ";
				
				
				
				$query_f = " SELECT  Sum(IW_GMOVI.CantIngresada - IW_GMOVI.CantDespachada) * 1 AS Stock  ";
				$query_f.= " FROM tavelli1.softland.IW_GMOVI WITH (INDEX(IW_GMOVI_Producto))   ";
				$query_f.= " WHERE (TipoOrigen = 'D' OR TipoDestino = 'D') AND IW_GMOVI.Fecha <= Convert(datetime,'".$hoy."',103)   ";
				$query_f.= " AND IW_GMOVI.Actualizado = - 1   ";
				$query_f.= " GROUP BY IW_GMOVI.CodProd HAVING IW_GMOVI.CodProd = '".$row_cabecera['codprod']."'"; 
				
				

				/*echo $query_b."<br>";
				echo $query_c."<br>";
				echo $query_d."<br>";
				echo $query_e."<br>";
				echo $query_f."<br><br>";
				*/
				$res_b = sqlsrv_query( $conn, $query_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				$num_b = sqlsrv_num_rows($res_b);	
				
				$res_c = sqlsrv_query( $conn, $query_c , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				$num_c = sqlsrv_num_rows($res_c);	
				
				$res_d = sqlsrv_query( $conn, $query_d , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				$num_d = sqlsrv_num_rows($res_d);	
				
				$res_e = sqlsrv_query( $conn, $query_e , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				$num_e = sqlsrv_num_rows($res_e);	

				$res_f = sqlsrv_query( $conn, $query_f , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				$num_f = sqlsrv_num_rows($res_f);	
				
				//echo $num_b."<br>";
				//echo $num_c."<br>";
				//echo $num_d."<br>";
				
				$max_resultado = max($num_b, $num_c, $num_d, $num_e, $num_f);
				//echo $max_resultado." : max<br><br>";
				for($a = 0;$a<$max_resultado; $a++)
				{				
					while ($row_b = sqlsrv_fetch_array($res_b))
					{
						$objPHPExcel->setActiveSheetIndex(0)	
						->setCellValue('A'.$posicion_b, $row_cabecera['orden'])
						->setCellValue('B'.$posicion_b, $row_cabecera['codprod'])
						->setCellValue('C'.$posicion_b, $row_cabecera['desprod'])
						->setCellValue('D'.$posicion_b, $row_cabecera['GlosaOrden'])
						->setCellValue('E'.$posicion_b, $row_cabecera['DescEsProcInterno'])
						->setCellValue('F'.$posicion_b, $row_cabecera['nomsolic'])
						->setCellValue('G'.$posicion_b, date_format($row_cabecera['FecIniProd'],'d-m-Y'))
						->setCellValue('H'.$posicion_b, $row_cabecera['Estado'])
						->setCellValue('I'.$posicion_b, $row_cabecera['CantFab'])					
						->setCellValue('K'.$posicion_b, $row_b['total'])
						->setCellValue('L'.$posicion_b, date_format($row_b['Fecha'],'d-m-Y'));	
							$posicion_b++;
					}
					
					while ($row_c = sqlsrv_fetch_array($res_c))
					{
						$objPHPExcel->setActiveSheetIndex(0)	
							->setCellValue('A'.$posicion_c, $row_cabecera['orden'])
							->setCellValue('B'.$posicion_c, $row_cabecera['codprod'])
							->setCellValue('C'.$posicion_c, $row_cabecera['desprod'])
							->setCellValue('D'.$posicion_c, $row_cabecera['GlosaOrden'])
							->setCellValue('E'.$posicion_c, $row_cabecera['DescEsProcInterno'])
							->setCellValue('F'.$posicion_c, $row_cabecera['nomsolic'])
							->setCellValue('G'.$posicion_c, date_format($row_cabecera['FecIniProd'],'d-m-Y'))
							->setCellValue('H'.$posicion_c, $row_cabecera['Estado'])
							->setCellValue('I'.$posicion_c, $row_cabecera['CantFab'])
							->setCellValue('J'.$posicion_c, $row_c['CantProd'])	
							->setCellValue('M'.$posicion_c, $row_c['EstaTerminado'])
							->setCellValue('N'.$posicion_c, date_format($row_c['FecAvPro'],'d-m-Y'));
						$posicion_c++;
						
					}
					while ($row_d = sqlsrv_fetch_array($res_d))
					{
						$objPHPExcel->setActiveSheetIndex(0)	
						->setCellValue('A'.$posicion_d, $row_cabecera['orden'])
						->setCellValue('B'.$posicion_d, $row_cabecera['codprod'])
						->setCellValue('C'.$posicion_d, $row_cabecera['desprod'])
						->setCellValue('D'.$posicion_d, $row_cabecera['GlosaOrden'])
						->setCellValue('E'.$posicion_d, $row_cabecera['DescEsProcInterno'])
						->setCellValue('F'.$posicion_d, $row_cabecera['nomsolic'])
						->setCellValue('G'.$posicion_d, date_format($row_cabecera['FecIniProd'],'d-m-Y'))
						->setCellValue('H'.$posicion_d, $row_cabecera['Estado'])
						->setCellValue('I'.$posicion_d, $row_cabecera['CantFab'])
						->setCellValue('O'.$posicion_d, $row_d['CantIngresada'])
						->setCellValue('P'.$posicion_d, date_format($row_d['Fecha'],'d-m-Y'))
						->setCellValue('Q'.$posicion_d, $row_d['Folio'])
						->setCellValue('R'.$posicion_d, $row_d['CodBod']);
							$posicion_d++;
					}
					
					while ($row_e = sqlsrv_fetch_array($res_e))
					{
						$objPHPExcel->setActiveSheetIndex(0)	
						->setCellValue('A'.$posicion_e, $row_cabecera['orden'])
						->setCellValue('B'.$posicion_e, $row_cabecera['codprod'])
						->setCellValue('C'.$posicion_e, $row_cabecera['desprod'])
						->setCellValue('D'.$posicion_e, $row_cabecera['GlosaOrden'])
						->setCellValue('E'.$posicion_e, $row_cabecera['DescEsProcInterno'])
						->setCellValue('F'.$posicion_e, $row_cabecera['nomsolic'])
						->setCellValue('G'.$posicion_e, date_format($row_cabecera['FecIniProd'],'d-m-Y'))
						->setCellValue('H'.$posicion_e, $row_cabecera['Estado'])
						->setCellValue('I'.$posicion_e, $row_cabecera['CantFab'])	
						->setCellValue('U'.$posicion_e, $row_e['glosa'])
						->setCellValue('V'.$posicion_e, $row_e['folio'])
						->setCellValue('W'.$posicion_e, $row_e['codbod']);
							if($row_e['glosa'] <> '')
							{
								$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('S'.$posicion_e, 'SI');
							}						
							

							$posicion_e++;
					}
					
					while ($row_f = sqlsrv_fetch_array($res_f))
					{
						$objPHPExcel->setActiveSheetIndex(0)	
						->setCellValue('A'.$posicion_f, $row_cabecera['orden'])
						->setCellValue('B'.$posicion_f, $row_cabecera['codprod'])
						->setCellValue('C'.$posicion_f, $row_cabecera['desprod'])
						->setCellValue('D'.$posicion_f, $row_cabecera['GlosaOrden'])
						->setCellValue('E'.$posicion_f, $row_cabecera['DescEsProcInterno'])
						->setCellValue('F'.$posicion_f, $row_cabecera['nomsolic'])
						->setCellValue('G'.$posicion_f, date_format($row_cabecera['FecIniProd'],'d-m-Y'))
						->setCellValue('H'.$posicion_f, $row_cabecera['Estado'])
						->setCellValue('I'.$posicion_f, $row_cabecera['CantFab'])	
						->setCellValue('T'.$posicion_f, $row_f['Stock']);		
							$posicion_f++;
					}						
					
				}
				//echo $posicion_b."<br>";
				//echo $posicion_c."<br>";
				//echo $posicion_d."<br>";
				//echo $posicion_e."<br>";
				//echo $posicion_f."<br>";
				
				$ultima_posicion = max($posicion_b, $posicion_c, $posicion_d, $posicion_e,$posicion_f);
					$posicion_b = $ultima_posicion;
					$posicion_c = $ultima_posicion;
					$posicion_d = $ultima_posicion;
					$posicion_e = $ultima_posicion;
					$posicion_f = $ultima_posicion;

					
			}

	$salida.= "</tbody></table>";
	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';		

	
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////        VENTAS      //////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function informe($fecdes, $fechas)
{

	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	
	
	$fecdes = str_replace("-","/",$fecdes);
	$fechas = str_replace("-","/",$fechas);
	//echo $fecdes." //".$fechas."<br><br>";
	$fecha_hoy = date("Y/m/d");
	$contador = 1;
	$contador_b = 1;
	$e = 2;
	$campos = "";
	$campos_b = "";
	$letra = "A";
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-ventas-".$fechaExcel.".xls";
	function amoneda($numero, $moneda)
	{  
    $longitud = strlen($numero);  
    $punto = substr($numero, -1,1);  
    $punto2 = substr($numero, 0,1);  
    $separador = ".";  
    if($punto == "."){  
    $numero = substr($numero, 0,$longitud-1);  
    $longitud = strlen($numero);  
    }  
    if($punto2 == "."){  
    $numero = "0".$numero;  
    $longitud = strlen($numero);  
    }  
    $num_entero = strpos ($numero, $separador);  
    $centavos = substr ($numero, ($num_entero));  
    $l_cent = strlen($centavos);  
    if($l_cent == 2){$centavos = $centavos."0";}  
    elseif($l_cent == 3){$centavos = $centavos;}  
    elseif($l_cent > 3){$centavos = substr($centavos, 0,3);}  
    $entero = substr($numero, -$longitud,$longitud-$l_cent);  
    if(!$num_entero){  
        $num_entero = $longitud;  
        $centavos = ".00";  
        $entero = substr($numero, -$longitud,$longitud);  
    }  
       
    $start = floor($num_entero/3);  
    $res = $num_entero-($start*3);  
    if($res == 0){$coma = $start-1; $init = 0;}else{$coma = $start; $init = 3-$res;}  
    $d= $init; $i = 0; $c = $coma;  
        while($i <= $num_entero){  
            if($d == 3 && $c > 0){$d = 0; $sep = "."; $c = $c-1;}else{$sep = "";}  
            $final .=  $sep.$entero[$i];  
            $i = $i+1; // todos los digitos  
            $d = $d+1; // poner las comas  
        }  
        if($moneda == "pesos")  {$moneda = "";  
        return $moneda." ".$final;  
        }  
        elseif($moneda == "dolares"){$moneda = "USD";  
        return $moneda." ".$final.$centavos;  
        }  
        elseif($moneda == "euros")  {$moneda = "EUR";  
        return $final.$centavos." ".$moneda;  
        }  
    }  
	
	
	
	
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Venta")
		->setSubject("Informe Venta")
		->setDescription("Informe Venta")
		->setKeywords("Office PHPExcel Videojet")
		->setCategory("Informe Venta");
			
	$drop_table = " drop table ".$bd['dsparam'].".TEMPprueba ";	
		//echo $drop_table."<br>";
		$res_drop = sqlsrv_query( $conn, $drop_table , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	$drop_table_b = " drop table ".$bd['dsparam'].".TEMPprueba_b ";	
		//echo $drop_table_b."<br>";
		$res_drop_b = sqlsrv_query( $conn, $drop_table_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

	$crear_tabla =" CREATE TABLE ".$bd['dsparam'].".TEMPprueba(Codigo varchar (20) NOt NULL, ";
	$crear_tabla.=" Atributo1 varchar (250),Atributo2 varchar (250),Atributo3 varchar (250),Atributo4 varchar (250),Atributo5 varchar (250),	";
	$crear_tabla.=" Atributo6 varchar (250),Atributo7 varchar (250),Atributo8 varchar (250),Atributo9 varchar (250),Atributo10 varchar (250),	";
	$crear_tabla.=" Atributo11 varchar (250),Atributo12 varchar (250),Atributo13 varchar (250),Atributo14 varchar (250),Atributo15 varchar (250),	";
	$crear_tabla.=" Atributo16 varchar (250),Atributo17 varchar (250),Atributo18 varchar (250),Atributo19 varchar (250),Atributo20 varchar (250)
	,Atributo21 varchar (250)
	,Atributo22 varchar (250)
	,Atributo23 varchar (250)
	,Atributo24 varchar (250)
	,Atributo25 varchar (250)
	,Atributo26 varchar (250)
	,Atributo27 varchar (250)
	,Atributo28 varchar (250)
	,Atributo29 varchar (250)
	,Atributo30 varchar (250)
	,Atributo31 varchar (250)
	,Atributo32 varchar (250)
	,Atributo33 varchar (250)
	,Atributo34 varchar (250)
	,Atributo35 varchar (250))	";
		//echo $crear_tabla."<br><br>";
		$res_tabla = sqlsrv_query( $conn, $crear_tabla , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	 $completar_sabana =" INSERT INTO ".$bd['dsparam'].".TEMPprueba(codigo)    ";
	 $completar_sabana.=" select a.codprod from ".$bd['softland'].".iw_tprod a   ";
	 $completar_sabana.=" left join ".$bd['softland'].".[iw_tprodTVAtrV] b  on a.CodProd = b.Codigo  ";
	 $completar_sabana.=" left join ".$bd['softland'].".[iw_tprodTTAtr] c  on b.CodTat = c.codtat and b.IdMaestro = c.idmaestro ";
		//echo $completar_sabana."<br><br>";
		$res_sabana = sqlsrv_query( $conn, $completar_sabana , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	$obtener_atributos = " select codtat,idmaestro,NombreTipo,DescripcionTipo,Tipo,ValorDef from ".$bd['softland'].".[iw_tprodTTAtr] order by CodTat ";
		//echo $obtener_atributos."<br><br>";
		$res_atributos = sqlsrv_query( $conn, $obtener_atributos , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	while ($row_atributos = sqlsrv_fetch_array($res_atributos))
	{
		$update_a =" UPDATE ".$bd['dsparam'].".TEMPprueba SET Atributo".$contador." = C.DescripcionLista FROM dbo.TEMPprueba AS A  ";
		$update_a.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtrT AS B ON A.Codigo collate SQL_Latin1_General_CP1_CI_AI = B.Codigo   ";
		$update_a.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtr AS C ON B.CodTaTe = C.CodTaTe AND B.CodTaT = C.CodTaT ";
		$update_a.=" Where B.CodTat = '".$row_atributos['codtat']."' ";
			//echo $update_a."<br>";
			sqlsrv_query( $conn, $update_a , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		$update_b =" UPDATE ".$bd['dsparam'].".TEMPprueba SET Atributo".$contador."= B.Valor   
			FROM ".$bd['dsparam'].".TEMPprueba AS A   ";
		$update_b.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtrV AS B ON A.Codigo = B.Codigo     collate SQL_Latin1_General_CP1_CI_AI ";
		$update_b.=" INNER JOIN ".$bd['softland'].".[iw_tprodTTAtr] AS C ON B.IdMaestro = C.IdMaestro AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos['codtat']."' ";
				sqlsrv_query( $conn, $update_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				//echo $update_b."<br><br>";	
					$campos.= "temp.Atributo".$contador." AS atr".$contador.",";
					$contador++;
				
	}

		$campos = substr($campos, 0, -1);
		//echo $campos." : campos_a<br>";
		
		
		$crear_tabla_b =" CREATE TABLE ".$bd['dsparam'].".TEMPprueba_b(Codigo varchar (10) NOt NULL, Atributo1 varchar (250),Atributo2 varchar (250),Atributo3 varchar (250),Atributo4 varchar (250), ";
		$crear_tabla_b.=" Atributo5 varchar (250),Atributo6 varchar (250),Atributo7 varchar (250),Atributo8 varchar (250),Atributo9 varchar (250),Atributo10 varchar (250), ";
		$crear_tabla_b.=" Atributo11 varchar (250),Atributo12 varchar (250),Atributo13 varchar (250),Atributo14 varchar (250),Atributo15 varchar (250),Atributo16 varchar (250), ";
		$crear_tabla_b.=" Atributo17 varchar (250),Atributo18 varchar (250),Atributo19 varchar (250),Atributo20 varchar (250),Atributo21 varchar (250)
		,Atributo22 varchar (250)
		,Atributo23 varchar (250)
		,Atributo24 varchar (250)
		,Atributo25 varchar (250)
		,Atributo26 varchar (250)
		,Atributo27 varchar (250)
		,Atributo28 varchar (250)
		,Atributo29 varchar (250)
		,Atributo30 varchar (250)
		,Atributo31 varchar (250)
		,Atributo32 varchar (250)
		,Atributo33 varchar (250)
		,Atributo34 varchar (250)
		,Atributo35 varchar (250)) ";
			//echo $crear_tabla_b."<br><br>";
			$res_tabla_b = sqlsrv_query( $conn, $crear_tabla_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			
	
		$completar_sabana_b =" INSERT INTO ".$bd['dsparam'].".TEMPprueba_b(codigo) ";
		$completar_sabana_b.=" select a.codaux from ".$bd['softland'].".cwtauxi a  left join ".$bd['softland'].".CWTAuxiTVAtrV b  on a.Codaux = b.Codigo ";
		$completar_sabana_b.=" left join ".$bd['softland'].".CWTAuxiTTAtr c  on b.CodTat = c.codtat and b.IdMaestro = c.idmaestro ";
			//echo $completar_sabana_b."<br><br>";
			$res_sabana_b = sqlsrv_query( $conn, $completar_sabana_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

			$obtener_atributos_b = "select codtat,idmaestro,NombreTipo,DescripcionTipo,Tipo,ValorDef from ".$bd['softland'].".CWTAuxiTTAtr order by CodTat ";
				//echo $obtener_atributos_b."<br><br>";
				$res_atributos_b = sqlsrv_query( $conn, $obtener_atributos_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			while ($row_atributos_b = sqlsrv_fetch_array($res_atributos_b))
			{
				$update_c =" UPDATE ".$bd['dsparam'].".TEMPprueba_b SET Atributo".$contador_b." = C.DescripcionLista FROM dsvideojet.dbo.TEMPprueba_b AS A ";
				$update_c.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtrT AS B ON A.Codigo = B.Codigo collate SQL_Latin1_General_CP1_CI_AI ";
				$update_c.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtr AS C ON B.CodTaTe = C.CodTaTe AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos_b['codtat']."' ";
					//echo $update_c."<br><br>";
					sqlsrv_query( $conn, $update_c , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				
				$update_d =" UPDATE ".$bd['dsparam'].".TEMPprueba_b SET Atributo".$contador_b."= B.Valor   FROM dsvideojet.dbo.TEMPprueba_b AS A  ";
				$update_d.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtrV AS B ON A.Codigo = B.Codigo   collate SQL_Latin1_General_CP1_CI_AI ";
				$update_d.=" INNER JOIN ".$bd['softland'].".CWTAuxiTTAtr AS C ON B.IdMaestro = C.IdMaestro AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos_b['codtat']."' ";
					//echo $update_d."<br><br>";
					sqlsrv_query( $conn, $update_d , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
					
				$campos_b.= "temp_b.Atributo".$contador_b." AS btr".$contador_b.",";
				$contador_b++;
	
			}
			//echo "<br><br><br>";
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	
	$styleArray = array(
    'font'  => array(
        'bold'  => true
        //'color' => array('rgb' => 'FF0000'),
        //'size'  => 15,
        //'name'  => 'Verdana'
    ));

	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	
	
	$campos_b = substr($campos_b, 0, -1);
	//echo $campos_b."<-- campos_b<br>";
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Codigo Cliente")); $letra++; //A
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Razon Social")); $letra++; //B
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Num Doc")); $letra++; //C
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Tipo Documento")); $letra++; //D
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Producto")); $letra++; //E
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Detalle Producto")); $letra++; //F
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Familia")); $letra++; //G
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Grupo 2")); $letra++; //H
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cantidad")); $letra++; //I
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Valor Unitario ")); $letra++; //J
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Valor Total (NETO)")); $letra++; //K
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Emision")); $letra++; //L
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Fecha Vencimiento")); $letra++; //M
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Vendedor")); $letra++; //N
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			
			/*$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Serie")); $letra++; //O
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			*/
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Partida")); $letra++; //O
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Fecha Venc Producto")); $letra++; //O
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Codigo Canal")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Descripcion Canal")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Codigo Cat Cliente")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Descripcion Cat Cliente")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			
			
			
			
			/*INICIO ATRIBUTOS*/
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("CATEGORIA(PGPS)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Código Goldenfrost")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("División")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Familia (BP)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Desc. de Subgrupo (GBU)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("CASA Estrategica")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Especialista")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cenabast")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Convenio Marco")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Lanzamiento")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Procedencia")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Compresión")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Tipo de Producto")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Puntera / Cierre")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Talla")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Color")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cod Barras Caja Master")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			
			/*
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("UxC")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			*/
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("(Und Vta x Caja Master)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cajas Estiba Base")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cajas Estiba Alto")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Cajas Estiba x Pallet")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Alto Caja Master (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Largo Caja Master (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Ancho Caja Master (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Vol Caja Master (cm 3)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Peso Caja Master (Kg)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Alto Und Vta (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Ancho Und Vta (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Largo Und Vta (cm)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("Vol. Und de Vta (cm3)")); $letra++;
			$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra."1", strtoupper("OC")); $letra++;
			
			
	
	$query_cabecera =" select distinct  gs.NroInt,gs.nvnumero,[DSBSN].[dbo].refDte(gs.NroInt) AS RefDte,gm.linea, gs.codaux,cwt.NomAux,gs.folio,gm.CodProd,iwt.esconfig as eskit,gm.kit, ".$bd['dsparam'].".grupoKit(gm.KIT) AS deskit ,iwt.desprod, ";
	$query_cabecera.=" grupo.DesGrupo, iwt.CodGrupo, iwt.CodSubGr, subGrupo.DesSubGr,";
	$query_cabecera.=" gm.CantFacturada,gm.preunimb, ";
	$query_cabecera.=" Round(gm.TotLinea-((gm.TotLinea * gs.porcdesc01)/100),0) as TotLinea,gs.fecha,gs.FechaVenc,'' as TipoCambio, ".$bd['dsparam'].".costoprom(gm.CodProd,'".$fecha_hoy."') as CostoProm, ";
	$query_cabecera.=" (gm.cantFacturada * ".$bd['dsparam'].".costoprom(gm.CodProd,'".$fecha_hoy."')  ) as costoTotalProm, ";
	$query_cabecera.=" ".$bd['dsparam'].".costoprom(gm.CodProd,gs.fecha) as CostoPromAlDia,  ";
	$query_cabecera.=" (gm.cantFacturada * ".$bd['dsparam'].".costoprom(gm.CodProd,gs.fecha) ) as costoTotalPromAlDia, ";
	$query_cabecera.=" gm.Partida, gm.Pieza, descCl.CatDes,";
	$query_cabecera.=" gs.CodVendedor, vendedor.VenDes, ";
	$query_cabecera.=" cast(gm.DetProd as varchar(max)) AS DetProd, ";
	$query_cabecera.=" 	case  ";
	$query_cabecera.=" 	when gs.SubTipoDocto='A' then 'Afecto' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'E' then 'Exento' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'x' then 'Exportación' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'T' then 'Docto Electrónico' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'L' then 'Liquidacion' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'N' then 'Liquidacion Factura' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'O' then 'Liquidacion electronica' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'C' then 'Documento Interno de Venta' ";
	$query_cabecera.=" 	else ";
	$query_cabecera.=" 	'--' ";
	$query_cabecera.=" 	end ";
	$query_cabecera.=" 	AS tipoDocto, ";
	$query_cabecera.= $campos.",";
	$query_cabecera.= $campos_b."  ";
	$query_cabecera.=" ,cl.CodCan, canal.CanDes,cl.CatCli, descCl.CatDes, gm.FechaVencto ";
	$query_cabecera.=" from ".$bd['softland'].".iw_gsaen gs  ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_gmovi gm on gs.tipo =gm.tipo and gs.nroint = gm.nroint ";
	//$query_cabecera.=" left join ".$bd['softland'].".iw_gmovi gm on gs.tipo =gm.tipo and gs.nroint = gm.nroint AND gs.CodAux = gm.CodAux ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtauxi cwt on gs.codaux = cwt.codaux ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tprod iwt on iwt.codprod = gm.codprod ";
	$query_cabecera.=" left join ".$bd['dsparam'].".TEMPprueba temp ON iwt.codprod = temp.Codigo collate Modern_Spanish_CI_AS ";
	$query_cabecera.=" left join ".$bd['dsparam'].".tempPrueba_b temp_b ON temp_b.Codigo = cwt.CodAux collate Modern_Spanish_CI_AS  ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtvend vendedor ON vendedor.VenCod = gs.CodVendedor ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtcvcl cl ON cl.CodAux = gs.CodAux ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtcgau descCl ON descCl.CatCod = cl.CatCli ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tgrupo grupo ON grupo.CodGrupo = iwt.CodGrupo ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tsubgr subGrupo ON subGrupo.CodSubGr = iwt.CodSubGr ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtcana canal ON canal.CanCod = cl.CodCan ";
	//AGREGAR : CWTCVCL => CODCAN Y CATCLI JUNTO CON SU DESCRIPCION. (4 CAMPOS MÁS) OK
	//cwtcana => descripcion canal OK
	//cwtcgau descCl OK
	//solo descripcion, agregar al final del reporte OK
	//agregar todos los titulos con mayuscula OK
	//COLUMNA H Y K FORMATO MONEDA OK
	//COLUMNA N LA CAMBIO POR GM.FECHAVENCIMIENTO (TABLA IW_GMOVI)
	$query_cabecera.=" WHERE  gs.tipo IN ('F','N','B','D') ";
	$query_cabecera.=" AND gs.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
	//$query_cabecera.=" AND gm.linea IS NOT NULL ";
	$query_cabecera.=" 	AND gs.EnMantencion = '0' ";
	
		echo "<br>";
		//echo $query_cabecera."<br><br>";
		$letra = "A";
	$res_cabecera = sqlsrv_query( $conn, $query_cabecera , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row_datos = sqlsrv_fetch_array($res_cabecera))
	{
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$e, $row_datos['codaux']); $letra++; //A
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$e, $row_datos['NomAux']); $letra++; //B
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['folio']); $letra++; //C
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['tipoDocto']); $letra++; //D
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['CodProd']); $letra++;  //E
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['desprod']); $letra++; //F
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['DesGrupo']); $letra++; //G
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['DesSubGr']); $letra++; //H
			/*COLUMNA H*/
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['CantFacturada']); $letra++; //I
			
			
			//$preunimb = round($row_datos['preunimb']);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['preunimb']); $letra++; //J
			
			//$totLinea = round($row_datos['TotLinea']);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, round($row_datos['TotLinea'])); $letra++;
			//es FECHA y no fechaVENCIMIENTO
			//AGREGAR UNA COLUMNA // FECHA Y FECHA VENCIMIENTO
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, date_format($row_datos['fecha'],'d/m/Y')); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, date_format($row_datos['FechaVenc'],'d/m/Y')); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['VenDes']); $letra++;
			/*
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['Pieza']); $letra++;*/
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['Partida']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, date_format($row_datos['FechaVencto'],'d/m/Y')); $letra++;			
			$prueba = str_replace('0','0',$row_datos['CodCan']);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $prueba); $letra++;
			
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['CanDes']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['CatCli']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['CatDes']); $letra++;
			
			
			/*INICIO ATRIBUTOS*/
			//echo $letra."<br>";
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr1']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr2']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr3']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr4']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr5']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr6']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr7']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr8']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr9']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr10']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr11']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr12']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr13']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr14']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr15']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr16']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr17']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr18']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr19']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr20']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr21']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr22']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr23']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr24']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr25']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr26']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr27']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr28']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr29']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['atr30']); $letra++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($letra.$e, $row_datos['RefDte']); $letra++;
			$letra = "A";
			$letra = "A";
			
/*
if($row_datos['nvnumero'] <> 0 && $row_datos['linea'] == 1)
				{
					//$queryDuplicado = "SELECT * FROM ".$bd['softland'].".iw_gsaen WHERE nvnumero = '".$row_datos['nvnumero']."' AND linea = '1' ";
					
					$queryDuplicado =" SELECT * FROM ".$bd['softland'].".iw_gsaen gs ";
					$queryDuplicado.=" LEFT JOIN ".$bd['softland'].".iw_gmovi gm on gs.tipo =gm.tipo and gs.nroint = gm.nroint AND gs.CodAux = gm.CodAux ";
					$queryDuplicado.=" WHERE gs.nvnumero = '".$row_datos['nvnumero']."' AND gm.linea = '1' ";
					$res_duplicado = sqlsrv_query( $conn, $queryDuplicado , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
					echo $queryDuplicado." duplicado<br>";
					$registros_query = sqlsrv_num_rows($res_duplicado);	
					if($registros_query > 2)
					{
						echo $row_datos['nvnumero']." Mayor a 2 <br>";
						$queryFecha =" SELECT max(gs.Fecha) AS fechaMax FROM ".$bd['softland'].".iw_gsaen gs LEFT JOIN ".$bd['softland'].".iw_gmovi gm ";
						$queryFecha.=" on gs.tipo =gm.tipo and gs.nroint = gm.nroint AND gs.CodAux = gm.CodAux ";
						$queryFecha.=" WHERE gs.nvnumero = '".$row_datos['nvnumero']."' AND gm.linea = '1' AND gs.tipo = 'S'  ";
						$res_Fecha = sqlsrv_query( $conn, $queryFecha , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
							while ($row_fecha = sqlsrv_fetch_array($res_Fecha))
							{
								//echo date_format($row_fecha['fechaMax'],'d-m-Y')." <--<br>";
								$fecha = date_format($row_fecha['fechaMax'],'d-m-Y');
								$costoNuevo = "SELECT ".$bd['dsparam'].".costoprom('".$row_datos['CodProd']."','".$fecha."') AS CostoPromAlDiaNuevo ";
								//echo $costoNuevo."<br>";
									$res_Costo = sqlsrv_query( $conn, $costoNuevo , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
									while ($row_costoPromNuevo = sqlsrv_fetch_array($res_Costo))
									{
										$costoPromNuevo = $row_costoPromNuevo['CostoPromAlDiaNuevo'];
									}
						$queryObs =" SELECT folio FROM ".$bd['softland'].".iw_gsaen gs LEFT JOIN ".$bd['softland'].".iw_gmovi gm ";
						$queryObs.=" on gs.tipo =gm.tipo and gs.nroint = gm.nroint AND gs.CodAux = gm.CodAux ";
						$queryObs.=" WHERE gs.nvnumero = '".$row_datos['nvnumero']."' AND gm.linea = '1' AND gs.tipo = 'S'  ";
								echo $queryObs."<br>";
									$key = 0;
									$obse = "";
									$res_obs = sqlsrv_query( $conn, $queryObs , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
									while ($row_obs = sqlsrv_fetch_array($res_obs))
									{
										$data[$key] = $row_obs['folio'];
										$key++;
									}			
									//var_dump($data);
									$resultado = array_unique($data);
									$contadorArray = count($data);
									//echo $contadorArray.": <-- contador";
									//echo "<br><br>";
									
									for($cc = 0; $cc <$contadorArray; $cc++)
									{
										$obse = $obse.",".$data[$cc];
									}
									//echo $obse." <--<br>";
									
								$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('AR'.$e, "*")
								->setCellValue('AS'.$e, $costoPromNuevo)
								->setCellValue('AT'.$e, $row_datos['nvnumero'])
								->setCellValue('AU'.$e, $obse);
								
								$data = "";
								$obse = "";
								$resultado = "";
							}
					}
				}	
*/				
				$e++;
		
		
	}
	
	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	


}

function informeVentaDetalles($fecdes, $fechas)
{
	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	
	$fecha_hoy = date("Y/m/d");
	$contador = 1;
	$contador_b = 1;
	$e = 2;
	$campos = "";
	$campos_b = "";
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-ventas-detalles-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Venta")
		->setSubject("Informe Venta")
		->setDescription("Informe Venta")
		->setKeywords("Office PHPExcel Tavelli")
		->setCategory("Informe Venta");
			
	$drop_table = " drop table ".$bd['dsparam'].".TEMPprueba ";	
		//echo $drop_table."<br>";
		$res_drop = sqlsrv_query( $conn, $drop_table , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	$drop_table_b = " drop table ".$bd['dsparam'].".TEMPprueba_b ";	
		//echo $drop_table_b."<br>";
		$res_drop_b = sqlsrv_query( $conn, $drop_table_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

	$crear_tabla =" CREATE TABLE ".$bd['dsparam'].".TEMPprueba(Codigo varchar (20) NOt NULL, ";
	$crear_tabla.=" Atributo1 varchar (250),Atributo2 varchar (250),Atributo3 varchar (250),Atributo4 varchar (250),Atributo5 varchar (250),	";
	$crear_tabla.=" Atributo6 varchar (250),Atributo7 varchar (250),Atributo8 varchar (250),Atributo9 varchar (250),Atributo10 varchar (250),	";
	$crear_tabla.=" Atributo11 varchar (250),Atributo12 varchar (250),Atributo13 varchar (250),Atributo14 varchar (250),Atributo15 varchar (250),	";
	$crear_tabla.=" Atributo16 varchar (250),Atributo17 varchar (250),Atributo18 varchar (250),Atributo19 varchar (250),Atributo20 varchar (250))	";
		//echo $crear_tabla."<br><br>";
		$res_tabla = sqlsrv_query( $conn, $crear_tabla , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	 $completar_sabana =" INSERT INTO ".$bd['dsparam'].".TEMPprueba(codigo)    ";
	 $completar_sabana.=" select a.codprod from ".$bd['softland'].".iw_tprod a   ";
	 $completar_sabana.=" left join ".$bd['softland'].".[iw_tprodTVAtrV] b  on a.CodProd = b.Codigo  ";
	 $completar_sabana.=" left join ".$bd['softland'].".[iw_tprodTTAtr] c  on b.CodTat = c.codtat and b.IdMaestro = c.idmaestro ";
		//echo $completar_sabana."<br><br>";
		$res_sabana = sqlsrv_query( $conn, $completar_sabana , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	$obtener_atributos = " select codtat,idmaestro,NombreTipo,DescripcionTipo,Tipo,ValorDef from ".$bd['softland'].".[iw_tprodTTAtr] order by CodTat ";
		//echo $obtener_atributos."<br><br>";
		$res_atributos = sqlsrv_query( $conn, $obtener_atributos , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	
	while ($row_atributos = sqlsrv_fetch_array($res_atributos))
	{
		$update_a =" UPDATE ".$bd['dsparam'].".TEMPprueba SET Atributo".$contador." = C.DescripcionLista FROM dbo.TEMPprueba AS A  ";
		$update_a.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtrT AS B ON A.Codigo collate SQL_Latin1_General_CP1_CI_AI = B.Codigo   ";
		$update_a.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtr AS C ON B.CodTaTe = C.CodTaTe AND B.CodTaT = C.CodTaT ";
		$update_a.=" Where B.CodTat = '".$row_atributos['codtat']."' ";
			//echo $update_a."<br>";
			sqlsrv_query( $conn, $update_a , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		$update_b =" UPDATE ".$bd['dsparam'].".TEMPprueba SET Atributo".$contador."= B.Valor   
			FROM ".$bd['dsparam'].".TEMPprueba AS A   ";
		$update_b.=" INNER JOIN ".$bd['softland'].".iw_tprodTVAtrV AS B ON A.Codigo = B.Codigo     collate SQL_Latin1_General_CP1_CI_AI ";
		$update_b.=" INNER JOIN ".$bd['softland'].".[iw_tprodTTAtr] AS C ON B.IdMaestro = C.IdMaestro AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos['codtat']."' ";
				sqlsrv_query( $conn, $update_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				//echo $update_b."<br><br>";	
					$campos.= "temp.Atributo".$contador." AS atr".$contador.",";
					$contador++;
				
	}

		$campos = substr($campos, 0, -1);
		//echo $campos." : campos_a<br>";
		
		
		$crear_tabla_b =" CREATE TABLE ".$bd['dsparam'].".TEMPprueba_b(Codigo varchar (10) NOt NULL, Atributo1 varchar (250),Atributo2 varchar (250),Atributo3 varchar (250),Atributo4 varchar (250), ";
		$crear_tabla_b.=" Atributo5 varchar (250),Atributo6 varchar (250),Atributo7 varchar (250),Atributo8 varchar (250),Atributo9 varchar (250),Atributo10 varchar (250), ";
		$crear_tabla_b.=" Atributo11 varchar (250),Atributo12 varchar (250),Atributo13 varchar (250),Atributo14 varchar (250),Atributo15 varchar (250),Atributo16 varchar (250), ";
		$crear_tabla_b.=" Atributo17 varchar (250),Atributo18 varchar (250),Atributo19 varchar (250),Atributo20 varchar (250)) ";
			//echo $crear_tabla_b."<br><br>";
			$res_tabla_b = sqlsrv_query( $conn, $crear_tabla_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			
	
		$completar_sabana_b =" INSERT INTO ".$bd['dsparam'].".TEMPprueba_b(codigo) ";
		$completar_sabana_b.=" select a.codaux from ".$bd['softland'].".cwtauxi a  left join ".$bd['softland'].".CWTAuxiTVAtrV b  on a.Codaux = b.Codigo ";
		$completar_sabana_b.=" left join ".$bd['softland'].".CWTAuxiTTAtr c  on b.CodTat = c.codtat and b.IdMaestro = c.idmaestro ";
			//echo $completar_sabana_b."<br><br>";
			$res_sabana_b = sqlsrv_query( $conn, $completar_sabana_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

			$obtener_atributos_b = "select codtat,idmaestro,NombreTipo,DescripcionTipo,Tipo,ValorDef from ".$bd['softland'].".CWTAuxiTTAtr order by CodTat ";
				//echo $obtener_atributos_b."<br><br>";
				$res_atributos_b = sqlsrv_query( $conn, $obtener_atributos_b , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			while ($row_atributos_b = sqlsrv_fetch_array($res_atributos_b))
			{
				$update_c =" UPDATE ".$bd['dsparam'].".TEMPprueba_b SET Atributo".$contador_b." = C.DescripcionLista FROM dsvideojet.dbo.TEMPprueba_b AS A ";
				$update_c.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtrT AS B ON A.Codigo = B.Codigo collate SQL_Latin1_General_CP1_CI_AI ";
				$update_c.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtr AS C ON B.CodTaTe = C.CodTaTe AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos_b['codtat']."' ";
					//echo $update_c."<br><br>";
					sqlsrv_query( $conn, $update_c , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				
				$update_d =" UPDATE ".$bd['dsparam'].".TEMPprueba_b SET Atributo".$contador_b."= B.Valor   FROM dsvideojet.dbo.TEMPprueba_b AS A  ";
				$update_d.=" INNER JOIN ".$bd['softland'].".CWTAuxiTVAtrV AS B ON A.Codigo = B.Codigo   collate SQL_Latin1_General_CP1_CI_AI ";
				$update_d.=" INNER JOIN ".$bd['softland'].".CWTAuxiTTAtr AS C ON B.IdMaestro = C.IdMaestro AND B.CodTaT = C.CodTaT Where B.CodTat = '".$row_atributos_b['codtat']."' ";
					//echo $update_d."<br><br>";
					sqlsrv_query( $conn, $update_d , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
					
				$campos_b.= "temp_b.Atributo".$contador_b." AS btr".$contador_b.",";
				$contador_b++;
	
			}
			//echo "<br><br><br>";
	
	
	$campos_b = substr($campos_b, 0, -1);
	//echo $campos_b."<-- campos_b<br>";

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', "Tipo")
			->setCellValue('B1', "Codigo Cliente")
			->setCellValue('C1', "Razon Social")
			->setCellValue('D1', "Num Doc")
			->setCellValue('E1', "Tipo Documento")
			->setCellValue('F1', "N. Venta")
			->setCellValue('G1', "N. Guia")
			->setCellValue('H1', "Producto")
			->setCellValue('I1', "Descripcion")
			->setCellValue('K1', "Detalle Producto")
			->setCellValue('J1', "Grupo/KIT")
			->setCellValue('L1', "Grupo 2")
			->setCellValue('M1', "Familia")
			->setCellValue('N1', "Cantidad")
			->setCellValue('O1', "Valor Unitario")
			->setCellValue('P1', "Valor Total (NETO)")
			->setCellValue('Q1', "Emision")
			->setCellValue('R1', "Tipo Cambio")
			->setCellValue('S1', "Costo Local Unit")
			->setCellValue('T1', "Costo Total Prom")
			->setCellValue('U1', "Costo Local Unit Al Dia")
			->setCellValue('V1', "Costo Total Prom Al Dia")			
			->setCellValue('W1', "Costo Estandar USD")
			->setCellValue('X1', "Costo STD CLP")
			->setCellValue('Y1', "Freight")
			->setCellValue('Z1', "C&D")
			->setCellValue('AA1', "Costo STD + Freight + C&D")
			->setCellValue('AB1', "Vendedor")
			->setCellValue('AC1', "Valor Total USD")
			->setCellValue('AD1', "Categoria de Cliente")
			->setCellValue('AE1', "SalesForce ID")
			->setCellValue('AF1', "Emision (Mes/Ano)")
			->setCellValue('AG1', "Quarter")
			->setCellValue('AH1', "Serie")
			->setCellValue('AI1', "Partida")
			->setCellValue('AJ1', "Mes")
			->setCellValue('AK1', "MODEL CLASS")
			->setCellValue('AL1', "PRODUCT TYPE")
			->setCellValue('AM1', "UNIDAD MEDIA")
			->setCellValue('AN1', "MODEL GROUP")
			->setCellValue('AO1', "TRANSFER PRICE")
			->setCellValue('AP1', "TPM")
			->setCellValue('AQ1', "CTA HP")
			->setCellValue('AR1', "NEW/OLD")
			->setCellValue('AS1', "Cuenta Activo")
			->setCellValue('AT1', "Cuenta Ventas")
			->setCellValue('AU1', "Cuenta Gastos")
			->setCellValue('AV1', "Cuenta Costo");
	
	$query_cabecera =" select distinct gm.linea, gs.codaux,gs.nvnumero AS nventa,cwt.NomAux,gs.folio,gm.CodProd,iwt.esconfig as eskit,gm.kit, ".$bd['dsparam'].".grupoKit(gm.KIT) AS deskit ,iwt.desprod, ";
	$query_cabecera.=" grupo.DesGrupo, iwt.CodGrupo, iwt.CodSubGr, subGrupo.DesSubGr,";
	$query_cabecera.=" gm.CantFacturada,gm.preunimb, ";
	$query_cabecera.=" Round(gm.TotLinea-((gm.TotLinea * gs.porcdesc01)/100),0) as TotLinea,gs.fecha,'' as TipoCambio, ".$bd['dsparam'].".costoprom(gm.CodProd,'".$fecha_hoy."') as CostoProm, ";
	$query_cabecera.=" (gm.cantFacturada * ".$bd['dsparam'].".costoprom(gm.CodProd,'".$fecha_hoy."')  ) as costoTotalProm, ";
	$query_cabecera.=" ".$bd['dsparam'].".costoprom(gm.CodProd,gs.fecha) as CostoPromAlDia,  ";
	$query_cabecera.=" (gm.cantFacturada * ".$bd['dsparam'].".costoprom(gm.CodProd,gs.fecha) ) as costoTotalPromAlDia, ";	
	$query_cabecera.=" gm.Partida, gm.Pieza, descCl.CatDes,";
	$query_cabecera.=" gs.CodVendedor, vendedor.VenDes, ";
	$query_cabecera.=" cast(gm.DetProd as varchar(max)) AS DetProd, ";
	$query_cabecera.=" 	case  ";
	$query_cabecera.=" 	when gs.SubTipoDocto='A' then 'Afecto' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'E' then 'Exento' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'x' then 'Exportación' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'T' then 'Docto Electrónico' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'L' then 'Liquidacion' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'N' then 'Liquidacion Factura' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'O' then 'Liquidacion electronica' ";
	$query_cabecera.=" 	when gs.SubTipoDocto = 'C' then 'Documento Interno de Venta' ";
	$query_cabecera.=" 	else ";
	$query_cabecera.=" 	'--' ";
	$query_cabecera.=" 	end ";
	$query_cabecera.=" 	AS tipoDocto, ";
	$query_cabecera.=" iwt.ctaactivo as cuentaActivo, iwt.Ctaventas as cuentaVentas, iwt.ctagastos as cuentaGastos, ";
	$query_cabecera.=" iwt.ctaCosto as cuentaCosto, iwt.ctadevolucion as cuentaDevolucion , ";
	$query_cabecera.= $campos.",";
	$query_cabecera.= $campos_b."  ";
	$query_cabecera.=" from ".$bd['softland'].".iw_gsaen gs  ";
	//$query_cabecera.=" left join ".$bd['softland'].".iw_gmovi gm on gs.tipo =gm.tipo and gs.nroint = gm.nroint ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_gmovi gm on gs.tipo =gm.tipo and gs.nroint = gm.nroint AND gs.CodAux = gm.CodAux ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtauxi cwt on gs.codaux = cwt.codaux ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tprod iwt on iwt.codprod = gm.codprod ";
	$query_cabecera.=" left join ".$bd['dsparam'].".TEMPprueba temp ON iwt.codprod = temp.Codigo collate Modern_Spanish_CI_AS ";
	$query_cabecera.=" left join ".$bd['dsparam'].".tempPrueba_b temp_b ON temp_b.Codigo = cwt.CodAux collate Modern_Spanish_CI_AS  ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtvend vendedor ON vendedor.VenCod = gs.CodVendedor ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtcvcl cl ON cl.CodAux = gs.CodAux ";
	$query_cabecera.=" left join ".$bd['softland'].".cwtcgau descCl ON descCl.CatCod = cl.CatCli ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tgrupo grupo ON grupo.CodGrupo = iwt.CodGrupo ";
	$query_cabecera.=" left join ".$bd['softland'].".iw_tsubgr subGrupo ON subGrupo.CodSubGr = iwt.CodSubGr ";
	$query_cabecera.=" WHERE  gs.tipo IN ('F','N','B','D') ";
	$query_cabecera.=" AND gs.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
	//$query_cabecera.=" AND gm.linea IS NOT NULL ";
	$query_cabecera.=" 	AND gs.EnMantencion = '0' ";
		//echo $query_cabecera;
		echo "<br>";
		//echo $query_cabecera."<br><br>";

	$res_cabecera = sqlsrv_query( $conn, $query_cabecera , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row_datos = sqlsrv_fetch_array($res_cabecera))
	{

			$query_nguia = "  select TOP 1 folio from ".$bd['softland'].".iw_gsaen where nvnumero = '".$row_datos['nventa']."' and tipo = 'S'";
			//echo $query_nguia."<br>";
			$res_nguia = sqlsrv_query( $conn, $query_nguia , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$folioExcel ='';
			while ($row_guia = sqlsrv_fetch_array($res_nguia))
			{
				$folioExcel = $row_guia['folio'];
				//echo $folioExcel." : <---folio<br>";
			}

					$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$e, "")
				->setCellValue('B'.$e, $row_datos['codaux'])
				->setCellValue('C'.$e, $row_datos['NomAux'])
				->setCellValue('D'.$e, $row_datos['folio'])
				->setCellValue('E'.$e, $row_datos['tipoDocto'])
				->setCellValue('F'.$e, $row_datos['nventa'])
				->setCellValue('G'.$e, $folioExcel)
				->setCellValue('H'.$e, $row_datos['CodProd'])
				->setCellValue('I'.$e, $row_datos['desprod'])				
				->setCellValue('K'.$e, $row_datos['DetProd'])
				->setCellValue('J'.$e, $row_datos['deskit'])
				->setCellValue('L'.$e, $row_datos['DesGrupo'])
				->setCellValue('M'.$e, $row_datos['DesSubGr'])
				->setCellValue('N'.$e, round($row_datos['CantFacturada']))
				->setCellValue('O'.$e, round($row_datos['preunimb']))
				->setCellValue('P'.$e, $row_datos['TotLinea'])
				->setCellValue('Q'.$e, date_format($row_datos['fecha'],'d/m/Y'))
				->setCellValue('R'.$e, "")
				->setCellValue('S'.$e, $row_datos['CostoProm'])
				->setCellValue('T'.$e, $row_datos['costoTotalProm'])
				->setCellValue('U'.$e, $row_datos['CostoPromAlDia'])
				->setCellValue('V'.$e, $row_datos['costoTotalPromAlDia'])
				->setCellValue('W'.$e, $row_datos['atr1'])
				->setCellValue('X'.$e, "")
				->setCellValue('Y'.$e, "")
				->setCellValue('Z'.$e, "")
				->setCellValue('AA'.$e, "")
				->setCellValue('AB'.$e, $row_datos['VenDes'])
				->setCellValue('AC'.$e, "0")
				->setCellValue('AD'.$e, $row_datos['CatDes'])
				->setCellValue('AE'.$e, $row_datos['btr1'])
				->setCellValue('AF'.$e, date_format($row_datos['fecha'],'Ym'))
				->setCellValue('AG'.$e, trimestreMes(date_format($row_datos['fecha'],'m')))
				->setCellValue('AH'.$e, $row_datos['Partida'])
				->setCellValue('AI'.$e, $row_datos['Pieza'])
				->setCellValue('AJ'.$e, nombreMes(date_format($row_datos['fecha'],'m')))
				->setCellValue('AK'.$e, $row_datos['atr4'])
				->setCellValue('AL'.$e, $row_datos['atr10'])
				->setCellValue('AM'.$e, $row_datos['atr2'])
				->setCellValue('AN'.$e, $row_datos['atr3'])
				->setCellValue('AO'.$e, $row_datos['atr5'])
				->setCellValue('AP'.$e, $row_datos['atr6'])
				->setCellValue('AQ'.$e, $row_datos['atr7'])
				->setCellValue('AR'.$e, $row_datos['atr8'])
				->setCellValue('AS'.$e, $row_datos['cuentaActivo'])
				->setCellValue('AT'.$e, $row_datos['cuentaVentas'])
				->setCellValue('AU'.$e, $row_datos['cuentaGastos'])
				->setCellValue('AV'.$e, $row_datos['cuentaCosto']);
				
				$e++;
	}
	
	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	

}

function informeTrazabilidad($fecdes, $fechas)
{
    //echo $fecdes." -- ".$fechas;
    include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	
	$fecha_hoy = date("Y/m/d");
	$e = 2;
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-Trazabilidad-Guias-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Trazabilidad Guias")
		->setSubject("Informe Trazabilidad Guias")
		->setDescription("Informe Trazabilidad Guias")
		->setKeywords("Office PHPExcel VideoJet")
		->setCategory("Informe Trazabilidad Guias");
    
    
 $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', "Cod Auxiliar")
			->setCellValue('B1', "Nom Auxiliar")
			->setCellValue('C1', "Rut Auxiliar")
			->setCellValue('D1', "Cod Bodega")
            ->setCellValue('E1', "Desc Bodega")
			->setCellValue('F1', "Cod Producto")
			->setCellValue('G1', "Desc Producto")
			->setCellValue('H1', "Tipo")
			->setCellValue('I1', "Estado")
			->setCellValue('J1', "Fecha")
			->setCellValue('K1', "Guia")
			->setCellValue('L1', "Nota Venta")
			->setCellValue('M1', "Factura")
			->setCellValue('N1', "Cliente Consumo")
			->setCellValue('O1', "Cantidad Ingresada")
			->setCellValue('P1', "Cantidad Despachada")
			->setCellValue('Q1', "Precio Unitario")
			->setCellValue('R1', "Total Linea")
			->setCellValue('S1', "Concepto")
			->setCellValue('T1', "Desc Concepto")
			->setCellValue('U1', "Glosa")
			->setCellValue('V1', "Partida")
			->setCellValue('W1', "Pieza")			
			->setCellValue('X1', "Fecha Vencimiento")
			->setCellValue('Z1', "Costo")
			->setCellValue('AA1', "Ano Comprobante")
			->setCellValue('AB1', "Numero Comprobante")			
			->setCellValue('AC1', "Numero Comprobante Ventas")
			->setCellValue('AD1', "Fecha Comprobante")
			->setCellValue('AE1', "Fecha Folio - Nuevo Campo")
			;	   

    
$sql =" select detalle.codaux,auxi.nomaux, auxi.RutAux,detalle.CodBode, bodega.DesBode, detalle.CodProd, prod.DesProd, ";
$sql.=" cabecera.Tipo, cabecera.Estado, detalle.Fecha, cabecera.Folio ";
$sql.=" , ".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo) as NvNumero ";
$sql.=" , ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) as factura ";
$sql.=" , detalle.CantIngresada,detalle.CantDespachada,detalle.PreUniMB,detalle.TotLinea,cabecera.Concepto, concepto.DesCodDr,cabecera.Glosa ";
$sql.=" , detalle.Partida, detalle.Pieza, detalle.FechaVencto,".$bd['dsparam'].".costoprom(detalle.CodProd,detalle.Fecha) as CostoProm ,";
$sql.=" cabecera.cpbanocostos, cabecera.cpbnumcostos, ";
$sql.=" (select CpbNumVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) ) as numeroComprobanteVentas, ";
$sql.=" (select cpbfec from ".$bd['softland'].".cwcpbte where cpbano =(select CpbAnoVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) )  ";
$sql.=" AND cpbnum = (select CpbNumVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) )) as fechacpbte ";
$sql.=" FROM ".$bd['softland'].".iw_gmovi detalle ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_gsaen cabecera ON detalle.Tipo = cabecera.Tipo AND detalle.NroInt = cabecera.NroInt ";
$sql.=" LEFT JOIN ".$bd['softland'].".cwtauxi auxi ON detalle.CodAux = auxi.CodAux ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tbode bodega ON bodega.CodBode = detalle.CodBode ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tprod prod ON prod.CodProd = detalle.CodProd ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_cocod concepto ON concepto.Concepto = cabecera.Concepto AND concepto.TipoDoc = cabecera.tipo ";
$sql.=" WHERE detalle.Tipo IN ('s','e')  ";
$sql.=" AND cabecera.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
$sql.=" order by cabecera.Tipo, cabecera.folio ";

   //echo $sql;
   $res_cabecera = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row_datos = sqlsrv_fetch_array($res_cabecera)) 
    {
		if($row_datos['Concepto'] == '7' || $row_datos['Concepto'] == '8' )
		{
			$resultado = 'SI';
		}

		else
		{
			$resultado = '';
		}
        $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$e, $row_datos['codaux'])
				->setCellValue('B'.$e, $row_datos['nomaux'])
				->setCellValue('C'.$e, $row_datos['RutAux'])
				->setCellValue('D'.$e, $row_datos['CodBode'])
				->setCellValue('E'.$e, $row_datos['DesBode'])
				->setCellValue('F'.$e, $row_datos['CodProd'])
				->setCellValue('G'.$e, $row_datos['DesProd'])
				->setCellValue('H'.$e, $row_datos['Tipo'])
				->setCellValue('I'.$e, $row_datos['Estado'])				
				->setCellValue('J'.$e, date_format($row_datos['Fecha'],'d/m/Y'))
				->setCellValue('K'.$e, $row_datos['Folio'])
				->setCellValue('L'.$e, $row_datos['NvNumero'])
				->setCellValue('M'.$e, $row_datos['factura'])
				->setCellValue('N'.$e, $resultado)
				->setCellValue('O'.$e, $row_datos['CantIngresada'])
				->setCellValue('P'.$e, $row_datos['CantDespachada'])
				->setCellValue('Q'.$e, $row_datos['PreUniMB'])
				->setCellValue('R'.$e, $row_datos['TotLinea'])
				->setCellValue('S'.$e, $row_datos['Concepto'])
				->setCellValue('T'.$e, $row_datos['DesCodDr'])
				->setCellValue('U'.$e, $row_datos['Glosa'])
				->setCellValue('V'.$e, $row_datos['Partida'])
				->setCellValue('W'.$e, $row_datos['Pieza'])
				->setCellValue('X'.$e, utf8_encode(date_format($row_datos['FechaVencto'],'d/m/Y')))
				->setCellValue('Z'.$e, $row_datos['CostoProm'])
				->setCellValue('AA'.$e, $row_datos['cpbanocostos'])
				->setCellValue('AB'.$e, $row_datos['cpbnumcostos'])
				->setCellValue('AC'.$e, $row_datos['numeroComprobanteVentas'])
				->setCellValue('AD'.$e, utf8_encode(date_format($row_datos['fechacpbte'],'d/m/Y')));
				//CostoProm
				
			$queryNuevaFecha = "SELECT fecha AS fechaNueva FROM ".$bd['softland'].".iw_gsaen WHERE folio = '".$row_datos['factura']."' ";
			//secho $queryNuevaFecha."<br>";
			$res_fecha = sqlsrv_query( $conn, $queryNuevaFecha , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
				while ($row_fecha = sqlsrv_fetch_array($res_fecha)) 
				{
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('AE'.$e, utf8_encode(date_format($row_fecha['fechaNueva'],'d/m/Y')));
				}
				
				
        $e++;
    }
    	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					//print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	
    
}

function listarCuentas()
{
	  include('includes/conexion.php');
	  $queryCuentas = "SELECT PCCODI, PCDESC FROM ".$bd['softland'].".cwpctas WHERE PCAUXI = 'S' and PCCDOC = 'S' ";
		//echo $queryCuentas;
		$salida = "";
		$i =0;
	$resCuentas = sqlsrv_query( $conn, $queryCuentas , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		while ($rowCuentas = sqlsrv_fetch_array($resCuentas)) 
		{
			if($i == 0)
			{
				$salida.='<div class="row">';
			}
			$salida.='<label class="col-sm-2">
				<input type="checkbox" name="cuenta[]" id="cuenta" value="'.$rowCuentas['PCCODI'].'">&nbsp;'.$rowCuentas['PCCODI'].'
			</label>';
			
			if($i % 4 == 0 && $i >0)
			{
				$salida.="</div>";
				//echo $i;
				$i =-1;
			}
			$i++;
		}
		echo $salida;
		
}

function informeSeguimiento($fecha, $cuentas)
{
	//echo $fecha."    ////    ".$cuentas;
	$fechaQuery = "'".$fecha."'";
	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');
	$fecha_hoy = date("Y/m/d");
	$e = 2;
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-Seguimiento-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Seguimiento Guias")
		->setSubject("Informe Seguimiento Guias")
		->setDescription("Informe Seguimiento Guias")
		->setKeywords("Office PHPExcel VideoJet")
		->setCategory("Informe Seguimiento Guias");
    
    
 $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', "Numero Doc")
			->setCellValue('B1', "Tipo Doc")
			->setCellValue('C1', "Cuota")
			->setCellValue('D1', "Cod Aux")
            ->setCellValue('E1', "Nombre Aux")
			->setCellValue('F1', "Cod Cuenta")
			->setCellValue('G1', "Desc Cuenta")
			->setCellValue('H1', "Saldo")
			->setCellValue('I1', "Fecha Vencimiento")
			->setCellValue('J1', "Fecha Ingresada")
			->setCellValue('K1', "Dias Vencidos")
			->setCellValue('L1', "Seguimiento")
			->setCellValue('M1', "Cod Compromiso")
			->setCellValue('N1', "Desc Compromiso");	
	
$query =" SELECT  ";
$query.=" saldo.MovNumDocRef, saldo.MovTipDocRef, saldo.CuotaRef, saldo.CodAux, auxi.NomAux,  ";
$query.=" saldo.PctCod, cuentas.PCDESC, (saldo.debe - saldo.haber) AS saldo, saldo.FV, ";
$query.=' Datediff("d",saldo.fv,convert(datetime,'.$fechaQuery.',103)) AS diasVencidos, ';
$query.=" cobranza.conversa as seguimiento,  compromiso.codcomp, compromiso.descomp ";
$query.=" FROM videojet.softland.CWDocSaldosFV saldo ";
$query.=" LEFT JOIN videojet.softland.xwseguimiento seguimiento ON seguimiento.NumDoc = saldo.MovNumDocRef ";
$query.=" LEFT JOIN videojet.softland.xwttcomp compromiso ON compromiso.codcomp = seguimiento.CodComp ";
$query.=" LEFT JOIN videojet.softland.cwtauxi auxi ON auxi.CodAux = saldo.CodAux ";
$query.=" LEFT JOIN videojet.softland.cwpctas cuentas ON cuentas.PCCODI = saldo.PctCod ";
$query.=" LEFT JOIN videojet.softland.xwcobranza cobranza ON cobranza.ClaveCob = seguimiento.ClaveCob ";
$query.=" WHERE PctCod IN (".$cuentas.")";

//echo $query."<br><br>";
//echo $cuentas."<br><br>";
$LETA=A;
$indice = 2;

$resSeguimiento = sqlsrv_query( $conn, $query , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($rowSeguimiento = sqlsrv_fetch_array($resSeguimiento)) 
	{
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['MovNumDocRef']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['MovTipDocRef']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['CuotaRef']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['CodAux']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, $rowSeguimiento['NomAux']);
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['PctCod']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['PCDESC']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['saldo']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode(date_format($rowSeguimiento['FV'],'d/m/Y')));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, str_replace('-', '/', $fecha));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['diasVencidos']));
			$LETA++;

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['seguimiento']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['codcomp']));
			$LETA++;
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($LETA.$indice, utf8_encode($rowSeguimiento['descomp']));
			$LETA++;			
		$LETA = A;
		$indice++;
	}		
	
    	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					//print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	
}
/*
function informeTrazabilidad($fecdes, $fechas)
{
    //echo $fecdes." -- ".$fechas;
    include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	
	$fecha_hoy = date("Y/m/d");
	$e = 2;
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-Trazabilidad-Guias-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Trazabilidad Guias")
		->setSubject("Informe Trazabilidad Guias")
		->setDescription("Informe Trazabilidad Guias")
		->setKeywords("Office PHPExcel VideoJet")
		->setCategory("Informe Trazabilidad Guias");
    
    
 $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', "Cod Auxiliar")
			->setCellValue('B1', "Nom Auxiliar")
			->setCellValue('C1', "Rut Auxiliar")
			->setCellValue('D1', "Cod Bodega")
            ->setCellValue('E1', "Desc Bodega")
			->setCellValue('F1', "Cod Producto")
			->setCellValue('G1', "Desc Producto")
			->setCellValue('H1', "Tipo")
			->setCellValue('I1', "Estado")
			->setCellValue('J1', "Fecha")
			->setCellValue('K1', "Guia")
			->setCellValue('L1', "Nota Venta")
			->setCellValue('M1', "Factura")
			->setCellValue('N1', "Cliente Consumo")
			->setCellValue('O1', "Cantidad Ingresada")
			->setCellValue('P1', "Cantidad Despachada")
			->setCellValue('Q1', "Precio Unitario")
			->setCellValue('R1', "Total Linea")
			->setCellValue('S1', "Concepto")
			->setCellValue('T1', "Desc Concepto")
			->setCellValue('U1', "Glosa")
			->setCellValue('V1', "Partida")
			->setCellValue('W1', "Pieza")			
			->setCellValue('X1', "Fecha Vencimiento")
			->setCellValue('Z1', "Costo")
			->setCellValue('AA1', "Ano Comprobante")
			->setCellValue('AB1', "Numero Comprobante")			
			->setCellValue('AC1', "Numero Comprobante Ventas")
			->setCellValue('AD1', "Fecha Comprobante");	   

    
$sql =" select detalle.codaux,auxi.nomaux, auxi.RutAux,detalle.CodBode, bodega.DesBode, detalle.CodProd, prod.DesProd, ";
$sql.=" cabecera.Tipo, cabecera.Estado, detalle.Fecha, cabecera.Folio ";
$sql.=" , ".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo) as NvNumero ";
$sql.=" , ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) as factura ";
$sql.=" , detalle.CantIngresada,detalle.CantDespachada,detalle.PreUniMB,detalle.TotLinea,cabecera.Concepto, concepto.DesCodDr,cabecera.Glosa ";
$sql.=" , detalle.Partida, detalle.Pieza, detalle.FechaVencto,".$bd['dsparam'].".costoprom(detalle.CodProd,detalle.Fecha) as CostoProm ,";
$sql.=" cabecera.cpbanocostos, cabecera.cpbnumcostos, ";
$sql.=" (select CpbNumVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) ) as numeroComprobanteVentas, ";
$sql.=" (select cpbfec from ".$bd['softland'].".cwcpbte where cpbano =(select CpbAnoVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) )  ";
$sql.=" AND cpbnum = (select CpbNumVentas from ".$bd['softland'].".iw_gsaen  where tipo = 'F'  ";
$sql.=" AND folio = ".$bd['dsparam'].".facturaTrazabilidad (".$bd['dsparam'].".[NvNumeroTrazabilidad] (cabecera.Folio, cabecera.Tipo)) )) as fechacpbte ";
$sql.=" FROM ".$bd['softland'].".iw_gmovi detalle ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_gsaen cabecera ON detalle.Tipo = cabecera.Tipo AND detalle.NroInt = cabecera.NroInt ";
$sql.=" LEFT JOIN ".$bd['softland'].".cwtauxi auxi ON detalle.CodAux = auxi.CodAux ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tbode bodega ON bodega.CodBode = detalle.CodBode ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_tprod prod ON prod.CodProd = detalle.CodProd ";
$sql.=" LEFT JOIN ".$bd['softland'].".iw_cocod concepto ON concepto.Concepto = cabecera.Concepto AND concepto.TipoDoc = cabecera.tipo ";
$sql.=" WHERE detalle.Tipo IN ('s','e')  ";
$sql.=" AND cabecera.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
$sql.=" order by cabecera.Tipo, cabecera.folio ";

   //echo $sql;
   $res_cabecera = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	while ($row_datos = sqlsrv_fetch_array($res_cabecera)) 
    {
		if($row_datos['Concepto'] == '7' || $row_datos['Concepto'] == '8' )
		{
			$resultado = 'SI';
		}

		else
		{
			$resultado = '';
		}
        $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$e, $row_datos['codaux'])
				->setCellValue('B'.$e, $row_datos['nomaux'])
				->setCellValue('C'.$e, $row_datos['RutAux'])
				->setCellValue('D'.$e, $row_datos['CodBode'])
				->setCellValue('E'.$e, $row_datos['DesBode'])
				->setCellValue('F'.$e, $row_datos['CodProd'])
				->setCellValue('G'.$e, $row_datos['DesProd'])
				->setCellValue('H'.$e, $row_datos['Tipo'])
				->setCellValue('I'.$e, $row_datos['Estado'])				
				->setCellValue('J'.$e, date_format($row_datos['Fecha'],'d/m/Y'))
				->setCellValue('K'.$e, $row_datos['Folio'])
				->setCellValue('L'.$e, $row_datos['NvNumero'])
				->setCellValue('M'.$e, $row_datos['factura'])
				->setCellValue('N'.$e, $resultado)
				->setCellValue('O'.$e, $row_datos['CantIngresada'])
				->setCellValue('P'.$e, $row_datos['CantDespachada'])
				->setCellValue('Q'.$e, $row_datos['PreUniMB'])
				->setCellValue('R'.$e, $row_datos['TotLinea'])
				->setCellValue('S'.$e, $row_datos['Concepto'])
				->setCellValue('T'.$e, $row_datos['DesCodDr'])
				->setCellValue('U'.$e, $row_datos['Glosa'])
				->setCellValue('V'.$e, $row_datos['Partida'])
				->setCellValue('W'.$e, $row_datos['Pieza'])
				->setCellValue('X'.$e, utf8_encode(date_format($row_datos['FechaVencto'],'d/m/Y')))
				->setCellValue('Z'.$e, $row_datos['CostoProm'])
				->setCellValue('AA'.$e, $row_datos['cpbanocostos'])
				->setCellValue('AB'.$e, $row_datos['cpbnumcostos'])
				->setCellValue('AC'.$e, $row_datos['numeroComprobanteVentas'])
				->setCellValue('AD'.$e, utf8_encode(date_format($row_datos['fechacpbte'],'d/m/Y')));
				//CostoProm
        $e++;
    }
    	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					//print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	
    
}

*/

function infGuias($fecdes,$fechas){
	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	
	
	$fecdes = str_replace("-","/",$fecdes);
	$fechas = str_replace("-","/",$fechas);
	//echo $fecdes." //".$fechas."<br><br>";
	$fecha_hoy = date("Y/m/d");
	$e = 2;
	$letra = "A";
	$fechaExcel = date('dmY-His');
	$fname = "informes/Informe-Guias-".$fechaExcel.".xls";
	
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2015")
		->setLastModifiedBy("Disofi 2015")
		->setTitle("Informe Guias")
		->setSubject("Informe Guias")
		->setDescription("Informe Guias")
		->setKeywords("Office PHPExcel Videojet")
		->setCategory("Informe Guias");
		//echo "<br><br><br>";
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	
	$styleArray = array(
    'font'  => array(
        'bold'  => true
        //'color' => array('rgb' => 'FF0000'),
        //'size'  => 15,
        //'name'  => 'Verdana'
    ));

	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	
	$campos_b = substr($campos_b, 0, -1);
	//echo $campos_b."<-- campos_b<br>";
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Folio")); $letra++; //A
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Codigo Producto")); $letra++; //B
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Descripción Producto")); $letra++; //C
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Codigo Aux")); $letra++; //D
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Nombre Aux")); $letra++; //E
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Codigo Vendedor")); $letra++; //F
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Nombre Vendedor")); $letra++; //G
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Fecha")); $letra++; //H
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Precio Unitario")); $letra++; //I
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Orden")); $letra++; //J
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Factura")); $letra++; //K
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Glosa")); $letra++; //L
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Codigo Bodega")); $letra++; //M
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Descripcion Bodega")); $letra++; //N
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue($letra."1", strtoupper("Cantidad Despachada")); $letra++; //O
	$objPHPExcel->getActiveSheet()->getStyle($letra.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);			
	
	$letra++;
			
	$query_cabecera ="select gsaen.folio, gmovi.codprod, prod.DesProd, gsaen.codaux,"; 
	$query_cabecera .="auxi.NomAux, gsaen.CodVendedor, vend.VenDes, gsaen.Fecha,";
	$query_cabecera .="gmovi.PreUniMB, gsaen.orden, gsaen.Factura, gsaen.Glosa, ";
	$query_cabecera .="gsaen.CodBode, bode.DesBode,gmovi.CantDespachada ";
	$query_cabecera .="from ".$bd['softland'].".iw_gsaen gsaen";
	$query_cabecera .=" LEFT JOIN ".$bd['softland'].".iw_gmovi gmovi";
	$query_cabecera .=" ON gsaen.NroInt =  gmovi.NroInt ";
	$query_cabecera .=" AND gsaen.Tipo = gmovi.Tipo AND gsaen.CodBode = gmovi.CodBode";
	$query_cabecera .=" LEFT JOIN ".$bd['softland'].".iw_tprod prod ON gmovi.codprod = prod.CodProd";
	$query_cabecera .=" LEFT JOIN ".$bd['softland'].".cwtauxi auxi ON gsaen.codaux = auxi.CodAux ";
	$query_cabecera .=" LEFT JOIN ".$bd['softland'].".cwtvend vend ON gsaen.CodVendedor = vend.VenCod";
	$query_cabecera .=" LEFT JOIN ".$bd['softland'].".iw_tbode bode ON bode.CodBode = gsaen.CodBode";
	$query_cabecera .=" where gsaen.tipo = 'S' and gsaen.concepto = '01'";
	$query_cabecera .=" and gsaen.fecha between convert(datetime,'".$fecdes."',103) AND convert(datetime,'".$fechas."',103) ";
	echo "<br>";
	echo $query_cabecera."<br><br>";
	$letra = "A";
	$res_cabecera = sqlsrv_query( $conn, $query_cabecera , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		
	while ($row_datos = sqlsrv_fetch_array($res_cabecera))
	{
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$e, $row_datos['folio']); $letra++; //A
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$e, $row_datos['codprod']); $letra++; //B
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['DesProd']); $letra++; //C
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['codaux']); $letra++; //D
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['NomAux']); $letra++;  //E
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['CodVendedor']); $letra++; //F
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['VenDes']); $letra++; //G
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, date_format($row_datos['Fecha'],'d/m/Y')); $letra++; //H
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['PreUniMB']); $letra++; //I
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['orden']); $letra++; //J
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['Factura']); $letra++;	//K	
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['Glosa']); $letra++;//L
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['CodBode']); $letra++;//M
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['DesBode']); $letra++;//N		
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($letra.$e, $row_datos['CantDespachada']); $letra++;//O
		
		$letra = "A";
		$letra = "A";			
		$e++;	
	}
	
	require_once ('includes/PHPExcel/IOFactory.php');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($fname);
	print $salida;
	print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';	
}

function informeMKT ($fecdes, $fechas)
{
	include('includes/conexion.php');
	require_once('includes/PHPExcel.php');	

	$fecha_hoy = date("Y/m/d");
	$contador = 1;
	$contador_b = 1;
	$campos = "";
	$campos_b = "";
	$fechaExcel   = date('dmY-His');
	$fname = "informes/Informe-MKT-".$fechaExcel.".xls";
	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Disofi 2019")
		->setLastModifiedBy("Disofi 2019")
		->setTitle("Informe MKT")
		->setSubject("Informe MKT")
		->setDescription("Informe MKT")
		->setKeywords("Office PHPExcel Videojet")
		->setCategory("Informe MKT");
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', "Tipo")
			->setCellValue('B1', "NroFe")
			->setCellValue('C1', "CodAux")
			->setCellValue('D1', "Proveedor")
			->setCellValue('E1', "OC")
			->setCellValue('F1', "DetalleProducto")
			->setCellValue('G1', "Fecha OC")
			->setCellValue('H1', "CC")
			->setCellValue('I1', "NombreCC")
			->setCellValue('J1', "Neto")
			->setCellValue('K1', "Total");
				
			$qry = "SELECT
			isnull(pwd.TtdCod,'')as TtdCod
			,isnull(pwd.NumDoc,0)as NumDoc
			,oc.CodAux,cwt.NomAux
			,oc.NumOC,ocd.DetProd
			,oc.FechaOC,oc.CodiCC
			,isnull(cwtc.DescCC,'')as DescCC
			,oc.NetoAfecto
			,oc.ValorTotOc,ocd.NumInterOC
			FROM ".$bd['softland'].".owordencom oc join ".$bd['softland'].".owordendet ocd
			on oc.NumInterOC = ocd.NumInterOC
			left join ".$bd['softland'].".cwtauxi cwt on cwt.CodAux = oc.CodAux
			left join ".$bd['softland'].".cwtccos cwtc on cwtc.CodiCC = oc.CodiCC
			left join ".$bd['softland'].".pwdoccom pwd on pwd.NumOC = oc.NumInterOC
			WHERE oc.FechaOC
			BETWEEN convert(datetime,'".$fecdes."',103)
			AND convert(datetime,'".$fechas."',103) order by FechaOC";
			
			$res_cabecera = sqlsrv_query( $conn, $qry, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
			$e=2;
			set_time_limit(0);
			while ($row_datos = sqlsrv_fetch_array($res_cabecera))
			{
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$e, $row_datos['TtdCod'])				
				->setCellValue('B'.$e, $row_datos['NumDoc'])
				->setCellValue('C'.$e, $row_datos['CodAux'])
				->setCellValue('D'.$e, $row_datos['NomAux'])
				->setCellValue('E'.$e, $row_datos['NumOC'])
				->setCellValue('F'.$e, $row_datos['DetProd'])
				->setCellValue('G'.$e, date_format($row_datos['FechaOC'], 'd/m/Y'))
				->setCellValue('H'.$e, $row_datos['CodiCC'])
				->setCellValue('I'.$e, $row_datos['DescCC'])
				->setCellValue('J'.$e, $row_datos['NetoAfecto'])
				->setCellValue('K'.$e, $row_datos['ValorTotOc']);
				$e++;
				if (fmod($e,2))
					{
						$objPHPExcel->setActiveSheetIndex()->getStyle('A'.$e.':K'.$e)->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setARGB('FFE8E5E5');
					}
	}
	$borders = array
			("borders" => array
			("allborders" => array
				("style" => PHPExcel_Style_BORDER::BORDER_THIN,"color" => array
						("argb" => "00000000"),
				),
			),
			);
		
			$objPHPExcel->getActiveSheet()->getStyle("A1:K".($e-1))->applyFromArray($borders);
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	
	require_once ('includes/PHPExcel/IOFactory.php');
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save($fname);
					//print $salida;
					print '<a href="'.$fname.'" class="descarga"><img src="images/excel.png" /> &nbsp; DESCARGAR ARCHIVO PARA EXCEL</a>';
}

?>