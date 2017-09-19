<?php
//-----------------------------------------
// Original Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
// Updated for OpenCart 2 by lucaslopatka@gmail.com
//-----------------------------------------

// Heading
$_['heading_title']      = 'Global Transport Secure (Q/QuickCommerce)';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified the account details!';
$_['text_development']   = '<span style="color: green;">Ready</span>';
$_['text_auth']          = 'Auth Only';
$_['text_capture']       = 'Auth & Capture';
$_['text_yes']      	 = 'Yes';
$_['text_no']      		 = 'No';

// Entry
$_['entry_status']       = 'Status:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_order_status'] = 'Successful Order Status:<br/><span class="help">The status of the order upon successful payment.</span>';
$_['entry_mid']          = 'Profile ID:<br/><span class="help">Get this from the Transport Secure Boarding site.</span>';
$_['entry_txntype']      = 'Txn Type:<br/><span class="help">AUTH - Authorization only.  Requires you to login and manually complete from your FMX account. AUTHPOST - Authorization and capture immediately</span>';
$_['entry_sort_order']   = 'Sort Order:';
$_['entry_test']         = 'Test Mode:';
$_['entry_ajax']         = 'Ajax Pre-Confirm';
$_['entry_debug']        = 'Debug Logging:<br/><span class="help">Logs transaction messages to "system/logs/'.basename(__FILE__, '.php').'_debug.txt" file (in ftp) for troubleshooting. Please send the logged messaging when contacting the developer for help.</span>';
$_['entry_total']   	 = 'Min Total:<br /><span class="help">The min total the cart must be to show this payment option. Recommend set to 0.01 or higher.</span>';
$_['entry_title']        = 'Title:<br/><span class="help">The title shown on the Payment step during checkout</span>';

// Help
$_['help_ajax']          = 'Pre-confirm the order upon clicking the confirmation button before sending to the gateway. This is only needed if there are server communication problems with the gateway and orders are being lost. By pre-confirming the order, the order starts in a pending state, then upon gateway return, the final order status is updated. (Recommended: DISABLED)';
$_['help_debug']         = 'Debug mode is used when there are payment or order update issues to help track down where the problem is. This usually includes saving logs or sending emails to the store email with extra information. (Recommended: DISABLED)';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify this payment module!';
$_['error_mid']          = 'Field required!';
$_['error_key']          = 'Field required!';
$_['error_desc']         = 'Field required!';

// Text
$_['text_payment']				  	 = 'Payment';
$_['text_success']					 = 'Success: You have modified Globalpay account details!';
$_['text_edit']                      = 'Edit Global Transport Secure';
$_['text_live']						 = 'Live';
$_['text_demo']						 = 'Demo';
$_['text_card_type']				 = 'Card type';
$_['text_enabled']					 = 'Enabled';
$_['text_use_default']				 = 'Use default';
$_['text_merchant_id']				 = 'Merchant ID';
$_['text_subaccount']				 = 'Subaccount';
$_['text_secret']					 = 'Shared secret';
$_['text_card_visa']				 = 'Visa';
$_['text_card_master']				 = 'Mastercard';
$_['text_card_amex']				 = 'American Express';
$_['text_card_switch']				 = 'Switch/Maestro';
$_['text_card_laser']				 = 'Laser';
$_['text_card_diners']				 = 'Diners';
$_['text_capture_ok']				 = 'Capture was successful';
$_['text_capture_ok_order']			 = 'Capture was successful, order status updated to success - settled';
$_['text_rebate_ok']				 = 'Rebate was successful';
$_['text_rebate_ok_order']			 = 'Rebate was successful, order status updated to rebated';
$_['text_void_ok']					 = 'Void was successful, order status updated to voided';
$_['text_settle_auto']				 = 'Auto';
$_['text_settle_delayed']			 = 'Delayed';
$_['text_settle_multi']				 = 'Multi';
$_['text_url_message']				 = 'You must supply the store URL to your Globalpay account manager before going live';
$_['text_payment_info']				 = 'Payment information';
$_['text_capture_status']			 = 'Payment captured';
$_['text_void_status']				 = 'Payment voided';
$_['text_rebate_status']			 = 'Payment rebated';
$_['text_order_ref']				 = 'Order ref';
$_['text_order_total']				 = 'Total authorised';
$_['text_total_captured']			 = 'Total captured';
$_['text_transactions']				 = 'Transactions';
$_['text_column_amount']			 = 'Amount';
$_['text_column_type']				 = 'Type';
$_['text_column_date_added']		 = 'Created';
$_['text_confirm_void']				 = 'Are you sure you want to void the payment?';
$_['text_confirm_capture']			 = 'Are you sure you want to capture the payment?';
$_['text_confirm_rebate']			 = 'Are you sure you want to rebate the payment?';
$_['text_globalpay']                 = '<a target="_blank" href="https://resourcecentre.globaliris.com/getting-started.php?id=OpenCart"><img src="view/image/payment/globalpay.png" alt="Globalpay" title="Globalpay" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant_id']				 = 'Merchant ID';
$_['entry_secret']					 = 'Shared secret';
$_['entry_rebate_password']			 = 'Rebate password';
$_['entry_total']					 = 'Total';
$_['entry_sort_order']				 = 'Sort order';
$_['entry_geo_zone']				 = 'Geo zone';
$_['entry_status']					 = 'Status';
$_['entry_debug']					 = 'Debug logging';
$_['entry_live_demo']				 = 'Live / Demo';
$_['entry_auto_settle']				 = 'Settlement type';
$_['entry_card_select']				 = 'Select card';
$_['entry_tss_check']				 = 'TSS checks';
$_['entry_live_url']				 = 'Live connection URL';
$_['entry_demo_url']				 = 'Demo connection URL';
$_['entry_status_success_settled']	 = 'Success - settled';
$_['entry_status_success_unsettled'] = 'Success - not settled';
$_['entry_status_decline']			 = 'Decline';
$_['entry_status_decline_pending']	 = 'Decline - offline auth';
$_['entry_status_decline_stolen']	 = 'Decline - lost or stolen card';
$_['entry_status_decline_bank']		 = 'Decline - bank error';
$_['entry_status_void']				 = 'Voided';
$_['entry_status_rebate']			 = 'Rebated';
$_['entry_notification_url']		 = 'Notification URL';

// Help
$_['help_total']					 = 'The checkout total the order must reach before this payment method becomes active';
$_['help_card_select']				 = 'Ask the user to choose their card type before they are redirected';
$_['help_notification']				 = 'You need to supply this URL to Globalpay to get payment notifications';
$_['help_debug']					 = 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise';
$_['help_dcc_settle']				 = 'If your subaccount is DCC enabled you must use Autosettle';

// Tab
$_['tab_api']					     = 'API Details';
$_['tab_account']		     		 = 'Accounts';
$_['tab_order_status']				 = 'Order status';
$_['tab_payment']					 = 'Payment settings';
$_['tab_advanced']					 = 'Advanced';

// Button
$_['button_capture']				 = 'Capture';
$_['button_rebate']					 = 'Rebate / refund';
$_['button_void']					 = 'Void';

// Error
$_['error_merchant_id']				 = 'Merchant ID is required';
$_['error_secret']					 = 'Shared secret is required';
$_['error_live_url']				 = 'Live URL is required';
$_['error_demo_url']				 = 'Demo URL is required';
$_['error_data_missing']			 = 'Data missing';
$_['error_use_select_card']			 = 'You must have "Select Card" enabled for subaccount routing by card type to work';