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
            <input type="hidden" name="action" value="create" />
            <?php echo $this->partial('Entity/Book/new_params') ?>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CREATE') ?>" />
        </form>
    </section>
</article>