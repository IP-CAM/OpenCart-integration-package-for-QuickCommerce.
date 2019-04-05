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
  <?php foreach ($sales as $sale) { ?>
  <div style="page-break-after: always;">
    <h1><?php echo $text_invoice; ?> #<?php echo $sale['sale_id']; ?></h1>
    <table class="table table-bsaleed">
      <thead>
        <tr>
          <td colspan="2"><?php echo $text_sale_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width: 50%;"><address>
            <strong><?php echo $sale['store_name']; ?></strong><br />
            <?php echo $sale['store_address']; ?>
            </address>
            <b><?php echo $text_telephone; ?></b> <?php echo $sale['store_telephone']; ?><br />
            <?php if ($sale['store_fax']) { ?>
            <b><?php echo $text_fax; ?></b> <?php echo $sale['store_fax']; ?><br />
            <?php } ?>
            <b><?php echo $text_email; ?></b> <?php echo $sale['store_email']; ?><br />
            <b><?php echo $text_website; ?></b> <a href="<?php echo $sale['store_url']; ?>"><?php echo $sale['store_url']; ?></a></td>
          <td style="width: 50%;"><b><?php echo $text_date_added; ?></b> <?php echo $sale['date_added']; ?><br />
            <?php if ($sale['invoice_no']) { ?>
            <b><?php echo $text_invoice_no; ?></b> <?php echo $sale['invoice_no']; ?><br />
            <?php } ?>
            <b><?php echo $text_sale_id; ?></b> <?php echo $sale['sale_id']; ?><br />
            <b><?php echo $text_payment_method; ?></b> <?php echo $sale['payment_method']; ?><br />
            <?php if ($sale['shipping_method']) { ?>
            <b><?php echo $text_shipping_method; ?></b> <?php echo $sale['shipping_method']; ?><br />
            <?php } ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bsaleed">
      <thead>
        <tr>
          <td style="width: 50%;"><b><?php echo $text_to; ?></b></td>
          <td style="width: 50%;"><b><?php echo $text_ship_to; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><address>
            <?php echo $sale['payment_address']; ?>
            </address></td>
          <td><address>
            <?php echo $sale['shipping_address']; ?>
            </address></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bsaleed">
      <thead>
        <tr>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_model; ?></b></td>
          <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
          <td class="text-right"><b><?php echo $column_price; ?></b></td>
          <td class="text-right"><b><?php echo $column_total; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sale['product'] as $product) { ?>
        <tr>
          <td><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td><?php echo $product['model']; ?></td>
          <td class="text-right"><?php echo $product['quantity']; ?></td>
          <td class="text-right"><?php echo $product['price']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($sale['voucher'] as $voucher) { ?>
        <tr>
          <td><?php echo $voucher['description']; ?></td>
          <td></td>
          <td class="text-right">1</td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($sale['total'] as $total) { ?>
        <tr>
          <td class="text-right" colspan="4"><b><?php echo $total['title']; ?></b></td>
          <td class="text-right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if ($sale['comment']) { ?>
    <table class="table table-bsaleed">
      <thead>
        <tr>
          <td><b><?php echo $column_comment; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $sale['comment']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>
  </div>
  <?php } ?>
</div>
</body>
</html>