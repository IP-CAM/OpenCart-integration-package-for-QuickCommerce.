<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
          <div class="row">
			<div class="form-group required col-md-6 required">
			  <label class="col-sm-4 control-label" for="input-name"><?php echo $entry_name; ?></label>
			  <div class="col-sm-8">
				<input type="text" class="form-control" name="order_name" value="<?php echo $order_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
				<?php if ($error_name) { ?>
				<div class="text-danger"><?php echo $error_name; ?></div>
				<?php } ?>
			  </div>
			</div>
			<div class="form-group col-md-6 required">
			  <label class="col-sm-4 control-label" for="input-vendor"><?php echo $entry_vendor; ?></label>
			  <div class="col-sm-8">
				<input type="text" class="form-control" name="vendor" value="<?php echo $vendor; ?>" class="form-control" id="input-vendor" />
				<input type="hidden" name="purchase_order_vendor_id" value="<?php echo $purchase_order_vendor_id; ?>" />
				<?php if ($error_vendor) { ?>
				<div class="text-danger"><?php echo $error_vendor; ?></div>
				<?php } ?>
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="form-group required col-md-6">
			  <label class="col-sm-4 control-label" for="input-payment"><?php echo $entry_payment; ?></label>
			  <div class="col-sm-8">
				<select class="form-control" name="purchase_order_payment_id" class="form-control" id="input-payment">
				  <?php foreach ($payments as $payment) { ?>
				  <option value="<?php echo $payment['purchase_order_payment_id']; ?>"<?php echo $payment['purchase_order_payment_id'] == $purchase_order_payment_id ? ' selected="selected"' : ''; ?>><?php echo $payment['name']; ?></option>
				  <?php } ?>
				</select>
				<?php if ($error_payment) { ?>
				<div class="text-danger"><?php echo $error_payment; ?></div>
				<?php } ?>
			  </div>
			</div>
			<div class="form-group col-md-6">
			  <label class="col-sm-4 control-label" for="input-shipping"><?php echo $entry_shipping; ?></label>
			  <div class="col-sm-8">
				<select class="form-control" name="purchase_order_shipping_id" class="form-control" id="input-shipping">
				  <?php foreach ($shippings as $shipping) { ?>
				  <option value="<?php echo $shipping['purchase_order_shipping_id']; ?>"<?php echo $shipping['purchase_order_shipping_id'] == $purchase_order_shipping_id ? ' selected="selected"' : ''; ?>><?php echo $shipping['name']; ?></option>
				  <?php } ?>
				</select>
				<?php if ($error_shipping) { ?>
				<div class="text-danger"><?php echo $error_shipping; ?></div>
				<?php } ?>
			  </div>
		    </div>
		  </div>
		  <div class="row">
			<div class="form-group required col-md-6 required">
			  <label class="col-sm-4 control-label" for="input-date-arrival"><?php echo $entry_date_arrival; ?></label>
			  <div class="col-sm-8">
				<input type="text" class="form-control" name="date_arrival" value="<?php echo $date_arrival; ?>" class="datetime form-control" id="input-date-arrival" data-date-format="YYYY-MM-DD HH:mm:ss" />
				<?php if ($error_date_arrival) { ?>
				<div class="text-danger"><?php echo $error_date_arrival; ?></div>
				<?php } ?>
			  </div>
			</div>
			<div class="form-group col-md-6">
			  <label class="col-sm-4 control-label" for="input-status"><?php echo $entry_status; ?></label>
			  <div class="col-sm-8">
				<select class="form-control" name="status_id" class="form-control" id="input-status">
				<?php foreach ($statuses as $status) { ?>
				<option value="<?php echo $status['order_status_id']; ?>"<?php echo $status_id == $status['order_status_id'] ? ' selected="selected"' : ''; ?>><?php echo $status['name']; ?></option>
				<?php } ?>
				</select>
				<?php if ($error_status) { ?>
				<div class="text-danger"><?php echo $error_status; ?></div>
				<?php } ?>
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="form-group col-md-6">
			  <label class="col-sm-4 control-label" for="input-received"><?php echo $entry_received; ?></label>
			  <div class="col-sm-8">
				<div class="checkbox-inline">
				  <input type="checkbox" name="received" value="1"<?php echo $received ? ' checked="checked"' : ''; ?> />
				</div>
			  </div>
		    </div>
			<div class="form-group col-md-6">
			  <label class="col-sm-4 control-label" for="input-date-received"><?php echo $entry_date_received; ?></label>
			  <div class="col-sm-8">
				<input type="text" class="form-control" name="date_received" value="<?php echo $date_received; ?>" class="datetime form-control" id="input-date-received" data-date-format="YYYY-MM-DD HH:mm:ss" />
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="form-group">
			  <label class="col-sm-2 control-label" for="input-received"><?php echo $entry_comment; ?></label>
			  <div class="col-sm-10">
				<textarea name="comment" class="form-control"><?php echo $comment; ?></textarea>
			  </div>
		    </div>
		  </div>
		  <table id="product" class="table table-hover table-bordered">
			<thead>
			  <tr>
				<td class="text-left"><?php echo $entry_product; ?></td>
				<td class="text-left"><?php echo $entry_sold; ?></td>
				<td class="text-left"><?php echo $entry_stock; ?></td>
				<td class="text-left"><?php echo $entry_model; ?></td>
				<td class="text-left"><?php echo $entry_quantity; ?></td>
				<td class="text-right"><?php echo $entry_price; ?></td>
				<td class="text-right"><?php echo $entry_total; ?></td>
				<td class="text-right"></td>
			  </tr>
			</thead>
			<tbody>
			  <?php $product_row = 0; ?>
			  <?php $option_row = 0; ?>
			  <?php foreach ($products as $product) { ?>
			  <tr>
				<td class="text-left"><input type="text" class="form-control" name="products[<?php echo $product_row; ?>][name]" value="<?php echo $product['name']; ?>" />
				  <?php if (isset($product['hasOption']) && $product['hasOption']) { ?>
				  <a onclick="addOption('<?php echo $product_row; ?>');"><i class="fa fa-plus-circle"></i></a>
				  <?php } ?>
				  <?php if (isset($product['options'])) { ?>
				  <?php foreach ($product['options'] as $option) { ?>
					<div id="option-<?php echo $option_row; ?>"><a onclick="$('#option-<?php echo $option_row; ?>').remove();"><i class="fa fa-trash"></i></a> <select class="form-control" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][product_option_id]" class="options" data="<?php echo $product_row; ?>" rel="<?php echo $option_row; ?>">
					  <?php foreach ($product['product_options'] as $product_option) { ?>
						<?php if ($product_option['product_option_id'] == $option['product_option_id']) { ?>
						<option value="<?php echo $product_option['product_option_id']; ?>" selected="selected"><?php echo $product_option['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $product_option['product_option_id']; ?>"><?php echo $product_option['name']; ?></option>
						<?php } ?>
					  <?php } ?>
					  </select>
					  <?php if ($option['values']) { ?>
					  <select class="form-control" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][product_option_value_id]" class="child">
						<?php foreach ($option['values'] as $value) { ?>
						  <?php if ($value['name'] == $option['value']) { ?>
							<option value="<?php echo $value['product_option_value_id']; ?>" selected="selected"><?php echo $value['name']; ?></option>
						  <?php } else { ?>
							<option value="<?php echo $value['product_option_value_id']; ?>"><?php echo $value['name']; ?></option>
						  <?php } ?>
						<?php } ?>
					  </select>
					  <input type="hidden" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][value]" value="<?php echo $option['value']; ?>" class="child" />
					  <?php } else { ?>
					  <input type="text" class="form-control" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][value]" value="<?php echo $option['value']; ?>" class="child" />
					  <input type="hidden" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][product_option_value_id]" value="0" class="child" />
					  <?php } ?>
					  <input type="hidden" name="products[<?php echo $product_row; ?>][options][<?php echo $option_row; ?>][name]" value="<?php echo $option['name']; ?>" />
					</div>
					<?php $option_row++; ?>
				  <?php } ?>
				  <?php } ?>
				  <input type="hidden" name="products[<?php echo $product_row; ?>][product_id]" value="<?php echo $product['product_id']; ?>" /></td>
				<td class="text-left"><?php echo isset($product['sold']) ? $product['sold'] : ''; ?></td>
				<td class="text-left"><?php echo isset($product['stock']) ? $product['stock'] : ''; ?></td>
				<td class="text-left"><input type="text" class="form-control" name="products[<?php echo $product_row; ?>][model]" value="<?php echo $product['model']; ?>" /></td>
				<td class="text-left"><input type="text" class="form-control" name="products[<?php echo $product_row; ?>][quantity]" value="<?php echo $product['quantity']; ?>" class="quantity" rel="<?php echo $product_row; ?>" /></td>
				<td class="text-right"><input type="text" class="form-control" name="products[<?php echo $product_row; ?>][price]" value="<?php echo $product['price']; ?>" class="price" rel="<?php echo $product_row; ?>" /></td>
				<td class="text-right"><span id="total-<?php echo $product_row; ?>"><?php echo $product['total']; ?></span>
				  <input type="hidden" name="products[<?Php echo $product_row; ?>][total]" value="<?php echo $product['total']; ?>" class="product-total" /></td>
				<td class="text-right"><a onclick="$(this).parents('tr').remove();$('.quantity').trigger('keyup');"><i class="fa fa-trash"></i></a></td>
			  </tr>
			  <?php $product_row++; ?>
			  <?php } ?>
			</tbody>
			<tfoot>
			  <tr>
				<td class="text-left" colspan="4"><a id="button-populate" class="btn btn-primary"><?php echo $button_populate; ?></a></td>
				<td class="text-right" colspan="4"><a id="button-product" class="btn btn-success"><?php echo $button_add_product; ?></a></td>
			  </tr>
			  <tr>
				<td class="text-right" colspan="8"><span id="product-total"></span></td>
			  </tr>
			</tfoot>
		  </table>
		  <table id="total" class="table table-hover table-bordered">
			<thead>
			  <tr>
				<td class="text-left"><?php echo $entry_total; ?></td>
				<td class="text-right"><?php echo $entry_value; ?></td>
				<td class="text-right"></td>
			  </tr>
			</thead>
			<tbody>
			  <?php $total_row = 0; ?>
			  <?php foreach ($totals as $order_total) { ?>
				<tr>
				  <td class="text-left"><input type="text" class="form-control" name="totals[<?php echo $total_row; ?>][name]" value="<?php echo $order_total['name']; ?>" /></td>
				  <td class="text-right"><input type="text" class="form-control" name="totals[<?php echo $total_row; ?>][value]" value="<?php echo $order_total['value']; ?>" class="order-value" /></td>
				  <td class="text-right"><a onclick="$(this).parents('tr').remove();$('.order-value').trigger('keyup');" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>
				</tr>
				<?php $total_row++; ?>
			  <?php } ?>
			</tbody>
			<tfoot>
			  <tr>
				<td class="rtext-ight" colspan="3"><a id="button-total" class="btn btn-success"><?php echo $button_add_total; ?></a></td>
			  </tr>
			  <tr>
				<td class="text-right" colspan="3"><span id="order-total"></span>
				  <input type="hidden" name="total" value="<?php echo $total; ?>" />
				</td>
			  </tr>
			</tfoot>
		  </table>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.datetime').datetimepicker({
		pickDate: true,
		pickTime: true
	});
});

