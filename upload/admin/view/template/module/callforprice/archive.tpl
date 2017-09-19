<table id="archiveWrapper<?php echo $store_id; ?>" class="table table-bordered table-hover" width="100%">
    <thead>
        <tr class="table-header">
			<th width="15%"><?php echo $table_custname; ?></th>
            <th width="15%"><?php echo $table_custphone; ?></th>
            <th width="15%"><?php echo $table_product; ?></th>
            <th width="15%"><?php echo $table_custnotes; ?></th>
			<th width="15%"><?php echo $table_adminnotes; ?></th>
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
                	<?php echo (!empty($source['anotes'])) ? $source['anotes'] : $text_empty; ?>
                </td>
        		<td>
                	<?php echo $source['date_created']; ?>
                </td>
        		<td>
                	<a onclick="removeCustomer('<?php echo $source['callforprice_id']; ?>')" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> <?php echo $button_remove; ?></a>
                </td>
       		</tr>
        <?php } ?>
	</tbody>
	<tfoot><tr><td colspan="8">
    	<br />
    	<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
    </td></tr></tfoot>
</table>
<div style="float:right;padding: 5px;">
	<a onclick="removeAllArchive()" class="btn btn-sm btn-warning"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo $button_removeall; ?></a>
</div>
<script>
$(document).ready(function(){
	$('#archiveWrapper<?php echo $store_id; ?> .pagination a').click(function(e){
		e.preventDefault();
		$.ajax({
			url: this.href,
			type: 'get',
			dataType: 'html',
			success: function(data) {				
				$("#archiveWrapper<?php echo $store_id; ?>").html(data);
			}
		});
	});		 
});
</script>