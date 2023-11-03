<?php namespace Modules\Oeuvre;

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
class Controller extends \Modules\AbstractController
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

        if ($this->action === 'searchAction' && sizeof($this->params) && strcmp((int) $this->params[0], $this->params[0]) === 0) {
            $this->action = 'indexNumAction';
        }

        if ($this->action === 'searchAction') {
            $filterList = (new Massive)->getCategories(\Defines\Catalog::getOeuvre());
            $filterList[] = '';
            $oAccess->bindKey('/0', array('list' => (new Quote)->convert($filterList)))
                    ->bindKey('/1', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
            (new \Access\Request\Search)->updateAccess($oAccess);

        } else {
            $oAccess->bindKey('/0', array('ctype' => 'integer'))
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
        // for a link highlighting
        $this->request->setParams(array('oeuvre'));
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
            'url' => 'oeuvre',
            'list' => $oModel->getPublications($iPage)
        ));
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
        $this->request->setParams(array('oeuvre'));
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);

        $oTranslate = \System\Registry::translation();

        $searchParam = $this->input->getPost('search', '');
        $searchKey = $this->input->getGet('/0');
        $iPage = $this->input->getGet('/1', 0, FILTER_VALIDATE_INT);
        if ((string)(int)$searchKey === $searchKey) {
            $iPage = $searchKey;
            $searchKey = '';
        }

        if ($searchParam) {
            $aList = $oModel->getPublicationsByPart($searchParam, $iPage);
        } elseif ($searchKey) {
            $aList = $oModel->getPublicationsByKey($searchKey, $iPage);
        } else {
            return $this->indexAction($aParams);
        }

        $oHelper->add('index', array(
            'stat' => null,
            'num' => Params::getPageCount($aList['count'], $oModel->getPageCount()),
            'curr' => $iPage,
            'title' => $searchKey,
            'search' => $searchParam,
            'desc' => $oTranslate->get(['description', $this->input->getUrl(null)]),
            'url' => $this->input->getUrl(null),
            'list' => $aList
        ));
        return $oHelper;
    }
}
