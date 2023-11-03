<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oValid = new \Access\Validate\Check();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemscope itemtype="http://schema.org/Article">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_BOOK_LIST'),
        'languages' => \Defines\Language::getList()
    ));
    $model = new \Modules\Book\Calendar\Model();

    if ($this->get('list')):
        ?><section class="clear el_ui_table ui" data-class="Ui/Table" data-actions="init">
            <header class="el_ui_table_row">
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_NAME') ?>" class="el_ui_table_cell" data-flex="30"><?php echo $translate->sys('LB_BOOK_LIST_NAME') ?></span>
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_SUMMARY') ?>" class="el_ui_table_cell bg_form center" data-flex="5" title="<?php echo $translate->sys('LB_BOOK_LIST_SUMMARY') ?>">
                    <small>&sum;</small>
                </span>
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_READ') ?>" class="el_ui_table_cell bg_accepted center" data-flex="5" title="<?php echo $translate->sys('LB_BOOK_LIST_READ') ?>">
                    <?php echo \Defines\Database\BookCategory::getIcon(\Defines\Database\BookCategory::READ) ?>
                </span>
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_WISH') ?>" class="el_ui_table_cell bg_form center" data-flex="5" title="<?php echo $translate->sys('LB_BOOK_LIST_WISH') ?>">
                    <?php echo \Defines\Database\BookCategory::getIcon(\Defines\Database\BookCategory::WISHLIST) ?>
                </span>
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_FINISH') ?>" class="el_ui_table_cell bg_note center" data-flex="5" title="<?php echo $translate->sys('LB_BOOK_LIST_FINISH') ?>">
                    <?php echo \Defines\Database\BookCategory::getIcon(\Defines\Database\BookCategory::FINISH) ?>
                </span>
                <!-- span title="<?php echo $translate->sys('LB_BUTTON_MODIFY') ?>" class="el_ui_table_cell txt_right fixed" data-flex="2">&nbsp;</span -->
                <span title="<?php echo $translate->sys('LB_BOOK_LIST_DESC') ?>" class="el_ui_table_cell" data-rowspan="2" data-flex="2,48"><?php echo $translate->sys('LB_BOOK_LIST_DESC') ?></span>
            </header>
            <?php
            /* @var $o \Data\Doctrine\Main\Content */
            foreach ($this->get('list') as $i => $o):
                /* @var $oStat \Data\Doctrine\Main\ContentViews */
                $oStat = (new \Data\ContentHelper)->getRepository()->getPages($o->getPattern());
                $iRead = $model->getList($o->getId(), \Defines\Database\BookCategory::READ, true);
                // Description
                $oDesc = $translate->entity(['description', $o->getPattern()], $o->getLanguage());
                $desc = trim($oDesc->getContent());
                ?>
                <section class="indent el_ui_table_row">
                    <a class="el_ui_table_cell" href="<?php echo $this->getUrl($o->getPattern(), null, $o->getLanguage()) ?>"><?php echo $o->getContent() ?></a>
                    <span class="el_ui_table_cell bg_form txt_right"><?php echo $oStat->getContentCount() - 1 ?></span>
                    <span class="el_ui_table_cell bg_accepted txt_right"><?php echo $iRead ?></span>
                    <span class="el_ui_table_cell bg_form txt_right"><?php echo $model->getList($o->getId(), \Defines\Database\BookCategory::WISHLIST, true) ?></span>
                    <span class="el_ui_table_cell bg_note txt_right"><?php echo $model->getList($o->getId(), \Defines\Database\BookCategory::FINISH, true) ?></span>
                    <span title="<?php echo $translate->sys('LB_BUTTON_MODIFY') ?>" class="el_ui_table_cell cr_pointer button txt_right fixed">
                        <img src="<?php echo (new \System\Minify\Images)->get() ?>css/el_box/write.gif" class="ui" data-class="Modules/Book/Calendar" data-actions="note" data-note="<?php echo $i ?>" data-id="<?php echo $oDesc->getId() ?>" data-href="<?php echo $this->getUrl($o->getPattern(), \Defines\Extension::JSON) ?>" />
                    </span>
                    <span title="<?php echo $desc ?>" class="el_ui_table_cell ui-note"><?php echo $desc ?></span>
                </section>
                <?php
            endforeach;
            ?>
        </section><?php
    endif;
    ?>

    <section class="el_form indent">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('book/calendar', \Defines\Extension::JSON) ?>">
            <p class="el_grid">
                <span><?php echo $translate->sys('LB_BOOK_LIST') ?>:</span>
                <input autocomplete="off" type="text" name="title" placeholder="<?php echo $translate->sys('LB_BOOK_LIST') ?>..." />
            </p>
            <input type="hidden" name="action" value="create" />
            <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CREATE') ?>">
        </form>
    </section>
    <?php
    echo $this->partial('Basic/Desc/dynamic', array(
        'url' => 'book/calendar'
    ));
    ?>
</article>