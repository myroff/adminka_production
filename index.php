<?php
//setlocale(LC_TIME, "de_DE");
/*
wenn die Webseite aus einem Unterordner ausgeführt wird(z.B. www.myPage.com/myOrder),
definiere BASIS_URL als "/myOrder/"
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__.'/config.php');

require_once __DIR__.'/MVC/config_db.php';
require_once __DIR__.'/Vendor/autoload.php';
require_once __DIR__.'/MVC/MVC.php';

#