<table id="ordersWrapper<?php echo $store_id; ?>" class="table table-bordered table-hover" width="100%">
    <thead>
        <tr class="table-header">
			<th width="20%"><?php echo $table_custname; ?></th>
            <th width="15%"><?php echo $table_custphone; ?></th>
            <th width="15%"><?php echo $table_product; ?></th>
            <th width="25%"><?php echo $table_custnotes; ?></th>
            <th width="10%"><?php echo $table_date; ?></th>
            <th width="5%"><?php echo $table_actions; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($sources as $source) { ?>
        	<tr>
				<td>
                	<?php echo $source['customer_name']; ?>
                </td>
        		<td>
                	<?php echo $source['customer_phone']; ?>
                </td>
        		<td>
                	<a href="<?php echo '../index.php?route=product/product&product_id='.$source['product_id']; ?>" target="_blank"><strong><?php echo $source['product_name']; ?></strong></a>
                </td>
                <td>
                	<?php echo (!empty($source['notes'])) ? $source['notes'] : $text_empty; ?>
                </td>
        		<td>
                	<?php echo $source['date_created']; ?>
                </td>
        		<td>
                	<a onclick="moveCustomer('<?php echo $source['callforprice_id']; ?>')" class="btn btn-xs btn-primary"><i class="fa fa-share"></i> <?php echo $button_move; ?></a>
                	<br /><a onclick="removeCustomer('<?php echo $source['callforprice_id']; ?>')" style="margin-top:3px;" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> <?php echo $button_remove; ?></a>
                </td>
       		</tr>
        <?php } ?>
	</tbody>
	<tfoot><tr><td colspan="6">
    	<br />
    	<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
    </td></tr></tfoot>
</table>
<div style="float:right;padding: 5px;">
	<a onclick="moveAll()" class="btn btn-sm btn-info"><i class="fa fa-share"></i>&nbsp;&nbsp;<?php echo $button_moveall; ?></a>
	<a onclick="removeAll()" class="btn btn-sm btn btn-warning"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo $button_removeall; ?></a>
</div>
<div class="modal fade modal-notes" tabindex="-1" role="dialog" aria-labelledby="notesModal" aria-hidden="true">
   <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $button_move; ?></h4>
      </div>
      <div class="modal-body">
        <?php echo $text_modal_1; ?> 
        <br />
        <textarea id="notes-data" data-id="0" class="form-control" rows="3"></textarea>
        <br />
        <small><?php echo $text_modal_2; ?></small> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
        <button type="button" class="btn btn-primary" onClick="moveAJAXCustomer($('#notes-data').attr('data-id'),$('#notes-data').val());"><?php echo $button_move; ?></button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
	$('#ordersWrapper<?php echo $store_id; ?> .pagination a').click(function(e){
		e.preventDefault();
		$.ajax({
			url: this.href,
			type: 'get',
			dataType: 'html',
			success: function(data) {				
				$("#ordersWrapper<?php echo $store_id; ?>").html(data);
			}
		});
	});		 
});
</script>