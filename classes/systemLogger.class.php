<?php

namespace LexSystems;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Store;
use SleekDB\Query;

class SystemLogger
{

    /**
     * @param string $type
     * @param string $message
     * @param string $location
     * @return array
     */

    public function __construct()
    {
        $config = [
            "auto_cache" => true,
            "cache_lifetime" => null,
            "timeout" => false,
            "primary_key" => "_id",
            "search" => [
                "min_length" => 2,
                "mode" => "or",
                "score_key" => "scoreKey",
                "algorithm" => Query::SEARCH_ALGORITHM["hits"]
            ]
        ];
        $this->db =  new \SleekDB\Store("cron-logs", __DIR__.'/../databases/',$config);
    }



    /**
     * @param string $type
     * @param string $message
     * @return array|bool[]
     * @throws \SleekDB\Exceptions\IOException
     * @throws \SleekDB\Exceptions\IdNotAllowedException
     * @throws \SleekDB\Exceptions\InvalidArgumentException
     * @throws \SleekDB\Exceptions\JsonException
     */

    public function logDb(string $type, string $message)
    {
        if(Config::ENABLE_LOGGING) {
            try
            {
                $this->db->insert([
                    'type' => '[' . $type . ']',
                    'message' => $message,
                    'date' => date('Y-m-d H:i:s')
                ]);
                return ['status' => true];

           }
           catch (InvalidArgumentException $e)
           {
               return ['status' => false, 'error' =>$e->getMessage()];
           }
        }
        else
        {
            return ['status' => false,'error' => 'Loggin not enabled. Check settings.'];
        }
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     * @throws \SleekDB\Exceptions\IOException
     */

    public function getLogEntries(int $limit = 10)
    {
        return $this->db->findAll(['date' => 'desc'],$limit);
    }

    /**
     * @return int
     * @throws \SleekDB\Exceptions\IOException
     */
    public function getLogCount()
    {
        return $this->db->count();
    }

    /**
     * Delete all log entries
     */
    public function deleteLogEntries()
    {
        $files = glob(__DIR__.'/../databases/cron-logs/data/*');
        foreach($files as $file) {
            if(is_file($file))
                if(unlink($file))
                {
                    return true;
                }
            else
            {
                return false;
            }
        }
    }
}