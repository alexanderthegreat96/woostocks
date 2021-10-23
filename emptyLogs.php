<?php
require "class.loader.php";
$cronWrapper = new \LexSystems\CronWrapper();
$update = $cronWrapper->emptyLogs();