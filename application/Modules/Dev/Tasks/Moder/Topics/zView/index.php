<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_MODER_TOPICS'),
        'title_href' => '',
        'languages' => \Defines\Language::getList()
    )); ?>
    <p class="indent"><?php echo sprintf($translate->sys('LB_TASK_MODER_TOPICS_RULES'), \Defines\Payments::MODER) ?></p>
    <form method="POST" action="<?php echo $this->getUrl('dev/tasks/moder/topics/'.$this->get('language'), \Defines\Extension::JSON) ?>" class="indent ui" data-class="Request/Form" data-actions="init">
        <input type="hidden" name="action" value="task" />
        <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CONTINUE') ?>" />
    </form>
</article>