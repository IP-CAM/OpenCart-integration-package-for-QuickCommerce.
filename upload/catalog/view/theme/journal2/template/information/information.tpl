<?php echo $header; ?>
<div id="container" class="container j-container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row">
    <div class="col-sm-12">
      <h1 class="heading-title"><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <?php echo $content_top; ?>
    </div>
  </div>
  <div class="row"><?php echo $column_left; ?><?php echo $column_right; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <!--<h1 class="heading-title"><?php echo $heading_title; ?></h1>-->
      <?php /*
      <?php echo $content_top; ?>
      */ ?>
      <?php echo $description; ?>
      <?php echo $content_bottom; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?> 