<?php
//==============================================================================
// Form Builder Pro v230.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================
?>

<?php if (version_compare(VERSION, '2.0', '<')) { ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<?php } ?>

<link rel="stylesheet" href="catalog/view/javascript/form_builder/picker.classic.min.css" />
<script type="text/javascript" src="catalog/view/javascript/form_builder/picker.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/form_builder/jquery.ui.widget.js"></script>
<script type="text/javascript" src="catalog/view/javascript/form_builder/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="catalog/view/javascript/form_builder/jquery.fileupload.js"></script>
<script type="text/javascript" src="catalog/view/javascript/form_builder/form_builder.js"></script>

<style type="text/css">
	.form-builder {
		width: 100%;
	}
	#form<?php echo $settings['module_id']; ?> .box-content {
		<?php if (empty($settings['positioning']) || $settings['positioning'] == 'absolute') { ?>
			height: <?php echo $total_rows * $settings['row_height']; ?>px;
		<?php } ?>
		position: relative;
	}
	.form-field {
		display: inline-block;
		vertical-align: top;
		<?php if (empty($settings['positioning']) || $settings['positioning'] == 'absolute') { ?>
			margin-left: 10px;
			overflow: hidden;
			position: absolute;
		<?php } else { ?>
			margin-bottom: 15px;
		<?php } ?>
	}
	.html-field {
		margin-left: 0 !important;
	}
	@media (max-width: 767px) {
		#form<?php echo $settings['module_id']; ?> .box-content {
			height: auto !important;
		}
		.form-field {
			position: static !important;
			width: 100% !important;
		}
	}
	#form<?php echo $settings['module_id']; ?> .form-field[data-parent] {
		display: none;
	}
	.form-field h4 {
		margin: 7px 0 !important;
	}
	.required-background {
		border-left: 3px solid #F00;
		border-radius: 5px;
		margin-left: 3px;
		padding-left: 5px;
	}
	.form-builder textarea {
		height: 70%;
	}
	.form-builder textarea,
	.form-builder input[type="text"],
	.form-builder input[type="password"],
	.form-builder label,
	.form-builder select {
		width: 90%;
	}
	.form-builder .picker select {
		width: auto;
	}
	.form-builder input[type="checkbox"],
	.form-builder input[type="radio"] {
		cursor: pointer;
	}
	<?php if (version_compare(VERSION, '2.0', '<')) { ?>
		.form-builder [disabled="disabled"] {
			opacity: 0.5;
			pointer-events: none;
		}
	<?php } ?>
	.form-builder [readonly] {
		background: #FFF;
		cursor: auto;
	}
	.form-builder label {
		border: 1px solid #FFF;
		cursor: pointer;
		display: block;
		padding: 0 5px;
		margin: 0;
	}
	.form-builder label:hover {
		border: 1px dashed #AAA;
		border-radius: 5px;
	}
	.file-field {
		display: none !important;
	}
	.file-upload-help, .file-upload-error, .file-upload-success {
		font-size: 11px;
	}
	.file-upload-help {
		color: #888;
	}
	.file-upload-error {
		color: #F00;
	}
	.file-upload-success {
		color: #080;
	}
	.file-upload-progress {
		margin-top: 10px;
	}
	.remove-icon {
		width: 12px;
		height: 12px;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAAAXNSR0IArs4c6QAAAAlwSFlzAAALEwAACxMBAJqcGAAABCRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIgogICAgICAgICAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICAgICAgICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDx0aWZmOkNvbXByZXNzaW9uPjU8L3RpZmY6Q29tcHJlc3Npb24+CiAgICAgICAgIDx0aWZmOlhSZXNvbHV0aW9uPjcyPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj43MjwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjEyPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT4xPC9leGlmOkNvbG9yU3BhY2U+CiAgICAgICAgIDxleGlmOlBpeGVsWURpbWVuc2lvbj4xMjwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAgICAgICAgIDxkYzpzdWJqZWN0PgogICAgICAgICAgICA8cmRmOlNlcS8+CiAgICAgICAgIDwvZGM6c3ViamVjdD4KICAgICAgICAgPHhtcDpNb2RpZnlEYXRlPjIwMTU6MDY6MTUgMTg6MDY6MzA8L3htcDpNb2RpZnlEYXRlPgogICAgICAgICA8eG1wOkNyZWF0b3JUb29sPlBpeGVsbWF0b3IgMy4zLjI8L3htcDpDcmVhdG9yVG9vbD4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cg1dGu8AAAHpSURBVCgVY2AAgmOelkm3or3/77M1dAbxYQDEB4mD5GFizMdczOIkjIzmS9rZMTAyMIb4fHy1d8nrT093asmYy9vb75B2dGAFivuHczDdn3vv6UUmMXGRhWLKSgzvp7QwiLH941IJCtq1R092DogG8cHiQHmQOpAtzFJM/z/LcHG6CUpJM/64ep6BT0SEXc7N24jjzVP2HxdPM3DpmTK8ePXu/9pT50v2vHx3nBlEyHx4+VLTwMSRm5uT7c/jBwz/Xj5l+PvqOQO7nBLDVz6xr6s2bc6vuPpoMtgGELHn7Zczlh9e6Ws6OGr/f/eGgeHPbwZmdk4GdnU9hr2r1mxIu/akAqQOBJhBxHZ1GT1TR9tijt8/BP9/+8LAxMTMwMjExMAEDAYxKSkul0/v9y99++klWMM+E0V1WV3t3fISonJ/Xj9nYOYVYGCRU2Rg/POX4f/HdwwCwiKCf7k4/IPZ/25d+OzDWyZhCekbquqqUr/evmbgEBBiePPn/78D23deANEgPkgcJA9SB7KB6dHnr/t+vv/AwC4kzPDxxx+GfSfOTvQ4etUaRIP4IHGQPEgdSAMIcK2w1pn9OMr9/zRDtUYgnwksCqRBfJA4SB6kDirOwMDDwCDqKcjlCxTghgtCGNwgcZA8TBwAL7/D1Ix5cVUAAAAASUVORK5CYII=');
		cursor: pointer;
		display: inline-block;
		margin-right: 5px;
		vertical-align: middle;
	}
	.picker__holder {
		outline: none;
	}
	<?php echo html_entity_decode($settings['additional_css'], ENT_QUOTES, 'UTF-8'); ?>
