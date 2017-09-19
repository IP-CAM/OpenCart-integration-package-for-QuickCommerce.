<?php
//==============================================================================
// Form Builder Pro v230.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================
?>

<style type="text/css">
	.input-group, input[type="text"] {
		width: 100% !important;
	}
	#buttons-column {
		display: inline-block;
		position: absolute;
		width: 150px;
	}
	#buttons-column a {
		text-align: left;
	}
	#fields-column {
		display: inline-block;
		margin-left: 170px;
	}
	#first-time-help a {
		color: #666;
	}
	.gridster {
		width: 800px;
		min-height: 500px;
	}
	<?php if (version_compare(VERSION, '2.0', '<')) { ?>
		.gridster h4 {
			font-size: 14px;
			margin: 0;
		}
	<?php } ?>
	.gridster ul {
		list-style-type: none;
		padding: 0;
		margin: 0;
	}
	.gridster li {
		background: #FFF;
		border: 1px solid white;
		border-radius: 5px;
		cursor: move;
		overflow: hidden;
		padding: 10px !important;
	}
	.gridster li:hover, .bordered, [aria-describedby] {
		border: 1px dashed gray !important;
	}
	.gridster .btn-xs {
		display: none;
		position: absolute;
		width: 50px;
	}
	.gridster .btn-primary.btn-xs {
		right: 70px;
	}
	.gridster .btn-danger.btn-xs, .gridster .btn-success.btn-xs {
		right: 10px;
	}
	.gridster li:hover .btn-xs:not([hidden]) {
		display: block;
	}
	.gridster input[type="email"],
	.gridster input[type="file"],
	.gridster input[type="text"],
	.gridster input[type="date"],
	.gridster input[type="time"] {
		width: 100% !important;
	}
	.gridster select[multiple] {
		height: 70px !important;
		width: 150px !important;
	}
	.gridster textarea {
		height: 80% !important;
		width: 100% !important;
	}
	.popover, .popover-content {
		width: 400px !important;
		max-width: 400px !important;
	}
	.popover textarea {
		font-family: monospace;
		height: 300px !important;
	}
	#html-editor {
		background: #FFF;
		border: 1px solid rgba(0, 0, 0, 0.2);
		border-radius: 5px;
		box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		padding: 10px 10px 0;
		position: absolute;
		right: 10%;
		width: 850px;
		z-index: 9999;
	}
	.captcha-example {
		border: 1px solid #CCC;
		border-radius: 3px;
		height: 75px;
		padding: 25px;
		text-align: center;
		width: 300px;
	}
	.grid {
		background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAAAyCAYAAAAZUZThAAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAALEwAACxMBAJqcGAAABCVpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIgogICAgICAgICAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICAgICAgICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDx0aWZmOkNvbXByZXNzaW9uPjU8L3RpZmY6Q29tcHJlc3Npb24+CiAgICAgICAgIDx0aWZmOlhSZXNvbHV0aW9uPjcyPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj43MjwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjIwMDwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDxleGlmOkNvbG9yU3BhY2U+MTwvZXhpZjpDb2xvclNwYWNlPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+NTA8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICAgICA8ZGM6c3ViamVjdD4KICAgICAgICAgICAgPHJkZjpTZXEvPgogICAgICAgICA8L2RjOnN1YmplY3Q+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE1OjA1OjE1IDExOjA1Ojg1PC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5QaXhlbG1hdG9yIDMuMy4yPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgrcE1oIAAABSElEQVR4Ae3cQQ6CMBAF0ApH4f4nIhwFUGLYlhU/GXiujE2c9s2Y6A/YmgcBAgQIECBAgAABAgQyAsuy7EOmlCoE6gms69p8QOr1zY5DAuM4hiopQ4AAAQIECBAgQIAAgdakWKaAQEdAitXBsURAimUGCBAgQIAAAQIECBBICkixktpqlROQYpVrmQ0nBaRYSW21CBAgQIAAAQIECBCQYpkBAh2BbdvcUdjxsfRygWFww+3LR8DxCRAgQIAAAQIECEQF5nn2v1hRccXKCfiZXq5lNpwSkGKlpNUhQIAAAQIECBAgQOAQcC2WOSBwISDFugCyTIAAAQIECBAgQIAAAQIEEgJSrISyGqUFpFil22fzBAgQIECAAAECBAgQIPAUASnWUzrpHLcJSLFuo/XGBAgQeLjA5zzf8X3rfD5Nk9d/GBz+E/Fmhy+at6zSY9AmFwAAAABJRU5ErkJggg==);
	}
