<?php namespace Engine\Response\JsonLd;

use Data\Doctrine\Main;

/**
 * Json-Ld Book
 *
 * @since 2016-10-12
 * @author Viachaslau Lyskouski
 */
class Book
{

    public function getAttributes(Main\Content $data, Main\ContentViews $stat)
    {
        // $firstDate = (new \Modules\Dev\History\Model)->getFirstDate($data);
        $ratingValue = \Defines\Database\Params::getRating($stat);
        $ratingCount = $stat->getVotesDown() + $stat->getVotesUp();

        $oTranslate = \System\Registry::translation();

        return array(
            '@context' => 'http://schema.org',
            '@type' => 'Book',
            'name' => $oTranslate->get(['og:title', $data->getPattern()]),
            'image' => $oTranslate->get(['og:image', $data->getPattern()]),
            'inLanguage' => $data->getLanguage(),
            'datePublished' => $oTranslate->get(['date', $data->getPattern()]),
            // 'datePublished' => $firstDate,
            'numberOfPages' => $oTranslate->get(['pageCount', $data->getPattern()]),
            'isbn' => $oTranslate->get(['isbn', $data->getPattern()]),
            'about' => '\cleanup{begin}'. $oTranslate->get(['description', $data->getPattern()]) . '\cleanup{end}',
            'description' => '\cleanup{begin}'. $oTranslate->get(['content#0', $data->getPattern()]) . '\cleanup{end}',
            'dateModified' => $data->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT),
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

    public function getAuthorList($authors)
    {
        $list = array();
        foreach ($authors as $author) {
            $list[] = array(
                '@type' => 'Person',
                'name' => $author,
                'url' => (new \Engine\Response\Template)->getUrl('book/overview/author/' . $author)
            );
        }
        return array(
            'author' => $list
        );
    }

}
