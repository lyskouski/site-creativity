<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('og:title'),
        'title_href' => $this->getUrl($this->get('pattern')),
        'subtitle' => $translate->sys('LB_PERSON_DRAFT'),
        'subtitle_href' => $this->getUrl('/person/work')
    ));

    if ($this->get('og:reply')):
        ?>
        <section class="el_panel bs_recess bg_mask">
            <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_REJECTED') ?></header>
            <p><?php echo $this->get('og:reply') ?></p>
            <div class="right button bg_attention ui" data-class="View/Height" data-actions="spoiler" data-target="closest:section:" data-status="1"><?php echo $translate->sys('LB_BUTTON_CONTINUE') ?></div>
            <p>&nbsp;</p>
        </section>
        <p>&nbsp;</p>
        <?php
    endif;
    ?>

    <form method="POST" class="ui" data-class="Modules/Person/Work" data-actions="init" action="<?php echo $this->getUrl($this->get('pattern'), \Defines\Extension::JSON) ?>">
        <section class="el_form indent">
            <aside class="right">
                <?php if (\System\Registry::user()->isAdmin()): ?>
                <div class="button bg_note ui" data-class="Modules/Person/Work" data-actions="source"><?php echo $translate->sys('LB_STYLE_SOURCE') ?></div>
                <?php endif ?>
                <div class="button bg_attention" data-type="submit"><?php echo $translate->sys('LB_BUTTON_SAVE') ?></div>
                <a id="ui-approve-action" class="button bg_note ui" data-class="Modules/Person/Work" data-actions="before" href="<?php echo $this->getUrl($this->get('url') . '/approve/' . $this->get('id')) ?>">
                    <?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?>
                </a>
            </aside>
            <?php echo $this->get('description') ?>
        </section>

        <section class="indent">
            <?php
            /* @var $o \Data\Doctrine\Main\ContentNew */
            foreach ($this->get('list') as $o):
                if (strpos($o->getType(),'content#') === 0):
                    ?><section class="indent el_border bg_highlight el_A4 ui" data-class="Modules/Person/Work" data-actions="bindPage"  spellcheck="true" contenteditable="true" data-name="content[]"><?php echo $o->getContent() ?></section><?php
                endif;
            endforeach;

            if ($this->get('firstPage')):
                ?><section class="indent el_border bg_highlight el_A4 ui" data-class="Modules/Person/Work" data-actions="bindPage"  spellcheck="true" contenteditable="true" data-name="content[]"><?php echo $o->getContent() ?></section><?php
            endif;
            ?>

            <section class="center">
                <a class="button bg_note right" data-class="Modules/Person/Work" data-actions="before" href="<?php echo $this->getUrl($this->get('url') . '/approve/' . $this->get('id')) ?>">
                    <?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?>
                </a>
                <div class="button bg_attention left" data-type="submit"><?php echo $translate->sys('LB_BUTTON_SAVE') ?></div>
            </section>
            <p class="indent">&nbsp;</p>
        </section>
    </form>


    <div class="hidden" >
        <!-- Panel for editor -->
        <footer id="ui-editPanel-bm">
            <code class="el_grid">
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_LATEX_TEXTBF') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="bold">B</div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_LATEX_TEXTIT') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="italic"><em>I</em></div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_STYLE_UNDERLINE') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="underline"><u>U</u></div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_STYLE_CROSSED') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="crossed"><s>S</s></div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_STYLE_SUP') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="sup"><sup>&veebar;</sup></div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_STYLE_SUB') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="sub"><sub>&barvee;</sub></div>
                <div class="left">&nbsp;</div>
                <div class="button left bg_button ui" title="<?php echo $translate->sys('LB_STYLE_ACCENT') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="accent">&Aacute;</div>
                <div class="left">&nbsp;</div>
                <div class="button left bg_note ui" title="<?php echo $translate->sys('LB_STYLE_CLEAR') ?>" data-class="View/Popup/EditPanel" data-actions="apply" data-command="clear"><u>T</u><sub>&Cross;</sub></div>
            </code>
        </footer>

        <!-- Form for image editor -->
        <div id="ui-sample-image">
            <p class="el_grid">
                <span><?php echo $translate->sys('LB_IMAGE_SIZE') ?>:</span>
                <input type="text" name="width" value="320" />
                <span><?php echo $translate->sys('LB_IMAGE_SIZE_SEP') ?></span>
                <input type="text" name="height" value="auto" />
            </p>
            <p class="el_grid">
                <strong><?php echo $translate->sys('LB_STYLE_POSITION') ?>:</strong>
                <span><input class="el_radio_col" type="radio" name="position" value="left" checked="checked" id="position-left" /><label for="position-left"><?php echo $translate->sys('LB_STYLE_POS_LEFT') ?>&nbsp;</label></span>
                <span><input class="el_radio_col" type="radio" name="position" value="center" id="position-center" /><label for="position-center"><?php echo $translate->sys('LB_STYLE_POS_CENTER') ?>&nbsp;</label></span>
                <span><input class="el_radio_col" type="radio" name="position" value="right" id="position-right" /><label for="position-right"><?php echo $translate->sys('LB_STYLE_POS_RIGHT') ?>&nbsp;</label></span>
            </p>
            <p class="el_grid">
                <strong><?php echo $translate->sys('LB_STYLE_COVER') ?>:</strong>
                <span><input class="el_radio_col" type="radio" name="cover" value="clear" checked="checked" id="cover-clear" /><label for="cover-clear"><?php echo $translate->sys('LB_STYLE_COV_TEXT') ?>&nbsp;</label></span>
                <span><input class="el_radio_col" type="radio" name="cover" value="text" id="cover-text" /><label for="cover-text"><?php echo $translate->sys('LB_STYLE_COV_CLEAR') ?>&nbsp;</label></span>
            </p>
        </div>

        <!-- Buttons for image editor -->
        <div id="ui-buttons-prompt">
            <button class="button bg_button" data-type="prompt"><?php echo $translate->sys('LB_BUTTON_MODIFY') ?></button>
            <button class="button bg_attention" data-type="cancel"><?php echo $translate->sys('LB_BUTTON_CANCEL') ?></button>
        </div>

        <!-- Loading mask -->
        <header id="ui-mask">
            <form>
                <div class="el_grid width_auto">
                    <strong><?php echo $translate->sys('LB_ACTION_SAVE') ?>:</strong>
                    <progress class="ui-mask-status el_full" max="100" value="0"><?php echo $translate->sys('LB_ACTION_SAVE_PROCESS') ?></progress>
                </div>
                <div>
                    <?php echo $translate->sys('LB_ACTION_SAVE_ATTENTION') ?>
                </div>
            </form>
        </header>
    </div>
</article>