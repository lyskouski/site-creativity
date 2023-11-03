<?php namespace Modules\Dev\Bugs;

/**
 * Bugs forum controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Bugs
 */
class Controller extends \Modules\Dev\Controller
{

    /**
     * Index action - list of topics
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $oResult = $this->getSection($aParams, 'bugs');

        if ($this->request->getRequestMethod() === \Defines\RequestMethod::GET) {
            $oResult->addPos(2, '../../Proposition/zView/counts',
                (new \Modules\Dev\Proposition\Model)->getWorkflow()
            );
        }

        return $oResult;
    }

    /**
     * Pagination action
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        return $this->getSection($aParams, 'bugs');
    }

}
