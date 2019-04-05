<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $purchase_order; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_purchase_order_print; ?>" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
				<a href="<?php echo $shipping; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i> Ship</a>
				<button id="button-save" type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
				<a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a>
			</div>

			<?php if (isset($action)) { ?>
			<?php if ($action == 'add') { ?>
			<h1><?php echo $heading_title; ?></h1>
			<?php } elseif ($action == 'edit') { ?>
			<h1><?php echo $heading_title_edit; ?></h1>
			<?php } ?>
			<?php } ?>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal">
					<div class="col-sm-12" data-target="vendor">
						<div class="col-sm-4">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-order-status">Reference No<?php /*echo $entry_order_status;*/ ?></label>
									<div class="col-sm-8">
										<input type="text" name="order_id" class="form-control" value="<?php echo $order_id; ?>" disabled="disabled" />
									</div>
								</div>
							</fieldset>
						</div>
						<div class="col-sm-4">

						</div>
						<div class="col-sm-4">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-currency"><?php echo $entry_currency; ?></label>
									<div class="col-sm-8">
										<select name="currency" id="input-currency" class="form-control">
											<?php foreach ($currencies as $currency) { ?>
											<?php if ($currency['code'] == $currency_code) { ?>
											<option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
											<?php } else { ?>
											<option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
											<?php } ?>
											<?php } ?>
										</select>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="col-sm-12"><hr></div>
					<div class="col-sm-12" data-target="vendor">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="col-sm-4 control-label" for="input-vendor"><?php echo $entry_vendor; ?></label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" name="vendor" value="<?php echo $vendor; ?>" placeholder="<?php echo $entry_vendor; ?>" id="input-vendor" class="form-control" />
										<input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>" />
								<span class="input-group-btn">
								<button type="button" id="button-vendor" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
								</span>
									</div>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
								<div class="col-sm-8">
									<input type="text" name="firstname" value="<?php echo $firstname; ?>" id="input-firstname" class="form-control" />
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
								<div class="col-sm-8">
									<input type="text" name="lastname" value="<?php echo $lastname; ?>" id="input-lastname" class="form-control" />
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-email"><?php echo $entry_email; ?></label>
								<div class="col-sm-8">
									<input type="text" name="email" value="<?php echo $bill_email; ?>" id="input-email" class="form-control" />
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
								<div class="col-sm-8">
									<input type="text" name="telephone" value="<?php echo $bill_telephone; ?>" id="input-telephone" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="input-fax"><?php echo $entry_fax; ?></label>
								<div class="col-sm-8">
									<input type="text" name="fax" value="<?php echo $bill_fax; ?>" id="input-fax" class="form-control" />
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-store"><?php echo $entry_store; ?></label>
									<div class="col-sm-8">
										<select name="store_id" id="input-store" class="form-control">
											<option value="0"><?php echo $text_default; ?></option>
											<?php foreach ($stores as $store) { ?>
											<?php if ($store['store_id'] == $store_id) { ?>
											<option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
											<?php } else { ?>
											<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
											<?php } ?>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="form-group required">
									<label class="col-sm-4 control-label" for="input-purchase-order-date"><?php echo 'Purchase Order Date'; ?></label>
									<div class="col-sm-8">
										<div class="input-group date">
											<input type="text" name="purchase_order_date" value="<?php echo $purchase_order_date; ?>" placeholder="<?php echo 'Purchase Order Date'; ?>" data-date-format="YYYY-MM-DD" id="input-purchase-order-date" class="form-control" />
									<span class="input-group-btn">
									<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span></div>
									</div>
								</div>

								<div class="form-group required">
									<label class="col-sm-4 control-label" for="input-date-received"><?php echo 'Date Received'; ?></label>
									<div class="col-sm-8">
										<div class="input-group date">
											<input type="text" name="date_received" value="<?php echo $due_date; ?>" placeholder="<?php echo 'Date Received'; ?>" data-date-format="YYYY-MM-DD" id="input-date-received" class="form-control" />
									<span class="input-group-btn">
									<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span></div>
									</div>
								</div>

								<div class="form-group required">
									<label class="col-sm-4 control-label" for="input-date-estimated"><?php echo 'Estimated Arrival'; ?></label>
									<div class="col-sm-8">
										<div class="input-group date">
											<input type="text" name="date_estimated" value="<?php echo $date_estimated; ?>" placeholder="<?php echo 'Estimated Arrival'; ?>" data-date-format="YYYY-MM-DD" id="input-date-estimated" class="form-control" />
									<span class="input-group-btn">
									<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span></div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-purchase-order-status">Status<?php /*echo $entry_order_status;*/ ?></label>
									<div class="col-sm-8">
										<select name="purchase_order_status_id" id="input-purchase-order-status" class="form-control">
											<?php foreach ($purchase_order_statuses as $purchase_order_status) { ?>
											<?php if ($purchase_order_status['purchase_order_status_id'] == $purchase_order_status_id) { ?>
											<option value="<?php echo $purchase_order_status['purchase_order_status_id']; ?>" selected="selected"><?php echo $purchase_order_status['name']; ?></option>
											<?php } else { ?>
											<option value="<?php echo $purchase_order_status['purchase_order_status_id']; ?>"><?php echo $purchase_order_status['name']; ?></option>
											<?php } ?>
											<?php } ?>
										</select>
										<input type="hidden" name="purchase_order_id" value="<?php echo $purchase_order_id; ?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-purchase-order-status">Received<?php /*echo $entry_order_status;*/ ?></label>
									<div class="col-sm-8">
										<select name="purchase_order_status_id" id="input-purchase-order-status" class="form-control">
											<option value="yes">Yes</option>
											<option value="no">Pending</option>
										</select>
									</div>
								</div>

								<!--<div class="form-group">
                <label class="col-sm-4 control-label" for="input-order-status">Order Status<?php /*echo $entry_order_status;*/ ?></label>
                <div class="col-sm-8">
                <select name="order_status_id" id="input-order-status" class="form-control" readonly="readonly">
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                </div>
                  </div>-->
							</fieldset>

						</div>
						<!-- Billing and Shipping Column -->
						<div class="col-sm-4">
							<div class="form-group required">
								<label class="col-sm-4 control-label" for="input-payment-address"><?php echo $entry_payment_address; ?></label>
								<div class="col-sm-8">
									<textarea name="payment_address" class="form-control" rows="4" disabled="disabled"><?php echo $payment_address; ?></textarea>
								</div>
							</div>
							<?php $text_none = ' --- Change address --- '; ?>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-8">
									<div class="input-group">
										<select name="payment_address" id="input-payment-address" class="form-control">
											<option value="0" selected="selected"><?php echo $text_none; ?></option>
											<?php foreach ($addresses as $address) { ?>
											<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
											<?php } ?>
										</select>
									<span class="input-group-btn">
									<button class="btn btn-primary button-payment-address-apply" data-loading-text="Loading..." type="button">Apply</button>
									<button id="button-payment-address" class="btn btn-primary" data-loading-text="Loading..." type="button">Edit</button>
									</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="input-shipping-address"><?php echo $entry_shipping_address; ?></label>
								<div class="col-sm-8">
									<textarea name="shipping_address" class="form-control" rows="4" disabled="disabled"><?php echo $shipping_address; ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-8">
									<div class="input-group">
										<select name="shipping_address" id="input-shipping-address" class="form-control">
											<option value="0" selected="selected"><?php echo $text_none; ?></option>
											<?php foreach ($addresses as $address) { ?>
											<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
											<?php } ?>
										</select>
									<span class="input-group-btn">
									<button class="btn btn-primary button-shipping-address-apply" data-loading-text="Loading..." type="button">Apply</button>
									<button id="button-shipping-address" class="btn btn-primary" data-loading-text="Loading..." type="button">Edit</button>
									</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<!-- Line Entry -->
						<div class="row" style="margin-top: 40px">
							<div class="col-sm-12 col-lg-4">
								<div id="tab-line">
									<fieldset>
										<legend style="margin: 35px 0; text-align: center;"><?php echo 'Add Line Item'; ?></legend>
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-line"><?php echo 'Product/Service'; ?></label>
											<div class="col-sm-8">
										<span class="input-group">
											<input type="text" name="product" value="" id="input-line" class="form-control" />
											<span class="input-group-btn">
												<button class="btn btn-info button-product-unlinked" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-search"></i> Search</button>
												<button class="btn btn-success button-product-linked" style="display: none" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button"><i class="fa fa-check"></i> Selected</button>
												<!--<button class="btn btn-danger button-clear-product" data-loading-text="Loading..." title="" data-toggle="tooltip" value="null" type="button" data-original-title="Remove"><i class="fa fa-trash-o"></i></button>-->
											</span>
										</span>
												<input type="hidden" name="product_id" value="" />
											</div>
										</div>
										<hr style="display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0; border-style: dotted">
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-description"><?php echo 'Description'; ?></label>
											<div class="col-sm-8">
												<textarea name="description" id="input-description" class="form-control"></textarea>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
											<div class="col-sm-8">
												<input type="text" name="quantity" id="input-quantity" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-price"><?php echo $entry_price; ?></label>
											<div class="col-sm-8">
												<input type="text" name="price" id="input-price" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-price"><!--<?php echo 'Taxable'; ?>--></label>
											<div class="col-sm-8">
												<!--<select name="order_status_id" id="input-order-status" class="form-control">
													<?php foreach ($order_statuses as $order_status) { ?>
													<?php if ($order_status['order_status_id'] == $order_status_id) { ?>
													<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
													<?php } else { ?>
													<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
													<?php } ?>
													<?php } ?>
												</select>-->
												<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
												<input type="checkbox" name="taxable" value="1" id="input-taxable" class="form-control" checked="checked" style="display: inline-block" /> <small>Taxable?</small>
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-4 control-label" for="input-tax-class"><?php echo 'Tax Class'; ?></label>
											<div class="col-sm-8">
												<select name="tax_class_id" id="input-tax-class" class="form-control">
													<option value=""><?php echo $text_select; ?></option>
													<?php if ($tax_classes) { ?>
													<?php foreach ($tax_classes as $tax_class) { ?>
													<option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
													<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										<div id="option"></div>
									</fieldset>
									<div class="text-right">
										<button type="button" class="button-line-add btn btn-primary" data-loading-text="<?php echo $text_loading; ?>"><i class="fa fa-plus-circle"></i> <?php echo 'Add Line Item'; ?></button>
										<button type="button" class="button-line-clear btn btn-info" data-loading-text="<?php echo $text_loading; ?>"><i class="fa fa-trash"></i> <?php echo 'Clear Form'; ?></button>
									</div>
								</div>
								<br />
							</div>
							<div class="col-sm-12 col-lg-8">
								<div class="row">
									<div class="col-xs-12" style="margin-bottom: 2rem">
										<div class="text-right">
											<button id="button-populate" type="button" data-toggle="tooltip" title="" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i><span> Populate Products</button>&nbsp;
											<button id="button-batch-delete" type="button" data-toggle="tooltip" title="" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i><span> Delete Lines</button>
										</div>
									</div>
								</div>
								<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
										<tr>
											<td><input type="checkbox" /></td>
											<td colspan="2" class="text-left"><?php echo 'Item Detail'; ?></td>
											<td class="text-right"><?php echo $column_quantity; ?></td>
											<!--<td class="text-right"><?php echo $column_revenue; ?></td>-->
											<td class="text-right"><?php echo $column_price; ?></td>
											<!--<td class="text-right"><?php echo $column_royalty; ?></td>-->
											<td class="text-right"><?php echo 'Tax Class'; //$column_tax_class; ?></td>
											<!--<td class="text-right"><?php echo $column_tax; ?></td>-->
											<td class="text-right"><?php echo $column_total; ?></td>

											<td></td>
										</tr>
										</thead>
										<tbody id="cart"></tbody>
										<tfoot id="total"></tfoot>
									</table>
								</div>
							</div>
							<div class="col-sm-12">
								<!-- Coupons & Discounts / Payment & Shipping / Memos -->
								<div class="row clearfix clear" style="margin-top: 20px;">
									<div class="col-sm-4">
									</div>

									<div class="col-sm-4">
										<fieldset>
											<div class="form-group required">
												<label class="col-sm-4 control-label" for="input-payment-method"><?php echo $entry_payment_method; ?></label>
												<div class="col-sm-8">
													<div class="input-group">
														<select name="payment_method" id="input-payment-method" class="form-control">
															<option value=""><?php echo $text_select; ?></option>
															<?php if ($payment_code) { ?>
															<option value="<?php echo $payment_code; ?>" selected="selected"><?php echo $payment_method; ?></option>
															<?php } ?>
														</select>
												<span class="input-group-btn">
												<button type="button" id="button-payment-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
												</span></div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="input-shipping-method"><?php echo $entry_shipping_method; ?></label>
												<div class="col-sm-8">
													<div class="input-group">
														<select name="shipping_method" id="input-shipping-method" class="form-control">
															<option value=""><?php echo $text_select; ?></option>
															<?php if ($shipping_code) { ?>
															<option value="<?php echo $shipping_code; ?>" selected="selected"><?php echo $shipping_method; ?></option>
															<?php } ?>
														</select>
											<span class="input-group-btn">
											<button type="button" id="button-shipping-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
											</span></div>
												</div>
											</div>
										</fieldset>
									</div>

									<div class="col-sm-4">
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-vendor-memo"><?php echo 'Supplier Memo'; //$entry_comment; ?></label>
											<div class="col-sm-8">
												<?php $vendor_memo = (!empty($vendor_memo)) ? $vendor_memo : "ALL SALES ARE FINAL. RETURNS AND/OR EXCHANGES ARE SUBJECT TO OUR POLICY ONLINE AT caffetech.com/returns AND WILL BE ISSUED AT CAFFE TECH'S DISCRETION. RETURNED EQUIPMENT MAY BE SUBJECT TO A 20% RESTOCKING FEE IF REMOVED FROM THE ORIGINAL PACKAGING."; ?>
												<textarea name="vendor_memo" rows="5" id="input-vendor-memo" class="form-control"><?php echo $vendor_memo; ?></textarea>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label" for="input-statement-memo"><?php echo 'Statement Memo'; //$entry_comment; ?></label>
											<div class="col-sm-8"><!-- TODO: Yes, this is butchery, but just getting this change in here -->
												<textarea name="statement_memo" rows="5" id="input-statement-memo" class="form-control"><?php echo $statement_memo; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
				</form>
			</div>
		</div>
		<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="shipping-address-modal">
			<div class="modal-dialog modal-md">
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
								<h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Shipping Address</h3>
								<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
							</div>
							<div class="panel-body">
								<div class="well">
									<form class="form-horizontal">
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-firstname"><?php echo $entry_firstname; ?></label>
											<div class="col-sm-9">
												<input type="text" name="firstname" value="<?php echo $shipping_firstname; ?>" id="input-shipping-firstname" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-lastname"><?php echo $entry_lastname; ?></label>
											<div class="col-sm-9">
												<input type="text" name="lastname" value="<?php echo $shipping_lastname; ?>" id="input-shipping-lastname" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="input-shipping-company"><?php echo $entry_company; ?></label>
											<div class="col-sm-9">
												<input type="text" name="company" value="<?php echo $shipping_company; ?>" id="input-shipping-company" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-address-1"><?php echo $entry_address_1; ?></label>
											<div class="col-sm-9">
												<input type="text" name="address_1" value="<?php echo $shipping_address_1; ?>" id="input-shipping-address-1" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="input-shipping-address-2"><?php echo $entry_address_2; ?></label>
											<div class="col-sm-9">
												<input type="text" name="address_2" value="<?php echo $shipping_address_2; ?>" id="input-shipping-address-2" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-city"><?php echo $entry_city; ?></label>
											<div class="col-sm-9">
												<input type="text" name="city" value="<?php echo $shipping_city; ?>" id="input-shipping-city" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-postcode"><?php echo $entry_postcode; ?></label>
											<div class="col-sm-9">
												<input type="text" name="postcode" value="<?php echo $shipping_postcode; ?>" id="input-shipping-postcode" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-country"><?php echo $entry_country; ?></label>
											<div class="col-sm-9">
												<select name="country_id" id="input-shipping-country" class="form-control">
													<option value=""><?php echo $text_select; ?></option>
													<?php foreach ($countries as $country) { ?>
													<?php if ($country['country_id'] == $shipping_country_id) { ?>
													<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
													<?php } else { ?>
													<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
													<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-shipping-zone"><?php echo $entry_zone; ?></label>
											<div class="col-sm-9">
												<select name="zone_id" id="input-shipping-zone" class="form-control">
												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 text-right">
												<button class="btn btn-primary button-shipping-address-apply" data-loading-text="Loading..." type="button">Apply</button>
												<button id="button-shipping-address-cancel" class="btn btn-default" data-action="close" data-loading-text="Loading..." type="button">Close</button>
											</div>
										</div>
										<div style="clear: both"></div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="payment-address-modal">
			<div class="modal-dialog modal-md">
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
								<h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Payment Address</h3>
								<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
							</div>
							<div class="panel-body">
								<div class="well">
									<form class="form-horizontal">
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-firstname"><?php echo $entry_firstname; ?></label>
											<div class="col-sm-9">
												<input type="text" name="firstname" value="<?php echo $payment_firstname; ?>" id="input-payment-firstname" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-lastname"><?php echo $entry_lastname; ?></label>
											<div class="col-sm-9">
												<input type="text" name="lastname" value="<?php echo $payment_lastname; ?>" id="input-payment-lastname" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="input-payment-company"><?php echo $entry_company; ?></label>
											<div class="col-sm-9">
												<input type="text" name="company" value="<?php echo $payment_company; ?>" id="input-payment-company" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-address-1"><?php echo $entry_address_1; ?></label>
											<div class="col-sm-9">
												<input type="text" name="address_1" value="<?php echo $payment_address_1; ?>" id="input-payment-address-1" class="form-control" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="input-payment-address-2"><?php echo $entry_address_2; ?></label>
											<div class="col-sm-9">
												<input type="text" name="address_2" value="<?php echo $payment_address_2; ?>" id="input-payment-address-2" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-city"><?php echo $entry_city; ?></label>
											<div class="col-sm-9">
												<input type="text" name="city" value="<?php echo $payment_city; ?>" id="input-payment-city" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-postcode"><?php echo $entry_postcode; ?></label>
											<div class="col-sm-9">
												<input type="text" name="postcode" value="<?php echo $payment_postcode; ?>" id="input-payment-postcode" class="form-control" />
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-country"><?php echo $entry_country; ?></label>
											<div class="col-sm-9">
												<select name="country_id" id="input-payment-country" class="form-control">
													<option value=""><?php echo $text_select; ?></option>
													<?php foreach ($countries as $country) { ?>
													<?php if ($country['country_id'] == $payment_country_id) { ?>
													<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
													<?php } else { ?>
													<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
													<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group required">
											<label class="col-sm-3 control-label" for="input-payment-zone"><?php echo $entry_zone; ?></label>
											<div class="col-sm-9">
												<select name="zone_id" id="input-payment-zone" class="form-control">
												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 text-right">
												<button class="btn btn-primary button-payment-address-apply" data-loading-text="Loading..." type="button">Apply</button>
												<button id="button-payment-address-cancel" class="btn btn-default" data-action="close" data-loading-text="Loading..." type="button">Close</button>
											</div>
										</div>
										<div style="clear: both"></div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="payment-modal">
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
								<h3 class="panel-title"><i class="fa fa-pencil"></i> Process Payment</h3>
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
		<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="shipping-modal">
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
								<h3 class="panel-title"><i class="fa fa-pencil"></i> Create Shipment</h3>
								<button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
							</div>
							<div class="panel-body"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript"><!--
			(function () {
				FormHelper = function () {
					this.init()
				};

				FormHelper.prototype.init = function() {
					//console.log('test');
				};

				FormHelper.createString = function() {
					//console.log('test string');
				}

				FormHelper.displayError = function(container, message) {
					container.prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + message + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

			})();

			// Disable the tabs
			$('#order a[data-toggle=\'tab\']').on('click', function (e) {
				return false;
			});

			$('#tab-order').find('.nav-tabs > li').on('click', function (e) {
				//console.log('click');
				$('[name=description], [name=product_id], [name=product]').val(''); // Clear fields
			});

			function saveCustomer() {
				$.ajax({
					url: 'index.php?route=api/vendor&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('[data-target=\'vendor\'] input[type=\'text\'], [data-target=\'vendor\'] input[type=\'hidden\'], [data-target=\'vendor\'] input[type=\'radio\']:checked, [data-target=\'vendor\'] input[type=\'checkbox\']:checked, [data-target=\'vendor\'] select, [data-target=\'vendor\'] textarea'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-vendor').button('loading');
					},
					complete: function() {
						$('#button-vendor').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							if (json['error']['warning']) {
								FormHelper.displayError(c, json['error']['warning']);
							}

							for (i in json['error']) {
								var element = $('#input-' + i.replace('_', '-'));

								if (element.parent().hasClass('input-group')) {
									$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
								} else {
									$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
								}
							}

							// Highlight any found errors
							$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
						} else {
							$.ajax({
								url: 'index.php?route=api/lines/add&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
								type: 'post',
								data: $('#cart input[name^=\'product\'][type=\'text\'], #cart input[name^=\'product\'][type=\'hidden\'], #cart input[name^=\'product\'][type=\'radio\']:checked, #cart input[name^=\'product\'][type=\'checkbox\']:checked, #cart select[name^=\'product\'], #cart textarea[name^=\'product\']'),
								dataType: 'json',
								beforeSend: function() {
									$('#button-line-add').button('loading');
								},
								complete: function() {
									$('#button-line-add').button('reset');
								},
								success: function(json) {
									$('.alert, .text-danger').remove();
									$('.form-group').removeClass('has-error');

									var c = $('#content > .container-fluid');
									if (json['error'] && json['error']['warning']) {
										FormHelper.displayError(c, json['error']['warning']);
									}
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});

							/*$.ajax({
							 url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
							 type: 'post',
							 data: $('#cart input[name^=\'voucher\'][type=\'text\'], #cart input[name^=\'voucher\'][type=\'hidden\'], #cart input[name^=\'voucher\'][type=\'radio\']:checked, #cart input[name^=\'voucher\'][type=\'checkbox\']:checked, #cart select[name^=\'voucher\'], #cart textarea[name^=\'voucher\']'),
							 dataType: 'json',
							 beforeSend: function() {
							 $('#button-voucher-add').button('loading');
							 },
							 complete: function() {
							 $('#button-voucher-add').button('reset');
							 },
							 success: function(json) {
							 $('.alert, .text-danger').remove();
							 $('.form-group').removeClass('has-error');

							 var c = $('#content > .container-fluid');
							 if (json['error'] && json['error']['warning']) {
							 FormHelper.displayError(c, json['error']['warning']);
							 }
							 },
							 error: function(xhr, ajaxOptions, thrownError) {
							 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							 }
							 });	*/

							// Refresh products, vouchers and totals
							refresh();

							// QC MOD
							// TODO: If...
							//$('a[href=\'#tab-cart\']').tab('show');
							$('a[href=\'#tab-store\']').tab('show');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}

			// TODO: Split into two methods -- totals logic will have to be called every time
			function refresh() {
				$.ajax({
					url: 'index.php?route=api/lines/lines&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					dataType: 'json',
					success: function(json) {
						$('.alert-danger, .text-danger').remove();

						// Check for errors
						var c = $('#content > .container-fluid');
						if (json['error']) {
							if (json['error']['warning']) {
								FormHelper.displayError(c, json['error']['warning']);
							}

							if (json['error']['stock']) {
								FormHelper.displayError(c, json['error']['stock'] + '</div>');
							}

							if (json['error']['minimum']) {
								for (i in json['error']['minimum']) {
									FormHelper.displayError(c, json['error']['minimum'][i]);
								}
							}
						}

						var shipping = false;

						html = '';

						/*<td class="text-left"><?php echo $line['name']; ?><br />
						 <input type="hidden" name="product[<?php echo $product_row; ?>][product_id]" value="<?php echo $line['product_id']; ?>" />
						 <?php foreach ($line['option'] as $option) { ?>
						 - <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
						 <?php if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') { ?>
						 <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['product_option_value_id']; ?>" />
						 <?php } ?>
						 <?php if ($option['type'] == 'checkbox') { ?>
						 <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option['product_option_value_id']; ?>" />
						 <?php } ?>
						 <?php if ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') { ?>
						 <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" />
						 <?php } ?>
						 <?php } ?></td>
						 <td class="text-left"><?php echo $line['model']; ?></td>
						 <td class="text-right">
						 <input class="form-control" type="number" name="product[<?php echo $product_row; ?>][quantity]" value="<?php echo $line['quantity']; ?>" /></td>
						 <td class="text-right">
						 <input class="form-control" type="number" name="product[<?php echo $product_row; ?>][revenue]" value="<?php echo $line['revenue']; ?>" /></td>
						 <td class="text-right">
						 <input class="form-control" type="number" name="product[<?php echo $product_row; ?>][price]" value="<?php echo $line['price']; ?>" /></td>
						 <td class="text-right">
						 <input class="form-control" type="number" name="product[<?php echo $product_row; ?>][royalty]" value="<?php echo $line['royalty']; ?>" /></td>
						 <td class="text-right">
						 <input class="form-control" type="number" name="product[<?php echo $product_row; ?>][total]" value="<?php echo $line['total']; ?>" /></td>
						 <td class="text-center" style="width: 3px;"><button type="button" value="<?php echo $line['key']; ?>" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>*/

						if (json['lines']) {
							for (i = 0; i < json['lines'].length; i++) {
								line = json['lines'][i];

								html += '<tr>';
								html += '  <td><input type="checkbox" name="key" value="' + line['key'] + '" /></td>';
								html += '  <td class="text-left">' + line['name'] + ' ' + (!line['stock'] ? '<span class="text-danger">***</span>' : '') + '<br />';
								html += '  <input type="hidden" name="line[' + i + '][product_id]" value="' + line['product_id'] + '" />';

								if (line['option']) {
									for (j = 0; j < line['option'].length; j++) {
										option = line['option'][j];

										html += '  - <small>' + option['name'] + ': ' + option['value'] + '</small><br />';

										if (option['type'] == 'select' || option['type'] == 'radio' || option['type'] == 'image') {
											html += '<input type="hidden" name="line[' + i + '][option][' + option['product_option_id'] + ']" value="' + option['product_option_value_id'] + '" />';
										}

										if (option['type'] == 'checkbox') {
											html += '<input type="hidden" name="line[' + i + '][option][' + option['product_option_id'] + '][]" value="' + option['product_option_value_id'] + '" />';
										}

										if (option['type'] == 'text' || option['type'] == 'textarea' || option['type'] == 'file' || option['type'] == 'date' || option['type'] == 'datetime' || option['type'] == 'time') {
											html += '<input type="hidden" name="line[' + i + '][option][' + option['product_option_id'] + ']" value="' + option['value'] + '" />';
										}
									}
								}

								html += '</td>';
								html += '  <td class="text-left">' + line['model'] + '</td>';
								html += '  <td class="text-right">' + line['quantity'] + '<input type="hidden" name="line[' + i + '][quantity]" value="' + line['quantity'] + '" /></td>';
								//html += '  <td class="text-right">' + line['revenue'] + '<input type="hidden" name="line[' + i + '][revenue]" value="' + line['revenue'] + '" /></td>';
								html += '  <td class="text-right">' + line['price'] + '</td>';
								//html += '  <td class="text-right">' + line['royalty'] + '</td>';
								html += '  <td class="text-right">' + line['tax_class'] + '</td>';
								html += '  <td class="text-right">' + line['total'] + '</td>';
								html += '  <td class="text-center" style="width: 3px;"><button type="button" value="' + line['key'] + '" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
								html += '</tr>';

								if (line['shipping'] != 0) {
									shipping = true;
								}
							}
						}

						if (!shipping) {
							$('select[name=\'shipping_method\'] option').removeAttr('selected');
							$('select[name=\'shipping_method\']').prop('disabled', true);
							$('#button-shipping-method').prop('disabled', true);
						} else {
							$('select[name=\'shipping_method\']').prop('disabled', false);
							$('#button-shipping-method').prop('disabled', false);
						}

						if (json['vouchers']) {
							for (i in json['vouchers']) {
								voucher = json['vouchers'][i];

								html += '<tr>';
								html += '  <td class="text-left">' + voucher['description'];
								html += '    <input type="hidden" name="voucher[' + i + '][code]" value="' + voucher['code'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][description]" value="' + voucher['description'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][from_name]" value="' + voucher['from_name'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][from_email]" value="' + voucher['from_email'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][to_name]" value="' + voucher['to_name'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][to_email]" value="' + voucher['to_email'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][voucher_theme_id]" value="' + voucher['voucher_theme_id'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][message]" value="' + voucher['message'] + '" />';
								html += '    <input type="hidden" name="voucher[' + i + '][amount]" value="' + voucher['amount'] + '" />';
								html += '  </td>';
								html += '  <td class="text-left"></td>';
								html += '  <td class="text-right">1</td>';
								html += '  <td class="text-right">' + voucher['amount'] + '</td>';
								html += '  <td class="text-right">' + voucher['amount'] + '</td>';
								html += '  <td class="text-center" style="width: 3px;"><button type="button" value="' + voucher['code'] + '" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
								html += '</tr>';
							}
						}

						if (json['lines'] == '' && json['vouchers'] == '') {
							html += '<tr>';
							html += '  <td colspan="9" class="text-center"><div style="min-height: 220px;"><span style="display: block; padding-top: 110px;"><?php echo $text_no_results; ?></span></div></td>';
							html += '</tr>';
						}

						$('#cart').html(html);

						// Totals
						html = '';

						/*if (json['products']) {
						 for (i = 0; i < json['products'].length; i++) {
						 product = json['products'][i];

						 html += '<tr>';
						 html += '  <td class="text-left">' + product['name'] + ' ' + (!product['stock'] ? '<span class="text-danger">***</span>' : '') + '<br />';

						 if (product['option']) {
						 for (j = 0; j < product['option'].length; j++) {
						 option = product['option'][j];

						 html += '  - <small>' + option['name'] + ': ' + option['value'] + '</small><br />';
						 }
						 }

						 html += '  </td>';
						 html += '  <td class="text-left">' + product['model'] + '</td>';
						 html += '  <td class="text-right">' + product['quantity'] + '</td>';
						 html += '  <td class="text-right">' + product['price'] + '</td>';
						 html += '  <td class="text-right">' + product['total'] + '</td>';
						 html += '</tr>';
						 }
						 }

						 if (json['vouchers']) {
						 for (i in json['vouchers']) {
						 voucher = json['vouchers'][i];

						 html += '<tr>';
						 html += '  <td class="text-left">' + voucher['description'] + '</td>';
						 html += '  <td class="text-left"></td>';
						 html += '  <td class="text-right">1</td>';
						 html += '  <td class="text-right">' + voucher['amount'] + '</td>';
						 html += '  <td class="text-right">' + voucher['amount'] + '</td>';
						 html += '</tr>';
						 }
						 }*/

						if (json['totals']) {
							for (i in json['totals']) {
								total = json['totals'][i];

								html += '<tr>';
								html += '  <td class="text-right" colspan="6">' + total['title'] + ':</td>';
								html += '  <td class="text-right">' + total['text'] + '</td>';
								html += '</tr>';
							}
						}

						if (!json['totals'] && !json['products'] && !json['vouchers']) {
							html += '<tr>';
							html += '  <td colspan="6" class="text-center"><?php echo $text_no_results; ?></td>';
							html += '</tr>';
						}

						$('#total').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}

			// Add all products to the cart using the api
			$('#button-refresh').on('click', function() {
				refresh();
			});

			// Currency
			$('select[name=\'currency\']').on('change', function() {
				$.ajax({
					url: 'index.php?route=api/currency&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: 'currency=' + $('select[name=\'currency\'] option:selected').val(),
					dataType: 'json',
					beforeSend: function() {
						$('select[name=\'currency\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
					},
					complete: function() {
						$('.fa-spin').remove();
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							FormHelper.displayError(c, json['error']);

							// Highlight any found errors
							$('select[name=\'currency\']').parent().parent().parent().addClass('has-error');
						}

						refresh();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('select[name=\'currency\']').trigger('change');

			// Customer
			$('input[name=\'vendor\']').autocomplete({
				'source': function(request, response) {
					$.ajax({
						url: 'index.php?route=vendor/vendor/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
						dataType: 'json',
						success: function(json) {
							json.unshift({
								vendor_id: '0',
								name: '<?php echo $text_none; ?>',
								firstname: '',
								lastname: '',
								email: '',
								telephone: '',
								fax: '',
								address: []
							});

							response($.map(json, function(item) {
								return {
									label: (typeof item['company_name'] === 'string' && item['company_name'] !== '') ? item['display_name'] + ' (' + item['company_name'] + ')' : item['display_name'],
									value: item['vendor_id'],
									firstname: item['firstname'],
									lastname: item['lastname'],
									email: item['email'],
									telephone: item['telephone'],
									fax: item['fax'],
									address: item['address']
								}
							}));
						}
					});
				},
				'select': function(item) {
					// Reset all custom fields
					$('[data-target=\'vendor\'] input[type=\'text\'], [data-target=\'vendor\'] input[type=\'text\'], [data-target=\'vendor\'] textarea').not('[data-target=\'vendor\'] input[name=\'vendor\'], [data-target=\'vendor\'] input[name=\'vendor_id\']').val('');
					$('[data-target=\'vendor\'] select option').removeAttr('selected');
					$('[data-target=\'vendor\'] input[type=\'checkbox\'], [data-target=\'vendor\'] input[type=\'radio\']').removeAttr('checked');

					$('[data-target=\'vendor\'] input[name=\'vendor\']').val(item['label']);
					$('[data-target=\'vendor\'] input[name=\'vendor_id\']').val(item['value']);
					$('[data-target=\'vendor\'] input[name=\'firstname\']').val(item['firstname']);
					$('[data-target=\'vendor\'] input[name=\'lastname\']').val(item['lastname']);
					$('[data-target=\'vendor\'] input[name=\'email\']').val(item['email']);
					$('[data-target=\'vendor\'] input[name=\'telephone\']').val(item['telephone']);
					$('[data-target=\'vendor\'] input[name=\'fax\']').val(item['fax']);

					html = '<option value="0"><?php echo $text_none; ?></option>';

					for (i in  item['address']) {
						html += '<option value="' + item['address'][i]['address_id'] + '">' + item['address'][i]['firstname'] + ' ' + item['address'][i]['lastname'] + ', ' + item['address'][i]['address_1'] + ', ' + item['address'][i]['city'] + ', ' + item['address'][i]['country'] + '</option>';
					}

					$('select[name=\'payment_address\']').html(html);
					$('select[name=\'shipping_address\']').html(html);

					$('select[name=\'payment_address\']').trigger('change');
					$('select[name=\'shipping_address\']').trigger('change');
				}
			});

			var entryTabs = $('#line-entry-tabs')
			var commissionTab = entryTabs.find('a').eq(4);
			commissionTab.on('click', function () {
				var t = $('#line-cart-tabs').find('a').eq(1);
				$(t).trigger('click');
			});

			var addLineButtons = $('#button-commission-add, .button-line-add');
			addLineButtons.on('click', function () {
				var t = $('#line-cart-tabs').find('a').eq(0);
				$(t).trigger('click');
			});

			$('[href$=tab-commission]').on('click', function () {
				var t = $('#line-cart-tabs').find('a').eq(1);
				$(t).trigger('click');
			});

			$('[href$=tab-product], [href$=tab-description]').on('click', function () {
				var t = $('#line-cart-tabs').find('a').eq(0);
				$(t).trigger('click');
			});

			$('#button-batch-delete').on('click', function() {
				var keys = [],
						selected,
						selectAll;

				selected = $('tbody#cart').find('td:first-child input[type=checkbox]:checked');
				selectAll = $('tbody#cart').prev('thead').find('td:first-child input[type=checkbox]:checked');

				selected.each(function (idx, row) {
					keys.push($(this).val());
				});

				// TODO: This is copied from the normal delete and should be refactored at some point
				$.ajax({
					url: 'index.php?route=api/lines/remove&token=<?php echo $token; ?>&store_id=0',
					// ' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: { key: keys },
					dataType: 'json',
					beforeSend: function() {
						//$(node).button('loading');
					},
					complete: function() {
						//$(node).button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();

						var c = $('#content > .container-fluid');
						// Check for errors
						if (json['error']) {
							FormHelper.displayError(c, json['error']);
						} else {
							// Refresh products, vouchers and totals
							refresh();
							selectAll.prop('checked', false);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#button-vendor').on('click', function() {
				saveCustomer();
			});

			var productSearch = $('#tab-line input[name=\'product\']').autocomplete({
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
									price: item['price'],
									tax_class_id: item['tax_class_id']
								}
							}));
						}
					});
				},
				'select': function(item) {
					//console.log(item);
					$('#tab-line input[name=\'product\']').val(item['label']);
					$('#tab-line textarea[name=\'description\']').val(item['label']);
					$('#tab-line input[name=\'product_id\']').val(parseInt(item['value']));
					$('#tab-line input[name=\'quantity\']').val(1);
					$('#tab-line input[name=\'price\']').val(item['price']);
					$('#tab-line select[name=\'tax_class_id\']').val(parseInt(item['tax_class_id']));

					$('#tab-line .button-product-linked').show();
					$('#tab-line .button-product-unlinked').hide();
				}
			});

			$('#tab-line input[name=\'product\']').on('keydown', function () {
				$('#tab-line input[name=\'product_id\']').val('');

				$('#tab-line .button-product-linked').hide();
				$('#tab-line .button-product-unlinked').show();
			});

			$('#tab-line input[name=\'product\']').on('keydown', function () {
				$('#tab-line input[name=\'product_id\']').val('');

				$('#tab-line .button-product-linked').hide();
				$('#tab-line .button-product-unlinked').show();
			});

			$('.button-line-clear').on('click', function() {
				$('#tab-line input[name=\'product\']').val('');
				$('#tab-line textarea[name=\'description\']').val('');
				$('#tab-line input[name=\'product_id\']').val('');
				$('#tab-line input[name=\'quantity\']').val('');
				$('#tab-line input[name=\'price\']').val('');

				$('#tab-line .button-product-linked').hide();
				$('#tab-line .button-product-unlinked').show();
			});

			$('#tab-line').on('click', 'input, select, textarea, button', function(e) {
				if ($(e.target).attr('name') !== 'product') {
					$('#tab-line input[name=\'product\']').next('ul.dropdown-menu').hide();
				}
			});

			$('.button-product-unlinked').on('click', function () {
				$('#tab-line input[name=\'product\']').trigger('keydown');
			});

			$('.button-line-add').on('click', function() {
				var that = this,
						data;

				data = $('#tab-line').find('input, select, textarea, checkbox').not('input[name=product]'); // Exclude the autocomplete widget

				$.ajax({
					url: 'index.php?route=api/lines/add&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: data,
					dataType: 'json',
					beforeSend: function() {
						$(that).button('loading');
					},
					complete: function() {
						$(that).button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							if (json['error']['warning']) {
								FormHelper.displayError(c, json['error']['warning']);
							}

							if (json['error']['option']) {
								for (i in json['error']['option']) {
									var element = $('#input-option' + i.replace('_', '-'));

									if (element.parent().hasClass('input-group')) {
										$(element).parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
									} else {
										$(element).after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
									}
								}
							}

							if (json['error']['store']) {
								FormHelper.displayError(c, json['error']['store']);
							}

							// Highlight any found errors
							$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
						} else {
							// Refresh products, vouchers and totals
							refresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Voucher
			/*$('#button-voucher-add').on('click', function() {
			 $.ajax({
			 url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
			 type: 'post',
			 data: $('#tab-voucher input[type=\'text\'], #tab-voucher input[type=\'hidden\'], #tab-voucher input[type=\'radio\']:checked, #tab-voucher input[type=\'checkbox\']:checked, #tab-voucher select, #tab-voucher textarea'),
			 dataType: 'json',
			 beforeSend: function() {
			 $('#button-voucher-add').button('loading');
			 },
			 complete: function() {
			 $('#button-voucher-add').button('reset');
			 },
			 success: function(json) {
			 $('.alert, .text-danger').remove();
			 $('.form-group').removeClass('has-error');

			 var c = $('#content > .container-fluid');
			 if (json['error']) {
			 if (json['error']['warning']) {
			 FormHelper.displayError(c, json['error']['warning']);
			 }

			 for (i in json['error']) {
			 var element = $('#input-' + i.replace('_', '-'));

			 if (element.parent().hasClass('input-group')) {
			 $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
			 } else {
			 $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
			 }
			 }

			 // Highlight any found errors
			 $('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
			 } else {
			 $('input[name=\'from_name\']').attr('value', '');
			 $('input[name=\'from_email\']').attr('value', '');
			 $('input[name=\'to_name\']').attr('value', '');
			 $('input[name=\'to_email\']').attr('value', '');
			 $('textarea[name=\'message\']').attr('value', '');
			 $('input[name=\'amount\']').attr('value', '<?php echo addslashes($voucher_min); ?>');

			 // Refresh products, vouchers and totals
			 refresh();
			 }
			 },
			 error: function(xhr, ajaxOptions, thrownError) {
			 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			 }
			 });
			 });*/

			$('tbody#cart').on('click', '.btn-danger', function() {
				var node = this;

				$.ajax({
					url: 'index.php?route=api/lines/remove&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: 'key=' + encodeURIComponent(this.value),
					dataType: 'json',
					beforeSend: function() {
						$(node).button('loading');
					},
					complete: function() {
						$(node).button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();

						var c = $('#content > .container-fluid');
						// Check for errors
						if (json['error']) {
							FormHelper.displayError(c, json['error']);
						} else {
							// Refresh products, vouchers and totals
							refresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#button-cart').on('click', function() {
				$('a[href=\'#tab-payment\']').tab('show');
			});

			// Universal modal close handler
			$('.modal').on('click', 'button[data-action=close]', function (e) {
				$(e.delegateTarget).modal('toggle'); // Damn close doesn't work? Or did bootstrap change something, whatever, same effect
			});

			// Payment Address
			$('select[name=\'payment_address\']').on('change', function() {
				$.ajax({
					url: 'index.php?route=vendor/vendor/address&token=<?php echo $token; ?>&address_id=' + this.value,
					dataType: 'json',
					beforeSend: function() {
						// TODO: Trigger animation in modal instead...
						//$('select[name=\'payment_address\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
					},
					complete: function() {
						// TODO: Trigger animation in modal instead...
						//$('#tab-payment .fa-spin').remove();
					},
					success: function(json) {
						// Reset all fields
						$('#payment-address-modal form input[type=\'text\'], #payment-address-modal form input[type=\'text\'], #payment-address-modal form textarea').val('');
						$('#payment-address-modal form select option').not('#payment-address-modal form select[name=\'payment_address\']').removeAttr('selected');
						$('#payment-address-modal form input[type=\'checkbox\'], #payment-address-modal form input[type=\'radio\']').removeAttr('checked');

						$('#payment-address-modal form input[name=\'firstname\']').val(json['firstname']);
						$('#payment-address-modal form input[name=\'lastname\']').val(json['lastname']);
						$('#payment-address-modal form input[name=\'company\']').val(json['company']);
						$('#payment-address-modal form input[name=\'address_1\']').val(json['address_1']);
						$('#payment-address-modal form input[name=\'address_2\']').val(json['address_2']);
						$('#payment-address-modal form input[name=\'city\']').val(json['city']);
						$('#payment-address-modal form input[name=\'postcode\']').val(json['postcode']);
						$('#payment-address-modal form select[name=\'country_id\']').val(json['country_id']);

						payment_zone_id = json['zone_id'];

						$('#payment-address-modal form select[name=\'country_id\']').trigger('change');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			var payment_zone_id = '<?php echo $payment_zone_id; ?>';

			$('#payment-address-modal form select[name=\'country_id\']').on('change', function() {
				$.ajax({
					url: 'index.php?route=sale/order/country&token=<?php echo $token; ?>&country_id=' + this.value,
					dataType: 'json',
					beforeSend: function() {
						$('#payment-address-modal form select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
					},
					complete: function() {
						$('#payment-address-modal form .fa-spin').remove();
					},
					success: function(json) {
						if (json['postcode_required'] == '1') {
							$('#payment-address-modal form input[name=\'postcode\']').parent().parent().addClass('required');
						} else {
							$('#payment-address-modal form input[name=\'postcode\']').parent().parent().removeClass('required');
						}

						html = '<option value=""><?php echo $text_select; ?></option>';

						if (json['zone'] && json['zone'] != '') {
							for (i = 0; i < json['zone'].length; i++) {
								html += '<option value="' + json['zone'][i]['zone_id'] + '"';

								if (json['zone'][i]['zone_id'] == payment_zone_id) {
									html += ' selected="selected"';
								}

								html += '>' + json['zone'][i]['name'] + '</option>';
							}
						} else {
							html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
						}

						$('#payment-address-modal form select[name=\'zone_id\']').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#payment-address-modal form select[name=\'country_id\']').trigger('change');

			$('#button-payment-address').on('click', function() {
				$('#payment-address-modal').modal('toggle');
			});

			$('.button-payment-address-apply').on('click', function() {
				var fields = $('#payment-address-modal').find('input, select', 'textarea'),
						data = [], form, search, replace;

				search = [
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					//'{zone_code}',
					'{country}'
				];

				form = $('#payment-address-modal');
				replace = [
					form.find('input[name=firstname]').val(),
					form.find('input[name=lastname]').val(),
					form.find('input[name=company]').val(),
					form.find('input[name=address_1]').val(),
					form.find('input[name=address_2]').val(),
					form.find('input[name=city]').val(),
					form.find('input[name=postcode]').val(),
					form.find('select[name=zone_id] option').filter(':selected').text(),
					form.find('select[name=country_id] option').filter(':selected').text()
				];

				var replaceArray = function (string, find, replace) {
					var regex;
					for (var i = 0; i < find.length; i++) {
						regex = new RegExp(find[i], "g");
						string = string.replace(regex, replace[i]);
					}

					return string;
				};

				var address = replaceArray('<?php echo $payment_address_format; ?>', search, replace);
				var input = $('textarea[name=payment_address]');
				/*console.log(input);
				 console.log(address);
				 console.log('- 1 -------------');*/
				input.val(address);
				/*console.log(input);
				 console.log(address);
				 console.log('- 2 -------------');*/

				$.ajax({
					url: 'index.php?route=api/payment/address&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('#payment-address-modal form input[type=\'text\'], #payment-address-modal form input[type=\'hidden\'], #payment-address-modal form input[type=\'radio\']:checked, #payment-address-modal form input[type=\'checkbox\']:checked, #payment-address-modal form select, #payment-address-modal form textarea'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-payment-address').button('loading');
					},
					complete: function() {
						$('#button-payment-address').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						// Check for errors
						if (json['error']) {
							if (json['error']['warning']) {
								FormHelper.displayError(c, json['error']['warning']);
							}

							for (i in json['error']) {
								var element = $('#input-payment-' + i.replace('_', '-'));

								if ($(element).parent().hasClass('input-group')) {
									$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
								} else {
									$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
								}
							}

							// Highlight any found errors
							$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
						} else {
							// Payment Methods
							$.ajax({
								url: 'index.php?route=api/payment/methods&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
								dataType: 'json',
								beforeSend: function() {
									$('#button-payment-address i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
									$('#button-payment-address').prop('disabled', true);
								},
								complete: function() {
									$('#button-payment-address i').replaceWith('<i class="fa fa-arrow-right"></i>');
									$('#button-payment-address').prop('disabled', false);
								},
								success: function(json) {
									var c = $('#content > .container-fluid');
									if (json['error']) {
										FormHelper.displayError(c, json['error']);
									} else {
										html = '<option value=""><?php echo $text_select; ?></option>';

										if (json['payment_methods']) {
											for (i in json['payment_methods']) {
												if (json['payment_methods'][i]['code'] == $('select[name=\'payment_method\'] option:selected').val()) {
													html += '<option value="' + json['payment_methods'][i]['code'] + '" selected="selected">' + json['payment_methods'][i]['title'] + '</option>';
												} else {
													html += '<option value="' + json['payment_methods'][i]['code'] + '">' + json['payment_methods'][i]['title'] + '</option>';
												}
											}
										}

										$('select[name=\'payment_method\']').html(html);
									}
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});

							// Refresh products, vouchers and totals
							refresh();

							// Save vendor
							saveCustomer();

							// If shipping required got to shipping tab else total tabs
							if ($('select[name=\'shipping_method\']').prop('disabled')) {
								$('a[href=\'#tab-total\']').tab('show');
							} else {
								$('a[href=\'#shipping-address-modal form\']').tab('show');
							}

							$('#payment-address-modal').modal('hide');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Shipping Address
			$('select[name=\'shipping_address\']').on('change', function() {
				$.ajax({
					url: 'index.php?route=vendor/vendor/address&token=<?php echo $token; ?>&address_id=' + this.value,
					dataType: 'json',
					beforeSend: function() {
						// TODO: Trigger animation in modal instead...
						//$('select[name=\'shipping_address\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
					},
					complete: function() {
						// TODO: Trigger animation in modal instead...
						$('#shipping-address-modal form .fa-spin').remove();
					},
					success: function(json) {
						// Reset all fields
						$('#shipping-address-modal form input[type=\'text\'], #shipping-address-modal form input[type=\'text\'], #shipping-address-modal form textarea').val('');
						$('#shipping-address-modal form select option').not('#shipping-address-modal form select[name=\'shipping_address\']').removeAttr('selected');
						$('#shipping-address-modal form input[type=\'checkbox\'], #shipping-address-modal form input[type=\'radio\']').removeAttr('checked');

						$('#shipping-address-modal form input[name=\'firstname\']').val(json['firstname']);
						$('#shipping-address-modal form input[name=\'lastname\']').val(json['lastname']);
						$('#shipping-address-modal form input[name=\'company\']').val(json['company']);
						$('#shipping-address-modal form input[name=\'address_1\']').val(json['address_1']);
						$('#shipping-address-modal form input[name=\'address_2\']').val(json['address_2']);
						$('#shipping-address-modal form input[name=\'city\']').val(json['city']);
						$('#shipping-address-modal form input[name=\'postcode\']').val(json['postcode']);
						$('#shipping-address-modal form select[name=\'country_id\']').val(json['country_id']);

						shipping_zone_id = json['zone_id'];

						$('#shipping-address-modal form select[name=\'country_id\']').trigger('change');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			var shipping_zone_id = '<?php echo $shipping_zone_id; ?>';

			$('#shipping-address-modal form select[name=\'country_id\']').on('change', function() {
				$.ajax({
					url: 'index.php?route=sale/order/country&token=<?php echo $token; ?>&country_id=' + this.value,
					dataType: 'json',
					beforeSend: function() {
						$('#shipping-address-modal form select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
					},
					complete: function() {
						$('#shipping-address-modal form .fa-spin').remove();
					},
					success: function(json) {
						if (json['postcode_required'] == '1') {
							$('#shipping-address-modal form input[name=\'postcode\']').parent().parent().addClass('required');
						} else {
							$('#shipping-address-modal form input[name=\'postcode\']').parent().parent().removeClass('required');
						}

						html = '<option value=""><?php echo $text_select; ?></option>';

						if (json['zone'] && json['zone'] != '') {
							for (i = 0; i < json['zone'].length; i++) {
								html += '<option value="' + json['zone'][i]['zone_id'] + '"';

								if (json['zone'][i]['zone_id'] == shipping_zone_id) {
									html += ' selected="selected"';
								}

								html += '>' + json['zone'][i]['name'] + '</option>';
							}
						} else {
							html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
						}

						$('#shipping-address-modal form select[name=\'zone_id\']').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#shipping-address-modal form select[name=\'country_id\']').trigger('change');

			$('#button-shipping-address').on('click', function() {
				$('#shipping-address-modal').modal('toggle');
			});

			$('.button-shipping-address-apply').on('click', function() {
				var fields = $('#payment-address-modal').find('input, select', 'textarea'),
						data = [], form, search, replace;

				search = [
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					//'{zone_code}',
					'{country}'
				];

				form = $('#shipping-address-modal');
				replace = [
					form.find('input[name=firstname]').val(),
					form.find('input[name=lastname]').val(),
					form.find('input[name=company]').val(),
					form.find('input[name=address_1]').val(),
					form.find('input[name=address_2]').val(),
					form.find('input[name=city]').val(),
					form.find('input[name=postcode]').val(),
					form.find('select[name=zone_id] option').filter(':selected').text(),
					form.find('select[name=country_id] option').filter(':selected').text()
				];

				var replaceArray = function (string, find, replace) {
					var regex;
					for (var i = 0; i < find.length; i++) {
						regex = new RegExp(find[i], "g");
						string = string.replace(regex, replace[i]);
					}

					return string;
				};

				var address = replaceArray('<?php echo $shipping_address_format; ?>', search, replace);
				var input = $('textarea[name=shipping_address]');
				/*console.log(input);
				 console.log(address);
				 console.log('- 1 -------------');*/
				input.val(address);
				/*console.log(input);
				 console.log(address);
				 console.log('- 2 -------------');*/

				$.ajax({
					url: 'index.php?route=api/shipping/address&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('#shipping-address-modal form input[type=\'text\'], #shipping-address-modal form input[type=\'hidden\'], #shipping-address-modal form input[type=\'radio\']:checked, #shipping-address-modal form input[type=\'checkbox\']:checked, #shipping-address-modal form select, #shipping-address-modal form textarea'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-shipping-address').button('loading');
					},
					complete: function() {
						$('#button-shipping-address').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						// Check for errors
						if (json['error']) {
							if (json['error']['warning']) {
								FormHelper.displayError(c, json['error']['warning']);
							}

							for (i in json['error']) {
								var element = $('#input-shipping-' + i.replace('_', '-'));

								if ($(element).parent().hasClass('input-group')) {
									$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
								} else {
									$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
								}
							}

							// Highlight any found errors
							$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
						} else {
							// Shipping Methods
							$.ajax({
								url: 'index.php?route=api/shipping/methods&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
								dataType: 'json',
								beforeSend: function() {
									$('#button-shipping-address i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
									$('#button-shipping-address').prop('disabled', true);
								},
								complete: function() {
									$('#button-shipping-address i').replaceWith('<i class="fa fa-arrow-right"></i>');
									$('#button-shipping-address').prop('disabled', false);
								},
								success: function(json) {
									$('#shipping-address-modal').modal('hide');

									if (json['error']) {
										$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
									} else {
										// Shipping Methods
										html = '<option value=""><?php echo $text_select; ?></option>';

										if (json['shipping_methods']) {
											for (i in json['shipping_methods']) {
												html += '<optgroup label="' + json['shipping_methods'][i]['title'] + '">';

												if (!json['shipping_methods'][i]['error']) {
													for (j in json['shipping_methods'][i]['quote']) {
														if (json['shipping_methods'][i]['quote'][j]['code'] == $('select[name=\'shipping_method\'] option:selected').val()) {
															html += '<option value="' + json['shipping_methods'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_methods'][i]['quote'][j]['title'] + ' - ' + json['shipping_methods'][i]['quote'][j]['text'] + '</option>';
														} else {
															html += '<option value="' + json['shipping_methods'][i]['quote'][j]['code'] + '">' + json['shipping_methods'][i]['quote'][j]['title'] + ' - ' + json['shipping_methods'][i]['quote'][j]['text'] + '</option>';
														}
													}
												} else {
													html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
												}

												html += '</optgroup>';
											}
										}

										$('select[name=\'shipping_method\']').html(html);
									}
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});

							// Refresh products, vouchers and totals
							refresh();

							// Save vendor
							saveCustomer();

							$('a[href=\'#tab-total\']').tab('show');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Shipping Method
			/*$('#button-shipping-method').on('click', function() {
			 $.ajax({
			 url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/shipping/method&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
			 type: 'post',
			 data: 'shipping_method=' + $('select[name=\'shipping_method\'] option:selected').val(),
			 dataType: 'json',
			 beforeSend: function() {
			 $('#button-shipping-method').button('loading');
			 },
			 complete: function() {
			 $('#button-shipping-method').button('reset');
			 },
			 success: function(json) {
			 $('.alert, .text-danger').remove();
			 $('.form-group').removeClass('has-error');

			 var c = $('#content > .container-fluid');
			 if (json['error']) {
			 FormHelper.displayError(c, json['error']);

			 // Highlight any found errors
			 $('select[name=\'shipping_method\']').parent().parent().parent().addClass('has-error');
			 }

			 if (json['success']) {
			 $('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

			 // Refresh products, vouchers and totals
			 refresh();
			 }
			 },
			 error: function(xhr, ajaxOptions, thrownError) {
			 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			 }
			 });
			 });*/

			// Payment Method
			/*$('#button-payment-method').on('click', function() {
			 $.ajax({
			 url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/payment/method&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
			 type: 'post',
			 data: 'payment_method=' + $('select[name=\'payment_method\'] option:selected').val(),
			 dataType: 'json',
			 beforeSend: function() {
			 $('#button-payment-method').button('loading');
			 },
			 complete: function() {
			 $('#button-payment-method').button('reset');
			 },
			 success: function(json) {
			 $('.alert, .text-danger').remove();
			 $('.form-group').removeClass('has-error');

			 var c = $('#content > .container-fluid');
			 if (json['error']) {
			 FormHelper.displayError(c, json['error']);

			 // Highlight any found errors
			 $('select[name=\'payment_method\']').parent().parent().parent().addClass('has-error');
			 }

			 if (json['success']) {
			 $('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

			 // Refresh products, vouchers and totals
			 refresh();
			 }
			 },
			 error: function(xhr, ajaxOptions, thrownError) {
			 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			 }
			 });
			 });*/


			// Shipping Method
			$('#button-shipping-method').on('click', function() {
				$('#shipping-modal').modal('toggle');

				/*$.ajax({
				 url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/shipping/method&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
				 type: 'post',
				 data: 'shipping_method=' + $('select[name=\'shipping_method\'] option:selected').val(),
				 dataType: 'json',
				 beforeSend: function() {
				 $('#input-shipping-method').attr('disabled', 'disabled');
				 },
				 complete: function() {
				 $('#input-shipping-method').removeAttr('disabled');
				 },
				 success: function(json) {
				 $('.alert, .text-danger').remove();
				 $('.form-group').removeClass('has-error');
				 if (json['error']) {
				 $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				 // Highlight any found errors
				 $('select[name=\'shipping_method\']').parent().parent().addClass('has-error');
				 }
				 if (json['success']) {
				 $('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				 // Refresh products, vouchers and totals
				 $('#button-refresh').trigger('click');
				 }
				 },
				 error: function(xhr, ajaxOptions, thrownError) {
				 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				 }
				 });*/
			});
			// Payment Method
			$('#button-payment-method').on('click', function() {
				$('#payment-modal').modal('toggle');

				$.ajax({
					url: 'index.php?route=api/payment/method&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: 'payment_method=' + $('select[name=\'payment_method\'] option:selected').val(),
					dataType: 'json',
					beforeSend: function() {
						$('#input-payment-method').attr('disabled', 'disabled');
					},
					complete: function() {
						$('#input-payment-method').removeAttr('disabled');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');
						if (json['error']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
							// Highlight any found errors
							$('select[name=\'payment_method\']').parent().parent().addClass('has-error');
						}
						if (json['success']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
							// Refresh products, vouchers and totals
							$('#button-refresh').trigger('click');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Coupon
			$('#button-coupon').on('click', function() {
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/coupon&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('input[name=\'coupon\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-coupon').button('loading');
					},
					complete: function() {
						$('#button-coupon').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							FormHelper.displayError(c, json['error']);

							// Highlight any found errors
							$('input[name=\'coupon\']').parent().parent().parent().addClass('has-error');
						}

						if (json['success']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

							// Refresh products, vouchers and totals
							refresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Voucher
			$('#button-voucher').on('click', function() {
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('input[name=\'voucher\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-voucher').button('loading');
					},
					complete: function() {
						$('#button-voucher').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							FormHelper.displayError(c, json['error']);

							// Highlight any found errors
							$('input[name=\'voucher\']').parent().parent().parent().addClass('has-error');
						}

						if (json['success']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

							// Refresh products, vouchers and totals
							refresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Reward
			$('#button-reward').on('click', function() {
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/reward&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('input[name=\'reward\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-reward').button('loading');
					},
					complete: function() {
						$('#button-reward').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');

						var c = $('#content > .container-fluid');
						if (json['error']) {
							FormHelper.displayError(c, json['error']);

							// Highlight any found errors
							$('input[name=\'reward\']').parent().parent().parent().addClass('has-error');
						}

						if (json['success']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

							// Refresh products, vouchers and totals
							refresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			// Affiliate
			/*$('input[name=\'affiliate\']').autocomplete({
			 'source': function(request, response) {
			 $.ajax({
			 url: 'index.php?route=marketing/affiliate/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			 dataType: 'json',
			 success: function(json) {
			 json.unshift({
			 affiliate_id: 0,
			 name: '<?php echo $text_none; ?>'
			 });

			 response($.map(json, function(item) {
			 return {
			 label: item['name'],
			 value: item['affiliate_id']
			 }
			 }));
			 }
			 });
			 },
			 'select': function(item) {
			 $('input[name=\'affiliate\']').val(item['label']);
			 $('input[name=\'affiliate_id\']').val(item['value']);
			 }
			 });*/

			// Checkout
			$('#button-save').on('click', function() {
				var purchase_order_id = $('input[name=\'purchase_order_id\']').val(),
						order_id = $('input[name=\'purchase_order_id\']').val(),
						url, data;

				if (purchase_order_id == 0) {
					url = 'index.php?route=transaction/purchase_order/add&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val();
				} else {
					url = 'index.php?route=transaction/purchase_order/edit&token=<?php echo $token; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val() + '&purchase_order_id=' + purchase_order_id;
				}

				$.ajax({
					url: url,
					type: 'post',
					data: {
						store_id: $('input[name=\'store_id\']').val(),
						purchase_order_id: $('input[name=\'purchase_order_id\']').val(),
						purchase_order_status_id:  $('select[name=\'purchase_order_status_id\']').val(),
						purchase_order_date:  $('input[name=\'purchase_order_date\']').val(),
						due_date:  $('select[name=\'due_date\']').val(),
						vendor_memo: $('textarea[name=\'vendor_memo\']').val(),
						statement_memo: $('textarea[name=\'statement_memo\']').val(),
						affiliate_id: $('input[name=\'affiliate_id\']').val(),
					},
					//data: $('input[name=\'purchase_order_id\'], select[name=\'purchase_order_status_id\'], textarea[name=\'comment\'], [name=\'affiliate_id\'], [name=\'purchase_order_id\']'),
					//data: $('input[name=\'purchase_order_id\'], select[name=\'purchase_order_status_id\'], textarea[name=\'comment\'], [name=\'affiliate_id\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-save').button('loading');
					},
					complete: function() {
						$('#button-save').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();

						var c = $('#content > .container-fluid');
						if (json['error']) {
							FormHelper.displayError(c, json['error']);
						}

						if (json['success']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '  <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}

						if (json['purchase_order_id']) {
							$('input[name=\'purchase_order_id\']').val(json['purchase_order_id']);
						}

						window.location.reload()
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#content').delegate('button[id^=\'button-upload\'], button[id^=\'button-custom-field\'], button[id^=\'button-payment-custom-field\'], button[id^=\'button-shipping-custom-field\']', 'click', function() {
				var node = this;

				$('#form-upload').remove();

				$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

				$('#form-upload input[name=\'file\']').trigger('click');

				if (typeof timer != 'undefined') {
					clearInterval(timer);
				}

				timer = setInterval(function() {
					if ($('#form-upload input[name=\'file\']').val() != '') {
						clearInterval(timer);

						$.ajax({
							url: 'index.php?route=tool/upload/upload&token=<?php echo $token; ?>',
							type: 'post',
							dataType: 'json',
							data: new FormData($('#form-upload')[0]),
							cache: false,
							contentType: false,
							processData: false,
							beforeSend: function() {
								$(node).button('loading');
							},
							complete: function() {
								$(node).button('reset');
							},
							success: function(json) {
								$(node).parent().find('.text-danger').remove();

								if (json['error']) {
									$(node).parent().find('input[type=\'hidden\']').after('<div class="text-danger">' + json['error'] + '</div>');
								}

								if (json['success']) {
									alert(json['success']);
								}

								if (json['code']) {
									$(node).parent().find('input[type=\'hidden\']').attr('value', json['code']);
								}
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					}
				}, 500);
			});

			$('.date').datetimepicker({
				pickTime: false
			});

			$('.datetime').datetimepicker({
				pickDate: true,
				pickTime: true
			});

			$('.time').datetimepicker({
				pickDate: false
			});

			refresh(); // Get the totals
			// TODO: Something interesting is going on here...
			// It appears that the order is populated with the vendor cart until a product is added?
			// I need to confirm this and decide whether I want to retain this behavior in QC

			//--></script>

		<script type="text/javascript">
			// Sort the custom fields
			$('[data-target=\'vendor\'] .form-group[data-sort]').detach().each(function() {
				if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('[data-target=\'vendor\'] .form-group').length) {
					$('[data-target=\'vendor\'] .form-group').eq($(this).attr('data-sort')).before(this);
				}

				if ($(this).attr('data-sort') > $('[data-target=\'vendor\'] .form-group').length) {
					$('[data-target=\'vendor\'] .form-group:last').after(this);
				}

				if ($(this).attr('data-sort') < -$('[data-target=\'vendor\'] .form-group').length) {
					$('[data-target=\'vendor\'] .form-group:first').before(this);
				}
			});

			// Sort the custom fields
			$('#payment-address-modal form .form-group[data-sort]').detach().each(function() {
				if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#payment-address-modal form .form-group').length) {
					$('#payment-address-modal form .form-group').eq($(this).attr('data-sort')).before(this);
				}

				if ($(this).attr('data-sort') > $('#payment-address-modal form .form-group').length) {
					$('#payment-address-modal form .form-group:last').after(this);
				}

				if ($(this).attr('data-sort') < -$('#payment-address-modal form .form-group').length) {
					$('#payment-address-modal form .form-group:first').before(this);
				}
			});

			$('#shipping-address-modal form .form-group[data-sort]').detach().each(function() {
				if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#shipping-address-modal form .form-group').length) {
					$('#shipping-address-modal form .form-group').eq($(this).attr('data-sort')).before(this);
				}

				if ($(this).attr('data-sort') > $('#shipping-address-modal form .form-group').length) {
					$('#shipping-address-modal form .form-group:last').after(this);
				}

				if ($(this).attr('data-sort') < -$('#shipping-address-modal form .form-group').length) {
					$('#shipping-address-modal form .form-group:first').before(this);
				}
			});

			$('body').find('table thead').on('click', 'td:first-child input[type=checkbox]', function (e) {
				var checked = $(this).prop('checked'),
						table = $(e.delegateTarget).closest('table'),
						selected = table.find('tbody tr td:first-child input[type=checkbox]');

				selected.each(function () {
					$(this).prop('checked', checked);
				});
			});
		</script></div>
	<?php echo $footer; ?>