<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('title', $translate->sys('LB_MIND_TRAINER')),
        'title_href' => $this->getUrl('mind')
    )); ?>
    <p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING') ?></p>
</article>