<?php
/***
 * Simple database connection class
 * (c) 2021 Alexandru Lupaescu
 */
namespace LexSystems;

class Database extends Config
{
    /**
     * @return false|\mysqli|null
     */

    public function connect(string $dbName = "")
    {
        if(!empty($dbName))
        {
            $con = mysqli_connect(Config::MYSQL_HOST,Config::MYSQL_USER,Config::MYSQL_PASS,$dbName);
        }
        else
        {
            $con = mysqli_connect(Config::MYSQL_HOST,Config::MYSQL_USER,Config::MYSQL_PASS,Config::MYSQL_DB);
        }

        if($con)
        {
            return $con;
        }
        else
        {
            die(mysqli_error($con));
        }
    }
}