function calculate(product_row) {
	var price = parseFloat($('input[name=\'products[' + product_row + '][price]\']').val());
	
	if ($('input[name=\'products[' + product_row + '][quantity]\']').val() == '') {
		var quantity = 0;
	} else {
		var quantity = parseFloat($('input[name=\'products[' + product_row + '][quantity]\']').val());
	}

	var total = price * quantity;
	
	$('#total-'  + product_row).html(total.toFixed(2));
	$('input[name=\'products[' + product_row + '][total]\']').val(total.toFixed(2));
	
	var product_total = 0;
	
	$('.product-total').each(function(){
		product_total += parseFloat($(this).val());
	});

	$('#product-total').html(product_total.toFixed(2));
	
	calculateTotal();
}

function calculateTotal() {
	if ($('#product-total').html() == '') {
		var order_total = 0;
	} else {
		var order_total = parseFloat($('#product-total').html());
	}
	
	$('.order-value').each(function(){
		order_total += parseFloat($(this).val());
	});

	$('#order-total').html(order_total.toFixed(2));
	$('input[name=\'total\']').val(order_total.toFixed(2));
}

function productautocomplete(product_row) {
	$('input[name=\'products[' + product_row + '][name]\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/purchase_order_vendor/product&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['product_id'],
							sold: item['sold'],
							stock: item['stock'],
							model: item['model'],
							price: item['price'],
							hasOption: item['hasOption']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'products[' + product_row + '][name]\']').val(item['label']);
			$('input[name=\'products[' + product_row + '][product_id]\']').val(item['value']);
			$('#sold-' + product_row).html(item['sold']);
			$('#stock-' + product_row).html(item['stock']);
			$('input[name=\'products[' + product_row + '][model]\']').val(item['model']);
			$('input[name=\'products[' + product_row + '][price]\']').val(item['price']);
			
			if (item['hasOption']) {
				html = ' <a onclick="addOption(' + product_row + ');"><i class="fa fa-plus-circle"></i></a>';
				
				$('input[name=\'products[' + product_row + '][name]\']').after(html);
			}
			
			calculate(product_row);
			
			return false;
		}
	});
}

