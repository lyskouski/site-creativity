<?php namespace Data\Model;

use Doctrine\ORM\EntityRepository;
use Defines\Database\CrMain;

/**
 * Helper for a book read
 *
 * @since 2016-09-27
 * @author Viachaslau Lyskouski
 * @package Data/Model
 */
class BookRead extends EntityRepository
{

    public function checkUserSpeed($user)
    {
        $prefixMonth = 'bhm';
        $queryMonth = $this->getEntityManager()->getRepository(CrMain::BOOK_READ_HISTORY_MONTHLY)
            ->createQueryBuilder($prefixMonth)
            ->where("$prefixMonth.user = :user")
            ->setParameter('user', $user);

        $date = date('Y-m-01', strtotime('-1 month'));
        $queryMonth->andWhere("$prefixMonth.updatedAt LIKE :updatedAt")
            ->setParameter('updatedAt', "$date%");

        $list = $queryMonth->getQuery()->getResult();

        $pages = 0;
        foreach ($list as $o) {
            $pages += $o->getPage();
        }

        return $pages / 30;
    }

}
