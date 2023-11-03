<?php namespace External\Sample;

/**
 * Systen autoloader
 *
 * @since 2015-08-10
 * @author Viachaslau Lyskouski
 * @package System
 */
class Bootstrap extends \System\Bootstrap
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

        foreach ($aFuncList as $sMethod) {
            Registry::logger()->debug("Bootstrap::$sMethod()", array('file' => __FILE__, 'line' => __LINE__));
            $this->{$sMethod}();
        }
    }

    public function initApplicationParams()
    {
        $oConfig = new Request\Config( __DIR__ . '/config/application.ini');
        $oConfig->setTranslationPath(__DIR__ . '/config/locale');

        $appPath = (new \Engine\Request\Input)->getServer('APP_PATH');
        $oConfig->setAppPath(__DIR__ . $appPath);

        Registry::setConfig($oConfig);
        Registry::setTranslation(new Translation(\Defines\Language::getDefault()));
        Registry::setLogger(new Logger($oConfig->getDebugMode()));
    }

    /**
     * Run controller with its action
     * @throws \Error\Validation
     */
    public function initController()
    {
        $oParams = new Request\Params();
        Registry::setTranslation(new Translation($oParams->getLanguage()));

        if (!$oParams->getModuleName()) {
            throw new \Error\Validation('Incorrect request!');
        }

        if ($oParams->getRequestMethod() === \Defines\RequestMethod::POST) {
            $iLength = (new \Engine\Request\Input)->getServer('CONTENT_LENGTH');
            $iMax = ini_get('post_max_size') * 1024 * 1024;
            if ($iLength > $iMax) {
                throw new \Error\Validation("Request limitation: $iLength (max: $iMax)");
            }
        }

        $sControllerName = $oParams->getModuleName() . '\Controller';
        new $sControllerName($oParams);
    }

}
