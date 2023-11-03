<?php namespace Modules\Person;

use Engine\Response\Meta\Title;

/**
 * General controller for index page
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
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('type' => 'string'))
                    ->bindKey('/1', array('list' => ['artwork', 'topic', 'comment']))
                    ->bindKey('/2', array('stype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('data', array('type' => 'array'))
        ;
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Override indexNumPage behaviour
     */
    protected function initMethod()
    {
        parent::initMethod();
        if ($this->params) {
            $this->action = "indexNumAction";
        }
    }

    /**
     * User page in edit mode
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function indexAction(array $aParams)
    {
        array_unshift($aParams, '');
        $this->request->setParams($aParams);
        $oHelper = new \Layouts\Helper\Person($this->request, $this->response);
        $oHelper->add('index', (new Model)->getPersonal());
        return $oHelper;
    }

    /**
     * User page overview
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function indexNumAction(array $aParams)
    {
        if (sizeof($aParams) > 1) {
            $exec = $aParams[1].'List';
            return $this->{$exec}($aParams[0]);
        }

        $oUser = \System\Registry::user();
        $bNew = $oUser->checkAccess('dev/tasks/auditor/text') || $oUser->getName() === $aParams[0];
        $aData = (new Model)->getPersonal($aParams[0], $bNew);
        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        if (!array_diff(array_keys($aData['content']), \Defines\Content\Attribute::getBasicList())) {
            $oHelper->add('missing', $aData);
        } else {
            $oHelper->add('view', $aData);
        }

        return $oHelper;
    }

    protected function getList($queryBuilder, $username, $url, $title) {
        (new Model)->checkPersonListDesc($username, $url, $title);

        $oHelper = new \Layouts\Helper\Basic($this->request, $this->response);
        $oHelper->add('artwork', array(
            'list' => $queryBuilder->getQuery()->getResult(),
            'curr' => $this->input->getGet('/2', 0),
            'num' => (new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder))->count(),
            'count_page' => \Defines\Database\Params::CONTENT_ON_PAGE,
            'username' => $username,
            'url' => $url,
            'subtitle' => $title
        ));
        return $oHelper;
    }

    protected function artworkList($username)
    {
        $queryBuilder = (new Work\Model)->getArtwork(
            $this->input->getGet('/2', 0),
            \Defines\Database\Params::CONTENT_ON_PAGE,
            $username,
            true
        );

        return $this->getList($queryBuilder, $username, "person/$username/artwork", 'LB_PERSON_WORK');
    }

    protected function topicList($username)
    {
        $curr = $this->input->getGet('/2', 0);

        $queryBuilder = (new Work\Model)->prepareList([
            'type' => ['=' => 'og:title'],
            'language' => ['=' => \System\Registry::translation()->getTargetLanguage()],
            'pattern' => ['LIKE' => 'dev/%/i%'],
            'author' => $username
        ]);
        $queryBuilder->setFirstResult($curr * \Defines\Database\Params::CONTENT_ON_PAGE)
            ->setMaxResults(\Defines\Database\Params::CONTENT_ON_PAGE);

        return $this->getList($queryBuilder, $username, "person/$username/topic", 'LB_TASK_MODER_TOPICS');
    }

    protected function commentList($username)
    {
        $curr = $this->input->getGet('/2', 0);

        $queryBuilder = (new Work\Model)->prepareList([
            'language' => ['=' => \System\Registry::translation()->getTargetLanguage()],
            'type' => ['LIKE' => 'comment#%'],
            'author' => $username
        ]);
        $queryBuilder->setFirstResult($curr * \Defines\Database\Params::CONTENT_ON_PAGE)
            ->setMaxResults(\Defines\Database\Params::CONTENT_ON_PAGE);

        $oHelper = $this->getList($queryBuilder, $username, "person/$username/comment", 'LB_TASK_MODER_REPLY');

        if ($this->request->getRequestMethod() === \Defines\RequestMethod::POST) {
            $oHelper->get(0)->changeTemplate('comments');
        } else {
            $oHelper->get(1)->changeTemplate('comments');
        }
        return $oHelper;
    }

    /**
     * Save changes for a personal page
     * @note redirect to indexAction
     *
     * @param array $aParams
     * @return \Layouts\Helper\Person
     */
    public function saveAction(array $aParams)
    {
        $aData = $this->input->getPost('data');
        (new Model)->savePersonal($aData);
        return $this->indexAction($aParams);
    }

    /**
     * Push personal page for (a verification and then) a public access
     * @note redirect to indexAction
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function publicateAction(array $aParams)
    {
        (new Model)->toCheck();
        return $this->indexAction($aParams);
    }
}
