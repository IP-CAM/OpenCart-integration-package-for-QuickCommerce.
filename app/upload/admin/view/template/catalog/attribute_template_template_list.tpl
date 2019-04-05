<!--<div class="row">
  <div class="col-sm-12" style="margin-bottom: 15px;">
    <button id="qc-attribute-load-selected" data-token="" data-toggle="tooltip" title="Import Selected" class="btn btn-warning pull-right"><i class="fa fa-adjust"></i> Load Selected</button>
  </div>
</div>-->
<div class="row">
  <div class="col-sm-12">
    <form id="qc-load-attributes-form">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
              <td class="text-left"><?php if (isset($sort) && $sort == 'title') { ?>
                <a href="<?php echo $sort_title; ?>" class="<?php echo strtolower($order); ?>"><?php echo 'Attribute Template'; //$column_title; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_title; ?>"><?php echo 'Attribute Template'; //$column_title; ?></a>
                <?php } ?></td>
              <?php /*
              <td class="text-right"><?php echo $column_action; ?></td>
              */ ?>
            </tr>
          </thead>
          <tbody>
            <?php if ($attribute_templates) { ?>
            <?php foreach ($attribute_templates as $template) { ?>
            <tr>
              <td class="text-center"><?php if (isset($selected) && in_array($template['attribute_template_id'], $selected)) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $template['attribute_template_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $template['attribute_template_id']; ?>" />
                <?php } ?></td>
              <td class="text-left"><?php echo $template['title']; ?></td>
              <?php /*
              <td class="text-right"><a href="<?php echo '#'; //$template['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
              */ ?>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="text-center" colspan="3"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 text-right">
    <button class="btn btn-warning qc-load-attribute-template" data-loading-text="Loading..." type="button">Load Selected</button>&nbsp;
    <button id="button-close-load-attributes" class="btn btn-default" data-action="close" data-loading-text="Loading..." type="button">Close</button>
  </div>
</div>
<div style="clear: both"></div>