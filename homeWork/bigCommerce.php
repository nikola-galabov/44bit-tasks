<?php

class BigCommerceConnection
{
    private $bigCommerceUser;
    private $bigCommercePassword;
    private $apiUrl;
    private $channelId;
    public $db;

    function __construct($channelId)
    {
        if($channelId == '7') { // RUPS

            $this->channelId = 7;
        } elseif($channelId == '15') { // BSLA

            $this->channelId = 15;
        }

        $this->db = mysqliSingleton::init();
    }

    // PRODUCT
    function listProducts($page) {
        $url = '/products?page=' . $page . '&limit=250';
        $result = $this->getResource($url);
        return $result;
    }

    function getProductBySku($sku) {
        $url = '/products?sku=' . $sku;
        $result = $this->getResource($url);
        return $result;
    }

    function getProductsCount() {
        $url = '/products/count';
        $result = $this->getResource($url);
        return $result;
    }

    function getProductByName($name) {
        $url = '/products.json?name=' . $name;
        $result = $this->getResource($url);
        return $result;
    }

    function getProductById($id) {
        $url = '/products/' . $id;
        $result = $this->getResource($url);
        return $result;
    }

    function updateProduct($productId, $data) {
        $url = '/products/' . $productId;
        $result = $this->updateResource($url, $data);
        return $result;
    }

    function createProduct($data) {
        $url = '/products';
        $result = $this->createResource($url, $data);
        return $result;
    }

    function deleteProduct($productId) {
        $url = '/products/' . $productId;
        $result = $this->deleteResource($url);
        return $result;
    }

    // CATEGORIES
    function getCategoryById($id) {
        $url = '/categories/' . $id;
        $result = $this->getResource($url);
        return $result;
    }

    function getCatgoriesCount() {
        $url = '/categories/count';
        $result = $this->getResource($url);
        return $result->count;
    }

    function createCategory($data) {
        $url = '/categories';
        $result = $this->createResource($url, $data);
        return $result;
    }

    function deleteCategory($categoryId) {
        $url = '/categories/' . $categoryId;
        $result = $this->deleteResource($url);
        return $result;
    }

    // PRODUCT IMAGE
    function getProductImages($productId) {
        $url = '/products/' . $productId . '/images';
        $result = $this->getResource($url);
        return $result;
    }

    function getProductImage($productId, $imageId) {
        $url = '/products/' . $productId . '/images/' . $imageId;
        $result = $this->getResource($url);
        return $result;    
    }

    function updateProductImage($imageId, $productId, $data) {
        $url = '/products/' . $productId . '/images/' . $imageId;
        $result = $this->updateResource($url, $data);
        return $result;
    }

    function createProductImage($productId, $data) {
        $url = '/products/' . $productId . '/images';
        $result = $this->createResource($url, $data);
        return $result;
    } 

    function deleteProductImage($productId, $imageId) {
        $url = '/products/' . $productId . '/images/' . $imageId;
        $result = $this->deleteResource($url);
        return $result;
    }

    // BRAND
    function getBrandsCount() {
        $url = '/brands/count';
        $result = $this->getResource($url);
        return $result;
    }

    function getBrandByName($name) {
        $url = '/brands?name=' . $name;
        $result = $this->getResource($url);
        return $result;
    }

    function listBarnds($page, $limit) {
        $url = '/brands?page=' . $page . '&limit=' . $limit;
        $result = $this->getResource($url);
        return $result;
    }

    // SKU
    function getSkuCount() {
        $url = '/products/skus/count';
        $result = $this->getResource($url);
        return $result;
    }

    function getProductSku($productId) {
        $url = '/products/' . $productId . '/skus';
        $result = $this->getResource($url);
        return $result;
    }

    function createProductSku($productId, $data) {
        $url = '/products/' . $productId . '/skus';
        $result = $this->createResource($url, $data);
        return $result;
    }

    // BULK PRICING RULES
    function listBulkPricingRules() {
        $url = '/products/discount_rules.json';
        $result = $this->getResource($url);
        return $result;
    }

    function getBulkPricingRules($productId) {
        $url = '/products/' . $productId . '/discountrules';
        $result = $this->getResource($url);
        return $result;
    }

    function bulkPricingRulesCount() {
        $url = '/products/discount_rules/count';
        $result = $this->getResource($url);
        return $result;
    }

    function createBulkPricingRule($productId, $data) {
        $url = '/products/' . $productId . '/discount_rules';
        $result = $this->createResource($url, $data);
        return $result;
    }

    function updateBulkPricingRule($productId, $data, $ruleId) {
        $url = '/products/' . $productId . '/discount_rules/' . $ruleId;
        $result = $this->updateResource($url, $data);
        return $result;
    }

    function deleteBulkPricingRule($productId, $ruleId) {
        $url = '/products/' . $productId . '/discount_rules/' . $ruleId;
        $result = $this->deleteResource($url);
        return $result;
    }

