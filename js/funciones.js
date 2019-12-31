/* Imprimir mensajes del formulario según su tipo... */
function showMessage(element, inputFocus, msgClass, msgText)
	{
	$(element).removeClass('ok');
	$(element).removeClass('alert');
	$(element).removeClass('error');
	$(element).removeClass('loading');
	$(element + ' p').html('');
	$(element + ' p').removeClass('ok');
	$(element + ' p').removeClass('alert');
	$(element + ' p').removeClass('error');
	$(element + ' p').removeClass('loading');
	$(element + ' p').addClass(msgClass);
	$(element).addClass(msgClass);
	var parametros = '';
	if($.trim(inputFocus) != '')
		{
		var primeraLetra = inputFocus.toString();
		primeraLetra = primeraLetra.substring(0, 1);
		if(primeraLetra == '#')
			{
			$(inputFocus).focus();
			$(inputFocus).select();
			}
		if(primeraLetra == '{')
			{
			parametros = inputFocus;
			}
		}
	$(element + ' p').html(msgText);
	$(element).miniNotification(parametros);
	}

/* Ocultar mensaje del formulario... */
/*function hide_message_div()
	{
	setTimeout(function(){$('div.message-div').fadeOut(1500);},5000);
	}
*/
/* Efecto ancla animada hacia un elemento X... */
function ir_elemento(elemento)
	{
	$('html, body').stop().animate({scrollTop: jQuery(elemento).offset().top}, 600);
	}

/* Bloquear y desbloquear botón de envío del formulario... */
function enviarFormDeshabilitar()
	{
	$('input#enviar').addClass('enviando');
	$('input#cancelar').addClass('cancelar_blocked');
	$('input#enviar, input#cancelar').attr('disabled', 'disabled');
	$('input#enviar').attr('value', 'Enviando datos...');
	}

function enviarFormHabilitar()
	{
	$('input#enviar').removeClass('enviando');
	$('input#cancelar').removeClass('cancelar_blocked');
	$('input#enviar, input#cancelar').removeAttr('disabled');	
	$('input#enviar').attr('value', 'Guardar Datos');
	}

function volver(url)
	{
	window.location.href = url;	
	}

function eliminar_registro(id, seccion, url_ajax, url_listado)
	{
	var mensaje = 'ATENCI\u00D3N: Se eliminar\u00E1 el elemento seleccionado.\u000A\u000AEsta operaci\u00F3n es irreversible. Desea continuar...?';
	if (confirm(mensaje))
		{
		var parametros = 
			{
			'id' : id,
			'accion' : 'delete',
			'seccion' : seccion
			};
		$.ajax({
			data:  parametros,
			url:   url_ajax,
			type:  'post',					
			success:  function(response)
				{
				var json = eval('(' + response + ')');
				if(json.tipo == 'ERROR')
					{
					showMessage('div#mini-notification', '', 'error', json.mensaje);
					}
				if(json.tipo == 'ACCION_OK')
					{
					showMessage('div#mini-notification', '', 'ok', json.mensaje);
					$('div#mini-notification').css('display', 'block');
					setTimeout(function(){ $(location).attr('href', url_listado);}, 2000);
					}
				}
			});
		}
	}

function eliminar_parametro(id, seccion, url_ajax, url_listado)
	{
	var mensaje = 'ATENCI\u00D3N: Se eliminar\u00E1 el Parametro para calculo de Comisiones seleccionado.\u000A\u000AEsta operaci\u00F3n es irreversible. Desea continuar...?';
	if (confirm(mensaje))
		{
		var parametros = 
			{
			'id' : id,
			'accion' : 'delete',
			'seccion' : seccion
			};
		$.ajax({
			data:  parametros,
			url:   url_ajax,
			type:  'post',					
			success:  function(response)
				{
				var json = eval('(' + response + ')');
				if(json.tipo == 'ERROR')
					{
					showMessage('div#mini-notification', '', 'error', json.mensaje);
					}
				if(json.tipo == 'ACCION_OK')
					{
					showMessage('div#mini-notification', '', 'ok', json.mensaje);
					$('div#mini-notification').css('display', 'block');
					setTimeout(function(){ $(location).attr('href', url_listado);}, 2000);
					}
				}
			});
		}
	}


