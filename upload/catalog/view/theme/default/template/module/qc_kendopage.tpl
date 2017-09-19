<div>
  <?php if ($markup) { ?>
    <?php echo $markup; ?>
  <?php } ?>
</div>

<script src="<?php echo $path ?>vendor/kpaged/lib/require/require.js" type="text/javascript"></script>
<?php if ($bootstrap) { ?>
<script type="text/javascript"><!--
  $(document).ready(function () {
	$('#bt_container > .bt-breadcrumb').css({ marginBottom: 0 });
	$('#bt_container > .container').removeClass('container').css({ boxSizing: 'border-box' }).find('.row').css({ margin: 0 }).find('.content_bg').remove().end()
		.find('#content').css({ marginBottom: 0 });
  });
  
  <?php echo $bootstrap; ?>
//--></script>
<?php } ?>

<!--<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/src/styles/kendo.common.css" rel="stylesheet" type="text/css" />-->
<!--<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/src/styles/kendo.minimal.css" rel="stylesheet" type="text/css" />-->
<link href="<?php echo $path ?>vendor/kpaged/styles/kpaf-base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/builds/telerik.kendoui.professional.2015.1.408/styles/kendo.common.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/builds/telerik.kendoui.professional.2015.1.408/styles/kendo.fiori.min.css" rel="stylesheet" type="text/css" />
<!--<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/builds/telerik.kendoui.professional.2015.1.408/styles/kendo.metro.min.css" rel="stylesheet" type="text/css" />-->
<link href="<?php echo $path ?>vendor/kpaged/lib/kendo/builds/telerik.kendoui.professional.2015.1.408/styles/kendo.dataviz.min.css" rel="stylesheet" type="text/css" />