<?php namespace Modules\Mind\Article;

use System\Converter\Massive;
use Defines\Database\Params;
use Engine\Validate\Helper\Quote;

/**
 * Ouvre controller
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\Oeuvre\Prose\Controller
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('search', ['ctype' => 'string'])
                    ->bindKey('ui-sort')
                    ->bindKey('ui-sort-type')
                    ->bindKey('ui-split')
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML);
        // Pagination
        if ($this->action === 'searchAction' && sizeof($this->params) && strcmp((int) $this->params[0], $this->params[0]) === 0) {
            $this->action = 'indexNumAction';
        }
        // Search
        if ($this->action === 'searchAction') {
            $aFilterList = (new Massive)->getCategories(\Defines\Catalog::getMind());
            $oAccess->bindKey('/0', array('list' => (new Quote)->convert($aFilterList)))
                ->bindKey('/1', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
            (new \Access\Request\Search)->updateAccess($oAccess);
        // Topic
        } elseif (isset($this->params[0]) && $this->params[0][0] === 'i') {
            if ($this->action !== 'commentAction') {
                $this->action = 'readAction';
            }
            $oAccess->bindKey('/0', array('pattern' => '/^i\d{1,}$/'))
                    ->bindKey('/1', array('ctype' => 'integer'))
                    ->bindKey('/2', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('inf', array('list' => ['1', 1]));
        // Pagination
        } else {
            $oAccess->bindKey('/0', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        }
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Init entity for displaying content
     */
    protected function initEntity()
    {
        if ($this->action === 'readAction') {
            $this->oEntity = new \Layouts\Entity\Prose($this->request, $this->response);
        }
    }

    /**
     * Current page
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexAction(array $aParams)
    {
        // for a link highlighting
        $this->request->setParams(array('mind'));
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oModel = new Model();

        $iPage = (int) current($aParams);
        $oStat = $oModel->getStatistics();

        $iNum = 0;
        if (!$oStat->getContentCount() instanceof \System\ArrayUndef) {
            $iNum = Params::getPageCount($oStat, $oModel->getPageCount());
        }

        $oHelper->add('index', array(
            'stat' => $oStat,
            'num' => $iNum,
            'curr' => $iPage,
            'url' => 'mind',
            'list' => $oModel->getPublications($iPage)
        ));
        return $oHelper;
    }

        /**
     * Main page for Prose publication
     * @param array $aParams
     * @return \Layouts\Entity\Prose
     */
    public function readAction(array $aParams)
    {
        // Check comments
        if ($this->input->getGet('/1', null, FILTER_VALIDATE_INT) === 0) {
            $oResult = $this->getComments($aParams);

        // Open requested page
        } else {
            $oResult = $this->getPage($aParams);
        }
        return $oResult;
    }

    /**
     * List pagination
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        return $this->indexAction($aParams);
    }

    /**
     * Show topics by using keywords values
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function searchAction(array $aParams)
    {
        $oModel = new Model();
        // for a link highlighting
        $this->request->setParams(array('mind'));
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oTranslate = \System\Registry::translation();

        $defTitle = $oTranslate->sys('LB_MIND');

        $searchParam = $this->input->getPost('search', $this->input->getGet('/0', ''));
        $iPage = $this->input->getGet('/1', 0, FILTER_VALIDATE_INT);
        if ((string)(int)$searchParam === $searchParam) {
            $iPage = $searchParam;
            $searchParam = '';
        }

        if ($searchParam) {
            $aList = $oModel->getPublicationsByPart($searchParam, $iPage);
        } else {
            $aList = $oModel->getPublicationsByKey($defTitle, $iPage);
        }
        $oHelper->add('index', array(
            'stat' => null,
            'num' => Params::getPageCount($aList['count'], $oModel->getPageCount()),
            'curr' => $iPage,
            'title' => $searchParam ? $searchParam : $defTitle,
            'search' => $searchParam,
            'desc' => $oTranslate->get(['description', $this->input->getUrl(null)]),
            'url' => $this->input->getUrl(null),
            'list' => $aList
        ));
        return $oHelper;
    }
}
