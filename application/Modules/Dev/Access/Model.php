<?php namespace Modules\Dev\Access;

/**
 * Model object for 'dev/access' page
 *
 * @note access titles
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    /**
     * @var \Data\UserHelper
     */
    protected $oHelper;

    /**
     * Init model with a helper stuff
     */
    public function __construct()
    {
        $this->oHelper = new \Data\UserHelper();
    }

    /**
     * Get full list of access and related to them actions
     *
     * @return array
     */
    public function getAccessList()
    {
        $aAccess = $this->oHelper->getAllProfiles();
        $aResult = array();
        /* @var $oAccess \Data\Doctrine\Main\Access */
        foreach ($aAccess as $oAccess) {
            $id = $oAccess->getId();
            $aResult = $this->defineList($id, $aResult);
            $aResult[$id]['title'] = \System\Registry::translation()->sys("{$oAccess->getTitle()}");
            $aResult[$id]['desc'] = \System\Registry::translation()->sys("{$oAccess->getTitle()}_DESCRIPTION");
            $aResult[$id]['pages'] = $this->oHelper->getRelatedActions($oAccess);
            // Check if the object has a parent
            if ($oAccess->getAccess()) {
                $idMain = $oAccess->getAccess()->getId();
                $aResult = $this->defineList($idMain, $aResult);
                $aResult[$idMain]['sub'][] = $id;
                $aResult[$id]['child'] = true;
            }
        }
        return $aResult;
    }

    /**
     * Get all possible actions
     *
     * @return array<\Data\Doctrine\Main\Action>
     */
    public function getActionList()
    {
        return $this->oHelper->getAllActions();
    }

    /**
     * Define new element if missing
     *
     * @param int $id
     * @param array $aResult
     * @return array
     */
    protected function defineList($id, array $aResult)
    {
        if (!isset($aResult[$id])) {
            $aResult[$id] = array(
                'sub' => array(),
                'child' => false
            );
        }
        return $aResult;
    }

    /**
     * Save changes into Database
     *
     * @param array $aAccessAction
     * @param array $aNewAccess
     */
    public function saveChanges($aAccessAction, $aNewAccess)
    {
        $aActual = $this->addNewAccess($aNewAccess, $aAccessAction);
        $this->updateAccess($aActual);

        if ($aAccessAction) {
            $aNew = $this->updatePermissions($aAccessAction);
            $this->addPermissions($aNew);
        }
    }

    /**
     * Add new access-types
     * @param array $aNewAccess
     */
    public function addNewAccess($aNewAccess, &$aAccessAction)
    {
        $oTool = new \System\Aggregator();
        $oManager = $this->oHelper->getEntityManager();
        $aTitles = array();
        // Add new access-types
        foreach ($aNewAccess as $id => $a) {
            if ($id > -1) {
                continue;
            }
            $aTitles[$a['title']] = $id;
            $oEntity = new \Data\Doctrine\Main\Access();
            $oEntity->setTitle($a['title']);
            $iParent = $oTool->getValue($a, 'access', null);
            if ($iParent) {
                $oEntity->setAccess($this->oHelper->findProfile($iParent));
            }
            $oManager->persist($oEntity);
        }
        $oManager->flush();
        return $this->reconfigureData($aTitles, $aNewAccess, $aAccessAction);
    }

    protected function reconfigureData($aTitles, $aNewAccess, &$aAccessAction)
    {
        $oTool = new \System\Aggregator();
        $aUpdated = $this->oHelper->getEntityManager()->getRepository(\Defines\Database\CrMain::ACCESS)->findBy(array(
            'title' => array_keys($aTitles)
        ));
        $aRel = array();
        /* @var $o \Data\Doctrine\Main\Access */
        foreach ($aUpdated as $o) {
            $id = $aTitles[$o->getTitle()];
            $aRel[$id] = $o->getId();
            if (isset($aNewAccess[$id])) {
                $aNewAccess[$o->getId()] = $aNewAccess[$id];
                unset($aNewAccess[$id]);
            }
            if (isset($aAccessAction[$id])) {
                $aAccessAction[$o->getId()] = $aAccessAction[$id];
                unset($aAccessAction[$id]);
            }
        }
        // @todo - update access relations
        foreach ($aNewAccess as $id => $a) {
            $iParent = $oTool->getValue($a, 'access', null);
            if (isset($aRel[$iParent])) {
                $aNewAccess[$id]['access'] = $aRel[$iParent];
            }
        }
        return $aNewAccess;
    }

    /**
     * Update current access-types
     * @param array $aNewAccess
     */
    public function updateAccess($aNewAccess)
    {
        $oTool = new \System\Aggregator();
        $oManager = $this->oHelper->getEntityManager();

        $aAccess = $this->oHelper->getAllProfiles();
        /* @var $oAccess \Data\Doctrine\Main\Access */
        foreach ($aAccess as $oAccess) {
            $aNew = $oTool->getValue($aNewAccess, $oAccess->getId(), false);
            // delete action
            if ($aNew === false) {
                $oManager->remove($oAccess);
            } else {
                $iParent = $oTool->getValue($aNew, 'access', null);
                // redeclare parent node
                if ($iParent && (is_null($oAccess->getAccess()) || $iParent !== $oAccess->getAccess()->getId())) {
                    $oAccess->setAccess($this->oHelper->findProfile($iParent));
                }
            }
        }
        $oManager->flush();
    }

    public function updatePermissions($aAccessAction)
    {
        $oManager = $this->oHelper->getEntityManager();
        foreach ($aAccessAction as $id => $aAction) {
            $oAccess = $this->oHelper->findProfile($id);
            $aList = $this->oHelper->getRelatedActions($oAccess);
            /* @var $oAA \Data\Doctrine\Main\AccessAction */
            foreach ($aList as $oAA) {
                $i = $oAA->getAction()->getId();
                if (in_array($i, array_keys($aAction))) {
                    $oAA->setPermission($aAction[$i]);
                    if ($oAA->getPermission() !== $aAction[$i]) {
                        $oManager->persist($oAA);
                    }
                    unset($aAccessAction[$id][$i]);
                } else {
                    $oManager->remove($oAA);
                }
            }
        }
        $oManager->flush();
        return $aAccessAction;
    }

    public function addPermissions($aAccessAction)
    {
        $oManager = $this->oHelper->getEntityManager();
        // Add new permissions
        foreach ($aAccessAction as $id => $aAction) {
            foreach ($aAction as $i => $b) {
                $oAA = new \Data\Doctrine\Main\AccessAction();
                $oAA->setAccess($this->oHelper->findProfile($id));
                $oAA->setAction($this->oHelper->findAction($i));
                $oAA->setPermission($b);
                $oManager->persist($oAA);
            }
        }
        $oManager->flush();
    }
}
