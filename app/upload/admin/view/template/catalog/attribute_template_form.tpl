<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-tax-class" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form-tax-class">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
            <div class="col-sm-10">
              <input type="text" name="title" value="<?php echo $title; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title" class="form-control" />
              <?php if ($error_title) { ?>
              <div class="text-danger"><?php echo $error_title; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <input type="text" name="description" value="<?php echo $description; ?>" placeholder="<?php echo $entry_description; ?>" id="input-description" class="form-control" />
              <?php if ($error_description) { ?>
              <div class="text-danger"><?php echo $error_description; ?></div>
              <?php } ?>
            </div>
          </div>
          <table id="template-attribute" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_rate; ?></td>
                <td class="text-left"><?php echo $entry_based; ?></td>
                <td class="text-left"><?php echo $entry_priority; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $attribute_row = 0; ?>
              <?php foreach ($template_attributes as $template_attribute) { ?>
              <tr id="template-attribute-row<?php echo $attribute_row; ?>">
                <td class="text-left"><select name="template_attribute[<?php echo $attribute_row; ?>][attribute_group_id]" class="form-control">
                    <?php foreach ($groups as $group) { ?>
                    <?php  if ($group['attribute_group_id'] == $template_attribute['attribute_group_id']) { ?>
                    <option value="<?php echo $group['attribute_group_id']; ?>" selected="selected"><?php echo $group['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $group['attribute_group_id']; ?>"><?php echo $group['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
                <td class="text-left">
                  <select name="template_attribute[<?php echo $attribute_row; ?>][attribute_id]" class="form-control">
                    <?php foreach ($attributes as $attribute) { ?>
                    <?php if ((int)$attribute['attribute_id'] == (int)$template_attribute['attribute_id']) { ?>
                    <option value="<?php echo $attribute['attribute_id']; ?>" selected="selected"><?php echo $attribute['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $attribute['attribute_id']; ?>"><?php echo $attribute['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
                <td class="text-left"><input type="text" name="template_attribute[<?php echo $attribute_row; ?>][value]" value="<?php echo base64_decode($template_attribute['value']); ?>" placeholder="<?php echo $entry_value; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#template-attribute-row<?php echo $attribute_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $attribute_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="text-left"><button type="button" onclick="addAttribute();" data-toggle="tooltip" title="<?php echo $button_rule_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
var attribute_row = <?php echo $attribute_row; ?>;

function addAttribute() {
	html  = '<tr id="template-attribute-row' + attribute_row + '">';
	html += '  <td class="text-left"><select name="template_attribute[' + attribute_row + '][attribute_group_id]" class="form-control">';
    <?php foreach ($groups as $group) { ?>
    html += '    <option value="<?php echo $group['attribute_group_id']; ?>"><?php echo addslashes($group['name']); ?></option>';
    <?php } ?>
    html += '  </select></td>';
	html += '  <td class="text-left"><select name="template_attribute[' + attribute_row + '][attribute_id]" class="form-control">';
    <?php foreach ($attributes as $attribute) { ?>
    html += '    <option value="<?php echo $attribute['attribute_id']; ?>"><?php echo addslashes($attribute['name']); ?></option>';
    <?php } ?>
    html += '  </select></td>';
	html += '  <td class="text-left"><input type="text" name="template_attribute[' + attribute_row + '][value]" value="" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#template-attribute-row' + attribute_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	
	$('#template-attribute tbody').append(html);
	
	attribute_row++;
}
//--></script></div>
<?php echo $footer; ?>