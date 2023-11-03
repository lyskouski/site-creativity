<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
$type = $this->get('type');
$list = $this->get('list');
?>
<div class="w-mm-100 el_grid_top <?php echo $this->get('class') ?> ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Book/Calendar:add" data-type="<?php echo $type ?>" data-pos="<?php echo sizeof($list) + 1 ?>">

    <?php if ($this->get('type') === \Defines\Database\BookCategory::FINISH): ?>
    <header class="indent el_border bg_attention ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Book/Calendar:add" data-type="<?php echo \Defines\Database\BookCategory::DELETE ?>" data-pos="0">
        <h3><a class="co_highlight" href="<?php echo $this->getUrl($this->get('url')) ?>"><?php echo $translate->sys('LB_BOOK_LIST_DELETE') ?></a></h3>
        <div>
            <?php echo $translate->sys('LB_BOOK_LIST_DELETE_DESC') ?>
        </div>
    </header>
    <?php endif ?>

    <header class="indent el_border <?php echo $this->get('header') ?> ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Book/Calendar:add" data-type="<?php echo $type ?>" data-pos="0">
        <?php if ($this->get('type') === \Defines\Database\BookCategory::READ): ?>
        <aside class="right unselectable el_radio_block ui" data-class="Modules/Book/Calendar" data-actions="pcnt" title="<?php echo $translate->sys('LB_BOOK_CALENDAR_PAGE_VIEW') ?>">
            <input type="checkbox" name="view_type" class="el_radio_switch" id="radio_switch" checked data-unchecked="<?php echo $translate->sys('LB_PAGE') ?>" data-checked="%" />
            <label class="el_radio_switch_lb" for="radio_switch">%</label>
        </aside>
        <?php elseif ($this->get('type') === \Defines\Database\BookCategory::WISHLIST): ?>
        <aside class="right unselectable el_radio_block ui" data-class="Modules/Book/Calendar" data-actions="list" title="<?php echo $translate->sys('LB_BOOK_CALENDAR_LIST_VIEW') ?>">
            <input type="checkbox" name="view_list" class="el_radio_switch" id="radio_list" checked data-unchecked="&top;" data-checked="&dashv;" />
            <label class="el_radio_switch_lb" for="radio_list">&dashv;</label>
        </aside>
        <?php endif ?>
        <h3><?php echo $this->get('title') ?></h3>
    </header>
    <div class="el_table_pair">
        &nbsp;
        <?php
        $i = 1;
        /* @var $o \Data\Doctrine\Main\BookRead */
        foreach ($list as $o):
            if ($o->getStatus() != $this->get('type')):
                continue;
            endif;
            $url = $o->getBook()->getContent()->getPattern();
            $lang = $o->getBook()->getContent()->getLanguage();
            if ($o->getQueue() !== $i):
                $o->setQueue($i);
                // Define time
                $time = $o->getUpdatedAt()->getTimestamp() + mt_rand(-2, 2);
                $o->setUpdatedAt((new \DateTime)->setTimestamp($time));

                \System\Registry::connection()->persist($o);
            endif;
            ?>
            <div class="el_table nowrap indent ui" data-class="Modules/Book/Calendar" data-actions="move" data-isbn="<?php echo $o->getBook()->getId() ?>" data-callback="Modules/Book/Calendar:add" data-type="<?php echo $type ?>" data-pos="<?php echo $o->getQueue() ?>">
                <div class="left el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $translate->get(['og:image', $url], $lang, $imgPath->adaptWork($url, '_type', 'work/book_type.svg')) ?>" data-proc="<?php echo $o->getPage() ?>" data-max="<?php echo $o->getBook()->getPages() ?>">
                    <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="cr_move el_circle_head" />
                    <div class="el_circle_num"><?php echo $o->getBook()->getPages() ?></div>
                </div>
                <div>
                    &nbsp;<a href="<?php echo $this->getUrl($url, null, $lang) ?>"><?php echo $translate->get(['og:title', $url], $lang) ?></a>
                    <?php
                    if ($this->get('type') === \Defines\Database\BookCategory::READ):
                        echo '(', $translate->get(['author', $url], $lang), ')';
                    endif;
                    ?>
                </div>
                <?php if ($this->get('type') === \Defines\Database\BookCategory::READ): ?>
                <div class="ui ui-pagination hidden left indent" data-class="Modules/Book/Calendar" data-actions="pagination">
                    <sup class="right indent_neg_right">
                        <input class="txt_right" type="text" value="<?php echo $o->getPage() ?>" size="3" />
                        <span><?php echo $translate->sys('LB_PAGE') ?></span>
                    </sup>
                    <progress min="0" max="<?php echo $o->getBook()->getPages() ?>" value="<?php echo $o->getPage() ?>"></progress>
                    <input class="transparent cr_pointer" type="range" min="0" max="<?php echo $o->getBook()->getPages() ?>" data-max="<?php echo $o->getBook()->getPages() ?>" value="<?php echo $o->getPage() ?>" />
                </div>
                <?php
                else:
                    ?><span>&nbsp;<?php echo $translate->get(['author', $url], $lang) ?></span><?php
                endif;
                $i++;
                ?><div class="el_border hidden" data-class="Modules/Book/Calendar" data-type="<?php echo $type ?>" data-pos="<?php echo $i ?>"></div>
            </div>
            <?php
            $i++;
        endforeach;

        for ($i; $i < 6; $i++):
            ?>
            <div class="el_table indent ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Book/Calendar:add" data-type="<?php echo $type ?>" data-pos="<?php echo $i ?>">
                <?php echo $i ?>. ...
            </div>
            <?php
        endfor;
        ?>
    </div>
</div>
<?php
\System\Registry::connection()->flush();