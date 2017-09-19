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
  <?php foreach ($invoices as $invoice) { 
  //var_dump($invoice); // TODO: Still got order ids in here!
  ?>
  <div style="page-break-after: always;">
    <h1><?php echo $text_picklist; ?> #<?php echo $invoice['order_id']; ?></h1>
    <table class="table table-binvoiceed">
      <thead>
        <tr>
          <td style="width: 50%;"><?php echo $text_from; ?></td>
          <td style="width: 50%;"><?php echo $text_invoice_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><address>
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
            <b><?php echo $text_invoice_id; ?></b> <?php echo $invoice['invoice_id']; ?><br />
            <?php if ($invoice['shipping_method']) { ?>
            <b><?php echo $text_shipping_method; ?></b> <?php echo $invoice['shipping_method']; ?><br />
            <?php } ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-binvoiceed">
      <thead>
        <tr>
          <td style="width: 50%;"><b><?php echo $text_to; ?></b></td>
          <td style="width: 50%;"><b><?php echo $text_contact; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $invoice['shipping_address']; ?></td>
          <td><?php echo $invoice['email']; ?><br/>
            <?php echo $invoice['telephone']; ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-binvoiceed">
      <thead>
        <tr>
          <td><b><?php echo $column_location; ?></b></td>
          <td><b><?php echo $column_reference; ?></b></td>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_weight; ?></b></td>
          <td><b><?php echo $column_model; ?></b></td>
          <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoice['lines'] as $line) { ?>
        <tr>
          <td><?php echo (isset($line['product']['location'])) ? $line['product']['location'] : ''; ?></td>
          <td><?php if (isset($line['product']) && !empty($line['product']['sku'])) { ?>
            <?php echo $text_sku; ?> <?php echo $line['product']['sku']; ?><br />
            <?php } ?>
            <?php if (isset($line['product']) && !empty($line['product']['upc'])) { ?>
            <?php echo $text_upc; ?> <?php echo $line['product']['upc']; ?><br />
            <?php } ?>
            <?php if (isset($line['product']) && !empty($line['product']['ean'])) { ?>
            <?php echo $text_ean; ?> <?php echo $line['product']['ean']; ?><br />
            <?php } ?>
            <?php if (isset($line['product']) && !empty($line['product']['jan'])) { ?>
            <?php echo $text_jan; ?> <?php echo $line['product']['jan']; ?><br />
            <?php } ?>
            <?php if (isset($line['product']) && !empty($line['product']['isbn'])) { ?>
            <?php echo $text_isbn; ?> <?php echo $line['product']['isbn']; ?><br />
            <?php } ?>
            <?php if (isset($line['product']) && !empty($line['product']['mpn'])) { ?>
            <?php echo $text_mpn; ?><?php echo $line['product']['mpn']; ?><br />
            <?php } ?></td>
            <td><?php echo $line['name']; ?>
            <?php /*foreach ($line['product']['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php }*/ ?></td>
          <td><?php echo (isset($line['product']) && !empty($line['product']['weight'])) ? $line['product']['weight'] : ''; ?></td>
          <td><?php echo $line['name']; ?></td>
          <td class="text-right"><?php echo $line['quantity']; ?></td>
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