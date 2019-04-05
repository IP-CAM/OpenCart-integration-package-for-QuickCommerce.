<div class="well">
  <form action="<?php echo $filter_action; ?>" method="post" enctype="multipart/form-data" id="form-product-filter">
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
          <option value="any"<?php if ($filter_status=='any') { echo ' selected="selected"'; } ?>></option>
          <option value="1"<?php if ($filter_status=='1') { echo ' selected="selected"'; } ?>><?php echo $text_enabled; ?></option>
          <option value="0"<?php if ($filter_status=='0') { echo ' selected="selected"'; } ?>><?php echo $text_disabled; ?></option>
        </select>
      </div>
      <div class="form-group">
        <label class="control-label" for="input-store"><?php echo $apftxt_store; ?></label>
        <select name="filter_store" id="input-store" class="form-control">
          <option value="any"<?php if ($filter_store=='any') { echo ' selected="selected"'; } ?>></option>
          <option value="0"<?php if ($filter_store=='0') { echo ' selected="selected"'; } ?>><?php echo $apftxt_default; ?></option>
          <?php foreach ($stores as $store) { ?>
          <option value="<?php echo $store['store_id']; ?>"<?php if ($store['store_id']==$filter_store) { echo ' selected="selected"'; } ?>><?php echo $store['name']; ?></option>
          <?php } ?>
        </select>
      </div>
      <?php /*
                  <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
      */ ?>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <label class="control-label"><?php echo $apftxt_categories; ?></label>
        <div class="well well-sm scroll1">
          <div class="checkbox">
            <label>
              <?php if (in_array(0, $product_category)) { ?>
              <input type="checkbox" name="product_category[]" value="0" checked="checked" /><?php echo $apftxt_none_cat; ?>
              <?php } else { ?>
              <input type="checkbox" name="product_category[]" value="0" /><?php echo $apftxt_none_cat; ?>
              <?php } ?>
            </label>
          </div>
          <?php foreach ($categories as $category) { ?>
          <div class="checkbox">
            <label>
              <?php if (in_array($category['category_id'], $product_category)) { ?>
              <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
              <?php echo $category['name']; ?>
              <?php } else { ?>
              <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" />
              <?php echo $category['name']; ?>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
        </div>
        <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $apftxt_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $apftxt_unselect_all; ?></a>
        <label class="control-label tooltip1"><span data-toggle="tooltip" title="<?php echo $apftxt_unselect_all_to_ignore; ?>"></span></label>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <label class="control-label"><?php echo $apftxt_manufacturers; ?></label>
        <div class="well well-sm scroll1">
          <div class="checkbox">
            <label>
              <?php if (in_array(0, $manufacturer_ids)) { ?>
              <input type="checkbox" name="manufacturer_ids[]" value="0" checked="checked" /><?php echo $apftxt_none; ?>
              <?php } else { ?>
              <input type="checkbox" name="manufacturer_ids[]" value="0" /><?php echo $apftxt_none; ?>
              <?php } ?>
            </label>
          </div>
          <?php foreach ($manufacturers as $manufacturer) { ?>
          <div class="checkbox">
            <label>
              <?php if (in_array($manufacturer['manufacturer_id'], $manufacturer_ids)) { ?>
              <input type="checkbox" name="manufacturer_ids[]" value="<?php echo $manufacturer['manufacturer_id']; ?>" checked="checked" />
              <?php echo $manufacturer['name']; ?>
              <?php } else { ?>
              <input type="checkbox" name="manufacturer_ids[]" value="<?php echo $manufacturer['manufacturer_id']; ?>" />
              <?php echo $manufacturer['name']; ?>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
        </div>
        <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $apftxt_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $apftxt_unselect_all; ?></a>
        <label class="control-label tooltip1"><span data-toggle="tooltip" title="<?php echo $apftxt_unselect_all_to_ignore; ?>"></span></label>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="form-group">
        <label class="control-label" for="input-tag"><?php echo $apftxt_tag; ?><span data-toggle="tooltip" title="<?php echo $apftxt_tag_help; ?>"></span></label>
        <input type="text" name="filter_tag" value="<?php echo $filter_tag; ?>" placeholder="<?php echo $apftxt_tag; ?>" id="input-tag" class="form-control" />
      </div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-hover"> <!-- filters table -->
      <tbody>

      <?php /*
                        <tr>
      <td class="text-left" style="width:236px;">
        <strong><?php echo $apftxt_name; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <input size="42" type="text" value="<?php echo $filter_name; ?>" name="filter_name">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_name_help; ?>"></span></label>
      </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_model; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <input size="42" type="text" value="<?php echo $filter_model; ?>" name="filter_model">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_model_help; ?>"></span></label>
      </td>
      </tr> */
      ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_p_filters; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <?php if($p_filters) { ?>
        <div class="well well-sm scroll1">
          <div class="checkbox">
            <label>
              <?php if (in_array(0, $filters_ids)) { ?>
              <input type="checkbox" name="filters_ids[]" value="0" checked="checked" /><?php echo $apftxt_none_fil; ?>
              <?php } else { ?>
              <input type="checkbox" name="filters_ids[]" value="0" /><?php echo $apftxt_none_fil; ?>
              <?php } ?>
            </label>
          </div>
          <?php foreach ($p_filters as $p_filter) { ?>
          <div class="checkbox">
            <label>
              <?php if (in_array($p_filter['filter_id'], $filters_ids)) { ?>
              <input type="checkbox" name="filters_ids[]" value="<?php echo $p_filter['filter_id']; ?>" checked="checked" />
              <?php echo $p_filter['group'].' &gt; '.$p_filter['name']; ?>
              <?php } else { ?>
              <input type="checkbox" name="filters_ids[]" value="<?php echo $p_filter['filter_id']; ?>" />
              <?php echo $p_filter['group'].' &gt; '.$p_filter['name']; ?>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
        </div>
        <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $apftxt_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $apftxt_unselect_all; ?></a>
        <label class="control-label tooltip1"><span data-toggle="tooltip" title="<?php echo $apftxt_unselect_all_to_ignore; ?>"></span></label>
        <?php } else { echo $apftxt_p_filters_none; } ?>
      </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_price; ?></strong>
        <span class="help"> <?php echo $apftxt_price_help; ?></span>
      </td>
      <td class="text-right">
        <?php echo $apftxt_greater_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $filter_price; ?>" name="filter_price">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      <td class="text-right">
        <?php echo $apftxt_less_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $price_max; ?>" name="price_max">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_discount; ?></strong>
      </td>
      <td class="text-right">

        <div style="float:left;border-right:1px solid #DDDDDD;margin: -7px;padding: 7px;">
          <?php echo $apftxt_customer_group; ?>
          <select name="d_cust_group_filter">
            <option value="any"<?php if ($d_cust_group_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_all; ?></option>
            <?php foreach ($customer_groups as $customer_group) { ?>
            <option value="<?php echo $customer_group['customer_group_id']; ?>"<?php if ($customer_group['customer_group_id']==$d_cust_group_filter) { echo ' selected="selected"'; } ?>><?php echo $customer_group['name']; ?></option>
            <?php } ?>
          </select>
        </div>

        <?php echo $apftxt_greater_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $d_filter_price; ?>" name="d_filter_price">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      <td class="text-right">
        <?php echo $apftxt_less_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $d_price_max; ?>" name="d_price_max">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_special; ?></strong>
        </td>
        <td class="text-right">

          <div style="float:left;border-right:1px solid #DDDDDD;margin: -7px;padding: 7px;">
            <?php echo $apftxt_customer_group; ?>
            <select name="s_cust_group_filter">
              <option value="any"<?php if ($s_cust_group_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_all; ?></option>
              <?php foreach ($customer_groups as $customer_group) { ?>
              <option value="<?php echo $customer_group['customer_group_id']; ?>"<?php if ($customer_group['customer_group_id']==$s_cust_group_filter) { echo ' selected="selected"'; } ?>><?php echo $customer_group['name']; ?></option>
              <?php } ?>
            </select>
          </div>

          <?php echo $apftxt_greater_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input size="10" type="text" value="<?php echo $s_filter_price; ?>" name="s_filter_price">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
        <td class="text-right">
          <?php echo $apftxt_less_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input size="10" type="text" value="<?php echo $s_price_max; ?>" name="s_price_max">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_tax_class; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <select name="tax_class_filter">
          <option value="any"<?php if ($tax_class_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
          <option value="0"<?php if ($tax_class_filter=='0') { echo ' selected="selected"'; } ?>> <?php echo $apftxt_none; ?> </option>
          <?php foreach ($tax_classes as $tax_class) { ?>
          <option value="<?php echo $tax_class['tax_class_id']; ?>"<?php if ($tax_class['tax_class_id']==$tax_class_filter) { echo ' selected="selected"'; } ?>><?php echo $tax_class['title']; ?></option>
          <?php } ?>
        </select>
      </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_quantity; ?></strong>
      </td>
      <td class="text-right">
        <?php echo $apftxt_greater_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $stock_min; ?>" name="stock_min">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      <td class="text-right">
        <?php echo $apftxt_less_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $stock_max; ?>" name="stock_max">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label></td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_minimum_quantity; ?></strong>
      </td>
      <td class="text-right">
        <?php echo $apftxt_greater_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $min_q_min; ?>" name="min_q_min">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      <td class="text-right">
        <?php echo $apftxt_less_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input size="10" type="text" value="<?php echo $min_q_max; ?>" name="min_q_max">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_subtract_stock; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <select name="subtract_filter">
            <option value="any"<?php if ($subtract_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
            <option value="1"<?php if ($subtract_filter=='1') { echo ' selected="selected"'; } ?>><?php echo $apftxt_yes; ?></option>
            <option value="0"<?php if ($subtract_filter=='0') { echo ' selected="selected"'; } ?>><?php echo $apftxt_no; ?></option>
          </select>
        </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_out_of_stock_status; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <select name="stock_status_filter">
            <option value="any"<?php if ($stock_status_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
            <?php foreach ($stock_statuses as $stock_status) { ?>
            <option value="<?php echo $stock_status['stock_status_id']; ?>"<?php if ($stock_status['stock_status_id']==$stock_status_filter) { echo ' selected="selected"'; } ?>><?php echo $stock_status['name']; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_requires_shipping; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <select name="shipping_filter">
            <option value="any"<?php if ($shipping_filter=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
            <option value="1"<?php if ($shipping_filter=='1') { echo ' selected="selected"'; } ?>><?php echo $apftxt_yes; ?></option>
            <option value="0"<?php if ($shipping_filter=='0') { echo ' selected="selected"'; } ?>><?php echo $apftxt_no; ?></option>
          </select>
        </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_date_available; ?></strong>
      </td>
      <td class="text-right">
        <?php echo $apftxt_greater_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input class="date" size="14" type="text" value="<?php echo $date_min; ?>" name="date_min" data-date-format="YYYY-MM-DD">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      <td class="text-right">
        <?php echo $apftxt_less_than_or_equal; ?>
      </td>
      <td class="text-left">
        <input class="date" size="14" type="text" value="<?php echo $date_max; ?>" name="date_max" data-date-format="YYYY-MM-DD">
        <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
      </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_date_added; ?></strong>
        </td>
        <td class="text-right">
          <?php echo $apftxt_greater_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input class="datetime" size="14" type="text" value="<?php echo $date_added_min; ?>" name="date_added_min" data-date-format="YYYY-MM-DD HH:mm">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
        <td class="text-right">
          <?php echo $apftxt_less_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input class="datetime" size="14" type="text" value="<?php echo $date_added_max; ?>" name="date_added_max" data-date-format="YYYY-MM-DD HH:mm">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_date_modified; ?></strong>
        </td>
        <td class="text-right">
          <?php echo $apftxt_greater_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input class="datetime" size="14" type="text" value="<?php echo $date_modified_min; ?>" name="date_modified_min" data-date-format="YYYY-MM-DD HH:mm">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
        <td class="text-right">
          <?php echo $apftxt_less_than_or_equal; ?>
        </td>
        <td class="text-left">
          <input class="datetime" size="14" type="text" value="<?php echo $date_modified_max; ?>" name="date_modified_max" data-date-format="YYYY-MM-DD HH:mm">
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
      </tr>
      */ ?>

      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_status; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <select name="prod_status">
          <option value="any"<?php if ($prod_status=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
          <option value="1"<?php if ($prod_status=='1') { echo ' selected="selected"'; } ?>><?php echo $apftxt_enabled; ?></option>
          <option value="0"<?php if ($prod_status=='0') { echo ' selected="selected"'; } ?>><?php echo $apftxt_disabled; ?></option>
        </select>
      </td>
      </tr>
      */ ?>



      <?php /*
                        <tr>
      <td class="text-left">
        <strong><?php echo $apftxt_with_attribute; ?></strong>
      </td>
      <td colspan="4" class="text-left">
        <select name="filter_attr">
          <option value="any"<?php if ($filter_attr=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
          <?php foreach ($all_attributes as $attrib) { ?>
          <option value="<?php echo $attrib['attribute_id']; ?>"<?php if ($attrib['attribute_id']==$filter_attr) { echo ' selected="selected"'; } ?>><?php echo $attrib['attribute_group']." > ".$attrib['name']; ?></option>
          <?php } ?>
        </select>
      </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_with_attribute_value; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <textarea name="filter_attr_val" cols="40" rows="2"><?php echo $filter_attr_val; ?></textarea>
          <label class="control-label"><span data-toggle="tooltip" title="<?php echo $apftxt_with_attribute_value_help; ?><br /><br /><?php echo $apftxt_leave_empty_to_ignore; ?>"></span></label>
        </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_with_this_option; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <select name="filter_opti">
            <option value="any"<?php if ($filter_opti=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
            <?php foreach ($all_options as $option) { ?>
            <option value="<?php echo $option['option_id']; ?>"<?php if ($option['option_id']==$filter_opti) { echo ' selected="selected"'; } ?>><?php echo $option['name']; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>

      <tr>
        <td class="text-left">
          <strong><?php echo $apftxt_with_this_option_value; ?></strong>
        </td>
        <td colspan="4" class="text-left">
          <select name="filter_opti_val">
            <option value="any"<?php if ($filter_opti_val=='any') { echo ' selected="selected"'; } ?>><?php echo $apftxt_ignore_this; ?></option>
            <?php foreach ($all_optval as $optval) { ?>
            <option value="<?php echo $optval['option_value_id']; ?>"<?php if ($optval['option_value_id']==$filter_opti_val) { echo ' selected="selected"'; } ?>><?php echo $optval['o_name']." > ".$optval['ov_name']; ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      */ ?>

      <tr>
        <td colspan="5" class="text-right" style="padding-top:15px !important;padding-bottom:15px !important;">
          <?php echo $apftxt_show_max_prod_per_pag1; ?>
          <input size="4" type="text" class="form-control" style="max-width: 60px; display: inline-block;" value="<?php echo $max_results; ?>" name="max_results">
          <?php echo $apftxt_show_max_prod_per_pag2; ?>
          &nbsp;&nbsp;&nbsp;
          <button type="submit" value="lista_prod" name="lista_prod" class="btn btn-primary" style="padding-left:43px;padding-right:43px;"><i class="fa fa-search"></i> <?php echo $apftxt_button_filter_products; ?></button>
          &nbsp;&nbsp;&nbsp;
          <button value="reset_filters" type="submit" name="reset_filters" class="btn btn-default"><?php echo $apftxt_button_reset_filters; ?></button>
        </td>
      </tr>

      </tbody>
    </table> <!-- filters table -->
  </div>
  </form>
</div>