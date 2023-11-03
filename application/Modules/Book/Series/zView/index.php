<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$user = \System\Registry::user();
$imgPath = new \System\Minify\Images();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_BOOK_OVERVIEW')),
        'title_href' => $this->getUrl('book/overview'),
        'languages' => $this->get('language_list'),
        'button' => $translate->sys('LB_BUTTON_ADD'),
        'button_href' => '/ru/person/work/book/series.html'
    ));

    $aData = $this->get('list');

    $template = \Defines\Response\ListType::getType();
    /* @var $oContent \Data\Doctrine\Main\Content */
    foreach ($aData['og:title'] as $sPattern => $oContent):
        $s = $oContent->getPattern();
        echo $this->partial($template, array(
            'author_txt' => \Data\UserHelper::getUsername($oContent->getAuthor()),
            'title' => $oContent->getContent(),
            'href' => $this->getUrl($oContent->getPattern()),
            'book_style' => true,
            'async' => false,
            'img_type' => $translate->get(['image', $oContent->getPattern()]),
            'entity' => $oContent,
            'origin' => true,
            'book_aside' => [],
            'img' => $aData['og:image'][$s],
            'text' => $aData['description'][$s],
            'updated_at' => $oContent->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
        ));
    endforeach;

    if (sizeof($aData['og:title'])):
        echo $this->partial('Basic/Nav/pages');
    else:
        ?><p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;

    echo $this->partial('Basic/search', array(
        'url' => 'book/series',
        'title' => $translate->sys('LB_BOOK_SEARCH'),
        'search' => $this->get('search'),
        'sort' => [
            \Access\Request\Search::SORT_NEW => $translate->sys('LB_SORT_BOOK_NEW'),
            \Access\Request\Search::SORT_BOOK_DATE => $translate->sys('LB_SORT_BOOK_DATE'),
            \Access\Request\Search::SORT_BOOK_AUHOR => $translate->sys('LB_SORT_BOOK_AUHOR'),
            \Access\Request\Search::SORT_BOOK_TITLE => $translate->sys('LB_SORT_BOOK_TITLE')
        ]
    ));

    ?>
</article>