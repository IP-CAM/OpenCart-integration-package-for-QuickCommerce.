//==============================================================================
// Form Builder Pro v230.3
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

var initialized = false;

function initializeFormBuilder() {
	// Parent-child fields
	var singleElementsToTrigger = [];
	var multipleElementsToTrigger = [];
	
	$('.form-field :input').on('change', function(){
		var key = $(this).parents('.form-field').attr('data-key');
		$(this).parents('form').find('[data-parent^="' + key + ':"]').slideUp();
		
		if ($(this).hasClass('checkbox-field')) {
			$('input[name="' + $(this).attr('name') + '"]:checked').each(function(){
				$(this).parents('form').find('[data-parent="' + key + ':' + $(this).val() + '"]').slideDown().css('display', 'inline-block');
			});
		} else if ($(this).hasClass('file-field')) {
			if ($(this).parent().find('.file-upload-success').length) {
				$(this).parents('form').find('[data-parent^="' + key + ':"]').slideDown().css('display', 'inline-block');
			}
		} else {
			$(this).parents('form').find('[data-parent="' + key + ':' + $(this).val() + '"]').slideDown().css('display', 'inline-block');
		}
	}).each(function(){
		if ($(this).hasClass('checkbox-field') || $(this).hasClass('radio-field')) {
			if ($.inArray('[name="' + $(this).attr('name') + '"]', multipleElementsToTrigger) == -1) {
				multipleElementsToTrigger.push('[name="' + $(this).attr('name') + '"]');
			}
		} else {
			singleElementsToTrigger.push('[name="' + $(this).attr('name') + '"]');
		}
	});
	
	for (index in singleElementsToTrigger) {
		$(singleElementsToTrigger[index]).trigger('change');
	}
	for (index in multipleElementsToTrigger) {
		$(multipleElementsToTrigger[index]).first().trigger('change');
	}
	
	// Date Selection fields
	$('.date-field').each(function(){
		var min = false;
		var max = false;
		var selectMonths = false;
		var selectYears = false;
		
		if ($(this).attr('min')) {
			min = $(this).attr('min').split('-');
			min = new Date(min[0], min[1]-1, min[2])
		}
		if ($(this).attr('max')) {
			max = $(this).attr('max').split('-');
			max = new Date(max[0], max[1]-1, max[2])
		}
		if ($(this).attr('selectmonths')) {
			selectMonths = true;
		}
		if ($(this).attr('selectyears')) {
			selectYears = $(this).attr('selectyears');
		}
		
		$(this).pickadate({
			format: $(this).attr('format'),
			min: min,
			max: max,
			selectMonths: selectMonths,
			selectYears: selectYears,
		});
	});
	
	// Time Selection fields
	$('.time-field').each(function(){
		var min = false;
		var max = false;
		
		if ($(this).attr('min')) {
			min = $(this).attr('min').split(':');
			min = [min[0], min[1]]
		}
		if ($(this).attr('max')) {
			max = $(this).attr('max').split(':');
			max = [max[0], max[1]]
		}
		
		$(this).pickatime({
			format: $(this).attr('format'),
			interval: parseInt($(this).attr('data-interval')),
			min: min,
			max: max,
		});
	});
	
	// File Upload fields
	$('.file-field').each(function(){
		$(this).fileupload({
			dataType: 'json',
			add: function(e, data) {
				if ($(this).attr('data-limit') && $(this).attr('data-limit') > 0 && $(this).parent().find('.file-upload-success').length > (parseInt($(this).attr('data-limit')) - 1)) {
					$(this).parent().find('.file-upload-progress').after('<div class="file-upload-error"><div class="remove-icon" onclick="$(this).parent().remove()"></div>' + form_language['error_file_limit'] + '</div>');
				} else {
					data.process().done(function(){
						data.submit();
					});
				}
			},
			done: function (e, data) {
				$('.file-upload-progress').html('');
				if (data.result['error']) {
					$(this).parent().find('.file-upload-progress').after('<div class="file-upload-error"><div class="remove-icon" onclick="$(this).parent().remove()"></div>' + data.result['name'] + ': <em>' + data.result['error'] + '</em></div>');
				} else {
					$(this).parents('form').find('[data-parent^="' + $(this).parent().attr('data-key') + ':"]').slideDown().css('display', 'inline-block');
					$(this).parent().find('.file-upload-progress').after('<div class="file-upload-success"><div class="remove-icon" onclick="if (confirm(form_language[\'button_delete\'])) { $(this).parent().remove(); $(\'.file-field\').change(); }"></div>' + data.result['name'] + '<input type="hidden" name="' + $(this).attr('id') + '[]" value="' + data.result['file'] + '" /></div>');
				}
			},
			progressall: function (e, data) {
				var progress = Math.round(data.loaded / data.total * 10000) / 100;
				$(this).parent().find('.file-upload-progress').html(progress + '%');
			}
		});
	});
	
	// finished
	initialized = true;
}
$(document).ready(function(){
	if (!initialized) {
		initializeFormBuilder();
	}
});

