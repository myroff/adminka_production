<?php

$__tmp__ = explode($_SERVER['DOCUMENT_ROOT'], __DIR__);
$__relativeBasePath__ = end($__tmp__);

/*** URL CONFIGS */
define('BASIS_URL', $__relativeBasePath__);
define('PUBLIC_URL', BASIS_URL . '/Public');
define('BASIS_RECHNUNG_URL', BASIS_URL."/Public/Rechnungen");

/*** PATH CONFIGS ***/
define('BASIS_DIR', __DIR__ . DIRECTORY_SEPARATOR); //z.B. D:\www\tut
define('PUBLIC_DIR', BASIS_DIR . 'Public' . DIRECTORY_SEPARATOR);
define('TWIG_CACHE_DIR', BASIS_DIR . 'TemplatesTwigCache' . DIRECTORY_SEPARATOR);
define('TWIG_TEMPLATE_DIR', BASIS_DIR . 'TemplatesTwig' . DIRECTORY_SEPARATOR);
define('BASIS_RECHNUNG_DIR', BASIS_DIR . 'Public' . DIRECTORY_SEPARATOR . 'Rechnungen' . DIRECTORY_SEPARATOR);
define('BLOGIC_DIR', BASIS_DIR . 'BLogic' . DIRECTORY_SEPARATOR);

$__tmp__ = NULL;
$__relativeBasePath__ = NULL;
