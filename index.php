<?php
//setlocale(LC_TIME, "de_DE");
/*
wenn die Webseite aus einem Unterordner ausgeführt wird(z.B. www.myPage.com/myOrder),
definiere BASIS_URL als "/myOrder/"
*/
#error_reporting(E_ALL);
#ini_set('display_errors', 1);

if ( !defined('BASIS_URL') )
	define('BASIS_URL', "/2018");
if ( !defined('BASIS_DIR') )
	define('BASIS_DIR', dirname(__FILE__)); //z.B. D:\www\tut
if ( !defined('ACTUAL_YEAR') )
	define('ACTUAL_YEAR', "2018");
if ( !defined('BASIS_RECHNUNG_URL') )
	define('BASIS_RECHNUNG_URL', "/".ACTUAL_YEAR."/Public/Rechnungen");
if ( !defined('BASIS_RECHNUNG_DIR') )
	define('BASIS_RECHNUNG_DIR', BASIS_DIR."/Public/Rechnungen");
if ( !defined('TWIG_TEMPLATE_DIR') )
	define('TWIG_TEMPLATE_DIR', BASIS_DIR."/TemplatesTwig");
if ( !defined('TWIG_CACHE_DIR') )
	define('TWIG_CACHE_DIR', BASIS_DIR."/TemplatesTwigCache");
/*
if ( !defined('AJAX_URL') )
	define('AJAX_URL', '/admin/ajax'); //z.B. D:\www\tut
*/

require_once './MVC/MVC.php';
