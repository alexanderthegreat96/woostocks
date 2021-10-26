# WooStocks - FGO to Woocommerce Product Stock Sync CRON Tool
Allows syncing from FGO to Woocommmerce. Implements a Logger with a Logger Interface 
for checking data inputs and outputs.  

# Installation
Edit config/Config.php
```php
 /**
     * Prestashop MySQL Details
     */
    const ENABLE_LOGGING = true;
        
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

```
# Chmod your directories
```bash
  chmod 777 -R databases
  chmod 777 -R paginations

```

# Deploying

 - Run updateWoocommerce.php + emptyLogs.php to empty all log entries.
 - Access /admin/ with your admin/admin by default  to check your logs
 - Lastly , set up the cron script for updateWoocommerce.php

# Other Links

- [FGO.ro](https://fgo.ro)
- [Woocommerce](https://woocommerce.github.io/woocommerce-rest-api-docs/#list-all-product-variations)
