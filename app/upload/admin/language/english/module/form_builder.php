<?php
//==============================================================================
// Form Builder Pro v230.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

$version = 'v230.1';

//------------------------------------------------------------------------------
// Heading
//------------------------------------------------------------------------------
$_['heading_title']						= 'Form Builder Pro';

//------------------------------------------------------------------------------
// Extension Settings
//------------------------------------------------------------------------------
$_['heading_form_list']					= 'Form List';

$_['column_module_name']				= 'Form Name';
$_['column_edit_module']				= 'Edit Form';
$_['column_copy_module']				= 'Copy Form';
$_['column_delete_module']				= 'Delete Form (and Responses)';

$_['button_create_new_form']			= 'Create New Form';

//------------------------------------------------------------------------------
// Form Report
//------------------------------------------------------------------------------
$_['tab_form_report']					= 'Form Report';

$_['button_export_report']				= 'Export Report';
$_['button_export_summary']				= 'Export Summary';
$_['button_toggle_blank_responses']		= 'Toggle Blank Responses';
$_['button_toggle_report_summary']		= 'Toggle Report/Summary';

$_['column_action']						= 'Action';
$_['column_customer']					= 'Customer';
$_['column_date_added']					= 'Date Added';
$_['column_ip_address']					= 'IP Address';
$_['column_responses']					= 'Responses';
$_['column_']							= '';

$_['text_guest']						= 'Guest';
$_['text_enter_a_delimiter']			= 'Enter a delimiter to use between each response. Use a comma to give each response its own column, or another character (like a semi-colon) to put all responses in a single column.';

//------------------------------------------------------------------------------
// General Settings
//------------------------------------------------------------------------------
$_['heading_create_new_form']			= 'Create a New Form';
$_['heading_edit']						= 'Edit';

$_['tab_general_settings']				= 'General Settings';

$_['entry_module_status']				= 'Status: <div class="help-text">Choose whether to enable or disable the form.</div>';
$_['entry_module_name']					= 'Name: <div class="help-text">Enter a name for the form, for admin reference only.</div>';
$_['entry_module_record_responses']		= 'Record Responses: <div class="help-text">Choose whether to record responses in the database. If set to "No", responses will still be e-mailed to the administrator.</div>';
$_['entry_module_heading']				= 'Heading: <div class="help-text">Enter the heading text for the form module box. HTML is supported.</div>';
$_['entry_module_recaptcha_site_key']	= 'ReCaptcha Site Key: <div class="help-text">If using a Captcha field on this form, fill in your ReCaptcha site key, or sign up for one <a target="_blank" href="https://www.google.com/recaptcha/admin">here</a>.</div>';
$_['entry_module_recaptcha_secret_key']	= 'ReCaptcha Secret Key: <div class="help-text">If using a Captcha field on this form, fill in your ReCaptcha secret key.</div>';
$_['entry_module_file_size']			= 'Maximum File Size (KB): <div class="help-text">Enter the maximum file size allowed for File Upload fields, in KB.<br />(1 MB = 1024 KB)</div>';
$_['entry_module_file_extensions']		= 'Allowed File Extensions: <div class="help-text">Enter the allowed file extensions, using periods before the extension and separated by commas. For example: <code>.gif, .jpg, .jpeg, .png</code></div>';

//------------------------------------------------------------------------------
// Display Settings
//------------------------------------------------------------------------------
$_['tab_display_settings']				= 'Display Settings';

$_['entry_module_layout_id']			= 'Layout:';
$_['entry_module_position']				= 'Position:';
$_['entry_module_sort_order']			= 'Sort Order:';

$_['text_content_top']					= 'Content Top';
$_['text_column_left']					= 'Column Left';
$_['text_column_right']					= 'Column Right';
$_['text_content_bottom']				= 'Content Bottom';

$_['help_module_locations']				= 'Forms created by Form Builder are standard modules. You can set your module locations in';

$_['entry_module_positioning']			= 'Positioning: <div class="help-text">Choose how fields are positioned relative to one another. If all of your fields use the same number of rows then use "Block". Otherwise, if you have a more complex form with cross-row fields, use "Absolute". If the form display looks strange, try switching this setting.</div>';
$_['text_block']						= 'Block';
$_['text_absolute']						= 'Absolute';

