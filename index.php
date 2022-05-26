<?php
//setlocale(LC_TIME, "de_DE");
/*
wenn die Webseite aus einem Unterordner ausgeführt wird(z.B. www.myPage.com/myOrder),
definiere BASIS_URL als "/myOrder/"
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__.'/config.php');

#phpinfo();
#require_once './MVC/config.php';
require_once './MVC/MVC.php';
