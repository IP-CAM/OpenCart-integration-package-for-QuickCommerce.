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
          <div class="form-group required">
						<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
							<?php if ($error_name) { ?>
							<div class="text-danger"><?php echo $error_name; ?></div>
							<?php } ?>
						</div>
		  		</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-manufacturer"><?php echo $entry_manufacturer; ?></label>
					<div class="col-sm-10">
						<div class="well well-sm" style="overflow:auto;height:150px;">
						<?php foreach ($manufacturers as $manufacturer) { ?>
						<div class="checkbox">
							<label style="width:100%;">
								<?php if (in_array($manufacturer['manufacturer_id'], $manufacturer_id)) { ?>
								<input type="checkbox" name="manufacturer_id[]" value="<?php echo $manufacturer['manufacturer_id']; ?>" checked="checked" />
								<?php echo $manufacturer['name']; ?>
								<?php } else { ?>
								<input type="checkbox" name="manufacturer_id[]" value="<?php echo $manufacturer['manufacturer_id']; ?>" />
								<?php echo $manufacturer['name']; ?>
								<?php } ?>
							</label>
						</div>
						<?php } ?>
						</div>
					</div>
					</div>
					<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
						<?php if ($error_email) { ?>
						<div class="text-danger"><?php echo $error_email; ?></div>
						<?php } ?>
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-fax"><?php echo $entry_fax; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="fax" value="<?php echo $fax; ?>" placeholder="<?php echo $entry_fax; ?>" id="input-fax" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-address-2"><?php echo $entry_address_2; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
					<div class="col-sm-10">
						<select class="form-control" name="country_id" class="form-control" id="input-country">
						<?php foreach ($countries as $country) { ?>
						<option value="<?php echo $country['country_id']; ?>"<?php echo $country['country_id'] == $country_id ? ' selected="selected"' : ''; ?>><?php echo $country['name']; ?></option>
						<?php } ?></select>
					</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
					<div class="col-sm-10">
						<select class="form-control" name="zone_id" class="form-control" id="input-zone"></select>
					</div>
					</div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=setting/setting/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			html = '';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
//--></script>
<?php echo $footer; ?>