<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>
    <article class="el_content">
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_BOOK_RECITE_LOAD'),
            'sub_languages' => \Defines\Language::getList()
        ));

        if ($this->get('message')):
            ?><div class="el_border indent co_accepted"><?php echo $this->get('message') ?></div><?php
        endif;
        ?>
        <section class="indent el_form">
            <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('book/recite/import', \Defines\Extension::JSON) ?>">
                <input type="hidden" name="action" value="manual" />
                <p><?php echo $translate->sys('LB_BOOK_RECITE_SINGLE') ?>:</p>
                <textarea name="quote"></textarea>
                <p class="el_grid">
                    <span><?php echo $translate->sys('LB_BOOK_ISBN') ?>:</span>
                    <input autocomplete="off" type="text" required name="isbn" value="<?php echo $this->get('isbn') ?>" />
                    <span>&nbsp;</span>
                    <span><?php echo $translate->sys('LB_BOOK_PAGE') ?>:</span>
                    <input autocomplete="off" type="text" required name="page" />
                </p>
                <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CREATE') ?>" />
            </form>
        </section>
        <br />
        <p><?php echo sprintf($translate->sys('LB_BOOK_RECITE_DEVICE_IMPORT'), '<b>Pocket Book</b> (www.pocketbook-int.com)', '<b>~/system/config/Active Contents/</b>'); ?></p>
        <section class="indent el_form">
            <form method="POST" class="ui" data-class="Request/Form" data-actions="init" data-stream="true" action="<?php echo $this->getUrl('book/recite/import', \Defines\Extension::JSON) ?>">
                <input type="hidden" name="action" value="pocketbook" />
                <div class="el_grid">
                    <span><?php echo $translate->sys('LB_BOOK_ISBN') ?>:</span>
                    <input autocomplete="off" type="text" required name="isbn" value="<?php echo $this->get('isbn') ?>" />
                    <span>&nbsp;</span>
                    <div><input type="file" name="content" /></div>
                </div>
                <p class="el_grid">
                    <span class="indent"><?php echo $translate->sys('LB_BUTTON_IMPORT') ?>:</span>
                    <span class="el_table_newline bg_mask">&nbsp;<input class="el_radio_col" type="radio" name="type" value="all" checked="checked" id="mark-all" /><label for="mark-all"><?php echo $translate->sys('LB_CONTENT') . ' + ' . $translate->sys('LB_BOOK_RECITE_SINGLE') ?></label></span>
                    <span class="el_table_newline bg_mask">&nbsp;<input class="el_radio_col" type="radio" name="type" value="content" id="mark-content" /><label for="mark-content"><?php echo $translate->sys('LB_CONTENT') ?></label></span>
                    <span class="bg_mask">&nbsp;<input class="el_radio_col" type="radio" name="type" value="quote" id="mark-quote" /><label for="mark-quote"><?php echo $translate->sys('LB_BOOK_RECITE_SINGLE') ?></label></span>
                </p>
                <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_IMPORT') ?>" />
            </form>
        </section>
    </article>
</article>