<?php namespace Engine\Response;

use System\Registry;

/**
 * Viewer class
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response
 */
class Template
{

    const T_DEFAULT = 'default';
    const VIEW_FOLDER = '/zView';

    protected $sTargetDir = '';
    protected $aData = array();
    protected $sTemplate = self::T_DEFAULT;

    /**
     * @var \System\Aggregator
     */
    protected $oAgg;

    /**
     * List of a temporary registered functions
     * @var array
     */
    protected $aFunc = array();

    /**
     * @var \Engine\Response
     */
    protected $response;

    /**
     * Set target directory
     *
     * @param string $sDirectory
     */
    public function __construct($sTemplate = null, $sTargetDir = null)
    {
        $this->oAgg = new \System\Aggregator();
        // Set destination folder
        if (is_null($sTargetDir)) {
            $aList = debug_backtrace(true);
            $sTargetDir = dirname($aList[strpos($aList[0]['file'], 'Layouts/Helper') ? 1 : 0]['file']) . self::VIEW_FOLDER;
        }
        Registry::logger()->debug('\Engine\Response\Template::__construct ', array('template' => $sTemplate, 'sTargetDir' => $sTargetDir));
        $this->sTargetDir = $sTargetDir;

        if (strpos($this->sTargetDir, Registry::config()->getAppPath()) === false) {
            $this->sTargetDir = self::getDefaultPath();
            Registry::logger()->debug('Missing template dir, get default', array('sTargetDir' => $this->sTargetDir));
        }

        // Set template
        if (!is_null($sTemplate)) {
            $this->changeTemplate($sTemplate);
        }
    }

    /**
     * Get response object for a meta modification
     *
     * @param \Engine\Response $response
     * @return \Engine\Response
     */
    public function link(\Engine\Response $response = null)
    {
        if ($response) {
            $this->response = $response;
        }
        return $this->response;
    }

    public function getTemplate()
    {
        return $this->sTemplate;
    }

    public function changeTemplate($sTemplate)
    {
        $this->sTemplate = str_replace('\\', '/', $sTemplate);
    }

    public static function getDefaultPath()
    {
        return realpath(Registry::config()->getAppPath() . '/../Views');
    }

    public function add($aParams)
    {
        $this->aData = array_merge($this->aData, $aParams);
        return $this;
    }

    public function set($sName, $mValue)
    {
        $this->aData[$sName] = $mValue;
        return $this;
    }

    /**
     * Get all values
     *
     * @return array
     */
    public function getAll()
    {
        return $this->aData;
    }

    /**
     * Get value from registered scope
     *
     * @param string $sName
     * @param mixed $mDefault
     * @return mixed
     */
    public function get($sName, $mDefault = null)
    {
        return $this->oAgg->getValue($this->aData, $sName, $mDefault);
    }

    /**
     * Find value by key in array
     *
     * @param array $aData
     * @param string $sName
     * @param mixed $mDefault
     * @return mixed
     */
    public function take(array $aData, $sName, $mDefault = null)
    {
        return $this->oAgg->getValue($aData, $sName, $mDefault);
    }

    /**
     * Get compiled URL
     *
     * @param string $sPath
     * @param string $sLanguage
     * @return type
     */
    public function getUrl($sPath, $sExtension = null, $sLanguage = null)
    {
        if (is_null($sLanguage)) {
            $sLanguage = Registry::translation()->getTargetLanguage();
            if ($this->get('language')) {
                $sLanguage = $this->get('language');
            }
        }
        if (is_null($sExtension)) {
            $sExtension = \Defines\Extension::HTML;
        }

        // Check extension
        $tmp = explode('.', $sPath);
        $key = end($tmp);
        if ($sExtension && in_array($key, \Defines\Extension::getList(true))) {
            $sPath = substr($sPath, 0, - strlen($key) - 1);
        }
        // Add language
        $path = ltrim($sPath, '/');
        $langCheck = explode('/', $path);
        if (!strpos($sPath, '://') && trim($sLanguage)) {
            if (in_array(current($langCheck), \Defines\Language::getList(), true)) {
                array_shift($langCheck);
            }
            $sPath = \System\Registry::config()->getUrl($sLanguage) . '/' . implode('/', $langCheck);
        }

        return str_replace(['&#39;', '//', ':/'], ["'", '/', '://'], $sPath . ($sExtension ? '.' . $sExtension : ''));
    }

    public function partial($sTemplate, array $aParams = null, $targetDir = null)
    {
        if (is_null($aParams)) {
            $aParams = $this->aData;
        }
        if (is_null($targetDir)) {
            $targetDir = $this->sTargetDir;
        }
        $oView = new self($sTemplate, $targetDir);
        $oView->link($this->link());
        $oView->add($aParams);
        return $oView->compile(false);
    }

    public function regFunction($sName, $fFunc)
    {
        $this->aFunc[$sName] = $fFunc;
    }

    public function evalFunction($sName, $aArg)
    {
        if (isset($this->aFunc[$sName])) {
            return call_user_func_array($this->aFunc[$sName], $aArg);
        }
    }

    public function compile($compile = true)
    {
        ob_start();
        $sDefPath = self::getDefaultPath();
        if (file_exists("{$this->sTargetDir}/{$this->sTemplate}.php")) {
            require "{$this->sTargetDir}/{$this->sTemplate}.php";
        } elseif (file_exists("{$sDefPath}/{$this->sTemplate}.php")) {
            require "{$sDefPath}/{$this->sTemplate}.php";
        } else {
            $sError = "Missing template '{$this->sTemplate}'";
            echo "{{ $sError }}";
            \System\Registry::logger()->emergency($sError, array(
                'path1' => "{$this->sTargetDir}/{$this->sTemplate}.php",
                'path2' => "{$sDefPath}/{$this->sTemplate}.php"
            ));
        }
        $sResult = ob_get_contents();
        ob_end_clean();
        if ($compile) {
            $sResult = (new Helper\Description)->compile($sResult);
        }
        return $sResult;
    }

    public function __toString()
    {
        return $this->compile(false);
    }
}
