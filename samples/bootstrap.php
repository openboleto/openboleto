<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Recife');

define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath(__DIR__ . DS . '..'));

$composer_autoload = APP_ROOT . DS . 'vendor' . DS . 'autoload.php';
if (!@include($composer_autoload)) {

    /* Include path */
    set_include_path(implode(PATH_SEPARATOR, array(
        __DIR__ . '/../src',
        get_include_path(),
    )));

    /* PEAR autoloader */
    spl_autoload_register(
        function($className) {
            $filename = strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
                $path .= DIRECTORY_SEPARATOR . $filename;
                if (is_file($path)) {
                    require_once $path;
                    return true;
                }
            }
            return false;
        }
    );
}