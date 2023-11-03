<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$user = \System\Registry::user();
$em = \System\Registry::connection();
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
        'button_href' => '/ru/person/work/book.html'
    ));

    $aData = $this->get('list');
    $tagsList = $aData[\Defines\Content\Attribute::TYPE_KEYS];

    $template = \Defines\Response\ListType::getType();
    /* @var $oContent \Data\Doctrine\Main\Content */
    foreach ($aData['og:title'] as $sPattern => $oContent):
        $s = $oContent->getPattern();

        if (strpos($aData['og:image'][$s], 'book.svg')):
            $aData['og:image'][$s] = str_replace('book.svg', 'prose.svg', $aData['og:image'][$s]);
        endif;

        $img = $imgPath->getWork() . 'book_type.svg';
        $isbn = $translate->entity(['isbn', $oContent->getPattern()])->getContent();

        /* @var $read \Data\Doctrine\Main\BookRead */
        $read = $em->getRepository(\Defines\Database\CrMain::BOOK_READ)->findBy(array(
            'book' => $em->getReference(\Defines\Database\CrMain::BOOK, $isbn),
            'user' => $user->getEntity()
        ));
        if ($read):
            $img = str_replace('.svg', "_{$read[0]->getStatus()}.svg", $img);
        endif;

        $authorName = $translate->get(['author', $oContent->getPattern()]);
        $tagsList[] = "/book/overview/author/{$authorName}:{$authorName}";

        echo $this->partial($template, array(
            'author_txt' => $authorName,
            'title' => $oContent->getContent(),
            'href' => $this->getUrl($oContent->getPattern()),
            'pageCount' => $translate->get(['pageCount', $oContent->getPattern()]),
            'book_style' => true,
            'async' => false,
            'img_type' => $img,
            'entity' => $oContent,
            'list' => $this->get('read_list'),
            'read' => $read,
            'origin' => true,
            // 'img_type' => $aData['image'][$s],
            'img' => $aData['og:image'][$s],
            'text' => $aData['description'][$s],
            'updated_at' => $translate->get(['date', $oContent->getPattern()], null, function($data) {
                return substr($data, 0, 4);
            })
        ));
    endforeach;

    if (sizeof($aData['og:title'])):
        shuffle($tagsList);
        echo $this->partial('Basic/tags_cloud', array(
            'text' => implode(',', $tagsList),
            'url' => $this->getUrl('book/overview', false)
        ));

        echo $this->partial('Basic/Nav/pages');
    else:
        ?><p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;

    echo $this->partial('Basic/search', array(
        'url' => 'book/overview',
        'search_action' => $this->get('search_action', 'search'),
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