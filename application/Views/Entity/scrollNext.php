<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="center el_content" id="ui-next_page">
    <a class="button bg_form ui" href="<?php echo str_replace('#!', '', $this->getUrl($this->get('url'))) ?>" data-href="<?php echo $this->getUrl($this->get('url')) ?>" data-class="Request/Pjax" data-actions="init,infiniteScroll"><?php echo $translate->sys('LB_PAGE_NEXT') ?></a>
    <p class="blur txt_left indent"><?php echo $translate->sys('LB_PAGE_INFINITE_SCROLL') ?></p>
</article>