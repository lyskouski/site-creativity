<?php
/* @var $this \Engine\Response\Template */

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = $this->get('stat');
$translate = \System\Registry::translation();
$oValid = new \Access\Validate\Check();

if (!$oStat):
    throw new \Error\Validation($translate->sys('LB_ERROR_INCORRECT_REQUEST'), \Defines\Response\Code::E_NOT_FOUND);
endif;

$sAccessTopic = $oStat->getContent()->getAccess();
if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted() && \System\Registry::user()->checkAccess('dev/tasks/moder/topics')):
    $sReplyLogin = $oStat->getContent()->getAuditor()->getUsername();
    // $sModerLink = "";
    ?><p class="indent bg_attention clear"><?php echo $translate->sys('LB_HEADER_423') ?>
        <a href="<?php echo $this->getUrl('person/'.$sReplyLogin) ?>"><?php echo $sReplyLogin ?></a>.
        <?php echo sprintf(
            $translate->sys('LB_TOPIC_DELETED'),
            \Data\UserHelper::getUsername($oStat->getContent()->getAuthor()),
            ' - ',
            $oStat->getContent()->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP)
        ) ?>
    </p><?php
endif;

?>
    <p>&nbsp;</p>
    <?php
    /* @var $o \Data\Doctrine\Main\Content */
    foreach ($this->get('list') as $o):
        $sLogin = \Data\UserHelper::getUsername($o->getAuthor());

        ?><section class="el_table indent">
            <?php
            if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted($o)):
                $sUserLink = "<a href=\"{$this->getUrl('person/'.$sLogin)}\">{$sLogin}</a>";
                $sModerLink = "<a href=\"{$this->getUrl('dev/history/'.$o->getId())}\">#{$o->getId()}</a>";
                $sInterval = (new \System\Converter\DateDiff)->getInterval(
                    $o->getUpdatedAt()->diff(new \DateTime)
                );
                if ($o->getAccess() === \Defines\User\Access::getModDecline()):
                    ?><p><em><?php echo sprintf($translate->sys('LB_REPLY_DELETED'), $sUserLink, $sModerLink, $sInterval) ?></em></p></section><?php
                else:
                    ?><p><?php echo "{$translate->sys('LB_COMMENT_CONTENT')} {$translate->sys('LB_HEADER_423')} {$sModerLink}" ?></p></section><?php
                endif;
                continue;
            endif; ?>
            <header>
                <small class="right">
                    <?php echo $o->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?>
                    <?php
                    if ($o->getAuditor()):
                        $sAuditor = $o->getAuditor()->getUsername();
                        ?><a href="<?php echo $this->getUrl("person/$sAuditor") ?>"><?php echo $sAuditor ?></a>
                        <span class="right">(<a href="<?php echo $this->getUrl('dev/history/' . $o->getId() ) ?>"><?php
                            echo $translate->sys('LB_FORUM_HISTORY')
                        ?></a>)</span><?php
                    endif;
                    ?>
                </small>
                <?php
                if (\System\Registry::user()->checkAccess($this->get('module_url'), 'modify')):
                    ?><a class="right bg_attention cr_pointer ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $this->getUrl($o->getPattern(), \Defines\Extension::JSON) ?>" data-data="{'action':'modify', 'id':<?php echo $o->getId() ?>}" title="<?php echo $translate->sys('LB_BUTTON_MODIFY') ?>">&nbsp;&ast;&nbsp;</a><?php
                endif;
                ?>
                <a href="<?php echo $this->getUrl("person/$sLogin") ?>"><?php echo $sLogin ?></a>
            </header>
        <?php echo nl2br($o->getContent()) ?>
        </section><?php
    endforeach;

    if ($oStat->getContentCount() > \Defines\Database\Params::COMMENTS_ON_PAGE):
        echo $this->partial('Basic/Nav/pages', array(
            'curr' => $this->get('page'),
            'num' => \Defines\Database\Params::getPageCount($oStat),
            'url' => $this->get('url')
        ));
    endif;

    echo $this->partial('forum/comment');

    ?>
</article>