</style>

<div id="help-modal" class="modal fade" hidden>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">&times;</a>
				<h4 class="modal-title"><?php echo $data['tab_form_fields'] . ' ' . $data['button_help']; ?></h4>
			</div>
			<div class="modal-body">
				<?php echo $data['help_form_fields']; ?>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo $data['button_close']; ?></a>
			</div>
		</div>
	</div>
</div>

<div id="buttons-column">
	<a class="btn btn-success btn-block" data-toggle="modal" href="#help-modal"><i class="fa fa-fw fa-lg fa-question-circle"></i> <?php echo $data['button_help']; ?></a>
	<a id="toggle-grid-button" class="btn btn-success btn-block" onclick="$('.gridster').toggleClass('grid'); $('#toggle-grid-button, .gridster li').toggleClass('bordered')">
		<i class="fa fa-fw fa-lg fa-table"></i> <?php echo $data['button_toggle_grid']; ?>
	</a>
	<a class="btn btn-primary btn-block" onclick="addField('captcha')"><i class="fa fa-fw fa-lg fa-shield pad-right-sm"></i> <?php echo $data['button_captcha']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('checkbox')"><i class="fa fa-fw fa-lg fa-check-square-o pad-right-sm"></i> <?php echo $data['button_checkboxes']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('date')"><i class="fa fa-fw fa-lg fa-calendar pad-right-sm"></i> <?php echo $data['button_date']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('email')"><i class="fa fa-fw fa-lg fa-envelope pad-right-sm"></i> <?php echo $data['button_email_address']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('file')"><i class="fa fa-fw fa-lg fa-upload pad-right-sm"></i> <?php echo $data['button_file_upload']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('hidden')"><i class="fa fa-fw fa-lg fa-eye-slash pad-right-sm"></i> <?php echo $data['button_hidden_data']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('html')"><i class="fa fa-fw fa-lg fa-code pad-right-sm"></i> <?php echo $data['button_html_block']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('radio')"><i class="fa fa-fw fa-lg fa-dot-circle-o pad-right-sm"></i> <?php echo $data['button_radio_buttons']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('select')"><i class="fa fa-fw fa-lg fa-list pad-right-sm"></i> <?php echo $data['button_select_dropdown']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('submit')"><i class="fa fa-fw fa-lg fa-square pad-right-sm"></i> <?php echo $data['button_submit_button']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('text')"><i class="fa fa-fw fa-lg fa-keyboard-o pad-right-sm"></i> <?php echo $data['button_text_input']; ?></a>
	<a class="btn btn-primary btn-block" onclick="addField('time')"><i class="fa fa-fw fa-lg fa-clock-o pad-right-sm"></i> <?php echo $data['button_time']; ?></a>
</div>

<div id="fields-column">
	<?php if (empty($data['module_id'])) { ?>
		<div id="first-time-help" style="position: absolute"><?php echo $data['help_form_fields']; ?></div>
	<?php } ?>
	<div class="gridster">
		<ul></ul>
	</div>
</div>

<div hidden id="captcha-html">
	<li class="captcha-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_captcha']; ?></h4>
			<div class="captcha-example"><em><?php echo $data['text_recaptcha_will_appear']; ?></em></div>
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="captcha" />
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_title']; ?></label>
				<div class="col-sm-8">
					<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
						<div class="input-group">
							<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
							<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][title_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</li>
</div>

<?php foreach (array('checkbox', 'radio', 'select') as $input_type) { ?>
	<div hidden id="<?php echo $input_type; ?>-html">
		<li class="<?php echo $input_type; ?>-field">
			<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
			<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
			<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
			<span class="field-demo">
				<?php if ($input_type == 'checkbox') { ?>
					<h4><?php echo $data['button_checkboxes']; ?></h4>
					<input type="checkbox" /> <?php echo $data['text_checkbox']; ?><br />
					<input type="checkbox" checked="checked" /> <?php echo $data['text_checkbox']; ?><br />
					<input type="checkbox" /> <?php echo $data['text_checkbox']; ?><br />
				<?php } elseif ($input_type == 'radio') { ?>
					<h4><?php echo $data['button_radio_buttons']; ?></h4>
					<input type="radio" /> <?php echo $data['text_radio']; ?><br />
					<input type="radio" checked="checked" /> <?php echo $data['text_radio']; ?><br />
					<input type="radio" /> <?php echo $data['text_radio']; ?><br />
				<?php } elseif ($input_type == 'select') { ?>
					<h4><?php echo $data['button_select_dropdown']; ?></h4>
					<select class="form-control"><option><?php echo $data['text_select']; ?></option></select><br />
				<?php } ?>
			</span>
			<div class="field-data" hidden>
				<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="<?php echo $input_type; ?>" />
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_required']; ?></label>
					<div class="col-sm-8">
						<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][required]">
							<option value="0"><?php echo $data['text_no']; ?></option>
							<option value="1"><?php echo $data['text_yes']; ?></option>
						</select>
					</div>
				</div>
				<?php foreach (array('title', 'choices', 'defaults') as $input_group) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
						<div class="col-sm-8">
							<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
								<div class="input-group">
									<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
									<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($input_type == 'select') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4"><?php echo $data['text_size']; ?></label>
						<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][size]" value="1" /></div>
					</div>
				<?php } ?>
			</div>
		</li>
	</div>
