<?php

namespace LexSystems;

class SystemLogger extends Database
{

    /**
     * @param string $type
     * @param string $message
     * @param string $location
     * @return array
     */

    public static function saveLog(string $type, string $message)
    {
        if(Config::ENABLE_LOGGING)
        {
            $location = Config::LOGS_DIR;
            $filename = time()."_log.txt";
            $logFile = fopen($location.'/'.$filename, "w");
            if($logFile)
            {
                $message = '['.$type.']'.' - '.$msg.' - ' .date("Y-m-D H:i:s");
                fwrite($logFile,$message);
                fclose($logFile);
                return ['status' => true,'log_file' => $location.'/'.$filename];
            }
            else
            {
                return ['status' => false,'error' => 'Cannot create the log file. Check permissions and make sure '.$location.' is writable. (777 perms)'];
            }
        }
        else
        {
            return ['status' => false,'Logging not enabled.Check settings.'];
        }

    }

    /**
     * @param string $type
     * @param string $message
     * @return array|bool[]
     */
    public function logDb(string $type, string $message)
    {
        if(Config::ENABLE_LOGGING)
        {
            $con = $this->connect(Config::IMPORTER_DB);

            $insert = mysqli_query($con, "INSERT into global_logs VALUES (DEFAULT,'[".addslashes($type)."]','".addslashes($message)."','".date("Y-m-d H:i:s")."')");

            if($insert)
            {
                return ['status' => true];
            }
            else
            {
                return ['status' => false,'error' => mysqli_error($con)];
            }
        }
        else
        {
            return ['status' => false,'error' => 'Loggin not enabled. Check settings.'];
        }


    }
}