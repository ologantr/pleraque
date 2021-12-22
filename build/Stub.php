<?php
if(version_compare("8.0.0", phpversion()) > 0)
    die("PHP 8.0.0 or higher is required");

spl_autoload_register(function(string $class) : void {
    require "phar://pleraque.phar/"
        . str_replace("\\", "/",
                      str_replace("Pleraque\\", "", $class))
        . ".php";
});
Phar::mapPhar("pleraque.phar");
__HALT_COMPILER();
?>
