<?php
// Heading
$_['heading_title']    		= 'Canada Post WS/REST(Q)';

// Text
$_['text_shipping']    		= 'Shipping';
$_['text_success']     		= 'Success: You have modified the shipping extension!';
$_['text_counter']    		= 'Counter';
$_['text_commercial']    	= 'Commercial';
$_['text_volumetric']    	= 'Volumetric';
$_['text_linear']    		= 'Linear';
$_['text_static']    		= 'Static';
$_['text_edit']    			= 'Edit Shipping';


$_['text_DOM.RP']      		= 'CA - Regular Parcel';
$_['text_DOM.EP']      		= 'CA - Expedited Parcel';
$_['text_DOM.XP']      		= 'CA - Xpresspost';
$_['text_DOM.XP.CERT'] 		= 'CA - Xpresspost Certified';
$_['text_DOM.PC']      		= 'CA - Priority';
$_['text_DOM.LIB']     		= 'CA - Library Books';
$_['text_USA.EP']      		= 'US - Expedited Parcel USA';
$_['text_USA.PW.ENV']  		= 'US - Priority Worldwide Envelope USA';
$_['text_USA.PW.PAK']   	= 'US - Priority Worldwide pak USA';
$_['text_USA.PW.PARCEL']    = 'US - Priority Worldwide Parcel USA';
$_['text_USA.SP.AIR']    	= 'US - Small Packet USA Air';
$_['text_USA.SP.SURF']    	= 'US - Small Packet USA Surface';
$_['text_USA.TP']    	   	= 'US - Tracked Packet';
$_['text_USA.XP']    	   	= 'US - Xpresspost USA';
$_['text_INT.TP']    	   	= 'INT - Tracked Packet';
$_['text_INT.XP']    	   	= 'INT - Xpresspost International';
$_['text_INT.IP.AIR']    	= 'INT - International Parcel Air';
$_['text_INT.IP.SURF']    	= 'INT - International Parcel Surface';
$_['text_INT.PW.ENV']    	= 'INT - Priority Worldwide Envelope Intl';
$_['text_INT.PW.PAK']    	= 'INT - Priority Worldwide pak Intl';
$_['text_INT.PW.PARCEL']    = 'INT - Priority Worldwide parcel Intl';
$_['text_INT.SP.AIR']    	= 'INT - Small Packet International Air';
$_['text_INT.SP.SURF']    	= 'INT - Small Packet International Surface';

$_['tab_debug']		   		= 'Debug';
$_['tab_support']		   	= 'Support';

// Entry
$_['entry_title']   		= 'Title:';
$_['entry_postcode']   		= 'Origin Post Code:';
$_['entry_mid']        		= 'API Username:';
$_['entry_key']        		= 'API Password:';
$_['entry_customer_number'] = 'Customer Number:';
$_['entry_adjust'] 			= 'Rate Adjust:';
$_['entry_service']    		= 'Services:';
$_['entry_geo_zone']   		= 'Geo Zone:';
$_['entry_tax_class']       = 'Tax Class:';
$_['entry_geo_zone']   		= 'Geo Zone:';
$_['entry_status']     		= 'Status:';
$_['entry_sort_order'] 		= 'Sort Order:';
$_['entry_quote_type'] 		= 'Quote Type:';
$_['entry_contract_id'] 	= 'Contract ID:';
$_['entry_debug']  	   		= 'Debug Logging:';
$_['entry_debug_file'] 		= 'Debug File:';
$_['entry_shipping_calc']	= 'Shipping Calculation Method:';
$_['entry_test'] 	   		= 'Dev Server:';
$_['entry_length'] 	   		= 'Avg Length (cm):';
$_['entry_width'] 	   		= 'Avg Width (cm):';
$_['entry_height'] 	   		= 'Avg Height (cm):';
$_['entry_display_weight'] 	= 'Display Weight Next to Rate:';
$_['entry_display_dims'] 	= 'Display Dimensions Next to Rate:';
$_['entry_display_date'] 	= 'Display Delivery Date Next to Rate:';
$_['entry_display_errors']  = 'Display Errors:';
$_['entry_lettermail'] 		= 'CA Lettermail Rates:';
$_['entry_lettermail_us'] 	= 'US Lettermail Rates:';
$_['entry_lettermail_int'] 	= 'Intl Lettermail Rates:';
$_['entry_signature'] 		= 'Rates include Signature:';
$_['entry_insurance'] 		= 'Rates include Insurance:';
$_['entry_cutoff']  		= 'Cutoff Time:';

