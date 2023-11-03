<?php namespace Modules\Dev\Questions;

/**
 * Questions forum controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Questions
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
        return $this->getSection($aParams, 'questions');
    }

    /**
     * Pagination action
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        return $this->getSection($aParams, 'questions');
    }

}
