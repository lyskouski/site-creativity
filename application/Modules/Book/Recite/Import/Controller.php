<?php namespace Modules\Book\Recite\Import;

/**
 * Book overview import controller
 *
 * @since 2016-12-19
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

    public function indexAction(array $aParams)
    {
        $this->request->setParams(array('recite'));
        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);

        return $oHelper->add('index', array_merge(array(
            'menu' => (new \Modules\Book\Recite\Model)->getMenu(),
            'active' => 'book/recite/import'
        ), $aParams));
    }

    public function changeAction(array $aParams)
    {
        // @todo check that forward is to internal pages
        /* @var $entity \Data\Doctrine\Main\Content */
        $entity = \System\Registry::connection()->find(\Defines\Database\CrMain::CONTENT, $this->input->getPost('id'));
        if (
                !\System\Registry::user()->isAdmin()
                && ($entity->getType() !== 'quote' || $entity->getAuthor() !== \System\Registry::user()->getEntity())
        ) {
            $code = \Defines\Response\Code::E_BAD_REQUEST;
            throw new \Error\Validation(\Defines\Response\Code::getHeader($code), $code);
        }
        $content = new \System\Converter\Content($this->input->getPost('quote'));
        $entity->setAccess(\Defines\User\Access::getAccessNew())
            ->setAuditor(null)
            ->setUpdatedAt(new \DateTime)
            ->setContent($content->getHtml());

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl($this->input->getPost('forward'));
        return $oHelper;
    }

    public function manualAction(array $aParams)
    {
        $aParams['isbn'] = (new \Access\Request\Params)->getIsbn(null, true);
        $id = (new Model)->addCite(
            $aParams['isbn'],
            $this->input->getPost('quote'),
            $this->input->getPost('page')
        );
        $aParams['message'] = sprintf(
            \System\Registry::translation()->sys('LB_RECITE_ADDED'), '#' . $id
        );

        return $this->indexAction($aParams);
    }

    public function pocketbookAction(array $aParams)
    {
        $isbn = (new \Access\Request\Params)->getIsbn(null, true);
        $type = $this->input->getPost('type');

        $file = new \Engine\Request\Input\File('content');
        if ($file->getType() !== 'text/html') {
            throw new \Error\Validation(sprintf('Expected `%s`-filetype, taken `%s`', 'text/html', $file->getType()));
        }
        $fileContent = $file->getContent();
        $model = new Model();
        // Update content
        if (in_array($type, ['all', 'content'])) {
            $model->addNav($isbn, $model->parseNav($fileContent));
        }
        // Update notes
        $list = array();
        if (in_array($type, ['all', 'quote'])) {
            $tmp = $model->parseCite($fileContent, Model::ID_CITE);
            $list = $model->parseCite($fileContent, Model::ID_COMMENT, $tmp);
        }

        foreach ($list as $page => $cite) {
            $model->addCite($isbn, $cite, $page);
        }

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oTemp = new \Engine\Response\Template();
        $oHelper->setUrl($oTemp->getUrl(
            $model->getBook($isbn)->getContent()->getPattern() . '/quote',
            \Defines\Extension::HTML,
            $model->getBook($isbn)->getContent()->getLanguage()
        ));
        return $oHelper;
    }
}
