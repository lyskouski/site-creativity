<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<article class="indent el_form">
    <span class="button bg_normal ui" data-class="View/Height" data-actions="spoiler" data-target="closest:article: > form" data-status="0" data-callback="View/Height:addHiddenClass">
        <?php echo $translate->sys('LB_FORUM_CREATE_TOPIC') ?>
    </span>
    <form method="POST" class="ui el_hidden" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('subtitle_href'), \Defines\Extension::JSON) ?>">
        <input type="hidden" name="action" value="create" />
        <p class="el_grid">
            <span><?php echo $translate->sys('LB_FORUM_TITLE') ?>:</span>
            <input type="text" autocomplete="off" name="title" required  placeholder="<?php echo $translate->sys('LB_FORUM_TITLE') ?>" />
        </p>
        <textarea name="description" required placeholder="<?php echo $translate->sys('LB_FORUM_DESCRIPTION') ?>"></textarea>
        <textarea name="content" rows="10" required placeholder="<?php echo $translate->sys('LB_FORUM_CONTENT') ?>"></textarea>
        <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_FORUM_CREATE') ?>" />
    </form>
</article>
<p>&nbsp;</p>