$('#product tbody').each(function(index, element) {
	productautocomplete(index);
});

$('input[name=\'vendor\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/purchase_order_vendor/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['purchase_order_vendor_id']
					}
				}));
			}
		});
	},
	select: function(item) {
		$('input[name=\'vendor\']').val(item['label']);
		$('input[name=\'purchase_order_vendor_id\']').val(item['value']);
		
		return false;
	}
});

var product_row = <?php echo $product_row; ?>;

$('#button-populate').click(function() {
	$.ajax({
		url: 'index.php?route=catalog/purchase_order_vendor/populate&token=<?php echo $token; ?>&purchase_order_vendor_id=' + $('input[name=\'purchase_order_vendor_id\']').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-populate').after('<i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else if (json['products']) {
				$('#product tbody').html('');
				
				for (i = 0; i < json['products'].length; i++) {
					html  = '';
					
					html += '<tr>';
					html += '  <td class="left"><input type="text" class="form-control" name="products[' + product_row + '][name]" value="' + json['products'][i]['name'] + '" />';
					
					if (json['products'][i]['hasOption']) {
						html += ' <a onclick="addOption(' + product_row + ');"><i class="fa fa-plus-circle"></i></a>';
					}
					
					html += '    <input type="hidden" name="products[' + product_row + '][product_id]" value="' + json['products'][i]['product_id'] + '" /></td>';
					html += '  <td class="text-left"><span id="sold-' + product_row + '">' + json['products'][i]['sold'] + '</span></td>';
					html += '  <td class="text-left"><span id="stock-' + product_row + '">' + json['products'][i]['stock'] + '</span></td>';
					html += '  <td class="text-left"><input type="text" class="form-control" name="products[' + product_row + '][model]" value="' + json['products'][i]['model'] + '" /></td>';
					html += '  <td class="text-left"><input type="text" class="form-control" name="products[' + product_row + '][quantity]" value="" class="quantity" rel="' + product_row + '" /></td>';
					html += '  <td class="text-right"><input type="text" class="form-control" name="products[' + product_row + '][price]" value="' + json['products'][i]['price'] + '" class="price" rel="' + product_row + '" /></td>';
					html += '  <td class="text-right"><span id="total-' + product_row + '"></span>';
					html += '    <input type="hidden" name="products[' + product_row + '][total]" value="" class="product-total" /></td>';
					html += '  <td class="text-right"><a onclick="$(this).parents(\'tr\').remove();$(\'.quantity\').trigger(\'keyup\');"><i class="fa fa-trash"></i></a></td>';
					html += '</tr>';
					
					$('#product tbody').append(html);
					
					if (json['products'][i]['hasOption']) {
						for (j = 0; j < json['products'][i]['options'].length; j++) {
							addOption(product_row, json['products'][i]['options'][j]['product_option_id'], json['products'][i]['options'][j]['product_option_value_id']);
						}
					}
					
					product_row++;
				}
				
				$('.quantity').trigger('keyup');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#button-product').click(function() {
	html  = '';
	html += '<tr>';
	html += '  <td class="text-left"><input type="text" class="form-control" name="products[' + product_row + '][name]" value="" />';
	html += '    <input type="hidden" name="products[' + product_row + '][product_id]" value="" /></td>';						
	html += '  <td class="text-left"><span id="sold-' + product_row + '"></span></td>';
	html += '  <td class="text-left"><span id="stock-' + product_row + '"></span></td>';
	html += '  <td class="text-left"><input type="text" class="form-control" name="products[' + product_row + '][model]" value="" /></td>';
	html += '  <td class="text-left"><input type="text" class="form-control" name="products[' + product_row + '][quantity]" value="" class="quantity" rel="' + product_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" class="form-control" name="products[' + product_row + '][price]" value="" class="price" rel="' + product_row + '" /></td>';
	html += '  <td class="text-right"><span id="total-' + product_row + '"></span>';
	html += '    <input type="hidden" name="products[' + product_row + '][total]" value="" class="product-total" /></td>';
	html += '  <td class="text-right"><a onclick="$(this).parents(\'tr\').remove();$(\'.quantity\').trigger(\'keyup\');"><i class="fa fa-trash"></i></a></td>';
	html += '</tr>';
	
	$('#product tbody').append(html);
	
	productautocomplete(product_row);
	
	product_row++;
});

$(document).on('keyup', '.quantity, .price', function() {
	calculate($(this).attr('rel'));
});

var total_row = <?php echo $total_row; ?>;

$('#button-total').click(function() {
	html  = '';
	html += '<tr>';
	html += '  <td class="text-left"><input type="text" class="form-control" name="totals[' + total_row + '][name]" value="" /></td>';						
	html += '  <td class="text-right"><input type="text" class="form-control" name="totals[' + total_row + '][value]" value="0.00" class="order-value" /></td>';
	html += '  <td class="text-right"><a onclick="$(this).parents(\'tr\').remove();$(\'.order-value\').trigger(\'keyup\');"><i class="fa fa-trash"></i></a></td>';
	html += '</tr>';
	
	$('#total tbody').append(html);
	
	total_row++;
});

$(document).on('keyup', '.order-value', function() {
	calculateTotal();
});

$('.quantity:first').trigger('keyup');

var option_row = <?php echo $option_row; ?>;

function addOption(product_row, product_option_id, product_option_value_id) {
	$.ajax({
		url: 'index.php?route=catalog/purchase_order_vendor/option&token=<?php echo $token; ?>&product_id=' + $('input[name=\'products[' + product_row + '][product_id]\']').val(),
		dataType: 'json',
		beforeSend: function() {
			$('input[name=\'products[' + product_row + '][name]\']').after('<i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			if (json['options']) {
				html  = '';
				html += '<div id="option-' + option_row + '"><a onclick="$(\'#option-' + option_row + '\').remove();"><i class="fa fa-trash"></i></a> <select class="form-control" name="products[' + product_row + '][options][' + option_row + '][product_option_id]" class="options" data="' + product_row + '" rel="' + option_row + '">';
				
				for (i = 0; i < json['options'].length; i++) {
					html += '<option value="' + json['options'][i]['product_option_id'] + '"';
					
					if (product_option_id == json['options'][i]['product_option_id']) {
						html += ' selected="selected"';
					}
					
					html += '>' + json['options'][i]['name'] + '</option>';
				}
				
				html += '</select><input type="hidden" name="products[' + product_row + '][options][' + option_row + '][name]" /></div>';
				
				$('input[name=\'products[' + product_row + '][name]\']').parent().append(html);
				
				if (typeof product_option_id != 'undefined') {
					addOptionValues(option_row, product_row, product_option_id, product_option_value_id);
				} else {
					$('#option-' + option_row + ' .options').trigger('change');
				}
				
				option_row++;
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function addOptionValues(option_row, product_row, product_option_id, product_option_value_id) {
	$.ajax({
		url: 'index.php?route=catalog/purchase_order_vendor/optionvalue&token=<?php echo $token; ?>&product_option_id=' + product_option_id,
		dataType: 'json',
		beforeSend: function() {
		},		
		complete: function() {
		},			
		success: function(json) {
			$('#option-' + option_row + ' .child').remove();
			$('input[name=\'products[' + product_row + '][options][' + option_row + '][name]\']').val($('#option-' + option_row).find('option:selected').text());
			
			html = '';
			
			if (json['values']) {
				html += '<select class="form-control" name="products[' + product_row + '][options][' + option_row + '][product_option_value_id]" class="child">';
				
				for (i = 0; i < json['values'].length; i++) {
					html += '<option value="' + json['values'][i]['product_option_value_id'] + '"';
					
					if (product_option_value_id == json['values'][i]['product_option_value_id']) {
						html += ' selected="selected"';
					}
					
					html += '>' + json['values'][i]['name'] + '</option>';
				}
				
				html += '</select>';
				
				html += '<input type="hidden" name="products[' + product_row + '][options][' + option_row + '][value]" class="child" />';
			} else {
				html += '<input type="text" class="form-control" name="products[' + product_row + '][options][' + option_row + '][value]" class="child" />';
				html += '<input type="hidden" name="products[' + product_row + '][options][' + option_row + '][product_option_value_id]" class="child" = "0" />';
			}
			
			$('select[name=\'products[' + product_row + '][options][' + option_row + '][product_option_id]\']').after(html);
		
			$('select[name=\'products[' + product_row + '][options][' + option_row + '][product_option_value_id]\']').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).on('change', '.options', function() {
	element = $(this);
	
	$.ajax({
		url: 'index.php?route=catalog/purchase_order_vendor/optionvalue&token=<?php echo $token; ?>&product_option_id=' + element.val(),
		dataType: 'json',
		beforeSend: function() {
			element.after('<i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			$('#option-' + element.attr('rel') + ' .child').remove();
			$('input[name=\'products[' + element.attr('data') + '][options][' + element.attr('rel') + '][name]\']').val(element.find('option:selected').text());
			
			html = '';
			
			if (json['values']) {
				html += '<select class="form-control" name="products[' + element.attr('data') + '][options][' + element.attr('rel') + '][product_option_value_id]" class="child">';
				
				for (i = 0; i < json['values'].length; i++) {
					html += '<option value="' + json['values'][i]['product_option_value_id'] + '">' + json['values'][i]['name'] + '</option>';
				}
				
				html += '</select>';
				
				html += '<input type="hidden" name="products[' + element.attr('data') + '][options][' + element.attr('rel') + '][value]" class="child" />';
			} else {
				html += '<input type="text" class="form-control" name="products[' + element.attr('data') + '][options][' + element.attr('rel') + '][value]" class="child" />';
				html += '<input type="hidden" name="products[' + element.attr('data') + '][options][' + element.attr('rel') + '][product_option_value_id]" class="child" = "0" />';
			}
			
			element.after(html);
			
			$('select[name=\'products[' + element.attr('data') + '][options][' + element.attr('rel') + '][product_option_value_id]\']').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$(document).on('change', 'select.child', function() {
	var value = $(this).find(':selected').text();
	
	$(this).siblings('input[type=\'hidden\'].child').val(value);
});
//--></script>
<?php echo $footer; ?>