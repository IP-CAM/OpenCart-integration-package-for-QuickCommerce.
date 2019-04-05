<div class="table-responsive" id="products-purchased">
  <table class="table table-bordered">
	<thead>
	  <tr>
		<th class="text-left"><input type="checkbox" checked="checked" /></th>
		<th class="text-left"><?php echo $column_name; ?></th>
		<!--<th class="text-left"><?php echo $column_model; ?></th>-->
		<th class="text-right"><?php echo $column_quantity; ?></th>
		<th class="text-right" colspan="1">Revenue</th>
		<th class="text-right" colspan="2">Deduct Cost</th>
		<th class="text-right" colspan="2">Billing Rate
			<!--<br><input class="form-control" type="number" value="<?php echo $product['price']; ?>">
			<select class="form-control" class="form-control">
			  <option value="+ Add">[+] Add</option>
			  <option value="-">[-] Subtract</option>
			  <option value="x">[x] Rate</option>
			  <option value="%" selected="selected">[%] Percent</option>
			  <option value="=">[=] Equals</option>
			</select>--></th>
		<th class="text-right">Total</th>
	  </tr>
	</thead>
	<tbody>
	  <?php if ($products) { ?>
	  <?php $subtotal = 0.00; ?>
	  <?php foreach ($products as $product) { ?>
		<?php $product_id = $product['product_id']; ?>
	  <tr>
		<?php $subtotal += (isset($product['total'])) ? $product['total'] : 0.00; ?>
		<td class="text-right" data-col="selected" data-type="boolean">
			<input class="form-control" type="checkbox" name="product_sales[<?php echo $product_id; ?>][product_id]" value="<?php echo $product_id; ?>" checked="checked"></td>
		<td class="text-left">
			<?php echo $product['name']; ?>
			<input type="hidden" name="product_sales[<?php echo $product_id; ?>][description]" value="<?php echo $product['name']; ?>"></td>
		<!--<td class="text-left">
			<?php echo $product['model']; ?></td>-->
		<td class="text-right" data-col="quantity" data-type="number">
			<input class="form-control" type="number" name="product_sales[<?php echo $product_id; ?>][quantity]" value="<?php echo $product['quantity']; ?>"></td>
		<!--<td class="text-right">
			<input type="checkbox" name="product_sales[<?php echo $product_id; ?>][custom_revenue]" checked="checked" value="1"></td>-->
		<td class="text-right" data-col="revenue" data-type="number">
			<input class="form-control" type="number" name="product_sales[<?php echo $product_id; ?>][revenue]" value="<?php echo $product['total']; ?>"></td>
		<td class="text-right" data-col="cost" data-type="number">
			<input type="checkbox" name="product_sales[<?php echo $product_id; ?>][deduct_cost]" value="1"></td>
		<td class="text-right" data-col="cost" data-type="number">
			<input class="form-control" type="number" name="product_sales[<?php echo $product_id; ?>][cost]" value="<?php echo $product['cost']; ?>" disabled="disabled"></td>
		<td class="text-right" data-col="rate-factor" data-type="number">
			<input class="form-control" type="number" name="product_sales[<?php echo $product_id; ?>][rate_factor]" value="10"></td>
		<td class="text-right" data-col="modifier" data-type="option">
			<select class="form-control" name="product_sales[<?php echo $product_id; ?>][price_prefix]" class="form-control">
			  <option value="+">[+] Add</option>
			  <option value="-">[-] Subtract</option>
			  <option value="*">[x] Rate</option>
			  <option value="%" selected="selected">[%] Percent</option>
			  <option value="=">[=] Equals</option>
			</select></td>
		<td class="text-right" data-col="total" data-type="number">
			<?php echo $product['total']; ?></td>
	  </tr>
	  <?php } ?>
	  <?php } else { ?>
	  <tr>
		<td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
	  </tr>
	  <?php } ?>
	</tbody>
	<tfoot id="total">
	<?php foreach($totals as $total) { ?>
		<tr>
			<td colspan="8" class="text-right"><?php echo $total['title']; ?></td>
			<td class="text-right"><?php echo $total['text']; ?></td>
		</tr>
	<?php } ?>
	</tfoot>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
