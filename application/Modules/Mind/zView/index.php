<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_MIND')),
        'title_href' => $this->getUrl( 'mind' ),
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_SITE_UPDATES'),
        'subtitle_href' => $this->getUrl( 'mind/article' )
    ));

    echo $this->partial('Basic/harmonic', array('list' => $this->get('list')));

    echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_MIND_PRAXIS')),
        'title_href' => $this->getUrl( 'mind' ),
        'num' => 2
    ));
    ?>
    <p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p>
</article>