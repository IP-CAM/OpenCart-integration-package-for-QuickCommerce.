<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-html" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-html" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>         
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-route"><?php echo $entry_route; ?></label>
            <div class="col-sm-10">
              <input type="text" name="route" value="<?php echo $route; ?>" placeholder="<?php echo $entry_route; ?>" id="input-route" class="form-control" />
              <?php if ($error_route) { ?>
              <div class="text-danger"><?php echo $error_route; ?></div>
              <?php } ?>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-path"><?php echo $entry_path; ?></label>
            <div class="col-sm-10">
              <input type="text" name="path" value="<?php echo $path; ?>" placeholder="<?php echo $entry_path; ?>" id="input-path" class="form-control" />
              <?php if ($error_path) { ?>
              <div class="text-danger"><?php echo $error_path; ?></div>
              <?php } ?>
            </div>
          </div>
		  <div class="form-group">
		    <label class="col-sm-2 control-label" for="input-bootstrap"><?php echo $entry_bootstrap; ?></label>
		    <div class="col-sm-10">
			  <textarea style="height: 300px" name="bootstrap" placeholder="<?php echo $entry_bootstrap; ?>" id="input-bootstrap" class="form-control summernote"><?php echo isset($bootstrap) ? $bootstrap : ''; ?></textarea>
			  <?php if ($error_bootstrap) { ?>
              <div class="text-danger"><?php echo $error_bootstrap; ?></div>
              <?php } ?>
		    </div>
		  </div>
		  <div class="form-group">
		    <label class="col-sm-2 control-label" for="input-markup"><?php echo $entry_markup; ?></label>
		    <div class="col-sm-10">
			  <textarea style="height: 300px" name="markup" placeholder="<?php echo $entry_markup; ?>" id="input-markup" class="form-control summernote"><?php echo isset($markup) ? $markup : ''; ?></textarea>
			  <?php if ($error_markup) { ?>
              <div class="text-danger"><?php echo $error_markup; ?></div>
              <?php } ?>
		    </div>
		  </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ace.min.js" type="text/javascript" charset="utf-8"></script>
  <script>
    //var bootstrapEditor = ace.edit("input-bootstrap");
	//bootstrapEditor.setOptions({
	//	maxLines: 30
	//});
	
    //bootstrapEditor.setTheme("ace/theme/monokai");
    //bootstrapEditor.getSession().setMode("ace/mode/javascript");
	
	//var markupEditor = ace.edit("input-markup");
    //markupEditor.setTheme("ace/theme/monokai");
    //markupEditor.getSession().setMode("ace/mode/javascript");
	//markupEditor.setOptions({
	//	maxLines: 30
	//});
  </script>
</div>
<?php echo $footer; ?>