<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/dbConnect.php');
require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/helpers.php');
require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/products.php');
require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/bulkPricingRule.php');
require_once($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/bigCommerceConnection.php');

$bulkPricingRule = new BulkPricingRule();
$hlp = new helpers;
$product = new products;

// for testing
	if( empty($_SESSION['filter']['pureCode']) || $_SESSION['filter']['pureCode'] != 'b12-45') {
		var_dump('You are testing!'); die;
	}


if($_POST['editBulkPricingRule'] == 1) {
	$rulesIds = $_POST['filteredProducts'];

	if(count($rulesIds) == 0) {
		$hlp->sessSetFlash('Nothing selected!', 'flashFail');
		header('Location:index.php');
		exit;
	}

	foreach ($rulesIds as $id) {
		$fields = $_POST['item'][$id];

		if($fields['bpr_maxQty'] < $fields['bpr_minQty']) {
			$msg = "Minimum quantity must be less than maximum quantity.";
	        $hlp->sessSetFlash($msg, 'flashFail');
	        header('Location:index.php');
	        exit;
		}

		$rulesForEdit[$id] = $fields;
	}

	$result = $bulkPricingRule->editBulkPricingRules($rulesIds, $rulesForEdit);

	if(count($result['errors']) == 0) {
		$msg = "There was an error with the rules with ids: " . implode($result['errors'], ', ') . '!';
        $hlp->sessSetFlash($msg, 'flashFail');
	}
	else {
		$msg = "Bulk Pricing Rules where successfully updated.";
        $hlp->sessSetFlash($msg, 'flashSuccess');
	}

	header('Location:index.php');
}
elseif($_POST['addBulkPricingRule'] == 1) {
	if(!isset($_POST['createBulkPricingRule'])) {
		header('Location:create.php');
		return;
	}

	if($rule['max'] < $rule['min']) {
		$msg = "Minimum quantity must be less than maximum quantity.";
        $hlp->sessSetFlash($msg, 'flashFail');
        header('Location:create.php');
        exit;
	}

	$rule = array(
		'min' => $_POST['min'],
		'max' => $_POST['max'],
		'type' => $_POST['discountType'],
		'type_value' => $_POST['discountValue']
	);

	$isExistingRule = $bulkPricingRule->checkForExistingRule($_POST['pureCode'], $rule);
	
	if($isExistingRule) {
		$msg = "Overlaping quantity.";
        $hlp->sessSetFlash($msg, 'flashFail');
        header('Location:create.php');
        exit;
	}

	$result = $bulkPricingRule->createPureCodePricingRule($_POST['pureCode'], $rule);

	if(count($result['errors']) > 0) {
		$msg = "There was an error creating the rules for the following products: " . implode(', ', $result['errors']) . '!';
        $hlp->sessSetFlash($msg, 'flashFail');
	}
	else {
		$msg = "Bulk Pricing Rules were successfully created!";
        $hlp->sessSetFlash($msg, 'flashSuccess');
	}

	header('Location:index.php');
}
elseif($_POST['deleteBulkPricingRule'] == 1) {
	$rulesForDelete = $_POST['filteredProducts'];
	$result = $bulkPricingRule->deleteBulkPricingRule($rulesForDelete);
	
	if(count($result['errors']) > 0) {
		$msg = "There was an error creating the rules for the following products: " . implode(', ', $result['errors']) . '!';
        $hlp->sessSetFlash($msg, 'flashFail');
	}
	else {
		$msg = "Bulk Pricing Rules were successfully deleted!";
        $hlp->sessSetFlash($msg, 'flashSuccess');
	}

	header('Location:index.php');
}