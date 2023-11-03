<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
$url = $oStat->getContent()->getPattern();

?><article class="el_content">
    <p>&nbsp;</p>
    <section class="indent">
        <img class="el_border el_width_full" src="<?php echo $translate->get(['og:image', $url]) ?>" />
        <p class="indent"><?php echo $translate->get(['description', $url]) ?></p>
        <p>&nbsp;</p>
        \module{Games/Accordion}
        <p>&nbsp;</p>
    </section>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_DESCRIPTION')),
        'num' => 2
    ));
    echo $this->partial('Ui/pageview', array(
        'url' => 'mind/article/i141456'
    ));
    echo $this->partial('Ui/pageview', array(
        'url' => 'mind/trainer'
    ));
    echo $this->partial('Ui/pageview', array(
        'url' => 'mind'
    ));
    ?>
</article>