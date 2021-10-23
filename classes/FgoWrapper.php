<?php
namespace LexSystems;
use Cassandra\Exception\DivideByZeroException;
use LexSystems\Framework\Middleware\Debugger;

class FgoWrapper extends Config
{
    /**
     * FgoWrapper constructor.
     */

    public function __construct()
    {
        $this->private_key = Config::PRIVATE_KEY;
        $this->cui = Config::COD_UNIC;
        $this->urlPlatform = Config::URL_PLATFORM;
    }

    /**
     * @return string
     */

    private function getHash()
    {
        return strtoupper(SHA1($this->cui . $this->private_key));
    }
    /**
     * @param string $productCode
     * @return array
     */

    public function getArticle(string $productCode)
    {
        $data = [];
        $data['CodUnic'] = $this->cui;
        $data['Hash'] = $this->getHash();
        $data['PlatformaUrl'] = $this->urlPlatform;
        $data['CodArticol'] = $productCode;


        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents('https://api.fgo.ro/v1/articol/get', false, $context);
        $response = json_decode($result,true);

        if($response['Success'])
        {
           return ['status' => true,'data' => $response['Result']];
        }
        else
        {
            return ['status' => false,'error' => $response['Message']];
        }
    }

    /**
     * @param array $productCodes
     * @return array
     */

    public function getArticles(array $productCodes)
    {

        if(is_array($productCodes) && count($productCodes) <= 30)
        {

            $data = [];
            $data['CodUnic'] = $this->cui;
            $data['Hash'] = $this->getHash();
            $data['PlatformaUrl'] = $this->urlPlatform;
            $data['Coduri'] = array_slice($productCodes,0,30);

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents('https://api.fgo.ro/v1/articol/getlist', false, $context);


            if($result)
            {
                $response = json_decode($result,true);
                if($response['Success'])
                {
                    return ['status' => true,'data' => $response['Result']];
                }
                else
                {
                    return ['status' => false,'error' => $response['Message']];
                }
            }
            else
            {
                return ['status' => false,'error' => 'Could not resolve endpoint!'];
            }


        }
        else
        {
            return ['status' => false,'error' => 'Un numar maxim de 30 de coduri de produs sunt suportate.'];
        }
    }
}
