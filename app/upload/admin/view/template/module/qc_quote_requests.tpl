<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-qc-admin" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="display display-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="display">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-incoming" data-toggle="tab"><?php echo $tab_incoming; ?></a></li>
					<li><a href="#tab-archive" data-toggle="tab"><?php echo $tab_archive; ?></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab-incoming">
						<div style="display: flex; flex-flow: row wrap">
							<div style="flex: 1 1 48%">
								<!--<h4>Incoming</h4>-->
								<?php echo $tab_incoming_content; ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab-archive">
						<div style="display: flex; flex-flow: row wrap">
							<div style="flex: 1 1 48%">
								<!--<h4>Archive</h4>-->
								<?php echo $tab_archive_content; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
	  <?php echo $footer; ?>
    <style scoped>
    .control-label .help {
      font-weight: normal !important;
    }

    label.control-label span::after {
      content: none;
    }
    </style>