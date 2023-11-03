<?php namespace Modules\Dev\Proposition;

use Defines\Database\Params;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Defines\Database\BoardCategory;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    public function getWorkflow()
    {
        $oRepWorkflow = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::WORKFLOW);
        $oRepRelease = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::RELEASED);

        $aLast = array();
        $aList = $oRepWorkflow->findBy(array(), array('id' => 'DESC'), Params::COMMENTS_ON_PAGE);
        /* @var $oWorkflow \Data\Doctrine\Main\Workflow */
        foreach ($aList as $oWorkflow) {
            $id = $oWorkflow->getContent()->getId();
            $a = isset($aLast[$id]) ? $aLast[$id] : array(
                'name' => $oWorkflow->getContent()->getContent(),
                'url' => $oWorkflow->getContent()->getPattern(),
                'data' => array()
            );
            $a['data'][] = array(
                1000 * $oWorkflow->getStartedAt()->getTimestamp(),
                1000 * ($oWorkflow->getStatus() ? (new \DateTime)->getTimestamp()  : $oWorkflow->getEndedAt()->getTimestamp())
            );
        }

        $taskModel = new \Modules\Dev\Board\Model();

        $statInfo = array();
        $now = new \DateTime();
        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->andWhere($criteria->expr()->gt('updatedAt', new \DateTime('-1 month')));
        $criteria->orderBy(['content' => 'ASC', 'updatedAt' => 'ASC']);

        /* @var $stat \Data\Doctrine\Main\Released */
        foreach ($oRepRelease->matching($criteria) as $stat) {
            $cnt = $stat->getContent();
            if ($cnt) {
                $statInfo[$cnt->getContent()][$stat->getUpdatedAt()->diff($now)->days] = $cnt->getPattern();
            }
        }

        return array(
            'curr' => $oRepWorkflow->findBy(array(
                'status' => Params::WORKFLOW_ACTIVE
            )),
            'last' => array_values($aLast),
            'stat' => $statInfo,

            'num_do' => (new Paginator($taskModel->getQueryTask(BoardCategory::RECENT)))->count(),
            'num_in' => (new Paginator($taskModel->getQueryTask(BoardCategory::ACTIVE)))->count(),
            'num_ok' => (new Paginator($taskModel->getQueryTask(BoardCategory::FINISH)))->count(),
            'num_no' => (new Paginator($taskModel->getQueryTask(BoardCategory::DELETE)))->count()
        );
    }

    public function getSubtaskList($pattern)
    {
        return \System\Registry::connection()->createQuery(
                "SELECT c
                FROM Data\Doctrine\Main\Content c
                WHERE
                    c.pattern = :pattern
                    AND c.type LIKE 'subtask#%'
                GROUP BY c.type
                ORDER BY c.access, c.content")
            ->setParameter('pattern', $pattern)
            ->getResult();
    }

    public function addSubtask($pattern, $type, $content)
    {
        $em = \System\Registry::connection();

        $entity = new \Data\Doctrine\Main\Content();
        $entity->setAccess(BoardCategory::RECENT)
                ->setAuthor(\System\Registry::user()->getEntity())
                ->setContent($content)
                ->setLanguage(\System\Registry::translation()->getTargetLanguage())
                ->setPattern($pattern)
                ->setType($type)
                ->setUpdatedAt(new \DateTime);
        $em->persist($entity);
        $em->flush();
    }

}
