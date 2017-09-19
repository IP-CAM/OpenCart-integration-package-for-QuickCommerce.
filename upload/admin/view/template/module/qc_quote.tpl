<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-qc-admin" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
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
		<div class="display display-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="display">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-qc-admin" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<select name="<?php echo $_name; ?>[status]" id="input-status" class="form-control">
								<option value="1" <?php echo (!empty($data['status']) && $data['status'] == '1') ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
								<option value="0"  <?php echo (empty($data['status']) || $data['status']== '0') ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-display"><?php echo $setting_showon; ?>
						</label>
						<div class="col-sm-10">
							<select name="<?php echo $_name; ?>[display]" id="input-display" class="form-control">
								<option value="default" <?php echo ((isset($data['display']) && $data['display'] == 'default')) ? 'selected=selected' : '' ?>><?php echo $setting_showon_h2; ?></option>
								<option value="selected" <?php echo ((isset($data['display']) && $data['display'] == 'selected')) ? 'selected=selected' : '' ?>><?php echo $setting_showon_h3; ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-notifications"><?php echo $setting_notification; ?><br>
							<span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_notification_h; ?></span>
						</label>
						<div class="col-sm-10">
							<select name="<?php echo $_name; ?>[notifications]" id="input-notifications" class="form-control">
								<option value="1" <?php echo ((isset($data['notifications']) && $data['notifications'] == '1')) ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
								<option value="0" <?php echo ((isset($data['notifications']) && $data['notifications'] == '0')) ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-width"><?php echo $setting_popupwidth; ?><br>
							<span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_popupwidth_h; ?></span>
						</label>
						<div class="col-sm-2">
							<div class="input-group">
								<input type="text" name="<?php echo $_name; ?>[popup_width]" value="<?php echo (isset($data['popup_width'])) ? $data['popup_width'] : '320' ?>" placeholder="" id="input-width" class="form-control" />
								<span class="input-group-addon">px</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $setting_design; ?><br>
							<span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_design_h; ?></span>
						</label>
						<div class="col-sm-10">
							<?php foreach ($languages as $language) { ?>
							<img src="view/image/flags/<?php echo $language['image']; ?>" style="float:left;position:absolute;margin-left:-20px;" title="<?php echo $language['name']; ?>" />
							<?php echo $setting_design_h2; ?>
							<input name="<?php echo $_name; ?>[title][<?php echo $language['language_id']; ?>]" class="form-control" type="text" value="<?php echo (isset($data['title'][$language['language_id']])) ? $data['title'][$language['language_id']] : 'display me!' ?>" />
            	<textarea id="input-description_<?php echo $language['language_id']; ?>" name="<?php echo $_name; ?>[custom_text][<?php echo $language['language_id']; ?>]" class="form-control">
								<?php if (isset($data['custom_text'][$language['language_id']])) {
								 echo $data['custom_text'][$language['language_id']];
								} else { ?>
								<p align="left"><span style="line-height: 1.6em;">Please provide a callback number and/or email address and we'll get back to you with a quotation as soon as we can!</span></p>
									<p align="left">Name:<br>{name_field}</p>
								  <p align="left">Phone:<br>{phone_field}</p>
								  <p align="left">Notes:<br>{notes_field}</p>
								  <p align="left">{submit_button}</p>
								<?php } ?>
							</textarea><br>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-mode"><?php echo $setting_captcha; ?><br>
							<span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_captcha_h; ?></span>
						</label>
						<div class="col-sm-10">
							<select name="<?php echo $_name; ?>[use_captcha]" id="input-use-captcha" class="form-control">
								<option value="0" <?php echo ((isset($data['use_captcha']) && $data['use_captcha'] == '0')) ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
								<option value="1" <?php echo ((isset($data['use_captcha']) && $data['use_captcha'] == '1')) ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
							</select>
							<div class="captcha-info" style="margin-top:10px;">
								<div class="input-group">
									<span class="input-group-addon"><?php echo $setting_captcha_h2; ?></span>
									<input type="text" name="<?php echo $_name; ?>[site_key]" class="form-control" value="<?php echo (isset($data['site_key'])) ? $data['site_key'] : '' ?>" />
								</div>
								<div class="input-group" style="margin-top:10px;">
									<span class="input-group-addon"><?php echo $setting_captcha_h3; ?></span>
									<input type="text" name="<?php echo $_name; ?>[secret_key]" class="form-control" value="<?php echo (isset($data['secret_key'])) ? $data['secret_key'] : '' ?>" />
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-custom-css"><?php echo $setting_css; ?><br>
							<span class="help"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $setting_css_h; ?></span>
						</label>
						<div class="col-sm-10">
							<textarea cols="100" rows="8" name="<?php echo $_name; ?>[custom_css]" placeholder="<?php echo $setting_css_h2; ?>" id="input-custom-css"><?php echo (isset($data['custom_css'])) ? $data['custom_css'] : '' ?></textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
	$(document).ready(function () {
		$('#button-qbo-connect').on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			// The qbo trigger is nested in the button so we have to click it programmatically
			$(this).find('a').first().trigger('click');
			//$('#qbo-connect-modal').modal('toggle');
		});

		$('#button-qbo-disconnect').on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var action = confirm('Are you sure you want to disconnect from Intuit services?');

			if (action) {
				$.ajax({
					url: '<?php echo $disconnect_url; ?>',
					success: function () {
						window.location.reload(true);
					}
				});
			}
			//$('#qbo-connect-modal').modal('toggle');
		});
	});
</script>
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script type="text/javascript">
	intuit.ipp.anywhere.setup({
		menuProxy: '<?php echo $menu_url; ?>',
		grantUrl: '<?php echo $oauth_url; ?>'
	});
</script>
<script type="text/javascript"><!--
		$('[id^=input-description]').summernote({height: 300});
	//--></script>

<!-- Override QBO settings -->
<style scoped>
	#button-qbo-connect .intuitPlatformConnectButton,
	#button-qbo-connect .intuitPlatformReconnectButton {
		background: none;
		border: medium none;
		display: flex;
		outline: medium none;
		text-decoration: none;
		text-indent: -9000px;
		text-transform: capitalize;
		width: 0; /* Hidden Button we click it programmatically */
		height: 0; /* Hidden Button we click it programmatically */
	}
	.control-label .help {
		font-weight: normal !important;
	}

	label.control-label span::after {
		content: none;
	}
</style>