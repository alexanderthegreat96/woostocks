<?php

namespace LexSystems;
class CronWrapper extends PrestashopHelpers
{

    /**
     * @param int $productId
     * @param string $productName
     * @param string $productCode
     * @param string $variations
     * @param string $stock
     * @return array|bool[]
     */

    private function importQueueProduct(int $productId, string $productName, string $productCode, string $variations,
                                        string $stock)
    {
        $con = $this->connect(Config::IMPORTER_DB);

        $logger = new SystemLogger();

        if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM prestashop_products WHERE productCode = '" . addslashes($productCode) . "'")) < 1) {
            $run = mysqli_query($con, "INSERT into prestashop_products VALUES (DEFAULT,'$productId','" . addslashes($productName) . "','" . addslashes($productCode) . "','$variations','$stock','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')");
            if ($run) {
                $logger->logDb('PRESTASHOP-DATA-IMPORT', 'Imported [' . $productCode . '] into table prestashop_products.');
                return ['status' => true];
            } else {
                $logger->logDb('PRESTASHOP-DATA-IMPORT', 'Error Importing [' . $productCode . '] into table prestashop_products.' . mysqli_error($con));
                return ['status' => false, 'error' => mysqli_error($con)];
            }
        }
    }


    /**
     * @param int $variationId
     * @param string $productCode
     * @param string $stock
     * @param string $physicalStock
     * @return array|bool[]
     */
    private function importQueueProductVariation(int $variationId, int $productId, string $productCode, string $stock, string $physicalStock)
    {
        if (!empty($productCode) && $productCode != "") {
            $con = $this->connect(Config::IMPORTER_DB);
            $logger = new SystemLogger();
            if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM prestashop_product_variations WHERE productCode = '" . addslashes($productCode) . "'")) < 1) {
                $run = mysqli_query($con, "INSERT into prestashop_product_variations VALUES (DEFAULT,'$variationId','$productId','" . addslashes($productCode) . "','$stock','$physicalStock','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')");
                if ($run) {
                    $logger->logDb('PRESTASHOP-DATA-IMPORT', 'Imported [' . $productCode . '] into table prestashop_product_variations.');
                    return ['status' => true];
                } else {
                    $logger->logDb('PRESTASHOP-DATA-IMPORT', 'Error importing [' . $productCode . '] into table prestashop_product_variations.' . mysqli_error($con));
                    return ['status' => false, 'error' => mysqli_error($con)];
                }
            }
        }
    }

    /**
     * @param string $productCode
     * @param string $stock
     * @return array|bool[]
     */

    private function updateQeueProduct(string $productCode, string $stock)
    {

        $con = $this->connect(Config::IMPORTER_DB);
        $logger = new SystemLogger();

        if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM prestashop_products WHERE productCode = '" . addslashes($productCode) . "' LIMIT 1")) == 1) {
            $run = mysqli_query($con, "UPDATE prestashop_products SET stock = '$stock',updatedAt='" . date("Y-m-d H:i:s") . "' WHERE productCode = '" . addslashes($productCode) . "'");
            if ($run) {
                $logger->logDb('FGO DATA-DUMP', 'Updated [' . $productCode . '] FROM prestashop_products AND set stock to [' . $stock . ']');
                return ['status' => true];
            } else {
                $logger->logDb('FGO DATA-DUMP', 'Unable to update [' . $productCode . '] FROM prestashop_products AND set stock to [' . $stock . '] ' . mysqli_error($con));
                return ['status' => false, 'error' => mysqli_error($con)];
            }
        }
    }

    /**
     * @param string $productCode
     * @param string $stock
     * @param string $physicalStock
     * @return array|bool[]
     */

    private function updateQeueProductVariation(string $productCode, string $stock, string $physicalStock)
    {
        $con = $this->connect(Config::IMPORTER_DB);
        $logger = new SystemLogger();

        if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM prestashop_product_variations WHERE productCode = '" . addslashes($productCode) . "' LIMIT 1")) == 1) {
            $run = mysqli_query($con, "UPDATE prestashop_product_variations SET stock = '$stock',physicalStock = '$physicalStock',updatedAt='" . date("Y-m-d H:i:s") . "' WHERE productCode = '" . addslashes($productCode) . "'");
            if ($run) {
                $logger->logDb('FGO DATA-DUMP', 'Updated [' . $productCode . '] FROM prestashop_product_variations AND set stock to [' . $stock . '] AND set physicalStock to [' . $physicalStock . ']');
                return ['status' => true];
            } else {
                $logger->logDb('FGO DATA-DUMP', 'Unable to update [' . $productCode . '] FROM prestashop_product_variations AND set stock to [' . $stock . '] AND set physicalStock to [' . $physicalStock . '] ' . mysqli_error($con));
                return ['status' => false, 'error' => mysqli_error($con)];
            }
        }
    }

    /**
     * @param string $limit
     * @param string $offset
     * @return array
     */
    private function queryPrestashopProducts(string $limit = "30", string $page = '1')
    {
        $con = $this->connect(Config::IMPORTER_DB);

        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
        $totalCountSQl = mysqli_query($con, "SELECT count(id) AS count FROM prestashop_products");
        $cc = mysqli_fetch_assoc($totalCountSQl);
        $totalCount = $cc['count'];
        $pages = ($totalCount % $limit == 0) ? ($totalCount / $limit) : (round($totalCount / $limit, 0) + 1);

        $get = mysqli_query($con, "SELECT id,productCode FROM prestashop_products LIMIT $offset,$limit");
        if (mysqli_num_rows($get)) {
            while ($a = mysqli_fetch_assoc($get)) {
                $data[] = $a;
            }
            return
                [
                    'status' => true,
                    'total' => $totalCount,
                    'currentPage' => $page,
                    'nextPage' => $page + 1,
                    'from' => $offset + 1,
                    'to' => ($offset + $limit),
                    'pages' => $pages,
                    'data' => $data
                ];
        } else {
            return ['status' => false, 'error' => 'No records found!'];
        }
    }

    /**
     * @param string $limit
     * @param string $page
     * @return array
     */

    private function queryPrestashopProductVariations(string $limit = "30", string $page = '1')
    {
        $con = $this->connect(Config::IMPORTER_DB);

        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
        $totalCountSQl = mysqli_query($con, "SELECT count(id) AS count FROM prestashop_product_variations");
        $cc = mysqli_fetch_assoc($totalCountSQl);
        $totalCount = $cc['count'];
        $pages = ($totalCount % $limit == 0) ? ($totalCount / $limit) : (round($totalCount / $limit, 0) + 1);

        $get = mysqli_query($con, "SELECT id,productCode FROM prestashop_product_variations LIMIT $offset,$limit");
        if (mysqli_num_rows($get)) {
            while ($a = mysqli_fetch_assoc($get)) {
                $data[] = $a;
            }
            return
                [
                    'status' => true,
                    'total' => $totalCount,
                    'currentPage' => $page,
                    'nextPage' => $page + 1,
                    'from' => $offset + 1,
                    'to' => ($offset + $limit),
                    'pages' => $pages,
                    'data' => $data
                ];
        } else {
            return ['status' => false, 'error' => 'No records found!'];
        }
    }

    /**
     * @param string $page
     * @return array|bool[]
     */

    public function writeProductsPageNumber(string $page)
    {
        $logger = new SystemLogger();
        $fullPath = dirname(__FILE__)."/../paginations/prestashop_products.txt";
        if(file_exists($fullPath))
        {
            $pageFile = fopen($fullPath, "w+");
            if ($pageFile) {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Written next page with value [' . $page . '] in paginations/prestashop_products.txt');
                fwrite($pageFile, $page);
                fclose($pageFile);
                return ['status' => true];
            } else {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in paginations/prestashop_products.txt. Permission issues.');
                return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
            }
        }
        else
        {
            $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in '.$fullPath.'. File not found.');
            return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
        }


    }

    /**
     * @param string $page
     * @return array|bool[]
     */

    private function writeVariationsPageNumber(string $page)
    {
        $logger = new SystemLogger();
        $fullPath = dirname(__FILE__)."/../paginations/prestashop_product_variations.txt";
        if(file_exists($fullPath))
        {
            $pageFile = fopen($fullPath, "w+");

            if ($pageFile) {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Written next page with value [' . $page . '] in paginations/prestashop_product_variations.txt');
                fwrite($pageFile, $page);
                fclose($pageFile);
                return ['status' => true];
            } else {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in paginations/prestashop_product_variations.txt. Permission issues.');
                return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
            }
        }
        else
        {
            $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in '.$fullPath.'. File not found.');
            return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
        }


    }

    /**
     * Imports data front prestashop database
     * to our secondary database
     */
    public function importQueueData()
    {
        $products = $this->fetchProductData();

        if ($products) {
            foreach ($products as $p) {
                $this->importQueueProduct($p['productId'], $p['productName'], $p['code'], $p['variation_count'], $p['stock']);
                if ($p['variations']) {
                    foreach ($p['variations'] as $v) {
                        $this->importQueueProductVariation($v['variationId'], $p['productId'], $v['productCode'], $v['stocks'][0]['stock'], $v['stocks'][0]['physicalStock']);
                    }
                }
            }
        }

    }

    /**
     * Return last page
     */
    private function returnLastProductsPage()
    {
        $fullPath = dirname(__FILE__)."/../paginations/prestashop_products.txt";
        if(file_exists($fullPath)) {
            $data = file_get_contents($fullPath);
            if(!empty($data))
            {
                return $data;
            }
            elseif($data !="")
            {
                return $data;
            }
            else
            {
                return "1";
            }
        }
        else
        {
            return "1";
        }

    }

    /**
     * @return false|string
     */
    private function returnLastVariationsPage()
    {
        $fullPath = dirname(__FILE__)."/../paginations/prestashop_product_variations.txt";
        if(file_exists($fullPath)) {
            $data = file_get_contents($fullPath);
            if(!empty($data))
            {
                return $data;
            }
            elseif($data !="")
            {
                return $data;
            }
            else
            {
                return "1";
            }
        }
        else
        {
            return "1";
        }

    }
    /**
     * Update Queue LIST
     * THIS LIST WILL BE IMPORTED
     * IN PRESTASHOP
     */
    public function updateProductQueueData()
    {

        $fgo = new FgoWrapper();
        $logger = new SystemLogger();
        $helpers = new PrestashopHelpers();

        echo "Started productsQueueData \n";


            $page = $this->returnLastProductsPage();
            echo "Starting from page: ".$page."\n";

            $logger->logDb('FGO DATA DUMP PAGINATE', 'Products: Read last cursor with value [' . $page . ']');
            $query = $this->queryPrestashopProducts(30, $page);

            if ($query['status']) {
                /**
                 * Write the next page
                 */

                $this->writeProductsPageNumber($query['nextPage']);

                foreach ($query['data'] as $d) {
                    $codes[] = str_replace(" ","",$d['productCode']);
                }

                echo "Adding: ".implode(",",$codes)." to queue \n";

                $logger->logDb("FGO DATA DUMP QUEUE","Adding: ".implode(",",$codes)." to queue");

                sleep(2);

                $apiData = $fgo->getArticles($codes);

                if ($apiData['status']) {
                    foreach ($apiData['data'] as $d) {
                        $this->updateQeueProduct($d['CodConta'], $d['Stoc']);
                        $getProductId = $helpers->getProductId($d['CodConta']);

                        if($getProductId)
                        {
                            $helpers->updatePrestashopProductStock($getProductId,$d['CodConta'],$d['Stoc']);
                        }

                        echo "Processed:".$d['CodConta']."\n";
                    }
                    /**
                     * Continue Running this
                     */

                    echo "Sleeping for 7 seconds \n";
                    sleep(7);
                    $this->updateProductQueueData();

                } else {
                    $logger->logDb('FGO DATA DUMP SAVE QUEUE', 'Error: ' . $apiData['error']);
                }

            } else {
                $this->writeProductsPageNumber('1');
                exit();
            }


    }

    /**
     * Reset prestashop_products table
     */
    public function resetProductsQueueData()
    {
        $logger = new SystemLogger();
        $con = $this->connect(Config::IMPORTER_DB);
        $run = mysqli_query($con, "TRUNCATE TABLE prestashop_products;");
        if($run)
        {
            $logger->logDb('PRODUCTS DB RESET','Emptied table prestashop_products');
        }
        else
        {
            $logger->logDb('PRODUCTS DB RESET','Unable to empty table prestashop_products '. mysqli_error($con));
        }
    }

    /**
     * Reset table prestashop_product_variations
     */
    public function resetProductVariationsQueueData()
    {
        $logger = new SystemLogger();
        $con = $this->connect(Config::IMPORTER_DB);
        $run = mysqli_query($con, "TRUNCATE TABLE prestashop_product_variations;");
        if($run)
        {
            $logger->logDb('PRODUCT VARIATIONS DB RESET','Emptied table prestashop_product_variations');
        }
        else
        {
            $logger->logDb('PRODUCTS VARIATIONS DB RESET','Unable to emtpy table prestashop_product_variations '. mysqli_error($con));
        }
    }

    /**
     * UPDATES QUEUE DATA FOR VARIATIONS
     */
    public function updateVariationsQeueData()
    {
        //limit execution to 30 seconds
        echo "Started variationsQueueData \r";

        $fgo = new FgoWrapper();
        $logger = new SystemLogger();
        $helpers = new PrestashopHelpers();

            $page = $this->returnLastVariationsPage();
            echo "Starting from page: ".$page."\n";

            $logger->logDb('FGO DATA DUMP PAGINATE', 'ProductVariations: Read last cursor with value [' . $page . ']');

            $query = $this->queryPrestashopProductVariations(30, $page);
            if ($query['status']) {
                /**
                 * Write the next page
                 */
                $this->writeVariationsPageNumber($query['nextPage']);

                foreach ($query['data'] as $d) {

                    $codes[] = str_replace(" ","",$d['productCode']);
                }
                echo "Adding: ".implode(",",$codes)." to queue \n";

                $logger->logDb("FGO DATA DUMP QUEUE","Adding: ".implode(",",$codes)." to queue");

                sleep(2);

                $apiData = $fgo->getArticles($codes);
                if ($apiData['status']) {
                    foreach ($apiData['data'] as $d) {
                        $this->updateQeueProductVariation($d['CodConta'], $d['Stoc'], $d['Stoc']);
                        $variation = $helpers->getVariationId($d['CodConta']);

                            if($variation)
                            {
                                $helpers->updatePrestashopProductVariationStock($variation['variationId'],$variation['productId'],$d['CodConta'],$d['Stoc'],$d['Stoc']);
                            }

                        echo "Processed:".$d['CodConta']."\n";
                    }

                    /**
                     * Continue Running this
                     */
                    echo "Sleeping for 7 seconds \n";
                    sleep(7);
                    $this->updateVariationsQeueData();

                } else {
                    $logger->logDb('FGO DATA DUMP SAVE QUEUE', 'Error: ' . $apiData['error']);
                }
            } else {
                $this->writeVariationsPageNumber('1');
            }

    }

    /**
     * Send Information to Prestashop Database
     */
    public function sendProductData()
    {
        $helpers = new PrestashopHelpers();
        $con = $this->connect(Config::IMPORTER_DB);
        $get = mysqli_query($con,"SELECT productId,productCode,stock FROM prestashop_products");
        if(mysqli_num_rows($get))
        {
            while($a = mysqli_fetch_assoc($get))
            {
                $d = $helpers->updatePrestashopProductStock($a['productId'],$a['productCode'],$a['stock']);
            }
        }
    }

    /**
     * Sends variation informationn to Prestashop Database
     */
    public function sendVariationsData()
    {
        $helpers = new PrestashopHelpers();
        $con = $this->connect(Config::IMPORTER_DB);
        $get = mysqli_query($con,"SELECT variationId,productId,productCode,stock,physicalStock FROM prestashop_product_variations");
        if(mysqli_num_rows($get))
        {
            while($a = mysqli_fetch_assoc($get))
            {
                $helpers->updatePrestashopProductVariationStock($a['variationId'],$a['productId'],$a['productCode'],$a['stock'],$a['physicalStock']);
            }
        }
    }

    /**
     * @return array|bool[]
     */
    public function emptyLogs()
    {
        $logger = new SystemLogger();
        $con = $this->connect(Config::IMPORTER_DB);
        $run = mysqli_query($con, "TRUNCATE table global_logs");
        if($run)
        {
            $logger->logDb('TRUNACTE LOGS','Emptied log entries');
            return ['status'=>true];
        }
        else
        {
            $logger->logDb('TRUNACTE LOGS','Unable to empty log entries '.mysqli_error($con));
            return ['status' => false,'error' => mysqli_error($con)];
        }
    }


}