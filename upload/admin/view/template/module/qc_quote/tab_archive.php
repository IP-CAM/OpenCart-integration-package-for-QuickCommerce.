<div class="row productsFilter">
  <div class="col-xs-3">
    <div class="input-group">
		<span class="input-group-addon"><?php echo $setting_products; ?></span>
		<input type="text" name="filter_name" value="" placeholder="" id="input-name" class="form-control" />
    </div>
  </div>
  <div class="col-xs-2">
  	<button type="button" id="button-filter" class="btn btn-primary pull-left"><i class="fa fa-search"></i> Filter</button>
  </div>
</div>
<hr />
<div id="archiveWrapper<?php echo $store['store_id']; ?>"> </div>
<script type="text/javascript">
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('#button-filter').on('click', function() {
	var filter_name = $('input[name=\'filter_name\']').val();
	$.ajax({
		url: "index.php?route=module/<?php echo $_name; ?>/getarchive&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>&filter_name=" + filter_name,
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#archiveWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	});
});

// Show date from the module
$(document).ready(function(){
	$.ajax({
		url: "index.php?route=module/<?php echo $_name; ?>/getarchive&token=<?php echo $token; ?>&page=1&store_id=<?php echo $store['store_id']; ?>",
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#archiveWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	});
});
// Remove all customers from the archive
function removeAllArchive() {      
	var r=confirm("<?php echo $alert_removeall; ?>");
	if (r==true) {
		$.ajax({
			url: 'index.php?route=module/<?php echo $_name; ?>/removeallarchive&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>',
			type: 'post',
			data: {'remove': r},
			success: function(response) {
				location.reload();
			}
		});
	}
}
// Remove single customer
function removeCustomer(cfpID) {      
	var r=confirm("<?php echo $alert_removecustomer; ?>");
	if (r==true) {
		$.ajax({
			url: 'index.php?route=module/<?php echo $_name; ?>/removecustomer&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>',
			type: 'post',
			data: {'callforprice_id': cfpID},
			success: function(response) {
				location.reload();
			}
		});
	}
}
// Move single customer
function moveCustomer(cfpID) { 
	$('#notes-data').val(''); 
	$('#notes-data').attr('data-id', cfpID);    
	$('.modal-notes').modal();
}

function moveAJAXCustomer(cfpID, cfpNotes) {
	var r=confirm("<?php echo $alert_movecustomer; ?>");
	if (r==true) {
		debugger;
		$.ajax({
			url: 'index.php?route=module/<?php echo $_name; ?>/movecustomer&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>',
			type: 'post',
			data: {'callforprice_id': cfpID, 'callforprice_notes' : cfpNotes},
			success: function(response) {
				debugger;
				location.reload();
			}
		});
	}	
}
</script>