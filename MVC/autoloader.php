<?php

spl_autoload_register( function ($classPath) {

    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath) . '.php';

    if(is_file(BLOGIC_DIR .  $relativePath)) {

        require BLOGIC_DIR . $relativePath;
    }
});
