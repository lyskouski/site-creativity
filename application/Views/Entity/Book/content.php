<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$lang = $this->get('entity')->getLanguage();

$nav = $translate->entity(['nav', $this->get('url')], $lang, false);

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemprop="mainEntity">
    <?php echo $this->partial('Entity/Book/nav_list', array(
       'url' => $this->get('url'),
       'url_active' => $this->get('url') . '/content'
    )) ?>

    <header class="el_content_header">
        <h1 class="title"><a class="nowrap" href="<?php echo $this->getUrl($this->get('url')) ?>"><?php echo $translate->sys('LB_CONTENT') ?></a></h1>
    </header>

    <section class="indent clear">
        <?php echo $this->partial('Basic/notion_plain', array(
            'author_txt' => $translate->get(['author', $this->get('url')], $lang),
            'title' => $translate->get(['og:title', $this->get('url')], $lang),
            'href' => $this->get('url'),
            'pageCount' => $translate->get(['pageCount', $this->get('url')], $lang),
            'book_style' => true,
            'async' => false,
            'img' => $translate->get(['og:image', $this->get('url')], $lang),
            'img_type' => $translate->get(['og:image', $this->get('url')], $lang),
            'entity' => $this->get('entity'),
            'origin' => true,
            'draggable' => false,
            'text' => '',
            'book_aside' => [],
            'updated_at' => $translate->get(['date', $this->get('url')], $lang, function($date) {
                return substr($date, 0, 4);
            })
        )); ?>
        <p>&nbsp;</p>
        <?php
        if ($nav && \System\Registry::user()->checkAccess('dev/tasks/auditor', 'update')):
            ?><a class="button bg_attention cr_pointer ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $this->getUrl('dev', \Defines\Extension::JSON) ?>" data-data="{'action':'modify', 'id':<?php echo $nav->getId() ?>}"><?php echo $translate->sys('LB_BUTTON_MODIFY') ?></a><?php
        endif;
        ?>
        <div class="indent" itemprop="articleBody mainEntityOfPage"><?php echo $nav ? strip_tags($nav->getContent()) : $translate->sys('LB_CONTENT_IS_MISSING') ?></div>
    </section>
</article>