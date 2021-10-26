<?php

/**
 * Autoload Classes in order
 * (c) Alexandru Lupaescu 2021
 */
require "config/Config.php";
require 'libs/sleekdb/vendor/autoload.php';
require 'classes/Debugger.php';
//require "classes/Database.php";
require "classes/systemLogger.class.php";
require "classes/DataRender.php";
require "libs/php-curl-class/vendor/autoload.php";
require "classes/FgoWrapper.php";
require "classes/Login.php";
require "classes/Admin.php";
require "libs/Woocommerce/vendor/autoload.php";
require 'classes/WoocommerceWrapper.php';
require 'classes/WoocommerceCronWrapper.php';
require 'classes/WoocommerceCronWrapperUpdate.php';
