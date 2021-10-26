<?php
namespace LexSystems;

use LexSystems\Framework\Middleware\Debugger;

class WoocommerceCronWrapperUpdate
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

    /**
     * @param int $page
     * @return array|bool[]
     */

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

    /**
     * Initiate cron job
     */
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

        /**
         * Simple products
         */
        $wc_simple_products = $wc->getProducts($getPage,'simple');

        /**
         * Variable products
         */

        $wc_variable_products = $wc->getProducts($getPage,'variable');


        if($wc_simple_products['status'] || $wc_variable_products['status'])
        {
            /**
             * If either of these statuses return a positive valude, get the page number
             * and write it down
             */
            if($wc_simple_products['total_pages'] > $wc_simple_products['current_page']+1)
            {
                $this->writeProductsPageNumber($wc_simple_products['current_page'] + 1);
            }
            else
            {
                $this->writeProductsPageNumber(1);
            }


            $simpleProducts = [];
            $variantProducts = [];

            if(isset($wc_simple_products['results']) && $wc_simple_products['results'])
            {
                $simple_product_results = $wc_simple_products['results'];
                foreach($simple_product_results as $s)
                {
                    array_push($simpleProducts,$s);
                }
            }

            if(isset($wc_variable_products['results']) && $wc_variable_products['results'])
            {
                $variable_product_results = $wc_variable_products['results'];
                foreach($variable_product_results as $d)
                {
                    $get_variants = $wc->getProductVariations($d->id);
                    if($get_variants['status'])
                    {
                        foreach($get_variants['results'] as $v)
                        {
                            // array_push($v,['parent_id' => $r->id]);
                            $v->parent_id = $d->id;
                            $v->name = $d->name;
                            array_push($variantProducts,$v);
                        }
                    }
                }
            }


            $simpleProducts = $wc->cleanNonSkuArray($simpleProducts);
            $variantProducts = $wc->cleanNonSkuArray($variantProducts);



            /**
             * Process simple skus
             */
            $simpleSkus = [];


            if($simpleProducts)
            {

                foreach($simpleProducts as $s)
                {
                   if($s->sku && !is_null($s->sku) && !empty($s->sku))
                   {
                       array_push($simpleSkus,$s->sku);
                   }
                }
                if($simpleSkus)
                {
                    $simpleSkus = array_filter($simpleSkus);
                    sleep(6);
                    $fgoReturns = $fgo->getArticles($simpleSkus);
                    if($fgoReturns['status'])
                    {
                        foreach($fgoReturns['data'] as $fg)
                        {
                            $cod_conta = $fg['CodConta'];
                            $stoc = $fg['Stoc'];
                            $mergeBackSimpleProducts = $wc->updateStockWithinArray($stoc,$cod_conta,$simpleProducts);
                        }

                        if($mergeBackSimpleProducts)
                        {
                            foreach($mergeBackSimpleProducts as $mm)
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
            }

            /**
             * Process Variations
             */

            $variantSkus = [];


            if($variantProducts)
            {

                foreach($variantProducts as $v)
                {
                    if($v->sku && !is_null($v->sku) && !empty($v->sku))
                    {
                        array_push($variantSkus,$v->sku);
                    }
                }

                if($variantSkus)
                {

                    $variantSkus = array_filter($variantSkus);
                    sleep(4);
                    $fgoReturns = $fgo->getArticles($variantSkus);
                    if($fgoReturns['status'])
                    {

                        foreach($fgoReturns['data'] as $fg)
                        {
                            $cod_conta = $fg['CodConta'];
                            $stoc = $fg['Stoc'];
                            $mergeBackVariantProducts = $wc->updateStockWithinArray($stoc,$cod_conta,$variantProducts);
                        }

                        if($mergeBackVariantProducts)
                        {

                            foreach($mergeBackVariantProducts as $mm)
                            {
                                $id = $mm->id;
                                $wsku = $mm->sku;
                                $name = $mm->name;
                                $parent_id = $mm->parent_id;

                                if($mm->stock_quantity)
                                {
                                    $stock = $mm->stock_quantity;
                                }
                                else
                                {
                                    $stock = '0';
                                }

                                $updateWc = $wc->updateWcProductVariation($parent_id,$id,$stock);
                                if($updateWc['status'])
                                {
                                    $logger->logDb('FGO-WC-UPDATE-VARIATION','Updated '.$name.' with sku:'.$wsku.' and set stock value to: '.$stock);
                                }
                                else
                                {
                                    $logger->logDb('FGO-WC-UPDATE-VARIATION','Could not update:' .$name.' with sku:'.$wsku.' Error:'.$updateWc['error']);
                                }
                            }
                        }
                    }
                    else
                    {
                        $logger->logDb('FGO-DATA-API', $fgoReturns['error']);
                    }
                }

            }

        }
        else
        {
            $logger->logDb('WC_PRODUCT_QUERY', 'Unable to execute request: '.$wc_simple_products['error'].''.$wc_variable_products['error']);
        }


    }
}