<?php } ?>

<?php foreach (array('date', 'time') as $input_type) { ?>
	<div hidden id="<?php echo $input_type; ?>-html">
		<li class="<?php echo $input_type; ?>-field">
			<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
			<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
			<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
			<span class="field-demo">
				<h4><?php echo $data['button_' . $input_type]; ?></h4>
				<input type="text" class="form-control" />
			</span>
			<div class="field-data" hidden>
				<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="<?php echo $input_type; ?>" />
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_required']; ?></label>
					<div class="col-sm-8">
						<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][required]">
							<option value="0"><?php echo $data['text_no']; ?></option>
							<option value="1"><?php echo $data['text_yes']; ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_title']; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][title_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_default_value']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][default]" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_earliest_value']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][min]" placeholder="<?php echo $data['placeholder_' . $input_type . '_format']; ?>" /></div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_latest_value']; ?></label>
					<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][max]" placeholder="<?php echo $data['placeholder_' . $input_type . '_format']; ?>" /></div>
				</div>
				<?php if ($input_type == 'time') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4"><?php echo $data['text_interval']; ?></label>
						<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][interval]" /></div>
					</div>
				<?php } ?>
			</div>
		</li>
	</div>
<?php } ?>

<div hidden id="email-html">
	<li class="email-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_email_address']; ?></h4>
			<input type="email" class="form-control" />
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="email" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_required']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][required]">
						<option value="0"><?php echo $data['text_no']; ?></option>
						<option value="1"><?php echo $data['text_yes']; ?></option>
					</select>
				</div>
			</div>
			<?php foreach (array('title', 'placeholder') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_require_confirmation']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][confirm]">
						<option value="0"><?php echo $data['text_no']; ?></option>
						<option value="1"><?php echo $data['text_yes']; ?></option>
					</select>
				</div>
			</div>
			<?php foreach (array('confirm_title', 'confirm_placeholder') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</li>
</div>

<div hidden id="file-html">
	<li class="file-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_file_upload']; ?></h4>
			<a class="btn btn-primary"><?php echo $data['button_file_upload']; ?></a>
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="file" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_required']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][required]">
						<option value="0"><?php echo $data['text_no']; ?></option>
						<option value="1"><?php echo $data['text_yes']; ?></option>
					</select>
				</div>
			</div>
			<?php foreach (array('title', 'button_text', 'success') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_file_limit']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][limit]" /></div>
			</div>
		</div>
	</li>
</div>

<div hidden id="hidden-html">
	<li class="hidden-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_hidden_data']; ?></h4>
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="hidden" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<?php foreach (array('title', 'data') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_display_in_customers_email']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][email]">
						<option value="0"><?php echo $data['text_no']; ?></option>
						<option value="1"><?php echo $data['text_yes']; ?></option>
					</select>
				</div>
			</div>
		</div>
	</li>
</div>

<div hidden id="html-html">
	<li class="html-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_html_block']; ?></h4>
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="html" />
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<div class="form-group">
				<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
					<div class="input-group">
						<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
						<textarea class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][html_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>"></textarea>
					</div>
				<?php } ?>
			</div>
		</div>
	</li>
</div>

<div hidden id="submit-html">
	<li class="submit-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<a class="btn btn-primary"><?php echo $data['button_submit_button']; ?></a>
		</span>
		<div class="field-data" hidden>
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][type]" value="submit" />
			<input type="hidden" class="nosave" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" />
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<?php foreach (array('button_text', 'please_wait', 'success') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_redirect_url']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][redirect]" /></div>
			</div>
		</div>
	</li>
</div>

<div hidden id="text-html">
	<li class="text-field">
		<a class="btn btn-danger btn-xs" onclick="deleteField($(this).parent())"><?php echo $data['button_delete']; ?></a>
		<a class="btn btn-primary btn-xs" onclick="openPopover($(this).parent())"><?php echo $data['button_edit']; ?></a>
		<a class="btn btn-success btn-xs" onclick="closePopover($(this).parent())" hidden><?php echo $data['button_save']; ?></a>
		<span class="field-demo">
			<h4><?php echo $data['button_text_input']; ?></h4>
			<input type="text" class="form-control" />
		</span>
		<div class="field-data" hidden>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_type']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][type]">
						<option value="text"><?php echo $data['text_text']; ?></option>
						<option value="password"><?php echo $data['text_password']; ?></option>
						<option value="textarea"><?php echo $data['text_textarea']; ?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_key']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][key]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_parent']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][parent]" placeholder="<?php echo $data['placeholder_setting_a_parent']; ?>" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_required']; ?></label>
				<div class="col-sm-8">
					<select class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][required]">
						<option value="0"><?php echo $data['text_no']; ?></option>
						<option value="1"><?php echo $data['text_yes']; ?></option>
					</select>
				</div>
			</div>
			<?php foreach (array('title', 'placeholder') as $input_group) { ?>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $data['text_' . $input_group]; ?></label>
					<div class="col-sm-8">
						<?php foreach ($data['language_array'] as $language_code => $language_name) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="<?php echo $data['language_flags'][$language_code]; ?>" /></span>
								<input type="text" class="nosave form-control" name="<?php echo 'module_' . $data['module_id'] . '_fields[0][' . $input_group . '_' . $language_code . ']'; ?>" placeholder="<?php echo $language_name; ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_min_length']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][min_length]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_max_length']; ?></label>
				<div class="col-sm-8"><input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][max_length]" /></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $data['text_allowed_characters']; ?></label>
				<div class="col-sm-8">
					<input type="text" class="nosave form-control" name="module_<?php echo $data['module_id']; ?>_fields[0][allowed]" />
					<?php echo $data['help_allowed_characters']; ?>
				</div>
			</div>
		</div>
	</li>
