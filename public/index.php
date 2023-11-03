<?php
setlocale(LC_ALL, 'UTF-8');
mb_internal_encoding('UTF-8');
bind_textdomain_codeset('default', 'UTF-8');

date_default_timezone_set('Europe/Minsk');

if (!strpos(get_include_path(), 'PEAR')) {
    set_include_path(
        get_include_path()
        . PATH_SEPARATOR . '/usr/share/php'
        . PATH_SEPARATOR . '/usr/share/php/PEAR'
    );
}

spl_autoload_register(function($sClassName) {
    $appDir = __DIR__ . '/../application/';
    $sPath = realpath($appDir . str_replace('\\', '/', $sClassName) . '.php');
    if ($sPath) {
        include $sPath;
    } elseif (is_dir($appDir . substr($sClassName, 0, strpos($sClassName, '\\')))) {
        throw new \Error\Application("Missing $sClassName !");
    }
    return $sPath;
});

require __DIR__ . '/../vendor/autoload.php';

// Check that CRON is running
global $argc, $argv;
$cron = false;
if ((boolean) $argc && php_sapi_name() === 'cli') { // PHP_SAPI
    $cron = array_slice($argv, 1);
}
\System\Registry::setCron($cron);

// Basic site functionality
new \System\Bootstrap();

// Check if any content is missing
\System\Registry::translation()->checkMissings();
