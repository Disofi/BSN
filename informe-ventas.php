<?php
//include('includes/funciones.php');
//include('includes/funciones_informes.php');
//$_SESSION['_pagina'] = basename($_SERVER['PHP_SELF']);
//$mandante = $_SESSION['admin']['id_mandante'];
?>

<div class="titulo_pagina"><h2 class="col-md-12">Informes &gt; Ventas</h2></div>

<h3 class="productsForm col-md-12 borde_gris2">Informe Ventas</h3>

<form name="form_informe_detalle" method="post" class="col-md-12" id="form_informe_detalle" action="index.php?mod=informe-ventas-ver">
   <div class="row">
        <label class="col-sm-2">Fecha Desde *</label>
        <div class="col-sm-2"><input type="text" name="fecdes" id="fecdes" class="datePicker" tabindex="3" readonly="readonly"></div>
        <label class="col-sm-2">Fecha Hasta *</label>
        <div class="col-sm-2"><input type="text" name="fechas" id="fechas" class="datePicker" tabindex="4" readonly="readonly"></div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <input name="seccion" type="hidden" id="seccion" value="informe_detalle" />
            <input type="submit" id="enviar"  class="float_right margin_top_10" value="Consultar" onclick="return validaform();" tabindex="5" />
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
			<label class="col-sm-12">
			<!--NOTA: ANTES DE EMITIR ESTE REPORTE RECORDAR REALIZAR PROCESO DE ACTUALIZACION DE COSTOS PROMEDIO EN SOFTLAND -->
			</label>
        </div>
    </div>		
</form>

<script>
$(document).ready(function()
    {
    $('input.datePicker').datepicker(
        {
        changeMonth: true,
        changeYear: true,
        firstDay: 1,
        yearRange: '<?php echo date('Y');?>:+2'
        });
    });   

function validaform()
{
	var fechaDesde = $('#fecdes').val();
	var fechaHasta = $('#fechas').val();
	if($.trim(fechaDesde) == '')
		{
			showMessage('div#mini-notification', '#fecdes', 'error', 'Estimado Usuario, Debe seleccionar una fecha ("Desde")');
			ir_elemento('header');
			return false;
		}
	if($.trim(fechaHasta) == '')
		{
			showMessage('div#mini-notification', '#fechas', 'error', 'Estimado Usuario, Debe seleccionar una fecha ("Hasta")');
			ir_elemento('header');
			return false;
		}
	

}

</script>