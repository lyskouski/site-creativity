<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_TRANSLATION_BOOK'),
        'title_href' => '',
        'languages' => \Defines\Language::getList()
    )); ?>
    <p class="indent"><?php echo sprintf($translate->sys('LB_TASK_TRANSLATION_TEXT_RULES'), \Defines\Payments::PAGE_HEADER) ?></p>
    <form method="POST" action="<?php echo $this->getUrl('dev/tasks/translation/book', \Defines\Extension::JSON) ?>" class="indent ui" data-class="Request/Form" data-actions="init">
        <input type="hidden" name="action" value="task" />
        <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CONTINUE') ?>" />
    </form>
    <p class="clear indent"><br /></p>
    <?php foreach ($this->get('list') as $entity): ?>
    <section class="el_table indent">
        <aside>
            <form method="POST" action="<?php echo $this->getUrl('dev/tasks/translation/book', \Defines\Extension::JSON) ?>" class="indent ui" data-class="Request/Form" data-actions="init">
                <input type="hidden" name="action" value="task" />
                <input type="hidden" name="id" value="<?php echo $entity->getId() ?>" />
                <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_REFRESH') ?>" />
            </form>
        </aside>
        <header><?php echo $entity->getContent() ?></header>
        <p><a href="<?php echo $this->getUrl($entity->getPattern()) ?>" target="_blank"><?php echo $entity->getPattern() ?></a></p>
    </section>
    <?php endforeach ?>
</article>