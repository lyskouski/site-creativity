<?php namespace Engine\Request\Input;

use Engine\Request\Input;
use \Engine\Validate\Common;

/**
 * Server params
 * @sample (new \Engine\Request\Input\Server)->getRelativePath()
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Engine/Request/Input
 */
class Server
{

    const RELATIVE_PATH = '/index.html';

    /**
     * @var Input
     */
    protected $oInput;

    /**
     * connect Input object to operate with INPUT_SERVER
     */
    public function __construct()
    {
        $this->oInput = new Input;
    }

    /**
     * Check if server is in the development mode
     *
     * @return boolean
     */
    public function isDevelopment()
    {
        return in_array(
            $this->getEnv(),
            array(
                \Defines\ServerType::LOCAL,
                \Defines\ServerType::DEV,
                \Defines\ServerType::ALPHA,
                \Defines\ServerType::TEST
            )
        );
    }

    /**
     * Check if it's a LIVE server
     *
     * @return boolean
     */
    public function isLive()
    {
        return $this->getEnv() === \Defines\ServerType::LIVE;
    }

    /**
     * Check if it's PHPUnit tests are running
     *
     * @return boolean
     */
    public function isTest()
    {
        return $this->getEnv() === \Defines\ServerType::TEST;
    }

    /**
     * Check if it's an internal request via PDF Factory or Webservices
     *
     * @return boolean
     */
    public function isExternalRequest()
    {
        return $this->getBrowser() !== '';
    }

    /**
     * Get REQUEST_METHOD
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->oInput->getServer('REQUEST_METHOD', \Defines\RequestMethod::getDefault(), FILTER_SANITIZE_STRING);
    }

    /**
     * Check if REQUEST_METHOD is accepted by IAC
     *
     * @return boolean
     */
    public function isRequestAccepted()
    {
        return in_array(
            $this->getRequestMethod(),
            \Defines\RequestMethod::getList()
        );
    }

    /**
     * Get PORT if it's not a default (80)
     *
     * @return string
     */
    public function getHttpPort()
    {
        $iPort = $this->oInput->getServer('SERVER_PORT', '', FILTER_SANITIZE_NUMBER_INT);
        if (80 == $iPort) {
            $iPort = '';
        }
        return $iPort;
    }

    /**
     * Get HTTP host name
     *
     * @return string
     */
    public function getHttpHost()
    {
        return $this->oInput->getServer('HTTP_HOST', '', FILTER_SANITIZE_STRING);
    }

    /**
     * Get referer path
     *
     * @return string
     */
    public function getHttpReferer()
    {
        return $this->oInput->getServer('HTTP_REFERER', '', FILTER_SANITIZE_STRING);
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->oInput->getServer('DOCUMENT_ROOT', '', FILTER_SANITIZE_STRING);
    }

    /**
     * Get user browser identificator
     * @note: HTTP_USER_AGENT vary from 4KB to 64KB
     *
     * @return string
     */
    public function getBrowser()
    {
        $sBrowser = $this->oInput->getServer('HTTP_USER_AGENT', '', FILTER_SANITIZE_STRING);
        return mb_substr($sBrowser, 0, 255, 'UTF-8');
    }

    /**
     * Check if it's a user request
     *
     * @return boolean
     */
    public function isBot()
    {
        $bResult = false;
        if (
            !$this->getBrowser()
            || !preg_match('/(MSIE|Opera|Firefox|Chrome|Safari|Netscape|Edge)/i', $this->getBrowser())
        ) {
            $bResult = true;
        }
        return $bResult;
    }

    /**
     * Get APPLICATION_ENV name
     * @note apache2/vhosts.d -> setenv APPLICATION_ENV "{value}"
     * @note nginx/vhost.d -> fastcgi_param APPLICATION_ENV "{value}";
     *
     * @return string - default `live`
     */
    public function getEnv()
    {
        return $this->oInput->getServer('APPLICATION_ENV', 'live', FILTER_SANITIZE_STRING);
    }

    /**
     * Change APPLICATION_ENV name
     *
     * @note [!] do not use this functionality,
     *       additionally applicable ONLY for a database compatibility services
     *       and PHPUnit tests
     *
     * @param string $sValue
     * @return \Engine\Request\Input\Server
     */
    public function setEnv($sValue)
    {
        // validate and trigger error
        $sValidatedValue = ( new Common)->getEnvironment($sValue, true);
        $this->oInput->setServer('APPLICATION_ENV', $sValidatedValue);
        return $this;
    }

    /**
     * Get SITE name
     * @note /vhosts.d -> setenv SITE "{value}"
     *
     * @fixme has to be fixed by administrators
     *
     * @return string
     */
    public function getSite()
    {
        return $this->oInput->getServer('SITE', '', FILTER_SANITIZE_STRING);
    }

    /**
     * Get relative path
     * @note SCRIPT_NAME, PHP_SELF etc. not always returns the correct data
     * @link  http://stackoverflow.com/questions/279966
     *
     * @return string
     */
    public function getRelativePath()
    {
        $sPath = trim($this->oInput->getServer('REQUEST_URI', '', FILTER_DEFAULT));

        if (!$sPath) {
            $sPath = self::RELATIVE_PATH;
        }
        if (strpos($sPath, '?')) {
            $sPath = strtok($sPath, '?');
        }
        return filter_var(html_entity_decode(urldecode($sPath)), FILTER_SANITIZE_STRING);
    }

    /**
     * Get requested filename
     *
     * @return string
     */
    public function getFilename()
    {
        return basename($this->oInput->getServer('SCRIPT_FILENAME', '', FILTER_SANITIZE_STRING));
    }

    /**
     * Check user IP (also behind the proxy)
     *
     * @return string
     */
    public function getUserIp()
    {
        $sProxy = $this->oInput->getServer('HTTP_X_FORWARDED_FOR', '', FILTER_VALIDATE_IP);
        if ($sProxy) {
            $sUserIp = $sProxy;
        } else {
            $sUserIp = $this->oInput->getServer('REMOTE_ADDR', '', FILTER_VALIDATE_IP);
        }
        return $sUserIp;
    }

}
