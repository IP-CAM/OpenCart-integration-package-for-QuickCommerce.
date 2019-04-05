<?php if ($modules) { ?>
<div class="column-right <?php echo $this->journal2->settings->get('flyout_column_right_active') ? 'flyout-right' : ''; ?>">
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
</div>
<?php } ?>
