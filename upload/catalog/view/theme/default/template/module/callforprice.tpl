<?php if($data['callforprice']['Enabled'] == 'yes'): ?>
	<div id="CFP_popup" style="display:none;width:<?php echo $data['callforprice']['PopupWidth']; ?>px;" class="CFP_popover bottom">
		<div class="arrow"></div>
		<h3 class="CFP_popover-title"><?php echo $CustomTitle; ?></h3>
		<div class="CFP_popover-content">
		</div>
	</div>
	
	<script>
		$(document).click(function(event) {
	       if (!$(event.target).is("#CFP_popup, .CFP_popover-title, .arrow, .CFP_popover-content, #CFPYourName, #CFPYourPhone, #CFPYourNotes, #CFPSubmit, #CFP_popup p, #CFP_popup span, .CFP_popover, #CFPForm, .CFPError, input.CFP_popover_field_error, #button-cart")) {
	            $('div#CFP_popup').fadeOut('slow');
	       }
	    });	
    </script>
<?php endif; ?>