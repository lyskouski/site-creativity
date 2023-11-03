<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'subtitle' => $translate->get(['og:title', $this->get('title_href')]),
        'subtitle_href' => $this->getUrl($this->get('title_href'))
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('title_href'), \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="find" />
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_BOOK_ISBN') ?>:</span>
                <input autocomplete="off" type="text" name="isbn" />
            </div>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_SEARCH') ?>" />
        </form>
    </section>
</article>