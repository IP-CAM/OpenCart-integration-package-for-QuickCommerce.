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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
		  <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <?php /*<li><a href="#tab-license" data-toggle="tab">License</a></li>
            <li><a href="#tab-about" data-toggle="tab">About</a></li>*/ ?>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-add-stock"><?php echo $entry_add_stock; ?></label>
				<div class="col-sm-10">
				  <select name="purchase_order_add_stock" id="input-add-stock" class="form-control">
					<option value="1"<?php echo $purchase_order_add_stock ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $purchase_order_add_stock ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-receive-email"><?php echo $entry_receive_email; ?></label>
				<div class="col-sm-10">
				  <select name="purchase_order_receive_email" id="input-receive-email" class="form-control">
					<option value="1"<?php echo $purchase_order_receive_email ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $purchase_order_receive_email ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-insert-email"><?php echo $entry_insert_email; ?></label>
				<div class="col-sm-10">
				  <select name="purchase_order_insert_email" id="input-insert-email" class="form-control">
					<option value="1"<?php echo $purchase_order_insert_email ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $purchase_order_insert_email ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-update-email"><?php echo $entry_update_email; ?></label>
				<div class="col-sm-10">
				  <select name="purchase_order_update_email" id="input-update-email" class="form-control">
					<option value="1"<?php echo $purchase_order_update_email ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $purchase_order_update_email ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
                <label class="col-sm-2 control-label" for="input-price"><span title="<?php echo $help_price; ?>" data-toggle="tooltip"><?php echo $entry_price; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="purchase_order_price" value="<?php echo $purchase_order_price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-placing" class="form-control" />
                </div>
              </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-hide"><?php echo $entry_hide; ?></label>
				<div class="col-sm-10">
				  <select name="purchase_order_hide" id="input-hide" class="form-control">
					<option value="1"<?php echo $purchase_order_hide ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $purchase_order_hide ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			</div>
			<?php require_once(DIR_TEMPLATE . 'module/purchase_order_about.tpl'); ?>
		  </div>
		</form>
      </div>
    </div>
	<?php /*<div style="color:#222222;text-align:center;"><?php echo $heading_title; ?> v<?php echo $version; ?> by <a href="http://www.marketinsg.com" target="_blank">MarketInSG</a></div>*/ ?>
  </div>
</div>
<?php echo $footer; ?>