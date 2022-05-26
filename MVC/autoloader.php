<?php

spl_autoload_register( function ($classPath) {

    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath) . '.php';

    if(is_file(BLOGIC_DIR .  $relativePath)) {
echo "<br>\n"."required ".$classPath;
        require BLOGIC_DIR . $relativePath;
    }
});