<script type="text/javascript"><!--
 
// TODO: Something like this would be slick but we're doing this quick and dirty for now
//https://github.com/remy/bind.js/blob/master/lib/bind.js

function DataItem (row) {
	this.element = row;
	this.data = [];
	this.clean = [];
	this.children = null;
	this.init(row);
	
	
};

DataItem.prototype.init = function (row) {
	var that = this;
	that.children = $(row).find('td');
	
	$.each(that.children, function(idx, cell) {
		that.fieldUpdated(cell);
		
		var field = $(cell).find('input, select').first();
		field.on('change', function (e) {
			that.fieldUpdated(cell);
			that.calcTotals();
		});
	});
	
	that.calcTotals();
	
	this.clean = JSON.parse(JSON.stringify(this.data)); // Copy without reference
};

DataItem.prototype.getField = function (col) {
	if (typeof col === 'number' || typeof col === 'string') {
		return this.data[col];
	}
};

DataItem.prototype.fieldUpdated = function (cell) {		
	var field = $(cell).find('input, select').first(),
		disabled = field.prop('disabled'),
		col = $(cell).attr('data-col'),
		type = $(cell).attr('data-type'),
		val = field.val();
		
	if (disabled) return; // Just return if the field is disabled
	
	if (col && type) {		
		if (typeof field === 'undefined' || field.length === 0) {
			// If we didn't find anything, grab the text and go
			val = $(cell).html();
		}
		
		if (type === 'number' && typeof val === 'string') {
			val = Number(val.replace(/[^0-9\.]+/g, ''));
		}
		
		this.data[col] = val;
		//console.log('update: ' + col + ' = ' + val);
	}
}

DataItem.prototype.calcTotals = function () {
	var quantity = this.data['quantity'],
		customQty = false;
		cost = this.data['cost'],
		deductCost = false,
		revenue = this.data['revenue'],
		rate = this.data['rate-factor'],
		modifier = this.data['modifier'],
		total = this.data['total'];
		
	/*console.log(quantity);
	console.log(cost);
	console.log(deductCost);
	console.log(revenue);
	console.log(rate);
	console.log(modifier);
	console.log(total);
	console.log('----------');*/
	console.log('Remember to fix commission invoice line calculations!');
		
	/*if (typeof cost !== 'undefined') {
		// Has cost been changed
		if (cost !== this.clean['cost']) {
			deductCost = true;
		} 
		
		// Has quantity changed
		if (quantity !== this.clean['quantity']) {
			customQty = true;
		}
		
		console.log(deductCost);
		
		if (customQty) ? price * quantity : total;
	} else if (typeof revenue !== 'undefined') {
		total = parseInt(revenue);
	}
	
	switch (modifier) {
		case '+':
			total = total + rate;
			break;
		case '-':
			total = total - rate;
			break;
		case '*':
			total = total * rate;
			break;
		case '%':
			//this.setField('total', rate / 100 * rev);			
			total = total * (rate / 100);
			break;
		case '=':
			total = rate;
			break;
	}*/
	
	//$(this.element).find('[data-col=total]').html(total.toFixed(2));
};

DataItem.prototype.setField = function (col, value) {
	this.data[col] = value;
	//this.element.find('[data-col=' + col + ']');
};

var sales = $('#tab-sales'),
	table = sales.find('table.table');
	rows = table.find('tbody tr');
	
rows.each(function (idx, row) {
	row = $(row).data('model', new DataItem(row));
	
	var checkbox = row.find('[data-col=cost] input[type=checkbox]'),
		cost = row.find('[data-col=cost] input[type=number]');
	
	checkbox.on('click', function () {
		if ($(this).is(':checked')) {		
			cost.removeAttr('disabled');
			console.log(cost);
		} else {
			cost.attr('disabled', 'disabled');
			console.log(cost);
		}
	});
	//console.log(checkbox);
});

