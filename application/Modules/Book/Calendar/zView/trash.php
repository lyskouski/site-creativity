<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');

?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('entity')->getContent(),
        'title_href' => $this->getUrl($this->get('entity')->getPattern()),
        'subtitle' => $translate->sys('LB_BOOK_LIST_DELETE'),
    ));

    /* @var $o \Data\Doctrine\Main\BookRead */
    foreach ($this->get('list') as $o):
        $url = $o->getBook()->getContent()->getPattern();
        ?>
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('entity')->getPattern(), \Defines\Extension::JSON) ?>">
            <input type="hidden" name="id" value="<?php echo $o->getId() ?>" />
            <input class="bg_attention" type="submit" data-extra="action=remove" value="<?php echo $translate->sys('LB_BUTTON_DELETE') ?>" />
            <input class="bg_accepted" type="submit" data-extra="action=restore" value="<?php echo $translate->sys('LB_BUTTON_RESTORE') ?>" />
        </form>
        <?php
        echo $this->partial('Basic/notion_plain', array(
            'aside' => '',
            'href' => $url,
            'title' => $translate->get(['og:title', $url]),
            'async' => false,
            'draggable' => false,
            'author' => $translate->get(['author', $url]),
            'text' => $translate->get(['description', $url])
        ));
    endforeach;
    ?>
</article>
