<?php namespace Modules\Book\Recite;

/**
 * Book overview controller
 * @see \Modules\AbstractController
 *
 * @since 2016-03-30
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $access = new Permission($this->action);
        return $access->validate(
            $this->request->getRequestMethod(),
            $this->request->getResponseType()
        );
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $this->request->setParams(array('recite'));
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);

        $model = new Model();

        $oHelper->add('index', array(
            'menu' => $model->getMenu(),
            'active' => 'book/recite',
            'list' => $model->getRandom()
        ));
        return $oHelper;
    }

    /**
     * Show the random list of citations
     *
     * @param array $aParams
     */
    public function mineAction(array $aParams)
    {
        $this->request->setParams(array('recite'));
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);

        $model = new Model();

        $oHelper->add('mine', array(
            'menu' => $model->getMenu(),
            'active' => 'book/recite/mine'
        ));
        return $oHelper;
    }

}
