<div class="row productsFilter">
  <div class="col-xs-3">
    <div class="input-group">
		<span class="input-group-addon"><?php echo $setting_products; ?></span>
		<input type="text" name="filter_name_view" value="" placeholder="" id="input-name-view" class="form-control" />
    </div>
  </div>
  <div class="col-xs-2">
  	<button type="button" id="button-filter-view" class="btn btn-primary pull-left"><i class="fa fa-search"></i> Filter</button>
  </div>
</div>
<hr />
<div id="ordersWrapper<?php echo $store['store_id']; ?>"> </div>
<script>
$('input[name=\'filter_name_view\']').autocomplete({
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
		$('input[name=\'filter_name_view\']').val(item['label']);
	}
});

$('#button-filter-view').on('click', function() {
	var filter_name = $('input[name=\'filter_name_view\']').val();
	$.ajax({
		url: "index.php?route=module/<?php echo $moduleNameSmall; ?>/getcustomers&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>&filter_name=" + filter_name,
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#ordersWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	});
});
// Show data from the module
$(document).ready(function(){
	$.ajax({
		url: "index.php?route=module/<?php echo $moduleNameSmall; ?>/getcustomers&token=<?php echo $token; ?>&page=1&store_id=<?php echo $store['store_id']; ?>",
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#ordersWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	});
});
// Remove all customers from the waiting list
function removeAll() {      
	var r=confirm("<?php echo $alert_removeall; ?>");
	if (r==true) {
		$.ajax({
			url: 'index.php?route=module/<?php echo $moduleNameSmall; ?>/removeallcustomers&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>',
			type: 'post',
			data: {'remove': r},
			success: function(response) {
				location.reload();
			}
		});
	}
}
// Move all customers to the archive
function moveAll() {      
	var r=confirm("<?php echo $alert_moveall; ?>");
	if (r==true) {
		$.ajax({
			url: 'index.php?route=module/<?php echo $moduleNameSmall; ?>/moveallcustomers&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>',
			type: 'post',
			data: {'move': r},
			success: function(response) {
				location.reload();
			}
		});
	}
}
</script>
