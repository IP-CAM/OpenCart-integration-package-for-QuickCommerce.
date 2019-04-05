<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<div style="position: absolute; right: 270px; top: 45px; width: 0;">
		<ipp:blueDot></ipp:blueDot>
		</div>
		<?php if ($connected == true) { ?>
		<button id="button-qbo-disconnect" class="btn btn-danger" tooltip="If you do this, you'll have to go back through the authorization/connection process to get connected again"><i class="fa fa-power-off"></i> Disconnect</button>
		<?php } else { ?>
		<button id="button-qbo-connect" class="btn btn-success" tooltip="QuickBooks connections expire after 6 months, so you have to this roughly every 5 and 1/2 months"><i class="fa fa-plug"></i> Connect<ipp:connectToIntuit></ipp:connectToIntuit></button>	
		<?php } ?>
		<button type="submit" form="form-qc-admin" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-qc-admin" class="form-horizontal">
          <!--<div class="form-group">
			<div class="col-sm-2">
				<button id="button-qbo-disconnect" class="btn btn-danger"><i class="fa fa-power-off"></i> Disconnect</button>
			</div>
			<label class="col-sm-10 control-label" for="">If you do this, you'll have to go back through the authorization/connection process to get connected again</label>
		  </div>
		  <div class="form-group">
			<div class="col-sm-2">
				<button id="button-qbo-connect" class="btn btn-success"><i class="fa fa-refresh"></i> Connect/Refresh</button>
			</div>
			<label class="col-sm-10 control-label" for="">QuickBooks connections expire after 6 months, so you have to this roughly every 5 and 1/2 months</label>
		  </div>-->
		  <div class="form-group">
			<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
			<div class="col-sm-10">
			  <select name="qc_status" id="input-status" class="form-control">
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
		  <div class="form-group">
			<label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_mode; ?></label>
			<div class="col-sm-10">
			  <select name="qc_mode" id="input-mode" class="form-control">
				<?php if ($mode) { ?>
				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				<option value="0"><?php echo $text_disabled; ?></option>
				<?php } else { ?>
				<option value="1"><?php echo $text_enabled; ?></option>
				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				<?php } ?>
			  </select>
			</div>
		  </div>
		  <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-connection" data-toggle="tab"><?php echo $tab_connection; ?></a></li>
            <li><a href="#tab-settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
            <li><a href="#tab-sync" data-toggle="tab">Synchronize</a></li>
            <li><a href="#tab-test" data-toggle="tab"><?php echo $tab_test; ?></a></li>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane active" id="tab-connection">
			  <fieldset>
				  <legend>Service Configuration</legend>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-entry-dsn"><?php echo $entry_dsn; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="input-entry-dsn" name="qc_dsn" value="<?php echo $dsn; ?>" />
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-entry-enc-key"><?php echo $entry_enc_key; ?></label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="input-entry-enc-key" name="qc_enc_key" value="<?php echo $enc_key; ?>" />
					</div>
				  </div>
				  <ul class="nav nav-tabs">
					<li class="active"><a href="#tab-development-keys" data-toggle="tab">Development</a></li>
					<li><a href="#tab-production-keys" data-toggle="tab">Production</a></li>
				  </ul>
				  <div class="tab-content">
					<div class="tab-pane active" id="tab-development-keys">
					  <div style="display: flex; flex-flow: row wrap">
						<div style="flex: 1 1 48%">
						  <h4>Development Keys</h4>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-token"><?php echo $entry_token; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-token" name="qc_dev_ipp_token" value="<?php echo $dev_ipp_token; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-key"><?php echo $entry_key; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-key" name="qc_dev_ipp_key" value="<?php echo $dev_ipp_key; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-secret" name="qc_dev_ipp_secret" value="<?php echo $dev_ipp_secret; ?>" />
							</div>
						  </div>
						</div>
						<?php /*<div style="flex: 1 1 48%">
						  <h4>Development URLs</h4>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-oauth-url"><?php echo $entry_oauth_url; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-oauth-url" name="qc_oauth_url" value="<?php echo $oauth_url; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-success-url"><?php echo $entry_success_url; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-success-url" name="qc_success_url" value="<?php echo $success_url; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-menu-url"><?php echo $entry_menu_url; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-menu-url" name="qc_menu_url" value="<?php echo $menu_url; ?>" />
							</div>
						  </div>
						</div>
						*/ ?>
					  </div>
					</div>
					<div class="tab-pane" id="tab-production-keys">
					  <div style="display: flex; flex-flow: row wrap">
						<div style="flex: 1 1 48%">
						  <h4>Production Keys</h4>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-token"><?php echo $entry_token; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-token" name="qc_prod_ipp_token" value="<?php echo $prod_ipp_token; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-key"><?php echo $entry_key; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-key" name="qc_prod_ipp_key" value="<?php echo $prod_ipp_key; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-secret" name="qc_prod_ipp_secret" value="<?php echo $prod_ipp_secret; ?>" />
							</div>
						  </div>
						</div>
						<?php /*<div style="flex: 1 1 48%">
						  <h4>Production URLs</h4>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-oauth-url"><?php echo $entry_oauth_url; ?></label>
							<div class="col-sm-10">
							  <input type="text" class="form-control" id="input-entry-oauth-url" name="qc_oauth_url" value="<?php echo $oauth_url; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-success-url"><?php echo $entry_success_url; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-success-url" name="qc_success_url" value="<?php echo $success_url; ?>" />
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-entry-menu-url"><?php echo $entry_menu_url; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="input-entry-menu-url" name="qc_menu_url" value="<?php echo $menu_url; ?>" />
							</div>
						  </div>
						</div>
						*/ ?>
					  </div>
					</div>
				  </div>
			  </fieldset>
			</div>
			<div class="tab-pane" id="tab-settings">
			  <div class="row">
			    <div class="col-sm-10 col-sm-push-2">
				<p>Income and Cost of Goods Sold (expense) accounts must be supplied when pushing inventory products to QuickBooks Online. </p>
				</div>
			  </div>
			  <div class="row">
			    <div class="col-sm-12">
					<div class="form-group">
					<label class="col-sm-2 control-label" for="input-mode">Default Income Account</label>
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
						<label class="col-sm-2 control-label" for="input-mode">Default COGS Account</label>
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
						<label class="col-sm-2 control-label" for="input-mode">Default Asset Account</label>
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
					<hr>
					<div class="form-group">
						<label class="col-sm-2 control-label">Default Product</label>
						<div class="col-sm-10">
							<span class="input-group">
								<input type="text" value="" id="input-default-product" class="form-control" />
								<span class="input-group-btn">
									<button class="btn btn-info button-product-unlinked" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-search"></i> Search</button>
									<button class="btn btn-success button-product-linked" style="display: none" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-check"></i> Selected</button>
									<!--<button class="btn btn-danger button-clear-product" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button" data-original-title="Remove"><i class="fa fa-trash-o"></i></button>-->
								</span>
							</span>
							<input type="hidden" name="qc_default_product" value="" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Default Service</label>
						<div class="col-sm-10">
							<span class="input-group">
								<input type="text" value="" id="input-default-service" class="form-control" />
								<span class="input-group-btn">
									<button class="btn btn-info button-product-unlinked" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-search"></i> Search</button>
									<button class="btn btn-success button-product-linked" style="display: none" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-check"></i> Selected</button>
									<!--<button class="btn btn-danger button-clear-product" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button" data-original-title="Remove"><i class="fa fa-trash-o"></i></button>-->
								</span>
							</span>
							<input type="hidden" name="qc_default_service" value="" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Default Shipping</label>
						<div class="col-sm-10">
							<span class="input-group">
								<input type="text" value="" id="input-default-shipping" class="form-control" />
								<span class="input-group-btn">
									<button class="btn btn-info button-product-unlinked" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-search"></i> Search</button>
									<button class="btn btn-success button-product-linked" style="display: none" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-check"></i> Selected</button>
									<!--<button class="btn btn-danger button-clear-product" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button" data-original-title="Remove"><i class="fa fa-trash-o"></i></button>-->
								</span>
							</span>
							<input type="hidden" name="qc_default_shipping" value="" />
						</div>
					</div>
			    </div>
			  </div>
			</div>
			<div class="tab-pane" id="tab-sync">
			  <div class="row">
				<div class="col-sm-6">
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Inventory Products</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Inventory Categories</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Tax Classes</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Customers</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Invoices</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-6 control-label" for="">Receipts</label>
					<div class="col-sm-6">
						<button class="btn btn-success"><i class="fa fa-cloud-upload"></i> Push</button>
						<button class="btn btn-info"><i class="fa fa-cloud-download"></i> Pull</button>
					</div>
				  </div>
				</div>
				<div class="col-sm-6">
					<iframe style="width: 100%; height: 100%; background: black; border: 1px solid grey"></iframe>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab-test">
			  <div class="row">
				<div class="col-sm-6">
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create a customer</label>
					<div class="col-sm-5">
						<button class="btn btn-default" ><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Edit a customer</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create a product (item)</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Edit a product (item)</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create a vendor</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Edit a vendor</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create a payment</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Edit a payment</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create an invoice</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Edit an invoice</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-7 control-label" for="">Create a purchase order</label>
					<div class="col-sm-5">
						<button class="btn btn-default"><i class="fa fa-plug"></i> Test</button>
					</div>
				  </div>
				</div>
				<div class="col-sm-6">
					<iframe style="width: 100%; height: 100%; background: black; border: 1px solid grey"></iframe>
				</div>
			  </div>
			</div>
		  </div>
        </form>
		<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="qbo-connect-modal">
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
						<h3 class="panel-title"><i class="fa fa-pencil"></i> Connect to QuickBooks</h3>
						<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
					</div>
					<div class="panel-body">
					<form class="form-horizontal">
						<!-- This form should only be displayed if a non-hosted payment solution was selected -->
						<!-- TODO: Payments should be a module -->
						<fieldset>
							<legend class="text-center"><?php echo $text_credit_card; ?></legend>
							<div class="alert alert-danger" id="cc-form-error" style="display:none;"></div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-cc-type"><?php echo $entry_cc_type; ?></label>
								<div class="col-sm-6">
									<select name="cc_type" id="input-cc-type" class="form-control">
										<option value="" selected="selected"><?php echo $text_select_card; ?></option>
										<?php foreach ($cards as $card) { ?>
											<option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
								<div class="col-sm-6">
									<input type="text" name="cc_number" id="input-cc-number" value="" placeholder="<?php echo $entry_cc_number; ?>" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="input-cc-start-date"><span data-toggle="tooltip" title="<?php echo $help_start_date; ?>"><?php echo $entry_cc_start_date; ?></span></label>
								<div class="col-sm-3">
									<select name="cc_start_date_month" id="input-cc-start-date" class="form-control">
										<option value="" selected="selected"></option>
										<?php foreach ($months as $month) { ?>
											<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-3">
									<select name="cc_start_date_year" class="form-control">
										<option value="" selected="selected"></option>
										<?php foreach ($year_valid as $year) { ?>
											<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
								<div class="col-sm-3">
									<select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
										<option value="" selected="selected"></option>
										<?php foreach ($months as $month) { ?>
											<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-3">
									<select name="cc_expire_date_year" class="form-control">
										<option value="" selected="selected"></option>
										<?php foreach ($year_expire as $year) { ?>
											<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
								<div class="col-sm-6">
									<input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="input-cc-issue"><span data-toggle="tooltip" title="<?php echo $help_issue; ?>"><?php echo $entry_cc_issue; ?></span></label>
								<div class="col-sm-6">
									<input type="text" name="cc_issue" value="" placeholder="<?php echo $entry_cc_issue; ?>" id="input-cc-issue" class="form-control" />
								</div>
							</div>
							<div class="row">
								<div class="col-sm-5 text-right">
									<button type="button" id="button-cc-form-close" class="btn btn-primary"><?php echo $button_close_cc; ?></button>
								</div>
								<div class="col-sm-5 text-center">
									<button type="button" id="button-cc-form-submit" class="btn btn-primary"><?php echo $button_process_cc; ?></button>
								</div>
							</div>
						</fieldset>
					</form>
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
		
		var productSearch = $('#input-default-product').autocomplete({
			'source': function(request, response) {
				$.ajax({
					url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
					dataType: 'json',			
					success: function(json) {
						response($.map(json, function(item) {
							return {
								label: item['name'],
								value: item['product_id'],
								model: item['model'],
								option: item['option'],
								price: item['price']						
							}
						}));
					}
				});
			},
			'select': function(item) {
				$('#input-default-product').val(item['label']);
				$('input[name=\'qc_default_product\']').val(item['value']);	
				
				$('.button-product-linked').show();
				$('.button-product-unlinked').hide();
			}	
		});
		
		var serviceSearch = $('#input-default-service').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
					dataType: 'json',			
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['name'],
								value: item['product_id'],
								model: item['model'],
								option: item['option'],
								price: item['price']						
							}
						}));
					}
				});
			},
			'select': function (item) {
				$('#input-default-service').val(item['label']);
				$('input[name=\'qc_default_service\']').val(item['value']);	
				
				$('.button-product-linked').show();
				$('.button-product-unlinked').hide();
			}	
		});
		
		var shippingSearch = $('#input-default-shipping').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
					dataType: 'json',			
					success: function(json) {
						response($.map(json, function(item) {
							return {
								label: item['name'],
								value: item['product_id'],
								model: item['model'],
								option: item['option'],
								price: item['price']						
							}
						}));
					}
				});
			},
			'select': function (item) {
				$('#input-default-shipping').val(item['label']);
				$('input[name=\'qc_default_shipping\']').val(item['value']);	
				
				$('.button-product-linked').show();
				$('.button-product-unlinked').hide();
			}	
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
</style>
