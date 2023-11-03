<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>
    <article class="el_content">
        <?php
        echo $this->partial('Basic/title', array(
            'languages' => \Defines\Language::getList(),
            'subtitle' => $translate->get(['og:title', 'book']),
            'subtitle_href' => $this->getUrl('book')
        ));
        ?>
        <section class="clear indent">
            <section class="el_ui_table ui" data-class="Ui/Table" data-actions="init">
                <header class="el_ui_table_row">
                    <span title="<?php echo $translate->sys('LB_STAT_BOOK_DATE') ?>" class="el_ui_table_cell" data-flex="12"><?php echo $translate->sys('LB_STAT_BOOK_DATE') ?></span>
                    <span title="<?php echo $translate->sys('LB_STAT_BOOK_TITLE') ?>" class="el_ui_table_cell" data-flex="43"><?php echo $translate->sys('LB_STAT_BOOK_TITLE') ?></span>
                    <span title="<?php echo $translate->sys('LB_BOOK_AUTHOR') ?>" class="el_ui_table_cell" data-flex="25"><?php echo $translate->sys('LB_BOOK_AUTHOR') ?></span>
                    <span title="<?php echo $translate->sys('LB_BOOK_DATE') ?>" class="el_ui_table_cell" data-flex="10"><?php echo $translate->sys('LB_BOOK_DATE') ?></span>
                    <span title="<?php echo $translate->sys('LB_STAT_BOOK_MARK') ?>" class="el_ui_table_cell" data-flex="10"><?php echo $translate->sys('LB_STAT_BOOK_MARK') ?></span>
                </header>
                <?php
                /* @var $o \Data\Doctrine\Main\BookRead */
                foreach ($this->get('list') as $o):
                    $date = $o->getUpdatedAt()->format(Defines\Database\Params::DATE_FORMAT);
                    $book = $o->getBook()->getContent();
                    ?><section class="indent el_ui_table_row">
                        <span class="el_ui_table_cell el_max_width">
                            <span class="cr_pointer ui" data-class="Modules/Person/Stat/Book" data-actions="date" data-id="<?php echo $o->getId() ?>" data-date="<?php echo $date ?>">
                                <?php echo $date ?>
                                <img src="<?php echo (new \System\Minify\Images)->get() ?>css/el_box/write.gif" />
                            </span>
                        </span>
                        <a class="el_ui_table_cell" href="<?php echo $this->getUrl($book->getPattern(), null, $book->getLanguage()) ?>">
                            <?php if ($o->getStatus() === \Defines\Database\BookCategory::DELETE):
                                echo '[', \Defines\Database\BookCategory::getIcon($o->getStatus()), '] ';
                            endif;
                            echo $translate->get(['og:title', $book->getPattern()], $book->getLanguage());
                            ?>
                        </a>
                        <span class="el_ui_table_cell">
                            <?php
                            $authors = explode(',', $o->getBook()->getAuthor());
                            foreach ($authors as $i => $name):
                                $name = trim($name);
                                $authors[$i] = '<a href="' . $this->getUrl('book/overview/author/' . $name) . '">' . $name . '</a>';
                            endforeach;
                            echo implode(', ', $authors);
                            ?>
                        </span>
                        <span class="el_ui_table_cell"><?php
                        if ($o->getBook()->getYear()):
                            echo $o->getBook()->getYear();
                        else:
                            // Fix missing year and take it from content
                            echo $translate->get(['date', $book->getPattern()], $book->getLanguage(), function($date) use ($o) {
                                $b = $o->getBook();
                                $b->setYear(substr($date, 0, 4));
                                \System\Registry::connection()->persist($b);
                                \System\Registry::connection()->flush($b);
                                return $b->getYear();
                            });
                        endif;
                        ?></span>
                        <span class="el_ui_table_cell el_max_width">
                            <?php
                            $mark = $translate->get(
                                ['mark-' . \System\Registry::user()->getEntity()->getId(), $book->getPattern()],
                                $book->getLanguage(),
                                function($mark) {
                                    if ($mark[0] === '{') {
                                        $mark = '?';
                                    }
                                    return $mark;
                                }
                            );
                            ?>
                            <span class="cr_pointer ui" data-class="Modules/Person/Stat/Book" data-actions="mark" data-id="<?php echo $book->getId() ?>" data-mark="<?php echo $mark ?>">
                                <img class="right" src="<?php echo (new \System\Minify\Images)->get() ?>css/el_box/write.gif" />
                                <?php echo $mark ?>
                            </span>
                        </span>
                    </section><?php
                endforeach;
                ?>
            </section>
        </section>

        <?php echo $this->partial('Basic/Nav/pages') ?>
    </article>
</article>