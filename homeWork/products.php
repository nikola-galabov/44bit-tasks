<?php
class products {
	public $db;

	function __construct() {
		$this->db = mysqliSingleton::init();
		$this->key = 0;
    }

    function setSKU($sku){
    	$this->sku = $sku;
    }
    function setUPC($upc){
    	$this->upc = $upc;
    }
    function setKEY($key){
    	$this->key = $key;
    }
    function setTitle($title){
    	$this->title = $title;
    }
    function setPrice($price){
    	$this->price = $price;
    }

	function getPureData($productID, $productCode = NULL){	//NOT USED, use getProduct($p2c_ID) instead
		if ($productID){
			$condition = '`prod_ID` = "'.$productID.'"';
		}
		elseif ($productCode){
			$condition = '`prod_ProductCode` = "'.$productCode.'"';
		}
		else{
			return 'No product defined!';
		}

		$q = "SELECT *
				FROM `Product`

				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`

				JOIN `PureCodeTerminal`
				ON `trm_ID` = `pure_TerminalID`

				JOIN `PureCodeCategory`
				ON `pcat_ID` = `pure_categoryID`

				JOIN `PureCodeType`
				ON `pct_ID` = `pure_typeID`


				WHERE
					$condition
				";
		$res = $this->db->query($q);
		return $res->fetch_all(MYSQL_ASSOC);
	}

    //get single product by p2c_ID, for Product Manager edit product
    function getProduct($p2c_ID){
    	$q = "SELECT *
				FROM
					`base_Product_Channel`

				JOIN `Product`
				ON `prod_ID` = `p2c_ProductID`

				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`
				JOIN `PureCodeTerminal`
				ON `trm_ID` = `pure_TerminalID`
				JOIN `PureCodeCategory`
				ON `pcat_ID` = `pure_categoryID`
				JOIN `PureCodeType`
				ON `pct_ID` = `pure_typeID`
				JOIN `User`
				ON `usr_ID` = `pure_LastModifiedBy`

				JOIN `ProductType`
				ON `pt_ID` = `prod_ProductTypeID`

				JOIN `base_Channel`
				ON `cha_ID` = `p2c_ChannelID`

				LEFT JOIN `base_Price`
				ON `prc_ID` = `p2c_PricePolicy`

				WHERE
					`p2c_ID` = '$p2c_ID'";
		$res = $this->db->query($q);
		return $res->fetch_all(MYSQL_ASSOC);
    }

