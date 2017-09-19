<div class="panel panel-default">
  <div class="panel-heading">
	<div class="pull-right">
      <?php /*
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-calendar"></i> <i class="caret"></i></a>
	  <ul id="range" class="dropdown-menu dropdown-menu-right">
        <li><a href="day"><?php echo $text_day; ?></a></li>
        <li><a href="week"><?php echo $text_week; ?></a></li>
        <li class="active"><a href="month"><?php echo $text_month; ?></a></li>
        <li><a href="year"><?php echo $text_year; ?></a></li>
      </ul>
	  */ ?>
	  <div class="actions" style="display: inline-block">
	    <a href="#" data-toggle="tooltip" title="" class="btn btn-info"><i class="fa fa-refresh"></i></a>
	    <a href="#" data-toggle="tooltip" title="" class="btn btn-warning"><i class="fa fa-eraser"></i></a>
	  </div>
    </div>
    <h3 class="panel-title"><i class="fa fa-refresh"></i> <?php echo $heading_title; ?></h3>
  </div>
  
  <ul class="list-group">
    <?php if ($activities) { ?>
    <?php foreach ($activities as $activity) { ?>
    <li class="list-group-item">
	  <?php echo $activity['comment']; ?><br />
      <small class="text-muted"><i class="fa fa-clock-o"></i> <?php echo $activity['date_added']; ?></small>
	</li>
    <?php } ?>
    <?php } else { ?>
    <li class="list-group-item text-center"><?php echo $text_no_results; ?></li>
    <?php } ?>
  </ul>
</div>