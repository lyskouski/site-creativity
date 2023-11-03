<?php namespace Modules\Dev\Tasks\Auditor;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Tasks
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindNullKey()
                ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('id', array('ctype' => 'integer'))
                    ->bindKey('content')
                    ->bindKey('action', array('list' => ['delete','restart', 'update']))
                    ->bindKey('pattern', array('sanitize' => FILTER_SANITIZE_STRING));

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Show summary information
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function indexAction(array $aParams)
    {
        $oModel = new Text\Model();
        // for a link highlighting
        $this->request->setParams(array('tasks'));
        $oHelper = new \Layouts\Helper\Dev($this->request, $this->response);

        $oHelper->add('index', array(
            'menu' => $oModel->getNavigation(),
            'active' => 'dev/tasks#!/auditor',
            'text_status' => $oModel->getSumTasks()
        ));
        return $oHelper;
    }

    /**
     * Reinit translation agency
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function restartAction(array $aParams)
    {
        if (!\System\Registry::user()->isAdmin()) {
            throw new \Error\Validation('Forbidden method');
        }

        $em = \System\Registry::connection();

        $entity = $em->getRepository(\Defines\Database\CrMain::CONTENT)->findOneBy(array(
            'pattern' => $this->input->getPost('pattern'),
            'language' => \System\Registry::translation()->getTargetLanguage(),
            'type' => 'og:title'
        ));

        if (!$entity) {
            throw new \Error\Validation('Restart cannot be done');
        }

        if (strpos($this->input->getPost('pattern'), 'book/overview/i') !== false) {
            $model = new \Modules\Dev\Tasks\Translation\Book\Model();
            $url = "/dev/tasks/translation/book";
        } else {
            $model = new \Modules\Dev\Tasks\Translation\Text\Model();
            $url = "/dev/tasks/translation/text";
        }
        // Find required fields
        foreach ($model->findTask($entity) as $oContent) {
            $oContent->setAuthor(\System\Registry::user()->getEntity());
            $em->persist($oContent);
            $oNewContent = $model->persistNewTask($oContent);
            $em->persist($oNewContent);
        }
        $em->flush();

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl($url));
        return $oHelper;
    }

    /**
     * Delete informational block
     *
     * @param array $aParams
     * @return \Layouts\Helper\Dev
     */
    public function deleteAction(array $aParams)
    {
        if (!\System\Registry::user()->isAdmin()) {
            throw new \Error\Validation('Forbidden method');
        }

        $pattern = $this->input->getPost('pattern');
        $em = \System\Registry::connection();
        $list = $em->getRepository(\Defines\Database\CrMain::CONTENT)->findByPattern($pattern);
        /* @var $o \Data\Doctrine\Main\Content */
        foreach ($list as $o) {
            // add to a history table in case
            $o->setAccess(\Defines\User\Access::getModDecline());
            $em->persist($o);
            $em->flush();
            // Remove book
            $book = $em->getRepository(\Defines\Database\CrMain::BOOK)->findOneByContent($o);
            if ($book) {
                $bookList = $em->getRepository(\Defines\Database\CrMain::BOOK_READ)->findByBook($book);
                foreach ($bookList as $list) {
                    $em->remove($list);
                }
                $em->remove($book);
            }
            // Remove element
            $em->remove($o);
            $em->flush();
        }

        return $this->indexAction($aParams);
    }

    /**
     * Update content without any intermediate step
     *
     * @param array $aParams
     * @return \Layouts\Helper\Zero
     */
    public function updateAction(array $aParams)
    {
        $id = $this->input->getPost('id');
        $em = \System\Registry::connection();
        /* @var $entity \Data\Doctrine\Main\Content */
        $entity = $em->find(\Defines\Database\CrMain::CONTENT, $id);

        $content = new \System\Converter\Content($this->input->getPost('content'), ['div', 'span', 'a']);
        $entity->setContent($content->getHtml())
            ->setSearch($content->getText())
            ->setAuditor(\System\Registry::user()->getEntity())
            ->setUpdatedAt(new \DateTime)
            ->setAccess(\Defines\User\Access::getModApprove());
        if (!$entity->getAuthor()) {
            $entity->setAuthor(\System\Registry::user()->getEntity());
        }
        $em->persist($entity);
        $em->flush();

        // return new \Layouts\Helper\Zero($this->request, $this->response);
        throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_202'), \Defines\Response\Code::E_OK);
    }
}
