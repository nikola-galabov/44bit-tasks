<?php

session_start();

error_reporting(E_ALL);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

require($_SERVER['DOCUMENT_ROOT'] . '/Secure/MarketingManager/includes/dbConnect.php');
require($_SERVER['DOCUMENT_ROOT'] . '/Secure/MarketingManager/includes/helpers.php');
require($_SERVER['DOCUMENT_ROOT'] . '/Secure/MarketingManager/includes/products.php');
require($_SERVER['DOCUMENT_ROOT'] . '/Secure/MarketingManager/includes/bigCommerceConnection.php');

$hlp = new helpers;

// first exports the products

// then replace the titles

// after the first invoke of the function, the products with empty ids will be updated
compareTheProducts();

function compareTheProducts() {
	$bcProducts = getProductsBC();

	$missingIds = array();

	foreach ($bcProducts as $bcProduct) {
		// get the product with this name from cp

		$cpProduct = getProductCP($bcProduct['name']);

		if(count($cpProduct) == 0) {
			// todo remove it
			continue;
			echo '<div style="margin-bottom: 10px; padding: 10px; border: 1px solid black">';
			echo "There is no product in cp with title: " . $bcProduct['name'] . '<br>';
			echo '<span><a target="_blank" href="http://www.replaceupsbattery.com' . $bcProduct['customUrl'] . '">' . $bcProduct['name'] . '</a></span>';
			echo "</div>";
			continue;
		}
		else if(count($cpProduct) > 1) {
			// todo remove it
			continue;
			echo '<div style="margin-bottom: 10px; padding: 10px; border: 1px solid black">';
			echo '<span>There are ' . count($cpProduct) . ' products in cp with title: ' . $bcProduct['name'] . '</span><br>';
			foreach ($cpProduct as $prod) {
				echo '<a target="_blank" href="http://dev.cp.44km.local/secure/MarketingManager/productsManager/productEdit.php?id=' . $prod['p2c_Id'] . '">' . $prod['p2c_Id'] . '</a><br>';
			}
			echo "</div>";

			continue;
		}

		echo '<div style="margin-bottom: 10px; padding: 10px; border: 1px solid black">';
		echo "<h5>Checking Product <strong>" . $bcProduct['name'] . "</strong></h5>";

		$cpProduct = $cpProduct[0];

		// if($bcProduct['warranty'] != $cpProduct['warranty']) {
		// 	echo '<span style="color:red;">Different warranty!</span><br>';
		// }

		// if($bcProduct['price'] != $cpProduct['price']) {
		// 	if($bcProduct['price'] != $cpProduct['salePrice']) {

		// 	}
		// 	echo '<span style="color:red;">Different Price!</span> | CP Price: ' . $cpProduct['price'] . ' Sale Price' . $cpProduct['salePrice'] . ' , BC Price: ' . $bcProduct['price'] . '<br>';
		// }

		// if($bcProduct['customUrl'] != $cpProduct['url']) {
		// 	echo '<span style="color:red;">Different url!</span><br>';
		// }

		if($bcProduct['bcId'] != $cpProduct['bcId']) {
			echo '<span style="color:red;">Different bcId!</span><br>';
			echo 'BigCommerce - ' . $bcProduct['bcId'] . '<br>';
			echo 'CP - ' . $cpProduct['bcId'] . '<br>';

			if(empty($cpProduct['bcId'])) {
				if(isExistingId($bcProduct['bcId'])){
					array_push($missingIds, $bcProduct['bcId']);
				}
				else{
					updateTheId($bcProduct['bcId'], $cpProduct['p2c_Id']);
				}
			}
		}

		// if($bcProduct['sku'] != $cpProduct['sku']) {
		// 	echo '<span style="color:red;">Different sku!</span><br>';
		// 	echo 'BigCommerce - ' . $bcProduct['sku'] . '<br>';
		// 	echo 'CP - ' . $cpProduct['sku'] . '<br>';
		// }

		// TODO Availability

		// if($bcProduct['url'] != $cpProduct['url']) {
		// 	echo '<span style="color:red;">Different url!</span><br>';
		// }

		echo "</div>";
	}

	echo '<h1>' . implode(', ', $missingIds) . '</h1>';
	var_dump(count($missingIds));
}