    // PRIVATE FUNCTIONS
    private function createResource($url, $data) {
        $productUrl = $this->apiUrl . $url;

        $ch = curl_init(); 
        curl_setopt( $ch, CURLOPT_URL, $productUrl );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Content-type: application/json', 'Accept: application/json') );                                
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec( $ch );
        if(! $response) {
            return false;
        }

        $result = json_decode($response);

        return $result;
    }

    private function getResource($url) {
        $url = $this->apiUrl . $url;

        $ch = curl_init(); 
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'Content-Length: 0') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET'); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );   

        $response = curl_exec( $ch );
        $result = json_decode($response);
        // $result = htmlspecialchars($response);

        return $result;
    }

    private function updateResource($url, $data) {
        $ch = curl_init($this->apiUrl . $url);

        //Data to update (JSON encoded)
        $data = json_encode($data);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json', 'Accept: application/json') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec( $ch );
        
        if( ! curl_errno($ch) ) {
            $result = json_decode($response);
            // $image = $this->getProductImages($result->id);
            // var_dump($image); die;
            return $result->id;
        }
        else {
            return false;
        }
    }

    private function deleteResource($url) {
        $ch = curl_init($this->apiUrl . $url);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'Content-Length: 0') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $response = curl_exec( $ch );
        
        if( ! curl_errno($ch) ) {
            return true;
        }
        else {
            return false;
        }
    }

    // OTHERS
    function displayResponseHeaders() {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $this->apiUrl . '/products/count');
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'Content-Length: 0') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET'); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );   
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec( $ch );
        var_dump($response);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        var_dump($header); die;
    }

    function fillIds($i) {
        $ch = curl_init(); 
        curl_setopt( $ch, CURLOPT_URL, $this->apiUrl . '/products.json?limit=200&page=' . $i );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'Content-Length: 0') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $response = curl_exec( $ch );

        $result = json_decode($response);
        
        $hlp = new helpers;
        $counter = 0;

        $q = "SELECT 
                `p2c_sku`
            FROM
                `base_Product_Channel`
            WHERE 
                `p2c_channelId` = $this->channelId
            GROUP BY
                `p2c_sku`
            HAVING 
                COUNT(*) > 1";

        $rslt = $hlp->qry($q, true);
        $duplicatedSKUs = "";

        foreach ($rslt->fetch_all(MYSQLI_ASSOC) as $sku) {
            $duplicatedSKUs .= "'" . $sku['p2c_sku'] . "',";
        }

        $duplicatedSKUs = rtrim($duplicatedSKUs, ",");

        foreach ($result as $key => $value) {
            echo 'Inserting product id: ' . $result[$key]->id . ' to a product with sku: ' . $result[$key]->sku . '<br>';
            $q = "UPDATE `base_product_channel` SET `p2c_Custom24` = " . $result[$key]->id;
            $q .= " WHERE `p2c_sku` = '" . $result[$key]->sku . "'";
            $q .= " AND `p2c_ChannelId` = " . $this->channelId;
            
            if($duplicatedSKUs) {
                $q .= " AND `p2c_sku` NOT IN(" . $duplicatedSKUs . ")";
            }

            $hlp->qry($q);
        }
    }

    function saveCategories($i) {
        $hlp = new helpers;
        $ch = curl_init(); 
        curl_setopt( $ch, CURLOPT_URL, $this->apiUrl . '/categories.json?limit=200&page=' . $i );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'Content-Length: 0') );                                   
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_USERPWD, $this->bigCommerceUser . ':' . $this->bigCommercePassword ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $response = curl_exec( $ch );

        $result = json_decode($response);

        foreach ($result as $key => $value) {
            $catId = $result[$key]->id; 
            $parentId = $result[$key]->parent_id;
            $name = $result[$key]->name;

            $q = "INSERT INTO `base_Categories` (`bscat_CatId`, `bscat_ParentCatId`, `bscat_CatName`, `bscat_ChannelId`) VALUES($catId, $parentId, '$name', $this->channelId)";
            $res = $hlp->qry($q);
            
            if($res) {
                echo 'Category with id: ' . $catId . ', parentId: ' . $parentId . ', and name: ' . $name . 'successfully inserted for channel: ' . $this->channelId . '<br>';
            }
        }

    }

    function updateProductInCp($productId, $allowPurchases, $availability_description, $isOutOfStock) {
        $hlp = new helpers;

        $q = 'UPDATE `base_Product_channel`';
        $q.= ' SET `p2c_Custom6` = "' . $allowPurchases; // allowPurchases
        $q.= '", `p2c_Custom8` = "' . $availability_description; // availability_description
        $q.= '", `p2c_isOutOfStock` = "' . $isOutOfStock;
        $q.= '" WHERE `p2c_channelid` = ' . $this->channelId . ' AND `p2c_Custom24` = "' . $productId . '"'; // product Id (from big commerce)

        // var_dump($q); die;
        $this->db->query($q);
    }
}