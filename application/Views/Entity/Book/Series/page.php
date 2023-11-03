<?php
/* @var $this \Engine\Response\Template */
$self = $this;
$translate = \System\Registry::translation();

/* @var $bookTitle \Data\Doctrine\Main\Content */
$bookSeries = $this->get('entity');

$author = \Data\UserHelper::getUsername($bookSeries->getAuthor());
$read = $this->get('read');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemprop="mainEntity">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $bookSeries->getContent(),
        'title_href' => $this->getUrl($bookSeries->getPattern()),
        'subtitle' => $translate->sys('LB_OEUVRE_BOOK_SERIES'),
        'subtitle_href' => $this->getUrl('book/series')
    ));
    ?>
    <div class="indent clear">
        <!-- div class="el_border bg_form indent left">
            <img class="el_border" src="<?php echo $translate->get(['og:image', $bookSeries->getPattern()]) ?>" title="<?php echo $bookSeries->getContent() ?>" alt="<?php echo $bookSeries->getContent() ?>" />
        </div -->
        <div class="clear el_grid el_table_pair">
            <blockquote>
                <span class="clear-nowrap"><?php echo $translate->get(['description', $bookSeries->getPattern()]) ?></span>
                <q><a href="<?php echo $this->getUrl("person/$author") ?>"><?php echo $author ?></a></q>
            </blockquote>
        </div>

        <div class="indent">
            <footer class="clear el_footer ui" data-class="View/Keys" data-actions="init" data-url="<?php echo $this->getUrl('book/series', '') ?>">
                <?php echo $translate->get(['keywords', $bookSeries->getPattern()]) ?>
            </footer>
        </div>
        <?php
        foreach ($this->get('list') as $series):
            $o = $series->getContent();
            $img = $translate->get(['og:image', $o->getPattern()], $o->getLanguage());

            $bookRead = array();
            if (isset($read[(int) $o->getId()])):
                $bookRead[] = $read[(int) $o->getId()];
            endif;

            echo $this->partial('Basic/notion_plain', array(
                'author_txt' => $translate->get(['author', $o->getPattern()], $o->getLanguage()),
                'title' => $translate->get(['og:title', $o->getPattern()], $o->getLanguage()),
                'href' => $this->getUrl($o->getPattern(), null, $o->getLanguage()) . '" target="_blank',
                'pageCount' => $translate->get(['pageCount', $o->getPattern()], $o->getLanguage()),
                'book_style' => true,
                'async' => false,
                'img' => $img,
                'img_type' => $img,
                'entity' => $o,
                'read' => $bookRead,
                'list' => $this->get('read_list'),
                'origin' => true,
                'draggable' => false,
                'text' => $translate->get(['description', $o->getPattern()], $o->getLanguage()),
                'updated_at' => $translate->get(['date', $o->getPattern()], $o->getLanguage(), function($date) {
                    return substr($date, 0, 4);
                })
            ));
        endforeach;

        ?>
    </div>
</article>