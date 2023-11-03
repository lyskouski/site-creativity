<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemscope itemtype="http://schema.org/Article">
    <p>&nbsp;</p>
    <?php
    /* @var $currentList \Data\Doctrine\Main\Content */
    $currentList = $this->get('entity');

    echo $this->partial('Basic/title', array(
        'title' => $currentList->getContent(),
        'title_url' => $this->getUrl($currentList->getPattern())
    ));

    /* @var $book \Data\Doctrine\Main\Book */
    $book = $this->get('book');
    $entity = $book->getContent();
    echo $this->partial('Basic/notion_plain', array(
        'author_txt' => $book->getAuthor(),
        'title' => $entity->getContent(),
        'href' => $this->getUrl($entity->getPattern()),
        'book_style' => true,
        'async' => false,
        'img_type' => $translate->get(['image', $entity->getPattern()]),
        'pageCount' => $book->getPages(),
        'entity' => $entity,
        'origin' => true,
        'book_aside' => [],
        'img' => $translate->get(['og:image', $entity->getPattern()]),
        'text' => $translate->get(['description', $entity->getPattern()]),
        'updated_at' => $entity->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
    ));


    ?>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($currentList->getPattern(), Defines\Extension::JSON) ?>">
        <input type="hidden" name="action" value="change" />
        <input type="hidden" name="id" value="<?php echo $book->getId() ?>" />
        <section class="el_table_pair"><?php
        /* @var $bookList \Data\Doctrine\Main\Content */
        foreach ($this->get('list') as $bookList):
            ?>
            <div class="el_table">
                <span class="indent"><input name="list" type="radio" value="<?php echo $bookList->getId() ?>" <?php echo $bookList === $currentList ? 'checked="checked"' : '' ?> /></span>
                <span class="text-left"><?php echo $bookList->getContent() ?></span>
            </div>
            <?php
        endforeach;
        ?>
        </section>
        <p class="indent"><input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_APPLY') ?>" /></p>
    </form>
</article>
