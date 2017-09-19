<!--
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------
-->
<?php if (!empty($error)) { ?>
<div class="warning"><?php echo $error; ?></div>
<?php } ?>

<iframe id="payment_iframe" name="payment_iframe" width="50%" height="450px" frameBorder="0" style="display:none; margin-left:50%;"></iframe>

<form action="<?php echo $action; ?>" method="post" id="checkout-form" target="payment_iframe">
<?php foreach ($fields as $key => $value) { ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
  <?php } ?>
</form>

<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
  $('#payment_iframe').slideDown();
  $('#button-confirm').hide();
  $('#checkout-form').submit();
});
//--></script>