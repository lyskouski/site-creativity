<?php namespace Layouts\Entity;

use Engine\Request\Params;
use Engine\Response\Template;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Mind extends \Layouts\Helper\Basic
{

    /**
     * Init layout helper-decorator
     * @note override header/footer path
     *
     * @param \Engine\Request\Params $oParams
     */
    public function __construct(Params $oParams, \Engine\Response $oResponse)
    {
        $this->init = 'Entity/Mind';
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
                    'menu' => $this->getTopNatigation(),
                    'rating' => $this->params->getParam('rating')
                ),
                Template::getDefaultPath()
            );
        }
    }

    public function getTopNatigation()
    {
        return [];
    }

    public function getPageTemplate()
    {
        return 'Entity/Mind/page';
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
                'Basic/footer',
                array(
                    'language' => $this->params->getLanguage()
                ),
                Template::getDefaultPath()
            );
        }
        return $this;
    }
}
