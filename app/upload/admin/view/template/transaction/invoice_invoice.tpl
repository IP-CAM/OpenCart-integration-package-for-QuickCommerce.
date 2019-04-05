<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
</head>
<body>
<div class="container">
  <?php foreach ($invoices as $invoice) { ?>
  <div style="page-break-after: always;">
    <h1><?php echo $text_invoice; ?> #<?php echo $invoice['order_id']; ?></h1>
	<table class="table table-bordered">
      <thead>
        <tr>
          <td colspan="2"><?php echo $text_order_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width: 50%;"><address>
            <strong><?php echo $invoice['store_name']; ?></strong><br />
            <?php echo $invoice['store_address']; ?>
            </address>
            <b><?php echo $text_telephone; ?></b> <?php echo $invoice['store_telephone']; ?><br />
            <?php if ($invoice['store_fax']) { ?>
            <b><?php echo $text_fax; ?></b> <?php echo $invoice['store_fax']; ?><br />
            <?php } ?>
            <b><?php echo $text_email; ?></b> <?php echo $invoice['store_email']; ?><br />
            <b><?php echo $text_website; ?></b> <a href="<?php echo $invoice['store_url']; ?>"><?php echo $invoice['store_url']; ?></a></td>
          <td style="width: 50%;"><b><?php echo $text_date_added; ?></b> <?php echo $invoice['date_added']; ?><br />
            <?php if ($invoice['invoice_no']) { ?>
            <b><?php echo $text_invoice_no; ?></b> <?php echo $invoice['invoice_no']; ?><br />
            <?php } ?>
            <b><?php echo $text_order_id; ?></b> <?php echo $invoice['order_id']; ?><br />
            <b><?php echo $text_payment_method; ?></b> <?php echo $invoice['payment_method']; ?><br />
            <?php if ($invoice['shipping_method']) { ?>
            <b><?php echo $text_shipping_method; ?></b> <?php echo $invoice['shipping_method']; ?><br />
            <?php } ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td style="width: 50%;"><b><?php echo $text_to; ?></b></td>
          <td style="width: 50%;"><b><?php echo $text_ship_to; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><address>
            <?php echo $invoice['payment_address']; ?>
            </address></td>
          <td><address>
            <?php echo $invoice['shipping_address']; ?>
            </address></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_model; ?></b></td>
          <td class="text-right"><?php echo $column_quantity; ?></td>
          <td class="text-right"><?php echo $column_price; ?></td>
          <?php /*
          <td class="text-right"><?php echo $column_revenue; ?></td>
          <td class="text-right"><?php echo $column_track; ?></td>
          <td class="text-right"><?php echo $column_royalty; ?></td>
          */ ?>
          <td class="text-right"><?php echo $column_total; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoice['lines'] as $line) { ?>
        <tr>
          <td><?php echo $line['name']; ?>
            <?php foreach ($line['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td><?php echo $line['model']; ?></td>
          <td class="text-right"><?php echo $line['quantity']; ?></td>
          <td class="text-right"><?php echo $line['price']; ?></td>
          <?php /*
          <td class="text-right"><?php echo (isset($line['revenue'])) ? $line['revenue'] : ''; ?></td>
          <td class="text-right"><?php echo (isset($line['track'])) ? $line['track'] : ''; ?></td>
          <td class="text-right"><?php echo (isset($line['royalty'])) ? $line['royalty'] : ''; ?></td>
          */ ?>
          <td class="text-right"><?php echo $line['total']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($invoice['voucher'] as $voucher) { ?>
        <tr>
          <td><?php echo $voucher['description']; ?></td>
          <td></td>
          <td class="text-right">1</td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($invoice['total'] as $total) { ?>
        <tr>
          <td class="text-right" colspan="4"><b><?php echo $total['title']; ?></b></td>
          <td class="text-right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if (!empty($invoice['customer_memo'])) { ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b><?php echo 'Customer Memo'; //$column_comment; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $invoice['customer_memo']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>
  </div>
  <?php } ?>
</div>
</body>
</html>