</style>

<div class="form-builder box" id="form<?php echo $settings['module_id']; ?>">
	<?php if (!empty($settings['heading_' . $language])) { ?>
		<div class="box-heading">
			<?php if (version_compare(VERSION, '2.0', '>=')) echo '<h3>'; ?>
			<?php echo $settings['heading_' . $language]; ?>
			<?php if (version_compare(VERSION, '2.0', '>=')) echo '</h3>'; ?>
		</div>
	<?php } ?>
	<div class="box-content">
		
		<form type="post" enctype="multipart/form-data">
			<?php $column_offset = array(0, 0, 0, 0, 0); ?>
			
			<?php foreach ($fields as $field) { ?>
				
				<?php if ($field['type'] == 'hidden') { ?>
					<input type="hidden" name="<?php echo $field['key']; ?>" value="<?php echo $field['data_' . $language]; ?>" />
					<?php continue; ?>
				<?php } ?>
				
				<?php
				$html_field = ($field['type'] == 'html') ? 'html-field' : '';
				$required = (!empty($field['required'])) ? 'required-field' : '';
				$parent = (!empty($field['parent'])) ? ' data-parent="' . trim(str_replace(': ', ':', $field['parent'])) . '"' : '';
				
				/*
				$columns = array($field['x']);
				if ($field['cols'] > 1) $columns[] = $field['x'] + 1;
				if ($field['cols'] > 2) $columns[] = $field['x'] + 2;
				if ($field['cols'] > 3) $columns[] = $field['x'] + 3;
				$columns = ' data-columns="' . implode(',', $columns) . '"';
				*/
				
				$css = 'width: ' . ($field['cols'] * 25 - 1) . '%;';
				
				if (empty($settings['positioning']) || $settings['positioning'] == 'absolute') {
					$css .= 'height: ' . ($field['rows'] * $settings['row_height']) . 'px;';
					$css .= 'left: ' . (($field['x'] - 1) * 25) . '%;';
					$css .= 'top: ' . (($field['y'] - 1) * $settings['row_height']) . 'px;';
				} elseif ($field['type'] == 'textarea') {
					$css .= 'height: ' . ($field['rows'] * $settings['row_height']) . 'px;';
				}
				?>
				
				<div class="form-field <?php echo $html_field . ' ' . $required; ?>" data-key="<?php echo $field['key']; ?>" <?php echo $parent; ?> style="<?php echo $css; ?>">
					
					<?php if ($html_field) { ?>
						
						<?php echo html_entity_decode($field['html_' . $language], ENT_QUOTES, 'UTF-8'); ?>
						
					<?php } else { ?>
						
						<?php if (isset($field['title_' . $language])) { ?>
							<h4><?php if ($required) { ?>
									<span style="color: #F00">*</span>
								<?php } ?>
								<?php echo $field['title_' . $language]; ?>
							</h4>
						<?php } ?>
						
						<?php if ($field['type'] == 'captcha') { ?>
							
							<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>" data-expired-callback="grecaptcha.reset()"></div>
							
						<?php } elseif ($field['type'] == 'checkbox' || $field['type'] == 'radio') { ?>
							
							<?php $defaults = array_map('trim', explode(';', $field['defaults_' . $language])); ?>
							<?php foreach (array_map('trim', explode(';', $field['choices_' . $language])) as $choice) { ?>
								<label>
									<?php $checked = (in_array($choice, $defaults)) ? 'checked="checked"' : ''; ?>
									<input type="<?php echo $field['type']; ?>" class="<?php echo $field['type']; ?>-field" name="<?php echo $field['key'] . ($field['type'] == 'checkbox' ? '[]' : ''); ?>" <?php echo $checked; ?> value="<?php echo $choice; ?>" />
									<?php echo $choice; ?>
								</label>
							<?php } ?>
							
						<?php } elseif ($field['type'] == 'date' || $field['type'] == 'time') { ?>
							
							<?php
							$attributes = 'data-interval="' . (!empty($field['interval']) ? $field['interval'] : '60') . '"';
							if (!empty($field['min'])) $attributes .= ' min="' . $field['min'] . '"';
							if (!empty($field['max'])) $attributes .= ' max="' . $field['max'] . '"';
							if (!empty($field['selectmonths'])) $attributes .= ' selectmonths="' . $field['selectmonths'] . '"';
							if (!empty($field['selectyears'])) $attributes .= ' selectyears="' . $field['selectyears'] . '"';
							if ($field['type'] == 'date') {
								$format = (!empty($settings['date_format'])) ? $settings['date_format'] : 'yyyy-mm-dd';
							} else {
								$format = (!empty($settings['time_format'])) ? $settings['time_format'] : 'h:i A';
							}
							$attributes .= ' format="' . $format . '"';
							?>        
							<input class="form-control <?php echo $field['type']; ?>-field" type="text" name="<?php echo $field['key']; ?>" value="<?php echo $field['default']; ?>" <?php echo $attributes; ?> />
							
						<?php } elseif ($field['type'] == 'email') { ?>
							
							<input type="text" class="form-control email-field" name="<?php echo $field['key']; ?>" placeholder="<?php echo $field['placeholder_' . $language]; ?>" />
							<?php if ($field['confirm']) { ?>
								<h4><?php if ($field['required']) { ?>
										<span style="color: #F00">*</span>
									<?php } ?>
									<?php echo $field['confirm_title_' . $language]; ?>
								</h4>
								<input type="text" class="form-control confirm-field" />
							<?php } ?>
							
						<?php } elseif ($field['type'] == 'file') { ?>
							
							<?php $limit = (isset($field['limit'])) ? (int)$field['limit'] : ''; ?>
							<input id="<?php echo $field['key']; ?>" class="file-field" type="file" name="file" <?php if (!$limit || $limit > 1) echo 'multiple'; ?> data-limit="<?php echo $limit; ?>" data-url="index.php?route=<?php echo $type . '/' . $name; ?>/upload&module_id=<?php echo $settings['module_id']; ?>">
							<a class="button btn btn-primary" onclick="$(this).prev().click()"><?php echo $field['button_text_' . $language]; ?></a>
							&nbsp;
							<span class="file-upload-help"><?php echo $settings['file_extensions']; ?></span>
							<div class="file-upload-progress"></div>
							
						<?php } elseif ($field['type'] == 'select') { ?>
							
							<?php $defaults = array_map('trim', explode(';', $field['defaults_' . $language])); ?>
							<?php if ($field['size'] > 1) { ?>
								<select class="form-control" name="<?php echo $field['key']; ?>[]" multiple="multiple" size="<?php echo (int)$field['size']; ?>">
							<?php } else { ?>
								<select class="form-control" name="<?php echo $field['key']; ?>">
							<?php } ?>
								<?php foreach (array_map('trim', explode(';', $field['choices_' . $language])) as $choice) { ?>
									<option value="<?php echo $choice; ?>" <?php if (in_array($choice, $defaults)) echo 'selected="selected"'; ?>><?php echo $choice; ?></option>
								<?php } ?>
							</select>
							
						<?php } elseif ($field['type'] == 'submit') { ?>
							
							<a class="button btn btn-primary" onclick="submitForm($(this), <?php echo $settings['module_id']; ?>, $(this).next().html(), $(this).next().next().html(), '<?php echo (!empty($field['redirect']) && substr($field['redirect'], 0, 4) != 'http' ? 'http://' : '') . $field['redirect']; ?>')"><?php echo $field['button_text_' . $language]; ?></a>
							<div style="display: none"><?php echo $field['please_wait_' . $language]; ?></div>
							<div style="display: none"><?php echo html_entity_decode($field['success_' . $language], ENT_QUOTES, 'UTF-8'); ?></div>
							
						<?php } elseif ($field['type'] == 'text' || $field['type'] == 'password') { ?>
							
							<?php $onblur = ($field['min_length']) ? 'onblur="validateMin($(this), ' . (int)$field['min_length'] . ')"' : ''; ?>
							<?php $onkeypress = ($field['max_length'] || $field['allowed']) ? 'onkeypress="validateMaxAllowed($(this), event, ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed']) . '\')"' : ''; ?>
							<?php $onpaste = ($field['max_length'] || $field['allowed']) ?  'onpaste="validatePaste($(this), ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed']) . '\')"' : ''; ?>
							<input class="form-control" type="<?php echo $field['type']; ?>" name="<?php echo $field['key']; ?>" <?php echo $onblur; ?> <?php echo $onkeypress; ?> <?php echo $onpaste; ?> placeholder="<?php echo $field['placeholder_' . $language]; ?>" />
							
						<?php } elseif ($field['type'] == 'textarea') { ?>
							
							<?php $onblur = ($field['min_length']) ? 'onblur="validateMin($(this), ' . (int)$field['min_length'] . ')"' : ''; ?>
							<?php $onkeypress = ($field['max_length'] || $field['allowed']) ? 'onkeypress="validateMaxAllowed($(this), event, ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed']) . '\')"' : ''; ?>
							<?php $onpaste = ($field['max_length'] || $field['allowed']) ?  'onpaste="validatePaste($(this), ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed']) . '\')"' : ''; ?>
							<textarea class="form-control" name="<?php echo $field['key']; ?>" <?php echo $onblur; ?> <?php echo $onkeypress; ?> <?php echo $onpaste; ?> placeholder="<?php echo $field['placeholder_' . $language]; ?>"></textarea>
							
						<?php } ?>
						
					<?php } ?>
				</div> <!-- .form-field -->
			<?php } ?>
		</form>
	</div>
</div>

<?php
	$form_language = "{";
	foreach ($settings as $setting_name => $setting) {
		if (strpos($setting_name, 'error_') !== 0 || strpos($setting_name, '_' . $language) === false) continue;
		$form_language .= "'" . str_replace('_' . $language, '', $setting_name) . "': '" . addslashes($setting) . "', ";
	}
	$form_language .= "'button_continue': '" . $button_continue . "', 'button_delete': '" . $button_delete . "?'}";
?>
<script type="text/javascript">
	var form_language = <?php echo $form_language; ?>
</script>
