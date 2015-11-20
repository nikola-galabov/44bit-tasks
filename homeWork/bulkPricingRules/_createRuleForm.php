<div id="marketingManager">
	<h1><?php echo $pageTitle; ?></h1>	

	<div class="genericBlock">
		<h2>
			For all products in <?php echo $channel ?> with
			<?php if($_SESSION['filter']['pureCode'] && $_SESSION['filter']['pureCode'] != "") { ?>
				Pure Code: <?php echo($_SESSION['filter']['pureCode']); ?>
			<?php } ?>

			<?php if($_SESSION['filter']['brand'] && $_SESSION['filter']['brand'] != "") { ?>
				Brand: <?php echo($_SESSION['filter']['brand']); ?>
			<?php } ?>
		</h2>
		<form action="action.php" method="POST">
			<div class="column-3">
				<input type="hidden" name="addBulkPricingRule" value="1">
				<input type="hidden" name="pureCode" value="<?php echo($_SESSION['filter']['pureCode']); ?>">
				<div>
					<label for="min">Min quantity:</label>
					<input type="text" id="min" name="min" value="">	
				</div>
				<div>
					<label for="max">Max quantity:</label>
					<input type="text" id="max" name="max" value="">	
				</div>
				<div>
					<label for="discountType">Discount Type:</label>
					<select name="discountType" class="chosen-select">
						<option value="price">Price</option>
						<option value="percent">Percentage</option>
						<option value="fixed">Fixed</option>
					</select>
				</div>
				<div>
					<label for="discountValue">Discount Value:</label>
					<input type="text" id="discountValue" name="discountValue">	
				</div>
			</div>

			<button name="createBulkPricingRule" value="1">Create</button>
		</form>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.selectAllProducts').trigger('click');
	});
</script>

