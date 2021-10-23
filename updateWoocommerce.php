<?php
require 'class.loader.php';
$logger = new \LexSystems\SystemLogger();
$starttime = time();
$wc_cron_wrapper = new \LexSystems\WoocommerceCronWrapper();
do{
    //make something
    $wc_cron_wrapper->init();
}
while ((time() - $starttime)<298); //stop with 298 seconds
$currentPage = $wc_cron_wrapper->returnLastProductsPage();
if($currentPage > 1)
{
    $currentPage = $currentPage - 1;
}
else
{
    $currentPage = $currentPage;
}
$wc_cron_wrapper->writeProductsPageNumber($currentPage);
$logger->logDb("FGO DATA DUMP PAGINATE","ensured restart from previous page with number " .$currentPage);
$logger->logDb("FGO DATADUMP","init: Cron stopped at 298 seconds.");