$_['entry_module_row_height']			= 'Row Height: <div class="help-text">When "Absolute" positioning is used, enter the height in pixels for every row. (Note: the grid background for the demo in the Form Fields tab is based on a 50 pixel height, so it will not line up properly for values that are not multiples of 50.)</div>';

$_['entry_create_form_page']			= 'Create Form Page: <div class="help-text">Clicking this button will:<br />(1) create a Layout specifically for the form,<br />(2) create a blank Information page dedicated to the form,<br />(3) add a module instance to that Layout, so it appears only on that Information page.</div>';
$_['text_you_must_save_the_form']		= '(You must save the form before creating a form page.)';
$_['button_create_form_page']			= 'Create Form Page';

$_['entry_module_additional_css']		= 'Additional CSS: <div class="help-text">Add any additional CSS styling here. If your CSS does not seem to be applying, try adding <code>!important</code> at the end of the declarations, to override any other CSS styling.</div>';
$_['placeholder_additional_css']		= 'This form\'s ID is #form';

$_['help_hiding_box_borders']			= 'To hide the module box heading and borders, you can add this CSS:';

//------------------------------------------------------------------------------
// Form Fields
//------------------------------------------------------------------------------
$_['tab_form_fields']					= 'Form Fields';

$_['help_form_fields']					= '
<p>To add fields to the form, follow these steps:</p><br />
<ol>
	<li>Click a field type on the left.</li><br />
	<li>To edit a field\'s settings, click its "Edit" button, or double-click anywhere when hovering over it.</li><br />
	<li>To resize a field, hover over it, then click and drag the resize icon in its lower-left corner.</li><br />
	<li>To move a field, just drag and drop it to the location where you want it.</li><br />
	<li>To remove a field, click its "Delete" button.</li><br />
	<li>Note: the rendered form here is a <b>demo only</b>. The demo form is 800 pixels wide, but the front-end will be full-width for whatever space it occupies. To view how the form actually displays, you need to visit the front-end where the form module is set to appear.</li><br />
</ol>
<p>Here are some notes about specific field settings:</p><br />
<ol>
	<li>The <b>Key</b> setting is used to track responses in the database. It must be unique for each field, and contain only alphanumeric characters.</li><br />
	<li>The <b>Parent</b> setting can be used to hide a field until its parent has the appropriate value. To do this, enter the Key for the parent, followed by a colon, then the value needed to show the field. For example, if you had checkboxes with a Key of "Choices", and wanted a Text Input field to be hidden until the customer selected choice "XYZ", you\'d enter <code>Choices:XYZ</code> in the Parent setting for the Text Input field. (Note: fields with a Parent value will still take up space in the form display, but will not appear until their parent has the appropriate value. It is recommended to place child fields next to each other horizontally, to avoid having blank space in the form.)</li><br />
	<li>For most fields, you can enter a <a target="_blank" href="http://en.wikipedia.org/wiki/Query_string">query string variable</a> in brackets to pull its value from the URL. For example, if you enter <code>[path]</code> and the URL contains <code>&path=2_7_13</code>, the value <code>2_7_13</code> will be entered into that location when the form loads. This will work even if SEO URLs are enabled, but you will need to know what the usual query string variables are.</li><br />
	<li>If the URL contains the product_id query string variable, you can also enter any column name in the "product" or "product_description" tables prefixed with "product_", and it will pull that information from the database. For example, if the URL contains <code>&product_id=5</code> and the product with the product_id of 5 has the name of "iPhone", you can enter <code>[product_name]</code> to have "iPhone" automatically inserted in that field.</li>
</ol>
';

$_['button_help']						= 'Help';
$_['button_toggle_grid']				= 'Toggle Grid';
$_['button_captcha']					= 'Captcha';
$_['button_checkboxes']					= 'Checkboxes';
$_['button_date']						= 'Date Selection';
$_['button_email_address']				= 'E-mail Address';
$_['button_file_upload']				= 'File Upload';
$_['button_hidden_data']				= 'Hidden Data';
$_['button_html_block']					= 'HTML Block';
$_['button_radio_buttons']				= 'Radio Buttons';
$_['button_select_dropdown']			= 'Select Dropdown';
$_['button_submit_button']				= 'Submit Button';
$_['button_text_input']					= 'Text Input';
$_['button_time']						= 'Time Selection';

