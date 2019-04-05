<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-shipping" form="form-invoice" formaction="<?php echo $shipping; ?>" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></button>
        <button type="submit" id="button-invoice" form="form-invoice" formaction="<?php echo $invoice; ?>" data-toggle="tooltip" title="<?php echo $button_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
        <a id="qc-qbo-import" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import from QuickBooks" class="btn btn-success"><i class="fa fa-cloud-download"></i> Import from QBO</a> <a id="qc-qbo-export" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Export to QuickBooks" class="btn btn-info"><i class="fa fa-cloud-upload"></i> Export to QBO</a> <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a></div>
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
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-invoice-id"><?php echo $entry_invoice_id; ?></label>
                <input type="text" name="filter_invoice_id" value="<?php echo $filter_invoice_id; ?>" placeholder="<?php echo $entry_invoice_id; ?>" id="input-invoice-id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-invoice-status"><?php echo $entry_invoice_status; ?></label>
                <select name="filter_invoice_status" id="input-invoice-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_invoice_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($invoice_statuses as $invoice_status) { ?>
                  <?php if ($invoice_status['invoice_status_id'] == $filter_invoice_status) { ?>
                  <option value="<?php echo $invoice_status['invoice_status_id']; ?>" selected="selected"><?php echo $invoice_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $invoice_status['invoice_status_id']; ?>"><?php echo $invoice_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-total"><?php echo $entry_total; ?></label>
                <input type="text" name="filter_total" value="<?php echo $filter_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
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
                  </select>
                  <span class="input-group-btn">
                    <button type="button" id="button-batch-action" class="btn btn-success pull-right"><i class="fa fa-list"></i> <?php echo 'Batch'; ?></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <form method="post" enctype="multipart/form-data" target="_blank" id="form-invoice" data-token="<?php echo $token; ?>">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-right"><?php if ($sort == 'o.invoice_id') { ?>
                    <a href="<?php echo $sort_invoice; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_invoice_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_invoice; ?>"><?php echo $column_invoice_id; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'o.invoice_no') { ?>
                    <a href="<?php echo ''; //$sort_invoice; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo 'No.'; //$column_invoice_no; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo ''; //$sort_invoice; ?>"><?php echo 'No.'; //$column_invoice_no; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'o.order_id') { ?>
                    <a href="<?php echo ''; //$sort_invoice; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo 'Order ID'; //$column_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo ''; //$sort_invoice; ?>"><?php echo 'Order ID'; //$column_order_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo ''; //$sort_customer; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo 'E-mail'; $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo ''; $sort_customer; ?>"><?php echo 'E-mail'; $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'o.total') { ?>
                    <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_total; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                    <?php } ?></td>
                  <!--<td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>-->
                  <!--<td class="text-left"><?php if ($sort == 'o.date_modified') { ?>
                    <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                    <?php } ?></td>-->
                  <td class="text-left"><?php if ($sort == 'o.invoice_date') { ?>
                    <a href="<?php echo ''; //$sort_date_modified; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo 'Invoice Date'; //$column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo ''; //$sort_date_modified; ?>"><?php echo 'Invoice Date'; //$column_date_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.due_date') { ?>
                    <a href="<?php echo ''; //$sort_date_modified; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo 'Due Date'; //$column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo ''; //$sort_date_modified; ?>"><?php echo 'Due Date'; //$column_date_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-left" colspan="2"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($invoice); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($invoices) { ?>
                <?php foreach ($invoices as $invoice) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($invoice['invoice_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $invoice['invoice_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $invoice['invoice_id']; ?>" />
                    <?php } ?>
                    <input type="hidden" name="shipping_code[]" value="<?php echo $invoice['shipping_code']; ?>" /></td>
                  <td class="text-right"><?php echo $invoice['invoice_id']; ?></td>
                  <td class="text-right"><?php echo $invoice['invoice_no']; ?></td>
                  <td class="text-right"><a href="<?php echo $invoice['order_url']; ?>" title="Go to Order #<?php echo $invoice['order_id']; ?>" target="_blank"><?php echo $invoice['order_id']; ?></a></td>
                  <td class="text-left">
                    <b><?php echo $invoice['customer_name']; ?></b>
                    <?php if (isset($invoice['customer']) && $invoice['customer'] != false) { ?>
                    <?php if (isset($invoice['customer']['company_name'])) { ?>
                    <br>
                    <small><?php echo $invoice['customer']['company_name']; ?></small>
                    <?php } ?>
                    <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $invoice['email']; ?></td>
                  <td class="text-right"><?php echo $invoice['total']; ?></td>
                  <!--<td class="text-left"><?php echo $invoice['date_added']; ?></td>-->
                  <!-- TODO: Replace with an 'is synced' icon -->
                  <!--<td class="text-left"><?php echo $invoice['date_modified']; ?></td>-->
                  <td class="text-left"><?php echo $invoice['invoice_date']; ?></td>
                  <td class="text-left"><?php echo $invoice['due_date']; ?></td>
                  <td class="text-left"><?php echo $invoice['status']; ?></td>
                  <td class="text-center">
                    <span data-id="<?php echo $invoice['invoice_id']; ?>" class="label label-default"><i class="fa fa-question"></i></span>
                  </td>
                  <td class="text-right"><a href="<?php echo $invoice['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info" target="_blank"><i class="fa fa-eye"></i></a> <a href="<?php echo $invoice['edit']; ?>" data-id="<?php echo $invoice['invoice_id']; ?>" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Sync with QuickBooks" class="btn btn-default"><i class="fa fa-refresh"></i></a> <a href="<?php echo $invoice['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a> <a href="<?php echo $invoice['delete']; ?>" id="button-delete<?php echo $invoice['invoice_id']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="12"><?php echo $text_no_results; ?></td>
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
	url = 'index.php?route=transaction/invoice&token=<?php echo $token; ?>';
	
	var filter_invoice_id = $('input[name=\'filter_invoice_id\']').val();
	
	if (filter_invoice_id) {
		url += '&filter_invoice_id=' + encodeURIComponent(filter_invoice_id);
	}
	
	var filter_customer = $('input[name=\'filter_customer\']').val();
	
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
	
	var filter_invoice_status = $('select[name=\'filter_invoice_status\']').val();
	
	if (filter_invoice_status != '*') {
		url += '&filter_invoice_status=' + encodeURIComponent(filter_invoice_status);
	}	

	var filter_total = $('input[name=\'filter_total\']').val();

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
				
	location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_customer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
            label: (typeof item['company_name'] === 'string' && item['company_name'] !== '') ? item['name'] + ' (' + item['company_name'] + ')' : item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_customer\']').val(item['label']);
	}	
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name^=\'selected\']').on('change', function() {
	$('#button-shipping, #button-invoice').prop('disabled', true);
	
	var selected = $('input[name^=\'selected\']:checked');
	
	if (selected.length) {
		$('#button-invoice').prop('disabled', false);
	}
	
	for (i = 0; i < selected.length; i++) {
		if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
			$('#button-shipping').prop('disabled', false);
			
			break;
		}
	}
});

$('input[name^=\'selected\']:first').trigger('change');

$('a[id^=\'button-delete\']').on('click', function(e) {
	e.preventDefault();
	
	if (confirm('<?php echo $text_confirm; ?>')) {
		location = $(this).attr('href');
	}
});
//--></script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>