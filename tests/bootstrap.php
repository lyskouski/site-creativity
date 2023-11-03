<?php
setlocale(LC_ALL, 'UTF-8');
mb_internal_encoding('UTF-8');
bind_textdomain_codeset('default', 'UTF-8');

date_default_timezone_set('Europe/Minsk');

// xdebug overview is not needed for tests
if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

spl_autoload_register(function($className) {
    $file = str_replace('\\', '/', $className) . '.php';
    $path = realpath(__DIR__ . '/../application/' . $file);
    $testPath = realpath(__DIR__ . '/' . $file);
    if ($testPath) {
        include $testPath;
        return true;
    } elseif ($path) {
        include $path;
        return true;
    }
    return false;
});
require __DIR__ . '/../vendor/autoload.php';

\System\Registry::setCron(false);

// Identify environment as a 'test'-environment
(new \Engine\Request\Input\Server)->setEnv(\Defines\ServerType::TEST);

// Predefine application params
(new \System\Bootstrap(false))->runApplicationParams();

// Init memory database by tables (from their entities)
$database = new \Tests\Config\Database();
\System\Registry::set(\Tests\Config\Database::REG_TYPE, $database);
\System\Registry::changeConnection(\Defines\Connector::MYSQL_DOCTRINE, $database->initConnection());
(new \Tests\DbType)->fillBySQL(__DIR__ . '/Tests/Config/start.sql');

// Run bootstrap in a normal way
new \System\Bootstrap();
