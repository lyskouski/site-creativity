<?php namespace Layouts\Helper;

use Engine\Response\Template;
use Engine\Request\Params;
use System\Registry;
use Engine\Response\Meta;
use Engine\Response\Helper\Url;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Basic
{

    protected $init = 'Basic';

    /**
     * @var \Engine\Request\Params
     */
    protected $params;

    /**
     * @var \Engine\Response
     */
    protected $response;

    /**
     * Init layout helper-decorator
     *
     * @param \Engine\Request\Params $oParams
     */
    public function __construct(Params $oParams, \Engine\Response $oResponse)
    {
        $this->params = $oParams;
        $this->response = $oResponse;

        $sLanguage = $oParams->getLanguage();

        if ($oParams->getRequestMethod() === \Defines\RequestMethod::GET) {
            $this->add(
                $this->init . '/header',
                array(
                    'language' => $sLanguage,
                    'ext' => $oParams->getResponseType(),
                    'buttons' => $this->getTopButtons(),
                    'menu' => $this->getTopNatigation()
                ),
                Template::getDefaultPath()
            );
        }
    }

    /**
     * Final prepare for layut template's list
     *
     * @return \Layouts\Helper\Basic
     */
    public function finalize()
    {
        if ($this->params->getRequestMethod() === \Defines\RequestMethod::GET) {
            $this->add(
                $this->init . '/footer',
                array(
                    'language' => $this->params->getLanguage()
                ),
                Template::getDefaultPath()
            );
        }
        return $this;
    }

    /**
     * Add template into the process list
     *
     * @param string $sTemplate
     * @param array $aParams
     * @param string $sDirPath
     * @return \Layouts\Helper\Basic
     */
    public function add($sTemplate, array $aParams = array(), $sDirPath = null)
    {
        $oTemplate = new Template($sTemplate, $sDirPath);
        $oTemplate->link($this->response);
        $oTemplate->add($aParams);
        $this->response->push($oTemplate);
        return $this;
    }

    /**
     * Add to a direct position of templates list
     *
     * @param integer $iPos
     * @param string $sTemplate
     * @param array $aParams
     * @param string $sDirPath
     * @return \Layouts\Helper\Basic
     */
    public function addPos($iPos, $sTemplate, array $aParams = array(), $sDirPath = null)
    {
        $oTemplate = new Template($sTemplate, $sDirPath);
        $oTemplate->link($this->response);
        $oTemplate->add($aParams);
        $this->response->push($oTemplate, $iPos);
        return $this;
    }

    /**
     * Get template for any purposes
     *
     * @param integer $iPos
     * @return \Engine\Response\Template
     */
    public function get($iPos)
    {
        $oTemplate = $this->response->getContent();
        return $oTemplate[$iPos];
    }

    protected function addMetaData()
    {
        // Content metadata
        $this->response->meta(new Meta\MetaContent($this->params->getResponseType()), true);
        $this->response->meta(new Meta\MetaCharset('UTF-8'), true);
        $this->response->meta(new Meta\MetaHttp(Meta\MetaHttp::TYPE_COMPATIBLE, 'IE=edge,chrome=1'), true);
        //<meta http-equiv="default-style" content="the document's preferred stylesheet">
        // Search-bot information
        $this->response->meta(new Meta\Meta(Meta\Meta::TYPE_ROBOTS, 'all'), true);
        $this->response->meta(new Meta\Meta(Meta\Meta::TYPE_DOCUMENT_STATE, 'dynamic'), true);
        $this->response->meta(new Meta\Meta(Meta\Meta::TYPE_REVISIT_AFTER, '7 days'), true);
    }

    protected function addJavaScripts()
    {
        // Skip for other requests
        if ($this->params->getRequestMethod() !== \Defines\RequestMethod::GET) {
            return;
        }
        $meta = new \System\Minify\MetaFiles();
        $lang = \System\Registry::translation()->getTargetLanguage();
        // Minified (concatenated) version
        if (\System\Registry::config()->getMinimize()) {
            $this->response->meta(new Meta\Style($meta->getDefault()), false);
            $this->response->meta(new Meta\Script($meta->getDefault()), false, true);
        // Full list of required files
        } else {
            // Add Styles into response
            foreach ($meta->getList($meta::TYPE_CSS) as $sPath) {
                $this->response->meta(new Meta\Style($sPath), true);
            }
            // Add Script into response
            foreach (array_reverse($meta->getList($meta::TYPE_JS)) as $sPath) {
                $this->response->meta(new Meta\Script($sPath), false, true);
            }
            // Add JS into response (right after translate model)
            $this->response->meta(new Meta\Script("classes/model/translate/$lang.js"), false, 'classes/model/translate.js');
        }
    }

    /**
     * Get all templates in 'Basic'-order
     *
     * @return array
     */
    public function sendResponse()
    {
        // Check scripts
        $this->addJavaScripts();
        // Add metadata
        $this->addMetaData();
        // Add footer if needed
        $this->finalize();
        // Publicate results
        $this->response->flush();
    }

    /**
     * Get auth and configuration buttons
     *
     * @return array
     */
    protected function getTopButtons()
    {
        $bAuth = Registry::user()->isLogged();

        $sLanguage = $this->params->getLanguage();
        $sType = $this->params->getResponseType();

        $oTemplate = new Template();
        if ($bAuth) {
            $aButtons = array(
                array(
                    'type' => 'attention',
                    'title' => Registry::translation()->sys('LB_LOGOUT'),
                    'href' => $oTemplate->getUrl('log/out', $sType, $sLanguage)
                ),
                array(
                    'type' => 'button',
                    'title' => Registry::translation()->sys('LB_PERSONAL'),
                    'href' => $oTemplate->getUrl('person/work', $sType, $sLanguage)
                ),
                array(
                    'type' => 'none',
                    'title' => Registry::user()->getName(),
                    'href' => $oTemplate->getUrl('person', $sType, $sLanguage)
                )
            );
        } else {
            $aButtons = array(
                array(
                    'type' => 'attention',
                    'title' => Registry::translation()->sys('LB_LOGIN'),
                    'href' => $oTemplate->getUrl('log/in', $sType, $sLanguage)
                )
            );
        }
        return $aButtons;
    }

    /**
     * Get TOP navigation
     *
     * @return array
     */
    public function getTopNatigation()
    {
        $sLanguage = $this->params->getLanguage();
        $sType = $this->params->getResponseType();

        $oTemplate = new Template();
        $aList = array(
            'oeuvre' => array(
                'title' => Registry::translation()->sys('LB_OEUVRE'),
                'href' => $oTemplate->getUrl('oeuvre', $sType, $sLanguage)
            ),
            'cognition' => array(
                'title' => Registry::translation()->sys('LB_COGNITION'),
                'href' => $oTemplate->getUrl('cognition', $sType, $sLanguage)
            ),
            'mind' => array(
                'title' => Registry::translation()->sys('LB_MIND'),
                'href' => $oTemplate->getUrl('mind', $sType, $sLanguage)
            ),
        );

        $sTargetAuth = current($this->params->getParams());
        if (isset($aList[$sTargetAuth])) {
            $aList[$sTargetAuth]['class'] = ' active';
        }

        return $aList;
    }

    /**
     * Get URL modification
     *
     * @param string $sChange
     * @return string
     */
    protected function updateUrl($sChange)
    {
        $sExtType = '.' . $this->params->getResponseType();

        $oUrlHelper = new Url($this->params);
        $sLink = $oUrlHelper->getUrl(null, false, false);

        return str_replace($sExtType, '', $sLink) . $sChange . $sExtType;
    }
}
