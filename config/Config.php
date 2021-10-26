<?php
/**
 * Configuration Params
 */
namespace LexSystems;

class Config
{
    /**
     * Logger Details
     */
    const ENABLE_LOGGING = true;

    /**
     * Cron details
     * how many seconds do you want to run
     * the cron
     */

    const CRON_LIMIT = 1350;
        
    /**
     * FGO Data
     */

    const PRIVATE_KEY = "";
    const COD_UNIC = "";
    const URL_PLATFORM = "";


    /**
     * WooCommerce Credentials
     */

    const WC_CK = '';
    const WC_CS = '';
    const WC_URL = '';

    /**
     * Login Inferface
     */

    const LOGIN_USERNAME = "admin";
    const LOGIN_PASSWORD = "admin";

}
