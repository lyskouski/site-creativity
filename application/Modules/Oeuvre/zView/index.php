<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_OEUVRE')),
        'title_href' => $this->getUrl('oeuvre'),
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_CONTENT')
    ));

    echo $this->partial('Basic/Nav/catalog', array(
        'list' => (new \Engine\Validate\Helper\Quote)->convert(\Defines\Catalog::getOeuvre()),
        'url' => 'oeuvre/search',
        'url_active' => (new \Engine\Request\Input)->getGet('/0')
    ));

    $aData = $this->get('list');
    $tagsList = $aData[\Defines\Content\Attribute::TYPE_KEYS];

    $template = \Defines\Response\ListType::getType();
    /* @var $oContent \Data\Doctrine\Main\Content */
    foreach ($aData[\Defines\Content\Attribute::TYPE_TITLE] as $sPattern => $oContent):
        $s = $oContent->getPattern();
        $authorName = \Data\UserHelper::getUsername($oContent->getAuthor());
        $tagsList[] = "/person/$authorName/artwork:{$authorName}";
        echo $this->partial($template, array(
            'author' => $authorName,
            'title' => $oContent->getContent(),
            'href' => $this->getUrl($oContent->getPattern()),
            'async' => false,
            'img_type' => $aData['image'][$s],
            'img' => $aData['og:image'][$s],
            'text' => $aData['description'][$s],
            'updated_at' => $oContent->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
        ));
    endforeach;


    if (sizeof($aData['og:title'])):
        shuffle($tagsList);
        echo $this->partial('Basic/tags_cloud', array(
            'text' => implode(',', $tagsList),
            'url' => $this->getUrl('oeuvre', false)
        ));

        echo $this->partial('Basic/Nav/pages');
    else:
        ?><p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
    endif;

    echo $this->partial('Basic/search', array(
        'url' => 'oeuvre',
        'search' => $this->get('search')
    ));
    ?>
</article>