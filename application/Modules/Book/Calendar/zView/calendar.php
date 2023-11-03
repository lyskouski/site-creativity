<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => '<img src="' . (new \System\Minify\Images)->get() . 'css/el_box/write.gif" />' . $this->get('entity')->getContent(),
        'title_href' => $this->getUrl($this->get('href')) . '" class="ui" data-class="Request/Pjax" data-actions="init" data-data="{\'action\':\'edit\'}',
        'subtitle' => $translate->sys('LB_BOOK_LIST'),
        'subtitle_href' => $this->getUrl('book/calendar')
    ));

    echo $this->partial('Entity/Book/search');
    ?>

    <section class="el_grid ui" data-class="Modules/Book/Calendar" data-actions="order" id="read_list">
        <?php
        echo $this->partial('calendar/list', array(
            'list' => $this->get('list'),
            'title' => $translate->sys('LB_BOOK_LIST_WISH'),
            'type' => \Defines\Database\BookCategory::WISHLIST,
            'header' => 'bg_form'
        ));

        echo $this->partial('calendar/list', array(
            'list' => $this->get('list'),
            'title' => $translate->sys('LB_BOOK_LIST_READ'),
            'type' => \Defines\Database\BookCategory::READ,
            'header' => 'bg_accepted',
            'class' => 'el_border el_table_newline'
        ));

        echo $this->partial('calendar/list', array(
            'list' => $this->get('list'),
            'title' => $translate->sys('LB_BOOK_LIST_FINISH'),
            'type' => \Defines\Database\BookCategory::FINISH,
            'url' => 'book/calendar/trash/i'. $this->get('entity')->getId(),
            'header' => 'bg_note',
            'class' => 'el_table_newline'
        ));
        ?>
    </section>

    <section id="read_stat">
        <?php
        echo $this->partial('calendar/stat', array(
            'list' => $this->get('list'),
            'pages/day' => $this->get('pages/day')
        ));
        ?>
    </section>

    <?php
    echo $this->partial('Basic/title', array(
        'subtitle' => $translate->sys('LB_BOOK_STATISTICS'),
        'num' => 2
    ));
    ?>
    <section class="indent">
        <?php
        $graph = $this->get('statistics');
        if ($graph && sizeof($graph) > 1):
            ?><div class="ui" data-class="View/Graph" data-actions="summary" data-title="<?php echo $translate->sys('LB_STAT_PAGES') ?>"><div class="hidden"><?php echo json_encode($this->get('statistics')) ?></div></div><?php
        else:
            ?><p><?php echo $translate->sys('LB_BOOK_STATISTICS_MISSING') ?></p><?php
        endif;
        ?>
    </section>

    <?php
    echo $this->partial('Basic/title', array(
        'subtitle' => 'Notification calendar',
        'num' => 2
    ));
    ?>
    <section class="indent">
        ...
    </section>
</article>