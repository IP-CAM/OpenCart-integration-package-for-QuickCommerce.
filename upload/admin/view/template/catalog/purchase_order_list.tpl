<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<a onclick="$('form').attr('action', '<?php echo $print; ?>');$('#form').attr('target', '_blank');$('form').submit();" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $button_print; ?>"><i class="fa fa-print"></i></a>
		<a href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_insert; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
		  <div class="table-responsive">
			<table class="table table-hover table-bordered">
			  <thead>
				<tr>
				  <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				  <td class="text-left"><?php if ($sort == 'purchase_order_id') { ?>
					<a href="<?php echo $sort_purchase_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_purchase_order_id; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_purchase_order_id; ?>"><?php echo $column_purchase_order_id; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'order_name') { ?>
					<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'vendor') { ?>
					<a href="<?php echo $sort_vendor; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_vendor; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_vendor; ?>"><?php echo $column_vendor; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'status') { ?>
					<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'payment') { ?>
					<a href="<?php echo $sort_payment; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_payment; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_payment; ?>"><?php echo $column_payment; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'shipping') { ?>
					<a href="<?php echo $sort_shipping; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_shipping; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_shipping; ?>"><?php echo $column_shipping; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'total') { ?>
					<a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'date_arrival') { ?>
					<a href="<?php echo $sort_date_arrival; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_arrival; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_date_arrival; ?>"><?php echo $column_date_arrival; ?></a>
					<?php } ?></td>
				  <td class="text-left"><?php if ($sort == 'date_received') { ?>
					<a href="<?php echo $sort_date_received; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_received; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_date_received; ?>"><?php echo $column_date_received; ?></a>
					<?php } ?></td>
				  <td class="text-right"><?php if ($sort == 'date_added') { ?>
					<a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
					<?php } else { ?>
					<a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
					<?php } ?></td>
				  <td class="text-right"><?php echo $column_action; ?></td>
				</tr>
			  </thead>
			  <tbody>
			    <tr class="filter">
				  <td></td>
				  <td><input type="text" class="form-control" name="filter_purchase_order_id" value="<?php echo $filter_purchase_order_id; ?>" size="4" /></td>
				  <td><input type="text" class="form-control" name="filter_order_name" value="<?php echo $filter_order_name; ?>" /></td>
				  <td><select class="form-control" name="filter_purchase_order_vendor_id">
					<option value=""></option>
					<?php foreach ($vendors as $vendor) { ?>
					<option value="<?php echo $vendor['purchase_order_vendor_id']; ?>"<?php echo $vendor['purchase_order_vendor_id'] == $filter_purchase_order_vendor_id ? 'selected="selected"' : ''; ?>><?php echo $vendor['name']; ?></option>
					<?php } ?>
				  </td>
				  <td><select class="form-control" name="filter_status_id">
					<option value=""></option>
					<?php foreach ($statuses as $status) { ?>
					<option value="<?php echo $status['order_status_id']; ?>"<?php echo $status['order_status_id'] == $filter_status_id ? 'selected="selected"' : ''; ?>><?php echo $status['name']; ?></option>
					<?php } ?>
				  </td>
				  <td colspan="6"></td>
				  <td class="text-right"><a onclick="filter();" class="btn btn-xs btn-primary" data-toggle="tooltip" title="<?php echo $button_filter; ?>"><i class="fa fa-search"></i></a></td>
				</tr>
				<?php if ($purchase_orders) { ?>
				<?php foreach ($purchase_orders as $purchase_order) { ?>
				<tr>
				  <td style="text-align: center;"><?php if ($purchase_order['selected']) { ?>
					<input type="checkbox" name="selected[]" value="<?php echo $purchase_order['purchase_order_id']; ?>" checked="checked" />
					<?php } else { ?>
					<input type="checkbox" name="selected[]" value="<?php echo $purchase_order['purchase_order_id']; ?>" />
					<?php } ?></td>
				  <td class="text-left"><?php echo $purchase_order['purchase_order_id']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['order_name']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['vendor']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['status']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['payment']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['shipping']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['total']; ?></td>
				  <td class="text-left"><?php echo $purchase_order['date_arrival']; ?></td>
				  <td class="text-left"><span id="received-<?php echo $purchase_order['purchase_order_id']; ?>"><?php echo $purchase_order['date_received']; ?></span></td>
				  <td class="text-right"><?php echo $purchase_order['date_added']; ?></td>
				  <td class="text-right">[ <a href="<?php echo $purchase_order['edit']; ?>"><?php echo $text_edit; ?></a> ]
					[ <a href="<?php echo $purchase_order['view']; ?>" target="_blank"><?php echo $text_view; ?></a> ]<br /><br />
					[ <a style="cursor:pointer;" onclick="confirm('<?php echo $text_confirm_received; ?>') ? received('<?php echo $purchase_order['purchase_order_id']; ?>') : false;"><?php echo $text_received; ?></a> ]
					[ <a style="cursor:pointer;" onclick="confirm('<?php echo $text_confirm_resend; ?>') ? resend('<?php echo $purchase_order['purchase_order_id']; ?>') : false;"><?php echo $text_resend; ?></a> ]</td>
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
</div>
<script type="text/javascript"><!--
function filter() {
	var url = 'index.php?route=catalog/purchase_order&token=<?php echo $token; ?>';
	
	var filter_purchase_order_id = $('input[name=\'filter_purchase_order_id\']').val();
	
	if (filter_purchase_order_id) {
		url += '&filter_purchase_order_id=' + filter_purchase_order_id;
	}
	
	var filter_order_name = $('input[name=\'filter_order_name\']').val();
	
	if (filter_order_name) {
		url += '&filter_order_name=' + encodeURIComponent(filter_order_name);
	}
	
	var filter_purchase_order_vendor_id = $('select[name=\'filter_purchase_order_vendor_id\']').val();
	
	if (filter_purchase_order_vendor_id) {
		url += '&filter_purchase_order_vendor_id=' + filter_purchase_order_vendor_id;
	}
	
	var filter_status_id = $('select[name=\'filter_status_id\']').val();
	
	if (filter_status_id) {
		url += '&filter_status_id=' + filter_status_id;
	}
	
	location = url;
}

function received(purchase_order_id) {
	$.ajax({
		url: 'index.php?route=catalog/purchase_order/received&token=<?php echo $token; ?>&purchase_order_id=' + purchase_order_id,
		dataType: 'json',
		beforeSend: function() {
			$('#received-' + purchase_order_id).after(' <i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else if (json['success']) {
				alert(json['success']);
				
				$('#received-' + purchase_order_id).html(json['date']);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function resend(purchase_order_id) {
	$.ajax({
		url: 'index.php?route=catalog/purchase_order/resend&token=<?php echo $token; ?>&purchase_order_id=' + purchase_order_id,
		dataType: 'json',
		beforeSend: function() {
			$('#received-' + purchase_order_id).after('<i class="fa fa-spinner fa-spin"></i>');
		},		
		complete: function() {
			$('.fa-spinner').remove();
		},			
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			} else if (json['success']) {
				alert(json['success']);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//--></script>
<?php echo $footer; ?> 