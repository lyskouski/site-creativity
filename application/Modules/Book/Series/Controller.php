<?php namespace Modules\Book\Series;

use Defines\Database\Params;

/**
 * Book overview controller
 * @see \Modules\AbstractController
 *
 * @since 2016-03-30
 * @author Viachaslau Lyskouski
 * @package Modules/Book/Overview
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('list' => ['search']))
                    ->bindKey('search', array('sanitize' => FILTER_SANITIZE_STRING))
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML);

        if ($this->action === 'searchAction') {
            $oAccess->bindKey('/0', array('sanitize' => FILTER_SANITIZE_STRING))
                ->bindKey('/1', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
            (new \Access\Request\Search)->updateAccess($oAccess);

        } else {
            $oAccess->bindKey('/0', array('pattern' => '/^(i|\d)\d{0,}$/'))
                    ->bindKey('/1', array('ctype' => 'integer'))
                    ->bindKey('/2', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        $curr = current($aParams);
        // Open comments
        if ($this->input->getGet('/1', null, FILTER_VALIDATE_INT) === 0) {
            $oHelper = $this->commentAction($aParams);

        // Show the book series
        } elseif ($curr[0] === 'i') {
            $model = new Model;

            $series = $model->getSeries(substr($curr, 1));
            $seriesList = $model->getSeriesContent($series);

            $oHelper = new \Layouts\Helper\Book($this->request, $this->response);
            $oHelper->add('Entity/Book/Series/page', array(
                'entity' => $series,
                'list' => $seriesList,
                'read_list' => (new \Modules\Book\Calendar\Model)->getAllList(false),
                'read' => $model->checkSeriesRead($seriesList)
            ));

        // List of book series
        } else {
            $oHelper = $this->indexNumAction([$curr]);
        }

        return $oHelper;
    }

    /**
     * List pagination
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        return $this->searchAction($aParams);
    }

    /**
     * Show topics by using keywords values
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function searchAction(array $aParams)
    {
        $model = new Model();

        $oHelper = new \Layouts\Helper\Book($this->request, $this->response);

        $oTranslate = \System\Registry::translation();

        $sSearch = $this->input->getGet('/0', '');
        $iPage = $this->input->getGet('/1', 0, FILTER_VALIDATE_INT);
        if ((string)(int)$sSearch === $sSearch) {
            $iPage = $sSearch;
            $sSearch = '';
        }

        $searchParam = $this->input->getPost('search', $sSearch);
        $aList = $model->getPublicationsByPart($searchParam, $iPage);

        if (!$aList['count']) {
            throw new \Error\Validation(
                $oTranslate->sys('LB_HEADER_404'),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }

        $oHelper->add('index', array(
            'stat' => null,
            'num' => Params::getPageCount($aList['count'], $model->getPageCount()),
            'curr' => $iPage,
            'title' => $searchParam ? $searchParam : $oTranslate->sys('LB_OEUVRE_BOOK_SERIES'),
            'desc' => $oTranslate->get(['description', $this->input->getUrl(null)]),
            'url' => $searchParam ? 'book/series/search/' . $searchParam : 'book/series',
            'list' => $aList,
            'search' => $searchParam,
            'language_list' => $searchParam ? null : \Defines\Language::getList()
        ));
        return $oHelper;
    }
}
