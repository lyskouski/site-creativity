<?php namespace System;

use Engine\Request;
use Engine\Response\Translation;
use Engine\Request\Input\Server;

/**
 * Systen autoloader
 *
 * @since 2015-08-10
 * @author Viachaslau Lyskouski
 * @package System
 */
class Bootstrap
{

    /**
     * Run all required function for a correct application behaviour
     * @note the same as: $this->initAutoloader()->...->initTranslation();
     */
    public function __construct($bAuto = true)
    {
        if (!$bAuto) {
            return;
        }

        $aFuncList = (new Aggregator)->func2array($this, 'init');
        // for PHPUnit tests exclude controller run
        if ((new Server)->isTest()) {
            $aFuncList = array_diff($aFuncList, array('initShutdown', 'initController'));
        }

        $this->runApplicationParams();
        // Boostrap customisation
        if (file_exists(Registry::config()->getAppPath(). '/Bootstrap.php')) {
            $bootClass = Registry::config()->getModulePrefix() . '\Bootstrap';
            new $bootClass($bAuto);
        // Normal behaviour
        } else {
            foreach ($aFuncList as $sMethod) {
                Registry::logger()->debug("Bootstrap::$sMethod()", array('file' => __FILE__, 'line' => __LINE__));
                $this->{$sMethod}();
            }
        }
    }

    public function runApplicationParams()
    {
        $sPath = realpath(__DIR__ . '/../../');
        $oConfig = new Request\Config($sPath . '/config/application.ini');
        $oConfig->setTranslationPath($sPath . '/config/locale');

        // For CLI
        $appPath = '';
        $input = new \Engine\Request\Input();
        if (is_array($input->getServer())) {
            $appPath = $input->getServer('APP_PATH', '');
        }

        if ($appPath && $appPath !== 'test') {
            $oConfig->setAppPath($sPath . '/application/External/'. $appPath);
        } else {
            $oConfig->setAppPath($sPath . '/application'. str_replace('\\', '/', $oConfig->getModulePrefix()) . '/');
        }
        // Set congifuration parameters
        Registry::setConfig($oConfig);

        // Check language from URL
        $language = \Defines\Language::getDefault();
        $currUrl = $input->getServer('HTTP_HOST') . $input->getServer('REQUEST_URI');
        foreach (Registry::config()->getUrlList() as $lang => $url) {
            $addr = trim(substr($url, strpos($url, ':')), ':/');
            if ($lang !== 'default' && strpos($currUrl, $addr) !== false) {
                $language = $lang;
                break;
            }
        }
        Registry::setTranslation(new Translation($language));

        // Define logger
        Registry::setLogger(new Logger($oConfig->getDebugMode()));
    }

    /**
     * Init sutdown functionlity to catch errors and thrown exceptions
     */
    public function initShutdown()
    {
        switch (Registry::config()->getDebugger()) {
            case \Defines\Debugger::WHOOPS:
                $whoops = new \Whoops\Run;
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                $whoops->register();
                break;

            default:
                $oHandler = new \System\Handler();
                register_shutdown_function(array($oHandler, 'catchShutdown'));
                set_exception_handler(array($oHandler, 'catchException'));
                set_error_handler(array($oHandler, 'catchError'));

        }
        // xdebug overview is not needed
        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }
        return $this;
    }

    /**
     * User identification
     */
    public function initUser()
    {
        $sToken = (new \Engine\Request\Input)->getCookie(\Access\User::COOKIE_AUTH);
        Registry::setUser(new \Access\User($sToken));
    }

    /**
     * Run controller with its action
     * @throws \Error\Validation
     */
    public function initController()
    {
        $params = new Request\Params();
        Registry::setTranslation(new Translation($params->getLanguage()));

        if (!$params->getModuleName()) {
            throw new \Error\Validation('Incorrect request!');
        }

        $migrate = new \Deprecated\Migration();
        $migrate->checkUkraine();

        if ($params->getRequestMethod() === \Defines\RequestMethod::POST) {
            $size = (new \Engine\Request\Input)->getServer('CONTENT_LENGTH');
            $limitSize = (new Converter\Number)->getBytes(ini_get('post_max_size'));
            if ($size > $limitSize) {
                throw new \Error\Validation("Request limitation: $size (max: $limitSize)");
            }
        }

        $cntName = $params->getModuleName() . '\Controller';
        if (!\System\Registry::cron() && $cntName !== '\Modules\Index\Controller') {
            $migrate->checkBackward();
        }
        new $cntName($params);
    }
}