function eliminar_rango(id, seccion, url_ajax, url_listado)
	{
	var mensaje = 'ATENCI\u00D3N: Se eliminar\u00E1 el Rango/Descuento seleccionado.\u000A\u000AEsta operaci\u00F3n es irreversible. Desea continuar...?';
	if (confirm(mensaje))
		{
		var parametros = 
			{
			'id' : id,
			'accion' : 'delete',
			'seccion' : seccion
			};
		$.ajax({
			data:  parametros,
			url:   url_ajax,
			type:  'post',					
			success:  function(response)
				{
				var json = eval('(' + response + ')');
				if(json.tipo == 'ERROR')
					{
					showMessage('div#mini-notification', '', 'error', json.mensaje);
					}
				if(json.tipo == 'ACCION_OK')
					{
					showMessage('div#mini-notification', '', 'ok', json.mensaje);
					$('div#mini-notification').css('display', 'block');
					setTimeout(function(){ $(location).attr('href', url_listado);}, 2000);
					}
				}
			});
		}
	}

/* Permitir solamente caracteres numericos (EJ: <input type="text" name="numeros" onkeypress="return allowOnlyNumbers(event);">)*/
function allowOnlyNumbers(e, obj)
	{
	var charCode = (e.which) ? e.which : e.keyCode;
	if(charCode > 31 && (charCode < 48 || charCode > 57))
		{
		$(obj).val('0');
		$(obj).focus();
		$(obj).select();
		return false;
		}
	if(charCode == 8)
		{
		if($(obj).val().length == 0)
			{
			$(obj).val('0');
			$(obj).focus();
			$(obj).select();
			return false;
			}
		}
	return true;
	}

function isAlphaNumeric(val)
	{
	if (val.match(/^[a-zA-Z0-9]+$/))
		{
		return true;
		}
	else
		{
		return false;
		}
	}
	
/* Para Calendario */
$(document).ready(function()
	{
	$('input.datePicker').datepicker(
		{
		changeMonth: true,
		changeYear: true,
		firstDay: 1,
		});
	});


/* PARA LAS TABLAS DINAMICAS */

$(document).ready(function(){
	$('#dataTable').DataTable({
		language: { 'url': 'js/jquery-datatables/languages/es.json' },
		aoColumnDefs: [{ 'bSortable': false, 'aTargets': ['no-sortable'] }]
		});
	});

/* POPUP */
$(document).ready(function()
	{
	$('a.tooltip_a').tooltip(
		{
		position:{
			my: 'center bottom-20',
			at: 'center top',
			using: function(position, feedback){
				$( this ).css(position);
				$('<div>')
				.addClass('arrow')
				.addClass(feedback.vertical)
				.addClass(feedback.horizontal)
				.appendTo(this);
				}
			}
		});
	});

/* FANCYBOX */
$(document).ready(function()
	{
	$('a.fancyboxBasic').fancybox({
		type: 'iframe',
		autoSize : false,
		beforeLoad : function(){         
			this.width  = parseInt(this.element.data('fancybox-width'));
			this.height = parseInt(this.element.data('fancybox-height'));
			this.modal = this.element.data('fancybox-modal');
			},
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600, 
		speedOut		: 400, 
		overlayShow		: false,
		helpers			: {
			'title' : null
			}
		});
	$('a.fancyboxModal').fancybox({
		modal: true,
		type: 'iframe',
		autoSize : false,
		beforeLoad : function(){         
			this.width  = parseInt(this.element.data('fancybox-width'));
			this.height = parseInt(this.element.data('fancybox-height'));
			this.modal = this.element.data('fancybox-modal');
			},
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600, 
		speedOut		: 400, 
		overlayShow		: false,
		helpers			: {
			'title' : null
			}
		});
	});
