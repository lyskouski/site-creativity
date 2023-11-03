<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sUrl = (new \Engine\Request\Input)->getUrl(true);

if ((new \Access\Validate\Comment)->isAccepted()):
    ?><article class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($sUrl, \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="reply" />
            <textarea rows="10" name="content" required placeholder="<?php echo $translate->sys('LB_COMMENT_CONTENT') ?>"></textarea>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_FORUM_CREATE') ?>">
            <small class="left">
                &nbsp;<a class="co_attention" href="<?php echo $this->getUrl('info/decor/forum') ?>" target="_blank"><?php echo $translate->sys('LB_DECOR') ?></a>
                &nbsp;<a href="<?php echo $this->getUrl('info/terms') ?>" target="_blank"><?php echo $translate->sys('LB_INFO_TERMS') ?></a>
            </small>
        </form>
    </article>
    <p>&nbsp;</p><?php
else:
    ?><p class="co_attention indent"><?php echo $translate->sys('LB_USER_ACCESS_COMMENT'), ': ', $translate->sys('LB_HEADER_423') ?></p><?php
endif;