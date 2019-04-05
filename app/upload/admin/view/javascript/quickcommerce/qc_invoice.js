$(document).ready(function () {
	// TODO: This is going to be common on more than one page so make something reusable
	var refresh = $('#form-invoice').find('.fa-refresh'),
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
			url: 'index.php?route=qc/invoice/sync&token=' + token + '&invoice_id=' + id,
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
	
	$('#button-batch-action').on('click', function (e) {
		//confirm('This action may affect multiple records. Are you sure you want to proceed?');
		
		var action = $('#batch-action').val();
		
		switch (action) {
			case 'sync':
				if (!confirm('Are you sure you want to export data to QuickBooks? This window will reload when complete.')) return;

				e.preventDefault();
				e.stopPropagation();

				$.ajax({
					url: 'index.php?route=qc/invoice/sync&token=' + $('#batch-action').attr('data-token'),
					type: 'post',
					data: $('#form-invoice').serializeArray(),
					beforeSend: function () {
						//button.removeClass('btn-default').addClass('btn-warning');
					},
					success: function (data, status, xhr) {
						//icon.removeClass('fa-refresh').addClass('fa-check');
						//button.removeClass('btn-warning').addClass('btn-success');

						window.location.reload();
					},
					error: function (xhr, status, message) {
						//icon.removeClass('fa-refresh').addClass('fa-refresh');
						//button.removeClass('btn-warning').addClass('btn-danger');
					},
					complete: function (xhr, status) {

					}
				});
				
				break;
			
			case 'delete':
				if (!confirm('You are about to delete multiple records from QuickCommerce and in QuickBooks. Are you sure you want to proceed?')) return;
				
				break;
		}
		
		/*$.ajax({
			url: 'index.php?route=qc/invoice/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});*/
	});
	
	$('#qc-qbo-import').on('click', function (e) {
		confirm('Are you sure you want to import data from QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/invoice/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
	
	$('#qc-peer-import').on('click', function (e) {
		//confirm('Are you sure you want to import data from QuickBooks? This window will reload when complete.');
		
		$('#p2p-import-modal').modal('toggle');
		
		/*$.ajax({
			url: 'index.php?route=qc/invoice/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});*/
	});
	
	$('#qc-qbo-export').on('click', function (e) {
		confirm('Are you sure you want to export data to QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/invoice/push&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
    
    var token = $('#form-invoice').attr('data-token');
    
    var indicators = $('#form-invoice').find('span[data-id]'),
        syncIds = [];
        
    indicators.each(function (idx, el) {
        syncIds.push($(el).attr('data-id')); 
    });
    
    $.ajax({
        type: 'post',
        url: 'index.php?route=qc/invoice/getSyncStatuses&token=' + token,
        data: {
            selected: syncIds 
        },
        success: function (data, status, xhr) {
            $.each(data, function (id, status) {
                var indicator = $('#form-invoice').find('span.label[data-id=' + id + ']'),
                    icon = indicator.find('i.fa');
                
                switch (status) {
                    case 'localonly':
                        indicator.attr('class', 'label label-info');
                        icon.attr('class', 'fa fa-thumb-tack');
                        break;
                        
                    case 'unlinked':
                        indicator.attr('class', 'label label-danger');
                        icon.attr('class', 'fa fa-unlink');
                        break;
                        
                    case 'remotenewer':
                        indicator.attr('class', 'label label-warning');
                        icon.attr('class', 'fa fa-download');
                        break;
                        
                    case 'localnewer':
                        indicator.attr('class', 'label label-success');
                        icon.attr('class', 'fa fa-upload');
                        break;
                        
                    case 'ok':
                        indicator.attr('class', 'label label-default');
                        icon.attr('class', 'fa fa-check');
                        break;
                }
            });
        },
        error: function (message) {
            console.log(message);
        }
    });
});