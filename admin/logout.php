<?php
session_start();
require '../class.loader.php';
$logout = \LexSystems\Login::logout();
if($logout)
{
    \LexSystems\Admin::redirect('login.php');
}