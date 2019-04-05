<div class="form-group required">
	<label class="col-sm-2 control-label" for="input-payment-firstname"><?php echo $entry_firstname; ?></label>
	<div class="col-sm-10">
	  <input type="text" name="firstname" value="<?php echo $payment_firstname; ?>" id="input-payment-firstname" class="form-control" />
	</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-lastname"><?php echo $entry_lastname; ?></label>
<div class="col-sm-10">
  <input type="text" name="lastname" value="<?php echo $payment_lastname; ?>" id="input-payment-lastname" class="form-control" />
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="input-payment-company"><?php echo $entry_company; ?></label>
<div class="col-sm-10">
  <input type="text" name="company" value="<?php echo $payment_company; ?>" id="input-payment-company" class="form-control" />
</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-address-1"><?php echo $entry_address_1; ?></label>
<div class="col-sm-10">
  <input type="text" name="address_1" value="<?php echo $payment_address_1; ?>" id="input-payment-address-1" class="form-control" />
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="input-payment-address-2"><?php echo $entry_address_2; ?></label>
<div class="col-sm-10">
  <input type="text" name="address_2" value="<?php echo $payment_address_2; ?>" id="input-payment-address-2" class="form-control" />
</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-city"><?php echo $entry_city; ?></label>
<div class="col-sm-10">
  <input type="text" name="city" value="<?php echo $payment_city; ?>" id="input-payment-city" class="form-control" />
</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-postcode"><?php echo $entry_postcode; ?></label>
<div class="col-sm-10">
  <input type="text" name="postcode" value="<?php echo $payment_postcode; ?>" id="input-payment-postcode" class="form-control" />
</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-country"><?php echo $entry_country; ?></label>
<div class="col-sm-10">
  <select name="country_id" id="input-payment-country" class="form-control">
	<option value=""><?php echo $text_select; ?></option>
	<?php foreach ($countries as $country) { ?>
	<?php if ($country['country_id'] == $payment_country_id) { ?>
	<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
	<?php } else { ?>
	<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
	<?php } ?>
	<?php } ?>
  </select>
</div>
</div>
<div class="form-group required">
<label class="col-sm-2 control-label" for="input-payment-zone"><?php echo $entry_zone; ?></label>
<div class="col-sm-10">
  <select name="zone_id" id="input-payment-zone" class="form-control">
  </select>
</div>
</div>
<?php foreach ($custom_fields as $custom_field) { ?>
<?php if ($custom_field['location'] == 'address') { ?>
<?php if ($custom_field['type'] == 'select') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <select name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control">
	<option value=""><?php echo $text_select; ?></option>
	<?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
	<?php if (isset($payment_custom_field[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $payment_custom_field[$custom_field['custom_field_id']]) { ?>
	<option value="<?php echo $custom_field_value['custom_field_value_id']; ?>" selected="selected"><?php echo $custom_field_value['name']; ?></option>
	<?php } else { ?>
	<option value="<?php echo $custom_field_value['custom_field_value_id']; ?>"><?php echo $custom_field_value['name']; ?></option>
	<?php } ?>
	<?php } ?>
  </select>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'radio') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <div id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>">
	<?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
	<div class="radio">
	  <?php if (isset($payment_custom_field[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $payment_custom_field[$custom_field['custom_field_id']]) { ?>
	  <label>
		<input type="radio" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
		<?php echo $custom_field_value['name']; ?></label>
	  <?php } else { ?>
	  <label>
		<input type="radio" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
		<?php echo $custom_field_value['name']; ?></label>
	  <?php } ?>
	</div>
	<?php } ?>
  </div>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'checkbox') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <div id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>">
	<?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
	<div class="checkbox">
	  <?php if (isset($payment_custom_field[$custom_field['custom_field_id']]) && in_array($custom_field_value['custom_field_value_id'], $payment_custom_field[$custom_field['custom_field_id']])) { ?>
	  <label>
		<input type="checkbox" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
		<?php echo $custom_field_value['name']; ?></label>
	  <?php } else { ?>
	  <label>
		<input type="checkbox" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
		<?php echo $custom_field_value['name']; ?></label>
	  <?php } ?>
	</div>
	<?php } ?>
  </div>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'text') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <input type="text" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'textarea') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <textarea name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" rows="5" placeholder="<?php echo $custom_field['name']; ?>" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control"><?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?></textarea>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'file') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <button type="button" id="button-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
  <input type="hidden" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : ''); ?>" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" />
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'date') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <div class="input-group date">
	<input type="text" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
	<span class="input-group-btn">
	<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
	</span></div>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'time') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <div class="input-group time">
	<input type="text" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="HH:mm" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
	<span class="input-group-btn">
	<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
	</span></div>
</div>
</div>
<?php } ?>
<?php if ($custom_field['type'] == 'datetime') { ?>
<div class="form-group custom-field custom-field<?php echo $custom_field['custom_field_id']; ?>" data-sort="<?php echo $custom_field['sort_order'] + 3; ?>">
<label class="col-sm-2 control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
<div class="col-sm-10">
  <div class="input-group datetime">
	<input type="text" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
	<span class="input-group-btn">
	<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
	</span></div>
</div>
</div>
<?php } ?>
<?php } ?>
<?php } ?>
<div class="row">
<div class="col-sm-6 text-left">
<button type="button" onclick="$('a[href=\'#tab-cart\']').tab('show');" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php echo $button_back; ?></button>
</div>
<div class="col-sm-6 text-right">
<button type="button" id="button-payment-address" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
</div>
</div>