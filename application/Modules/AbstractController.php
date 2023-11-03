<?php namespace Modules;

use Engine\Response;
use Engine\Response\Helper;
use Engine\Response\Meta;
use System\Aggregator;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules
 */
abstract class AbstractController
{

    /**
     * @var \Engine\Request\Input
     */
    protected $input;

    /**
     * @var \Engine\Request\Params
     */
    protected $request;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var \Engine\Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $action = 'indexAction';

    protected $commentTpl = 'Entity/comments';

    /**
     * Controller initialization
     * @param \Engine\Request\Params $oParams
     * @param boolean $bStopInit
     * @return null
     */
    public function __construct(\Engine\Request\Params $oParams, $bStopInit = false)
    {
        $this->request = $oParams;
        $this->params = $oParams->getParams();
        $this->input = new \Engine\Request\Input();

        $oAgg = new Aggregator();
        // to avoid a wrong way of validation
        $aAbstractList = $oAgg->func2array(__CLASS__, 'init');
        $aList = array_unique(array_merge($aAbstractList, $oAgg->func2array($this, 'init')));
        // Stop autoinitial common part: avoid methods from AbstractController
        if ($bStopInit) {
            $aList = array_diff($aList, $aAbstractList);
            // array_unshift($aList, 'initPermission');
            array_unshift($aList, 'initResponse');
        }
        // Preinit execution
        foreach ($aList as $sMethod) {
            $this->{$sMethod}();
        }
        // Trigger action and get response
        $bStopInit || $this->run();
    }

    protected function getTmplPath()
    {
        return \System\Registry::config()->getAppPath()
            . str_replace(['\\', 'Modules/', '/Controller'], '/', get_class($this))
            . \Engine\Response\Template::VIEW_FOLDER;
    }

    /**
     * Check AMP existance
     * @return boolean - default `true`
     */
    public function hasAmp()
    {
        return true;
    }

    /**
     * Check RSS existance
     * @return boolean - default `false`
     */
    public function hasRss()
    {
        return false;
    }

    /**
     * Forward to another controller and action
     *
     * @param string $sClass - part of class' name '\Modules\{$sClass}\Controller'
     * @param string $sAction - action name 'index'
     * @param array $aParams - input parameters (as from GET array in a normal way)
     * @return \Layouts\Helper\Basic
     */
    public function forward($sClass, $sAction = 'index', array $aParams = array())
    {
        $prefix = \System\Registry::config()->getModulePrefix();
        $sName = "{$prefix}\\$sClass\\Controller";
        return (new $sName($this->request, true))->{"{$sAction}Action"}($aParams);
    }

    /**
     * Index page
     * @sample content:
     *     return $this->getSection( $aParams, 'index' );
     *
     * @param array $aParams
     */
    abstract public function indexAction(array $aParams);

    /**
     * Numerical options on the page
     *
     * @param array $aParams
     * @throws \Error\Validation
     */
    public function indexNumAction(array $aParams)
    {
        throw new \Error\Validation('Not applicable for this page');
    }

    /**
     * Add comment in topic
     * @todo pagination
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function commentAction(array $aParams)
    {
        $sUrl = $this->input->getUrl(null);
        $iPage = $this->input->getPost('page', 0, FILTER_VALIDATE_INT);

        $rep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT);

        if ($this->input->getPost('content')) {
            if (!\System\Registry::user()->isLogged()) {
                throw new \Error\Validation(
                    \System\Registry::translation()->sys('LB_ERROR_NOT_AUTHORIZED'),
                    \Defines\Response\Code::E_UNAUTHORIZED
                );
            }
            $rep->addComment(
                $sUrl,
                $this->input->getPost('content', '', FILTER_SANITIZE_STRING),
                $this->input->getPost('mark', '', FILTER_SANITIZE_STRING)
            );
        }

        $oReturn = new \Layouts\Helper\Basic($this->request, $this->response);

        $this->response->meta(new \Engine\Response\Meta\Script('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js'));
        $this->response->meta(new \Engine\Response\Meta\Script('//yastatic.net/share2/share.js'));

        return $oReturn->add($this->commentTpl, array(
            'list' => $rep->findComments($sUrl, $iPage),
            'url' => $sUrl,
            'page' => ++$iPage
        ));
    }

    /**
     * Common behaviour for each forum section
     *
     * @param array $aParams
     * @param string $sType
     * @return \Layouts\Helper\Basic
     * @throws \Error\Validation
     */
    protected function getSection($aParams, $sType, $iCount = 0)
    {
        $path = trim(str_replace('\\', '/', \System\Registry::config()->getModulePrefix()), '/');
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        $oHelper->add(
            $sType, $aParams, __DIR__
            . str_replace(
                array('\\', $path, 'Controller'),
                array(DIRECTORY_SEPARATOR),
                get_class($this)
            )
            . \Engine\Response\Template::VIEW_FOLDER
        );
        return $oHelper;
    }

