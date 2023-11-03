<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

setlocale(LC_ALL, 'UTF-8');
mb_internal_encoding('UTF-8');
bind_textdomain_codeset('default', 'UTF-8');

date_default_timezone_set('Europe/Minsk');

spl_autoload_register(function($sClassName) {
    $sPath = realpath(__DIR__ . '/../application/' . str_replace('\\', '/', $sClassName) . '.php');
    if ($sPath) {
        include $sPath;
        return true;
    }
    return false;
});
require __DIR__ . '/../vendor/autoload.php';

// Identify environment as a 'test'-environment
(new \Engine\Request\Input\Server)->setEnv(\Defines\ServerType::DEV);

// Predefine application params
(new \System\Bootstrap(false))->runApplicationParams();

// replace with mechanism to retrieve EntityManager in your app
$entityManager = \System\Registry::connection(\Defines\Connector::MYSQL_DOCTRINE);

return ConsoleRunner::createHelperSet($entityManager);