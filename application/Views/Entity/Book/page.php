<?php /* @var $this \Engine\Response\Template */
$self = $this;
$translate = \System\Registry::translation();

/* @var $bookTitle \Data\Doctrine\Main\Content */
$bookTitle = $this->get('entity');
$title = $translate->get(['og:title', $bookTitle->getPattern()]);

$bookUrl = $this->getUrl($bookTitle->getPattern());
$bookLD = new \Engine\Response\JsonLd\Book();
\System\Registry::structured()->append(
    $bookLD->getAttributes($bookTitle, \System\Registry::stat())
);

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Book">
    <?php
    echo $this->partial('Entity/Book/nav_list', array(
       'url' => $bookTitle->getPattern(),
       'url_active' => $bookTitle->getPattern()
    ));

    echo $this->partial('Basic/title', array(
        'title' => $title,
        'title_href' => $this->getUrl($bookTitle->getPattern()),
        'languages' => \Defines\Language::getContentList($bookTitle->getPattern())
    ));
    ?>
    <meta itemprop="inLanguage" content="<?php echo $bookTitle->getLanguage() ?>" />
    <div class="el_grid_normalized">
        <div class="indent el_grid_top w-20p">
            <img itemprop="image" class="el_width_full bg_mask el_border cr_pointer ui-select-width" src="<?php echo $translate->get(['og:image', $bookTitle->getPattern()]) ?>" title="<?php echo $title ?>" />
            <?php echo $this->partial('Entity/Book/state') ?>
        </div>
        <div class="el_grid_top el_table_newline">
            <div class="indent">
                <p>
                    <strong><?php echo $translate->sys('LB_BOOK_AUTHOR') ?>:</strong><br />
                    <span class="clear-nowrap"><?php
                        $authors = explode(',', $this->get('author'));
                        \System\Registry::structured()->add($bookLD->getAuthorList($authors));
                        foreach ($authors as $i => $name):
                            $name = trim($name);
                            $authors[$i] = '<a href="' . $self->getUrl('book/overview/author/' . $name) . '" itemprop="author">' . $name . '</a>';
                        endforeach;
                        echo implode(', ', $authors);
                    ?></span>
                </p>
                <p>&nbsp;</p>
                <p>
                    <strong><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</strong><br />
                    <span itemprop="name" class="clear-nowrap"><?php echo $title ?></span>
                </p>
                <p>&nbsp;</p>
                <p>
                    <strong><?php echo $translate->sys('LB_BOOK_CATEGORY') ?>:&nbsp;</strong>
                    <span class="clear-nowrap"><?php
                    $keywords = $translate->entity(['keywords', $bookTitle->getPattern()])->getContent();
                    $a = explode(',', $keywords);
                    foreach ($a as $i => $name):
                        if ($i):
                            echo ', ';
                        endif;
                        $name = trim($name);
                        ?><a href="<?php echo $this->getUrl('book/overview/search/' . $name) ?>" itemprop="genre"><?php echo $name ?></a><?php
                    endforeach;
                    ?></span>
                </p>
                <?php
                if ($this->get('series')):
                    ?><p>&nbsp;</p>
                    <p><strong><?php echo $translate->sys('LB_CONTENT_SERIES') ?>:</strong></p>
                    <div class="el_table_pair indent_vertical">
                        <?php
                        /* @var $series \Data\Doctrine\Main\ContentSeries */
                        foreach ($this->get('series') as $series):
                            ?><div class="el_table indent"><a href="<?php echo $this->getUrl($series->getSeries()->getPattern()) ?>"><?php
                                echo $series->getSeries()->getContent();
                            ?></a></div><?php
                        endforeach;
                        ?>
                    </div>
                    <?php

                elseif (\System\Registry::user()->checkAccess('dev/tasks/auditor', 'index')):
                    ?><p>&nbsp;</p>
                    <p><strong><?php echo $translate->sys('LB_CONTENT_SERIES') ?>:</strong></p><br />
                    <a class="button bg_accepted" href="<?php echo $this->getUrl('person/work/book/series') ?>"><?php echo $translate->sys('LB_BUTTON_CREATE') ?></a>
                    <p>&nbsp;</p>
                    <?php
                else:
                    ?><p>&nbsp;</p><?php
                endif;
                ?>
                <p><strong><?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?>:</strong></p>
                <p class="clear-nowrap" itemprop="about"><?php echo $translate->get(['description', $bookTitle->getPattern()]) ?></p>
                <p>&nbsp;</p>
                <p>
                    <strong><?php echo $translate->sys('LB_BOOK_DATE') ?>:&nbsp;</strong>
                    <span itemprop="datePublished"><?php echo (int) $translate->entity(['date', $bookTitle->getPattern()])->getContent() ?></span>
                </p>
                <div data-isbn="<?php echo $translate->get(['isbn', $bookTitle->getPattern()]) ?>">
                    <?php
                    if (!$this->get('read') || !($readList = current($this->get('read'))) || $readList->getStatus() != \Defines\Database\BookCategory::READ):
                        ?>
                        <strong><?php echo $translate->sys('LB_BOOK_PAGES') ?>:&nbsp;</strong>
                        <span itemprop="numberOfPages"><?php echo $translate->get(['pageCount', $bookTitle->getPattern()]) ?></span>
                        <?php
                    else:
                        ?>
                        <div class="left">
                            <strong><?php echo $translate->sys('LB_BOOK_PAGES') ?>:&nbsp;</strong>
                            <span itemprop="numberOfPages"><?php echo $translate->get(['pageCount', $bookTitle->getPattern()]) ?></span>
                        </div>
                        <div class="left hidden indent ui-pagination ui" data-class="Modules/Book/Calendar" data-actions="pagination">
                            <sup class="right indent_neg_right">
                                <input class="txt_right" type="text" value="<?php echo $readList->getPage() ?>" size="3" />
                                <span><?php echo $translate->sys('LB_PAGE') ?></span>
                            </sup>
                            <progress min="0" max="<?php echo $readList->getBook()->getPages() ?>" value="<?php echo $readList->getPage() ?>"></progress>
                            <input class="transparent cr_pointer" type="range" min="0" max="<?php echo $readList->getBook()->getPages() ?>" data-max="<?php echo $readList->getBook()->getPages() ?>" value="<?php echo $readList->getPage() ?>" data-url="<?php echo $this->getUrl($readList->getContent()->getPattern(), \Defines\Extension::JSON) ?>" />
                        </div>
                        <p class="indent">&nbsp;</p>
                        <?php
                    endif;
                    ?>
                </div>
                <?php if (!is_null($this->get('mark'))): ?>
                    <div class="indent_vertical">
                        <p><strong><?php echo $translate->sys('LB_BOOK_RATING') ?>:&nbsp;</strong></p>
                        <div class="bg_highlight el_border indent w-200 center"><strong><?php
                        $mrk = $this->get('mark');
                        //for ($i = 0; $i < \Defines\Database\Params::MAX_USER_RATING; $i++):
                        //    echo $i < $mrk ? '&starf;' : '&star;';
                        //endfor;
                        echo number_format((float) $mrk, 1), ' ', $translate->sys('LB_PAGE_COUNT'), ' ', \Defines\Database\Params::MAX_USER_RATING;
                        echo " ({$this->get('cnt', 1)})";
                        ?></strong></div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="el_grid_gap">&nbsp;</div>
    </div>

    <section class="indent" itemprop="description">
                <?php if ($this->get('comments')): ?>
                    <p><strong><?php echo $translate->sys('LB_BOOK_COMMENTS') ?>:</strong></p>
                    <div class="el_table_pair indent_vertical">
                        <?php
                        /* @var $comment \Data\Doctrine\Main\Content */
                        foreach ($this->get('comments') as $comment):
                            $interval = (new \System\Converter\DateDiff)->getInterval($comment->getUpdatedAt()->diff(new \DateTime));
                            $key = substr($comment->getType(), strrpos($comment->getType(), '-') + 1);
                            $icon = '<span class="%s" title="%s">%s <small class="co_inactive">%s</small></span>';
                            switch ($key):
                                case 'down':
                                    $icon = sprintf($icon, 'co_attention', $translate->sys('LB_COMMENT_MARK_NEGATIVE'), '&cross;', $interval);
                                    break;
                                case 'up':
                                    $icon = sprintf($icon, 'co_accepted', $translate->sys('LB_COMMENT_MARK_POSITIVE'), '&check;', $interval);
                                    break;
                                default:
                                    $icon = sprintf($icon, 'co_accepted', '', '', $interval);
                            endswitch;
                            $username = \Data\UserHelper::getUsername($comment->getAuthor());
                            ?>
                            <blockquote class="el_table clear-nowrap">
                                <p class="indent"><?php echo $icon ?><br /><?php echo $comment->getContent() ?></p>
                                <q class="right indent_neg_right"><a target="_blank" href="<?php
                                        echo $this->getUrl('person/' . $username); ?>"><?php
                                        echo $username;
                                    ?></a></q>
                                <small>&nbsp;</small>
                            </blockquote>
                            <?php
                        endforeach;
                        ?>
                    </div>
                <?php endif ?>
                <p>&nbsp;</p>
                <?php echo $translate->get(['content#0', $bookTitle->getPattern()]) ?>
                <p>&nbsp;</p>
                <?php
                $isbn = $translate->entity(['isbn', $bookTitle->getPattern()])->getContent();
                if ($isbn > 0):
                    ?>
                    <p>
                        <strong><?php echo $translate->sys('LB_BOOK_ISBN') ?>:&nbsp;</strong>
                        <span itemprop="isbn"><?php echo $translate->get(['isbn', $bookTitle->getPattern()]) ?></span>
                    </p>
                    <p>
                        <strong><?php echo $translate->sys('LB_BOOK_UDC') ?>:&nbsp;</strong>
                        <span class="clear-nowrap"><?php
                            $udc = $translate->entity(['udc', $bookTitle->getPattern()])->getContent();
                            if ((!$udc || $udc[0] === '{') && \System\Registry::user()->checkAccess('book/overview', 'edit')):
                                ?><a class="co_attention ui" href="<?php echo $bookUrl ?>" data-class="Request/Pjax" data-actions="init" data-data="{'action':'udc'}">&olarr;&nbsp;<?php echo \System\Registry::translation()->sys('LB_BUTTON_REFRESH') ?></a><?php
                            else:
                                echo $udc;
                            endif;
                        ?></span>
                    </p>
                    <?php
                endif;
                ?>
        <p>&nbsp;</p>
        <?php echo $this->partial('Entity/share', array('class' => 'left')) ?>
    </section>
</article>