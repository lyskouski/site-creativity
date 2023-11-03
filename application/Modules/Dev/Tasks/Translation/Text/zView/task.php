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
        'title' => $translate->sys('LB_TASK_TRANSLATION_TEXT'),
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
        <form method="POST" action="<?php echo $this->getUrl('dev/tasks/translation/text', \Defines\Extension::JSON) ?>" class="ui" data-class="Request/Form" data-actions="init">
            <input type="hidden" name="action" value="save" />

            <?php if ((string)$oReply->getContent()): ?>
            <input type="hidden" name="list[<?php echo $oReply->getId() ?>]" value="<?php echo $oReply->getContent() ?>" />
            <?php endif ?>

            <div class="el_grid">
                <span><?php echo $translate->sys('LB_PERSON_TITLE') ?></span>
                <input type="text" name="list[<?php echo $o->getId() ?>]" value="<?php echo $o->getContent() ?>" />
            </div>
            <p>&nbsp;</p>

            <?php
            if ($o = $this->get('keywords')):
                ?>
                <div class="el_grid">
                    <span><?php echo $translate->sys('LB_PERSON_KEYWORDS') ?></span>
                    <input type="text" name="list[<?php echo $o->getId() ?>]" value="<?php echo $o->getContent() ?>" />
                </div>
                <p>&nbsp;</p>
                <?php
            endif;

            if ($o = $this->get('description')):
                ?>
                <p><?php echo $translate->sys('LB_PERSON_DESCRIPTION') ?></p>
                <textarea name="list[<?php echo $o->getId() ?>]"><?php echo $o->getContent() ?></textarea>
                <?php
            endif;

            if ($o = $this->get('content#0')):
                ?>
                <textarea name="list[<?php echo $o->getId() ?>]"><?php echo $o->getContent() ?></textarea>
                <?php
            endif;
            ?>
            <input type="checkbox" name="next" <?php echo $this->get('next') ? 'checked' : '' ?> /> <?php echo $translate->sys('LB_TASK_AUTO_NEW') ?>
            <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_APPLY') ?>" />
        </form>
    </section>
    <p>&nbsp;</p>
    <?php echo $this->partial('Basic/title', array(
        'subtitle' => $translate->sys('LB_PAGE_OVERVIEW_LANGUAGES')
    ));

    foreach ($this->get('samples', array()) as $sLang => $aContent): ?>
    <section class="el_table indent">
        <aside>
            <span class="cr_pointer bg_attention ui"
               data-class="Request/Translation" data-actions="yandex" data-from="<?php echo $sLang ?>" data-to="<?php echo $translate->getTargetLanguage() ?>" data-content="<?php echo $aContent['keywords']->getContent() ?>">&nbsp;?&nbsp;</span>
            <strong><?php echo $translate->sys('LB_PERSON_KEYWORDS') ?></strong>
            <br />
            <?php echo $aContent['keywords']->getContent() ?>
        </aside>
        <header class="im_left">
            <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
            <span><?php echo $aContent['og:title']->getContent() ?>
            <span class="cr_pointer bg_attention ui"
               data-class="Request/Translation" data-actions="yandex" data-from="<?php echo $sLang ?>" data-to="<?php echo $translate->getTargetLanguage() ?>" data-content="<?php echo $aContent['og:title']->getContent() ?>">&nbsp;?&nbsp;</span>
            </span>
        </header>
        <p>
            <?php echo $aContent['description']->getContent() ?>
            <span class="cr_pointer bg_attention ui"
               data-class="Request/Translation" data-actions="yandex" data-from="<?php echo $sLang ?>" data-to="<?php echo $translate->getTargetLanguage() ?>" data-content="<?php echo $aContent['description']->getContent() ?>">&nbsp;?&nbsp;</span>
            <?php if (strlen($aContent['description']->getContent()) < 150):
                ?><br />&nbsp;<?php
            endif;
            ?>
        </p>
    </section>
    <?php endforeach ?>
</article>