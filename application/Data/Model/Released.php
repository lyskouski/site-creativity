<?php namespace Data\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Helper for a content view
 *
 * @author Viachaslau Lyskouski
 * @since 2016-02-02
 * @package Data/Model
 */
class Released extends EntityRepository
{

    protected function getTime($version)
    {
        return $this->_em
                ->createQuery("SELECT r.updatedAt FROM \Data\Doctrine\Main\Released AS r WHERE r.version LIKE :version")
                ->setParameter('version', "%{$version}%")
                ->setMaxResults(1)
                ->getSingleScalarResult();
    }

    /**
     * Change testsed status to OK
     *
     * @param string $version
     * @return integer
     */
    public function updateTests($version)
    {
        return $this->_em->createQuery(
                "UPDATE \Data\Doctrine\Main\Released AS r
                SET r.tested = 1
                WHERE r.tested = 0 AND r.updatedAt <= :updatedAt"
            )
            ->setParameter('updatedAt', $this->getTime($version))
            ->execute();
    }

    /**
     *
     * @param integer $branch
     * @param string $version
     * @return integer
     */
    public function updateBranch($branch, $version)
    {
        $em = $this->getEntityManager();
        $i = $em->createQuery(
                "UPDATE \Data\Doctrine\Main\Released AS r
                SET r.branch = :branch
                WHERE r.branch != :branch AND r.updatedAt <= :updatedAt"
            )
            ->setParameter('updatedAt', $this->getTime($version))
            ->setParameter('branch', $branch)
            ->execute();

        $i += $em->createQuery(
                "UPDATE \Data\Doctrine\Main\Released AS r
                SET r.branch = :prev
                WHERE r.branch = :branch AND r.updatedAt > :updatedAt"
            )
            ->setParameter('updatedAt', $this->getTime($version))
            ->setParameter('branch', $branch)
            ->setParameter('prev', $branch-1)
            ->execute();

        return $i;
    }

}
