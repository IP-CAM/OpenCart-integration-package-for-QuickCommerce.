<?php if($moduleData['Enabled'] != 'no'): ?>   
    <form id="CFPForm">
        <input type="hidden" name="CFPProductID" value="<?php echo $CFPProductID; ?>" />     
  		<?php
        $string = html_entity_decode($moduleData['CustomText'][$language_id]);
        $patterns = array();
        $patterns[0] = '/{name_field}/';
        $patterns[1] = '/{phone_field}/';
        $patterns[2] = '/{notes_field}/';
        $patterns[3] = '/{submit_button}/';
        $replacements = array();
        $replacements[0] = '<input type="text" class="form-control" name="CFPYourName" id="CFPYourName" placeholder="'.$CFP_Name.'" value="" />';
        $replacements[1] = '<input type="text" class="form-control" name="CFPYourPhone" id="CFPYourPhone" placeholder="'.$CFP_Phone.'" value="" />';
        $replacements[2] = '<textarea class="form-control" name="CFPYourNotes" id="CFPYourNotes" placeholder="'.$CFP_Notes.'" value="" />';
        $replacements[3] = (($moduleData['UseCaptcha']=='yes') ? '<div class="g-recaptcha" style="margin-bottom:5px;" data-sitekey="'.$site_key.'"></div>' : '').'<a id="CFPSubmit" class="btn btn-primary">'.$CallForPrice_SubmitButton.'</a>';
        echo preg_replace($patterns, $replacements, $string);
        ?>
        <div class="CFP_loader"><div class="CFP_loading"></div></div>
        <div id="CFPSuccess"></div>  
    </form>
	<script>
    $('#CFPSubmit').on('click', function(){
        $('input#CFPYourName').removeClass("CFP_popover_field_error");
        $('input#CFPYourPhone').removeClass("CFP_popover_field_error");
        $('div.CFPError').remove();
    
        if ((document.getElementById("CFPYourName").value == 0) )
        {
              $('input#CFPYourName').addClass("CFP_popover_field_error");
              $('input#CFPYourName').after('<div class="CFPError"><?php echo $CallForPrice_Error1; ?></div>');
        } else if ((document.getElementById("CFPYourPhone").value.length == 0)) {
              $('input#CFPYourPhone').addClass("CFP_popover_field_error");
              $('input#CFPYourPhone').after('<div class="CFPError"><?php echo $CallForPrice_Error1; ?></div>');
        } else {
            $.ajax({
                url: 'index.php?route=module/callforprice/submitform',
                type: 'post',
                data: $('#CFPForm').serialize(),
				dataType: 'json',
                beforeSend: function(){
                      $('#CFPSubmit').hide();
                      $('.CFP_loader').show();
                },
                success: function(response) {
					if (response['error']) { 
						alert(response['error']);
						$('.CFP_loader').hide();
						$('#CFPSubmit').show();
					} else if (response['success']) {
						$('.CFP_loader').hide();
						$('#CFPSubmit').hide();
						$('#CFPSuccess').html("<div class='alert alert-success CFPsuccess' style='display: none;'><?php echo $CallForPrice_Success; ?></div>");
						$('.CFPsuccess').fadeIn('slow');
						$('#CFPYourName').val('');
						$('#CFPYourPhone').val('');
						$('#CFPYourNotes').val('');
					}
                }
            });
        }
    });
    </script>
    <?php if(!empty($moduleData['CustomCSS'])): ?>
		<style>
            <?php echo htmlspecialchars_decode($moduleData['CustomCSS']); ?>
        </style>
    <?php endif; ?>
    <?php if(($moduleData['UseCaptcha']=='yes')): ?>
		<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    <?php endif; ?>
<?php endif; ?>