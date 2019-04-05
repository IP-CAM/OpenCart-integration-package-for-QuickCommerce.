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
	  <td class="text-left"><?php if ($sort == 'p.status') { ?>
		<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
		<?php } else { ?>
		<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
		<?php } ?></td>
	  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
	</tr>
  </thead>
  <tbody>
	<?php if (isset($products) && count($products) > 0) { ?>
	<?php foreach ($products as $product) { ?>
	<tr>
	  <td class="text-center"><?php if ($product['image']) { ?>
		<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
		<?php } else { ?>
		<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
		<?php } ?></td>
	  <td class="text-left"><?php echo $product['model']; ?></td>
	  <td class="text-left">
		<input type="text" name="model[<?php echo $product['product_id']; ?>]" value="<?php echo $product['local_model']; ?>" class="input-product form-control" />
	  </td>
	  
	  </td>
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
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>