    function createProduct($product){
    	$hlp = new helpers;
		$_SESSION['createProduct'] = $product;
		unset($product['createProduct']);
    	if ($product['manufacturer'] == NULL || 
    		$product['manufacturer'] == '' || 
    		$product['pure-code'] == NULL || 
    		$product['pure-code'] == '' || 
    		$product['productCode'] == NULL || 
    		$product['productCode'] == '' || 
    		$product['product-type'] == NULL ||
    		$product['product-type'] == 0
		) {
    		$hlp->sessSetFlash('Product Code, Product Type, Pure Code and Manufacturer are required!', 'flashFail');
    		header('location: createProduct.php');
    		exit;
    	}

    	if($product['quantity'] < 1) {
    		$hlp->sessSetFlash('Quantity has to be a positive number!', 'flashFail');
    		header('location: createProduct.php');
    		exit;	
    	}

    	$pureCode = $product['pure-code'];
    	$productCode = $product['productCode'];
    	$productTypeId = $product['product-type'];
    	$model = $product['model'] ? $product['model'] : NULL;
    	$modelPrim = $product['modelPrim'] ? $product['modelPrim'] : NULL;
    	$series = $product['series'] != '' ? $product['series'] : NULL;
    	$seriesShort = $product['seriesShort'] ? $product['seriesShort'] : NULL;
    	$subCategoryName = $product['subCategoryName'] ? $product['subCategoryName'] : NULL;
    	$qty = $product['quantity'];
    	$manufacturer = $product['manufacturer'];
    	$cartridge = $product['cartridge'] ? $product['cartridge'] : NULL;
    	// begin Transaction 
    	$this->db->autocommit(false);
    	$q = "INSERT INTO 
			`Product` 
			(
				`prod_PureCode`,
				`prod_ProductCode`,
				`prod_ProductTypeID`,
				`prod_Model`,
				`prod_ModelPrim`,
				`prod_Series`,
				`prod_SeriesShort`,
				`prod_SubCategoryName`,
				`prod_Qty`,
				`prod_Manufacturer`,
				`prod_Cartridge`
			)
    		VALUES 
    		(?,?,?,?,?,?,?,?,?,?,?)";

		$stmt = $this->db->prepare($q);
		$stmt->bind_param(
			'ssisssssisi',
			$pureCode,
			$productCode,
			$productTypeId,
			$model,
			$modelPrim,
			$series,
			$seriesShort,
			$subCategoryName,
			$qty,
			$manufacturer,
			$cartridge
		);

		$res = $stmt->execute();

		if(!$res){
			$hlp->sessSetFlash($this->db->error, 'flashFail');
			header('location: createProduct.php');
			exit;
		} 
		else {
			unset($_SESSION['createProduct']);
			$prod_id = $this->db->insert_id;
			$_SESSION['createProduct'] = $prod_id;
			
			if( count($_POST['productSpecific']) ) {
				$cc = $_POST['productSpecific']['cc'] ? $_POST['productSpecific']['cc'] : NULL;
				$rating = $_POST['productSpecific']['rating'] ? $_POST['productSpecific']['rating'] : NULL;
				$standards = $_POST['productSpecific']['standards'] ? $_POST['productSpecific']['standards'] : NULL;
				$compatability = $_POST['productSpecific']['compatability'] ? $_POST['productSpecific']['compatability'] : NULL;
				$overloadProtection = $_POST['productSpecific']['overloadProtection'] ? $_POST['productSpecific']['overloadProtection'] : NULL;
				$shortProtection = $_POST['productSpecific']['shortProtection'] ? $_POST['productSpecific']['shortProtection'] : NULL;
				$reservedProtection = $_POST['productSpecific']['reservedProtection'] ? $_POST['productSpecific']['reservedProtection'] : NULL;
				$note = $_POST['productSpecific']['note'] ? $_POST['productSpecific']['note'] : NULL;
				$iloVar = $_POST['productSpecific']['iloVar'] ? $_POST['productSpecific']['iloVar'] : NULL;

				$q = "INSERT INTO 
					`base_ProductSpecific` 
					(
						`psp_ProductCodeID`,
						`psp_PureCode`,
						`psp_typeID`,
						`psp_cc`,
						`psp_rating`,
						`psp_standards`,
						`psp_compatability`,
						`psp_overloadProtection`,
						`psp_shortProtection`,
						`psp_reversedProtection`,
						`psp_note`,
						`psp_iloVar`,
						`psp_timeStamp`
					)
		    		VALUES 
		    		(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

				$stmt = $this->db->prepare($q);
				
				$stmt->bind_param(
					'isisssssssss',
					$prod_id,
					$pureCode,
					$productTypeId,
					$cc,
					$rating,
					$standards,
					$compatability,
					$overloadProtection,
					$shortProtection,
					$reservedProtection,
					$note,
					$iloVar
				);

				$res = $stmt->execute();
			}

			$result = $this->db->commit();

			if(! $result) {
				$hlp->sessSetFlash($this->db->error, 'flashFail');
				header('location: createProduct.php');
				exit;
			}

			$hlp->sessSetFlash('Product created!', 'flashSuccess');
			header('location: productsInsertFilter.php');
		}
    }

    //Get all products (productCodes) available on a Sales Channel
    function getProductsByByChannel($channel, $published = NULL){
    	$andWhere = NULL;

    	if ($published){
    		$andWhere = "AND `p2c_Published` = '1'";
    	}

    	$q = "SELECT *
				FROM
					`base_Channel`
				JOIN `base_Product_Channel`
				ON `p2c_ChannelID` = `cha_ID`

				JOIN `Product`
				ON `prod_ID` = `p2c_ProductID`

				WHERE
					`cha_ID` = '$channel'
				$andWhere
		";

		$res = $this->db->query($q);

		return $res->fetch_all(MYSQLI_ASSOC);
    }


	// Gel all products by Brand, Type, Category
	function getProducts_channel_Filter($conditions){
		$andWhere = NULL;
		$brand = $type = $category = $productCode = $pureCode = NULL;

		if (is_array($conditions)){
			extract($conditions);
			$brand = $this->esc($brand);
			$type = $this->esc($type);
			$category = $this->esc($category);
			$productCode = $this->esc($productCode);
			$pureCode = $this->esc($pureCode);
		}

		if ($brand){
			$andWhere .= "AND `prod_Manufacturer` = '$brand' ";
		}
		if ($type){
			$andWhere .= "AND `prod_ProductTypeID` = '$type' ";
		}
		if ($category){
			$andWhere .= "AND `pure_categoryID` = '$category' ";
		}
		if ($productCode){
			$andWhere .= "AND `prod_productCode` = '$productCode' ";
		}
		if ($pureCode){
			$andWhere .= "AND `pure_Code` = '$pureCode' ";
		}

		$q = "SELECT *
				FROM
					`base_Channel`

				JOIN `base_Product_Channel`
				ON `p2c_ChannelID` = `cha_ID`

				JOIN `base_Price`
				ON `prc_ChannelID` = `cha_ID`

				JOIN `Product`
				ON `prod_ID` = `prc_ProductID`

				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`

				WHERE
				1=1
				$andWhere
			";

		$res = $this->db->query($q);

		return $res->fetch_all(MYSQLI_ASSOC);
	}


	// Get all products with pending (price) change -> Generate feed
	function getProductsPricePending(){
		$q = "SELECT *
				FROM
					`base_Price`

				JOIN `base_Channel`
				ON `cha_ID` = `prc_ChannelID`

				JOIN `Product`
				ON `prod_ID` = `prc_ProductID`

				WHERE `prc_Pending` = '1'
		";

		$res = $this->db->query($q);

		return d($res->fetch_all(MYSQLI_ASSOC));
	}


	function clearPendingStatus($channelID){
		$q = "UPDATE `base_Price`
				SET
					`prc_Pending` = '0'
				WHERE
					`prc_ChannelID` = '$channelID'
			";

		$res = $this->db->query($q);
		if (!$this->db->error){
			return $this->db->affected_rows;
		}
		else{
			die($this->db->error);
		}
	}

	function getProductsFilter($conditions, $ids = NULL, $p2c = false){
		$andWhere = NULL;
		$brand = $type = $category = $productCode = $pureCode = NULL;


		if ($ids){
			foreach ($ids as $id){
				if ($p2c){
					$andWhere .= " `p2c_ID` = '$id' OR";
				}
				else{
					$andWhere .= " `prod_ID` = '$id' OR";
				}
			}
			$andWhere = trim($andWhere, 'OR');	//trim the trailing 'OR'
		}
		else{
			if (is_array($conditions)){
				extract($conditions);
				$brand = $this->esc($brand);
				$type = $this->esc($type);
				$category = $this->esc($category);
				$productCode = $this->esc($productCode);
				$pureCode = $this->esc($pureCode);
				$channel = $this->esc($channel);
				$key = $this->esc($key);
				$frn = $this->esc($frn);
			}
			$andWhere = ' 1 = 1 ';
			if ($brand){
				$andWhere .= "AND `prod_Manufacturer` LIKE '%$brand%' ";
			}
			if ($type){
				$andWhere .= "AND `prod_ProductTypeID` = '$type' ";
			}
			if ($category){
				$andWhere .= "AND `pure_categoryID` = '$category' ";
			}
			if ($productCode){
				$andWhere .= "AND `prod_productCode` LIKE '%$productCode%' ";
			}
			if ($pureCode){
				$andWhere .= "AND `pure_Code` = '$pureCode' ";
			}
			if($channel){
				$andWhere .= "AND `p2c_ChannelID` = '$channel' ";
			}
			if($key){
				$andWhere .= "AND `p2c_key` = '$key' ";
			}
			if($frn == '2'){	//Not foreign only
				$andWhere .= "AND `p2c_key` = '0' ";
			}
			elseif($frn){		//Foreign only OR all products
				$andWhere .= "AND `p2c_key` != '0' ";
			}
		}

		$q = "SELECT *
				FROM `base_Product_Channel`
				JOIN `Product`
				ON `prod_ID` = `p2c_ProductID`
				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`
				JOIN `base_Channel`
				ON `cha_ID` = `p2c_ChannelID`
				LEFT JOIN `base_Price`
				ON  `prc_ID` = `p2c_PricePolicy`
				JOIN `ProductType`
				ON `prod_ProductTypeID` = `pt_ID`
				WHERE
				$andWhere
			";
		$res = $this->db->query($q);

		return $res->fetch_all(MYSQLI_ASSOC);
	}

	function getProductByPureAndQty($pureCode = NULL, $qty = NULL, $prod_Manufacturer = NULL, $prod_ProductTypeID = NULL){
		//can select more fields if needed
		$q = "SELECT `prod_Model`, `prod_Series`, `prod_Manufacturer`
				FROM `Product`
				WHERE 1=1";
			$q .= $pureCode ? " AND `prod_pureCode` = '$pureCode'" : '';
			$q .= $qty ? " AND `prod_Qty` = '$qty'" : '';
			$q .= $prod_Manufacturer ? " AND `prod_Manufacturer` = '$prod_Manufacturer'" : '';
			$q .= $prod_ProductTypeID ? " AND `prod_ProductTypeID` = '$prod_ProductTypeID'" : '';
			$q .= " ORDER BY `prod_Manufacturer` ASC";

		$res = $this->db->query($q);

		return $res->fetch_all(MYSQLI_ASSOC);
	}

	function prodWithCustomPrice($conditions){
		$andWhere = NULL;
		$brand = $type = $category = $productCode = $pureCode = NULL;

		if (is_array($conditions)){
			extract($conditions);
			$brand = $this->esc($brand);
			$type = $this->esc($type);
			$category = $this->esc($category);
			$productCode = $this->esc($productCode);
			$pureCode = $this->esc($pureCode);
			$channel = $this->esc($channel);
		}
		$andWhere = '`prc_ProductID` != "0"
				AND
				 	`prc_PureCode` = "0"';
		if ($brand){
			$andWhere .= "AND `prod_Manufacturer` LIKE '%$brand%' ";
		}
		if ($type){
			$andWhere .= "AND `prod_ProductTypeID` = '$type' ";
		}
		if ($category){
			$andWhere .= "AND `pure_categoryID` = '$category' ";
		}
		if ($productCode){
			$andWhere .= "AND `prod_productCode` LIKE '%$productCode%' ";
		}
		if ($pureCode){
			$andWhere .= "AND `pure_Code` = '$pureCode' ";
		}
		if($channel){
			$andWhere .= "AND `p2c_ChannelID` = '$channel' ";
		}

		$q = "SELECT *
				FROM `base_Product_Channel`
				JOIN `Product`
				ON `prod_ID` = `p2c_ProductID`
				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`
				JOIN `base_Channel`
				ON `cha_ID` = `p2c_ChannelID`
				LEFT JOIN `base_Price`
				ON  `prc_ID` = `p2c_PricePolicy`
				JOIN `ProductType`
				ON `prod_ProductTypeID` = `pt_ID`
				WHERE
				$andWhere
			";
		$res = $this->db->query($q);
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	//DEPRECATED | check if it's used
	function getAllAvailableProducts(){

		//log it
    	$logData = array('action' => __FUNCTION__, 'message' => 'Use of deprecated function', 'data' => json_encode($_POST));
    	helpers::logIt($logData);

		$q = "SELECT `Product`.*, `base_Product_Channel`.*, `pt_Name`
				FROM `base_Product_Channel`
				JOIN `Product`
				ON `p2c_ProductID` = `prod_ID`
				JOIN `ProductType`
				ON `pt_ID` = `prod_ProductTypeID`

				 ";
		$res = $this->db->query($q);
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	function getProducts($conditions){
		foreach ($conditions as $cond){
			if ($cond['value']){
				$value = explode(',', $cond['value']);
				$value = implode('","', $value);
				$andWhere .= ' AND `'.$cond['field'].'` IN ("'.$value.'")';
			}
		}

		$q = "SELECT `prod_ID`, `prod_ProductCode`, `prod_PureCode`, `prod_Manufacturer`, `pt_Name`
				FROM `Product`
				JOIN `ProductType`
				ON `prod_ProductTypeID` = `pt_ID`
				WHERE
				`prod_isActive` = 1
				$andWhere
			";

		$res = $this->db->query($q);
		$allProducts = $res->fetch_all(MYSQLI_ASSOC);

		return $allProducts;
	}

	//get items for select2
	function getProductsAjax($conditions){
		if ($conditions != ''){
			$where = ' WHERE `'.$conditions['field'].'` LIKE "%'.$conditions['value'].'%" ';
		}
		else { $where = NULL; }

		$q = "SELECT DISTINCT `".$conditions['field']."`
				FROM `Product`
				JOIN `ProductType`
				ON `prod_ProductTypeID` = `pt_ID`
				$where
				LIMIT 40
		";
		$res = $this->db->query($q);
		$allProducts = $res->fetch_all(MYSQLI_ASSOC);

		return $allProducts;
	}
	//get items for select2
	function getCategoriesAjax($conditions){
		if ($conditions != ''){
			$where = ' WHERE `'.$conditions['field'].'` LIKE "%'.$conditions['value'].'%"';

			$where .= is_numeric($conditions['value']) ? 'OR `bscat_CatID` = "'.$conditions['value'].'"' : '';
			$where .= is_numeric($conditions['value']) ? 'OR `bscat_ParentCatID` = "'.$conditions['value'].'"' : '';
		}
		else { $where = 'WHERE 1=1 '; }

		$q = "SELECT
				CONCAT(`bscat_CatID`, IF (`bscat_ParentCatID` IS NULL, '', CONCAT(',',`bscat_ParentCatID`))) as 'bscat_id',
				CONCAT(`bscat_CatName`, ' (', `bscat_CatID`, IF (`bscat_ParentCatID` IS NULL, '', CONCAT(',',`bscat_ParentCatID`)),')') as bscat_CatName
				FROM `base_Categories`
				$where
				AND
					`bscat_ChannelID` = {$_SESSION['filter']['channel']}
				LIMIT 40
		";

		$res = $this->db->query($q);
		$allProducts = $res->fetch_all(MYSQLI_ASSOC);
		return $allProducts;
	}

	function getCategoriesAsStringAjax($conditions) {
		$searched = $conditions['value'];

		$allResQ = "SELECT * FROM `base_Categories` WHERE `bscat_ChannelId` = {$_SESSION['filter']['channel']} AND `bscat_CatName` LIKE '%$searched%' LIMIT 40";
		$res = $this->db->query($allResQ);
		$categories = $res->fetch_all(MYSQLI_ASSOC);
		
		$result = array();
		foreach ($categories as $category) {
			$id = $category['bscat_CatID'];
			$currentCategory = $this->getCategoriesAsString($id);
			array_push($result, array('bscat_id' => $id, 'bscat_CatName' => $currentCategory));
		}
		
		return $result;
	}

	function getCategoriesAsString($categoryId) {
		$q = "SELECT * FROM `base_Categories` WHERE `bscat_ChannelID` = {$_SESSION['filter']['channel']} AND `bscat_CatID` = $categoryId";
		$res = $this->db->query($q);
		$category = $res->fetch_all(MYSQLI_ASSOC)[0];

		$result = $category['bscat_CatName'];
		$parentId = intval($category['bscat_ParentCatID']);

		while ($parentId) {
			$parentQ = "SELECT * FROM `base_Categories` WHERE `bscat_ChannelID` = {$_SESSION['filter']['channel']} AND `bscat_CatID` = $parentId";
			$res = $this->db->query($parentQ);
			$parent = $res->fetch_all(MYSQLI_ASSOC);
			$result = $parent[0]['bscat_CatName'] . '/' . $result;
			$parentId = intval($parent[0]['bscat_ParentCatID']);
		}

		return $result;
	}

	function getCategories($channelId) {
		$q = "SELECT * FROM `base_Categories` WHERE `bscat_ChannelID` = $channelId";
		$res = $this->db->query($q);
		$categories = $res->fetch_all(MYSQLI_ASSOC);
		$result = array();

		foreach ($categories as $index => $category) {
			$name = $this->getCategoriesAsString($category['bscat_CatID']);
			$result[$index] = array('bscat_CatID' => $category['bscat_CatID'], 'name' => $name);
		}

		return $result;
	}

	function getExistingProducts(){	//from input
		$i = 0;
		//existing products check
		foreach ($_POST['item'] as $product){
			if ($product['p2c_ChannelID'] == ''){
				$product['p2c_ChannelID'] = $_POST['channelIn'];
			}
			$q = "SELECT * FROM `base_Product_Channel`
					WHERE
						`p2c_ProductID` = '".$product['prod_ID']."'
					AND
						`p2c_ChannelID` = '".$product['p2c_ChannelID']."'
					AND
						`p2c_key` = 0
				";
			$res = $this->db->query($q);

			if ($res->num_rows > 0){
				$res = $res->fetch_all(MYSQLI_ASSOC);
				$existing[] = $res[0];
			}
		}
		return $existing;
	}

	function insertProducts($productsArray, $overwrite){

	/*-----*/
	// error_reporting(E_ALL);
	// ini_set('display_startup_errors',1);
	// ini_set('display_errors',1);
	// error_reporting(-1);
	/*-----*/

		$errorCount = 0;
		$i=0;
		$return['errorCount'] = 0;
		$return['itemsInserted'] = 0;


		//custom fields default values
		//we get the Channel ID from the first product in array!
		require_once('../scripts/'.$productsArray[0]['p2c_ChannelID'].'-import.php');

		//lets loop the products
		foreach ($productsArray as $product){
			extract($product);

			$insert = true;		//will insert new product if it's not overwritten
			if ($overwrite == true){

				$q = "SELECT `p2c_ID`
						FROM `base_Product_Channel`
						WHERE
						`p2c_ProductID` = '$prod_ID'
						AND
						`p2c_ChannelID` = '$p2c_ChannelID'
						AND
						`p2c_key` = 0";
				$res = $this->db->query($q);

				if ($this->db->error){
					$errorCount++;
					die($this->db->error);
				}
				if ($res->num_rows > 1){
					die('Multiple products with same ID and Channel found, stopping!');
				}
				else{
					$p2c_product = $res->fetch_all(MYSQLI_ASSOC);
					$p2c_ID = $p2c_product[0]['p2c_ID'];

					//lets update all but (array) $skip fields
					$customFieldsAction = populateCustomFields($p2c_ID, $product, array('p2c_SKU', 'p2c_Custom23'), $this);
					if ($customFieldsAction !== true){
						$errorCount++;
						//d($customFieldsAction);		//off
					}
					else{
						if ($this->db->affected_rows == 1 ){
							$insert = false;	//to skip insert action
							//generate Feed price for the new products
							$prc = new price;
							$dump = $prc->getPrice($product, $p2c_ChannelID, $xcompany = NULL, $p2c_ID, true);
							$i++;
						}
					}
				}
			}
			elseif ($overwrite == false){
				$q = "SELECT * FROM
						`base_Product_Channel`
					WHERE
						`p2c_ProductID` = '$prod_ID'
					AND
						`p2c_ChannelID` = '$p2c_ChannelID'
					AND
						`p2c_key` = ".$this->key;
				$res = $this->db->query($q);
				if ($res->num_rows > 0){
					//d('Duplicate not foreign product found!');
					continue; //skips current loop/inserting current item
				}
			}

			$csFields = $this->getCsFields($product);

			if ($insert === true){
				//insert
				$q = "INSERT INTO `base_Product_Channel`
						(
							".$csFields['fields']."
							`p2c_ProductID`,
							`p2c_ChannelID`,
							`p2c_Published`,
							`p2c_key`,
							`p2c_Custom23`
						)
						VALUES
						(
							".$csFields['values']."
							'$prod_ID',
							'$p2c_ChannelID',
							'1',
							".$this->key.",
							'$Category'
				)";

				$this->db->query($q);
				if ($this->db->error){
					$errorCount++;
					d($this->db->error);		//off
				}
				// and use function() to create default content for custom fields
				$customFieldsAction = populateCustomFields($this->db->insert_id, $product, array(), $this);
				if ($customFieldsAction !== true){
					$errorCount++;
					d($customFieldsAction);		//off
				}

				//generate Feed price for the new products
				$prc = new price;
				$dump = $prc->getPrice($product, $p2c_ChannelID, $xcompany = NULL, $this->db->insert_id, true);
				$i++;
			}

			$return['errorCount'] = $errorCount;
			$return['itemsInserted'] = $i;
		}	//foreach

		//log it
    	$logData = array('action' => __FUNCTION__, 'message' => 'Inserting products', 'data' => json_encode($_POST));
		if ($return['errorCount'] > 0){
			$logData['isError'] = 1;
		}
    	helpers::logIt($logData);

		$_SESSION['selectedProducts'] = NULL;
		$_SESSION['existingProducts'] = NULL;

		return $return;
	}

	function productTitleRefresh($products, $fields, $channelID, &$hlp){
		require_once('../scripts/'.$channelID.'-import.php');

		foreach ($products as $product) {
			$q = "SELECT
				    `trm_Name`,
				    `pt_ID`,
				    `pt_Name`,
				    `prod_SubCategoryName`,
				    `prod_ID`,
				    `prod_ProductCode`,
				    `prod_PureCode`,
				    `prod_Manufacturer`,
				    `prod_Qty`

					FROM `Product`

					JOIN `PureCode`
					ON `prod_PureCode`      = `pure_Code`

					JOIN `PureCodeTerminal`
					ON `pure_TerminalID`    = `trm_ID`

					JOIN `ProductType`
					ON `prod_ProductTypeID` = `pt_ID`

					JOIN `base_Product_Channel`
					ON `p2c_ProductID` = `prod_ID`

				   WHERE `p2c_ID` = '".$product."'";

			$res = $hlp->qry($q, true);
			$productArray = $res->fetch_all(MYSQLI_ASSOC);
			$productArray[0]['p2c_ChannelID'] = $channelID;

			//populateCustomFields($product, $productArray, $skip = array(), $update = false);
			$overwrite = true;
			$insertAction = $this->insertProducts($productArray, $overwrite);
			$error += $insertAction['errorCount'];
			$inserted += $insertAction['itemsInserted'];
		}
		return array('errorCount' => $error, 'itemsInserted' => $inserted);
	}

	function productInfoUpdate($productInfo){

		//log it
    	$logData = array('action' => __FUNCTION__, 'message' => 'Edit Product info', 'data' => json_encode($_POST));
    	helpers::logIt($logData);

		//sanitize input
		foreach ($productInfo as $k => $prodInfo){
			$productInfo[$k] = $this->db->real_escape_string($prodInfo);
		}

		//count fields in table
		$q = "SELECT * FROM `base_Channel`
				WHERE
					`cha_code` = '".$productInfo['cha_code']."'";
		$res = $this->db->query($q);
		$channel = $res->fetch_all(MYSQLI_ASSOC);
		$channel = $channel[0];
		$p2c_colCount = count($channel);

		for ($i = 0; $i <= $p2c_colCount; $i++){
			if (isset($productInfo['p2c_Custom'.$i])){
				$andUpdate .= '`p2c_Custom'.$i.'` = "'.$productInfo['p2c_Custom'.$i].'", ';				//TODO...
			}
		}
		$andUpdate = trim($andUpdate, ', ');
		//concatenate $andUpdate...
		$q = "UPDATE `base_Product_Channel`
				SET
					`p2c_Title` = '".$productInfo['p2c_Title']."',
					`p2c_Description` = '".$productInfo['p2c_Description']."',
					`p2c_ShortDescription` = '".$productInfo['p2c_ShortDescription']."',
					`p2c_AutoOptionSet` = '".$productInfo['p2c_AutoOptionSet']."',
					$andUpdate
				WHERE
					`p2c_ID` = '".$productInfo['p2c_ID']."'
			";
			//Removed from quyery:	`p2c_OptionSetID` = '".$productInfo['p2c_OptionSetID']."',
		$this->db->query($q);

		if ($this->db->error){
			return $this->db->error;
		}
		else{
			return true;
		}
	}


	function deleteProducts($products){
		foreach ($products as $p2c_ID){
			$q = "DELETE FROM `base_Product_Channel`
					WHERE `p2c_ID` = '$p2c_ID'";
			$this->db->query($q);
		}
		if($this->db->error){
			$return = $this->db->error;
		}
		else{
			$return = true;
		}

		//log it
    	$logData = array('action' => __FUNCTION__,
						'message' => 'Deleting Products.('.$return.')',
						'data' => json_encode($_POST));
    	helpers::logIt($logData);

		return $return;
	}

	function ordersGraphData($prod_ProductCode, $p2c_ID, $group = 'DAY'){	//single product
		$this->db->select_db("OrderManager");

		if ($group == 'WEEK'){$wkExt = ',3';}

		//get orders count
		$q = "SELECT count(`oit_ID`) as oit_count, `ord_orderDateTime`
				FROM
					`OrderDetail`

				JOIN `Order`
				ON `oit_OrderID` = `ord_ID`

				WHERE
					`ord_orderDateTime` > NOW() - INTERVAL 3 MONTH
				AND
					`oit_ProductCode` = '$prod_ProductCode'

				GROUP BY $group(`ord_orderDateTime`$wkExt)
				ORDER BY `ord_orderDateTime` ASC
				";
		$res = $this->db->query($q);
		$orders = $res->fetch_all(MYSQLI_ASSOC);
		foreach ($orders as &$ord){
			$ord = array(''.(strtotime($ord['ord_orderDateTime'])*1000).','.$ord['oit_count']);		//strtotime() * 1000  JavaScript format
		}
		$orders = array('name'=>'Orders', 'data' => $orders);
		$this->db->select_db("CP");	//and back to `CP` db

		//get price history
		$q = "SELECT *
				FROM
					`base_FeedPriceLog`

				WHERE
					`fpl_time` > NOW() - INTERVAL 3 MONTH
				AND
					`fpl_p2cID` = '$p2c_ID'


				GROUP BY (`fpl_time`)
				ORDER BY `fpl_time` ASC
				";
		$res = $this->db->query($q);

		$prcHistory = $res->fetch_all(MYSQLI_ASSOC);
		foreach ($prcHistory as &$his){
			$his = array(''.(strtotime($his['fpl_time'])*1000).','.$his['fpl_Price']);		//strtotime() * 1000  JavaScript format
		}
		$prcHistory = array('name'=>'PriceHistory', 'data' => $prcHistory);

		$return['prcHistory'] = $prcHistory;
		$return['orders'] = $orders;
		return $return;
	}

	function createDateRangeArray($strDateFrom,$strDateTo){
	    // takes two dates formatted as YYYY-MM-DD and creates an
	    // inclusive array of the dates between the from and to dates.

	    // could test validity of dates here but I'm already doing
	    // that in the main script

	    $aryRange=array();

	    $iDateFrom = mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo = mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }
	    return $aryRange;
	}

	//query products info and call the channel file
	function prepareExport($products, $returnResult){

		//to measure script time --------------------------------------------------------------
		$_SESSION['time'] = microtime(true);

		//log it
    	$logData = array('action' => __FUNCTION__,
						'message' => 'Exporting Products',
						'data' => json_encode($_POST));
    	helpers::logIt($logData);

		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes

		$hlp = new helpers;
		$andWhere = '';
		foreach ($products as $product){
			$andWhere .= ' `p2c_ID` = "'.$product.'" OR';
		}
		$andWhere = trim($andWhere, 'OR');

		$q = "SELECT
					`p2c_ID`,
					`p2c_SKU`,
					`p2c_ChannelID`,
					`p2c_Title`,
					`p2c_Price`,
					`p2c_SalePrice`,
					`p2c_Description`,
					`p2c_ShortDescription`,
					`p2c_AutoOptionSet`,
					`p2c_isGenuine`,

					`Texts`.*,

					`cha_code`,

					`pt_ID`,
					`pt_Name`,

					`prod_productCode`,
					`prod_Qty`,
					`prod_ID`,
					`prod_Manufacturer`,
					`prod_Model`,
					`prod_Cartridge`,

					`pure_Code`,
					`pure_productWeight`,
					`pure_Width`,
					`pure_Height`,
					`pure_Length`,
					`pure_AmpH`,
					`pure_Volts`,
					`trm_Name`,

					`p2c_Custom1`,
					`p2c_Custom2`,
					`p2c_Custom3`,
					`p2c_Custom4`,
					`p2c_Custom5`,
					`p2c_Custom6`,
					`p2c_Custom7`,
					`p2c_Custom8`,
					`p2c_Custom9`,
					`p2c_Custom10`,
					`p2c_Custom11`,
					`p2c_Custom12`,
					`p2c_Custom13`,
					`p2c_Custom14`,
					`p2c_Custom15`,
					`p2c_Custom16`,
					`p2c_Custom17`,
					`p2c_Custom18`,
					`p2c_Custom19`,
					`p2c_Custom20`,
					`p2c_Custom21`,
					`p2c_Custom22`,
					`p2c_Custom23`,
					`p2c_Custom24`,
					`p2c_Custom25`,
					`p2c_Custom26`,
					`p2c_Custom27`,
					`p2c_Custom28`,
					`p2c_Custom29`,
					`p2c_Custom30`,
					`p2c_Custom31`,
					`p2c_Custom32`,
					`p2c_Custom33`,
					`p2c_Custom34`,
					`p2c_Custom35`

				FROM `base_Product_Channel`

				JOIN `Product`
				ON `prod_ID` = `p2c_ProductID`

				JOIN `ProductType`
				ON `pt_ID` = `prod_ProductTypeID`

				JOIN `PureCode`
				ON `pure_Code` = `prod_PureCode`

				JOIN `PureCodeTerminal`
				ON `trm_ID` = `pure_TerminalID`

				JOIN `base_Channel`
				ON `cha_ID` = `p2c_ChannelID`

				JOIN `Texts`
				ON `txt_ChannelID` = `p2c_ChannelID`

				/* LEFT JOIN `base_OptionSet`
				ON `prod_PureCode` = `os_PureCode` AND `prod_Qty` = `os_Qty` AND `p2c_ChannelID` = `os_ChannelID` */

				WHERE
					`txt_TypeID` = `prod_ProductTypeID`
				AND `prod_isActive` = 1
				AND (
				$andWhere
				)
				";
		
		$res = $this->db->query($q, MYSQLI_USE_RESULT);
		$products = $res->fetch_all(MYSQLI_ASSOC);
		$products = array_values($this->removeOptions($products));	//test----

		if (count($products)){
			//include the appropriate file, "outputs" $toExport array for .csv
			include('../scripts/'.$products[0]['p2c_ChannelID'].'-export.csv.php');
			
			if($returnResult) {
				return $toExport;
			}

			$hlp->csv($toExport, $products[0]['cha_code'].'_'.date('l_jS_F_Y_H:i'));
		}
		else{
			$hlp->sessSetFlash('Not enough info for products.', 'flashFail');
			//log it
	    	$logData = array('action' => __FUNCTION__,
							'message' => 'Failed to retrieve product info.',
							'data' => json_encode(array('query' => addslashes(str_replace(array("\r\n", "\n", "\r"), ' ',$q)))),
							'log_isError' => '1');
	    	helpers::logIt($logData);
	    	//go away
			header('Location: productsViewList.php');
			exit();
		}

	}

	function setGenuine($products, $state){
		$q = "UPDATE `base_Product_Channel`
				SET `p2c_isGenuine` = $state
				WHERE `p2c_ID` IN (".implode(', ', $products).")";
		$this->db->query($q);
		if($this->db->error){
			$return = $this->db->error;
		}
		else{
			$return = true;
		}
		return $return;
	}

	function prodPriceHistoryGraphData($p2c_ID){
		$q = "SELECT `fpl_time`, `fpl_Price`
				FROM `base_FeedPriceLog`
				WHERE
					`fpl_p2cID` = '$p2c_ID'
				ORDER BY `fpl_time` ASC
				";
		$res = $this->db->query($q);
		$prcHistory = $res->fetch_all(MYSQLI_ASSOC);

		foreach ($prcHistory as &$row){
			$row['x'] = strtotime($row['fpl_time'])*1000;
			$row['y'] = $row['fpl_Price'];
		}

		return $prcHistory;
	}

	function getCsFields($product){
		$i = 0;

		foreach ($product as $k => $field){
			if (strpos($k, 'Custom') !== FALSE){
				$ret['fields'] .= "`$k`, ";
				$ret['values'] = "'$field', ";
			}
			$i++;
		}
		
		return $ret;
	}

	function removeOptions($products){
		return $products;
		//d($products);
		$tArra = array();
		foreach ($products as $product){
			if ($product['os_TypeID'] !== '0'){
				$tArra[] = $product['p2c_ID'];
			}
		}
		foreach ($products as $k => $product){
			if ($product['os_TypeID'] === '0'){
				if (in_array($product['p2c_ID'], $tArra)){
					unset($products[$k]);
				}
			}
		}
		//d($products);
		return $products;
	}

	function getPureCode($pureCode, $isById = false){
		$where = $isById ? "`pure_ID` = '$pureCode'" : "`pure_Code` = '$pureCode'" ;
		$pureCode = $this->esc($pureCode);
		$q = "SELECT *
				FROM `PureCode`
				WHERE $where";
		$result = $this->db->query($q);
		return $result->fetch_array(MYSQLI_ASSOC);
	}

    function esc($q){
    	return $this->db->real_escape_string($q);
    }


    function getProductsById($p2c_IDs) {
		for($i = 0; $i < count($p2c_IDs); ++$i) {
		    if ($i === 0) {
		    	$ids .= "`p2c_ID` = '$p2c_IDs[$i]' ";
		    } else {
				$ids .= "OR `p2c_ID` = '$p2c_IDs[$i]' ";
		    }
		}
		$q = "SELECT
				`p2c_ID`,
				`p2c_ChannelID`,
				`p2c_Custom18`,
				`p2c_SalePrice`,
				`p2c_Price`
			FROM
				`base_Product_Channel`
			WHERE
				$ids";
		$res = $this->db->query($q, MYSQLI_USE_RESULT);
		$products = $res->fetch_all(MYSQLI_ASSOC);
		$products = array_values($this->removeOptions($products));

		return $products;
    }

    function getFullUrlAndPrice($productsInfo){
    	$resultArr = array();
		for($i = 0; $i < count($productsInfo); ++$i) {
	    	if($productsInfo[$i]['p2c_ChannelID'] == 6){
				$resultUrl = $productsInfo[$i]['p2c_Custom18'];
			} elseif ($productsInfo[$i]['p2c_ChannelID'] == 7) {
				$resultUrl = "http://www.replaceupsbattery.com" . $productsInfo[$i]['p2c_Custom18'];
			} elseif ($productsInfo[$i]['p2c_ChannelID'] == 8) {
				$resultUrl = "http://www.batteryquality.com" . $productsInfo[$i]['p2c_Custom18'];
			} elseif ($productsInfo[$i]['p2c_ChannelID'] == 15) {
				$resultUrl = "http://www.slabatterystore.com" . $productsInfo[$i]['p2c_Custom18'];
			}

			if($productsInfo[$i]['p2c_SalePrice'] === "0.00" || is_null($productsInfo[$i]['p2c_SalePrice']) || $productsInfo[$i]['p2c_SalePrice'] > 0){
				$resultPrice = $productsInfo[$i]['p2c_Price'];
			} else {
				$resultPrice = $productsInfo[$i]['p2c_SalePrice'];
			}

			$resultArr[$i]['url'] = $resultUrl;
			$resultArr[$i]['price'] = $resultPrice;

		}
		return $resultArr;
    }

    function getAdGroupsKeywordsIdPrice($adsUrlPrice) {

    	foreach ($adsUrlPrice as $key => $ad) {
    		if ($key === 0) {
		    	$urlWhere .= "`aar_finalURL` = '" . $ad['url'] . "'";
		    } else {
		    	$urlWhere .= " OR `aar_finalURL` = '" . $ad['url'] . "'";
		    }
    	}

    	$q = "SELECT DISTINCT `aar_adGroupID`, `akr_keywordID`, `aar_finalURL`, `aar_date`
    		FROM
    			`AdWordsAdsReport`
    		LEFT JOIN
				`AdWordsKeywordReport`
			ON
				`aar_adGroupID` = `akr_adGroupID`
			WHERE
				`aar_date` = (SELECT MAX(aar_date) FROM `AdWordsAdsReport`)
			AND
				(" . $urlWhere . ") AND `akr_date` = `aar_date`";

    	$res = $this->db->query($q, MYSQLI_USE_RESULT);
		$ads = $res->fetch_all(MYSQLI_ASSOC);
		$ads = array_values($this->removeOptions($ads));

		$adsDetails = array();

		foreach ($ads as $key => $ad) {
			$adsDetails[$key]['keywordID'] = $ad['akr_keywordID'];
			$adsDetails[$key]['groupID'] = $ad['aar_adGroupID'];
			foreach ($adsUrlPrice as $adPrice) {
				if($ad['aar_finalURL'] === $adPrice['url']){
					$adsDetails[$key]['price'] = $adPrice['price'];
				}
			}
		}

		return $adsDetails;
    }

    function getCategoriesIds($categories, $channelId) {
    	$categories = explode(';', $categories);
    	
    	$result = array();
    	// TODO find solution for category with '/' in their name
    	foreach ($categories as $category) {
    		$categoriesParts = explode('/', $category);
    		$lastChildCategory = $categoriesParts[count($categoriesParts) - 1];
    		$lastChildParent = $categoriesParts[count($categoriesParts) - 2];
    		
    		$q = "SELECT `child`.`bscat_CatID` FROM `base_Categories` as `child`
				JOIN `base_Categories` as `parent` ON `child`.`bscat_ParentCatID` = `parent`.`bscat_CatID`
				WHERE `child`.`bscat_ChannelID` = $channelId
				AND `parent`.`bscat_ChannelID` = $channelId
				AND `child`.`bscat_CatName` LIKE '%$lastChildCategory%' 
				AND `parent`.`bscat_CatName` LIKE '%$lastChildParent%'";

    		$res = $this->db->query($q);
    		
    		if($res && $res->num_rows == 1) {
    			$categoryId = $res->fetch_all(MYSQLI_ASSOC)[0]['bscat_CatID'];
    			$categoryId = intval($categoryId);
    		}
    		else {
    			return false;
    		}

    		array_push($result, $categoryId);
    	}

    	// var_dump($result); die;
    	return $result;
    }

    function importProducts($products) {
    	$hlp = new helpers;

    	// check the channel
    	$channelId = $_SESSION['filter']['channel'];

    	if($channelId == '6') {
    		$result = $this->importProductsInVolusion($products);
    	} 
    	elseif($channelId == '7' || $channelId == '15') {
    		$result = $this->importProductsInBigCommerce($products, $channelId);
    	}

    	return $result;
    }

    private function importProductsInVolusion($products) {
    	$productsToCreate = $this->prepareExport($products, true);

    	foreach ($productsToCreate as $productToCreate) {
    		$data[$productToCreate['productcode']] = array(
	    		'ProductCode' => $productToCreate['productcode'],
	    		'Vendor_PartNo' => $productToCreate['vendor_partno'],
	    		'ProductName' => $productToCreate['productname'],
	    		'HideProduct' => $productToCreate['hideproduct'],
	    		'Photos_Cloned_From' => $productToCreate['photos_cloned_from'],
	    		'AutoDropShip' => $productToCreate['autodropship'],
	    		'DoNotAllowBackOrders' => $productToCreate['donotallowbackorders'],
	    		'ProductKeywords' => htmlspecialchars($productToCreate['productkeywords']),
	    		'ProductNameShort' => $productToCreate['productnameshort'],
	    		'ProductDescription' => htmlspecialchars($productToCreate['productdescription']),
	    		'ProductDescriptionShort' => htmlspecialchars($productToCreate['productdescriptionshort']),
	    		'METATAG_Title' => $productToCreate['metatag_title'],
	    		'METATAG_Description' => htmlspecialchars($productToCreate['metatag_description']),
	    		'METATAG_Keywords' => htmlspecialchars($productToCreate['metatag_keywords']),
	    		'ProductWeight' => $productToCreate['productweight'],
	    		'FreeShippingItem' => $productToCreate['freeshippingitem'],
	    		'AllowPriceEdit' => $productToCreate['allowpriceedit'],
	    		'ProductPrice' => $productToCreate['productprice'],
	    		'SalePrice' => $productToCreate['saleprice'],
	    		'Availability' => $productToCreate['availability'],
	    		'Photo_AltText' => $productToCreate['photo_alttext'],
	    		'Hide_YouSave' => $productToCreate['hide_yousave'],
	    		'Hide_FreeAccessories' => $productToCreate['hide_freeaccessories'],
	    		'ProductManufacturer' => $productToCreate['productmanufacturer'],
	    		'Hide_When_OutOfStock' => $productToCreate['hide_when_outofstock'],
	    		'AddToPO_Now' => $productToCreate['addtopo_now'],
	    		'EnableMultiChildAddToCart' => $productToCreate['enablemultichildaddtocart'],
	    		'EnableOptions_InventoryControl' => $productToCreate['enableoptions_inventorycontrol'],
	    		'Length' => $productToCreate['length'],
	    		'Width' => $productToCreate['width'],
	    		'Height' => $productToCreate['height'],
	    		'Ships_By_Itself' => $productToCreate['ships_by_itself'],
	    		'Oversized' => $productToCreate['oversized'],
	    		'Additional_Handling_Indicator' => $productToCreate['additional_handling_indicator']
	    		
	    		// NULL PROPS
	    		// 'TaxableProduct' => $productToCreate['taxableproduct'],
	    		// 'Fixed_ShippingCost' => $productToCreate['fixed_shippingcost'],
	    		// 'Fixed_ShippingCost_Outside_LocalRegion' => $productToCreate['fixed_shippingcost_outside_localregion'],
	    		// 'OrderFinished_Note' => $productToCreate['orderfinished_note']
	    	);
    	}

    	$volusion = new VolusionConnection();
    	$xml = $volusion->buildXmlBetter($data);
    	$result = $volusion->createProduct($xml);

    	return $result;
    }

    private function importProductsInBigCommerce($products, $channelId) {
    	$bigCommerce = new BigCommerceConnection($channelId);
		$productsToCreate = $this->prepareExport($products, true);
		$errors = array();
		
		foreach ($productsToCreate as $key => $product) {
			// Product object properties - The required properties for creating a product in Big Commerce are name, price, categories, type, availability, weight
			$name = $product['Product Name'];
			$price = $product['Price'];
			$sale_price = $product['Sale Price'];
			
			$categories = $this->getCategoriesIds($product['Category'], $channelId);
			if(!$categories) {
				$categories = array(0);
			}

			$type = "physical";
			$availability = "available";
			if(strtolower($product['Allow Purchases?']) == 'n') {
				$availability = "disabled";
			}

			$weight = $product['Product Weight'];

			// Other properties
			$height = $product['Product Height'];
			$width = $product['Product Width'];
			$depth = $product['Product Depth'];
			$sku = $product['Product Code/SKU'];
			$is_free_shipping = $this->convertToBoolean($product['Free Shipping']);
			$availability_description = $product['Product Availability'];
			$search_keywords = $product['Search Keywords'];
			$condition = ucfirst($product['Product Condition']); // enum New Used Refurbished
			$is_condition_shown = $this->convertToBoolean($product["Show Product Condition?"]); // bool
			$bin_picking_number = $product['Bin Picking Number'];
			$description = $product["Product Description"];
			$cost_price = $product["Cost Price"];
			$retail_price = $product["Retail Price"];
			$fixed_cost_shipping_price = $product["Fixed Shipping Cost"];
			$warranty = $product["Product Warranty"];
			$inventory_tracking = $product["Track Inventory"]; // enum - none, simple, sku
			$inventory_level = $product["Current Stock Level"]; // int
			$inventory_warning_level = $product["Low Stock Level"]; // int
			$page_title = $product["Page Title"];
			$meta_keywords = $product["Meta Keywords"];
			$meta_description = $product["Meta Description"];
			$is_visible = $this->convertToBoolean($product['Product Visible?']);
			$custom_url = $product["Product URL"];
			$brandName = $product["Brand Name"];
			$brand_id = 0;

			// get brand's id
			$brand = $bigCommerce->getBrandByName($brandName);
			if($brand) {
				$brand_id = $brand[0]->id;
			}
				
				// NULL Props
				// // Google Product Search Mappings Resource
				// $global_trade_item_number = $product["GPS Global Trade Item Number"]; // str
				// $manufacturer_part_number = $product["GPS Manufacturer Part Number"]; // str
				// $gender = $product["GPS Gender"];
				// $age_group = $product["GPS Age Group"];
				// $color = $product["GPS Color"];
				// $size = $product["GPS Size"];
				// $material = $product["GPS Material"];
				// $pattern = $product["GPS Pattern"]; // pattern
				// $category_id = $product["GPS Category"]; // int
				// $enabled = $product["GPS Enabled"]; // bool
				// // events
				// $event_date_field_name = $product["Event Date Name"];
				// $event_date_start = $product["Event Date Start Date"];
				// $event_date_end = $product["Event Date End Date"];
				// $sort_order = $product["Sort Order"]; // int
				// // MYOB
				// $myob_asset_account = $product["MYOB Asset Acct"]; // str
				// $myob_income_account = $product["MYOB Income Acct"]; // str
				// $myob_expense_account = $product["MYOB Expense Acct"]; // str
				// // Image 2 ?!
			// $product["Product Image ID - 2"];
				// $product["Product Image File - 2"];
				// $product["Product Image Description - 2"]
				// $product["Product Image Is Thumbnail - 2"]
				// $product["Product Image Sort - 2"]
				// $upc = $product["Product UPC/EAN"]; // str
	 		// $tax_class_id = $product["Product Tax Class"]; //int
	 		// // IMAGE
	 		// $id = $product["Product Image ID - 1"];
	 		// $product["Product Image Sort - 1"] // int

			$productToImport = array(
				'name' => $name,
				'price' => $price,
				'sale_price' => $sale_price,
				'categories' => $categories,
				'type' => $type,
				'availability' => $availability,
				'weight' => $weight,
				'height' => $height,
				'width' => $width,
				'depth' => $depth,
				'sku' => $sku,
				'is_free_shipping' => $is_free_shipping,
				'is_visible' => $is_visible,
				'availability_description' => $availability_description,
				'search_keywords' => $search_keywords,
				'condition' => $condition,
				'is_condition_shown' => $is_condition_shown,
				'bin_picking_number' => $bin_picking_number,
				'description' => $description,
				'cost_price' => $cost_price,
				'retail_price' => $retail_price,
				'fixed_cost_shipping_price' => $fixed_cost_shipping_price,
				'warranty' => $warranty,
				'inventory_tracking' => $inventory_tracking,
				'inventory_level' => $inventory_level,
				'inventory_warning_level' => $inventory_warning_level,
				'page_title' => $page_title,
				'meta_keywords' => $meta_keywords,
				'meta_description' => $meta_description,
				'custom_url' => $custom_url,
				'brand_id' => $brand_id
			);
			
			$productToImport = json_encode($productToImport);
			$importedProduct = $bigCommerce->createProduct($productToImport);
			
			if(! $importedProduct->id) {
				array_push($errors, $name);
			}
			
			$updateQry = "UPDATE `base_Product_Channel` SET `p2c_Custom24` = {$importedProduct->id} WHERE `p2c_id` = {$products[$key]}";
			$this->db->query($updateQry);
			
			if($importedProduct->id) {
				// create product image
 				$imageData = array(
 					'image_file' => $product["Product Image File - 1"],
 					'description' => $product["Product Image Description - 1"],
 					'is_thumbnail' => $this->convertToBoolean($product["Product Image Is Thumbnail - 1"])
				);

 				$imageData = json_encode($imageData);
				$productImage = $bigCommerce->createProductImage($importedProduct->id, $imageData);

				echo '<hr>';
			}
		}

		if(count($errors) > 0) {
			return $errors;
		}

		return true;
    }

    function deactivateProducts($products) {
    	foreach ($products as $p2c_ID){
			$q = "UPDATE 
    			`base_Product_Channel` 
			JOIN 
				`Product` 
			ON 
				`prod_id` = `p2c_ProductID` 
			JOIN 
				`ProductType` 
			ON 
				`prod_ProductTypeID` = `pt_id` 
			SET 
				`prod_isActive` = 0,
				`prod_ProductTypeId` = 61
			WHERE 
				`p2c_id` = $p2c_ID";

			$res = $this->db->query($q);
		}

		if($this->db->error){
			$return = $this->db->error;
		}
		else{
			$return = true;
		}

		return $return;
    }

    function isSpecialOffer($productCode) {
    	$q = "SELECT `prod_ProductTypeID` FROM `Product` WHERE `prod_ProductCode` = '$productCode'";
		$res = $this->db->query($q);
    		
		if($res && $res->num_rows == 1) {
			$typeId = $res->fetch_all(MYSQLI_ASSOC)[0]['prod_ProductTypeID'];

			if($typeId == 9) { // offers
				return true;
			}

			return false;
		}
		else {
			return false;
		}
    }
    
    function insertProductAd($productId, $aAdGroupIds, $aKeywordsIds, $url, $statement) {
    	$qProductAdWords = "INSERT INTO `Product_AdWords`
						(
							`p2a_productChannelID`,
    						`p2a_adGroupID`,
    						`p2a_keywordID`,
    						`p2a_url`,
							`p2a_statement`
						)
					VALUES
						(
							(SELECT `p2c_ID` FROM `base_Product_Channel` WHERE `p2c_ID` = ".$productId."),
							'".$aAdGroupIds."',
							'".$aKeywordsIds."',
							'".$url."',
    						$statement
    					)";
    	$response = $this->db->query($qProductAdWords);
    	return $response;
    }

    private function convertToBoolean($str) {
    	if(strtolower($str) == 'y') {
    		return true;
    	}

    	return false;
    }
} //class
