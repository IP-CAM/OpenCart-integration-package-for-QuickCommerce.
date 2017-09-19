<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button id="qc-qbo-import" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Import from QuickBooks" class="btn btn-success"><i class="fa fa-cloud-download"></i> Import from QBO</button>
				<button id="qc-qbo-export" data-token="<?php echo $token; ?>" data-toggle="tooltip" title="Export to QuickBooks" class="btn btn-info"><i class="fa fa-cloud-upload"></i> Export to QBO</button>
				<button type="submit" form="form-option" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-option" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-type"><?php echo $entry_store; ?></label>
						<div class="col-sm-10">
							<select name="type" id="input-type" class="form-control">
								<option value="0" selected="selected">Default</option>
								<?php foreach ($stores as $store) { ?>
								<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<table id="account" class="table table-striped table-bordered table-hover">
						<thead>
						<tr>
							<td class="text-left">QBO ID</td>
							<td class="text-left">Account #</td>
							<td class="text-left required">Name</td>
							<td class="text-left">Classification</td>
							<td class="text-left">Account Type</td>
							<td class="text-left">Sub Type</td>
							<td class="text-right">Action</td>
						</tr>
						</thead>
						<tbody>
						<?php $account_row = 0; ?>
						<?php if (isset($accounts) && count($accounts) > 0) { ?>
							<?php foreach ($accounts as $account) { ?>
						<tr id="account-row<?php echo $account_row; ?>">
							<td class="text-left"><?php echo (isset($account['feed_id'])) ? $account['feed_id'] : ''; ?><!-- <input type="text" name="account[<?php echo $account_row; ?>][account_id]" value="<?php echo (isset($account['feed_id'])) ? $account['feed_id'] : ''; ?>" class="form-control" />--></td>
							<td class="text-right"><input type="text" name="account[<?php echo $account_row; ?>][account_num]" value="<?php echo $account['account_num']; ?>" class="form-control" /></td>
							<td class="text-left"><input type="hidden" name="account[<?php echo $account_row; ?>][account_id]" value="<?php echo $account['account_id']; ?>" />
								<input type="text" name="account[<?php echo $account_row; ?>][name]" value="<?php echo isset($account['name']) ? $account['name'] : ''; ?>" placeholder="<?php echo $entry_event; ?>" class="form-control" />
								<?php /*if (isset($error_event[$account_row][$language['language_id']])) { ?>
								<div class="text-danger"><?php echo $error_event[$account_row][$language['language_id']]; ?></div>
								<?php }*/ ?></td>
							<td class="text-left"><input type="text" name="account[<?php echo $account_row; ?>][classification]" value="<?php echo $account['classification']; ?>" class="form-control" /></td>
							<td class="text-left"><input type="text" name="account[<?php echo $account_row; ?>][account_type]" value="<?php echo $account['account_type']; ?>" class="form-control" /></td>
							<td class="text-left"><input type="text" name="account[<?php echo $account_row; ?>][account_type]" value="<?php echo $account['account_sub_type']; ?>" class="form-control" /></td>
							<td class="text-right">
								<button type="button" onclick="$('#account-row<?php echo $account_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
								<?php if (true/*!$account['enabled']*/) { ?>
								<button data-toggle="tooltip" title="<?php echo $button_enable; ?>" class="btn btn-success"><i class="fa fa-plus-circle"></i></button>
								<?php } else { ?>
								<button data-toggle="tooltip" title="<?php echo $button_disable; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
								<?php } ?>
							</td>
						</tr>
								<?php $account_row++; ?>
							<?php } ?>
						<?php } ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="6"></td>
							<td class="text-right"><button type="button" onclick="addAccount();" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
						</tr>
						</tfoot>
					</table>
				</form>
				<div class="row">
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
					<div class="col-sm-6 text-right"><?php echo $results; ?></div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript"><!--
		/*$('select[name=\'type\']').on('change', function() {
		 if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
		 $('#account').show();
		 } else {
		 $('#account').hide();
		 }
		 });

		 $('select[name=\'type\']').trigger('change');*/

		var codes = [
			{ code: 'pre.admin.product.add', category: 'Products', params: '(array)data' }
		];

		var widget = $('input[name=\'account[0][trigger]\']').autocomplete({
			source: function(request, response) {
				response($.map(codes, function (item) {
					return {
						category: item['category'],
						label: item['code'] + ' - ' + item['params'],
						value: item['code'],
						//type: item['code'],
						option_value: item['code']
					}
				}));
			}
		});

		var account_row = <?php echo $account_row; ?>;

		function addAccount() {
			html  = '<tr id="account-row' + account_row + '">';
			html += '  <td class="text-left"><input type="hidden" name="account[' + account_row + '][account_id]" value="" />';
			html += '  <td class="text-left"><input type="text" name="account[' + account_row + '][account_number]" value="" class="form-control" /></td>';
			html += '      <input type="text" name="account[' + account_row + '][name]" value="" class="form-control" />';
			html += '  </td>';
			html += '  <td class="text-left"><input type="text" name="account[' + account_row + '][classification]" value="" class="form-control" /></td>';
			html += '  <td class="text-left"><input type="text" name="account[' + account_row + '][account_type]" value="" class="form-control" /></td>';
			html += '  <td class="text-left"><input type="text" name="account[' + account_row + '][account_sub_type]" value="" class="form-control" /></td>';
			html += '  <td class="text-right"><input type="text" name="account[' + account_row + '][action]" value="" class="form-control" /></td>';
			html += '  <td class="text-right">';
			html += '    <button type="button" onclick="$(\'#account-row<?php echo $account_row; ?>\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			html += '    <button data-toggle="tooltip" title="<?php echo $button_disable; ?>" class="btn btn-success"><i class="fa fa-plus-circle"></i></button>';
			html += '  </td>';
			html += '</tr>';

			$('#account tbody').append(html);

			account_row++;
		}
		//--></script></div>
<?php echo $footer; ?>