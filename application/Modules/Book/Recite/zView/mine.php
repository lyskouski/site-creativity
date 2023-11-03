<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>
    <article class="el_content">
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_BOOK_RECITE_MINE'),
            'sub_languages' => \Defines\Language::getList()
        ));

        ?>

        <div class="clear bg_headers indent el_border bg_mask"><?php echo $translate->sys('LB_HEADER_503') ?></div>
    </article>
</article>