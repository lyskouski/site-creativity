<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
/* @var $aContent array<\Data\Doctrine\Main\Content> */
$aContent = new \System\ArrayUndef($this->get('content', array()));
?>
<article class="el_content ui" data-class="Modules/Person" data-actions="init" data-type="person">
    <?php
    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->sys('LB_PERSON_PAGE_EDIT_MODE'),
            'title_href' => $this->getUrl('person'),
            'languages' => \Defines\Language::getList(),
            'subtitle' => $translate->sys('LB_PERSON_TO_VIEW'),
            'subtitle_href' => $this->getUrl($this->get('url'))
        )
    );
    ?>

    <?php if (substr($aContent['og:title']->getAccess(), 1, 1) == \Defines\User\Access::AUDIT): ?>
        <section class="el_panel bs_recess bg_mask">
            <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_PUBLIC') ?></header>
            <p><?php echo $translate->sys('LB_PERSON_WAIT_AUDIT') ?></p>
        </section>
    <?php else: ?>
    <p class="clear indent">
        <a id="ui-publicate" class="button bg_attention right"><?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?></a>
        <a id="ui-save" class="button bg_button left"><?php echo $translate->sys('LB_BUTTON_SAVE') ?></a>
        &nbsp;
    </p>
    <p>&nbsp;</p>
    <?php endif ?>

    <?php if ((string)$aContent['og:reply']->getContent()): ?>
    <section class="el_panel bs_recess bg_mask">
        <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_REJECTED') ?></header>
        <p><?php echo $aContent['og:reply']->getContent() ?></p>
        <div class="right button bg_attention ui" data-class="View/Height" data-actions="spoiler" data-target="closest:section:" data-status="1"><?php echo $translate->sys('LB_BUTTON_CONTINUE') ?></div>
        <p><small><?php echo $aContent['og:reply']->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?></small>&nbsp;</p>
    </section>
    <?php endif ?>

    <section class="el_panel el_border_dashed">
        <div class="el_grid">
            <div class="el_panel el_border_dashed" data-type="og:image" data-value="<?php echo $aContent['og:image']->getContent() ?>"><img src="<?php echo $aContent['og:image']->getContent() ?>" /></div>
            <div class="el_panel el_border_dashed el_width_full el_form">
                <form method="POST" class="ui" data-class="Request/Form" data-actions="disable">
                    <p><?php echo $translate->sys('LB_PERSON_TITLE') ?>:</p>
                    <input class="el_full" type="text" data-type="og:title" required value="<?php echo $aContent['og:title']->getContent() ?>" />
                    <div class="el_grid">
                        <span><?php echo $translate->sys('LB_PERSON_NAME') ?>:</span>
                        <input class="el_full" type="text" data-type="author" required value="<?php echo $aContent['author']->getContent() ?>" />
                    </div>
                    <p><?php echo $translate->sys('LB_PERSON_DESCRIPTION') ?>:</p>
                    <textarea rows="10" data-type="description" required placeholder="<?php echo $translate->sys('LB_PERSON_DESCRIPTION') ?>"><?php echo $aContent['description']->getContent() ?></textarea>
                    <p><?php echo $translate->sys('LB_PERSON_KEYWORDS') ?>:</p>
                    <input class="el_full" type="text" data-type="keywords" required value="<?php echo $aContent['keywords']->getContent() ?>" />
                </form>
            </div>
        </div>
    </section>

    <?php
    echo $this->partial('Stat/_compile', array(
        'data' => $aContent,
        'user' => $this->get('user'),
        'edit' => true
    ));
    ?>

    <section class="el_panel el_border bg_attention cr_pointer ui" data-class="Modules/Person" data-actions="add">
        <?php echo $translate->sys('LB_PERSON_ADD_BLOCK') ?>
    </section>

</article>

<div class="hidden" id="ui-elememts">
    <!-- Navigation elements for editing, moving and deleting current elements -->
    <div id="ui-navigation" class="el_grid">
        <a class="button bg_note cr_pointer nowrap fs_small ui" href="#" data-class="View/Href" data-actions="preventDefault" title="<?php echo $translate->sys('LB_BUTTON_EDIT') ?>">
            <span class="el_icon">&lowast;</span>
            <?php echo $translate->sys('LB_BUTTON_EDIT') ?>
        </a>
        <a class="button bg_button cr_move nowrap fs_small ui" href="#" data-class="View/Href" data-actions="preventDefault" title="<?php echo $translate->sys('LB_BUTTON_MOVE') ?>">
            <span class="el_icon">&udhar;</span>
            <?php echo $translate->sys('LB_BUTTON_MOVE') ?>
        </a>
        <a class="button bg_attention cr_pointer fs_small nowrap ui" href="#" data-class="View/Href" data-actions="preventDefault" title="<?php echo $translate->sys('LB_BUTTON_DELETE') ?>">
            <span class="el_icon">&Cross;</span>
            <?php echo $translate->sys('LB_BUTTON_DELETE') ?>
        </a>
    </div>
    <!-- Trap for a movements -->
    <section id="ui-trap" class="el_panel el_border_dashed" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person:trap">
        <?php echo $translate->sys('LB_PERSON_POSITION') ?>
    </section>
    <!-- List of available elements -->
    <section id="ui-list">
        <header class="bg_attention cr_default">
            <a class="right button bg_attention" id="ui-close">
                <?php echo $translate->sys('LB_BUTTON_CANCEL') ?>
                <span class="hidden"><?php echo $translate->sys('LB_PERSON_ADD_BLOCK') ?></span>
            </a>
            <?php echo $translate->sys('LB_PERSON_SELECT_BLOCK') ?>
        </header>
        <?php
        foreach (\Defines\Templates\Stat::getList() as $sTemp):
            echo $this->partial($sTemp, array('user' => $this->get('user'), 'edit' => true));
        endforeach;
        ?>
    </section>

    <?php echo $this->partial('Ui/image') ?>

    <!-- Error message -->
    <p id="ui-error"><?php echo $translate->sys('LB_ERROR_REQUIRED') ?></p>

</div>