// Tooltip
$_['tooltip_postcode']   		= 'This is your origin postcode. Must be based in Canada.';
$_['tooltip_mid']        		= 'This is the first half of your API key, before the ":" colon. Be sure you use the correct one for which server you selected.';
$_['tooltip_key']        		= 'This is the second half of your API key, after the ":" colon. Be sure you use the correct one for which server you selected.';
$_['tooltip_customer_number'] 	= 'Get this from your Canada Post "My Profile" page near the top. This is the same for either server.';
$_['tooltip_adjust'] 			= 'Apply a fee or discount to each rate. +/- Flat value or %. (ex 10%, 1.25, -2, -5%)';
$_['tooltip_geo_zone']   		= '';
$_['tooltip_tax']        		= '';
$_['tooltip_status']     		= '';
$_['tooltip_service']     		= 'Choose the services you want to offer to customers during checkout';
$_['tooltip_sort_order'] 		= '';
$_['tooltip_quote_type'] 		= 'Counter = Standard Rates<br/> Commercial = Discounted Rates for your account';
$_['tooltip_contract_id'] 		= 'Needed for Commercial Discounted Rates';
$_['tooltip_debug']				= 'Logs messages between store and gateway for troubleshooting to the system/logs folder in FTP.';
$_['tooltip_debug_file'] 		= '';
$_['tooltip_shipping_calc']		= 'Canada Post WS does not support individual package rates at this time so choose your best alternative.';
$_['tooltip_test'] 	   			= 'Yes = Dev server<br/>No = Prod Server. Only use Dev server if you have a Developer account.';
$_['tooltip_length'] 	   		= 'Used if Volumetric Shipping = NO or as a fallback for 0 volume<br/>(ex 5)';
$_['tooltip_width'] 	   		= 'Used if Volumetric Shipping = NO or as a fallback for 0 volume<br/>(ex 5)';
$_['tooltip_height'] 	   		= 'Used if Volumetric Shipping = NO or as a fallback for 0 volume<br/>(ex 5)';
$_['tooltip_display_weight'] 	= 'Show weight next to the rate title during checkout';
$_['tooltip_display_dims'] 		= 'Show dimensions (2x3x4) next to the rate title during checkout';
$_['tooltip_display_date'] 		= 'Show delivery date next to th rate title during checkout. Format is based on your language file date format.';
$_['tooltip_display_errors']  	= 'Show/Hide errors on the checkout page when there are no rates. For example instead of "No rates available" it will just hide. Enable this when debugging problems.';
$_['tooltip_lettermail'] 		= 'Leave blank to always return premium rates for cart weight less than 500g';
$_['tooltip_lettermail_us'] 	= 'Leave blank to always return premium rates for cart weight less than 500g';
$_['tooltip_lettermail_int'] 	= 'Leave blank to always return premium rates for cart weight less than 500g';
$_['tooltip_tax_class'] 		= 'Which Tax class to use for taxing the shipping rate';
$_['tooltip_geo_zone'] 			= 'Which customer geo zone is allowed to see this shipping option';
$_['tooltip_signature'] 		= 'Rates will be rated with signature required flag';
$_['tooltip_insurance'] 		= 'Rates will be rated with the cart subtotal as the insured amount';
$_['tooltip_cutoff'] 			= 'If showing Transit Times and the time is already past this hour, then add a day to the shipping time. This is a based on a 24-hour clock and based on the server time.';

// Help
$_['help_shipping_calc'] 	= 'Volumetric - Each product\'s dimensions are multiplied together, summed together, and cube-rooted (eg. 125 cuberoot = 5 so dimensions will be 5x5x5).<br/>Linear - Finds the longest and widest item in the cart and sums all the heights. It assumes height will always be the shortest way to stack items in the box.<br/>Static - Override with the static Avg measurements below.';
$_['help_lettermail']  		= 'The Canada Post Rate server only offers premium rates. For packages less than 500g, you can set up Lettermail rates for your customers to select from. The format is weight:cost, weight:cost. All weights are in grams. (ex. 30:0.61, 50:1.05, 100:1.29, 200:2.10, 300:2.95, 400:3.40, 500:3.65). The weight implies the max weight for that rate. <a href="http://www.canadapost.ca/tools/pg/supportdocuments/lm_pricesheet-e.pdf" target="_blank">Canada Rates</a>';
$_['help_lettermail_us']  	= 'The Canada Post Rate server only offers premium rates. For packages less than 500g, you can set up Lettermail rates for your customers to select from. The format is weight:cost, weight:cost. All weights are in grams. (ex. 30:1.05, 50:1.29, 100:2.10, 200:3.70, 500:7.40). The weight implies the max weight for that rate. <a href="http://www.canadapost.ca/cpo/mc/business/productsservices/letterpost.jsf" target="_blank">US & INTL Rates</a>';
$_['help_lettermail_int']  	= 'The Canada Post Rate server only offers premium rates. For packages less than 500g, you can set up Lettermail rates for your customers to select from. The format is weight:cost, weight:cost. All weights are in grams. (ex. 30:1.80, 50:2.58, 100:4.20, 200:7.40, 500:14.80). The weight implies the max weight for that rate. <a href="http://www.canadapost.ca/cpo/mc/business/productsservices/letterpost.jsf" target="_blank">US & INTL Rates</a>';
$_['help_debug'] 			= '<b style="color:red;">Enable this setting when having issues and send log to Developer when contacting for support. The file is located at "system/logs/'.basename(__FILE__, '.php').'_debug.txt" accessible via FTP. You may also need to send this file to FedEx Tech support.</b>';
$_['help_service'] 			= 'Note: Not all selected services will always be displayed to the customer. Some services are limited based on actual package dimensions and locations. This simply enables the ability to show them, if they are returned. If you are not getting any rates, try checking a few more services.';
$_['help_cutoff'] 			= 'Current Server time is: '.date("H:m:s");

// Error
$_['error_fields'] 			= 'mid,postcode,service,key,customer_number,length,width,height';
$_['error_permission'] 		= 'Warning: You do not have permission to modify Canada Post shipping!';
$_['error_postcode']   		= 'Field Required!';
$_['error_mid']  	   		= 'Field Required!';
$_['error_key']  	   		= 'Field Required!';
$_['error_customer_number'] = 'Field Required!';
$_['error_service']    		= 'You must select at least one service!';
?>