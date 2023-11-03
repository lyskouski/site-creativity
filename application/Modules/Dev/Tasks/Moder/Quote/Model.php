<?php namespace Modules\Dev\Tasks\Moder\Quote;

/**
 * Model object for quotation check
 *
 * @since 2015-11-20
 * @author Viachaslau Lyskouski
 */
class Model extends \Modules\Dev\Tasks\Moder\Reply\Model
{

    /**
     * Get task
     *
     * @param string $sLanguage
     * @return \Data\Doctrine\Main\Content
     * @throws \Error\Validation
     */
    public function getList($sLanguage)
    {
        return (new \Data\ContentHelper)->getEntityManager()->createQuery(
                "SELECT c
                FROM Data\Doctrine\Main\Content c
                WHERE
                    c.language = :language
                    AND c.auditor IS NULL
                    AND c.type = :type
                ORDER BY c.updatedAt DESC"
            )
            ->setParameter('language', $sLanguage)
            ->setParameter('type', 'quote')
            ->setMaxResults(\Defines\Database\Params::COMMENTS_ON_PAGE)
            ->getResult();
    }
}
