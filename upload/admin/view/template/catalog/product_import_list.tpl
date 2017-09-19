<div class="table-responsive">
<table class="table table-bordered table-hover">
  <thead>
	<tr>
	  <td class="text-center"><?php echo $column_image; ?></td>
	  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
		<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
		<?php } ?></td>
	  <td class="text-left"><?php if ($sort == 'p.model') { ?>
		<a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
		<?php } ?></td>
	  <td class="text-right"><?php if ($sort == 'p.price') { ?>
		<a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
		<?php } ?></td>
	  <td class="text-right"><?php if ($sort == 'p.quantity') { ?>
		<a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
		<?php } ?></td>
	  <td class="text-left"><?php if ($sort == 'p.status') { ?>
		<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
		<?php } ?></td>
	  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
	</tr>
  </thead>
  <tbody>
	<?php if (isset($db2_products) && count($db2_products) > 0) { ?>
	<?php foreach ($db2_products as $product) { ?>
	<tr>
	  <td class="text-center"><?php if ($product['image']) { ?>
		<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
		<?php } else { ?>
		<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
		<?php } ?></td>
	  <td class="text-left"><?php echo $product['model']; ?></td>
	  <td class="text-left">
		<!--<span class="input-group">-->
		<!--<input type="text" value="<?php echo $product['model']; ?>" class="input-product form-control" />-->
			<input type="text" value="<?php echo $product['local_model']; ?>" class="input-product form-control" />
			<!--<span class="input-group-btn">
				<button class="btn btn-info button-product-unlinked" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-search"></i> Search</button>
				<button class="btn btn-success button-product-linked" style="display: none" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-check"></i> Selected</button>
			</span>
		</span>-->
		<input type="hidden" name="product_id[<?php echo $product['product_id']; ?>]" value="<?php echo (isset($product['local_id'])) ? $product['local_id'] : ''; ?>">
	  </td>
	  
	  </td>
	  <td class="text-right"><?php if ($product['special']) { ?>
		<span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
		<div class="text-danger"><?php echo $product['special']; ?></div>
		<?php } else { ?>
		<?php echo $product['price']; ?>
		<?php } ?></td>
	  <td class="text-right"><?php if ($product['quantity'] <= 0) { ?>
		<span class="label label-warning"><?php echo $product['quantity']; ?></span>
		<?php } elseif ($product['quantity'] <= 5) { ?>
		<span class="label label-danger"><?php echo $product['quantity']; ?></span>
		<?php } else { ?>
		<span class="label label-success"><?php echo $product['quantity']; ?></span>
		<?php } ?></td>
	  <td class="text-left"><?php echo $product['status']; ?></td>
	  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
		<input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
		<?php } else { ?>
		<input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
		<?php } ?></td>
	</tr>
	<?php } ?>
	<?php } else { ?>
	<tr>
	  <td class="text-center" colspan="8"><?php echo 'No Results'; ?><?php //echo $text_no_results; ?></td>
	</tr>
	<?php } ?>
  </tbody>
</table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $db2_pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $db2_results; ?></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	$('.input-product').each(function () {
		$(this).autocomplete({
			'source': function(request, response) {
				$.ajax({
					url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
					dataType: 'json',			
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['model'],
								value: item['product_id'],
								model: item['model'],
								option: item['option'],
								price: item['price']						
							}
						}));
					}
				});
			},
			'select': function (item) {
				console.log(item);
				$(this).val(item['model']);
				$(this).closest('td').find('input[name^=\'product_id\']').val(item['value']);
				//$('#tab-line .button-product-linked').show();
				//$('#tab-line .button-product-unlinked').hide();
			}	
		});
	});
});
</script>