<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<button id="qc-peer-import" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import from Peer" class="btn btn-warning"><i class="fa fa-list-alt"></i> Import from Peer</button> <button id="qc-qbo-import" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import from QuickBooks" class="btn btn-success"><i class="fa fa-cloud-download"></i> Import from QBO</button> <button id="qc-qbo-export" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Export to QuickBooks" class="btn btn-info"><i class="fa fa-cloud-upload"></i> Export to QBO</button> <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			
        <button type="submit" form="form-product" formaction="<?php echo $copy; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default"><i class="fa fa-copy"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
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

        <?php if (is_array($error_warning)) {
            if (isset($error_warning['message'])) {
                $msg = '<b>' . $error_warning['error'] . '</b> ' . $error_warning['message'] . '.<br>';
                $msg .= '<ul style="list-style-type: none; padding-left: 12px"><li><b>' . $error_warning['code'] . '</b>: ' . $error_warning['detail'] . '</li></ul>';
                
                $error_warning = $msg;
                unset($msg);
            } else {
                $error_warning = $error_warning['error'];
            }
        } ?>
            
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        
        <?php if (isset($product_filters)) {
            echo $product_filters;
        } ?>
        <div class="well" style="display: none">
            
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
                <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-price"><?php echo $entry_price; ?></label>
                <input type="text" name="filter_price" value="<?php echo $filter_price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                <input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-image"><?php echo $entry_image; ?></label>
                <select name="filter_image" id="input-image" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_image) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_image && !is_null($filter_image)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>

		<div class="well">
          <div class="row">
            <div class="col-sm-9"></div>
            <div class="col-sm-3 pull-right">
			  <div class="form-group">
                <label class="control-label" for="input-batch-action"><?php echo 'Batch Actions'; ?></label>
                <div class="input-group">
				<select name="batch_action" id="batch-action" class="form-control" style="font-family: 'FontAwesome', Arial" data-token="<?php echo $token; ?>">
                  <option value="sync" selected="selected"><!--<i class="fa fa-refresh"></i>-->&#xf021; <?php echo '&nbsp;&nbsp;Sync With QuickBooks'; ?></option>
                  <option value="delete"><!--<i class="fa fa-trash"></i>-->&#xf1f8; <?php echo '&nbsp;&nbsp;Delete From QuickBooks'; ?></option>
                  <option value="assign_accounts"><!--<i class="fa fa-external-link-square"></i>-->&#xf14c; <?php echo '&nbsp;&nbsp;Assign Accounts'; ?></option>
                  <option value="generate_seo_urls"><!--<i class="fa fa-underline"></i>-->&#xf0cd; <?php echo '&nbsp;&nbsp;Generate SEO URLs'; ?></option>
                </select>
				<span class="input-group-btn">
				  <button type="button" id="button-batch-action" class="btn btn-success pull-right"><i class="fa fa-list"></i> <?php echo 'Batch'; ?></button>
				</span>
				</div>
              </div>
			</div>
		  </div>
		</div>
			
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-center"><?php echo $column_image; ?></td>
                  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'p.model') { ?>
                    <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'p.price') { ?>
                    <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'p.quantity') { ?>
                    <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'p.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>

        <td class="text-center"><?php echo 'Sync'; ?></td>
            
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $product['name']; ?></td>
                  <td class="text-left"><?php echo $product['model']; ?></td>
                  <td class="text-right"><?php if ($product['special']) { ?>
                    <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
                    <div class="text-danger"><?php echo $product['special']; ?></div>
                    <?php } else { ?>
                    <?php echo $product['price']; ?>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($product['quantity'] <= 0) { ?>
                    <span class="label label-warning"><?php echo $product['quantity']; ?></span>
                    <?php } elseif ($product['quantity'] <= 5) { ?>
                    <span class="label label-danger"><?php echo $product['quantity']; ?></span>
                    <?php } else { ?>
                    <span class="label label-success"><?php echo $product['quantity']; ?></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $product['status']; ?></td>

        <td class="text-center">
            <span data-id="<?php echo $product['product_id']; ?>" class="label label-default"><i class="fa fa-question"></i></span>
        </td>
            
                  <td class="text-right">
				<a href="#" data-id="<?php echo $product['product_id']; ?>" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Sync with QuickBooks" class="btn btn-default"><i class="fa fa-refresh"></i></a> <a href="<?php echo $product['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
			</td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}

	var filter_price = $('input[name=\'filter_price\']').val();

	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}

	var filter_quantity = $('input[name=\'filter_quantity\']').val();

	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

  var filter_image = $('select[name=\'filter_image\']').val();

  if (filter_image != '*') {
    url += '&filter_image=' + encodeURIComponent(filter_image);
  }

	location = url;
});
//--></script>
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
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
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('input[name=\'filter_model\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['model'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_model\']').val(item['label']);
	}
});
//--></script></div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="batch-assign-accounts-modal" data-token="<?php echo $token; ?>">
		<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!--<div class="modal-header">
			  <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
			  <h4 id="mySmallModalLabel" class="modal-title">Edit Address</h4>
			</div>-->
			<style scoped>
			.modal-body {
				padding: 0;
			}
			</style>
			<div class="modal-body">
			  <div class="panel panel-default">
				<div class="panel-heading">	
					<h3 class="panel-title"><i class="fa fa-list-alt"></i> Assign Accounts to Products</h3>
					<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="well">
								<form class="form-horizontal" id="form-assign-product-account">
									<div class="row">
										<fieldset>
											<div class="col-sm-12">
												<div class="form-group">
												<label class="col-sm-2 control-label" for="input-mode">Income Account</label>
												<div class="col-sm-10">
													<select name="qc_income_account" id="input-mode" class="form-control">
													<?php if (isset($accounts)) { ?>
													<?php foreach ($accounts as $account) { ?>
													<?php
													$account_name = $account['name'];
													$selected = ($income_account == (int)$account['account_id']) ? 'selected="selected"' : '';
													if (isset($account['account_num']) && !empty($account['account_num'])) {
														$account_name = $account['account_num'] . ' - ' . $account_name;
													}
													?>
													<option value="<?php echo $account['account_id']; ?>" <?php echo $selected; ?>><?php echo $account_name; ?></option>
													<?php } ?>
													<?php } ?>
													</select>
												</div>
												</div>
												<div class="form-group">
													<label class="col-sm-2 control-label" for="input-mode">COGS Account</label>
													<div class="col-sm-10">
														<select name="qc_cogs_account" id="input-mode" class="form-control">
														<?php if (isset($accounts)) { ?>
														<?php foreach ($accounts as $account) { ?>
														<?php
														$account_name = $account['name'];
														$selected = ($cogs_account == (int)$account['account_id']) ? 'selected="selected"' : '';
														if (isset($account['account_num']) && !empty($account['account_num'])) {
															$account_name = $account['account_num'] . ' - ' . $account_name;
														}
														?>
														<option value="<?php echo $account['account_id']; ?>" <?php echo $selected; ?>><?php echo $account_name; ?></option>
														<?php } ?>
														<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-2 control-label" for="input-mode">Asset Account</label>
													<div class="col-sm-10">
														<select name="qc_asset_account" id="input-asset-account" class="form-control">
														<?php if (isset($accounts)) { ?>
														<?php foreach ($accounts as $account) { ?>
														<?php
														$account_name = $account['name'];
														$selected = ($asset_account == (int)$account['account_id']) ? 'selected="selected"' : '';
														if (isset($account['account_num']) && !empty($account['account_num'])) {
															$account_name = $account['account_num'] . ' - ' . $account_name;
														}
														?>
														<option value="<?php echo $account['account_id']; ?>" <?php echo $selected; ?>><?php echo $account_name; ?></option>
														<?php } ?>
														<?php } ?>
														</select>
													</div>
												</div>
											</div>
										</fieldset>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 text-right">
							<button class="btn btn-primary button-payment-address-apply" data-loading-text="Loading..." type="button">Apply</button>
							<button id="button-payment-address-cancel" class="btn btn-default" data-action="close" data-loading-text="Loading..." type="button">Close</button>
						</div>
					</div>
					<div style="clear: both"></div>
				</div>
			  </div>
			</div>
		</div>
		</div>
	</div>
			

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="p2p-import-modal" data-token="<?php echo $token; ?>">
		<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<!--<div class="modal-header">
			  <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
			  <h4 id="mySmallModalLabel" class="modal-title">Edit Address</h4>
			</div>-->
			<style scoped>
			.modal-body {
				padding: 0;
			}
			</style>
			<div class="modal-body">
			  <div class="panel panel-default">
				<div class="panel-heading">	
					<h3 class="panel-title"><i class="fa fa-list-alt"></i> Import Products</h3>
					<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="well">
								<form class="form-horizontal" id="form-product-p2p-filter">
									<div class="row">
									<fieldset>
										<div class="col-sm-12">
											<div class="form-group">
												<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
												<div class="col-sm-10">
													<select multiple class="form-control">
														<option selected="selected">QuickCommerce Parent Site</option>
														<option disabled="disabled">Demo Feed 1</option>
														<option disabled="disabled">Demo Feed 2</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-8 pull-right">
												<!-- EDIT -->
												<button id="qc-peer-shares" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="View Products" class="btn btn-info pull-right"><i class="fa fa-list-alt"></i> View Products</button>
												<!-- END -->
												</div>
											</div>
										</div>
									</fieldset>
									</div>
									<div class="row">
									<fieldset>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-name"><?php echo $entry_name; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-model"><?php echo $entry_model; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-match"> </label>
												<div class="col-sm-8">
													<input type="checkbox" name="filter_match" value="1" id="input-match" class="form-control" style="display: inline-block; margin-right: 1em" checked="checked" /><b><?php echo 'Show matching products only'; ?></b>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-price"><?php echo $entry_price; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_price" value="<?php echo $filter_price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo 'Categories'; ?></span></label>
												<div class="col-sm-8">
												  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" data-token="<?php echo $token; ?>" />
												  <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
													<?php foreach ($product_categories as $product_category) { ?>
													<div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
													  <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
													</div>
													<?php } ?>
												  </div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-8 pull-right">
												<!-- EDIT -->
												<button type="button" id="button-p2p-import-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
												<!-- END -->
												</div>
											</div>
										</div>
									</fieldset>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="row">
						<fieldset>
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12"><legend>Import Options</legend></div>
								</div>
							</div>
							<div class="col-sm-2" for="">	
								<div class="form-group">
									<label class="col-sm-8 control-label" for="import-images">Images</label>
									<div class="col-sm-2">
										<input type="checkbox" name="images" value="true" id="import-images" class="form-control" checked="checked" />
									</div>
								</div>
							</div>
							<div class="col-sm-2" for="">	
								<div class="form-group">
									<label class="col-sm-8 control-label" for="import-categories">Categories</label>
									<div class="col-sm-2">
										<input type="checkbox" name="categories" value="true" id="import-categories" class="form-control" checked="checked" />
									</div>
								</div>
							</div>
							<div class="col-sm-2" for="">	
								<div class="form-group">
									<label class="col-sm-8 control-label" for="import-attributes">Attributes</label>
									<div class="col-sm-2">
										<input type="checkbox" name="attributes" value="true" id="import-attributes" class="form-control" />
									</div>
								</div>
							</div>
							<div class="col-sm-2" for="">	
								<div class="form-group">
									<label class="col-sm-8 control-label" for="import-options">Options</label>
									<div class="col-sm-2">
										<input type="checkbox" name="options" value="true" id="import-options" class="form-control" />
									</div>
								</div>
							</div>
							<div class="col-sm-4" for="">	
								<div class="form-group">
									<div class="col-sm-6">
										<button id="qc-peer-import-selected" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import Selected" class="btn btn-warning"><i class="fa fa-adjust"></i> Import Selected</button>
									</div>
									<div class="col-sm-6">
										<button id="qc-peer-import-all" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import Selected" class="btn btn-success"><i class="fa fa-circle-o"></i> Import All</button>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="row" style="margin-top: 20px;">
						<div class="col-sm-12">
							<div class="panel panel-default">
							  <!--<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
							  </div>-->
							  <div class="panel-body">
								<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product-p2p-import">
								  <div class="table-responsive">
									<table class="table table-bordered table-hover">
									  <thead>
										<tr>
										  <td class="text-center"><?php echo $column_image; ?></td>
										  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
											<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
											<?php } ?></td>
										  <td class="text-left"><?php if ($sort == 'p.model') { ?>
											<a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
											<?php } ?></td>
										  <td class="text-right"><?php if ($sort == 'p.price') { ?>
											<a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
											<?php } ?></td>
										  <td class="text-right"><?php if ($sort == 'p.quantity') { ?>
											<a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
											<?php } ?></td>
										  <td class="text-left"><?php if ($sort == 'p.status') { ?>
											<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
											<?php } ?></td>
										  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
										</tr>
									  </thead>
									  <tbody>
										<tr>
										  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
										</tr>
									  </tbody>
									</table>
								  </div>
								</form>
								<div class="row">
								</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
			  </div>
			</div>
		</div>
		</div>
	</div>
	<style scoped>
		.modal-xl {
			width: 1248px;
		}
	</style>
			

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="seo-rename-modal" data-token="<?php echo $token; ?>">
		<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<!--<div class="modal-header">
			  <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
			  <h4 id="mySmallModalLabel" class="modal-title">Edit Address</h4>
			</div>-->
			<style scoped>
			.modal-body {
				padding: 0;
			}
			</style>
			<div class="modal-body">
			  <div class="panel panel-default">
				<div class="panel-heading">	
					<h3 class="panel-title"><i class="fa fa-list-alt"></i> Batch Generate SEO URLs for Products</h3>
					<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="well">
								<form class="form-horizontal" id="form-seo-rename-product-filter">
									<div class="row">
									<fieldset>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-name"><?php echo $entry_name; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
												</div>
											</div>
											<hr>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-field"><?php echo 'Field'; //$filter_quantity; ?></label>
												<div class="col-sm-8">
													<small>Choose a field and its corresponding tag will be appended to the expression editor</small>
													<br>
													<br>
													<select multiple="multiple" name="input_field" value="<?php echo 'Field'; //$filter_quantity; ?>" style="height: 120px; overflow: auto;" placeholder="<?php echo 'Field'; //$filter_quantity; ?>" id="input-field" class="form-control">
														<option>Product ID {product_id}</option>
														<option>Name {name}</option>
														<option>QuickBooks Name {qb_name}</option>
														<option>Manufacturer {manufacturer}</option>
														<option>SKU {sku}</option>
														<option>MPN {mpn}</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<?php /*
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-price"><?php echo $entry_price; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_price" value="<?php echo $filter_price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
												</div>
											</div>
											*/ ?>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-model"><?php echo 'QBO Name'; //$entry_model; ?></label>
												<div class="col-sm-8">
													<input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
												</div>
											</div>
											<hr>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-expr"><?php echo 'Expr'; //$entry_expr; ?>
												</label>
												<div class="col-sm-8">
													<small>1. {product_id}-my-url.htm</small>
													<br>
													<small>2. Regular Expr. (preg_replace)</small>
													<br>
													<br>
													<textarea rows="6" name="expr" placeholder="<?php echo 'Expr'; //$entry_expr; ?>" id="input-expr" class="form-control">{product_id}-{manufacturer:{convert-spaces: true,convert-special:true}}-{model{convert-spaces: true,convert-special:true}}</textarea>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo 'Categories'; ?></span></label>
												<div class="col-sm-8">
												  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" data-token="<?php echo $token; ?>" />
												  <div id="product-category" class="well well-sm" style="height: 100px; overflow: auto;">
													<?php foreach ($product_categories as $product_category) { ?>
													<div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
													  <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
													</div>
													<?php } ?>
												  </div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-8 pull-right">
												<!-- EDIT -->
												<button type="button" id="button-seo-rename-filter" class="btn btn-info pull-right"><i class="fa fa-eye"></i> <?php echo 'Preview'; //$button_filter; ?></button>
												<!-- END -->
												</div>
											</div>
										</div>
									</fieldset>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="row">
						<fieldset>
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12"><legend>Preview</legend></div>
								</div>
							</div>
							<div class="col-sm-4 col-sm-push-8" for="">	
								<div class="form-group">
									<div class="col-sm-6">
										<button id="qc-seo-rename-selected" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Generate for Selected" class="btn btn-warning"><i class="fa fa-adjust"></i> Generate for Selected</button>
									</div>
									<div class="col-sm-6">
										<button id="qc-seo-rename-all" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Generate for All" class="btn btn-success"><i class="fa fa-circle-o"></i> Generate for All</button>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="row" style="margin-top: 20px;">
						<div class="col-sm-12">
							<div class="panel panel-default">
							  <!--<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
							  </div>-->
							  <div class="panel-body">
								<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-seo-rename">
								  <div class="table-responsive">
									<table class="table table-bordered table-hover">
									  <thead>
										<tr>
										  <td class="text-center"><?php echo $column_image; ?></td>
										  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
											<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
											<?php } ?></td>
										  <td class="text-left"><?php if ($sort == 'p.model') { ?>
											<a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
											<?php } ?></td>
										  <td class="text-right"><?php if ($sort == 'p.price') { ?>
											<a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
											<?php } ?></td>
										  <td class="text-right"><?php if ($sort == 'p.quantity') { ?>
											<a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
											<?php } ?></td>
										  <td class="text-left"><?php if ($sort == 'p.status') { ?>
											<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
											<?php } else { ?>
											<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
											<?php } ?></td>
										  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
										</tr>
									  </thead>
									  <tbody>
										<tr>
										  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
										</tr>
									  </tbody>
									</table>
								  </div>
								</form>
								<div class="row">
								</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
			  </div>
			</div>
		</div>
		</div>
	</div>
	<style scoped>
		.modal-xl {
			width: 1248px;
		}
	</style>
			
<?php echo $footer; ?>