    protected function initMethod()
    {
        $actionParams = (new Aggregator)->getValue($this->params, 0, '');
        $action = $this->input->getPost('action', $actionParams, FILTER_SANITIZE_STRING);

        $this->action = 'indexAction';
        // Set custom action
        if (method_exists($this, $action . 'Action')) {
            $this->action = $action . 'Action';
            if ($action === $actionParams) {
                $this->params = array_slice($this->params, 1);
            }
        }
        // Set pagination action
        elseif (strcmp((int) $actionParams, $actionParams) === 0) {
            $this->params[0] = (int) $this->params[0];
            $this->action = 'indexNumAction';
        }
        // Add other params
        foreach ($this->params as $sName => $mValue) {
            if (is_int($sName)) {
                $this->input->setGet("/{$sName}", $mValue);
            } else {
                $this->input->setGet($sName, $mValue);
            }
        }
    }

    /**
     * Init model if exists
     *
     * @public - will be applied for all inherited classes
     */
    protected function getDefaultModel()
    {
        $sModelName = str_replace('Controller', 'Model', get_class($this));
        \System\Registry::logger()->debug("+ model class: $sModelName");
        return new $sModelName();
    }

    /**
     * Check that actual URL is used
     */
    protected function initActualUrl()
    {
        $tmpl = new Response\Template();

        $url = $this->input->getUrl(null);
        $initUrl = \System\Registry::config()->getUrl();
        $htmlUrl = $tmpl->getUrl($url, \Defines\Extension::HTML);
        // $lang = strtolower(substr($this->input->getServer('HTTP_ACCEPT_LANGUAGE'), 0, 2));

        // Robot page
        if ($this->request->getResponseType() === \Defines\Extension::TXT && $this->action === 'robotsAction') {
            // ignore any redirect check

        // Index page
        } elseif (
                in_array($this->input->getServer('REQUEST_URI'), ['','/'], true)
                && $this->input->getServer('HTTP_HOST') === 'creativity.by'
        ) {
            (new \Deprecated\Migration)->redirect(\System\Registry::config()->getUrl('ru'));//, '302 Moved Temporarily');
            // Language identification
            //if (in_array($lang, \Defines\Language::getList(), true)) {
            //    (new \Deprecated\Migration)->redirect(\System\Registry::config()->getUrl($lang));
            //}
            // Else show summary page
        // Validate adress (migration to https or another domain)
        } elseif (
                !\System\Registry::cron()
                && in_array($this->request->getResponseType(), [
                    \Defines\Extension::HTML,
                    \Defines\Extension::AMP,
                    \Defines\Extension::TXT
                ])
                && strpos($initUrl,  $this->input->getUrlProtocol() . '://' . $this->input->getServer('HTTP_HOST')) === false
        ) {
            (new \Deprecated\Migration)->redirect($htmlUrl);
        }
    }

    /**
     * Check response type, method availability and user access
     *
     * @public - will be applied for all inherited classes
     */
    protected function initPermission()
    {
        // Check user permission
        $bPermission = \System\Registry::user()->checkAccess($this->request->getModuleUrl(), $this->action);
        if (!$bPermission) {
            $oTranslate = \System\Registry::translation();
            $errorMssg = $oTranslate->sys('LB_ERROR_NOT_AUTHORIZED');
            if (\System\Registry::user()->isLogged()) {
                $errorMssg = $oTranslate->sys('LB_ERROR_PRIVILEGES_LIMITATION');
            }
            throw new \Error\Validation($errorMssg);
        }
    }

