<?php
// TODO: WIDGET FACTORY - This is a first step
// Credits: from Kendo UI
function random() {
  return (float)rand()/(float)getrandmax();
}

function guid() {
	$id = '';
	$i = null;
	$random = null;

	for ($i = 0; $i < 32; $i++) {
		$random = random() * 16 | 0;

		if ($i == 8 || $i == 12 || $i == 16 || $i == 20) {
			$id .= '-';
		}

		$id .= base_convert((string)($i == 12 ? 4 : ($i == 16 ? ($random & 3 | 8) : $random)), 10, 16); //.toString(16);
	}

	return $id;
}
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="qc-product-customizer" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="" class="btn btn-success"><i class="fa fa-magic"></i> EZ Product Customizer</button> 
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="row">
          <div class="col-sm-12 col-lg-5">
            <div class="form-group required">
              <label class="col-sm-2 col-lg-3 control-label" for="input-model"><?php echo $entry_model; ?></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
                <?php if ($error_model) { ?>
                <div class="text-danger"><?php echo $error_model; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-parent"><span data-toggle="tooltip" title="<?php echo $help_parent; ?>"><?php echo $entry_parent; ?></span></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="parent" value="<?php echo $parent ?>" placeholder="<?php echo $entry_parent; ?>" id="input-parent" class="form-control" data-token="<?php echo $token; ?>" />
                <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-qbname"><span data-toggle="tooltip" title="<?php echo $help_qbname; ?>"><?php echo $entry_qbname; ?></span></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="qbname" value="<?php echo $qbname; ?>" placeholder="<?php echo $entry_qbname; ?>" id="input-qbname" class="form-control" readonly="readonly" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-manufacturer"><span data-toggle="tooltip" title="<?php echo $help_manufacturer; ?>"><?php echo $entry_manufacturer; ?></span></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="manufacturer" value="<?php echo $manufacturer ?>" placeholder="<?php echo $entry_manufacturer; ?>" id="input-manufacturer" class="form-control" />
                <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-image"><?php echo $entry_image; ?></label>
              <div class="col-sm-10 col-lg-9">
                 <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                 <!--<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                 <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                 <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>-->
                <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-price"><?php echo $entry_price; ?></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-points"><span data-toggle="tooltip" title="<?php echo $help_points; ?>"><?php echo $entry_points; ?></span></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="points" value="<?php echo $points; ?>" placeholder="<?php echo $entry_points; ?>" id="input-points" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
              <div class="col-sm-10 col-lg-9">
                <select name="tax_class_id" id="input-tax-class" class="form-control">
                <option value="0"><?php echo $text_none; ?></option>
                <?php foreach ($tax_classes as $tax_class) { ?>
                <?php if ($tax_class['tax_class_id'] == $tax_class_id) { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                <?php } ?>
                <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
              <div class="col-sm-5 col-lg-3">
                <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
              </div>
              <label class="col-sm-2 col-lg-3 control-label" for="input-minimum"><span data-toggle="tooltip" title="<?php echo $help_minimum; ?>"><?php echo $entry_minimum; ?></span></label>
              <div class="col-sm-5 col-lg-3">
                <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="<?php echo $entry_minimum; ?>" id="input-minimum" class="form-control" />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-subtract"><?php echo $entry_subtract; ?></label>
              <div class="col-sm-5 col-lg-3">
                <select name="subtract" id="input-subtract" class="form-control">
                  <?php if ($subtract) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <option value="0"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } ?>
                </select>
              </div>
              <label class="col-sm-2 col-lg-3 control-label" for="input-stock-status"><span data-toggle="tooltip" title="<?php echo $help_stock_status; ?>"><?php echo $entry_stock_status; ?></span></label>
              <div class="col-sm-5 col-lg-3">
                <select name="stock_status_id" id="input-stock-status" class="form-control">
                  <?php foreach ($stock_statuses as $stock_status) { ?>
                  <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                  <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 col-lg-3 control-label" for="input-status"><?php echo $entry_status; ?></label>
              <div class="col-sm-5 col-lg-3">
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
              <label class="col-sm-2 col-lg-3 control-label" for="input-date-available"><?php echo $entry_date_available; ?></label>
              <div class="col-sm-3">
                <div class="input-group date">
                <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="<?php echo $entry_date_available; ?>" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control" />
                <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                </span></div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-location"><?php echo $entry_location; ?></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="location" value="<?php echo $location; ?>" placeholder="<?php echo $entry_location; ?>" id="input-location" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 col-lg-3 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
              <div class="col-sm-10 col-lg-9">
                <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-lg-7">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
              <li><a href="#tab-catalog" data-toggle="tab"><?php echo 'Catalog'; //$tab_package; ?></a></li>
              <li><a href="#tab-package" data-toggle="tab"><?php echo 'Package'; //$tab_package; ?></a></li>
              <li><a href="#tab-pricing" data-toggle="tab"><?php echo 'Pricing'; //$tab_pricing; ?></a></li>
              <li><a href="#tab-attribute" data-toggle="tab"><?php echo 'Attributes'; //$tab_attribute; ?></a></li>
              <li><a href="#tab-option" data-toggle="tab"><?php echo 'Options'; $tab_option; ?></a></li>
              <!--<li><a href="#tab-recurring" data-toggle="tab"><?php echo $tab_recurring; ?></a></li>-->
              <li><a href="#tab-download" data-toggle="tab"><?php echo 'Downloads'; //$tab_recurring; ?></a></li>
              <li><a href="#tab-related" data-toggle="tab"><?php echo 'Related'; //$tab_recurring; ?></a></li>
              <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
              <li><a href="#tab-reward" data-toggle="tab"><?php echo 'Credits'; //$tab_reward; ?></a></li>
              <!--<li><a href="#tab-design" data-toggle="tab"><?php echo $tab_design; ?></a></li>-->
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab-general">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
                    <?php if ($error_keyword) { ?>
                    <div class="text-danger"><?php echo $error_keyword; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                  <div class="col-sm-10">
                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                      <div class="checkbox">
                        <label>
                          <?php if (in_array(0, $product_store)) { ?>
                          <input type="checkbox" name="product_store[]" value="0" checked="checked" />
                          <?php echo $text_default; ?>
                          <?php } else { ?>
                          <input type="checkbox" name="product_store[]" value="0" />
                          <?php echo $text_default; ?>
                          <?php } ?>
                        </label>
                      </div>
                      <?php foreach ($stores as $store) { ?>
                      <div class="checkbox">
                        <label>
                          <?php if (in_array($store['store_id'], $product_store)) { ?>
                          <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                          <?php echo $store['name']; ?>
                          <?php } else { ?>
                          <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                          <?php echo $store['name']; ?>
                          <?php } ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                    <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                      <?php foreach ($product_categories as $product_category) { ?>
                      <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                        <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-catalog">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-sku"><span data-toggle="tooltip" title="<?php echo $help_sku; ?>"><?php echo $entry_sku; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="<?php echo $entry_sku; ?>" id="input-sku" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-upc"><span data-toggle="tooltip" title="<?php echo $help_upc; ?>"><?php echo $entry_upc; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="upc" value="<?php echo $upc; ?>" placeholder="<?php echo $entry_upc; ?>" id="input-upc" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-ean"><span data-toggle="tooltip" title="<?php echo $help_ean; ?>"><?php echo $entry_ean; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="ean" value="<?php echo $ean; ?>" placeholder="<?php echo $entry_ean; ?>" id="input-ean" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-jan"><span data-toggle="tooltip" title="<?php echo $help_jan; ?>"><?php echo $entry_jan; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="jan" value="<?php echo $jan; ?>" placeholder="<?php echo $entry_jan; ?>" id="input-jan" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-isbn"><span data-toggle="tooltip" title="<?php echo $help_isbn; ?>"><?php echo $entry_isbn; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="isbn" value="<?php echo $isbn; ?>" placeholder="<?php echo $entry_isbn; ?>" id="input-isbn" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-mpn"><span data-toggle="tooltip" title="<?php echo $help_mpn; ?>"><?php echo $entry_mpn; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="mpn" value="<?php echo $mpn; ?>" placeholder="<?php echo $entry_mpn; ?>" id="input-mpn" class="form-control" />
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-pricing">
                <div class="form-group" id="edit-discount">
                <label class="col-sm-2 control-label"><?php echo 'Discounts'; //$entry_store; ?></label>
                <div class="table-responsive col-sm-10">
                  <table id="discount" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <td class="text-left"><?php echo $entry_customer_group; ?></td>
                        <td class="text-right"><?php echo $entry_quantity; ?></td>
                        <td class="text-right"><?php echo $entry_priority; ?></td>
                        <td class="text-right"><?php echo $entry_price; ?></td>
                        <td></td>
                      </tr>
                    </thead>
                      <tbody>
                        <?php $discount_row = 0; ?>
                        <?php foreach ($product_discounts as $product_discount) { ?>
                        <?php $pd_id_prefix = 'd' . $discount_row . '_'; ?>
                        <?php $pd_name_prefix = 'product_discount[' . $discount_row . ']'; ?>
                        <tr id="discount-row<?php echo $discount_row; ?>">
                          <td class="text-left"><select id="<?php echo $pd_id_prefix; ?>customer_group_id" name="<?php echo $pd_name_prefix; ?>[customer_group_id]" class="form-control">
                              <?php foreach ($customer_groups as $customer_group) { ?>
                              <?php if ($customer_group['customer_group_id'] == $product_discount['customer_group_id']) { ?>
                              <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                              <?php } else { ?>
                              <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                              <?php } ?>
                              <?php } ?>
                            </select></td>
                          <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>quantity" name="<?php echo $pd_name_prefix; ?>[quantity]" value="<?php echo $product_discount['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                          <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>priority" name="<?php echo $pd_name_prefix; ?>[priority]" value="<?php echo $product_discount['priority']; ?>" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>
                          <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>price" name="<?php echo $pd_name_prefix; ?>[price]" value="<?php echo $product_discount['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                          <td class="text-left"><button type="button" onclick="$('#discount-row<?php echo $discount_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                        </tr>
                        <?php $discount_row++; ?>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="4"></td>
                          <td class="text-left"><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="<?php echo $button_discount_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                        </tr>
                      </tfoot>
                  </table>
                </div>
                </div>
                <div class="form-group" id="edit-special">
                  <label class="col-sm-2 control-label"><?php echo 'Specials'; //$entry_store; ?></label>
                  <div class="table-responsive col-sm-10">
                  <table id="special" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <td class="text-left"><?php echo $entry_customer_group; ?></td>
                        <td class="text-right"><?php echo $entry_priority; ?></td>
                        <td class="text-right"><?php echo $entry_price; ?></td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $special_row = 0; ?>
                      <?php foreach ($product_specials as $product_special) { ?>
                      <?php $ps_id_prefix = 's' . $special_row . '_'; ?>
                      <?php $ps_name_prefix = 'product_special[' . $special_row . ']'; ?>
                      <tr id="special-row<?php echo $special_row; ?>">
                        <td class="text-left"><select id="<?php echo $ps_id_prefix; ?>customer_group_id" name="<?php echo $ps_name_prefix; ?>[customer_group_id]" class="form-control">
                            <?php foreach ($customer_groups as $customer_group) { ?>
                            <?php if ($customer_group['customer_group_id'] == $product_special['customer_group_id']) { ?>
                            <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                          </select></td>
                        <td class="text-right"><input type="text" id="<?php echo $ps_id_prefix; ?>priority" name="<?php echo $ps_name_prefix; ?>[priority]" value="<?php echo $product_special['priority']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                        <td class="text-right"><input type="text" id="<?php echo $ps_name_prefix; ?>price" name="<?php echo $ps_name_prefix; ?>[price]" value="<?php echo $product_special['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                        <td class="text-left"><button type="button" onclick="$('#special-row<?php echo $special_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                      </tr>
                      <?php $special_row++; ?>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="3"></td>
                        <td class="text-left"><button type="button" onclick="addSpecial();" data-toggle="tooltip" title="<?php echo $button_special_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                      </tr>
                    </tfoot>
                  </table>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-package">
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_shipping; ?></label>
                  <div class="col-sm-10">
                    <label class="radio-inline">
                      <?php if ($shipping) { ?>
                      <input type="radio" name="shipping" value="1" checked="checked" />
                      <?php echo $text_yes; ?>
                      <?php } else { ?>
                      <input type="radio" name="shipping" value="1" />
                      <?php echo $text_yes; ?>
                      <?php } ?>
                    </label>
                    <label class="radio-inline">
                      <?php if (!$shipping) { ?>
                      <input type="radio" name="shipping" value="0" checked="checked" />
                      <?php echo $text_no; ?>
                      <?php } else { ?>
                      <input type="radio" name="shipping" value="0" />
                      <?php echo $text_no; ?>
                      <?php } ?>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-length"><?php echo 'Dimensions <br><small>(L x W x H)</small>'; //$entry_dimension; ?></label><!-- TODO: HTML allowed for lang stuff -->
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-4">
                        <input type="text" name="length" value="<?php echo $length; ?>" placeholder="<?php echo $entry_length; ?>" id="input-length" class="form-control" />
                      </div>
                      <div class="col-sm-4">
                        <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
                      </div>
                      <div class="col-sm-4">
                        <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-length-class"><?php echo $entry_length_class; ?></label>
                  <div class="col-sm-10">
                    <select name="length_class_id" id="input-length-class" class="form-control">
                      <?php foreach ($length_classes as $length_class) { ?>
                      <?php if ($length_class['length_class_id'] == $length_class_id) { ?>
                      <option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-weight"><?php echo $entry_weight; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="<?php echo $entry_weight; ?>" id="input-weight" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-weight-class"><?php echo $entry_weight_class; ?></label>
                  <div class="col-sm-10">
                    <select name="weight_class_id" id="input-weight-class" class="form-control">
                      <?php foreach ($weight_classes as $weight_class) { ?>
                      <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
                      <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-download">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-download"><span data-toggle="tooltip" title="<?php echo $help_download; ?>"><?php echo $entry_download; ?></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="download" value="" placeholder="<?php echo $entry_download; ?>" id="input-download" class="form-control" />
                      <div id="product-download" class="well well-sm" style="height: 150px; overflow: auto;">
                        <?php foreach ($product_downloads as $product_download) { ?>
                        <div id="product-download<?php echo $product_download['download_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_download['name']; ?>
                          <input type="hidden" name="product_download[]" value="<?php echo $product_download['download_id']; ?>" />
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="tab-pane" id="tab-attribute">
                <div class="row">
                  <div class="col-sm-12">
                  <div class="fieldgroup pull-right" style="margin: 10px 0;">
                    <button id="qc-load-attribute-template" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Load Product Template" class="btn btn-warning qc-load-attribute-template"><i class="fa fa-arrow-circle-down"></i> Load Product Template</button>
                    <!--<button id="qc-edit-attribute-template" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Edit Template" class="btn btn-info"><i class="fa fa-pencil"></i> Edit Templates</button>-->
                  </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="attribute" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <td class="text-left"><?php echo $entry_attribute; ?></td>
                        <td class="text-left"><?php echo $entry_text; ?></td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                    <?php $attribute_row = 0; ?>
                    <?php foreach ($product_attributes as $product_attribute) { ?>
                    <?php $pa_id_prefix = 'a' . $attribute_row . '_'; ?>
                    <?php $pa_name_prefix = 'product_attribute[' . $attribute_row . ']'; ?>
                    <tr id="attribute-row<?php echo $attribute_row; ?>">
                      <td class="text-left" style="width: 40%;"><input type="text" id="<?php echo $pa_id_prefix; ?>name" name="<?php echo $pa_name_prefix; ?>[name]" value="<?php echo $product_attribute['name']; ?>" placeholder="<?php echo $entry_attribute; ?>" class="form-control" />
                        <input type="hidden" id="<?php echo $pa_id_prefix; ?>attribute_id" name="<?php echo $pa_name_prefix; ?>[attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" /></td>
                      <td class="text-left"><?php foreach ($languages as $language) { ?>
                        <?php $pad_id_prefix = 'a' . $attribute_row . '_l' . $language['language_id'] . '_'; ?>
                        <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                          <textarea id="<?php echo $pad_id_prefix; ?>text" name="<?php echo $pa_name_prefix; ?>[product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="1" placeholder="<?php echo $entry_text; ?>" class="form-control"><?php echo isset($product_attribute['product_attribute_description'][$language['language_id']]) ? $product_attribute['product_attribute_description'][$language['language_id']]['text'] : ''; ?></textarea>
                        </div>
                        <?php } ?></td>
                      <td class="text-left"><button type="button" onclick="$('#attribute-row<?php echo $attribute_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $attribute_row++; ?>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="2"></td>
                        <td class="text-left"><button type="button" onclick="addAttribute();" data-toggle="tooltip" title="<?php echo $button_attribute_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="tab-pane" id="tab-related">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="<?php echo $help_related; ?>"><?php echo $entry_related; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="related" value="" placeholder="<?php echo $entry_related; ?>" id="input-related" class="form-control" />
                    <div id="product-related" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_relateds as $product_related) { ?>
                    <div id="product-related<?php echo $product_related['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_related['name']; ?>
                      <input type="hidden" name="product_related[]" value="<?php echo $product_related['product_id']; ?>" />
                    </div>
                    <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-option">
                <div class="row">
                  <?php $guid = guid(); ?>
                  <div class="oc-widget" data-role="oc-autocomplete-tabs" data-id="<?php echo $guid; ?>">
                    <div class="col-sm-2">
                      <ul class="nav nav-pills nav-stacked" data-role="oc-autocomplete-tabs-menu">
                        <?php $option_row = 0; ?>
                        <?php foreach ($product_options as $product_option) { ?>
                        <?php $option_id = $guid . '_' . $option_row; ?>
                        <li><a href="#<?php echo $option_id; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#<?php echo $option_id; ?>\']').parent().remove(); $('#<?php echo $option_id; ?>').remove(); $('#option a:first').tab('show');"></i> <?php echo $product_option['name']; ?></a></li>
                        <?php $option_row++; ?>
                        <?php } ?>
                        <li>
                          <input type="text" name="option" value="" placeholder="<?php echo $entry_option; ?>" id="input-option" class="form-control" />
                        </li>
                      </ul>
                    </div>
                    <div class="col-sm-10">
                      <div class="tab-content">
                        <?php $option_row = 0; ?>
                        <?php $option_value_row = 0; ?>
                        <?php foreach ($product_options as $product_option) { ?>
                        <?php $option_id = $guid . '_' . $option_row; ?>
                        <?php $po_id_prefix = 'po' . $option_row . '_'; ?>
                        <?php $po_name_prefix = 'product_option[' . $option_row . ']'; ?>

                        <div class="tab-pane" id="<?php echo $option_id; ?>">
                          <input type="hidden" id="<?php echo $po_id_prefix; ?>product_option_id" name="<?php echo $po_name_prefix; ?>[product_option_id]" value="<?php echo $product_option['product_option_id']; ?>" />
                          <input type="hidden" id="<?php echo $po_id_prefix; ?>name" name="<?php echo $po_name_prefix; ?>[name]" value="<?php echo $product_option['name']; ?>" />
                          <input type="hidden" id="<?php echo $po_id_prefix; ?>option_id" name="<?php echo $po_name_prefix; ?>[option_id]" value="<?php echo $product_option['option_id']; ?>" />
                          <input type="hidden" id="<?php echo $po_id_prefix; ?>type" name="<?php echo $po_name_prefix; ?>[type]" value="<?php echo $product_option['type']; ?>" />
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-required<?php echo $option_row; ?>"><?php echo $entry_required; ?></label>
                            <div class="col-sm-8">
                              <select id="<?php echo $po_id_prefix; ?>required" name="<?php echo $po_name_prefix; ?>[required]" id="input-required<?php echo $option_row; ?>" class="form-control">
                                <?php if ($product_option['required']) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-sm-2">
                              <button type="button" name="qc-fill-select-options" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Fill Options</button>
                            </div>
                          </div>
                          <?php if ($product_option['type'] == 'text') { ?>
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'textarea') { ?>
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-10">
                              <textarea name="<?php echo $po_name_prefix; ?>[value]" rows="5" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control"><?php echo $product_option['value']; ?></textarea>
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'file') { ?>
                          <div class="form-group" style="display: none;">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-10">
                              <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'date') { ?>
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-3">
                              <div class="input-group date">
                                <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span></div>
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'time') { ?>
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-10">
                              <div class="input-group time">
                                <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span></div>
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'datetime') { ?>
                          <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                            <div class="col-sm-10">
                              <div class="input-group datetime">
                                <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span></div>
                            </div>
                          </div>
                          <?php } ?>
                          <?php if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') { ?>
                          <div class="table-responsive">
                            <table id="option-value<?php echo $option_row; ?>" class="table table-striped table-bordered table-hover">
                              <thead>
                                <tr>
                                  <td class="text-left"><?php echo $entry_option_value; ?></td>
                                  <td class="text-right"><?php echo $entry_quantity; ?></td>
                                  <td class="text-left"><?php echo $entry_subtract; ?></td>
                                  <td class="text-right"><?php echo $entry_price; ?></td>
                                  <td class="text-right"><?php echo $entry_option_points; ?></td>
                                  <td class="text-right"><?php echo $entry_weight; ?></td>
                                  <td></td>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($product_option['product_option_value'] as $product_option_value) { ?>
                                <?php $pov_id_prefix = 'po' . $option_row . '_v' . $option_value_row . '_'; ?>
                                <?php $pov_name_prefix = 'product_option[' . $option_row . '][product_option_value][' . $option_value_row . ']'; ?>
                                <tr id="option-value-row<?php echo $option_value_row; ?>">
                                  <td class="text-left"><select id="<?php echo $pov_id_prefix; ?>option_value_id" name="<?php echo $pov_name_prefix; ?>[option_value_id]" class="form-control">
                                      <?php if (isset($option_values[$product_option['option_id']])) { ?>
                                      <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                                      <?php if ($option_value['option_value_id'] == $product_option_value['option_value_id']) { ?>
                                      <option value="<?php echo $option_value['option_value_id']; ?>" selected="selected"><?php echo $option_value['name']; ?></option>
                                      <?php } else { ?>
                                      <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                                      <?php } ?>
                                      <?php } ?>
                                      <?php } ?>
                                    </select>
                                    <input type="hidden" id="<?php echo $pov_id_prefix; ?>product_option_value_id" name="<?php echo $pov_name_prefix; ?>[product_option_value_id]" value="<?php echo $product_option_value['product_option_value_id']; ?>" /></td>
                                  <td class="text-right"><input type="text" id="<?php echo $pov_id_prefix; ?>quantity" name="<?php echo $pov_name_prefix; ?>[quantity]" value="<?php echo $product_option_value['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                                  <td class="text-left"><select id="<?php echo $pov_id_prefix; ?>subtract" name="<?php echo $pov_name_prefix; ?>[subtract]" class="form-control">
                                      <?php if ($product_option_value['subtract']) { ?>
                                      <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                      <option value="0"><?php echo $text_no; ?></option>
                                      <?php } else { ?>
                                      <option value="1"><?php echo $text_yes; ?></option>
                                      <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                      <?php } ?>
                                    </select></td>
                                  <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>price_prefix" name="<?php echo $pov_name_prefix; ?>[price_prefix]" class="form-control">
                                      <?php if ($product_option_value['price_prefix'] == '+') { ?>
                                      <option value="+" selected="selected">+</option>
                                      <?php } else { ?>
                                      <option value="+">+</option>
                                      <?php } ?>
                                      <?php if ($product_option_value['price_prefix'] == '-') { ?>
                                      <option value="-" selected="selected">-</option>
                                      <?php } else { ?>
                                      <option value="-">-</option>
                                      <?php } ?>
                                    </select>
                                    <input type="text" id="<?php echo $pov_id_prefix; ?>price" name="<?php echo $pov_name_prefix; ?>[price]" value="<?php echo $product_option_value['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                                  <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>points_prefix" name="<?php echo $pov_name_prefix; ?>[points_prefix]" class="form-control">
                                      <?php if ($product_option_value['points_prefix'] == '+') { ?>
                                      <option value="+" selected="selected">+</option>
                                      <?php } else { ?>
                                      <option value="+">+</option>
                                      <?php } ?>
                                      <?php if ($product_option_value['points_prefix'] == '-') { ?>
                                      <option value="-" selected="selected">-</option>
                                      <?php } else { ?>
                                      <option value="-">-</option>
                                      <?php } ?>
                                    </select>
                                    <input type="text" id="<?php echo $pov_id_prefix; ?>points" name="<?php echo $pov_name_prefix; ?>[points]" value="<?php echo $product_option_value['points']; ?>" placeholder="<?php echo $entry_points; ?>" class="form-control" /></td>
                                  <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>weight_prefix" name="<?php echo $pov_name_prefix; ?>[weight_prefix]" class="form-control">
                                      <?php if ($product_option_value['weight_prefix'] == '+') { ?>
                                      <option value="+" selected="selected">+</option>
                                      <?php } else { ?>
                                      <option value="+">+</option>
                                      <?php } ?>
                                      <?php if ($product_option_value['weight_prefix'] == '-') { ?>
                                      <option value="-" selected="selected">-</option>
                                      <?php } else { ?>
                                      <option value="-">-</option>
                                      <?php } ?>
                                    </select>
                                    <input type="text" id="<?php echo $pov_id_prefix; ?>weight" name="<?php echo $pov_name_prefix; ?>[weight]" value="<?php echo $product_option_value['weight']; ?>" placeholder="<?php echo $entry_weight; ?>" class="form-control" /></td>
                                  <td class="text-left"><button type="button" onclick="$(this).tooltip('destroy');$('#option-value-row<?php echo $option_value_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                </tr>
                                <?php $option_value_row++; ?>
                                <?php } ?>
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="6"></td>
                                  <td class="text-left"><button type="button" onclick="addOptionValue('#form-product, #form-product-customizer', '<?php echo $option_row; ?>');" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                          <select id="option-values<?php echo $option_row; ?>" style="display: none;">
                            <?php if (isset($option_values[$product_option['option_id']])) { ?>
                            <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                            <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                          </select>
                          <?php } ?>
                        </div>
                        <?php $option_row++; ?>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-recurring">
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_recurring; ?></td>
                      <td class="text-left"><?php echo $entry_customer_group; ?></td>
                      <td class="text-left"></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $recurring_row = 0; ?>
                    <?php foreach ($product_recurrings as $product_recurring) { ?>

                    <tr id="recurring-row<?php echo $recurring_row; ?>">
                      <td class="text-left"><select name="product_recurring[<?php echo $recurring_row; ?>][recurring_id]" class="form-control">
                          <?php foreach ($recurrings as $recurring) { ?>
                          <?php if ($recurring['recurring_id'] == $product_recurring['recurring_id']) { ?>
                          <option value="<?php echo $recurring['recurring_id']; ?>" selected="selected"><?php echo $recurring['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $recurring['recurring_id']; ?>"><?php echo $recurring['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                      <td class="text-left"><select name="product_recurring[<?php echo $recurring_row; ?>][customer_group_id]" class="form-control">
                          <?php foreach ($customer_groups as $customer_group) { ?>
                          <?php if ($customer_group['customer_group_id'] == $product_recurring['customer_group_id']) { ?>
                          <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                      <td class="text-left"><button type="button" onclick="$('#recurring-row<?php echo $recurring_row; ?>').remove()" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $recurring_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addRecurring()" data-toggle="tooltip" title="<?php echo $button_recurring_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              </div>
              <div class="tab-pane" id="tab-image">
                <div class="table-responsive">
                  <table id="images" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <td class="text-left"><?php echo $entry_image; ?></td>
                        <td class="text-right"><?php echo $entry_sort_order; ?></td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $image_row = 0; ?>
                      <?php foreach ($product_images as $product_image) { ?>
                      <tr id="image-row<?php echo $image_row; ?>">
                        <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                        <td class="text-right"><input type="text" name="product_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $product_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                        <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                      </tr>
                      <?php $image_row++; ?>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="2"></td>
                        <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="tab-pane" id="tab-reward">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <td class="text-left"><?php echo $entry_customer_group; ?></td>
                          <td class="text-right"><?php echo $entry_reward; ?></td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <tr>
                          <td class="text-left"><?php echo $customer_group['name']; ?></td>
                          <td class="text-right"><input type="text" name="product_reward[<?php echo $customer_group['customer_group_id']; ?>][points]" value="<?php echo isset($product_reward[$customer_group['customer_group_id']]) ? $product_reward[$customer_group['customer_group_id']]['points'] : ''; ?>" class="form-control" /></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
              </div>
              <div class="tab-pane" id="tab-design">
                <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_store; ?></td>
                      <td class="text-left"><?php echo $entry_layout; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left"><?php echo $text_default; ?></td>
                      <td class="text-left"><select name="product_layout[0]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($product_layout[0]) && $product_layout[0] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php foreach ($stores as $store) { ?>
                    <tr>
                      <td class="text-left"><?php echo $store['name']; ?></td>
                      <td class="text-left"><select name="product_layout[<?php echo $store['store_id']; ?>]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($product_layout[$store['store_id']]) && $product_layout[$store['store_id']] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="row">
              <div class="col-lg-12">
                <ul class="nav nav-tabs" id="language">
                  <?php foreach ($languages as $language) { ?>
                  <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php foreach ($languages as $language) { ?>
                  <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                        <?php if (isset($error_name[$language['language_id']])) { ?>
                        <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                      <div class="col-sm-10">
                        <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                      </div>
                    </div>
                    <!-- TODO: Show on demand -->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo 'Meta Title'; //$entry_meta_title; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="product_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                        <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                        <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo 'Meta Description'; //$entry_meta_description; ?></label>
                      <div class="col-sm-10">
                        <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo 'Meta Keywords'; //$entry_meta_keyword; ?></label>
                      <div class="col-sm-10">
                        <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-tag<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_tag; ?>"><?php echo $entry_tag; ?></span></label>
                      <div class="col-sm-10">
                        <input type="text" name="product_description[<?php echo $language['language_id']; ?>][tag]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['tag'] : ''; ?>" placeholder="<?php echo $entry_tag; ?>" id="input-tag<?php echo $language['language_id']; ?>" class="form-control" />
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          </div>
        </form>
      </div>
    </div>
    <!--<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="p2p-import-modal" data-token="<?php echo $token; ?>">-->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="qc-customizer-modal" data-token="<?php echo $token; ?>">
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
                <h3 class="panel-title"><i class="fa fa-magic"></i> Quick Configure Product</h3>
                <button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="well">
                      <div><span><b>This configuration wizard will help walk you through creating or updating your product. Please note that this is a live editor: <span style="color: darkred">changes made in this window are instantly applied</span> to your product.</b></span></div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="well">
                      <form class="form-horizontal" id="form-product-customizer">
                        <div class="panel-group" id="accordion">
                          <div id="qc-customizer-product-questions" class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><a href="#collapse-type" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><b>1. Select Product Type</b></a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="collapse-type">
                              <div class="panel-body">
                                <div class="row">
                                  <fieldset>
                                    <div class="col-sm-12">
                                      <div class="well">
                                        <span>Filling in this short questionnaire will help us enable/disable fields that you may or not need depending on the product type you have selected.</span>
                                      </div>
                                      <hr>
                                    </div>
                                    <div class="col-sm-12">
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label" for="input-name">Choose an item type
                                          <br>
                                          <small style="font-weight: normal; font-size: 9px; color: darkred;">Changing this field will reconfigure your answers below!</small>
                                        </label>
                                        <div class="col-sm-2">
                                          <select id="qc-customizer-product-type" class="form-control">
                                            <option value="inventory" selected="disabled">Inventory Product</option>
                                            <option value="noninventory">Non-Inventory</option>
                                            <option value="downloadable">Downloadable</option>
                                            <option value="service">Service</option>
                                            <option value="resource">Resource</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo 'I purchase this product from a vendor.'; ?></label>
                                        <div class="col-sm-9">
                                          <label class="radio-inline">
                                            <?php if ($shipping) { ?>
                                            <input type="radio" name="has_vendor" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="has_vendor" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                          </label>
                                          <label class="radio-inline">
                                            <?php if (!$shipping) { ?>
                                            <input type="radio" name="has_vendor" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="has_vendor" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo 'I track quantities of this product.'; ?></label>
                                        <div class="col-sm-9">
                                          <label class="radio-inline">
                                            <?php if ($shipping) { ?>
                                            <input type="radio" name="track_quantity" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="track_quantity" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                          </label>
                                          <label class="radio-inline">
                                            <?php if (!$shipping) { ?>
                                            <input type="radio" name="track_quantity" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="track_quantity" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo 'This product has shipping.'; ?></label>
                                        <div class="col-sm-9">
                                          <label class="radio-inline">
                                            <?php if ($shipping) { ?>
                                            <input type="radio" name="shipping" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="shipping" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                          </label>
                                          <label class="radio-inline">
                                            <?php if (!$shipping) { ?>
                                            <input type="radio" name="shipping" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="shipping" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo 'This product has dimensions.'; ?></label>
                                        <div class="col-sm-9">
                                          <label class="radio-inline">
                                            <?php if ($shipping) { ?>
                                            <input type="radio" name="has_dimensions" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="has_dimensions" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                          </label>
                                          <label class="radio-inline">
                                            <?php if (!$shipping) { ?>
                                            <input type="radio" name="has_dimensions" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="has_dimensions" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo 'I rent/lease out this asset.'; ?></label>
                                        <div class="col-sm-9">
                                          <label class="radio-inline">
                                            <?php if ($shipping) { ?>
                                            <input type="radio" name="is_bookable" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="is_bookable" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                          </label>
                                          <label class="radio-inline">
                                            <?php if (!$shipping) { ?>
                                            <input type="radio" name="is_bookable" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="is_bookable" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <div class="col-sm-8 pull-right">
                                          <!-- EDIT -->
                                          <button id="qc-wizard-continue" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Continue" class="btn btn-primary pull-right qc-wizard-continue"><i class="fa fa-arrow-circle-down"></i> Continue</button>
                                          <!-- END -->
                                        </div>
                                      </div>
                                    </div>
                                  </fieldset>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div id="qc-customizer-product-details" class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><a href="#collapse-details" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><b>2. Enter Basic Details</b></a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="collapse-details">
                              <div class="panel-body">
                                <div class="row">
                                  <fieldset>
                                    <div class="col-sm-12">
                                      <legend></legend>
                                    </div>
                                    <div class="col-sm-12">
                                      <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="input-model"><?php echo $entry_model; ?></label>
                                        <div class="col-sm-10">
                                          <input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
                                          <?php if ($error_model) { ?>
                                          <div class="text-danger"><?php echo $error_model; ?></div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                      <?php foreach ($languages as $language) { ?>
                                      <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                                        <div class="form-group required">
                                          <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                                          <div class="col-sm-10">
                                            <input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                                            <?php if (isset($error_name[$language['language_id']])) { ?>
                                            <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                                            <?php } ?>
                                          </div>
                                        </div>
                                        <?php /*
                                          <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                                        <div class="col-sm-10">
                                          <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                                        </div>
                                      </div>
                                      */ ?>
                                      <!-- TODO: Show on demand -->
                                      <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo 'Meta Title'; //$entry_meta_title; ?></label>
                                        <div class="col-sm-10">
                                          <input type="text" name="product_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                                          <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                                          <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-manufacturer"><span data-toggle="tooltip" title="<?php echo $help_manufacturer; ?>"><?php echo $entry_manufacturer; ?></span></label>
                                      <div class="col-sm-10">
                                        <input type="text" name="manufacturer" value="<?php echo $manufacturer ?>" placeholder="<?php echo $entry_manufacturer; ?>" id="input-manufacturer" class="form-control" />
                                        <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
                                      <div class="col-sm-10">
                                        <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                                        <!--<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                                        <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                                        <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>-->
                                        <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-price"><?php echo $entry_price; ?></label>
                                      <div class="col-sm-10">
                                        <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-points"><span data-toggle="tooltip" title="<?php echo $help_points; ?>"><?php echo $entry_points; ?></span></label>
                                      <div class="col-sm-10">
                                        <input type="text" name="points" value="<?php echo $points; ?>" placeholder="<?php echo $entry_points; ?>" id="input-points" class="form-control" />
                                      </div>
                                    </div>

                                    <fieldset id="qc-customizer-product-stock">
                                      <hr>
                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                                        <div class="col-sm-5 col-lg-3">
                                          <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
                                        </div>
                                        <label class="col-sm-2 control-label" for="input-minimum"><span data-toggle="tooltip" title="<?php echo $help_minimum; ?>"><?php echo $entry_minimum; ?></span></label>
                                        <div class="col-sm-5 col-lg-3">
                                          <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="<?php echo $entry_minimum; ?>" id="input-minimum" class="form-control" />
                                        </div>
                                      </div>

                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-subtract"><?php echo $entry_subtract; ?></label>
                                        <div class="col-sm-5 col-lg-3">
                                          <select name="subtract" id="input-subtract" class="form-control">
                                            <?php if ($subtract) { ?>
                                            <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                            <option value="0"><?php echo $text_no; ?></option>
                                            <?php } else { ?>
                                            <option value="1"><?php echo $text_yes; ?></option>
                                            <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                            <?php } ?>
                                          </select>
                                        </div>
                                        <label class="col-sm-2 control-label" for="input-stock-status"><span data-toggle="tooltip" title="<?php echo $help_stock_status; ?>"><?php echo $entry_stock_status; ?></span></label>
                                        <div class="col-sm-5 col-lg-3">
                                          <select name="stock_status_id" id="input-stock-status" class="form-control">
                                            <?php foreach ($stock_statuses as $stock_status) { ?>
                                            <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                                            <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                          </select>
                                        </div>
                                      </div>
                                    </fieldset>

                                    <fieldset id="qc-customizer-product-dimensions">
                                      <hr>
                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-length"><?php echo 'Dimensions <br><small>(L x W x H)</small>'; //$entry_dimension; ?></label><!-- TODO: HTML allowed for lang stuff -->
                                        <div class="col-sm-10">
                                          <div class="row">
                                            <div class="col-sm-4">
                                              <input type="text" name="length" value="<?php echo $length; ?>" placeholder="<?php echo $entry_length; ?>" id="input-length" class="form-control" />
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-length-class"><?php echo $entry_length_class; ?></label>
                                        <div class="col-sm-10">
                                          <select name="length_class_id" id="input-length-class" class="form-control">
                                            <?php foreach ($length_classes as $length_class) { ?>
                                            <?php if ($length_class['length_class_id'] == $length_class_id) { ?>
                                            <option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-weight"><?php echo $entry_weight; ?></label>
                                        <div class="col-sm-10">
                                          <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="<?php echo $entry_weight; ?>" id="input-weight" class="form-control" />
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-weight-class"><?php echo $entry_weight_class; ?></label>
                                        <div class="col-sm-10">
                                          <select name="weight_class_id" id="input-weight-class" class="form-control">
                                            <?php foreach ($weight_classes as $weight_class) { ?>
                                            <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
                                            <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                          </select>
                                        </div>
                                      </div>
                                    </fieldset>
                                    <div class="form-group">
                                      <div class="col-sm-8 pull-right">
                                        <!-- EDIT -->
                                        <button id="qc-wizard-continue" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Continue" class="btn btn-primary pull-right qc-wizard-continue"><i class="fa fa-arrow-circle-down"></i> Continue</button>
                                        <!-- END -->
                                      </div>
                                    </div>
                                </div>
                                </fieldset>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="qc-customizer-product-specs" class="panel panel-default">
                          <div class="panel-heading">
                            <h4 class="panel-title"><a href="#collapse-options" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><b>3. Product Specifications</b></a></h4>
                          </div>
                          <div class="panel-collapse collapse" id="collapse-options">
                            <div class="panel-body">
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="fieldgroup pull-right" style="margin: 10px 0;">
                                    <button id="qc-load-attribute-template" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Load Product Template" class="btn btn-warning qc-load-attribute-template"><i class="fa fa-arrow-circle-down"></i> Load Product Template</button>
                                  </div>
                                </div>
                              </div>
                              <div id="qc-customizer-attributes" class="table-responsive">
                                <table id="attribute" class="table table-striped table-bordered table-hover">
                                  <thead>
                                  <tr>
                                    <td class="text-left"><?php echo $entry_attribute; ?></td>
                                    <td class="text-left"><?php echo $entry_text; ?></td>
                                    <td></td>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php $attribute_row = 0; ?>
                                  <?php foreach ($product_attributes as $product_attribute) { ?>
                                  <?php $pa_id_prefix = 'a' . $attribute_row . '_'; ?>
                                  <?php $pa_name_prefix = 'product_attribute[' . $attribute_row . ']'; ?>
                                  <tr id="attribute-row<?php echo $attribute_row; ?>">
                                    <td class="text-left" style="width: 40%;"><input type="text" id="<?php echo $pa_id_prefix; ?>name" name="<?php echo $pa_name_prefix; ?>[name]" value="<?php echo $product_attribute['name']; ?>" placeholder="<?php echo $entry_attribute; ?>" class="form-control" />
                                      <input type="hidden" id="<?php echo $pa_id_prefix; ?>attribute_id" name="<?php echo $pa_name_prefix; ?>[attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" /></td>
                                    <td class="text-left"><?php foreach ($languages as $language) { ?>
                                      <?php $pad_id_prefix = 'a' . $attribute_row . '_l' . $language['language_id'] . '_'; ?>
                                      <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                                        <textarea id="<?php echo $pad_id_prefix; ?>text" name="<?php echo $pa_name_prefix; ?>[product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="1" placeholder="<?php echo $entry_text; ?>" class="form-control"><?php echo isset($product_attribute['product_attribute_description'][$language['language_id']]) ? $product_attribute['product_attribute_description'][$language['language_id']]['text'] : ''; ?></textarea>
                                      </div>
                                      <?php } ?></td>
                                    <td class="text-left"><button type="button" onclick="$('#attribute-row<?php echo $attribute_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                  </tr>
                                  <?php $attribute_row++; ?>
                                  <?php } ?>
                                  </tbody>
                                  <tfoot>
                                  <tr>
                                    <td colspan="2"></td>
                                    <td class="text-left"><button type="button" onclick="addAttribute();" data-toggle="tooltip" title="<?php echo $button_attribute_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                  </tr>
                                  </tfoot>
                                </table>
                              </div>
                              <div class="row">
                                <?php $guid = guid(); ?>
                                <div id="qc-customizer-options" class="oc-widget" data-role="oc-autocomplete-tabs" data-id="<?php echo $guid; ?>">
                                  <div class="col-sm-2">
                                    <ul class="nav nav-pills nav-stacked" data-role="oc-autocomplete-tabs-menu">
                                      <?php $option_row = 0; ?>
                                      <?php foreach ($product_options as $product_option) { ?>
                                      <?php $option_id = $guid . '_' . $option_row; ?>
                                      <li><a href="#<?php echo $option_id; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#<?php echo $option_id; ?>\']').parent().remove(); $('#<?php echo $option_id; ?>').remove(); $('#option a:first').tab('show');"></i> <?php echo $product_option['name']; ?></a></li>
                                      <?php $option_row++; ?>
                                      <?php } ?>
                                      <li>
                                        <input type="text" name="option" value="" placeholder="<?php echo $entry_option; ?>" id="input-option" class="form-control" />
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="col-sm-10">
                                    <div class="tab-content">
                                      <?php $option_row = 0; ?>
                                      <?php $option_value_row = 0; ?>
                                      <?php foreach ($product_options as $product_option) { ?>
                                      <?php $option_id = $guid . '_' . $option_row; ?>
                                      <?php $po_id_prefix = 'po' . $option_row . '_'; ?>
                                      <?php $po_name_prefix = 'product_option[' . $option_row . ']'; ?>

                                      <div class="tab-pane" id="<?php echo $option_id; ?>">
                                        <input type="hidden" id="<?php echo $po_id_prefix; ?>product_option_id" name="<?php echo $po_name_prefix; ?>[product_option_id]" value="<?php echo $product_option['product_option_id']; ?>" />
                                        <input type="hidden" id="<?php echo $po_id_prefix; ?>name" name="<?php echo $po_name_prefix; ?>[name]" value="<?php echo $product_option['name']; ?>" />
                                        <input type="hidden" id="<?php echo $po_id_prefix; ?>option_id" name="<?php echo $po_name_prefix; ?>[option_id]" value="<?php echo $product_option['option_id']; ?>" />
                                        <input type="hidden" id="<?php echo $po_id_prefix; ?>type" name="<?php echo $po_name_prefix; ?>[type]" value="<?php echo $product_option['type']; ?>" />
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-required<?php echo $option_row; ?>"><?php echo $entry_required; ?></label>
                                          <div class="col-sm-8">
                                            <select id="<?php echo $po_id_prefix; ?>required" name="<?php echo $po_name_prefix; ?>[required]" id="input-required<?php echo $option_row; ?>" class="form-control">
                                              <?php if ($product_option['required']) { ?>
                                              <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                              <option value="0"><?php echo $text_no; ?></option>
                                              <?php } else { ?>
                                              <option value="1"><?php echo $text_yes; ?></option>
                                              <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                              <?php } ?>
                                            </select>
                                          </div>
                                          <div class="col-sm-2">
                                            <button type="button" name="qc-fill-select-options" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Fill Options</button>
                                          </div>
                                        </div>
                                        <?php if ($product_option['type'] == 'text') { ?>
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-10">
                                            <input type="text" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'textarea') { ?>
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-10">
                                            <textarea name="<?php echo $po_name_prefix; ?>[value]" rows="5" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control"><?php echo $product_option['value']; ?></textarea>
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'file') { ?>
                                        <div class="form-group" style="display: none;">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-10">
                                            <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'date') { ?>
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-3">
                                            <div class="input-group date">
                                              <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                              <span class="input-group-btn">
                                              <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                              </span></div>
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'time') { ?>
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-10">
                                            <div class="input-group time">
                                              <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                              <span class="input-group-btn">
                                              <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                              </span></div>
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'datetime') { ?>
                                        <div class="form-group">
                                          <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                                          <div class="col-sm-10">
                                            <div class="input-group datetime">
                                              <input type="text" id="<?php echo $po_id_prefix; ?>value" name="<?php echo $po_name_prefix; ?>[value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                                              <span class="input-group-btn">
                                              <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                              </span></div>
                                          </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') { ?>
                                        <div class="table-responsive">
                                          <table id="option-value<?php echo $option_row; ?>" class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                              <td class="text-left"><?php echo $entry_option_value; ?></td>
                                              <td class="text-right"><?php echo $entry_quantity; ?></td>
                                              <td class="text-left"><?php echo $entry_subtract; ?></td>
                                              <td class="text-right"><?php echo $entry_price; ?></td>
                                              <td class="text-right"><?php echo $entry_option_points; ?></td>
                                              <td class="text-right"><?php echo $entry_weight; ?></td>
                                              <td></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($product_option['product_option_value'] as $product_option_value) { ?>
                                            <?php $pov_id_prefix = 'po' . $option_row . '_v' . $option_value_row . '_'; ?>
                                            <?php $pov_name_prefix = 'product_option[' . $option_row . '][product_option_value][' . $option_value_row . ']'; ?>
                                            <tr id="option-value-row<?php echo $option_value_row; ?>">
                                              <td class="text-left"><select id="<?php echo $pov_id_prefix; ?>option_value_id" name="<?php echo $pov_name_prefix; ?>[option_value_id]" class="form-control">
                                                  <?php if (isset($option_values[$product_option['option_id']])) { ?>
                                                  <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                                                  <?php if ($option_value['option_value_id'] == $product_option_value['option_value_id']) { ?>
                                                  <option value="<?php echo $option_value['option_value_id']; ?>" selected="selected"><?php echo $option_value['name']; ?></option>
                                                  <?php } else { ?>
                                                  <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                                                  <?php } ?>
                                                  <?php } ?>
                                                  <?php } ?>
                                                </select>
                                                <input type="hidden" id="<?php echo $pov_id_prefix; ?>product_option_value_id" name="<?php echo $pov_name_prefix; ?>[product_option_value_id]" value="<?php echo $product_option_value['product_option_value_id']; ?>" /></td>
                                              <td class="text-right"><input type="text" id="<?php echo $pov_id_prefix; ?>quantity" name="<?php echo $pov_name_prefix; ?>[quantity]" value="<?php echo $product_option_value['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                                              <td class="text-left"><select id="<?php echo $pov_id_prefix; ?>subtract" name="<?php echo $pov_name_prefix; ?>[subtract]" class="form-control">
                                                  <?php if ($product_option_value['subtract']) { ?>
                                                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                                  <option value="0"><?php echo $text_no; ?></option>
                                                  <?php } else { ?>
                                                  <option value="1"><?php echo $text_yes; ?></option>
                                                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                                  <?php } ?>
                                                </select></td>
                                              <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>price_prefix" name="<?php echo $pov_name_prefix; ?>[price_prefix]" class="form-control">
                                                  <?php if ($product_option_value['price_prefix'] == '+') { ?>
                                                  <option value="+" selected="selected">+</option>
                                                  <?php } else { ?>
                                                  <option value="+">+</option>
                                                  <?php } ?>
                                                  <?php if ($product_option_value['price_prefix'] == '-') { ?>
                                                  <option value="-" selected="selected">-</option>
                                                  <?php } else { ?>
                                                  <option value="-">-</option>
                                                  <?php } ?>
                                                </select>
                                                <input type="text" id="<?php echo $pov_id_prefix; ?>price" name="<?php echo $pov_name_prefix; ?>[price]" value="<?php echo $product_option_value['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                                              <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>points_prefix" name="<?php echo $pov_name_prefix; ?>[points_prefix]" class="form-control">
                                                  <?php if ($product_option_value['points_prefix'] == '+') { ?>
                                                  <option value="+" selected="selected">+</option>
                                                  <?php } else { ?>
                                                  <option value="+">+</option>
                                                  <?php } ?>
                                                  <?php if ($product_option_value['points_prefix'] == '-') { ?>
                                                  <option value="-" selected="selected">-</option>
                                                  <?php } else { ?>
                                                  <option value="-">-</option>
                                                  <?php } ?>
                                                </select>
                                                <input type="text" id="<?php echo $pov_id_prefix; ?>points" name="<?php echo $pov_name_prefix; ?>[points]" value="<?php echo $product_option_value['points']; ?>" placeholder="<?php echo $entry_points; ?>" class="form-control" /></td>
                                              <td class="text-right"><select id="<?php echo $pov_id_prefix; ?>weight_prefix" name="<?php echo $pov_name_prefix; ?>[weight_prefix]" class="form-control">
                                                  <?php if ($product_option_value['weight_prefix'] == '+') { ?>
                                                  <option value="+" selected="selected">+</option>
                                                  <?php } else { ?>
                                                  <option value="+">+</option>
                                                  <?php } ?>
                                                  <?php if ($product_option_value['weight_prefix'] == '-') { ?>
                                                  <option value="-" selected="selected">-</option>
                                                  <?php } else { ?>
                                                  <option value="-">-</option>
                                                  <?php } ?>
                                                </select>
                                                <input type="text" id="<?php echo $pov_id_prefix; ?>weight" name="<?php echo $pov_name_prefix; ?>[weight]" value="<?php echo $product_option_value['weight']; ?>" placeholder="<?php echo $entry_weight; ?>" class="form-control" /></td>
                                              <td class="text-left"><button type="button" onclick="$(this).tooltip('destroy');$('#option-value-row<?php echo $option_value_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                            </tr>
                                            <?php $option_value_row++; ?>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                              <td colspan="6"></td>
                                              <td class="text-left"><button type="button" onclick="addOptionValue('#form-product, #form-product-customizer', '<?php echo $option_row; ?>');" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                            </tr>
                                            </tfoot>
                                          </table>
                                        </div>
                                        <select id="option-values<?php echo $option_row; ?>" style="display: none;">
                                          <?php if (isset($option_values[$product_option['option_id']])) { ?>
                                          <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                                          <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                                          <?php } ?>
                                          <?php } ?>
                                        </select>
                                        <?php } ?>
                                      </div>
                                      <?php $option_row++; ?>
                                      <?php } ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <fieldset>
                                  <div class="col-sm-12">
                                    <div class="form-group">
                                      <div class="col-sm-8 pull-right">
                                        <!-- EDIT -->
                                        <button id="qc-wizard-continue" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Continue" class="btn btn-primary pull-right qc-wizard-continue"><i class="fa fa-arrow-circle-down"></i> Continue</button>
                                        <!-- END -->
                                      </div>
                                    </div>
                                  </div>
                                </fieldset>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="qc-customizer-product-pricing" class="panel panel-default">
                          <div class="panel-heading">
                            <h4 class="panel-title"><a href="#collapse-pricing" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><b>4. Customize Pricing</b></a></h4>
                          </div>
                          <div class="panel-collapse collapse" id="collapse-pricing">
                            <div class="panel-body">
                              <div class="row">
                                <fieldset>
                                  <div class="col-sm-12">
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-price"><?php echo 'Retail Price'; $entry_price; ?></label>
                                      <div class="col-sm-10">
                                        <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" disabled="disabled" />
                                      </div>
                                    </div>
                                    <div class="form-group" id="edit-discount">
                                      <label class="col-sm-2 control-label"><?php echo 'Discounts'; //$entry_store; ?></label>
                                      <div class="table-responsive col-sm-10">
                                        <table id="discount" class="table table-striped table-bordered table-hover">
                                          <thead>
                                          <tr>
                                            <td class="text-left"><?php echo $entry_customer_group; ?></td>
                                            <td class="text-right"><?php echo $entry_quantity; ?></td>
                                            <td class="text-right"><?php echo $entry_priority; ?></td>
                                            <td class="text-right"><?php echo $entry_price; ?></td>
                                            <td></td>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <?php $discount_row = 0; ?>
                                          <?php foreach ($product_discounts as $product_discount) { ?>
                                          <?php $pd_id_prefix = 'd' . $discount_row . '_'; ?>
                                          <?php $pd_name_prefix = 'product_discount[' . $discount_row . ']'; ?>
                                          <tr id="discount-row<?php echo $discount_row; ?>">
                                            <td class="text-left"><select id="<?php echo $pd_id_prefix; ?>customer_group_id" name="<?php echo $pd_name_prefix; ?>[customer_group_id]" class="form-control">
                                                <?php foreach ($customer_groups as $customer_group) { ?>
                                                <?php if ($customer_group['customer_group_id'] == $product_discount['customer_group_id']) { ?>
                                                <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                              </select></td>
                                            <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>quantity" name="<?php echo $pd_name_prefix; ?>[quantity]" value="<?php echo $product_discount['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                                            <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>priority" name="<?php echo $pd_name_prefix; ?>[priority]" value="<?php echo $product_discount['priority']; ?>" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>
                                            <td class="text-right"><input type="text" id="<?php echo $pd_id_prefix; ?>price" name="<?php echo $pd_name_prefix; ?>[price]" value="<?php echo $product_discount['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                                            <td class="text-left"><button type="button" onclick="$('#discount-row<?php echo $discount_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                          </tr>
                                          <?php $discount_row++; ?>
                                          <?php } ?>
                                          </tbody>
                                          <tfoot>
                                          <tr>
                                            <td colspan="4"></td>
                                            <td class="text-left"><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="<?php echo $button_discount_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                          </tr>
                                          </tfoot>
                                        </table>
                                      </div>
                                    </div>
                                    <div class="form-group" id="edit-special">
                                      <label class="col-sm-2 control-label"><?php echo 'Specials'; //$entry_store; ?></label>
                                      <div class="table-responsive col-sm-10">
                                        <table id="special" class="table table-striped table-bordered table-hover">
                                          <thead>
                                          <tr>
                                            <td class="text-left"><?php echo $entry_customer_group; ?></td>
                                            <td class="text-right"><?php echo $entry_priority; ?></td>
                                            <td class="text-right"><?php echo $entry_price; ?></td>
                                            <td></td>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <?php $special_row = 0; ?>
                                          <?php foreach ($product_specials as $product_special) { ?>
                                          <?php $ps_id_prefix = 's' . $special_row . '_'; ?>
                                          <?php $ps_name_prefix = 'product_special[' . $special_row . ']'; ?>
                                          <tr id="special-row<?php echo $special_row; ?>">
                                            <td class="text-left"><select id="<?php echo $ps_id_prefix; ?>customer_group_id" name="<?php echo $ps_name_prefix; ?>[customer_group_id]" class="form-control">
                                                <?php foreach ($customer_groups as $customer_group) { ?>
                                                <?php if ($customer_group['customer_group_id'] == $product_special['customer_group_id']) { ?>
                                                <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                              </select></td>
                                            <td class="text-right"><input type="text" id="<?php echo $ps_id_prefix; ?>priority" name="<?php echo $ps_name_prefix; ?>[priority]" value="<?php echo $product_special['priority']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                                            <td class="text-right"><input type="text" id="<?php echo $ps_id_prefix; ?>price" name="<?php echo $ps_name_prefix; ?>[price]" value="<?php echo $product_special['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                                            <td class="text-left"><button type="button" onclick="$('#special-row<?php echo $special_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                          </tr>
                                          <?php $special_row++; ?>
                                          <?php } ?>
                                          </tbody>
                                          <tfoot>
                                          <tr>
                                            <td colspan="3"></td>
                                            <td class="text-left"><button type="button" onclick="addSpecial();" data-toggle="tooltip" title="<?php echo $button_special_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                          </tr>
                                          </tfoot>
                                        </table>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <div class="col-sm-8 pull-right">
                                        <!-- EDIT -->
                                        <button id="qc-wizard-continue" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Continue" class="btn btn-primary pull-right qc-wizard-continue"><i class="fa fa-arrow-circle-down"></i> Continue</button>
                                        <!-- END -->
                                      </div>
                                    </div>
                                  </div>
                                </fieldset>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="qc-customizer-product-assignment" class="panel panel-default">
                          <div class="panel-heading">
                            <h4 class="panel-title"><a href="#collapse-assignment" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><b>5. Assign Product to Store Areas</b></a></h4>
                          </div>
                          <div class="panel-collapse collapse" id="collapse-assignment">
                            <div class="panel-body">
                              <div class="row">
                                <fieldset>
                                  <div class="col-sm-12">
                                    <legend></legend>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                                      <div class="col-sm-10">
                                        <div class="well well-sm" style="height: 150px; overflow: auto;">
                                          <div class="checkbox">
                                            <label>
                                              <?php if (in_array(0, $product_store)) { ?>
                                              <input type="checkbox" name="product_store[]" value="0" checked="checked" />
                                              <?php echo $text_default; ?>
                                              <?php } else { ?>
                                              <input type="checkbox" name="product_store[]" value="0" />
                                              <?php echo $text_default; ?>
                                              <?php } ?>
                                            </label>
                                          </div>
                                          <?php foreach ($stores as $store) { ?>
                                          <div class="checkbox">
                                            <label>
                                              <?php if (in_array($store['store_id'], $product_store)) { ?>
                                              <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                                              <?php echo $store['name']; ?>
                                              <?php } else { ?>
                                              <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                                              <?php echo $store['name']; ?>
                                              <?php } ?>
                                            </label>
                                          </div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                                      <div class="col-sm-10">
                                        <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                                        <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                                          <?php foreach ($product_categories as $product_category) { ?>
                                          <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                                            <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                                          </div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </fieldset>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                      <fieldset>
                        <!--<div class="col-sm-2" for="">
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
                        </div>-->
                        <!--<div class="col-sm-4 pull-right" for="">
                            <div class="form-group pull-right">
                                <div class="col-sm-6">
                                    <button id="qc-peer-import-all" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import Selected" class="btn btn-success pull-right"><i class="fa fa-check"></i> Apply Changes</button>
                                </div>
                            </div>
                        </div>-->
                      </fieldset>
                    </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 text-right">
                  <button id="button-close-qc-customizer" class="btn btn-primary" data-action="close" data-loading-text="Loading..." type="button">Done</button>
                </div>
              </div>
              <div style="clear: both"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="qc-load-attributes-modal">
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
              <h3 class="panel-title"><i class="fa fa-pencil"></i> Load Attributes from Template</h3>
              <button style="float: right" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            </div>
            <div class="panel-body">
              <?php echo $attribute_template_template_list; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({height: 300});
<?php } ?>
//--></script>
  <script type="text/javascript"><!--
// Manufacturer
$('input[name=\'manufacturer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					manufacturer_id: 0,
					name: '<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['manufacturer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'manufacturer\']').val(item['label']);
		$('input[name=\'manufacturer_id\']').val(item['value']);
	}
});

var forms = $('#form-product, #form-product-customizer');
// Category
$('input[name=\'category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'category\']').val('');

    //$('#product-category' + item['value']).remove();
    forms.find('#product-category' + item['value']).remove();

    //$('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
    forms.find('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
	}
});

//$('#product-category').delegate('.fa-minus-circle', 'click', function() {
forms.find('#product-category').delegate('.fa-minus-circle', 'click', function() {
	var id = $(this).parent().attr('id');
  forms.find('#' + id).each(function () {
    $(this).remove();
  });
	//$(this).parent().remove();
});

// Filter
$('input[name=\'filter\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/filter/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['filter_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter\']').val('');

		$('#product-filter' + item['value']).remove();

		$('#product-filter').append('<div id="product-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_filter[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-filter').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Downloads
$('input[name=\'download\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/download/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['download_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'download\']').val('');

		$('#product-download' + item['value']).remove();

		$('#product-download').append('<div id="product-download' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_download[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-download').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Related
$('input[name=\'related\']').autocomplete({
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
		$('input[name=\'related\']').val('');

		$('#product-related' + item['value']).remove();

		$('#product-related').append('<div id="product-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_related[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-related').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
//--></script>
  <script type="text/javascript">
var attribute_row = <?php echo $attribute_row; ?>;

function addAttribute(attribute) {
  var idPrefix = 'a' + attribute_row + '_',
      namePrefix = 'product_attribute[' + attribute_row + ']',
      id = '', name = '', text = '';

  if (attribute) {
    // TODO: I'm not sure if this actually does anything
    var id = attribute.hasOwnProperty('id') ? attribute.id : '',
        name = attribute.hasOwnProperty('name') ? attribute.name : '',
        text = attribute.hasOwnProperty('text') ? attribute.text : '';
  }

  html  = '<tr id="attribute-row' + attribute_row + '">';
	html += '  <td class="text-left" style="width: 20%;"><input type="text" id="' + idPrefix + 'name" name="' + namePrefix + '[name]" value="' + name + '" placeholder="<?php echo $entry_attribute; ?>" class="form-control" /><input type="hidden" id="' + idPrefix + 'attribute_id" name="' + namePrefix + '[attribute_id]" value="' + id + '" /></td>';
	html += '  <td class="text-left">';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><textarea id="' + idPrefix + '<?php echo $language['language_id']; ?>_text" name="' + namePrefix + '[product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="1" placeholder="<?php echo $entry_text; ?>" class="form-control">' + text + '</textarea></div>';
    <?php } ?>
	html += '  </td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#attribute-row' + attribute_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

	$('#attribute tbody').append(html);

	attributeautocomplete(attribute_row);

	attribute_row++;
}

function attributeautocomplete(attribute_row) {
	$('input[name=\'product_attribute[' + attribute_row + '][name]\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/attribute/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							category: item.attribute_group,
							label: item.name,
							value: item.attribute_id
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'product_attribute[' + attribute_row + '][name]\']').val(item['label']);
			$('input[name=\'product_attribute[' + attribute_row + '][attribute_id]\']').val(item['value']);
		}
	});
}

$('#attribute tbody tr').each(function(index, element) {
	attributeautocomplete(index);
});
</script>
  <script type="text/javascript">
var option_row = <?php echo $option_row; ?>;

function addOption(target, item) {
  var widgetId = $(target).attr('data-id'),
      optionId = widgetId + '_' + option_row,
      menu;

  menu = $(target).find('[data-role=oc-autocomplete-tabs-menu]');

  var idPrefix = 'po' + option_row + '_',
      namePrefix = 'product_option[' + option_row + ']';

  html  = '<div class="tab-pane" id="' + optionId + '">';
  html += '	<input type="hidden" id="' + idPrefix + 'product_option_id" name="' + namePrefix + '[product_option_id]" value="" />';
  html += '	<input type="hidden" id="' + idPrefix + 'name" name="' + namePrefix + '[name]" value="' + item['label'] + '" />';
  html += '	<input type="hidden" id="' + idPrefix + 'option_id" name="' + namePrefix + '[option_id]" value="' + item['value'] + '" />';
  html += '	<input type="hidden" id="' + idPrefix + 'type" name="' + namePrefix + '[type]" value="' + item['type'] + '" />';

  html += '	<div class="form-group">';
  html += '	  <label class="col-sm-2 control-label" for="input-required' + option_row + '"><?php echo $entry_required; ?></label>';
  html += '	  <div class="col-sm-8"><select name="' + namePrefix + '[required]" id="input-required' + option_row + '" class="form-control">';
  html += '	      <option value="1"><?php echo $text_yes; ?></option>';
  html += '	      <option value="0"><?php echo $text_no; ?></option>';
  html += '	  </select></div>';
  html += '	  <div class="col-sm-2">';
  html += '		<button type="button" name="qc-fill-select-options" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Fill Options</button>';
  html += '	  </div>';
  html += '	</div>';

  if (item['type'] == 'text') {
    html += '	<div class="form-group">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-10"><input type="text" name="' + namePrefix + '[value]" value="" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control" /></div>';
    html += '	</div>';
  }

  if (item['type'] == 'textarea') {
    html += '	<div class="form-group">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-10"><textarea name="' + namePrefix + '[value]" rows="5" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control"></textarea></div>';
    html += '	</div>';
  }

  if (item['type'] == 'file') {
    html += '	<div class="form-group" style="display: none;">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-10"><input type="text" name="' + namePrefix + '[value]" value="" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control" /></div>';
    html += '	</div>';
  }

  if (item['type'] == 'date') {
    html += '	<div class="form-group">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-3"><div class="input-group date"><input type="text" name="' + namePrefix + '[value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
    html += '	</div>';
  }

  if (item['type'] == 'time') {
    html += '	<div class="form-group">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-10"><div class="input-group time"><input type="text" name="' + namePrefix + '[value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="HH:mm" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
    html += '	</div>';
  }

  if (item['type'] == 'datetime') {
    html += '	<div class="form-group">';
    html += '	  <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
    html += '	  <div class="col-sm-10"><div class="input-group datetime"><input type="text" name="' + namePrefix + '[value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
    html += '	</div>';
  }

  if (item['type'] == 'select' || item['type'] == 'radio' || item['type'] == 'checkbox' || item['type'] == 'image') {
    html += '<div class="table-responsive">';
    html += '  <table id="option-value' + option_row + '" class="table table-striped table-bordered table-hover">';
    html += '  	 <thead>';
    html += '      <tr>';
    html += '        <td class="text-left"><?php echo $entry_option_value; ?></td>';
    html += '        <td class="text-right"><?php echo $entry_quantity; ?></td>';
    html += '        <td class="text-left"><?php echo $entry_subtract; ?></td>';
    html += '        <td class="text-right"><?php echo $entry_price; ?></td>';
    html += '        <td class="text-right"><?php echo $entry_option_points; ?></td>';
    html += '        <td class="text-right"><?php echo $entry_weight; ?></td>';
    html += '        <td></td>';
    html += '      </tr>';
    html += '  	 </thead>';
    html += '  	 <tbody>';
    html += '    </tbody>';
    html += '    <tfoot>';
    html += '      <tr>';
    html += '        <td colspan="6"></td>';

    var selector = '#form-product, #form-product-customizer';

    html += '        <td class="text-left"><button type="button" onclick="addOptionValue(\'' + selector + '\' , ' + option_row + ');" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>';
    html += '      </tr>';
    html += '    </tfoot>';
    html += '  </table>';
    html += '</div>';

    html += '  <select id="option-values' + option_row + '" style="display: none;">';

    for (i = 0; i < item['option_value'].length; i++) {
      html += '  <option value="' + item['option_value'][i]['option_value_id'] + '">' + item['option_value'][i]['name'] + '</option>';
    }

    html += '  </select>';
    html += '</div>';
  }

  $(target).find('.tab-content').append(html);
  menu.children('li:last-child').before('<li><a href="#' + optionId + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$(\'a[href=\\\'#' + optionId + '\\\']\').parent().remove(); $(\'#' + optionId + '\').remove(); $(\'[data-id=' + widgetId + '] [data-role=oc-autocomplete-tabs-menu] a:first\').tab(\'show\')"></i> ' + item['label'] + '</li>');
  menu.find('a[href=\'#' + optionId + '\']').tab('show');

  $('.date').datetimepicker({
    pickTime: false
  });

  $('.time').datetimepicker({
    pickDate: false
  });

  $('.datetime').datetimepicker({
    pickDate: true,
    pickTime: true
  });

  //option_row++;
};

var option_value_row = <?php echo $option_value_row; ?>;

function addOptionValue(target, option_row) {
  target = target || '';

  selectors = target.split(',');
  if (typeof selectors !== 'string') {
    for (i = 0; i < selectors.length; i++) {
      var clean = selectors[i].trim();
      selectors[i] = clean;
    }
  } else {
    selectors = [target];
  }

  var idPrefix = 'po' + option_row + '_v' + option_value_row + '_',
      namePrefix = 'product_option[' + option_row + '][product_option_value][' + option_value_row + ']';

  html  = '<tr id="option-value-row' + option_value_row + '">';
  html += '  <td class="text-left"><select id="' + idPrefix + 'option_value_id" name="' + namePrefix + '[option_value_id]" class="form-control">';
  html += $('#option-values' + option_row).html();
  html += '  </select><input type="hidden" id="' + idPrefix + 'product_option_value_id" name="' + namePrefix + '[product_option_value_id]" value="" /></td>';
  html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'quantity" name="' + namePrefix + '[quantity]" value="" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><select id="' + idPrefix + 'subtract" name="' + namePrefix + '[subtract]" class="form-control">';
  html += '    <option value="0" selected="selected"><?php echo $text_no; ?></option>';
  html += '    <option value="1"><?php echo $text_yes; ?></option>';
  html += '  </select></td>';
  html += '  <td class="text-right"><select id="' + idPrefix + 'price_prefix" name="' + namePrefix + '[price_prefix]" class="form-control">';
  html += '    <option value="+">+</option>';
  html += '    <option value="-">-</option>';
  html += '  </select>';
  html += '  <input type="text" id="' + idPrefix + 'price" name="' + namePrefix + '[price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><select id="' + idPrefix + 'points_prefix" name="' + namePrefix + '[points_prefix]" class="form-control">';
  html += '    <option value="+">+</option>';
  html += '    <option value="-">-</option>';
  html += '  </select>';
  html += '  <input type="text" id="' + idPrefix + 'points" name="' + namePrefix + '[points]" value="" placeholder="<?php echo $entry_points; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><select id="' + idPrefix + 'weight_prefix" name="' + namePrefix + '[weight_prefix]" class="form-control">';
  html += '    <option value="+">+</option>';
  html += '    <option value="-">-</option>';
  html += '  </select>';
  html += '  <input type="text" id="' + idPrefix + 'weight" name="' + namePrefix + '[weight]" value="" placeholder="<?php echo $entry_weight; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(this).tooltip(\'destroy\');$(\'#option-value-row' + option_value_row + '\').remove();" data-toggle="tooltip" rel="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

  console.log(selectors);
  for (i = 0; i < selectors.length; i++) {
    console.log(option_row);
    var valueRow = $(selectors[i]).find('#option-value' + option_row);
    console.log(valueRow);
    valueRow.find('tbody').append(html);
  }

  $('[rel=tooltip]').tooltip();

  option_value_row++;
};

// Select all widgets
$('.oc-widget').each(function () {
  $(this).attr('data-id', guid());
});

var dynamicOptionsWidget = null,
    dynamicOptionsCustomizerWidget = null,
    dynamicOptionsWidgetParams;

dynamicOptionsWidgetParams = {
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/option/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            category: item['category'],
            label: item['name'],
            value: item['option_id'],
            type: 'select',
            option_value: item['option_value']
          }
        }));
      }
    });
  },
  'select': function(item) {
    widget = $(this).closest('[data-role=oc-autocomplete-tabs]');

    addOption(widget, item);

    if (isFunction(this.callback)) {
      this.callback.apply(this, [item]);
    }

    option_row++;
  }
};

dynamicOptionsWidget = $('#form-product input[name=\'option\']').autocomplete($.extend({
  'callback': function (item) {
    console.log('callback for main options widget');
    if (typeof item !== 'undefined') {
      var widget = $('#form-product-customizer input[name=\'option\']').closest('[data-role=oc-autocomplete-tabs]');
      addOption(widget, item);
    } else {
      console.log('error: no item defined');
    }

  }
}, dynamicOptionsWidgetParams));

dynamicOptionsCustomizerWidget = $('#form-product-customizer input[name=\'option\']').autocomplete($.extend({
  'callback': function (item) {
    console.log('callback for customizer options widget');
    if (typeof item !== 'undefined') {
      var widget = $('#form-product input[name=\'option\']').closest('[data-role=oc-autocomplete-tabs]');
      addOption(widget, item);
    } else {
      console.log('error: no item defined');
    }
  }
}, dynamicOptionsWidgetParams));

</script>
  <!-- TODO: FUTURE WIDGET TEMPLATING INJECT JS USING KENDO TEMPLATE SYNTAX
    <script id="option-value-row" type="text/html">
    <tr id="option-value-row' + option_value_row + '">
      <td class="text-left">
        <select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_value_id]" class="form-control">
        </select>
        <input type="hidden" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][product_option_value_id]" value="" /></td>
    </tr>
  </script>-->
  <script type="text/javascript"><!--
var discount_row = <?php echo $discount_row; ?>;

function addDiscount() {
  var idPrefix = 'd' + discount_row + '_',
      namePrefix = 'product_discount[' + discount_row + ']';

	html  = '<tr id="discount-row' + discount_row + '">';
  html += '  <td class="text-left"><select id="' + idPrefix + 'customer_group_id" name="' + namePrefix + '[customer_group_id]" class="form-control">';
  <?php foreach ($customer_groups as $customer_group) { ?>
  html += '    <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo addslashes($customer_group['name']); ?></option>';
  <?php } ?>
  html += '  </select></td>';
  html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'quantity" name="' + namePrefix + '[quantity]" value="" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'priority" name="' + namePrefix + '[priority]" value="" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>';
	html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'price" name="' + namePrefix + '[price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
  //html += '  <td class="text-left"><div class="input-group date"><input type="text" name="' + namePrefix + '[date_start]" value="" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
	//html += '  <td class="text-left"><div class="input-group date"><input type="text" name="' + namePrefix + '[date_end]" value="" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#discount-row' + discount_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#discount tbody').append(html);

	$('.date').datetimepicker({
		pickTime: false
	});

	discount_row++;
}
</script>
<script type="text/javascript">
var special_row = <?php echo $special_row; ?>;

function addSpecial() {
  var idPrefix = 's' + special_row + '_',
      namePrefix = 'product_special[' + special_row + ']';

	html  = '<tr id="special-row' + special_row + '">';
  html += '  <td class="text-left"><select id="' + idPrefix + 'customer_group_id" name="' + namePrefix + '[customer_group_id]" class="form-control">';
  <?php foreach ($customer_groups as $customer_group) { ?>
  html += '      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo addslashes($customer_group['name']); ?></option>';
  <?php } ?>
  html += '  </select></td>';
  html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'priority" name="' + namePrefix + '[priority]" value="" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>';
	html += '  <td class="text-right"><input type="text" id="' + idPrefix + 'price" name="' + namePrefix + '[price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
    //html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="' + namePrefix + '[date_start]" value="" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
	//html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="' + namePrefix + '[date_end]" value="" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#special-row' + special_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#special tbody').append(html);

	$('.date').datetimepicker({
		pickTime: false
	});

	special_row++;
}
</script>
<script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
	html  = '<tr id="image-row' + image_row + '">';
	html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#images tbody').append(html);

	image_row++;
}
//--></script>
  <script type="text/javascript"><!--
var recurring_row = <?php echo $recurring_row; ?>;

function addRecurring() {
	recurring_row++;

	html  = '';
	html += '<tr id="recurring-row' + recurring_row + '">';
	html += '  <td class="left">';
	html += '    <select name="product_recurring[' + recurring_row + '][recurring_id]" class="form-control">>';
	<?php foreach ($recurrings as $recurring) { ?>
	html += '      <option value="<?php echo $recurring['recurring_id']; ?>"><?php echo $recurring['name']; ?></option>';
	<?php } ?>
	html += '    </select>';
	html += '  </td>';
	html += '  <td class="left">';
	html += '    <select name="product_recurring[' + recurring_row + '][customer_group_id]" class="form-control">>';
	<?php foreach ($customer_groups as $customer_group) { ?>
	html += '      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>';
	<?php } ?>
	html += '    <select>';
	html += '  </td>';
	html += '  <td class="left">';
	html += '    <a onclick="$(\'#recurring-row' + recurring_row + '\').remove()" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a>';
	html += '  </td>';
	html += '</tr>';

	$('#tab-recurring table tbody').append(html);
}
//--></script>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.time').datetimepicker({
	pickDate: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script>
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
$('#option a:first').tab('show');
//--></script></div>

    <style scoped>
      .modal-xl {
        width: 1248px;
      }

      @media screen and (max-width: 1247px) {
        .modal-xl {
          width: 100%;
        }
      }
    </style>
    <script type="text/javascript">
$(document).ready(function () {
  var token = $('#qc-load-attributes-modal').attr('data-token');
	
	// Category
	$('#form-product-customizer input[name=\'category\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/category/autocomplete&token=' + token + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['category_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'category\']').val('');
			
			$('#form-product-customizer #product-category' + item['value']).remove();
			
			$('#form-product-customizer #product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
		}
	});

	$('#form-product-customizer #product-category').delegate('.fa-minus-circle', 'click', function() {
		$(this).parent().remove();
	});
	
	// Category
	$('#form-seo-rename-product-filter input[name=\'category\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/category/autocomplete&token=' + $(this).attr('data-token') + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['category_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'category\']').val('');
			
			$('#form-seo-rename-product-filter #product-category' + item['value']).remove();
			
			$('#form-seo-rename-product-filter #product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');	
		}
	});

	$('#form-seo-rename-product-filter #product-category').delegate('.fa-minus-circle', 'click', function() {
		$(this).parent().remove();
	});
    
    // Accordion sequence
    var target = $('#accordion'),
      panelCollapse = '.panel-collapse',
      next = target.find('.qc-wizard-continue');


    next.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Fix weird accordion issue
      target.find(panelCollapse).removeAttr('style');
      
      var content = $(this).closest(panelCollapse);
      content.addClass('collapse').removeClass('in');
      console.log(content);
      
      var nextPanel = content.closest('.panel').next(),
          nextCollapse = nextPanel.find(panelCollapse).first();
      
      nextCollapse.removeClass('collapse');
      
    });
	
	
});
    </script>
<?php echo $footer; ?> 