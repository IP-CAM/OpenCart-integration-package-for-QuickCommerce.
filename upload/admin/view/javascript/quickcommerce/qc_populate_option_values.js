// Add first row so we have a rendered option value selectbox
$(document).ready(function () {
	$(document.body).on('click', 'button[name=qc-fill-select-options]', function (e) {
		var tabs = $('#tab-option .tab-content .tab-pane'),
			tab = $(e.currentTarget).closest('.tab-pane.active');
			tabSelector = '#' + tab.attr('id');
			tabIndex = tabs.index(tab);

		//addOptionValue(tabIndex);
		addOptionValue(tabSelector, tabIndex); // TODO: Reverse param order for better compatibility

		var options = $(tabSelector).find('tr[id^=option-value-row]').first().find('select.form-control').first().find('option');
		var currentSelect;

		$.each(options, function (idx, option) {
			var opt = $(option), 
				val = opt.attr('value'), 
				text = opt.text();
			
			console.log(option);
			console.log(opt);
			
			if (idx === 0) return true;
			addOptionValue(tabSelector, tabIndex); // TODO: Reverse param order for better compatibility
			currentSelect = $('table#option-value' + tabIndex + ' tbody').find('tr[id^=option-value-row]').last().find('select.form-control').first();
			currentSelect.val(val);
		});
	});
});
