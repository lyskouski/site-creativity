<?php namespace Engine\Request;

/**
 * Configuration data
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Request
 */
class Config
{

    /**
     * Configuration parameters
     * @var array
     */
    protected $aParams = array();

    /**
     * Path to Modules (Controllers, Models, Views) directory
     * @var string
     */
    protected $sAppPath;

    /**
     * Path to a translations directory
     * @var string
     */
    protected $sTranslationPath;

    /**
     * Init configuration data
     */
    public function __construct($sPath)
    {
        $this->sDataPath = $sPath;

        $sAppType = (new \Engine\Request\Input\Server)->getEnv();
        $aIni = parse_ini_file($sPath, true);
        $aConfig = $aIni['default'];
        if (isset($aIni[$sAppType])) {
            $aConfig = $aIni[$sAppType];
        }

        $this->aParams = $this->mergeConfig(
                $this->prepareConfig($aIni['global']), $this->prepareConfig($aConfig)
        );

        $this->updatePHP($this->aParams['php']);
    }

    public function mergeConfig($aInitial, $aUpdate)
    {
        foreach ($aUpdate as $sKey => $mValue) {
            if (!isset($aInitial[$sKey])) {
                $aInitial[$sKey] = array();
            }
            if (is_array($mValue)) {
                $aInitial[$sKey] = $this->mergeConfig($aInitial[$sKey], $mValue);
            } else {
                $aInitial[$sKey] = $mValue;
            }
        }
        return $aInitial;
    }

    /**
     * Convert strings with dot separation into arrays
     *
     * @param array $aConfig
     * @return array
     */
    protected function prepareConfig($aConfig)
    {
        $aParams = array();
        foreach ($aConfig as $sName => $mValue) {
            $a = &$aParams;
            foreach (explode('.', $sName) as $sPart) {
                if (!isset($a[$sPart])) {
                    $a[$sPart] = array();
                }
                $a = &$a[$sPart];
            }
            $a = $mValue;
        }
        return $aParams;
    }

    /**
     * Update PHP configuration parameters
     *
     * @param array $aConfig
     */
    protected function updatePHP($aConfig)
    {
        foreach ($aConfig as $sFunction => $mValues) {
            if (is_array($mValues)) {
                foreach ($mValues as $sParam => $sValue) {
                    $sFunction($sParam, $sValue);
                }
            } else {
                $sFunction($mValues);
            }
        }
    }

    /**
     * Define application directory
     *
     * @param string $sPath
     */
    public function setAppPath($sPath)
    {
        $this->sAppPath = $sPath;
    }

    /**
     * Get application dir path
     *
     * @return string
     */
    public function getAppPath()
    {
        if (is_null($this->sAppPath)) {
            throw new \Error\Application('Incorrect implementation');
        }
        return $this->sAppPath;
    }

        /**
     * Get application dir path
     *
     * @return string
     */
    public function getPublicPath()
    {
        if (is_null($this->sAppPath)) {
            throw new \Error\Application('Incorrect implementation');
        }
        return realpath($this->sAppPath . '/../../public');
    }

    /**
     * Get default controller path
     *
     * @return string
     */
    public function getModulePrefix()
    {
        return $this->aParams['module_prefix'];
    }

    /**
     * Get title prefix
     *
     * @return string
     */
    public function getTitlePrefix()
    {
        return $this->aParams['title_prefix'];
    }

    /**
     * Define application directory
     *
     * @param string $sPath
     */
    public function setTranslationPath($sPath)
    {
        $this->sTranslationPath = $sPath;
    }

    /**
     * Get application dir path
     *
     * @return string
     */
    public function getTranslationPath()
    {
        if (is_null($this->sAppPath)) {
            throw new \Error\Application('Incorrect implementation');
        }
        return $this->sTranslationPath;
    }

    public function getConfigPath()
    {
        return realpath($this->sTranslationPath . '/..');
    }

    /**
     * Get default controller path
     *
     * @return string
     */
    public function getDefController()
    {
        return $this->aParams['controller_default'];
    }

    /**
     * Get DEBUG status
     *
     * @return boolean
     */
    public function getDebugMode()
    {
        return $this->aParams['debug_mode'];
    }

    /**
     * Get main dir path where creativity_hg, _test, _main are located
     *
     * @return string
     */
    public function getReleaseDir()
    {
        return $this->aParams['dir'];
    }


    /**
     * Get DEBUG status
     *
     * @return boolean
     */
    public function getDevMode()
    {
        return $this->aParams['dev_mode'];
    }

    /**
     * Get yndex key for API.Translation
     *
     * @return boolean
     */
    public function getYandexKey()
    {
        return $this->aParams['yandex']['translation_key'];
    }

    /**
     * Get all possible languages
     *
     * @return boolean
     */
    public function getMinimize()
    {
        return (boolean) $this->aParams['minimize'];
    }

    /**
     * Check if searches could index content
     *
     * @return boolean
     */
    public function getIndexing() {
        return (boolean) $this->aParams['indexes'];
    }

    /**
     * Get database connection's parameters
     * @sample \System\Registry::config()->getPdo()->mysql;
     *
     * @return object
     */
    public function getPdo()
    {
        return $this->aParams['pdo_connect'];
    }

    /**
     * Get debugger type
     * @sample 'whoops' - external module for an attractive bug overview
     * @sample 'default' - internal implementation
     *
     * @return string
     */
    public function getDebugger()
    {
        return $this->aParams['debug_type'];
    }

    /**
     * Get basic email address for sending and getting messages
     *
     * @return string
     */
    public function getMailName()
    {
        return $this->aParams['mail']['smtp']['username'];
    }

    /**
     * Get all fields for a Google API
     * @return array
     */
    public function getGoogleAPI()
    {
        return $this->aParams['google'];
    }

    /**
     * SMTP server parameters to login and send messages
     *
     * @return array
     */
    public function getMailPush()
    {
        return $this->aParams['mail']['smtp'];
    }

    /**
     * Get basic URL
     *
     * @return string
     */
    public function getUrl($lang = null, $suffix = true)
    {
        $var = $this->aParams['basic_url'];
        if (is_null($lang)) {
            $lang = \System\Registry::translation()->getTargetLanguage();
        }
        if (is_array($var) && isset($var[$lang])) {
            $var = $var[$lang];
        } else {
            if (is_array($var)) {
                $var = $var['default'];
            }
            if ($suffix) {
                $var .= "/$lang";
            }
        }
        return $var;
    }

    /**
     * Get basic URL list
     *
     * @return string
     */
    public function getUrlList()
    {
        return (array) $this->aParams['basic_url'];
    }

    /**
     * Get basic social URL
     *
     * @return string
     */
    public function getSocial()
    {
        return new \System\ArrayUndef($this->aParams['social']);
    }

    /**
     * Get proxy configuration
     *
     * @return string
     */
    public function getProxy()
    {
        return $this->aParams['proxy'];
    }

    /**
     * Return social API parameters
     *
     * @return string
     */
    public function getSocialApi($type)
    {
        return new \System\ArrayUndef($this->aParams['api'][$type]);
    }

    /**
     * IMAP server parameters to read messages
     *
     * @return array - ['server'=>.., 'username'=>.., 'password'=>..]
     */
    public function getMailPull()
    {
        return array(
            'server' => $this->aParams['mail']['imap']['server'],
            'username' => $this->aParams['mail']['smtp']['username'],
            'password' => $this->aParams['mail']['smtp']['password']
        );
    }

}
