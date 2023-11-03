<?php namespace Modules\Mind\Trainer\Accordion;

/**
 * Mind Trainer (controller): Accordion
 *
 * @since 2019-10-23
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\Mind\Trainer\AbstractController
{
    public function getModel()
    {
        return new Model();
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $oHelper = new \Layouts\Entity\Mind($this->request, $this->response);
        $oHelper->add('index', [], $this->getTmplPath());
        return $oHelper;
    }
}