    /**
     * Init viewer
     * @public - will be applied for all inherited classes
     */
    protected function initResponse()
    {
        $oTranslate = \System\Registry::translation();

        $this->response = new Response();
        $type = $this->request->getResponseType();
        $this->response->setLayoutType($type);

        // Check Cache
        if ($type == \Defines\Extension::AMP) {
            new \System\Minify\AmpPage(true);
        }

        // Parameters
        $sSiteName = $oTranslate->sys('LB_SITE_TITLE');
        $this->response->title($sSiteName);

        $oUrlHelper = new Helper\Url($this->request);
        $sLink = $oUrlHelper->getUrl();

        if ($this->action === 'indexNumAction') {
            $sLink = str_replace(
                $this->request->getModuleUrl() . '/' . $this->params[0],
                $this->request->getModuleUrl(),
                $sLink
            );
        }

        $sLanguage = $this->request->getLanguage();

        // Font style
        // $fontStyle = new Meta\Style(Meta\Style::TYPE_STYLESHEET);
        // $this->response->meta($fontStyle->setSrc('https://fonts.googleapis.com/css?family=Forum'));

        //<meta http-equiv="refresh" content="300">
        // Project description
        $this->response->meta(new Meta\Ogp(Meta\Ogp::TYPE_SITE, $sSiteName), true);
        $this->response->meta(new Meta\Ogp(Meta\Ogp::TYPE_TYPE, 'website'), true);
        $this->response->meta(new Meta\Ogp(Meta\Ogp::TYPE_URL, $sLink), true);
        $this->response->meta(new Meta\Ogp('twitter:url', $sLink), true);

        $homeUrl = \System\Registry::config()->getUrl();
        $this->response->meta(new Meta\Ogp('homepage', $homeUrl), true);
        $this->response->meta(new Meta\Link('copyright', "$homeUrl/info.html"), true);

        // Other data
        $sBasicLink = $this->input->getUrl();

        $iPage = end($this->params);
        if ((int) $iPage === -1) {
            $this->response->title($oTranslate->sys('LB_SITE_UPDATES'));
        } elseif ((string) (int) $iPage === (string) $iPage) {
            $this->response->title($oTranslate->sys('LB_PAGE') . ' ' . $iPage);
        } elseif ($this->action === 'indexNumAction') {
            $this->response->title($iPage);
        }

        foreach ($oTranslate->desc($this->input->getUrl(null), $sLanguage, $sBasicLink) as $mMeta) {
            if (is_object($mMeta)) {
                $this->response->meta($mMeta, true);
            } elseif ($mMeta) {
                $pattern = \System\Registry::stat()->getContent();
                if ($pattern && strripos($pattern->getPattern(), '/')) {
                    $mMeta .= ' (' . $oTranslate->get(['og:title', substr($pattern->getPattern(), 0, strripos($pattern->getPattern(), '/'))]) . ')';
                }
                $this->response->title($mMeta);
                if ((string) $iPage === '0') {
                    $this->response->title($oTranslate->sys('LB_COMMENTS'), ': ');
                }
            }
        }
        $this->response->meta(new Meta\Meta(Meta\Meta::TYPE_AUTHOR, 'Viachaslau Lyskouski(FieryCat)'), true);
        $this->response->meta(new Meta\Meta('viewport', 'width=device-width,minimum-scale=1,initial-scale=1'), true);
        // Canonical main page
        $tmpl = new Response\Template();
        $url = $this->input->getUrl(null);
        $htmlUrl = $tmpl->getUrl($url, \Defines\Extension::HTML);
        $this->response->meta(new Meta\Link('canonical', $htmlUrl), true);
        // Accelerated Mobile Pages (AMP) Pages
        if ($this->hasAmp()) {
            $this->response->meta(new Meta\Link('amphtml', $tmpl->getUrl($url, \Defines\Extension::AMP)), true);
        }
        // Check RSS availability
        if ($this->hasRss()) {
            $this->response->meta(new Meta\Rss($sLanguage, $sLink));
        }
    }

    /**
     * User interface for authorization and permission verification
     * @note Has to be created for each Controller separately
     *
     * @throws \Error\Validation
     * @return \Access\Allowed
     */
    abstract protected function initAllowed();

    /**
     * Operate with data
     */
    final public function run()
    {
        \System\Registry::logger()->debug(get_class($this) . "->{$this->action}", $this->params);
        $oLayoutHelper = $this->{$this->action}($this->params);
        $oLayoutHelper->sendResponse();
    }
}
