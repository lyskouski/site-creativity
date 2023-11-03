<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$title = $this->get('title', $translate->sys('LB_CONTENT_SEARCH'));
$imgPath = new \System\Minify\Images();

$request = new \Engine\Request\Input();
$sort = $request->getPost(\Access\Request\Search::SORT);
$sortType = $request->getPost(\Access\Request\Search::SORT_TYPE, 0);
$split = $request->getCookie(\Access\Request\Search::SPLIT);

$sortList = array_merge(
    array(
        'new' => $translate->sys('LB_SORT_NEW'),
        'rating' => $translate->sys('LB_SORT_RATING'),
        'view' => $translate->sys('LB_SORT_VIEW')
    ),
    $this->get('sort', [])
);
?>
<div class="indent clear el_form">
    <p><?php echo $this->get('desc', $translate->get(['description', $request->getUrl(null)])) ?></p>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('url'), \Defines\Extension::JSON) ?>">
        <input type="hidden" name="action" value="<?php echo $this->get('search_action', 'search') ?>" />
        <input type="hidden" name="<?php echo \Access\Request\Search::SORT_TYPE ?>" value="<?php echo $sortType ?>" />
        <div class="el_grid">
            <span><?php echo $translate->sys('LB_BUTTON_SEARCH') ?></span>
            <input type="text" name="search" placeholder="<?php echo $title ?>" value="<?php echo $this->get('search', '') ?>" />
            <div>&nbsp;</div>
            <div class="el_table_newline indent ui-select-width" data-width="240">
                <span class="ui-select-width"><?php echo $translate->sys('LB_SORTING_TYPE') ?>:&nbsp;</span>
                <div class="indent_neg_inline inline">
                    <select name="<?php echo \Access\Request\Search::SORT ?>" class="ui" data-class="Ui/Element" data-actions="select" data-autosubmit="true" data-direction="true">
                        <?php
                        foreach ($sortList as $key => $title):
                            ?><option <?php echo $sort === $key ? ' selected="selected"' : '' ?> value="<?php echo $key ?>"><?php echo $title ?></option><?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <?php
            if ($this->get('sort_menu_type', null) === false):
                // Disable filter type
            elseif ($this->get('sort_menu_type')):
                $options = $this->get('sort_menu_type');
                ?>
                <div class="el_table_newline indent ui-select-width" data-width="240">
                    <span class="ui-select-width"><?php echo $options['title'] ?>:&nbsp;</span>
                    <div class="indent_neg_inline inline">
                        <select name="<?php echo $options['name'] ?>" class="ui w-240" data-class="Ui/Element" data-actions="select" data-autosubmit="true">
                            <?php
                            foreach ($options['list'] as $key => $title):
                                ?><option <?php echo $sort === $key ? ' selected="selected"' : '' ?> value="<?php echo $key ?>"><?php echo $title ?></option><?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            else:
                ?>
                <div class="el_table_newline indent ui-select-width" data-width="240">
                    <span class="ui-select-width"><?php echo $translate->sys('LB_PRESENT_TYPE') ?>:&nbsp;</span>
                    <div class="indent_neg_inline inline">
                        <select name="<?php echo \Access\Request\Search::SPLIT ?>" class="ui w-240" data-class="Ui/Element" data-actions="select" data-autosubmit="true">
                            <option value="tile" <?php echo $split !== 'plain' ? ' selected="selected"' : '' ?> data-image="<?php echo $imgPath->get() ?>css/el_box/split-tile.png"><?php echo $translate->sys('LB_SPLIT_TILE') ?></option>
                            <option value="plain" <?php echo $split === 'plain' ? ' selected="selected"' : '' ?> data-image="<?php echo $imgPath->get() ?>css/el_box/split-plain.png"><?php echo $translate->sys('LB_SPLIT_PLAIN') ?></option>
                        </select>
                    </div>
                </div>
                <?php
            endif;
            ?>
            <div>&nbsp;</div>
        </div>
        <?php echo $this->get('extra_search_data') ?>
        <input class="bg_button" type="submit" value="<?php echo $translate->sys('LB_BUTTON_SEARCH') ?>" />
    </form>
</div>
<p>&nbsp;</p>