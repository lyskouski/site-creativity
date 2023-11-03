<?php namespace Modules\Oeuvre\Prose;

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
                    ->bindKey('inf', array('list' => ['1', 1]))
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('pattern' => '/^i\d{1,}$/'))
                    ->bindKey('/1', array('ctype' => 'integer'))
                    ->bindKey('/2', array('ctype' => 'integer'))
            ->copyToExtension(\Defines\Extension::JSON);
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Init entity for displaying content
     */
    protected function initEntity()
    {
        $this->oEntity = new \Layouts\Entity\Prose($this->request, $this->response);
    }

    /**
     * Main page for Prose publication
     * @param array $aParams
     * @return \Layouts\Entity\Prose
     */
    public function indexAction(array $aParams)
    {
        $oTranslate = \System\Registry::translation();
        // Forward to search if the pattern was not set
        if (!$aParams) {
            $sName = $oTranslate->sys('LB_CATEGORY_PROSE');
            $this->input->setGet('/0', $sName);
            $oResult = $this->forward('Oeuvre', 'search', array($sName, 0));

        // Open comments
        } elseif ($this->input->getGet('/1', null, FILTER_VALIDATE_INT) === 0) {
            $oResult = $this->getComments($aParams);

        // Open requested page
        } else {
            $oResult = $this->getPage($aParams);
        }
        return $oResult;
    }

    /**
     * Pagination behaviour
     * @note forward to indexAction
     *
     * @param array $aParams
     * @return \Layouts\Entity\Prose
     */
    public function indexNumAction(array $aParams)
    {
        return $this->indexAction($aParams);
    }

    /**
     * Display comments
     * @todo pagination
     *
     * @param array $aParams
     * @return \Layouts\Entity\Prose
     */
    protected function getComments(array $aParams)
    {
        $sUrl = $this->input->getUrl(null);
        $oRep = (new \Data\ContentHelper)->getRepository();

        $iPage = $this->input->getGet('/2' , 0);

        $oProse = new \Layouts\Entity\Prose($this->request, $this->response);
        $oProse->add('Entity/comments', array(
            'list' => $oRep->findComments($sUrl, $iPage),
            'url' => $sUrl,
            'next_page' => ++$iPage
        ));

        return $oProse;
    }

    /**
     * Display current page
     *
     * @param array $aParams
     * @return \Layouts\Entity\Prose
     */
    protected function getPage(array $aParams)
    {
        $sUrl = $this->input->getUrl(null);

        $oHelper = new \Data\ContentHelper();
        $oStat = \System\Registry::stat();
        $o = $oHelper->getRepository()->findById(substr($this->input->getGet('/0'), 1));
        // Missing content
        if (!$o) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_410'),
                \Defines\Response\Code::E_DELETED
            );
        // Check if the content was moved to another url
        } elseif ($oStat->getContentCount() instanceof \System\ArrayUndef) {
            $sExt = \Defines\Extension::getDefault();
            $sUrlGoto = $this->input->getUrl("{$o->getLanguage()}/{$o->getPattern()}.{$sExt}");
            $oResult = (new \Layouts\Helper\Redirect($this->request, $this->response))->setUrl($sUrlGoto);
        // Normal behaviour
        } else {
            $oResult = $this->preparePage($oStat, $this->input, $sUrl);
        }
        return $oResult;
    }

    /**
     * Form page content
     *
     * @param \Data\Doctrine\Main\ContentViews $oStat
     * @param \Engine\Request\Input $oInput
     * @param string $sUrl
     * @return \Layouts\Entity\Prose
     */
    protected function preparePage($oStat, $oInput, $sUrl)
    {
        $oTranslate = \System\Registry::translation();
        $iPage = $oInput->getGet('/1', 0);

        $entity = \System\Registry::connection()
            ->getRepository(\Defines\Database\CrMain::CONTENT)->findOneBy([
                'type' => 'content#' . $iPage,
                'pattern' => $sUrl,
                'language' => $oTranslate->getTargetLanguage()
            ]
        );

        if (!$entity) {
            throw new \Error\Validation($oTranslate->sys('LB_PAGE_IS_MISSING'), \Defines\Response\Code::E_DELETED);
        }

        $aData = array(
            'content' => $entity->getContent(),
            'entity' => $entity,
            'url' => $iPage > 1 ? "/$sUrl#!/" . ($iPage - 1) : "/$sUrl",
            'page' => $oInput->getPost('inf') ? -1 : $iPage
        );
        // Check if continue is needed
        if ($oInput->getPost('inf') !== null) {
            $aData[\Error\TextAbstract::E_CODE] = \Defines\Response\Code::E_PARTIAL_CONTENT;
            $aData[\Error\TextAbstract::E_MESSAGE] = '';
        }
        $this->oEntity->add($this->oEntity->getPageTemplate(), $aData);

        // Check if next page exists
        if ($oStat->getContentCount() > ++$iPage) {
            $this->oEntity->add('Entity/scrollNext', array(
                'url' => "/$sUrl#!/" . $iPage
            ));
        } else {
            $this->oEntity->add('Basic/null');
        }
        return $this->oEntity;
    }
}