function getProductCP($productName) {
	global $hlp;

	$selectQry = "SELECT 
					`p2c_Price` as `price`,
					`p2c_SalePrice` as `salePrice`,
					`p2c_Custom18` as `url`,
					`p2c_Custom22` as `warranty`,
					`p2c_Custom24` as `bcId`,
					`p2c_Sku` as `sku`,
					`p2c_Custom8` as `availability`,
					`pc`.`p2c_Id`
				FROM
					`CP`.`base_Product_channel` as `pc`
				JOIN
					`ProductsTitles` as `pt`
				ON
					`pc`.`p2c_Id` = `pt`.`p2c_Id`
				WHERE 
					`pt`.`title` = '{$productName}'";

	$res = $hlp->qry($selectQry, true);

	$prod = $res->fetch_all(MYSQLI_ASSOC);
	
	return $prod;
}

function getProductsBC() {
	global $hlp;

	$selectQry = "SELECT 
					*
				FROM 
					`CP`.`ReplaceUpsProducts`";

	$res = $hlp->qry($selectQry, true);

	$products = $res->fetch_all(MYSQLI_ASSOC);

	return $products;
}

function insertProduct($bcProduct) {
	global $hlp;

	try {
		$insertQuery = "INSERT INTO 
						`CP`.`ReplaceUpsProducts`
						(
							`name`,
							`description`,
							`availability`,
							`price`,
							`sku`,
							`pageTitle`,
							`customUrl`,
							`warranty`,
							`bcId`,
							`isVisible`
						)
					VALUES
						(
							'{$bcProduct->name}',
							'{$bcProduct->description}',
							'{$bcProduct->availability}',
							{$bcProduct->price},
							'{$bcProduct->sku}',
							'{$bcProduct->page_title}',
							'{$bcProduct->custom_url}',
							'{$bcProduct->warranty}',
							{$bcProduct->id},
							{$bcProduct->is_visible}
						)";
	
		$res = $hlp->qry($insertQuery);

		if($res == false) {
			echo "Product with Id " . $bcProduct->id . " was not inserted. <br>";

			return;
		}

		echo "Product Inserted <br>";
	} 
	catch (Exception $ex) {
		echo "Product with Id " . $bcProduct->id . " was not inserted. <br>";
		var_dump($ex);
		echo "<br>";
	}
}

function exportTheProducts($channelId) {
	$bigCommerce = new BigCommerceConnection($channelId);
	// get products count
	// $productsCount = $bigCommerce->getProductsCount();
	// $productsCount = $productsCount->count;
	// 14273

	$maxPage = 57;

	for($page = 1; $page <= $maxPage; $page++) {
		$products = $bigCommerce->listProducts($page);

		foreach ($products as $product) {
			// insert the data
			insertProduct($product);
		}
	}
}

function replaceTitles($channelId) {
	global $hlp;

	$qry = "SELECT
				`p2c_id` as `id`, `p2c_title` as `title`, `p2c_ProductId` as `prodId`
			FROM
				`base_Product_Channel`
			WHERE
				`p2c_ChannelID` = {$channelId}";

	$res = $hlp->qry($qry, true);
	$products = $res->fetch_all(MYSQLI_ASSOC);

	foreach ($products as $prod) {
		$title = $hlp->textsReplacer(array('title' => $prod['title']), $prod['prodId'], NULL, $channelId);
		$title = $title['title'];
		
		$insertQry = "INSERT INTO 
						`ProductsTitles`
						(
							`p2c_ID`,
							`title`,
							`channelId`
						)
					VALUES
						(
							{$prod['id']},
							'{$title}',
							{$channelId}
						)";

		$res = $hlp->qry($insertQry);

		if(!$res) {
			echo $prod['id'] . '<br>';
		}
	}
}

function isExistingId($id) {
	global $hlp;

	$qry = "SELECT 
			*
		FROM
			`base_Product_Channel`
		WHERE
			`p2c_ChannelID` = 7
		AND
			`p2c_Custom24`
		IN 
		({$id})";

	$res = $hlp->qry($qry, true);
	
	if($res->num_rows == 0) {
		return false;
	}

	return true;
}

function updateTheId($id, $p2cId) {
	global $hlp;

	$qry = "UPDATE `base_Product_channel` SET `p2c_Custom24` = {$id} WHERE `p2c_Id` = {$p2cId}";
	$res = $hlp->qry($qry);
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
	$("div:not(:has(>span))").hide();
</script>