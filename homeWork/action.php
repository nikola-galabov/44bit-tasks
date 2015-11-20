<?php
session_start ();

include_once ($_SERVER ["DOCUMENT_ROOT"] . '/EXTENTIONS/php/debug/kint/Kint.class.php'); // debug
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/dbConnect.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/prices.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/channels.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/products.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/helpers.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/bigCommerceConnection.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/volusionConnection.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/textEditor.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/ProductsManager/manageOutOfStock.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/productsManager/adCustomizerFeedService.php');
// Call AdWords API
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/EXTENTIONS/php/GooglePHP/examples/AdWords/v201506/init.php');
require_once ADWORDS_UTIL_PATH . '/ReportUtils.php';
error_reporting ( E_ERROR | E_WARNING | E_PARSE );
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
$prc = new price ();
$chan = new channels ();
$prod = new products ();
$hlp = new helpers ();
$textEditor = new texts ();
// d($_SESSION['selectedProducts']);
// d($_SESSION['existingProducts']);

// set channel and chSpecific for all
if ($_SESSION ['selectedProducts'] ['channelIn'] != '') {
	foreach ( $_SESSION ['selectedProducts'] ['item'] as &$item ) {
		$item = array_replace ( $item, array (
				'p2c_ChannelID' => $_SESSION ['selectedProducts'] ['channelIn'] 
		) );
	}
}

// d($_SESSION['selectedProducts']);

