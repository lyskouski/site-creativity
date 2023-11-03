<?php namespace Modules\Cron\Task\Book;

use Defines\Database\CrMain;

/**
 * Description of BookReadHistory
 *
 * @since 2016-05-11
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\Cron\AbstractController
{

    public function dailyAction(array $aParams)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(CrMain::BOOK_READ_HISTORY);

        $dailyList = array();
        /* @var $entity \Data\Doctrine\Main\BookReadHistory */
        foreach ($rep->findAll() as $entity) {
            $id = $entity->getBookRead()->getId();
            if (!isset($dailyList[$id])) {
                $dailyList[$id] = new \Data\Doctrine\Main\BookReadHistoryDaily();
                $dailyList[$id]->setBookRead($entity->getBookRead())
                    ->setContent($entity->getContent())
                    ->setPage(0)
                    ->setUpdatedAt(new \DateTime);
            }
            $dailyList[$id]->setPage($dailyList[$id]->getPage() + (int) $entity->getPage());
            $em->persist($dailyList[$id]);
            $em->remove($entity);
        }
        $em->flush();
        return new \Layouts\Helper\Zero($this->request, $this->response);
    }

    public function monthlyAction(array $aParams)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(CrMain::BOOK_READ_HISTORY_DAILY);

        $monthList = array();

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria->expr()->gt('updatedAt', new \DateTime('-1 month')));
        /* @var $entity \Data\Doctrine\Main\BookReadHistoryDaily */
        foreach ($rep->matching($criteria) as $entity) {
            $content = $entity->getContent();
            $id = $content->getId();
            $user = $content->getAuthor();
            if (!isset($monthList[$id])) {
                $monthList[$id] = new \Data\Doctrine\Main\BookReadHistoryMonthly();
                $monthList[$id]->setContent($content)
                    ->setUser($user)
                    ->setPage(0)
                    ->setUpdatedAt(new \DateTime);
            }
            $monthList[$id]->setPage($monthList[$id]->getPage() + (int) $entity->getPage());
            $em->persist($monthList[$id]);
        }

        $em->flush();
        return new \Layouts\Helper\Zero($this->request, $this->response);
    }

}