$_['text_recaptcha_will_appear']		= 'reCAPTCHA will appear here';
$_['text_checkbox']						= 'Checkbox';
$_['text_radio']						= 'Radio';

$_['text_key']							= 'Key:';
$_['text_parent']						= 'Parent: <div class="help-text">Key:Value</div>';
$_['placeholder_setting_a_parent']		= 'Setting a Parent will hide this field';
$_['text_required']						= 'Required:';
$_['text_title']						= 'Title: <div class="help-text">HTML is supported.</div>';
$_['text_choices']						= 'Choices: <div class="help-text">Enter the choices customers can select, separated by ; (semi-colons)</div>';
$_['text_defaults']						= 'Default(s): <div class="help-text">Enter the choices selected by default, separated by ; (semi-colons)</div>';
$_['text_size']							= 'Size: <div class="help-text">Enter 1 for a regular dropdown, and a higher number to show a multi-select box.</div>';

$_['text_default_value']				= 'Default Value:';
$_['text_earliest_value']				= 'Earliest Value:';
$_['text_latest_value']					= 'Latest Value:';
$_['text_interval']						= 'Interval: <div class="help-text">Enter in minutes. Defaults to 60.</div>';
$_['placeholder_date_format']			= 'YYYY-MM-DD';
$_['placeholder_time_format']			= 'HH:MM (use 13-24 for PM)';

$_['text_placeholder']					= 'Placeholder Text:';
$_['text_require_confirmation']			= 'Require Confirmation:';
$_['text_confirm_title']				= 'Confirm Field Title: <div class="help-text">HTML is supported.</div>';
$_['text_confirm_placeholder']			= 'Confirm Field Placeholder Text:';

$_['text_button_text']					= 'Button Text: <div class="help-text">HTML is supported.</div>';
$_['text_success']						= 'Success Message: <div class="help-text">HTML is supported.</div>';
$_['text_file_limit']					= 'File Limit: <div class="help-text">Enter the number of files allowed to be uploaded. Leave blank to have no limit.</code></div>';

$_['text_data']							= 'Data:';
$_['text_display_in_customers_email']	= 'Display in Customer\'s E-mail:';

$_['text_type']							= 'Type:';
$_['text_text']							= 'Text';
$_['text_password']						= 'Password';
$_['text_textarea']						= 'Textarea';
$_['text_min_length']					= 'Min Length:';
$_['text_max_length']					= 'Max Length:';
$_['text_allowed_characters']			= 'Allowed Characters:';
$_['help_allowed_characters']			= '<div class="help-text">For example, to only allow numbers and hyphens, you would enter: 01234567890-</div>';

$_['text_please_wait']					= '"Please Wait" Text:';
$_['text_redirect_url']					= 'Redirect URL: <div class="help-text">Leave blank to stay on the same page.</div>';

//------------------------------------------------------------------------------
// Error Messages
//------------------------------------------------------------------------------
$_['tab_error_messages']				= 'Error Messages';

$_['entry_module_error_required']		= 'Required Fields: <div class="help-text">Displayed when all the required fields are not filled in.</div>';
$_['entry_module_error_captcha']		= 'Captcha: <div class="help-text">Displayed when the correct captcha code is not entered.</div>';
$_['entry_module_error_invalid_email']	= 'Invalid E-mail: <div class="help-text">Displayed when an invalid e-mail address is used in an e-mail field.</div>';
$_['entry_module_error_email_mismatch']	= 'E-mail Mismatch: <div class="help-text">Displayed when an e-mail field and its confirmation field do not match.</div>';
$_['entry_module_error_minlength']		= 'Minimum Length: <div class="help-text">Displayed when a response for a text field does not meet its minimum required length. Use [min] in place of the Min Length value.</div>';
$_['entry_module_error_file_name']		= 'File Name Length: <div class="help-text">Displayed when an uploaded file name is less than 3 or greater than 128 characters.</div>';
$_['entry_module_error_file_size']		= 'File Size: <div class="help-text">Displayed when an uploaded file size is greater than a file upload field maximum file size.</div>';
$_['entry_module_error_file_ext']		= 'File Extension: <div class="help-text">Displayed when an uploaded file extension does not match the allowed file extensions.</div>';
$_['entry_module_error_file_limit']		= 'File Limit: <div class="help-text">Displayed when the number of files uploaded exceeds the allowed limit.</div>';
$_['entry_module_error_file_upload']	= 'File Upload: <div class="help-text">Displayed for general file upload errors.</div>';

