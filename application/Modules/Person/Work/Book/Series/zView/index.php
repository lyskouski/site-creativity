<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'subtitle' => $translate->sys('LB_OEUVRE_BOOK_SERIES'),
        'subtitle_href' => $this->getUrl($this->get('title_href'))
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('title_href'), \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="<?php echo $this->get('action', 'create') ?>" />
            <div class="el_grid_normalized">
                <div class="el_grid_top">
                    <?php if ($this->get('list')): ?>
                    <div>
                        <div class="indent_vertical left"><?php echo $translate->sys('LB_PATTERN') ?>:</div>
                        <select class="ui" data-class="Ui/Element" data-actions="select" data-callback="Modules/Person/Work/Book/Series:setByPattern">
                            <option disabled selected="selected"><?php echo $translate->sys('LB_BOOK_LIST_NAME') ?>...</option>
                            <?php
                            /* @var $o \Data\Doctrine\Main\Content */
                            foreach ($this->get('list') as $o):
                                ?><option value="<?php echo $o->getPattern() ?>"><?php echo $o->getContent() ?></option><?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <?php endif ?>
                    <p class="el_grid indent_vertical">
                        <span><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</span>
                        <input class="ui-select-width" autocomplete="off" type="text" required name="og:title" value="<?php echo $this->get('og:title') ?>" placeholder="<?php echo $translate->sys('LB_ARTICLE_TITLE') ?>..." />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_CATEGORY') ?>:</span>
                        <input autocomplete="off" type="text" name="keywords" value="<?php echo $this->get('keywords', '') ?>" placeholder="<?php echo $translate->sys('LB_BOOK_CATEGORY_DESC') ?>..." />
                    </p>
                    <p class="indent_vertical">
                        <?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?><br />
                        <textarea rows="4" required placeholder="<?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?>..." name="description"><?php echo $this->get('description') ?></textarea>
                    </p>
                </div>
                <div class="w-2p">&nbsp;</div>
                <div class="w-10p">
                    <div class="el_grid_top ui" data-class="Modules/Person/Work" data-actions="image" name="keywords" data-value="<?php echo $this->get('og:image') ?>" >
                        <input type="hidden" name="og:image" value="<?php echo $this->get('og:image') ?>" />
                        <div class="hidden">
                            <?php echo $this->partial('Ui/image') ?>
                        </div>
                        <img class="el_width_full bg_mask el_border cr_pointer" src="<?php echo $this->get('og:image') ?>" title="<?php echo $translate->sys('LB_ARTICLE_CHANGE_IMAGE') ?>" />
                    </div>
                </div>
            </div>
            <input type="hidden" name="content#0" value="<?php echo $this->get('content#0') ?>" />
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CONTINUE') ?>" />
        </form>
    </section>
</article>