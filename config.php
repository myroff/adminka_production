<?php

/*** URL CONFIGS */
define('BASIS_URL', "");
define('PUBLIC_URL', BASIS_URL . '/Public');
define('BASIS_RECHNUNG_URL', BASIS_URL."/Public/Rechnungen");

/*** PATH CONFIGS ***/
define('BASIS_DIR', __DIR__ . DIRECTORY_SEPARATOR); //z.B. D:\www\tut
define('TWIG_CACHE_DIR', BASIS_DIR . 'TemplatesTwigCache' . DIRECTORY_SEPARATOR);
define('TWIG_TEMPLATE_DIR', BASIS_DIR . 'TemplatesTwig' . DIRECTORY_SEPARATOR);
define('BASIS_RECHNUNG_DIR', BASIS_DIR . 'Public' . DIRECTORY_SEPARATOR . 'Rechnungen' . DIRECTORY_SEPARATOR);
define('BLOGIC_DIR', BASIS_DIR . 'BLogic' . DIRECTORY_SEPARATOR);

