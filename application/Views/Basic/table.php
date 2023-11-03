<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

$iSize = \Defines\Database\Params::COMMENTS_ON_PAGE;

$numConv = new \System\Converter\Number();

$oValid = new \Access\Validate\Check();
$oRep = (new \Data\ContentHelper)->getRepository();
/* @var $oContent \Data\Doctrine\Main\Content */
foreach ($this->get('list', []) as $oContent):
    if (!$oContent->getAuthor() && $oContent->getAuditor()):
        $oContent->setAuthor($oContent->getAuditor());
        \System\Registry::connection()->persist($oContent);
        \System\Registry::connection()->flush($oContent);
    endif;

    $sLogin = \Data\UserHelper::getUsername($oContent->getAuthor());
    /* @var $oStat \Data\Doctrine\Main\ContentViews */
    $oStat = $oRep->getPages($oContent->getPattern());
    $oReplyLogin = $oContent->getAuditor();
    $sInterval = (new \System\Converter\DateDiff)->getInterval(
        $oContent->getUpdatedAt()->diff(new \DateTime)
    );
    ?>
    <section class="el_table indent">
        <?php
        if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted($oContent)):
            $sUserLink = "<a href=\"{$this->getUrl('person/'.$sLogin)}\">{$sLogin}</a>";
            $sModerLink = "<a href=\"{$this->getUrl($oContent->getPattern())}\">#{$oContent->getId()}</a>";
            if ($oContent->getAccess() === \Defines\User\Access::getModDecline()):
                ?><p><em><?php echo sprintf($translate->sys('LB_TOPIC_DELETED'), $sUserLink, $sModerLink, $sInterval) ?></em></p></section><?php
            else:
                ?><p><?php echo "{$oContent->getContent()} ({$translate->sys('LB_HEADER_423')} {$sModerLink})" ?></p></section><?php
            endif;
            continue;
        endif;
        ?>
        <aside>
            <!-- div class="el_new">&nbsp;</div -->
            <p class="el_grid">
                <span><span class="left im_icon im_icon_views">&nbsp;</span><?php echo $numConv->getFloat($oStat->getVisitors()) ?>&nbsp;</span>
                <span><span class="left im_icon im_icon_comment">&nbsp;</span><?php echo $numConv->getFloat($oStat->getContentCount()) ?>&nbsp;</span>
            </p>
            <p><?php echo $translate->sys('LB_FORUM_LAST_COMMENT') ?></p>
            <?php if ($oReplyLogin): ?>
                <p><?php echo $translate->sys('LB_FROM') ?>
                    <a href="<?php echo $this->getUrl("person/{$oReplyLogin->getUsername()}" ) ?>"><?php echo $oReplyLogin->getUsername() ?></a>
                </p>
            <?php endif ?>
            <p><?php echo $sInterval ?></p>
        </aside>
        <header>
            <a href="<?php echo $this->getUrl($oContent->getPattern() . '/-1' ) ?>#<?php echo $oStat->getContentCount() ?>" class="nowrap">
                <?php
                if (!$oValid->setType(\Defines\User\Access::COMMENT)->isAccepted($oContent)):
                    ?><img src="<?php echo $imgPath->get() ?>icon/locked.svg" height="12px" width="14px" title="<?php echo $translate->sys('LB_USER_ACCESS_COMMENT'), ': ', $translate->sys('LB_HEADER_423') ?>" alt="<?php echo $translate->sys('LB_HEADER_423') ?>" /><?php
                endif;

                if ($this->get('search')):
                    echo $translate->get(['og:title', $oContent->getPattern()]);
                    echo ' (', str_replace($this->get('search'), "<ins>{$this->get('search')}</ins>", $oContent->getContent()), ')';
                else:
                    echo $oContent->getContent();
                endif;
                ?>
            </a>
            <small>
                <?php echo $translate->sys('LB_ACCESS_AUTHOR') ?>: <a href="<?php echo $this->getUrl("person/$sLogin" ) ?>"><?php echo $sLogin ?></a>
                &nbsp;(<?php echo $oContent->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?>)
            </small>
        </header>
        <p class="el_min_height"><?php echo $translate->get(['description', $oContent->getPattern()]); ?>&nbsp;</p>
        <footer class="el_footer">
        <?php if ($oStat->getContentCount() > \Defines\Database\Params::COMMENTS_ON_PAGE): ?>
            <aside>
                <?php
                $iCounter = \Defines\Database\Params::getPageCount($oStat) + 1;
                if ($iCounter > 3):
                    $iCounter = 3;
                    ?>
                    <a href="<?php echo $this->getUrl($oContent->getPattern() . '/-1' ) ?>"><?php echo $translate->sys('LB_PAGE_LAST') ?></a>
                    <span>..</span>
                    <?php
                endif;
                for ($i = $iCounter; $i > 1; $i--):
                    ?><a href="<?php echo $this->getUrl($oContent->getPattern() . '/'. ($i - 1) ) ?>"><?php echo $i ?></a><?php
                endfor ?>
                <a href="<?php echo $this->getUrl($oContent->getPattern()) ?>">1</a>
            </aside>
        <?php endif ?>
        </footer>
    </section>
    <?php
endforeach;