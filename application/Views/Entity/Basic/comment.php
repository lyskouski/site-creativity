<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oValid = new \Access\Validate\Check();
/* @var $oComment \Data\Doctrine\Main\Content */
$oComment = $this->get('comment');
$sUsername = \Data\UserHelper::getUsername($oComment->getAuthor());
?>
<section class="el_table indent"  itemprop="review" itemscope itemtype="http://schema.org/Review">
    <?php
    $sInterval = (new \System\Converter\DateDiff)->getInterval(
        $oComment->getUpdatedAt()->diff(new \DateTime)
    );
    if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted()):
        $sUserLink = "<a href=\"{$this->getUrl('person/' . $sUsername)}\">{$sUsername}</a>";
        $sModerLink = "<a href=\"{$this->getUrl('dev/history/' . $oComment->getId())}\">#{$oComment->getId()}</a>";
        if ($oContent->getAccess() === \Defines\User\Access::getModDecline()):
            ?><p><em><?php echo sprintf($translate->sys('LB_REPLY_DELETED'), $sUserLink, $sModerLink, $sInterval) ?></em></p></section><?php
        else:
            ?><p><em><?php echo "$sUserLink: {$translate->sys('LB_COMMENT_CONTENT')} ({$translate->sys('LB_HEADER_423')} {$sModerLink})" ?></em></p></section><?php
        endif;
        return;
    endif;
    ?>
    <header>
        <meta itemprop="datePublished" content="<?php echo $oComment->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT) ?>" />
        <?php
        $cls = '';
        $symbol = '<span class="%s" title="%s">%s</span>';
        if (strpos($oComment->getType(), '-up')):
            $cls = 'co_approve';
            $symbol = sprintf($symbol, $cls, $translate->sys('LB_COMMENT_MARK_POSITIVE'), '&check;');
            ?><meta itemprop="reviewRating" content="<?php echo \Defines\Database\Params::MAX_RATING ?>" /><?php
        elseif (strpos($oComment->getType(), '-down')):
            $cls = 'co_attention';
            $symbol = sprintf($symbol, $cls, $translate->sys('LB_COMMENT_MARK_NEGATIVE'), '&cross;');
            ?><meta itemprop="reviewRating" content="0" /><?php
        else:
            $symbol = '';
        endif;
        ?>
        <small class="right"><?php echo $symbol ?>&nbsp;<span class="<?php echo $cls ?>"><?php echo $sInterval ?></span></small>
        <a itemprop="author" property="itemprop" itemscope itemtype="http://schema.org/Person" class="nowrap" href="<?php echo $this->getUrl("person/$sUsername") ?>"><?php echo $sUsername ?></a>
    </header>
    <p itemprop="reviewBody"><?php echo $oComment->getContent() ?></p>
</section><?php
