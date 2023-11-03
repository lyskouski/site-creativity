<?php namespace Modules\Dev\Proposition;


/**
 * Proposition forum controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Proposition
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
        $oResult = $this->getSection($aParams, 'proposition');

        if ($this->request->getRequestMethod() === \Defines\RequestMethod::GET) {
            $oResult->addPos(2, 'counts', (new Model)->getWorkflow());

            // @todo take fixed from the database
            $oResult->addPos(2, 'fixed', [
                'list' => ['dev/proposition/i20165']
            ]);
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
        return $this->getSection($aParams, 'proposition');
    }

    public function topicAction(array $aParams)
    {
        $oResult = parent::topicAction($aParams);

        if ($this->request->getRequestMethod() === \Defines\RequestMethod::GET) {
            $url = $this->input->getUrl(null);
            $oResult->addPos(2, 'subtask', array(
                'list' => (new Model)->getSubtaskList($url),
                'url' => $url
            ));
        }

        return $oResult;
    }

    public function subtaskAction(array $aParams)
    {
        $model = new Model();

        $model->addSubtask(
            $this->input->getUrl(null),
            $this->input->getPost('title'),
            $this->input->getPost('description')
        );

        $url = $this->input->getUrl(null);

        $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
        $oResult->add('subtask_add', array(
            'list' => $model->getSubtaskList($url),
            'url' => $url
        ));
        return $oResult;
    }

}
