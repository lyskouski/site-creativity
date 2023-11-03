<?php namespace Modules\Index;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    /**
     *
     * @return array
     */
    public function getLastUpdate()
    {
        $em = \System\Registry::connection();
        $name = \Defines\Database\CrMain::CONTENT;

        $query = $em->createQuery(
            "SELECT c
            FROM  $name c
            WHERE c.type = :type AND (
                c.pattern LIKE :pattern1
                OR c.pattern LIKE :pattern2
                OR c.pattern LIKE :pattern3
                OR c.pattern LIKE :pattern4
                OR c.pattern LIKE :pattern5
                OR c.pattern LIKE :pattern6
            )
            ORDER BY c.updatedAt DESC"
            )
            ->setParameter('type', 'og:title')
            ->setParameter('pattern1', 'mind/article/i%')
            ->setParameter('pattern2', 'oeuvre/prose/i%')
            ->setParameter('pattern3', 'oeuvre/poetry/i%')
            ->setParameter('pattern4', 'oeuvre/drawing/i%')
            ->setParameter('pattern5', 'oeuvre/music/i%')
            ->setParameter('pattern6', 'book/overview/i%')
            ->setMaxResults(8)
            ->setFirstResult(0);

        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT);
        return $rep->prepareData($query->getResult());
    }
}