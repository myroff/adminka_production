<?php

/*** URL CONFIGS */
define('BASIS_URL', "");
define('BASIS_RECHNUNG_URL', BASIS_URL."/Public/Rechnungen");

/*** PATH CONFIGS ***/
define('BASIS_DIR', __DIR__ . DIRECTORY_SEPARATOR); //z.B. D:\www\tut
define('TWIG_CACHE_DIR', BASIS_DIR . 'TemplatesTwigCache' . DIRECTORY_SEPARATOR);
define('TWIG_TEMPLATE_DIR', BASIS_DIR . 'TemplatesTwig' . DIRECTORY_SEPARATOR);
define('BASIS_RECHNUNG_DIR', BASIS_DIR . 'Public' . DIRECTORY_SEPARATOR . 'Rechnungen' . DIRECTORY_SEPARATOR);
define('BLOGIC_DIR', BASIS_DIR . 'BLogic' . DIRECTORY_SEPARATOR);

/*** DB CONFIGS ***/
define('DB_HOST', "adminka_db");
define('DB_PORT', "3306");
define('DB_NAME', "name");
define('DB_USER', "user");
define('DB_PSWD', "pswd");
