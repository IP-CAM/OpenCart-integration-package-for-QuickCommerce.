<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
</head>
<body style="padding:10px;">
<?php foreach ($orders as $order) { ?>
<div style="page-break-after: always;">
  <h1><?php echo $order['order_name']; ?></h1>
  <table class="table table-bordered table-striped table-hover">
    <tr>
      <td><?php echo $store_name; ?><br />
        <?php echo $store_address; ?><br />
        <?php echo $text_telephone; ?> <?php echo $store_telephone; ?><br />
        <?php if ($store_fax) { ?>
        <?php echo $text_fax; ?> <?php echo $store_fax; ?><br />
        <?php } ?>
        <?php echo $store_email; ?></td>
      <td align="text-right" valign="top"><table>
          <tr>
            <td style="width:150px;"><b><?php echo $text_date_added; ?></b></td>
            <td><?php echo $order['date_added']; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $text_order; ?></b></td>
            <td><?php echo $order['order_name']; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $text_payment_method; ?></b></td>
            <td><?php echo $order['purchase_order_payment']; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $text_shipping_method; ?></b></td>
            <td><?php echo $order['purchase_order_shipping']; ?></td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table class="table table-bordered table-hover table-striped">
    <tr class="heading">
      <td><b><?php echo $text_to; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $order['vendor']; ?><br />
	    <?php if ($order['address_1']) { ?>
	    <?php echo $order['address_1']; ?><br />
		<?php } ?>
		<?php if ($order['address_2']) { ?>
	    <?php echo $order['address_2']; ?><br />
		<?php } ?>
		<?php if ($order['city']) { ?>
	    <?php echo $order['city']; ?><br />
		<?php } ?>
		<?php if ($order['postcode']) { ?>
	    <?php echo $order['postcode']; ?><br />
		<?php } ?>
		<?php if ($order['country']) { ?>
	    <?php echo $order['country']; ?><br />
		<?php } ?>
		<?php if ($order['zone']) { ?>
	    <?php echo $order['zone']; ?><br />
		<?php } ?>
        <?php echo $order['email']; ?><br />
		<?php if ($order['telephone']) { ?>
        <?php echo $order['telephone']; ?>
		<?php } ?>
        <?php if ($order['fax']) { ?>
        <?php echo $order['fax']; ?>
		<?php } ?></td>
    </tr>
  </table>
  <table class="table table-bordered table-hover table-striped">
    <tr class="heading">
      <td><b><?php echo $entry_product; ?></b></td>
      <td><b><?php echo $entry_model; ?></b></td>
      <td align="right"><b><?php echo $entry_quantity; ?></b></td>
      <td align="right"><b><?php echo $entry_price; ?></b></td>
      <td align="right"><b><?php echo $entry_total; ?></b></td>
    </tr>
    <?php foreach ($order['products'] as $product) { ?>
    <tr>
      <td><?php echo $product['name']; ?>
        <?php foreach ($product['options'] as $option) { ?>
        <br />
        &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
        <?php } ?></td>
      <td><?php echo $product['model']; ?></td>
      <td align="right"><?php echo $product['quantity']; ?></td>
      <td align="right"><?php echo $product['price']; ?></td>
      <td align="right"><?php echo $product['total']; ?></td>
    </tr>
    <?php } ?>
    <?php foreach ($order['totals'] as $total) { ?>
    <tr>
      <td align="right" colspan="4"><b><?php echo $total['name']; ?>:</b></td>
      <td align="right"><?php echo $total['value']; ?></td>
    </tr>
    <?php } ?>
	<tr>
      <td align="right" colspan="4"><b><?php echo $entry_total; ?></b></td>
      <td align="right"><?php echo $order['order_total']; ?></td>
    </tr>
  </table>
  <?php if ($order['comment']) { ?>
  <table class="product">
    <tr class="heading">
      <td><b><?php echo $entry_comment; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $order['comment']; ?></td>
    </tr>
  </table>
  <?php } ?>
</div>
<?php } ?>
</body>
</html>