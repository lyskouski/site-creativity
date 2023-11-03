<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>

<section class="el_form indent">
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('href'), \Defines\Extension::JSON) ?>">
        <input id="search_area_type" type="hidden" name="type" value="" />
        <div class="el_grid_normalized">
            <div class="el_grid_top">
                <div class="indent">
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</span>
                        <input autocomplete="off" type="text" name="title" placeholder="<?php echo $translate->sys('LB_ARTICLE_TITLE') ?>..." />
                    </p>
                </div>
                <div class="indent">
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_AUTHOR') ?>:</span>
                        <input autocomplete="off" type="text" name="author" placeholder="<?php echo $translate->sys('LB_BOOK_AUTHOR') ?>..." />
                    </p>
                </div>
            </div>
            <div class="el_table_newline el_grid_normalized el_form">
                <div class="indent">
                    <p class="el_grid">
                        <span><span class="el_circle el_border bg_button"><?php echo $translate->sys('LB_OR') ?></span> <?php echo $translate->sys('LB_BOOK_ISBN') ?>:</span>
                        <input autocomplete="off" type="text" name="isbn" />
                    </p>
                </div>
            </div>
        </div>
        <input type="hidden" name="action" value="search" />
        <input type="hidden" name="language" value="<?php echo \System\Registry::translation()->getTargetLanguage() ?>" />
        <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_SEARCH') ?>" /><?php
        echo $this->partial('Basic/language', array(
            'languages' => \Defines\Language::getList(),
            'url' => null,
            'ui' => array(
                'class' => 'Modules/Book/Calendar',
                'actions' => 'setFormLanguage'
            )
        ));
        ?>
    </form>
</section>

<section id="search_option"></section>

<section id="search_list">
    <p class="indent">&nbsp;</p>
</section>