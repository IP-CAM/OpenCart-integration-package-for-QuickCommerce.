<?php if ($modules) { ?>
<div class="column-left <?php echo $this->journal2->settings->get('flyout_column_left_active') ? 'flyout-left' : ''; ?>">
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
</div>
<?php } ?>