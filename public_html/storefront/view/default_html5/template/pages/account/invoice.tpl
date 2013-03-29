<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<div class="container">
      <table class="table table-striped table-bordered">
        <tr>
          <td><?php if ($invoice_id) { ?>
            <b><?php echo $text_invoice_id; ?></b><br />
            <?php echo $invoice_id; ?><br />
            <br />
            <?php } ?>
            <b><?php echo $text_order_id; ?></b><br />
            #<?php echo $order_id; ?><br />
            <br />
            <b><?php echo $text_email; ?></b><br />
            <?php echo $email; ?><br />
            <br />
            <b><?php echo $text_telephone; ?></b><br />
            <?php echo $telephone; ?><br />
            <br />
            <?php if ($fax) { ?>
            <b><?php echo $text_fax; ?></b><br />
            <?php echo $fax; ?><br />
            <br />
            <?php } ?>
            <?php if ($shipping_method) { ?>
            <b><?php echo $text_shipping_method; ?></b><br />
            <?php echo $shipping_method; ?><br />
            <br />
            <?php } ?>
            <b><?php echo $text_payment_method; ?></b><br />
            <?php echo $payment_method; ?></td>
          <td><?php if ($shipping_address) { ?>
            <b><?php echo $text_shipping_address; ?></b><br />
            <?php echo $shipping_address; ?><br />
            <?php } ?></td>
          <td><b><?php echo $text_payment_address; ?></b><br />
            <?php echo $payment_address; ?><br /></td>
        </tr>
      </table>
	</div>
    <div class="container">
		<table class="table table-striped table-bordered">
		  <tr>
		    <th align="left"><?php echo $text_product; ?></th>
		    <th align="left"><?php echo $text_model; ?></th>
		    <th align="right"><?php echo $text_quantity; ?></th>
		    <th align="right"><?php echo $text_price; ?></th>
		    <th align="right"><?php echo $text_total; ?></th>
		  </tr>
		  <?php foreach ($products as $product) { ?>
		  <tr>
		    <td align="left" valign="top"><a href="<?php echo str_replace('%ID%', $product['id'], $product_link) ?>"><?php echo $product['name']; ?></a>
		      <?php foreach ($product['option'] as $option) { ?>
		      <br />
		      &nbsp;<small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
		      <?php } ?></td>
		    <td align="left" valign="top"><?php echo $product['model']; ?></td>
		    <td align="right" valign="top"><?php echo $product['quantity']; ?></td>
		    <td align="right" valign="top"><?php echo $product['price']; ?></td>
		    <td align="right" valign="top"><?php echo $product['total']; ?></td>
		  </tr>
		  <?php } ?>
		</table>

		<div class="span4 pull-right">
        <table class="table table-striped table-bordered">
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td align="right"><?php echo $total['title']; ?></td>
            <td align="right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </table>
		</div>
   </div> 
    
    <?php if ($comment) { ?>
	<div class="container">
    	<h4 class="heading4"><?php echo $text_comment; ?></h4>
    	<div class="content"><?php echo $comment; ?></div>
	</div>
    <?php } ?>

    <?php echo $this->getHookVar('order_attributes'); ?>

    <?php if ($historys) { ?>
    <div class="container">
    <h4 class="heading4"><?php echo $text_order_history; ?></h4>
		<table class="table table-striped table-bordered">
		  <tr>
		    <th align="left"><?php echo $column_date_added; ?></th>
		    <th align="left"><?php echo $column_status; ?></th>
		    <th align="left"><?php echo $column_comment; ?></th>
		  </tr>
		  <?php foreach ($historys as $history) { ?>
		  <tr>
		    <td valign="top"><?php echo $history['date_added']; ?></td>
		    <td valign="top"><?php echo $history['status']; ?></td>
		    <td valign="top"><?php echo $history['comment']; ?></td>
		  </tr>
		  <?php } ?>
		</table>
    </div>
    <?php } ?>

	<div class="control-group">
	    <div class="controls">
	    	<div class="mt20 mb40">
				<a href="<?php echo $continue; ?>" class="btn mr10" title="<?php echo $button_continue->text ?>">
				    <i class="<?php echo $button_continue->{icon}; ?>"></i>
				    <?php echo $button_continue->text ?>
				</a>
				<a href="javascript:window.print();" class="btn btn-orange mr10 pull-right" title="<?php echo $button_print->text ?>">
				    <i class="<?php echo $button_print->{icon}; ?>"></i>
				    <?php echo $button_print->text ?>
				</a>
	    	</div>	
	    </div>
	</div>
	
</form>
</div>