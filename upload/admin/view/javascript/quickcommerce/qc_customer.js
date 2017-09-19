$(document).ready(function () {
	// TODO: This is going to be common on more than one page so make something reusable
	var refresh = $('#form-customer').find('.fa-refresh'),
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
			url: 'index.php?route=qc/customer/sync&token=' + token + '&customer_id=' + id,
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
		confirm('Import data from QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/customer/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			dataType: 'json',
			success: function (data, status, xhr) {
				if (data.hasOwnProperty('errors')) {
					alert(xhr.responseText);
					return false;
				}

				window.setTimeout(function () {
					alert('Success! Reloading in 3 seconds...');
					window.location.reload();
				}, 3000);
			},
			error: function (xhr, status, error) {
				alert(error);
			}
		});
	});
	
	$('#qc-qbo-export').on('click', function (e) {
		alert('Sorry, this feature is not available yet.');
		
		/*$.ajax({
			//url: 'index.php?route=catalog/product/add&token=6271d30efe19e1bd702502d2700a69bf'
			//url: 'index.php?route=catalog/product/add&token=6271d30efe19e1bd702502d2700a69bf'
			url: 'index.php?route=qc/product/fetch&token=6271d30efe19e1bd702502d2700a69bf',
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});*/
	});
    
    var token = $('#qc-qbo-import').attr('data-token');
    
    var indicators = $('#form-customer').find('span[data-id]'),
        syncIds = [];
        
    indicators.each(function (idx, el) {
        syncIds.push($(el).attr('data-id')); 
    });
    
    $.ajax({
        type: 'post',
        url: 'index.php?route=qc/customer/getSyncStatuses&token=' + token,
        data: {
            selected: syncIds 
        },
        success: function (data, status, xhr) {
            $.each(data, function (id, status) {
                var indicator = $('#form-customer').find('span.label[data-id=' + id + ']'),
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
                        indicator.attr('class', 'label label-success');
                        icon.attr('class', 'fa fa-download');
                        break;
                        
                    case 'localnewer':
                        indicator.attr('class', 'label label-success');
                        icon.attr('class', 'fa fa-upload');
                        break;
                        
                    case 'ok':
                        indicator.attr('class', 'label label-success');
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