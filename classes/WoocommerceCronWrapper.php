<?php
namespace LexSystems;

use LexSystems\Framework\Middleware\Debugger;

class WoocommerceCronWrapper
{
    /**
     * Return last page
     */
    public function returnLastProductsPage()
    {
        $fullPath = dirname(__FILE__)."/../paginations/wc_products.txt";
        if(file_exists($fullPath)) {
            $data = file_get_contents($fullPath);
            if(!empty($data))
            {
                return (int)$data;
            }
            elseif($data !="")
            {
                return (int)$data;
            }
            else
            {
                return 1;
            }
        }
        else
        {
            return 1;
        }

    }

    public function writeProductsPageNumber(int $page)
    {
        $logger = new SystemLogger();
        $fullPath = dirname(__FILE__)."/../paginations/wc_products.txt";
        if(file_exists($fullPath))
        {
            $pageFile = fopen($fullPath, "w+");
            if ($pageFile) {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Written next page with value [' . $page . '] in paginations/wc_products.txt');
                fwrite($pageFile, $page);
                fclose($pageFile);
                return ['status' => true];
            } else {
                $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in paginations/wc_products.txt. Permission issues.');
                return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
            }
        }
        else
        {
            $logger->logDb('FGO DATA DUMP PAGINATE', 'Unable to write next page with value [' . $page . '] in '.$fullPath.'. File not found.');
            return ['status' => false, 'error' => 'Permission issues. Coult not save the last page integer.'];
        }
    }

    public function init()
    {
        $logger = new SystemLogger();
        $wc = new WoocommerceWrapper();
        $fgo = new FgoWrapper();

        $getPage = $this->returnLastProductsPage();
        /*
         * Get 30 products
         * and assemble array
         */

        $wc_products = $wc->getProducts($getPage);

        if($wc_products['status'])
        {
            if($wc_products['total_pages'] > $wc_products['current_page']+1)
            {
                $this->writeProductsPageNumber($wc_products['current_page'] + 1);
            }
            else
            {
                $this->writeProductsPageNumber(1);
            }

            $results = $wc_products['results'];
            foreach($results as $p)
            {
                $skus[] = $p->sku;
            }
            if($skus)
            {
                $skus = array_filter($skus);
                sleep(6);
                $fgoReturns = $fgo->getArticles($skus);
                if($fgoReturns['status'])
                {
                    foreach($fgoReturns['data'] as $fg)
                    {
                        $cod_conta = $fg['CodConta'];
                        $stoc = $fg['Stoc'];
                        $mergeBack = $wc->updateStockWithinArray($stoc,$cod_conta,$results);
                    }

                    if($mergeBack)
                    {
                        foreach($mergeBack as $mm)
                        {
                            $id = $mm->id;
                            $wsku = $mm->sku;
                            $name = $mm->name;
                         
                            if($mm->stock_quantity)
                            {
                                $stock = $mm->stock_quantity;
                            }
                            else
                            {
                                $stock = '0';
                            }

                            $updateWc = $wc->updateWcProduct($id,$stock);
                            if($updateWc['status'])
                            {
                                $logger->logDb('FGO-WC-UPDATE','Updated '.$name.' with sku:'.$wsku.' and set stock value to: '.$stock);
                            }
                            else
                            {
                                $logger->logDb('FGO-WC-UPDATE','Could not updaate:' .$name.' with sku:'.$wsku.' Error:'.$updateWc['error']);
                            }
                        }
                    }
                }
                else
                {
                    $logger->logDb('FGO-DATA-API', $fgoReturns['error']);
                }
            }
            else
            {
                $logger->logDb('WC-PRODUCT-QUEUE','Page:' .$getPage.', did not contain skus.');
            }


        }
        else
        {
            $logger->logDb('WC_PRODUCT_QUERY', 'Unable to execute request: '.$wc_products['error']);
        }


    }
}
