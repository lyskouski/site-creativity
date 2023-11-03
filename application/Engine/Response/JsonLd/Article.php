<?php namespace Engine\Response\JsonLd;

use Data\Doctrine\Main;

/**
 * Json-Ld Article
 *
 * @since 2016-10-12
 * @author Viachaslau Lyskouski
 */
class Article
{

    public function getAttributes(Main\Content $data, $content, Main\ContentViews $stat, $firstDate = null)
    {
        if (is_null($firstDate)) {
            $firstDate = (new \Modules\Dev\History\Model)->getFirstDate($data);
        }
        $ratingValue = \Defines\Database\Params::getRating($stat);
        $ratingCount = $stat->getVotesDown() + $stat->getVotesUp();
        $favicon = \System\Registry::config()->getUrl(null, false) . '/favicon.png';
        $username = \Data\UserHelper::getUsername($data->getAuthor());

        $oTranslate = \System\Registry::translation();

        return array(
            '@context' => 'http://schema.org',
            '@type' => 'Article',
            'name' => $data->getContent(),
            'author' => array(
                '@type' => 'Person',
                'name' => $username,
                'url' => (new \Engine\Response\Template)->getUrl('person/' . $username)
            ),
            'inLanguage' => $data->getLanguage(),
            'datePublished' => $firstDate,
            'dateModified' => $data->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT),
            'headline' => $oTranslate->get(['description', $data->getPattern()]),
            'mainEntityOfPage' => '\cleanup{begin}'. $content . '\cleanup{end}',
            'image' => \System\Registry::config()->getUrl(null, false) . $oTranslate->get(['og:image', $data->getPattern()]),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $oTranslate->sys('LB_SITE_TITLE'),
                'address' => '',
                'telephone' => '',
                'logo' => array(
                    '@type' => 'ImageObject',
                    'image' => $favicon,
                    'url' => $favicon,
                    'width' => 32,
                    'height' => 32
                )
            ),
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'bestRating' => \Defines\Database\Params::MAX_RATING,
                'worstRating' => 0,
                'ratingValue' => $ratingValue,
                'reviewCount' => $ratingCount,
                'ratingCount' => $ratingCount
            )
        );
    }

    public function getEditor($data)
    {
        return array(
            'editor' => array(
                '@type' => 'Person',
                'name' => $data->getAuditor()->getUsername(),
                'url' => (new \Engine\Response\Template)->getUrl('person/' . $data->getAuditor()->getUsername())
            )
        );
    }
}
