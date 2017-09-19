<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-gtsecure" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $text_url_message; ?></div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-gtsecure" class="form-horizontal">
          <ul class="nav nav-tabs" id="tabs">
			<?php foreach ($tabs as $tab) { ?>
            <li><a href="#<?php echo $tab['id']; ?>" data-toggle="tab"><?php echo $tab['title']; ?></a></li>
			<?php } ?>
          </ul>
          <div class="tab-content">
            <?php foreach ($tabs as $tab) { ?>
			<div class="tab-pane active" id="tab-api">
			  <?php foreach ($fields as $field) { ?>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-merchant-id"><?php echo ((!empty($field['required']) ) ? '<span class="required">*</span>' : '') . $field['entry']; ?></label>
                <div class="col-sm-10">
                  <!--<input type="text" name="globalpay_merchant_id" value="<?php echo $globalpay_merchant_id; ?>" placeholder="<?php echo $entry_merchant_id; ?>" id="input-merchant-id" class="form-control" />
                  <?php if ($error_merchant_id) { ?>
                  <div class="text-danger"><?php echo $error_merchant_id; ?></div>
                  <?php } ?>
				  -->
					<?php if ($field['type'] == 'select') { ?>
					<select name="<?php echo $field['name']; ?>" <?php echo (isset($field['multiple']) && $field['multiple']) ? 'multiple="multiple"' : ''?> <?php echo (isset($field['size']) && $field['size']) ? 'size="' . $field['size'] . '"' : ''?>>
					<?php foreach ($field['options'] as $key => $value) : ?>
						<option value="<?php echo $key; ?>"<?php if((is_array($field['value']) && in_array($key, $field['value'])) || ($field['value'] == $key)) echo ' selected="selected"'?>><?php echo $value; ?></option>
					<?php endforeach; ?>
					</select>
					<?php } elseif ($field['type'] == 'radio') {?>
					<?php foreach($field['options'] as $key => $value) : ?>
					<input type="radio" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" value="<?php echo $key; ?>"<?php if($field['value'] == $key) echo ' checked="checked"'; ?> /><label for="<?php echo $field['name']; ?>"><?php echo $value; ?></label>
					<?php endforeach; ?>
					<?php } elseif ($field['type'] == 'text') {?>
					<input type="text" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" <?php echo (isset($field['size']) && $field['size']) ? 'size="' . $field['size'] . '"' : ''?>/>
					<?php } elseif ($field['type'] == 'password') {?>
					<input type="password" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" <?php echo (isset($field['size']) && $field['size']) ? 'size="' . $field['size'] . '"' : ''?>/>
					<?php } elseif ($field['type'] == 'checkbox') {?>
					<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" value="1"<?php if($field['value']) echo 'checked="checked"'; ?> />
					<?php } elseif ($field['type'] == 'file') {?>
					<input type="file" name="<?php echo $field['name']; ?>" value="" <?php echo (isset($field['size']) && $field['size']) ? 'size="' . $field['size'] . '"' : ''?> />
					<?php } elseif ($field['type'] == 'textarea') {?>
					<textarea name="<?php echo $field['name']; ?>" cols="<?php echo $field['cols']; ?>" rows="<?php echo $field['rows']; ?>"><?php echo $field['value']; ?></textarea>
					<?php } elseif ($field['type'] == 'hidden') {?>
					<input type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />
					<?php } elseif ($field['type'] == 'label') {?>
					<label id="<?php echo $field['name']; ?>"><?php echo $field['value']; ?></label>
					<?php } ?>
					<?php if (!empty($field['help'])) { ?>
					<span class="help"><?php echo $field['help']; ?></span><br />
					<?php } ?>
					<?php if (!empty($field['error'])) { ?>
					<span class="error"><?php echo $field['error']; ?></span>
					<?php } ?>
                </div>
              </div>
			  <?php } ?>
            </div>
			<?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>