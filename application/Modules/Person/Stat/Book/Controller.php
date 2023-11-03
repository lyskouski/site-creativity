<?php namespace Modules\Person\Stat\Book;

use Modules\Person\Stat\Model;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Person/Stat/Book
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('id', array('ctype' => 'integer'))
                    ->bindKey('date', (new \Access\Filter\Pattern)->date()->get())
                    ->bindKey('mark', array('ctype' => 'integer', 'min' => 0, 'max' => \Defines\Database\Params::MAX_USER_RATING))
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexNumAction(array $aParams)
    {
        return $this->indexAction($aParams);
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function indexAction(array $aParams)
    {
        $model = new Model();
        $page = (int) current($aParams);
        $limit = 50;
        list($list, $count) = $model->getBookRead($page, $limit);

        $this->request->setParams(array('stat'));
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);
        $oHelper->add('index', array(
            'menu' => $model->getMenu(),
            'active' => 'person/stat/book',
            'list' => $list,
            'num' => \Defines\Database\Params::getPageCount($count, $limit),
            'curr' => $page,
            'url' => 'person/stat/book'
        ));
        return $oHelper;
    }


    /**
     * Update date for the finished book
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     * @throws \Error\Validation
     */
    public function dateAction(array $aParams)
    {
        $id = $this->input->getPost('id');

        $em = \System\Registry::connection();
        /* @var $br \Data\Doctrine\Main\BookRead */
        $br = (new Model)->getBookReadById($id);
        $br->setUpdatedAt(new \DateTime($this->input->getPost('date')));
        $em->persist($br);
        $em->flush();

        return $this->indexAction($aParams);
    }

    public function markAction(array $aParams)
    {
        $id = $this->input->getPost('id');

        $oTranslate = \System\Registry::translation();
        $em = \System\Registry::connection();

        $book = $em->find(\Defines\Database\CrMain::CONTENT, $id);
        if (!$book) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_404'),
                \Defines\Response\Code::E_CONFLICT
            );
        }
        $mark = $oTranslate->entity(['mark-' . \System\Registry::user()->getEntity()->getId(), $book->getPattern()], $book->getLanguage());
        $mark->setContent($this->input->getPost('mark', 0, FILTER_VALIDATE_INT));
        $em->persist($mark);
        $em->flush();

        return $this->indexAction($aParams);
    }
}
