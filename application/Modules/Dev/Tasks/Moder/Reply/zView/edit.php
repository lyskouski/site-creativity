<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');

?>

<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_MODER_REPLY'),
        'title_href' => $this->getUrl('dev/tasks/moder/reply'),
        'subtitle' => $translate->sys('LB_RETURN'),
        'subtitle_href' => $this->getUrl('dev/tasks/moder/reply')
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('url'), \Defines\Extension::JSON) ?>">
            <h3><?php echo $translate->get(['og:title', $this->get('content')->getPattern()]) ?></h3>
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id" value="<?php echo $this->get('content')->getId() ?>" />
            <textarea rows="10" name="content" required><?php echo $this->get('content')->getContent() ?></textarea>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_FORUM_CREATE') ?>">
            <small class="left">
                &nbsp;<a class="co_attention" href="<?php echo $this->getUrl('info/decor/forum') ?>" target="_blank"><?php echo $translate->sys('LB_DECOR') ?></a>
            </small>
        </form>
    </section>
</article>