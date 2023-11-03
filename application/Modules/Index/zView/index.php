<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    $aDefault =  array('og:title' => []);

    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->sys('LB_SITE_TITLE'),
            'title_href' => $this->getUrl('index'),
            'languages' => \Defines\Language::getList(),
            'subtitle' => $translate->sys('LB_FORUM_NEWS'),
            'subtitle_href' => $this->getUrl('dev/news')
        )
    );

    echo $this->partial('Basic/harmonic', array('list' => $this->get('news_list')));


    $services = array(
        'dev',
        'book'
    );

    $imgPath = new \System\Minify\Images();

    ?><div class="clear indent_neg"><?php
    foreach ($services as $url):
        echo $this->partial('Basic/notion_small', array(
            'img' => $translate->get(['og:image', $url], null, $imgPath->adaptWork($url)),
            'img_type' => $translate->get(['image', $url], null, $imgPath->adaptWork($url, '_type')),
            'title' => $translate->get(['og:title', $url]),
            'href' => $url,
            'async' => false,
            'text' => $translate->get(['description', $url])
        ));
    endforeach;
    ?></div><?php

    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->sys('LB_SITE_UPDATES'),
            'title_href' => $this->getUrl('index'),
            'subtitle' => $translate->sys('LB_OEUVRE'),
            'subtitle_href' => $this->getUrl('oeuvre'),
            'num' => 2
        )
    );

    // @todo: Get from the Database a pined book
    $bookUrl = 'book/overview/i20345';
    echo $this->partial('Basic/Adver/book', array(
        'img' => $translate->get(['og:image', $bookUrl]),
        'img_type' => $imgPath->getWork() . 'book_type.svg',
        'title' => $translate->get(['og:title', $bookUrl]),
        'href' => $this->getUrl($bookUrl),
        'text' => $translate->get(['description', $bookUrl]),
        'text_extra' => $translate->get(['content#0', $bookUrl])
    ));

    // Oeuvre publications
    $a1 = $this->get('oeuvre_list');
    if ($a1 && $a1['description']):
        echo $this->partial('Entity/Basic/notion', array(
            'list' => $this->get('oeuvre_list')
        ));
    else:
        ?><p class="clear indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;


    echo $this->partial(
        'Basic/title',
        array(
            'subtitle' => $translate->sys('LB_COGNITION'),
            'subtitle_href' => $this->getUrl('cognition'),
            'num' => 2
        )
    );

    // @todo Cognition publications
    ?><p class="clear indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php


    echo $this->partial(
        'Basic/title',
        array(
            'subtitle' => $translate->sys('LB_MIND'),
            'subtitle_href' => $this->getUrl('mind'),
            'num' => 2
        )
    );

    // Mind publications
    $a2 = $this->get('mind_list');
    if ($a2 && $a2['description']):
        echo $this->partial('Entity/Basic/notion', array(
            'list' => $this->get('mind_list')
        ));
    else:
        ?><p class="clear indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;

    ?>
</article>