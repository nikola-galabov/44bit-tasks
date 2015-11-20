<?php 
	session_start();
	require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/dbConnect.php');
	require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/helpers.php');
	require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/bulkPricingRule.php');
	
	// ini_set('display_errors',1);
	// ini_set('display_startup_errors',1);
	// error_reporting(-1);
	
	// The ids of the supported channels
	$supportedChannels = array(7);

	// TODO 
	$supportedChannelsNames = array('RUPS');

	$channelId = $_SESSION['filter']['channel'];
	$pureCode = $_SESSION['filter']['pureCode'];
	$brand = $_SESSION['filter']['brand'];
	// TODO get the name of type!
	$type = $_SESSION['filter']['type'];


	$filtersAsText = ($pureCode != "" ? "Pure Code: " . $pureCode : "") . "<br>" . ($brand != "" ? "Brand: " . $brand : "") . "<br>" . ($type != "" ? "Type: " . $type : "");

	if($channelId == 7) {
		$channelName = "RUPS";
	}
	
	$hlp = new helpers;
	$discountRule = new BulkPricingRule;
	$pageTitle = 'Bulk Pricing Rules for ' . $channelName;
	$discountRules = $discountRule->listDiscountRules($pureCode);
	$aPrcMngr = "active";

	$columns = array(	
					'bpr_ID' => 'ID',
					'bpr_minQty' => 'Min Quantity',
					'bpr_maxQty' => 'Max Quantity',
					'bpr_discountType' => 'Discount Type',
					'bpr_discountValue' => 'Discount Value',
					'bpr_productID' => 'BigCommerce ID'
				);
	
	include($_SERVER["DOCUMENT_ROOT"].'/secure/MarketingManager/elements/header.php');
?>
<!-- FOR TESTING ONLY -->
<?php
if( empty($_SESSION['filter']['pureCode']) || $_SESSION['filter']['pureCode'] != 'b12-45') {
		echo '<h1>You are testing with the wrong PureCode!</h1>';
	} 
?>
<!-- FOR TESTING ONLY -->
	<div id="marketingManager">
		<?php
		if(! in_array($channelId, $supportedChannels)) {
			echo '<h1>The feature is not implemented for this channel!</h1>';
			echo '<h2>The supported channels are: ' . implode(', ', $supportedChannelsNames) . '.</h2>';
		}
		else {
		?>
			<h1><?php echo $pageTitle; ?></h1>
			
			<h2><?php echo $filtersAsText; ?></h2>
			<form action="action.php" method="POST">
				<button name="editBulkPricingRule" value="1">Edit Selected</button>
				<button name="addBulkPricingRule" value="1">Add New Rule</button>

				<?php  
					$hlp->table(
						$discountRules,
						$edit = array('bpr_minQty', 'bpr_maxQty' , 'bpr_discountValue', 'bpr_discountType', 'bpr_productID'),
						$check = 'bpr_ID',
						$usedFields = array_keys($columns),
						$hidden = array(),
						$translate = $columns
					); 
				?>
				<input type="hidden" name="pureCode" value="<?php echo $pureCode; ?>">
				<button name="editBulkPricingRule" value="1">Edit Selected</button>
				<button name="addBulkPricingRule" value="1">Add New Rule</button>
				<button class="redBtn" name="deleteBulkPricingRule" onclick="return confirm('Sure?');" value="1">Delete Selected</button>
			</form>

		</div>

		<script>
			$(document).ready(function() {
				$('.selectAllProducts').trigger('click');	
			});
		</script>
	<?php } ?>
<?php include($_SERVER["DOCUMENT_ROOT"].'/secure/MarketingManager/elements/footer.php'); ?>
