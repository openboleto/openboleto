<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Sao_Paulo');

/* Include path */
set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__ . DIRECTORY_SEPARATOR . 'src',
    __DIR__ . DIRECTORY_SEPARATOR . 'tests',
    get_include_path(),
)));

/* PEAR autoloader */
spl_autoload_register(function($className) {
    $filename = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $className) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        $path = $path . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            include $path;
            return true;
        }
    }
    return false;
});