if ($_POST ['overwrite'] == '1') { // overwrite existing products for that channel
	$insProducts = $prod->insertProducts ( $_SESSION ['selectedProducts'] ['item'], true );
	if ($insProducts ['errorCount'] == 0) {
		$hlp->sessSetFlash ( $insProducts ['itemsInserted'] . " products inserted/updated, " . $insProducts ['errorCount'] . " errors. <br />" . "Feed Prices are generated. Please recheck.", 'flashSuccess' );
	} else {
		$hlp->sessSetFlash ( $insProducts ['itemsInserted'] . " products inserted/updated, " . $insProducts ['errorCount'] . " errors. <br />" . "Please recheck.", 'flashFail' );
	}
	header ( 'Location: productsViewList.php' );
	exit ();
} elseif ($_POST ['skip'] == '1') { // /skip existing products
	$insProducts = $prod->insertProducts ( $_SESSION ['selectedProducts'] ['item'], false );
	if ($insProducts ['errorCount'] == 0) {
		$hlp->sessSetFlash ( $insProducts ['itemsInserted'] . " products inserted/updated, " . $insProducts ['errorCount'] . " errors. <br />" . "Feed Prices are generated. Please recheck.", 'flashSuccess' );
	} else {
		$hlp->sessSetFlash ( $insProducts ['itemsInserted'] . " products inserted/updated, " . $insProducts ['errorCount'] . " errors. <br />" . "Please recheck.", 'flashFail' );
	}
	header ( 'Location: productsViewList.php' );
	exit ();
} elseif ($_POST ['productInfoUpdate']) {
	$updateAction = $prod->productInfoUpdate ( $_POST );
	// d($_POST);
	if ($updateAction === true) {
		$hlp->sessSetFlash ( "Product updated! (ID: " . $_POST ['p2c_ID'] . ")", 'flashSuccess' );
		header ( 'Location:productEdit.php?id=' . $_POST ['p2c_ID'] );
		exit ();
	} else {
		$hlp->sessSetFlash ( "MySql Error: " . $updateAction, 'flashFail' );
		header ( 'Location:productEdit.php?id=' . $_POST ['p2c_ID'] );
		exit ();
	}
} elseif ($_POST ['exportCsv']) {
	if (is_array ( $_POST ['filteredProducts'] ) and count ( $_POST ['filteredProducts'] )) {
		$prod->prepareExport ( $_POST ['filteredProducts'] );
	} else {
		$hlp->sessSetFlash ( "Nothing selected!", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['out-of-stock']) {
	if (is_array ( $_POST ['filteredProducts'] ) and count ( $_POST ['filteredProducts'] )) {
		// get the channel
		$product = $prod->getProduct ( $_POST ['filteredProducts'] [0] );
		$channelId = $product [0] ['p2c_ChannelID'];
		$availability = htmlspecialchars ( '<meta content="OutOfStock" itemprop="availability">' ) . 'Out Of Stock';
		$_POST ['channelId'] = $channelId;
		
		if ($channelId == "6") { // BS
			$volusionConnector = new VolusionConnection ();
			$productsData = [ ];
			
			foreach ( $_POST ['filteredProducts'] as $key => $productId ) {
				$product = $prod->getProduct ( $productId );
				$productCode = $product [0] ['prod_ProductCode']; // Product Code
				$productsData [$key] = array (
						'ProductCode' => $productCode,
						'StockStatus' => '0',
						'DoNotAllowBackOrders' => 'Y',
						'Availability' => $availability 
				);
			}
			
			// Confirmation
			if (isset ( $_POST ['updateConfirmed'] )) {
				if ($_POST ['updateConfirmed']) {
					$successMessage = "Products successfully marked as out of stock and AdGroups are paused!";
					
					// if there is a products that are not updated for some reason they will be push into this array
					$errorWithUpdate = [ ];
					
					$xml = $volusionConnector->buildXml ( $productsData, $successMessage );
					
					$result = $volusionConnector->updateProduct ( $xml );
					
					// var_dump($result); die;
					if ($result) {
						foreach ( $result->Products as $product ) {
							if (strtolower ( ( string ) $product->Success ) == 'true') {
								$volusionConnector->updateProductInCP ( ( string ) $product->ProductCode, 'Y', $availability, 1 );
							} else {
								array_push ( $errorWithUpdate, ( string ) $product->ProductCode );
							}
						}
					}

					if( ! $result || count($errorWithUpdate) > 0 ) {
						$hlp->sessSetFlash('There was an error with the following products: ' . implode(", ", $errorWithUpdate), 'flashFail');
					}
					else {
						if ($channelId == "6") {
							// Pause all selected items in AdWords
							pauseOutOfStockItems($_POST['filteredProducts'], $channelId);
						}
						$hlp->sessSetFlash($successMessage, 'flashSuccess');
					}

					header('Location: productsViewList.php');
				}
				else {
					header('Location: productsViewList.php');
				}
			} else {
				include ('productsUpdateConfirmation.php');
			}
		} elseif ($channelId == "7" || $channelId == "15") { // Replace UPS OR BSLA
			
			if ($channelId == "7") { // RUPS
				$bigCommerce = new BigCommerceConnection ( $channelId );
			} elseif ($channelId == "15") { // BSLA
				$bigCommerce = new BigCommerceConnection ( $channelId );
			}
			
			$productsData = [ ];
			$availability_description = 'Out Of Stock';
			
			foreach ( $_POST ['filteredProducts'] as $key => $value ) {
				$productsData [$key] = array (
						'p2c_ID' => $value,
						'availability' => 'disabled',
						'availability_description' => $availability_description 
				);
			}
			
			// Confirmation
			if (isset ( $_POST ['updateConfirmed'] )) {
				if ($_POST ['updateConfirmed']) {
					$successMessage = "Products successfully marked as out of stock and AdGroups are paused!";
					
					$errorWithUpdate = [ ];
					
					// update product in bit commerce
					foreach ( $_POST ['filteredProducts'] as $productId ) {
						// get big commerce product id
						$product = $prod->getProduct ( $productId );
						$bcProductId = $product [0] ['p2c_Custom24'];
						
						// update product in bit commerce
						$productData = array (
								'availability' => 'disabled',
								'availability_description' => $availability_description 
						);
						$updatedProductId = $bigCommerce->updateProduct ( $bcProductId, $productData );
						
						// update product in cp if there is no errors
						if ($updatedProductId) {
							// $bigCommerce->updateProductInCp($updatedProductId, 'N', $availability_description, 1);
						} else {
							array_push ( $errorWithUpdate, $productId );
						}
					}

					if( count($errorWithUpdate) > 0 ) {
						$hlp->sessSetFlash('There was an error with the following products: ' . implode(", ", $errorWithUpdate), 'flashFail');
					}
					else {
						if($channelId == "15") { // BSLA
							// Pause all selected items in AdWords
							pauseOutOfStockItems($_POST['filteredProducts'], $channelId);
						}
						$hlp->sessSetFlash($successMessage, 'flashSuccess');
					}
					header ( 'Location: productsViewList.php' );
				} else {
					header ( 'Location: productsViewList.php' );
				}
			} else {
				include ('productsUpdateConfirmation.php');
			}
		}
	} else {
		$hlp->sessSetFlash ( "Nothing selected!", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['in-stock']) {
	if (is_array ( $_POST ['filteredProducts'] ) and count ( $_POST ['filteredProducts'] )) {
		// get the channel
		$product = $prod->getProduct ( $_POST ['filteredProducts'] [0] );
		$channelId = $product [0] ['p2c_ChannelID'];
		$_POST ['channelId'] = $channelId;
		
		if ($channelId == "6") { // BS
			$volusionConnector = new VolusionConnection ();
			$productsData = [ ];
			
			foreach ( $_POST ['filteredProducts'] as $key => $productId ) {
				$product = $prod->getProduct ( $productId );
				$productCode = $product [0] ['prod_ProductCode']; // Product Code
				$productsData [$key] = array (
						'ProductCode' => $productCode,
						'StockStatus' => '9999',
						'DoNotAllowBackOrders' => 'N',
						'Availability' => $_POST ['availability-value'] 
				);
			}
			
			if (isset ( $_POST ['updateConfirmed'] )) {
				if ($_POST ['updateConfirmed']) {
					$successMessage = "Products successfully marked as in stock and AdGroups are resumed!";
					
					$xml = $volusionConnector->buildXml ( $productsData, $successMessage );
					$result = $volusionConnector->updateProduct ( $xml );
					$errorWithUpdate = [ ];
					
					if ($result) {
						foreach ( $result->Products as $product ) {
							if (strtolower ( ( string ) $product->Success ) == 'true') {
								$volusionConnector->updateProductInCP ( ( string ) $product->ProductCode, 'N', $_POST ['availability-value'], 0 );
							} else {
								array_push ( $errorWithUpdate, ( string ) $product->ProductCode );
							}
						}
					}
					
					if (! $result || count ( $errorWithUpdate ) > 0) {
						$hlp->sessSetFlash ( 'There was an error with the following products: ' . implode ( ", ", $errorWithUpdate ), 'flashFail' );
					} else {
						$hlp->sessSetFlash ( $successMessage, 'flashSuccess' );
						if ($channelId == "6") {
							// Resume paused items from AdWords
							resumePausedItems ( $_POST ['filteredProducts'], $channelId );
						}
					}
					
					header ( 'Location: productsViewList.php' );
				} else {
					header ( 'Location: productsViewList.php' );
				}
			} else {
				include ('productsUpdateConfirmation.php');
			}
		} elseif ($channelId == "7" || $channelId == "15") { // Replace UPS OR BSLA
			
			if ($channelId == "7") { // RUPS
				$bigCommerce = new BigCommerceConnection ( $channelId );
			} elseif ($channelId == "15") { // BSLA
				$bigCommerce = new BigCommerceConnection ( $channelId );
			}
			
			$productsData = [ ];
			$availability_description = "In Stock";
			
			foreach ( $_POST ['filteredProducts'] as $key => $value ) {
				$productsData [$key] = array (
						'p2c_ID' => $value,
						'availability' => 'available',
						'availability_description' => $availability_description 
				);
			}
			
			// Confirmation
			if (isset ( $_POST ['updateConfirmed'] )) {
				if ($_POST ['updateConfirmed']) {
					$successMessage = "Products successfully marked as in stock and AdGroups are resumed!";
					$productData = array (
							'availability' => 'available',
							'availability_description' => $_POST ['availability-description'] 
					);
					
					$errorWithUpdate = [ ];
					
					foreach ( $_POST ['filteredProducts'] as $productId ) {
						// get big commerce product id
						$product = $prod->getProduct ( $productId );
						$bcProductId = $product [0] ['p2c_Custom24'];
						
						// update product in bit commerce
						$updatedProductId = $bigCommerce->updateProduct ( $bcProductId, $productData );
						
						// update product in cp if there is no errors
						if ($updatedProductId) {
							$bigCommerce->updateProductInCp ( $updatedProductId, 'Y', $availability_description, 0 );
						} else {
							array_push ( $errorWithUpdate, $productId );
						}
					}

					if( count($errorWithUpdate) > 0 ) {
						$hlp->sessSetFlash('There was an error with the following products: ' . implode(", ", $errorWithUpdate), 'flashFail');
					}
					else {
						$hlp->sessSetFlash($successMessage, 'flashSuccess');

						if ($channelId == "15") {
							// Resume paused items from AdWords
							resumePausedItems ( $_POST ['filteredProducts'], $channelId );
						}
					}
					
					header ( 'Location: productsViewList.php' );
				} else {
					header ( 'Location: productsViewList.php' );
				}
			} else {
				include ('productsUpdateConfirmation.php');
			}
		}
	} else {
		$hlp->sessSetFlash ( "Nothing selected!", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['deleteSelected']) {
	$deleteAction = $prod->deleteProducts ( $_POST ['filteredProducts'] );
	if ($deleteAction === true) {
		$hlp->sessSetFlash ( "Products deleted!", 'flashSuccess' );
		header ( 'Location:productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( "MySql Error: " . $deleteAction, 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['deactivateSelected']) {
	$deactivated = $prod->deactivateProducts ( $_POST ['filteredProducts'] );
	
	if ($deactivated === true) {
		$hlp->sessSetFlash ( "Products deactivated!", 'flashSuccess' );
		header ( 'Location:productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( "MySql Error: " . $deactivated, 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['refreshTitle']) {
	$refreshAction = $prod->productTitleRefresh ( $_POST ['filteredProducts'], $_POST ['fieldsToExport'], $_SESSION ['filter'] ['channel'], $hlp );
	
	if ($refreshAction ['errorCount'] == 0) {
		$hlp->sessSetFlash ( $refreshAction ['itemsInserted'] . " products updated", 'flashSuccess' );
		header ( 'Location:productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( $refreshAction ['errorCount'] . ' errors', 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['setGenuine']) {
	if (! count ( $_POST ['filteredProducts'] )) {
		$hlp->sessSetFlash ( "You must select at least one product!", 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
	$setGenuine = $prod->setGenuine ( $_POST ['filteredProducts'], 1 );
	if ($setGenuine === true) {
		$hlp->sessSetFlash ( "Products marked as genuine!", 'flashSuccess' );
		header ( 'Location:productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( "MySql Error: " . $setGenuine, 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['setNotGenuine']) {
	if (! count ( $_POST ['filteredProducts'] )) {
		$hlp->sessSetFlash ( "You must select at least one product!", 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
	$setGenuine = $prod->setGenuine ( $_POST ['filteredProducts'], 0 );
	if ($setGenuine === true) {
		$hlp->sessSetFlash ( "Products marked as replacement!", 'flashSuccess' );
		header ( 'Location:productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( "MySql Error: " . $setGenuine, 'flashFail' );
		header ( 'Location:productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['updateText']) {
	$textEditor->updateText ( $_POST );
} elseif ($_POST ['updatePrices'] == 1) {
	if (count ( $_POST ['filteredProducts'] ) == 0) {
		$hlp->sessSetFlash ( "Error: No product selected", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
	
	// check channel
	$channel = $_SESSION ['filter'] ['channel'];
	
	// get products to update with their prices
	$productsToUpdate = array ();
	foreach ( $_POST ['filteredProducts'] as $product ) {
		$q = "SELECT
				`p2c_Id`,
				`p2c_Custom24`,
				`prod_productCode`,
				`p2c_Price`,
				`p2c_SalePrice`
			FROM
				`base_Product_channel`
			JOIN
				`Product`
			ON
				`p2c_productid` = `prod_id`
			WHERE
				`p2c_id` =  $product
			AND `p2c_ChannelId` = $channel";
		
		$res = $hlp->qry ( $q, true );
		
		$currentProduct = $res->fetch_all ( MYSQLI_ASSOC );
		$productsToUpdate [$product] = $currentProduct [0];
	}
	
	if ($channel == '6') {
		confirmation ( $_POST ['updatePricesConfirmed'], 'updatePricesConfirmation.php', 'productsViewList.php', $productsToUpdate );
		$result = $prc->updatePriceInVolusion ( $productsToUpdate );
		
		if ($result) {
			$hlp->sessSetFlash ( "The prices are successfully updated!", 'flashSuccess' );
		} else {
			$hlp->sessSetFlash ( "Error with updating prices!", 'flashFail' );
		}
	} elseif ($channel == '7' || $channel == '15') {
		confirmation ( $_POST ['updatePricesConfirmed'], 'updatePricesConfirmation.php', 'productsViewList.php', $productsToUpdate );
		$result = $prc->updatePriceInBigCommerce ( $channel, $productsToUpdate );
		
		if (is_array ( $result ) && count ( $result ) > 0) {
			$hlp->sessSetFlash ( "There was an error with the following products: " . implode ( ', ', $result ), 'flashFail' );
		} else {
			$hlp->sessSetFlash ( "The prices are successfully updated!", 'flashSuccess' );
		}
	}
	
	header ( 'Location: productsViewList.php' );
} elseif ($_POST ['updateTexts'] == 1) {
	if (count ( $_POST ['filteredProducts'] ) == 0) {
		$hlp->sessSetFlash ( "Error: No product selected", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
	
	$channelId = $_SESSION ['filter'] ['channel'];
	$data = array ();
	
	if ($channelId == '6') {
		$_POST ['fieldsToExport'] = array (
				'productcode',
				'productdescription',
				'metatag_description',
				'metatag_keywords',
				'metatag_title',
				'photo_alttext',
				'productdescriptionshort',
				'productname',
				'productnameshort',
				'productkeywords' 
		);
		$products = $prod->prepareExport ( $_POST ['filteredProducts'], true );
		
		foreach ( $products as $product ) {
			if ($prod->isSpecialOffer ( $product ['productcode'] )) {
				continue;
			}
			
			$data [$product ['productcode']] = array (
					'ProductDescription' => htmlspecialchars ( $product ['productdescription'] ),
					'ProductDescriptionShort' => htmlspecialchars ( $product ['productdescriptionshort'] ),
					'METATAG_Description' => htmlspecialchars ( $product ['metatag_description'] ),
					'METATAG_Keywords' => htmlspecialchars ( $product ['metatag_keywords'] ),
					'METATAG_Title' => htmlspecialchars ( $product ['metatag_title'] ),
					'Photo_AltText' => htmlspecialchars ( $product ['photo_alttext'] ),
					'ProductKeywords' => htmlspecialchars ( $product ['productkeywords'] ),
					'ProductCode' => htmlspecialchars ( $product ['productcode'] ) 
			);
		}
	} else {
		$_POST ['fieldsToExport'] = array (
				'Meta Description',
				'Meta Keywords',
				'Page Title',
				'Product Description',
				'Product Image Description - 1',
				'Product Name',
				'Product Warranty',
				'Search Keywords' 
		);
		$_POST ['exportOptions'] = 0;
		$products = $prod->prepareExport ( $_POST ['filteredProducts'], true );
		
		foreach ( $products as $product ) {
			$data [$product ['productcode']] = array (
					'description' => $product ['Product Description'],
					'warranty' => $product ['Product Warranty'],
					'page_title' => $product ['Page Title'],
					'meta_keywords' => $product ['Meta Keywords'],
					'meta_description' => $product ['Meta Description'],
					'image_description' => $product ['Product Image Description - 1'],
					'ProductCode' => $product ['productcode'],
					'BigCommerceId' => $product ['BigCommerceId'] 
			);
		}
	}
	
	if (count ( $data ) > 0) {
		confirmation ( $_POST ['updateTextsConfirmed'], 'updateTextsConfirmation.php', 'productsViewList.php', $data );
	}
	
	if ($channelId == '6') {
		$volusion = new VolusionConnection ();
		$xml = $volusion->buildXmlBetter ( $data );
		$result = $volusion->updateProduct ( $xml );
		
		if ($result) {
			$hlp->sessSetFlash ( "The texts are successfully updated!", 'flashSuccess' );
		} else {
			$hlp->sessSetFlash ( "Error with updating texts!", 'flashFail' );
		}
	} elseif ($channelId == '7' || $channelId == '15') {
		$errorWithUpdates = array ();
		
		foreach ( $data as $productData ) {
			// $data = $textEditor->getTextFeedByP2C_ID($p2c_id, $channelId);
			$bcId = $productData ['BigCommerceId'];
			$imageDescription = $productData ['image_description'];
			unset ( $productData ['image_description'] );
			unset ( $productData ['BigCommerceId'] );
			unset ( $productData ['ProductCode'] );
			
			// pass the data
			$bigCommerce = new BigCommerceConnection ( $channelId );
			$result = $bigCommerce->updateProduct ( $bcId, $productData );
			
			if (! $result) {
				array_push ( $errorWithUpdate, $p2c_id );
			} else {
				$image = $bigCommerce->getProductImages ( $result );
				$imageId = $image [0]->id;
				$result = $bigCommerce->updateProductImage ( $imageId, $bcId, array (
						'description' => $imageDescription 
				) );
			}
		}
		
		if (count ( $errorWithUpdate ) > 0) {
			$hlp->sessSetFlash ( "There was an error with the following products: " . implode ( ', ', $errorWithUpdates ), 'flashFail' );
		} else {
			$hlp->sessSetFlash ( "The texts are successfully updated!", 'flashSuccess' );
		}
	}
	
	header ( 'Location: productsViewList.php' );
} elseif ($_POST ['adwords-price']) {
	if (is_array ( $_POST ['filteredProducts'] ) and count ( $_POST ['filteredProducts'] )) {
		$errorLog = changePriceAdCustomizer ( $_POST ['filteredProducts'], $_SESSION ['filter'] ['channel'] );

		if (count ( $errorLog ) > 0) {
			$hlp->sessSetFlash ( "There was an error with the following products: <br />" . implode ( ', ', $errorLog ), 'flashFail' );
			header ( 'Location:productsViewList.php' );
			exit ();
		} else {
			$hlp->sessSetFlash ( "The prices on AdWords Ads are successfully updated!", 'flashSuccess' );
			header ( 'Location:productsViewList.php' );
			exit ();
		}
	} else {
		$hlp->sessSetFlash ( "Nothing selected!", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['createProduct']) {
	foreach ( $_POST ['productSpecific'] as $key => $field ) {
		if ($field == "") {
			unset ( $_POST ['productSpecific'] [$key] );
		}
	}
	
	$res = $prod->createProduct ( $_POST );
} elseif ($_POST ['importProducts']) {
	if (count ( $_POST ['filteredProducts'] ) == 0) {
		$hlp->sessSetFlash ( "Error: No product selected", 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
	
	$products = $_POST ['filteredProducts'];
	$importResult = $prod->importProducts ( $products );
	
	if ($importResult === true) {
		$hlp->sessSetFlash ( 'Products were successfully imported.', 'flashSuccess' );
		header ( 'Location: productsViewList.php' );
		exit ();
	} else {
		$hlp->sessSetFlash ( 'There was an error with products: ' . implode ( ', ', $importResult ), 'flashFail' );
		header ( 'Location: productsViewList.php' );
		exit ();
	}
} elseif ($_POST ['createCategory']) {
	$categoryName = $_POST ['category-name'];
	$parentId = $_POST ['parent-category'];
	$categoryData = array (
			'name' => $categoryName,
			'parent_id' => $parentId,
			'is_visible' => true 
	);
	
	$categoryData = json_encode ( $categoryData );
	$channelId = $_SESSION ['filter'] ['channel'];
	$bigCommerce = new BigCommerceConnection ( $channelId );
	$result = $bigCommerce->createCategory ( $categoryData );
	
	if ($result->id) {
		$insertQuery = "INSERT INTO
							`base_Categories` (`bscat_CatId`, `bscat_ParentCatId`, `bscat_CatName`, `bscat_ChannelId`)
						VALUES({$result->id}, {$result->parent_id}, '{$result->name}', $channelId)";
		$res = $hlp->qry ( $insertQuery );
		
		if ($res) {
			$hlp->sessSetFlash ( 'Category "' . $result->name . '" was created.', 'flashSuccess' );
			header ( 'Location: productsViewList.php' );
			exit ();
		}
	}
	
	$hlp->sessSetFlash ( 'Something went wrong.', 'flashFail' );
	header ( 'Location: createCategory.php' );
	exit ();
}
/*
 * This function includes a confirmation form template
 *
 * @param confirmationTemplate - the template that will be included
 * @param confirmationChecker - this is the $_POST[] param inside the confirmation's template form
 * @param redirectTemplate - if the user cancels the confirmation he will be send to this page
 * @param productsToUpdate - array with the products and their feed that will be updated if the user confirm his choise
 * @return true if confirmation is passed or redirect if it didn't
 */
function confirmation($confirmationChecker, $confirmationTemplate, $redirectTemplate, $productsToUpdate) {
	$hlp = new helpers ();
	
	if (! isset ( $confirmationChecker )) {
		include ($confirmationTemplate);
		exit ();
	} else {
		if ($confirmationChecker) {
			return true;
		} else {
			header ( 'Location: ' . $redirectTemplate );
			exit ();
		}
	}
}

?>
