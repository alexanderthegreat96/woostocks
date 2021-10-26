<?php
namespace LexSystems;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use LexSystems\Config;
use LexSystems\Framework\Middleware\Debugger;

class WoocommerceWrapper
{
    /**
     * WoocommerceWrapper constructor.
     */

    public function __construct()
    {
        $this->wc = new Client
        (
            Config::WC_URL,
            Config::WC_CK,
            Config::WC_CS,
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );
    }

    /**
     * @param int $page
     * @param string $keyword
     * @param array $productIds
     * @param string $sku
     * @param int $perPage
     * @return array
     */


    public function getProducts(int $page = 1,string $type = 'simple', string $keyword = '', array $productIds = [] ,string $sku = '', int $perPage = 30)
    {
        try{
            $results = $this->wc->get('products',['page' => $page,'per_page' => $perPage,'type'=> $type,'search' => $keyword,'sku' => $sku ,'include' => $productIds]);

            if(isset($this->wc->http->getResponse()->getHeaders()['x-wp-totalpages']))
            {
                $total_pages = $this->wc->http->getResponse()->getHeaders()['x-wp-totalpages'];
            }
            else
            {
                $total_pages = null;
            }

            return ['status' => true,'results' => $results,'total_pages' => $total_pages,'current_page' => $page];
        }
        catch(HttpClientException $e)
        {
            return ['status' => false,'error' => $e->getMessage()];
        }

    }

    /**
     * @param int $wc_id
     * @param int $perPage
     * @return array
     */

    public function getProductVariations(int $wc_id,int $perPage = 30)
    {
        try{
            $results = $this->wc->get('products/'.$wc_id.'/variations',['page' => 1, 'per_page' => $perPage]);

            if(isset($this->wc->http->getResponse()->getHeaders()['x-wp-totalpages']))
            {
                $total_pages = $this->wc->http->getResponse()->getHeaders()['x-wp-totalpages'];
            }
            else
            {
                $total_pages = null;
            }

            return ['status' => true,'results' => $results,'total_pages' => $total_pages,'current_page' => 1];
        }
        catch(HttpClientException $e)
        {
            return ['status' => false,'error' => $e->getMessage()];
        }

    }

    /**
     * @param int $productId
     * @return array
     */

    public function getProduct(int $productId)
    {
        try{
            $data = $this->wc->get('products/'.$productId);
            return ['status' => true,'data' => $data];
        }
        catch (HttpClientException $e)
        {
            return ['status' => false,'error' => $e->getMessage()];
        }
    }


    /**
     * @param int $wc_id
     * @param string $title
     * @param string $sku
     * @param string $price
     * @param string $slug
     * @return array
     */

    public function updateWcProduct(int $wc_id, string $stock = '0')
    {
        try{
            $this->wc->put('products/'.$wc_id,['stock_quantity' => $stock]);
            $result = ['status' => true];
        }
        catch(HttpClientException $e)
        {
            $result =  ['status' => false,'error' => $e->getMessage()];
        }

        return $result;

    }

    /**
     * @param int $wc_id
     * @param int $variationId
     * @param string $stock
     * @return array|bool[]
     */

    public function updateWcProductVariation(int $wc_id,int $variationId, string $stock = '0')
    {
        try{
            $this->wc->put('products/'.$wc_id.'/variations/'.$variationId,['stock_quantity' => $stock]);
            $result = ['status' => true];
        }
        catch(HttpClientException $e)
        {
            $result =  ['status' => false,'error' => $e->getMessage()];
        }

        return $result;

    }

    public function updateStockWithinArray(string $stock, string $sku, array &$array)
    {
        if($array)
        {
            foreach($array as $key=>$value)
            {
                if($array[$key]->sku == $sku)
                {
                    $array[$key]->stock_quantity  = $stock;
                }
            }
            return $array;
        }

    }

    /**
     * @param array $array
     * @return array
     */

    public function cleanNonSkuArray(array $array)
    {
       if($array && count($array) > 0)
       {
           foreach($array as $key=>$val)
           {
               if(isset($array[$key]->sku) && $array[$key]->sku == '')
               {
                   unset($array[$key]);
               }
           }
           return $array;
       }
    }
}
