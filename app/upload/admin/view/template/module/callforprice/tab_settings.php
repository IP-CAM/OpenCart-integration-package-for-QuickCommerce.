<div class="container-fluid">
	<div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_showon; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_showon_h; ?></span>
      </div>
      <div class="col-xs-3">
        <select id="ProductsChecker" name="<?php echo $moduleName; ?>[Alert]" class="form-control">
            <option value="default" <?php echo ((isset($moduleData['Alert']) && $moduleData['Alert'] == 'default')) ? 'selected=selected' : '' ?>><?php echo $setting_showon_h2; ?></option>
           <option value="selected" <?php echo ((isset($moduleData['Alert']) && $moduleData['Alert'] == 'selected')) ? 'selected=selected' : '' ?>><?php echo $setting_showon_h3; ?></option>
        </select>
      </div>
    </div>
    <div class="row productsInput">
      <hr />
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_products; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_products_h; ?>.</span>
      </div>
      <div class="col-xs-3">
        <input type="text" name="products" value="" class="form-control" />
        <div id="product" class="well well-sm" style="height:140px;overflow: auto;">
            <?php if (!empty($moduleData['Products'])) {
                foreach ($moduleData['Products'] as $pr) { 
                    $product = $modelCatalogProduct->getProduct($pr); ?>
                    <div id="products-<?php echo $pr; ?>"> 
                        <?php echo $product['name']; ?>&nbsp;<i class="fa fa-minus-circle"></i>
                        <input type="hidden" name="<?php echo $moduleName; ?>[Products][]" value="<?php echo $pr; ?>" />
                    </div>
                <?php }
            } ?>
        </div>      
      </div>
    </div>
	<hr />
	<div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_notification; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_notification_h; ?></span>
      </div>
      <div class="col-xs-3">
        <select name="<?php echo $moduleName; ?>[Notifications]" class="form-control">
            <option value="yes" <?php echo ((isset($moduleData['Notifications']) && $moduleData['Notifications'] == 'yes')) ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
           <option value="no" <?php echo ((isset($moduleData['Notifications']) && $moduleData['Notifications'] == 'no')) ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
        </select>
      </div>
    </div>
	<hr />
	<div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_popupwidth; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_popupwidth_h; ?></span>
      </div>
      <div class="col-xs-3">
      	<div class="input-group">
          <input type="text" name="<?php echo $moduleName; ?>[PopupWidth]" class="form-control" value="<?php echo (isset($moduleData['PopupWidth'])) ? $moduleData['PopupWidth'] : '320' ?>" />
          <span class="input-group-addon">px</span>
        </div>
      </div>
    </div>
	<hr />
	<div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_design; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_design_h; ?></span>
      </div>
      <div class="col-xs-7">
      	<?php foreach ($languages as $language) { ?>
    		<img src="view/image/flags/<?php echo $language['image']; ?>" style="float:left;position:absolute;margin-left:-20px;" title="<?php echo $language['name']; ?>" />
  			<?php echo $setting_design_h2; ?> <input name="<?php echo $moduleName; ?>[CustomTitle][<?php echo $language['language_id']; ?>]" class="form-control" type="text" value="<?php echo (isset($moduleData['CustomTitle'][$language['language_id']])) ? $moduleData['CustomTitle'][$language['language_id']] : 'Alert me!' ?>" />
            <textarea id="description_<?php echo $language['language_id']; ?>" name="<?php echo $moduleName; ?>[CustomText][<?php echo $language['language_id']; ?>]" class="form-control"><?php echo (isset($moduleData['CustomText'][$language['language_id']])) ? $moduleData['CustomText'][$language['language_id']] : '<p align="left"><span style="line-height: 1.6em;">Fill your phone below and we will notify you about the price of this product\'s price as soon as possible!</span></p>
<p align="left">Name:<br>{name_field}</p><p align="left">Phone:<br>{phone_field}</p><p align="left">Notes:<br>{notes_field}</p><p align="left">{submit_button}</p>' ?></textarea><br />
 		<?php } ?>
      </div>
    </div>
	<hr />
	<div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_captcha; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_captcha_h; ?></span>
      </div>
      <div class="col-xs-3">
      	<select id="CaptchaChecker" name="<?php echo $moduleName; ?>[UseCaptcha]" class="form-control">
			<option value="no" <?php echo ((isset($moduleData['UseCaptcha']) && $moduleData['UseCaptcha'] == 'no')) ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
            <option value="yes" <?php echo ((isset($moduleData['UseCaptcha']) && $moduleData['UseCaptcha'] == 'yes')) ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
        </select>
        <div class="captcha-info" style="margin-top:10px;">
            <div class="input-group">
                <span class="input-group-addon"><?php echo $setting_captcha_h2; ?></span>
                <input type="text" name="<?php echo $moduleName; ?>[SiteKey]" class="form-control" value="<?php echo (isset($moduleData['SiteKey'])) ? $moduleData['SiteKey'] : '' ?>" />
            </div>
            <div class="input-group" style="margin-top:10px;">
                <span class="input-group-addon"><?php echo $setting_captcha_h3; ?></span>
                <input type="text" name="<?php echo $moduleName; ?>[SecretKey]" class="form-control" value="<?php echo (isset($moduleData['SecretKey'])) ? $moduleData['SecretKey'] : '' ?>" />
            </div>
        </div>
      </div>
    </div>
    <hr />
    <div class="row">
      <div class="col-xs-3">
        <h5><strong><?php echo $setting_css; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_css_h; ?></span>
      </div>
      <div class="col-xs-3">
      	<textarea rows="5" name="<?php echo $moduleName; ?>[CustomCSS]" placeholder="<?php echo $setting_css_h2; ?>" class="form-control"><?php echo (isset($moduleData['CustomCSS'])) ? $moduleData['CustomCSS'] : '' ?></textarea>
      </div>
    </div>
</div>
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
	$('#description_<?php echo $language['language_id']; ?>').summernote({
		height: 250
	});
<?php } ?>
//--></script>
<script>
$('input[name=\'products\']').autocomplete({
	delay: 500,
	source: function(request, response) {
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
	select: function(item) {
		$('input[name=\'products\']').val('');
		
		$('#product' + item['value']).remove();
		
		$('#product').append('<div id="product' + item['value'] + '">' + item['label'] + '&nbsp;<i class="fa fa-minus-circle"></i><input type="hidden" name="<?php echo $moduleName; ?>[Products][]" value="' + item['value'] + '" /></div>');

	}
});

$('#product').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
</script>