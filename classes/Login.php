<?php
namespace LexSystems;
class Login extends Config
{
    /**
     * @return array|false[]
     */

    public static function returnSession()
    {
        if($_SESSION)
        {
            foreach($_SESSION as $key=>$val)
            {
                $params[$key] = $_SESSION[$key];
            }
            return $params;
        }
        else
        {
            return ['status' => false];
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return array|bool[]
     */

    public static function login(string $username, string $password)
    {
        if($username == Config::LOGIN_USERNAME && $password == Config::LOGIN_PASSWORD)
        {
            $_SESSION['status'] = true;
            $_SESSION['user'] = Config::LOGIN_USERNAME;
            return ['status' => true];
        }
        else
        {
            return ['status' => false,'error' => 'Credentiale Incorecte'];
        }
    }

    /**
     * @return bool
     */

    public static function logout()
    {
        session_destroy();
        return true;
    }
}