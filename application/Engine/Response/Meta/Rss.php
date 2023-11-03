<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Rss extends MetaAbstract
{

    const NAME = 'link';

    // <link href="/citadel/rss" lang="ru" type="application/rss+xml" rel="alternate" title="Новостной канал сайта &laquo;Цитадель&raquo; (сервисы для виртуальных миров)" />
    public function __construct ( $sLanguage, $sHref )
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    'rel' => 'alternate',
                    'lang' => $sLanguage,
                    'type' => 'application/rss+xml',
                    'href' => pathinfo($sHref, PATHINFO_DIRNAME ) .'/'. pathinfo($sHref, PATHINFO_FILENAME) . '.' . \Defines\Extension::RSS,
                    'title' => \System\Registry::translation()->sys( 'LB_SITE_TITLE' ) . ' (' . $sHref . ')'
                ))
            )
        );

    }

    public function isEqual ( MetaInterface $oMeta )
    {
        return $oMeta instanceof Rss;

    }

}
