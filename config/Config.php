<?php
/**
 * Configuration Params
 */
namespace LexSystems;

class Config
{
    /**
     * Prestashop MySQL Details
     */
    const ENABLE_LOGGING = true;
    const MYSQL_HOST = "localhost";
    const MYSQL_USER = "mgromita_presta";
    const MYSQL_PASS = "Amarian123";

    const MYSQL_DB = "mgromita_presta";
    const IMPORTER_DB = "mgromita_importer";

        
    /**
     * FGO Data
     */

    const PRIVATE_KEY = "625FA84B383080AB7A1B0830BD4044B3";
    const COD_UNIC = "10363240";
    const URL_PLATFORM = "https://woostocks.mgromitalia.ro/";


    /**
     * WooCommerce Credentials
     */

    const WC_CK = 'ck_55f3318d366338ee9ddcb55f9522c613f6d76276';
    const WC_CS = 'cs_2186da9755b4e15151ada7a32cbcae7032d67ade';
    const WC_URL = 'https://test.mgromitalia.ro/';

    /**
     * Login Inferface
     */

    const LOGIN_USERNAME = "admin";
    const LOGIN_PASSWORD = "admin";

}
