<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');

/* @var $o \Data\Doctrine\Main\ContentNew */
$o = $this->get('og:title', new \System\ArrayUndef());
$oReply = $this->get('og:reply', new \System\ArrayUndef());
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_TRANSLATION_BOOK'),
        'title_href' => '',
        'subtitle' => $translate->sys('LB_VIEW_PAGE'),
        'subtitle_href' => $this->getUrl($o->getPattern(), null, $o->getLanguage())
    )); ?>

    <?php if ((string)$oReply->getContent()): ?>
    <section class="el_panel bs_recess bg_mask">
        <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_REJECTED') ?></header>
        <p><?php echo $oReply->getContent() ?></p>
        <p><small><?php echo $oReply->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?></small>&nbsp;</p>
    </section>
    <?php else: ?>
    <p class="indent">
        <?php echo $translate->sys('LB_TASK_TRANSLATION_TEXT_ATTENTION') ?>
    </p>
    <p>&nbsp;</p>
    <?php endif ?>

    <section class="el_form indent">
        <form method="POST" action="<?php echo $this->getUrl('dev/tasks/translation/book', \Defines\Extension::JSON) ?>" class="ui" data-class="Request/Form" data-actions="init">
            <input type="hidden" name="action" value="save" />
            <?php
            echo $this->partial(
                'Entity/Book/new_params',
                $this->get('list')
            );
            ?>
            <input type="checkbox" name="next" <?php echo $this->get('next') ? 'checked' : '' ?> /> <?php echo $translate->sys('LB_TASK_AUTO_NEW') ?>
            <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_APPLY') ?>" />
        </form>
    </section>
</article>