// Validation functions
function validateMin(element, min) {
	if (element.val().length && element.val().length < min) {
		alert(form_language['error_minlength'].replace('[min]', min));
		element.focus();
	}
}

function validateMaxAllowed(element, event, max, allowed) {
	if ($.inArray(event.which, [0, 8, 13]) != -1) return;
	if (max && (element.val().length + 1) > max && element[0].selectionStart == element[0].selectionEnd) {
		event.preventDefault();
	}
	if (allowed && allowed.indexOf(String.fromCharCode(event.which)) == -1) {
		event.preventDefault();
	}
}

function validatePaste(element, max, allowed) {
	setTimeout(function(){
		if (allowed) {
			var regex = new RegExp('[^' + allowed.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&') + ']', 'g');
			element.val(element.val().replace(regex, ''));
		}
		if (max && element.val().length > max) {
			element.val(element.val().substr(0, max));
		}
	}, 0);
}

function validateForm(module_id) {
	var errors = [];
	$('#form' + module_id + ' .required-background').removeClass('required-background');
	
	$('#form' + module_id + ' .required-field:visible').each(function(){
		var element = $(this);
		element.find(':input').not('button').each(function(){
			if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
				element.addClass('required-background');
			} else if ($(this).attr('type') == 'file') {
				if (!element.find('input[type="hidden"]').val()) {
					element.addClass('required-background');
				}
			} else if (!$(this).val()) {
				element.addClass('required-background');
			}
		});
		element.find(':checkbox:checked, :radio:checked').each(function(){
			element.removeClass('required-background');
		});
	});
	if ($('#form' + module_id + ' .required-background').length) {
		errors.push(form_language['error_required']);
	}
	
	var regex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/i;
	$('#form' + module_id + ' .email-field').each(function(){
		if ($(this).val() || $(this).parent().find('.confirm-field').val()) {
			if (!regex.test($(this).val())) {
				$(this).parent().addClass('required-background');
				errors.push(form_language['error_invalid_email']);
			}
			if ($(this).parent().find('.confirm-field').length && $(this).val() != $(this).parent().find('.confirm-field').val()) {
				$(this).parent().addClass('required-background');
				errors.push(form_language['error_email_mismatch']);
			}
		}
	});
	
	return errors;
}

// Submission function
function submitForm(element, module_id, please_wait, success, redirect) {
	var buttonText = element.html();
	element.attr('disabled', 'disabled').html(please_wait);
	
	var errors = validateForm(module_id);
	if (errors.length) {
		alert('• ' + errors.join('\n• '));
		element.removeAttr('disabled').html(buttonText);
	} else {
		$.ajax({
			type: 'POST',
			url: 'index.php?route=module/form_builder/submit&module_id=' + module_id + '&captcha=' + (typeof grecaptcha === 'undefined' ? '' : grecaptcha.getResponse()),
			data: $('#form' + module_id).find(':input:not(:checkbox), :checkbox:checked').serialize(),
			success: function(data) {
				element.removeAttr('disabled').html(buttonText);
				if (data.trim() == 'success') {
					if (redirect.toLowerCase() == 'http://replace') {
						$('#form' + module_id + ' .box-content').html(success);
					} else {
						if (success) {
							alert(success);
						}
						if (redirect.toLowerCase() == 'http://back') {
							history.back();
						} else if (redirect) {
							location = redirect;
						}
					}
				} else {
					alert(data);
				}
			}
		});
	}
}
