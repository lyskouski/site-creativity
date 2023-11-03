<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<section>
    <search id="read_list">
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
    </search>
    <?php
    echo $this->partial('calendar/stat', array(
        'list' => $this->get('list'),
        'pages/day' => $this->get('pages/day'),
        'search' => true
    ));
    ?>

    <search id="list_name">
        <div class="bg_highlight el_border">
            <a class="co_approve" href="<?php echo $this->getUrl($this->get('entity')->getPattern()) ?>"><?php echo $this->get('entity')->getContent() ?></a>
        </div>
    </search>
</section>