//------------------------------------------------------------------------------
// E-mail Settings
//------------------------------------------------------------------------------
$_['tab_email_settings']				= 'E-mail Settings';

$_['help_email_shortcodes']				= '
	<div class="well">
		For Subject and Message fields below, you can use a field\'s Key surrounded by square brackets [ and ] to insert the customer\'s response to that field. For example, if you had a field with a Key of "name", you could enter <code>[name]</code> to show the value for the "name" field.<br /><br />
		To insert a list of all form responses, use the <code>[form_responses]</code> shortcode. The full list of shortcodes you can use to insert other pieces of information is as follows:<br /><br />
		<table style="width: 100%; font-family: monospace">
			<tr><td>
				[store_name]<br />
				[store_url]<br />
				[store_owner]<br />
				[store_address]<br />
			</td><td>
				[store_email]<br />
				[store_telephone]<br />
				[store_fax]<br />
				[cart_contents]<br />
				[customer_ip]<br />
			</td><td>
				[current_date]<br />
				[current_time]<br />
				[form_name]<br />
				[form_responses]<br />
			</td></tr>
		</table>
	</div>
';
$_['entry_module_admin_email']			= 'Admin E-mail Address(es): <div class="help-text">Enter the e-mail address(es) where form responses are sent, separated by , (commas).</div>';
$_['entry_module_admin_subject']		= 'Admin E-mail Subject:';
$_['entry_module_admin_message']		= 'Admin E-mail Message:';
$_['entry_module_customer_email']		= 'E-mail Customer Their Responses: <div class="help-text">Select whether to e-mail the customer a copy of their responses, if the form includes an "E-mail Address" field type.</div>';
$_['entry_module_customer_subject']		= 'Customer E-mail Subject:';
$_['entry_module_customer_message']		= 'Customer E-mail Message:';

//------------------------------------------------------------------------------
// Restrictions
//------------------------------------------------------------------------------
$_['tab_restrictions']					= 'Restrictions';

$_['entry_module_stores']				= 'Store(s): <div class="help-text">Select the stores where this module will appear.</div>';
$_['entry_module_languages']			= 'Language(s): <div class="help-text">Select the languages for which this module will appear.</div>';
$_['entry_module_customer_groups']		= 'Customer Group(s): <div class="help-text">Select the customer groups for which this module will appear. The "Guests" checkbox applies to all customers not logged in to an account.</div>';
$_['entry_module_currencies']			= 'Currencies: <div class="help-text">Select the currencies for which this module will appear.</div>';

$_['text_guests']						= '<em>Guests</em>';

//------------------------------------------------------------------------------
// Standard Text
//------------------------------------------------------------------------------
$_['copyright']							= '<hr /><div class="text-center" style="margin: 15px">' . $_['heading_title'] . ' (' . $version . ') &copy; <a target="_blank" href="http://www.getclearthinking.com">Clear Thinking, LLC</a></div>';

$_['standard_autosaving_enabled']		= 'Auto-Saving Enabled';
$_['standard_confirm']					= 'This operation cannot be undone. Continue?';
$_['standard_error']					= '<strong>Error:</strong> You do not have permission to modify ' . $_['heading_title'] . '!';
$_['standard_max_input_vars']			= '<strong>Warning:</strong> The number of settings is close to your <code>max_input_vars</code> server value. You should enable auto-saving to avoid losing any data.';
$_['standard_please_wait']				= 'Please wait...';
$_['standard_saved']					= 'Saved!';
$_['standard_saving']					= 'Saving...';
$_['standard_select']					= '--- Select ---';
$_['standard_success']					= 'Success!';
$_['standard_testing_mode']				= 'Your log is too large to open! Clear it first, then run your test again.';
$_['standard_vqmod']					= '<strong>Warning:</strong> This extension requires vQmod, which is not installed on your store. Please download it from <a target="_blank" href="http://www.vqmod.com">www.vqmod.com</a> and follow the installation instructions.';

$_['standard_module']					= 'Modules';
$_['standard_shipping']					= 'Shipping';
$_['standard_payment']					= 'Payments';
$_['standard_total']					= 'Order Totals';
$_['standard_feed']						= 'Feeds';
?>