/**
 * jQuery serializeObject
 * @copyright 2014, macek <paulmacek@gmail.com>
 * @link https://github.com/macek/jquery-serialize-object
 * @license BSD
 * @version 2.5.0
 */
!function(e,i){if("function"==typeof define&&define.amd)define(["exports","jquery"],function(e,r){return i(e,r)});else if("undefined"!=typeof exports){var r=require("jquery");i(exports,r)}else i(e,e.jQuery||e.Zepto||e.ender||e.$)}(this,function(e,i){function r(e,r){function n(e,i,r){return e[i]=r,e}function a(e,i){for(var r,a=e.match(t.key);void 0!==(r=a.pop());)if(t.push.test(r)){var u=s(e.replace(/\[\]$/,""));i=n([],u,i)}else t.fixed.test(r)?i=n([],r,i):t.named.test(r)&&(i=n({},r,i));return i}function s(e){return void 0===h[e]&&(h[e]=0),h[e]++}function u(e){switch(i('[name="'+e.name+'"]',r).attr("type")){case"checkbox":return"on"===e.value?!0:e.value;default:return e.value}}function f(i){if(!t.validate.test(i.name))return this;var r=a(i.name,u(i));return l=e.extend(!0,l,r),this}function d(i){if(!e.isArray(i))throw new Error("formSerializer.addPairs expects an Array");for(var r=0,t=i.length;t>r;r++)this.addPair(i[r]);return this}function o(){return l}function c(){return JSON.stringify(o())}var l={},h={};this.addPair=f,this.addPairs=d,this.serialize=o,this.serializeJSON=c}var t={validate:/^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,key:/[a-z0-9_]+|(?=\[\])/gi,push:/^$/,fixed:/^\d+$/,named:/^[a-z0-9_]+$/i};return r.patterns=t,r.serializeObject=function(){return new r(i,this).addPairs(this.serializeArray()).serialize()},r.serializeJSON=function(){return new r(i,this).addPairs(this.serializeArray()).serializeJSON()},"undefined"!=typeof i.fn&&(i.fn.serializeObject=r.serializeObject,i.fn.serializeJSON=r.serializeJSON),e.FormSerializer=r,r});

$('#button-commission-add').on('click', function() {
	var that = this,
		idxArr = [],
		data = null,
		selected;

	selected = $('#products-purchased tbody').find('td:first-child input[type=checkbox]:checked');

	selected.each(function (idx, row) {
		idxArr.push(idx);
	});
	
	$('#products-purchased tbody').find('tr').each(function (idx, row) {
		if (idxArr.indexOf(idx) !== -1) {
			if (data === null) {
				data = $(row).find('input[name^=product_sales], select[name^=product_sales], checkbox[name^=product_sales]')
			} else {
				data = data.add($(row).find('input[name^=product_sales], select[name^=product_sales], checkbox[name^=product_sales]'));
			}
		}
	});
	
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

$('#products-purchased').find('table thead').on('click', 'th:first-child input[type=checkbox]', function (e) {
	var checked = $(this).prop('checked'),
		table = $(e.delegateTarget).closest('table'),
		selected = table.find('tbody tr td:first-child input[type=checkbox]');
		
	selected.each(function () {
		$(this).prop('checked', checked);
	});
});
	
$('#button-filter').on('click', function() {
	<?php if (isset($invoice_id) && $invoice_id) { ?>
	var url = 'index.php?route=transaction/invoice/edit&token=<?php echo $token; ?>&invoice_id=<?php echo $invoice_id; ?>';
	<?php } else { ?>
	var url = 'index.php?route=transaction/invoice/add&token=<?php echo $token; ?>';
	<?php } ?>
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
	
	if (filter_order_status_id != 0) {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

	location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script> 
</div>
<?php /* echo $footer; */ ?>