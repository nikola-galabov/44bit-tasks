<?php 
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/secure/MarketingManager/includes/bigCommerceConnection.php');

class BulkPricingRule {
	private $db;
    private $table;
    private $columns;
    private $bigCommerce;

	function __construct() {
		$this->db = mysqliSingleton::init();
        $this->table = "BulkPricingRule";
        $this->columns = array(
            'min' => 'bpr_minQty',
            'max' => 'bpr_maxQty',
            'discountType' => 'bpr_discountType',
            'discountValue' => 'bpr_discountValue',
            'productId' => 'bpr_ProductID',
            'ruleId' => 'bpr_ruleID',
            'isDeleted' => 'bpr_isDeleted',
            'id' => 'bpr_id',
            'pureCode' => 'bpr_PureCode'
        );

        // TODO channel is hardcoded!
        $this->bigCommerce = new BigCommerceConnection(7);
    }

    function listDiscountRules($pureCode, $allProductsRules = false) {
        // $distinct = "";
        if($allProductsRules) {
            $distinct = "";
        }

        $selectQuery = "SELECT 
                            * 
                        FROM 
                            {$this->table}
                        WHERE
                            {$this->columns['isDeleted']} = 0
                        AND 
                            {$this->columns['pureCode']} = '{$pureCode}'";
        
        $result = $this->db->query($selectQuery);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getDiscountRules($productId) {
        $selectQuery = "SELECT * 
        FROM 
            {$this->table}
        WHERE 
            {$this->columns['productId']} = $productId";

        $result = $this->db->query($selectQuery);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getRuleIdAndProductId($ids) {
        
        $filter = " WHERE ";

        if(is_array($ids)) {
            $filter .= "`br2p_RuleID` IN(" . implode(',', $ids) . ")";
        } else {
            $filter .= "`br2p_RuleID` =" . $id;
        }

        $selectQuery = "SELECT 
                            `br2p_BigCommerceProductID` as `productId`,
                            `br2p_BigCommerceRuleID` as `ruleId`
                        FROM
                            `BulkRules_Products`" . 
                        $filter;

        $result = $this->db->query($selectQuery);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function editBulkPricingRules($ids, $fields) {
        $result = array(
            'errors' => array(),
            'success' => array()
        );

        foreach ($ids as $id) {
            $getProductIdAndRuleId = "SELECT 
                                        `br2p_BigCommerceProductID` as `productId`, `br2p_BigCommerceRuleID` as `ruleId`
                                    FROM
                                        `BulkRules_Products`
                                    WHERE
                                        `br2p_RuleID` = {$id}";

            $rules = $this->db->query($getProductIdAndRuleId);
            $rules = $rules->fetch_all(MYSQLI_ASSOC);

            $ruleData = array(
                'min' => $fields[$id]['bpr_minQty'],
                'max' => $fields[$id]['bpr_maxQty'],
                'type' => $fields[$id]['bpr_discountType'],
                'type_value' => $fields[$id]['bpr_discountValue']
            );

            foreach ($rules as $rule) {
                $updated = $this->bigCommerce->updateBulkPricingRule($rule['productId'], $ruleData, $rule['ruleId']);

                if(! $updated->id) {
                    array_push($result['errors'], $rule['ruleId']);
                    continue;
                }

                array_push($result['success'], $rule['ruleId']);
            }
            
            $updateSuccess = $this->updateBulkPricingRule($id, $fields[$id]);

            if(! $updateSuccess) {
                array_push($result['errors'], $id);
                continue;
            }
        }

        return $result;
    }

    function updateBulkPricingRule($ruleId, $ruleData) {
        $updateQuery = "UPDATE 
                            `BulkPricingRule`
                        SET 
                            {$this->columns['min']} = {$ruleData['bpr_minQty']},
                            {$this->columns['max']} = {$ruleData['bpr_maxQty']},
                            {$this->columns['discountType']} = '{$ruleData['bpr_discountType']}',
                            {$this->columns['discountValue']} = {$ruleData['bpr_discountValue']}
                        WHERE
                            `bpr_id` = $ruleId";

        $result = $this->db->query($updateQuery);

        return $result;
    }

    function createPureCodePricingRule($pureCode, $rule) {
        $insertQuery = "INSERT INTO 
                            {$this->table}
                            (
                                {$this->columns['min']},
                                {$this->columns['max']},
                                {$this->columns['discountType']},
                                {$this->columns['discountValue']},
                                {$this->columns['pureCode']}
                            )
                        VALUES
                            (
                                {$rule['min']},
                                {$rule['max']},
                                '{$rule['type']}',
                                {$rule['type_value']},
                                '{$pureCode}'
                            )";
        
        $insertResult = $this->db->query($insertQuery);
        
        if($insertResult) {
            $pureCodeRuleId = $this->db->insert_id;
        }
        
        // get the products for the pure code
        $products = $this->getProductsFromChannel($pureCode, 7);

        $result = array(
            'errors' => array(),
            'success' => array()
        );

        foreach ($products as $product) {
            $bcRuleId = $this->insertRuleInChannel($product['productId'], $rule);
            
            if($bcRuleId == false) {
                array_push($result['errors'], $product['productCode']);
                continue;
            }

            $insertQuery = "INSERT INTO 
                                `BulkRules_Products`
                                (
                                    `br2p_RuleID`,
                                    `br2p_BigCommerceProductID`,
                                    `br2p_BigCommerceRuleID`
                                )
                            VALUES
                                (
                                    {$pureCodeRuleId},
                                    {$product['productId']},
                                    {$bcRuleId}
                                )";

            $isInserted = $this->db->query($insertQuery);

            if(! $isInserted) {
                array_push($result['errors'], $product['productCode']);
                continue;
            }

            array_push($result['success'], $product['productCode']);
        }

        return $result;
    }

    function deleteBulkPricingRule($ruleIds) {
        $rules = $this->getRuleIdAndProductId($ruleIds);
        $idAsString = implode(',', $ruleIds);

        $result = array(
            'errors' => array(), 
            'success' => array()
        );

        foreach ($rules as $rule) {
            // DELETE FROM BigCommerce
            $isDeleted = $this->bigCommerce->deleteBulkPricingRule($rule['productId'], $rule['ruleId']);

            // DELETE FROM CP
            if($isDeleted) {
                $deleteFromRulesProducts = "DELETE FROM
                                                `BulkRules_Products`
                                            WHERE
                                                `br2p_RuleID` IN({$idAsString})
                                                ";

                $res = $this->db->query($deleteFromRulesProducts);

                // check for errors
                if(!$res) {
                    array_push($result['errors'], $rule['ruleId']);
                    continue;
                }

                $deleteFromRules = "DELETE FROM
                                        `BulkPricingRule`
                                    WHERE
                                        `bpr_ID` IN({$idAsString})";

                $res = $this->db->query($deleteFromRules);
                
                // check for errors
                if(!$res) {
                    array_push($result['errors'], $rule['ruleId']);
                    continue;
                }

                array_push($result['success'], $rule['ruleId']);
            }
            else {
                array_push($result['errors'], $rule['ruleId']);
            }     
        }

        return $result;
    }

    function checkForExistingRule($pureCode, $rule) {
        // check for existing
        $checkQry = "SELECT 
                        *
                    FROM
                        `BulkPricingRule`
                    WHERE 
                        `bpr_PureCode` = '{$pureCode}'";

        $result = $this->db->query($checkQry);
        $existingRules = $result->fetch_all(MYSQLI_ASSOC);

        // check for overlaping qty
        foreach ($existingRules as $existingRule) {

            if($rule['min'] >= $existingRule[$this->columns['min']] && $rule['min'] <= $existingRule[$this->columns['max']]) {
                return true;
            }

            if($rule['max'] >= $existingRule[$this->columns['min']] && $rule['max'] <= $existingRule[$this->columns['max']]) {
                return true;
            }

            if(
                $rule['min'] < $existingRule[$this->columns['min']] && 
                $rule['min'] < $existingRule[$this->columns['max']] &&
                $rule['max'] > $existingRule[$this->columns['min']] && 
                $rule['max'] > $existingRule[$this->columns['max']]    
            ) {
                return true;
            }
        }

        return false;
    }

    function insertRuleInChannel($productId, $rule) {
        $ruleData = json_encode($rule);
        $result = $this->bigCommerce->createBulkPricingRule($productId, $ruleData);

        return $result;
    }

    // move this function in products class
    function getBigCommerceProductIdByProductCode($productCode, $channelId) {
        $getQuery = "SELECT 
                        `p2c_custom24` as `bigCommerceId`
                    FROM 
                        `base_Product_Channel`
                    JOIN 
                        `Product`
                    ON
                        `p2c_ProductId` = `prod_id`
                    WHERE
                        `prod_ProductCode` = '{$productCode}'
                    AND
                        `p2c_ChannelId` = {$channelId}
                    LIMIT 1";

        // var_dump($getQuery); die;
        $result = $this->db->query($getQuery);
        
        return $result->fetch_all(MYSQLI_ASSOC)[0];
    }

    private function getProductsFromChannel($pureCode, $channelId) {
        $selectQry = "SELECT 
                        `p2c_Custom24` as `productId`, `prod_ProductCode` as `productCode`
                    FROM 
                        `base_Product_Channel`
                    JOIN
                        `Product`
                    ON
                        `prod_id` = `p2c_ProductID`
                    WHERE 
                        `p2c_ChannelId` = {$channelId}
                    AND
                        `prod_PureCode` = '{$pureCode}'";

        $res = $this->db->query($selectQry);

        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
