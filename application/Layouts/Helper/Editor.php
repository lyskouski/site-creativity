<?php namespace Layouts\Helper;

use Engine\Response\Meta;
use Engine\Response\Template;

/**
 * Editor layout
 *
 * @since 2017-04-26
 * @author Viachaslau Lyskouski
 */
class Editor extends Person
{

    /**
     * Update editor target path for templates
     *
     * @param \Engine\Request\Params $oParams
     * @param \Engine\Response $oResponse
     */
    public function __construct(\Engine\Request\Params $oParams, \Engine\Response $oResponse)
    {
        $this->init = 'Editor';
        parent::__construct($oParams, $oResponse);

        $editor = new Meta\Style(Meta\Style::TYPE_STYLESHEET);
        $this->response->meta($editor->setSrc('editor.css'));

        $font = new Meta\Style(Meta\Style::TYPE_STYLESHEET);
        $this->response->meta($font->setSrc('lib/font-awesome/font-awesome.css'));
    }

    public function updateHeader(array $params = [])
    {
        if ($this->params->getRequestMethod() === \Defines\RequestMethod::GET) {
            $this->response->clear();
            $this->add(
                $this->init . '/header',
                array_merge(array(
                    'language' => $this->params->getLanguage(),
                    'ext' => $this->params->getResponseType(),
                    'buttons' => $this->getTopButtons(),
                    'menu' => $this->getTopNatigation()
                ), $params),
                Template::getDefaultPath()
            );
        }
    }
}