</div>

<input id="form-layout" type="hidden" name="module_<?php echo $data['module_id']; ?>_layout" value="" />

<script type="text/javascript">
	$(document).ready(function(){
		$('.summernote').summernote({
			defaultFontName: 'Arial',
			height: 150,
		});
	}).on('dblclick', '.gridster li', function(){
		openPopover($(this));
	}).on('keypress', '[name*="[key]"]', function(e){
		if (('abcdefghijklmnopqrstuvwxyz01234567890').indexOf(String.fromCharCode(e.which).toLowerCase()) == -1) {
			e.preventDefault();
		}
	}).on('paste', '[name*="[key]"]', function(e){
		e.preventDefault();
	});
	
	function addField(type) {
		$('#first-time-help').remove();
		
		var firstUnusedNumber = 1;
		while ($('.gridster').html().indexOf('fields[' + firstUnusedNumber + ']') != -1) {
			firstUnusedNumber++;
		}
		var html = $('#' + type + '-html').html().replace(/nosave/g, '').replace(/fields\[0\]/g, 'fields[' + firstUnusedNumber + ']');
		
		gridster.add_widget(html, 4, 2, 1, 999);
		window.scrollTo(0, document.body.scrollHeight);
		openPopover($('.gridster li:last-child'));
	}
	
	function deleteField(element) {
		if (confirm('<?php echo $data['standard_confirm']; ?>')) {
			gridster.remove_widget(element);
			closePopover(element);
			element.remove();
		}
	}
	
	function openPopover(element) {
		$('.btn-primary, .btn-danger').attr('hidden', 'hidden');
		element.find('.btn-success').show();
		$('.btn-block').attr('disabled', 'disabled');
		$('.container-fluid + hr').css('margin-top', '400px');
		$('.gs-resize-handle').hide();
		$('.gridster li').css('cursor', 'auto');
		gridster.disable();
		
		if (element.hasClass('html-field')) {
			$('body').prepend('<div id="html-editor" style="top: ' + (element.find('.btn-success').offset().top + 30) + 'px">' + element.find('.field-data').html() + '</div>');
			$('#html-editor').find('textarea').addClass('summernote').summernote({
				defaultFontName: 'Arial',
				height: 150,
			});
		} else {
			element.popover({
				html: true,
				placement: 'bottom',
				title: 'Settings',
				content: element.find('.field-data').html(),
				trigger: 'focus',
			}).popover('show');
		}
	}
	
	function closePopover(element) {
		$('.btn-primary, .btn-danger').removeAttr('hidden');
		element.find('.btn-success').hide();
		$('.btn-block').removeAttr('disabled');
		$('.container-fluid + hr').css('margin-top', '10px');
		$('.gs-resize-handle').show();
		$('.gridster li').css('cursor', 'move');
		gridster.enable();
		
		if (element.hasClass('html-field')) {
			$('#html-editor').find('.summernote').each(function(i){
				element.find('textarea').eq(i).html($(this).summernote('code'));
			});
			$('#html-editor').find('input').each(function(i){
				element.find('input').eq(i).attr('value', $(this).val());
			});
			$('#html-editor').find('.summernote').summernote('destroy');
			$('#html-editor').remove();
		} else {
			var popover = element.next().find('.popover-content');
			setInputAttributes(popover);
			element.find('.field-data').html(popover.html());
			element.popover('destroy');
		}
		
		if (element.find('[name*="[key]"]').val() == '') {
			element.find('[name*="[key]"]').attr('value', element.find('[name*="[type]"]').val() + (element.index() + 1));
		}
		
		$('#form-layout').val(gridster.serialize());
		setDemo(element);
	}
	
	function setDemo(element) {
		var html = '';
		
		var required = (element.find('[name*="[required]"] option[selected]').attr('value') > 0) ? '<span style="color: #F00">*</span> ' : '';
		html += (element.find('[name*="[title_"]').val()) ? '<h4>' + required + element.find('[name*="[title_"]').val() + '</h4>' : '';
		
		if (element.hasClass('captcha-field')) {
			html += '<div class="captcha-example"><em><?php echo $data['text_recaptcha_will_appear']; ?></em></div>';
		} else if (element.hasClass('checkbox-field') || element.hasClass('radio-field')) {
			var defaults = $.map(element.find('[name*="[defaults_"]').val().split(';'), function(n,i){ return n.trim() });
			var choices = $.map(element.find('[name*="[choices_"]').val().split(';'), function(n,i){ return n.trim() });
			for (i in choices) {
				var checked = ($.inArray(choices[i], defaults) === -1) ? '' : 'checked="checked"';
				html += '<input type="' + (element.hasClass('checkbox-field') ? 'checkbox' : 'radio') + '" ' + checked + ' /> ' + choices[i] + '<br />';
			}
		} else if (element.hasClass('select-field')) {
			var defaults = $.map(element.find('[name*="[defaults_"]').val().split(';'), function(n,i){ return n.trim() });
			var choices = $.map(element.find('[name*="[choices_"]').val().split(';'), function(n,i){ return n.trim() });
			var size = element.find('[name*="[size]"]').val();
			html += '<select class="form-control"' + (size > 1 ? 'multiple size="' + size + '"' : '') + '>';
			for (i in choices) {
				var selected = ($.inArray(choices[i], defaults) === -1) ? '' : 'selected="selected"';
				html += '<option ' + selected + '>' + choices[i] + '</option>';
			}
			html += '</select>';
		} else if (element.hasClass('date-field') || element.hasClass('time-field')) {
			html += '<input class="form-control" type="text" value="' + element.find('[name*="[default]"]').val() + '" />';
		} else if (element.hasClass('email-field')) {
			html += '<input class="form-control" type="email" placeholder="' + element.find('[name*="[placeholder_"]').val() + '" />';
			if (element.find('[name*="[confirm]"] option[selected]').attr('value') > 0) {
				html += '<br /><br /><h4>' + required + element.find('[name*="[confirm_title_"]').val() + '</h4>';
				html += '<input class="form-control" type="email" placeholder="' + element.find('[name*="[confirm_placeholder_"]').val() + '" />';
			}
		} else if (element.hasClass('file-field')) {
			html += '<a class="btn btn-primary">' + element.find('[name*="[button_text_"]').val() + '</a> &nbsp; <span class="help-text">' + $('body').find('[name*="file_extensions"]').val() + '</span>';
		} else if (element.hasClass('hidden-field')) {
			html += '<em>' + element.find('[name*="[data_"]').val() + '</em>';
		} else if (element.hasClass('html-field')) {
			html += element.find('[name*="[html_"]').val();
		} else if (element.hasClass('submit-field')) {
			html += '<a class="btn btn-primary" onclick="alert(\'' + element.find('[name*="[success_"]').val().replace("'", '') + '\')">' + element.find('[name*="[button_text_"]').val() + '</a>';
		} else if (element.hasClass('text-field')) {
			if (element.find('[name*="[type]"] option[selected]').attr('value') == 'textarea') {
				html += '<textarea class="form-control" placeholder="' + element.find('[name*="[placeholder_"]').val() + '"></textarea>';
			} else {
				html += '<input class="form-control" type="' + element.find('[name*="[type]"]').val() + '" placeholder="' + element.find('[name*="[placeholder_"]').val() + '" />';
			}
		}
		
		element.find('.field-demo').html(html);
	}
	
	//var widgetWidth = Math.round($(window).width() * 1/6);
	gridster = $('.gridster ul').gridster({
		widget_base_dimensions: [200, <?php echo (!empty($data['saved']['module_' . $data['module_id'] . '_row_height'])) ? $data['saved']['module_' . $data['module_id'] . '_row_height'] : '50'; ?>],
		widget_margins: [0, 0],
		max_cols: 4,
		serialize_params: function($w, wgd) {
			return $w.find('[name*="[key]"]').val() + ':' + wgd.col + '-' + wgd.row + '-' + wgd.size_x + '-' + wgd.size_y;
		},
		resize: {
			enabled: true,
			start: function(){
				$('.gridster li').addClass('bordered');
			},
			stop: function(){
				if (!$('#toggle-grid-button').hasClass('bordered')) $('.bordered').removeClass('bordered');
				$('#form-layout').val(gridster.serialize());
			}
		},
		draggable: {
			start: function(){
				$('.gridster li').addClass('bordered');
			},
			stop: function(){
				if (!$('#toggle-grid-button').hasClass('bordered')) $('.bordered').removeClass('bordered');
				$('#form-layout').val(gridster.serialize());
			}
		}
	}).data('gridster');
	
	$(document).ready(function(){
		<?php if (!empty($data['saved']['module_' . $data['module_id'] . '_fields'])) { ?>
			var widgets = [];
			
			<?php
			$layouts = array();
			
			foreach (explode(',', $data['saved']['module_' . $data['module_id'] . '_layout']) as $layout) {
				$pair = explode(':', $layout);
				$layouts[$pair[0]] = $pair[1];
			}
			
			$fields = array();
			
			foreach ($data['saved']['module_' . $data['module_id'] . '_fields'] as $field) {
				if (empty($layouts[$field['key']])) continue;
				$pos = explode('-', $layouts[$field['key']]);
				$fields[$pos[1] . $pos[0]] = $field;
			}
			
			ksort($fields);
			
			$i = 1;
			foreach ($fields as $field) {
				if (empty($field['key']) || empty($field['type'])) continue;
				
				if ($field['type'] == 'textarea' || $field['type'] == 'password') {
					$field_type = 'text';
				} else {
					$field_type = $field['type'];
				}
			?>
				var clone = $('#<?php echo $field_type; ?>-html').clone();
				clone.html(clone.html().replace(/nosave/g, '').replace(/fields\[0\]/g, 'fields[<?php echo $i; ?>]'));
				
				<?php foreach ($field as $key => $value) { ?>
					clone.find('[name*="[<?php echo $key; ?>]"]').val('<?php echo str_replace(array("'", "\n"), array("\'", "\\n"), html_entity_decode($value, ENT_QUOTES, 'UTF-8')); ?>');
				<?php } ?>
				
				<?php $pos = explode('-', $layouts[$field['key']]); ?>
				setInputAttributes(clone);
				setDemo(clone.find('li'));
				gridster.add_widget(clone.html(), <?php echo $pos[2] . ', ' . $pos[3] . ', ' . $pos[0] . ', ', $pos[1]; ?>);
				<?php $i++; ?>
			<?php } ?>
			$('#form-layout').val(gridster.serialize());
		<?php } ?>
	});
</script>