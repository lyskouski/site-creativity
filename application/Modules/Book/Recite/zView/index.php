<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$tmp = $this;

echo $this->partial('Basic/Nav/crumbs');

if ($this->get('menu')): ?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>
<?php endif ?>
    <article class="el_content">
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_BOOK_RECITE'),
            'sub_languages' => \Defines\Language::getList()
        ));

        if (!$this->get('list')):
            ?><p class="indent"><?php echo $translate->sys('LB_CONTENT_IS_MISSING'); ?></p><?php
        endif;

        /* @var $el \Data\Doctrine\Main\Content */
        foreach ($this->get('list') as $el):
            $book = $el->getContent2();
            ?><div class="el_table nowrap indent">
                <p class="clear-nowrap"><?php echo $el->getContent() ?><p>
                <div class="nowrap bg_mask indent indent_neg_left indent_neg_bottom">
                    <a href="<?php echo $this->getUrl($book->getPattern()) ?>"><?php echo $translate->get(['og:title', $book->getPattern()]) ?></a>
                    &nbsp;/&nbsp;
                    <?php echo $translate->get(['author', $book->getPattern()], null, function($data) use ($tmp) {
                        $res = array();
                        foreach (explode(',', $data) as $auth) {
                            $res[] = '<a href="' . $tmp->getUrl('book/overview/author/' . $auth) . '">'. $auth . '</a>';
                        }
                        return implode(', ', $res);
                    }) ?>,&nbsp;<?php echo $translate->get(['date', $book->getPattern()], null, function($date) {
                        return substr($date, 0, 4);
                    }) ?>.&nbsp;&ndash;&nbsp;<?php echo substr($el->getPattern(), strlen($book->getPattern()) + 1) . ' ' . $translate->sys('LB_PAGE') ?>
                </div>
            </div><?php
        endforeach;

        //echo
        $this->partial('Basic/search', array(
            'url' => 'book/recite',
            'title' => $translate->sys('LB_BOOK_RECITE_SEARCH'),
            'search' => $this->get('search'),
            'sort_menu_type' => array(
                'name' => 'type',
                'title' => $translate->sys('LB_SEARCH_TYPE'),
                'list' => [
                    'content' => $translate->sys('LB_SEARCH_BY_CONTENT'),
                    'label' => $translate->sys('LB_SEARCH_BY_LABEL'),
                    'author' => $translate->sys('LB_SORT_BOOK_AUHOR'),
                    'title' => $translate->sys('LB_SORT_BOOK_TITLE')
                ]
            ),
            'extra_search_data' => '<div><input align="absmiddle name="mine" type="checkbox" /> ' . $translate->sys('LB_SEARCH_RECITE_MINE') . '</div>'
        ));

        ?>
    </article>
<?php if ($this->get('menu')): ?>
</article>
<?php endif;