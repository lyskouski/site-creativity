<?php namespace Modules\Oeuvre\Poetry;

/**
 * Ouvre controller
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\Oeuvre\Prose\Controller
{

    /**
     * @see \Modules\Oeuvre\Prose\Controller::initEntity
     * @note override entity for displaying content
     */
    protected function initEntity() {
        $this->oEntity = new \Layouts\Entity\Poetry($this->request, $this->response);
    }

    public function indexAction(array $aParams)
    {
        // Forward to search if the pattern was not set
        if (!$aParams) {
            $sName = \System\Registry::translation()->sys('LB_CATEGORY_POETRY');
            $this->input->setGet('/0', $sName);
            $oResult = $this->forward('Oeuvre', 'search', array($sName, 0));
        } else {
            $oResult = parent::indexAction($aParams);
        }
        return $oResult;
    }
}
