<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemscope itemtype="http://schema.org/Article">
    <p>&nbsp;</p>
    <?php
    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->sys( 'LB_SITE_INFO_AUTHORS' ),
            'title_href' => $this->getUrl('info/authors')
        )
    );

    echo $this->partial('Basic/Desc/dynamic', array(
        'url' => 'info/authors'
    ));
    ?>
</article>