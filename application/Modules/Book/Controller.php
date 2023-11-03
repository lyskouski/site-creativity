<?php namespace Modules\Book;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        // To avoid autocreate content
        \System\Registry::translation()->skipUpdate();

        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindNullKey();
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexAction(array $aParams)
    {
        /* @var $rep \Data\Model\ContentRepository */
        $rep = (new \Data\ContentHelper)->getRepository();
        $aParams['book_top'] = $rep->findLastTopics('book/overview', 0, 2, false);
        $aParams['book_list'] = $rep->findLastTopics('book/overview', 2, 8);

        $aParams['series_list'] = $rep->findLastTopics('book/series', 0, 7, false);

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
        $oHelper->add('index', $aParams);
        return $oHelper;
    }

}
