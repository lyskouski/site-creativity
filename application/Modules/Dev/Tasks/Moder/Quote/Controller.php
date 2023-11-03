<?php namespace Modules\Dev\Tasks\Moder\Quote;

/**
 * Operate with book quotations
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\Dev\Tasks\Moder\Reply\Controller
{
    protected function initModel()
    {
        $this->model = new Model;
    }

    /**
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        if (!$aParams) {
            $aParams[0] = \System\Registry::translation()->getTargetLanguage();
        }

        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);
        return $oHelper->add('index', array(
            'language' => $aParams[0],
            'url' => 'dev/tasks/moder/quote',
            'list' => (new Model)->getList($aParams[0])
        ));
    }

    public function deleteAction(array $aParams)
    {
        $id = $this->input->getPost('id');
        $em = \System\Registry::connection();
        $entity = $em->find(\Defines\Database\CrMain::CONTENT, $id);
        if ($entity && strpos($entity->getType(), 'quote') === 0) {
            $em->remove($entity);
            $em->flush();
        } else {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_403'),
                \Defines\Response\Code::E_FORBIDDEN
            );
        }
        return $this->indexAction($aParams);
    }
}
