<?php
/**
 * Prestashop Helpers
 * Functionality to access PrestashopDB objects
 * (c) 2021 Alexandru Lupaescu
 */
namespace LexSystems;
class PrestashopHelpers extends Database
{
    /**
     * @param array $array
     * @param int $limit
     * @return array
     */

    public static function chunkMyArray(array $array, int $limit = 30)
    {
        if(count($array) <= $limit)
        {
            return $array;
        }
        else
        {
            return array_chunk($array, $limit);
        }
    }
    /**
     * @return array
     */

    private function getProducts()
    {
        $con = $this->connect();
        $query =
            "
                SELECT
                ps_product.id_product as product_id,
                ps_product.reference as product_code,
                ps_product.quantity as mainStock
                FROM
                ps_product
                WHERE ps_product.reference  != ''
                ORDER BY ps_product.id_product ASC
            ";
        $run = mysqli_query($con, $query);
        if($run && mysqli_num_rows($run))
        {
            while($a = mysqli_fetch_assoc($run))
            {
                $data[] = $a;
            }

            return ['status' => true,'data' => $data];
        }
        else
        {
            return ['status' => false,'error' => 'No records found!'];
        }
    }

    public function getProductName(int $productId)
    {
        $con = $this->connect();
        $run = mysqli_query($con,"SELECT `name` FROM ps_product_lang WHERE id_product = '$productId' LIMIT 1");
        if(mysqli_num_rows($run))
        {
            $a = mysqli_fetch_assoc($run);
            return $a['name'];
        }
        else
        {
            return 'Produs Neidenfiticat!';
        }
    }

    /**
     * @param int $productId
     * @return array
     */

    private function getVariations(int $productId)
    {
        $con = $this->connect();
        $query =
            "
                SELECT 
                id_product_attribute as variationId,
                reference as productCode
                FROM
                ps_product_attribute
                WHERE id_product = '".$productId."'
                ORDER BY reference ASC
            ";
        $run = mysqli_query($con, $query);
        if($run && mysqli_num_rows($run))
        {
            while($a = mysqli_fetch_assoc($run))
            {
                    $stock = $this->getStockAvailable($productId,$a['variationId']);
                    if($stock['status'])
                    {
                        $stock = $stock['data'];
                    }
                    else
                    {
                        $stock = null;
                    }

                    $data[] =
                        [
                            'variationId' => $a['variationId'],
                            'productCode' => $a['productCode'],
                            'stocks' =>  $stock
                        ];
            }

            return ['status' => true,'data' => $data];
        }
        else
        {
            return ['status' => false,'error' => 'No records found!'];
        }
    }

    private function getStockAvailable(int $productId, int $variationId)
    {
        $con = $this->connect();
        $query =
            "
                SELECT 
                quantity as stock,
                physical_quantity as physicalStock
                FROM
                ps_stock_available
                WHERE id_product = '".$productId."' AND id_product_attribute = '$variationId'
            ";
        $run = mysqli_query($con, $query);
        if($run && mysqli_num_rows($run))
        {
            while($a = mysqli_fetch_assoc($run))
            {
                $data[] = $a;
            }

            return ['status' => true,'data' => $data];
        }
        else
        {
            return ['status' => false,'error' => 'No records found!'];
        }
    }

    /**
     * Fethces Global DATA
     */
    public function fetchProductData()
    {
        $products = $this->getProducts();
        if($products['status'])
        {
            foreach($products['data'] as $p)
            {
                $variations = $this->getVariations($p['product_id']);

                if($variations['status'])
                {
                   $vData = $variations['data'];
                }
                else
                {
                    $vData = null;
                }
                if(!is_null($vData))
                {
                    $count = count($vData);
                }
                else
                {
                    $count = 0;
                }
                $data[] =
                    [
                        "productId" => $p['product_id'],
                        "productName" => $this->getProductName($p['product_id']),
                        "code" => $p['product_code'],
                        'stock' => $p['mainStock'],
                        'variation_count' => $count,
                        'variations' => $vData
                    ];
            }

            return $data;
        }
        else
        {
            echo $products['error'];
        }
    }

