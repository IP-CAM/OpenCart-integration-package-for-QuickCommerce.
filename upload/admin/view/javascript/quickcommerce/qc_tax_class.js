$(document).ready(function () {
	// TODO: This is going to be common on more than one page so make something reusable
	var refresh = $('#form-product').find('.fa-refresh'),
		buttons = [];
		
	$.each(refresh, function (idx, icon) {
		buttons.push($(icon).parent('a'));
	});

	var triggerSync = function (e) {
		e.preventDefault();
		e.stopPropagation();

		var button = $(e.currentTarget),
		  icon = button.find('i'),
		  token = button.attr('data-token'),
		  id = button.attr('data-id');
		
		$.ajax({
			url: 'index.php?route=qc/tax_class/sync&token=' + token + '&tax_class_id=' + id,
			type: 'GET',
			beforeSend: function () {
				button.removeClass('btn-default').addClass('btn-warning');
			},
			success: function (data, status, xhr) {
				icon.removeClass('fa-refresh').addClass('fa-check');
				button.removeClass('btn-warning').addClass('btn-success');
				
				window.location.reload();
			},
			error: function (xhr, status, message) {
				icon.removeClass('fa-refresh').addClass('fa-refresh');
				button.removeClass('btn-warning').addClass('btn-danger');
			},
			complete: function (xhr, status) {
				
			}
		});
	};

	$.each(buttons, function (idx, button) {
		button.on('click', triggerSync);
	});
	
	$('#qc-qbo-import').on('click', function (e) {
		confirm('Are you sure you want to import data from QuickBooks? This window will reload when complete.');
		
		var inputId = $('body').find('#input-tax-class-id').val();
		inputId = (inputId > 0) ? inputId : null;
		
		var url = '';
		if (inputId !== null) {
			url = 'index.php?route=qc/tax_rule/fetch&token=' + $(this).attr('data-token') + '&tax_class_id=' + inputId; 
		} else {
			url = 'index.php?route=qc/tax_class/fetch&token=' + $(this).attr('data-token');
		}
		
		$.ajax({
			url: url,
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
	
	$('#qc-qbo-export').on('click', function (e) {
		confirm('Are you sure you want to export data to QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/tax_class/push&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
	
	// Parent
	$('input[name=\'parent\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/tax_class/autocomplete&token=' + $(this).attr('data-token') + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					json.unshift({
						parent_id: 0,
						name: ''
					});
					
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['tax_class_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'parent\']').val(item['label']);
			$('input[name=\'parent_id\']').val(item['value']);
		}	
	});
});