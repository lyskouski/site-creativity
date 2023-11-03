<?php namespace Modules\Person\Work;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    const ARTWORK_LIST = 8;

    public function getUserContent()
    {
        return array();
    }

    public function getDraft()
    {
        $em = \System\Registry::connection();
        return $em->getRepository(\Defines\Database\CrMain::CONTENT_NEW)->findBy(array(
            'author' => \System\Registry::user()->getEntity(),
            'language' => \System\Registry::translation()->getTargetLanguage(),
            'type' => 'og:title'
        ));
    }

    /**
     * Prepare request
     *
     * @params array $params
     * @return \Doctrine\ORM\QueryBuilder
     * @throws \Error\Validation
     */
    public function prepareList(array $params, array $sort = array('c.updatedAt' => 'DESC'))
    {
        $em = \System\Registry::connection();
        $queryBuilder = $em->getRepository(\Defines\Database\CrMain::CONTENT)->createQueryBuilder('c');
        // Find by author
        if (array_key_exists('author', $params)) {
            $user = \System\Registry::user()->getEntity();
            if ($params['author']) {
                $user = $em->getRepository(\Defines\Database\CrMain::USER)->findOneByUsername($params['author']);
            }
            unset($params['author']);
            if (!$user) {
                throw new \Error\Validation(
                    \System\Registry::translation()->sys('LB_ERROR_MISSING_DATA')
                );
            }
            $queryBuilder->andWhere('c.author = :author')
                ->setParameter('author', $user);
        }
        // Where parameters
        $i = 0;
        foreach ($params as $key => $values) {
            foreach ($values as $type => $value) {
                // Update by a list of variables
                if (is_array($value)) {
                    $a = array_fill(0, sizeof($value), "c.$key $type :{$key}{i}");
                    foreach ($a as $k =>  &$part) {
                        $part = str_replace('{i}', $i, $part);
                        $queryBuilder->setParameter($key.$i, $value[$k]);
                        $i++;
                    }
                    $queryBuilder->andWhere(implode(' OR ', $a));
                // Single value
                } else {
                    $queryBuilder->andWhere("c.$key $type :{$key}{$i}")
                        ->setParameter($key.$i, $value);
                }
                $i++;
            }
        }
        // Define ordering
        foreach ($sort as $key => $order) {
            $queryBuilder->addOrderBy($key, $order);
        }
        return $queryBuilder;
    }

    public function getArtwork($iPage = 0, $iCount = self::ARTWORK_LIST, $username = null, $query = false)
    {
        $queryBuilder = $this->prepareList([
            'type' => ['=' => 'og:title'],
            'language' => ['=' => \System\Registry::translation()->getTargetLanguage()],
            'pattern' => ['LIKE' => \Defines\Content\Type::getPublications()],
            'author' => $username
        ]);

        $queryBuilder->setFirstResult($iPage * $iCount)
            ->setMaxResults($iCount);

        return $query ? $queryBuilder : $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get User works
     *
     * @param array $aOrder
     */
    public function getDashboard()
    {
        return array(
            'draft' => $this->getDraft(),
            'artwork' => $this->getArtwork()
        );
    }
}