    /**
     * @param string $productCode
     * @return false|mixed|string
     */
    public function getProductId(string $productCode)
    {
        $con = $this->connect(Config::IMPORTER_DB);
        $get = mysqli_query($con, 'SELECT productId FROM prestashop_products WHERE productCode = "'.$productCode.'" LIMIT 1');
        if(mysqli_num_rows($get))
        {
            $a = mysqli_fetch_assoc($get);
            return $a['productId'];
        }
        else
        {
            return false;
        }
    }

    /**
     * @param string $productCode
     * @return array|false
     */

    public function getVariationId(string $productCode)
    {
        $con = $this->connect(Config::IMPORTER_DB);
        $get = mysqli_query($con, 'SELECT productId,variationId FROM prestashop_product_variations WHERE productCode = "'.$productCode.'" LIMIT 1');
        if(mysqli_num_rows($get))
        {
            $a = mysqli_fetch_assoc($get);
            return [
                'productId' => $a['productId'],
                'variationId' => $a['variationId']
                ];
        }
        else
        {
            return false;
        }
    }

    /**
     * @param int $productId
     * @param string $productCode
     * @param string $stock
     * @return array|bool[]
     */
    public function updatePrestashopProductStock(int $productId, string $productCode, string $stock)
    {
        $con = $this->connect();

        $logger = new SystemLogger();

        if($stock < "0")
        {
            $stock = "0";
        }
        else
        {
            $stock = $stock;
        }

        $query =
            "
                UPDATE 
                ps_product
                SET
                quantity = '$stock'
                WHERE
                id_product = '$productId';
            ";
        if($stock == "0")
        {
            $query =
                "
               UPDATE
               ps_stock_available
               SET 
                quantity = '0',
                physical_quantity = '0',
                out_of_stock = '1'
                WHERE id_product = '$productId';
            ";
        }
        else
        {
            $query =
                "
               UPDATE
               ps_stock_available
               SET 
                quantity = '$stock',
                physical_quantity = '$stock'
                WHERE id_product = '$productId';
            ";
        }

        $update = mysqli_multi_query($con, $query);
        if($update)
        {

            $logger->logDb('PRESTASHOP-DATABASE-UPDATE','Updated stock for ['.$productCode.'] with ID: ['.$productId.'] in table ps_product AND ps_stock_available.');
            return ['status' => true];
        }
        else
        {
            $logger->logDb('PRESTASHOP-DATABASE-UPDATE','Unable to update stock for ['.$productCode.'] with ID: ['.$productId.'] in table ps_product AND ps_stock_available.'.mysqli_error($con));
            return ['status' => false,'error' => mysqli_error($con)];
        }
    }

    /**
     * @param int $variationId
     * @param int $productId
     * @param string $productCode
     * @param string $stock
     * @param string $physicalStock
     * @return array|bool[]
     */
    public function updatePrestashopProductVariationStock(int $variationId, int $productId, string $productCode, string $stock, string $physicalStock)
    {
        $con = $this->connect();
        $logger = new SystemLogger();

        if($stock < "0")
        {
            $stock = "0";
        }
        else
        {
            $stock = $stock;
        }

        if($stock == "0")
        {
            $query =
                "
                UPDATE
                ps_stock_available
                SET
                quantity = '0',
                physical_quantity = '0',
                out_of_stock = '1'
                WHERE
                id_product = '$productId' AND id_product_attribute = '$variationId';
            ";
        }
        else
        {
            $query =
                "
                UPDATE
                ps_stock_available
                SET
                quantity = '$stock',
                physical_quantity = '$physicalStock'
                WHERE
                id_product = '$productId' AND id_product_attribute = '$variationId';
            ";
        }

        $query .=
            "
            UPDATE 
            ps_product_attribute 
            SET quantity = '$stock' 
            refference = '".addslashes($productCode)."';";

        $update = mysqli_multi_query($con, $query);
        if($update)
        {
            $logger->logDb('PRESTASHOP-DATABASE-UPDATE','Updated stock for ['.$productCode.'] with id_product_attribute ['.$variationId.'] AND id_product ['.$productId.']  in ps_stock_available AND ps_product_attribute.');
            return ['status' => true];
        }
        else
        {
            $logger->logDb('PRESTASHOP-DATABASE-UPDATE','Unable to update stock for ['.$productCode.'] with id_product_attribute ['.$variationId.'] AND id_product ['.$productId.']  in ps_stock_available AND ps_product_attribute '.mysqli_error($con));
            return ['status' => 'false','error' => mysqli_error($con)];
        }
    }


}