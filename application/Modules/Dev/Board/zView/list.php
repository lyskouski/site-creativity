<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$type = $this->get('type');
$imgPath = new \System\Minify\Images();
?>
<div class="el_grid_top <?php echo $this->get('class') ?> ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Dev/Board:add" data-type="<?php echo $type ?>">
    <?php if ($this->get('type') === \Defines\Database\BoardCategory::FINISH): ?>
    <header class="indent el_border bg_attention ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Dev/Board:add" data-type="<?php echo \Defines\Database\BookCategory::DELETE ?>">
        <h3><?php echo $translate->sys('LB_DEV_BOARD_DELETE') ?></h3>
        <div>
            <?php echo $translate->sys('LB_DEV_BOARD_DELETE_DESC') ?>
        </div>
    </header>
    <?php endif ?>

    <header class="indent el_border <?php echo $this->get('header') ?> ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Dev/Board:add" data-type="<?php echo $type ?>">
        <h3><?php echo $this->get('title') ?></h3>
    </header>
    <div class="el_table_pair">
        &nbsp;
        <?php
        $i = 1;
        $list = $this->get('list');
        /* @var $o \Data\Doctrine\Main\Content */
        foreach ($list[\Defines\Content\Attribute::TYPE_TITLE] as $url => $o):
            ?>
            <div class="el_table nowrap indent ui" data-class="View/DragDrop" data-actions="drag,drop" data-pattern="<?php echo $url ?>" data-callback="Modules/Dev/Board:add" data-type="<?php echo $type ?>">
                <div class="left el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $list['image'][$url] ?>" data-proc="1" data-max="1">
                    <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="cr_move el_circle_head" />
                    <div class="el_circle_num"></div>
                </div>
                <div>
                    &nbsp;<a href="<?php echo $this->getUrl($url) ?>"><?php echo $translate->get(['og:title', $url]) ?></a>
                </div>
                <span>&nbsp;<?php echo \Data\UserHelper::getUsername($o->getAuthor()) ?></span>
            </div>
            <?php
            $i++;
        endforeach;

        for ($i; $i < 6; $i++):
            ?>
            <div class="el_table indent ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Dev/Board:add" data-type="<?php echo $type ?>">
                <?php echo $i ?>. ...
            </div>
            <?php
        endfor;
        ?>
    </div>
</div>