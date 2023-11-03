<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
?>
<article class="el_content">
    <?php
    $aDefault =  array('og:title' => []);

    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->get(['og:title', 'book']),
            'title_href' => $this->getUrl('book'),
            'languages' => \Defines\Language::getList(),
            'subtitle' => $translate->sys('LB_FORUM_NEWS'),
            'subtitle_href' => $this->getUrl('book')
//            'subtitle' => $oTranslate->sys('LB_BOOK_RECITE'),
//            'subtitle_href' => $this->getUrl('book/recite')
        )
    );

    echo $this->partial('Basic/harmonic', array('list' => $this->get('series_list', array())));

    echo $this->partial(
        'Basic/title',
        array(
            'subtitle' => $translate->sys('LB_BOOK_OVERVIEW'), // LB_SITE_UPDATES
            'subtitle_href' => $this->getUrl('book/overview'),
            'num' => 1
        )
    );

    /* @var $o \Data\Doctrine\Main\Content */
    foreach ($this->get('book_top') as $o):
        $s = $o->getPattern();
        echo $this->partial('Basic/Adver/book', array(
            'img' => $translate->get(['og:image', $s], null, $imgPath->adaptWork($s, '', 'work/book.svg')),
            'img_type' => $translate->get(['image', $s], null, $imgPath->adaptWork($s, '_type', 'work/book_type.svg')),
            'title' => $o->getContent(),
            'href' => $this->getUrl($o->getPattern()),
            'text' => $translate->get(['description', $o->getPattern()]),
            'text_extra' => $translate->get(['content#0', $o->getPattern()])
        ));
    endforeach;

    if ($this->get('book_list')):
        $aData = $this->get('book_list');
        /* @var $oContent \Data\Doctrine\Main\Content */
        foreach ($aData['og:title'] as $sPattern => $oContent):
            $s = $oContent->getPattern();
            if (strpos($aData['og:image'][$s], 'book.svg')):
                $aData['og:image'][$s] = str_replace('book.svg', 'prose.svg', $aData['og:image'][$s]);
            endif;
            if (strpos($aData['image'][$s], 'overview_type.svg')):
                $aData['image'][$s] = str_replace('overview_type.svg', 'book_type.svg', $aData['image'][$s]);
            endif;

            $imgPath = new \System\Minify\Images();

            echo $this->partial('Basic/notion', array(
                'author_txt' => $translate->get(['author', $oContent->getPattern()]),
                'title' => $oContent->getContent(),
                'href' => $this->getUrl($oContent->getPattern()),
                'async' => false,
                'img_type' => $imgPath->adapt($aData['image'][$s]),
                'img' => $imgPath->adapt($aData['og:image'][$s]),
                'text' => $aData['description'][$s],
                'updated_at' => $translate->get(['date', $oContent->getPattern()], null, function($date) {
                    return substr($date, 0, 4);
                })
            ));
        endforeach;
    else:
        ?><p class="clear indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;

    ?>
</article>