<?php
/* @var $this \Engine\Response\Template */

$oStat = \System\Registry::stat();
$ratingValue = \Defines\Database\Params::getRating($oStat);
$ratingCount = $oStat->getVotesDown() + $oStat->getVotesUp();

$numConv = new \System\Converter\Number();

$aSocial = \System\Registry::config()->getSocial();
$translate = \System\Registry::translation();
?>
<footer class="el_footer">
    <?php if (is_null($this->get('aside', null))): ?>
        <aside itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <meta itemprop="bestRating" content="<?php echo \Defines\Database\Params::MAX_RATING ?>" />
            <meta itemprop="worstRating" content="0" />
            <meta itemprop="ratingValue" content="<?php echo $ratingValue ?>" />
            <meta itemprop="reviewCount" content="<?php echo $ratingCount ?>" />
            <meta itemprop="ratingCount" content="<?php echo $ratingCount ?>" />
            <?php if ($oStat->getContent()): ?>
            <a class="ui" href="<?php echo $this->getUrl($oStat->getContent()->getPattern()) ?>" data-class="Request/Pjax" data-actions="init" data-data="{'action':'comment'}">
                <?php echo $translate->sys('LB_SET_MARK') ?>
            </a>
            <?php endif ?>
            <span><?php echo $numConv->getFloat($oStat->getVisitors()) ?>&nbsp;</span>
            <span class="im_center im_icon im_icon_views">&nbsp;</span>
            <span><?php echo $ratingValue, ' ', $translate->sys('LB_PAGE_COUNT'), ' ', Defines\Database\Params::MAX_RATING , ' (', $numConv->getFloat($ratingCount), ')'; ?></span>
            <span class="im_center im_icon im_icon_rating">&nbsp;</span>
        </aside>
    <?php endif ?>
    <strong class="el_footer_header"><a href="<?php echo $this->getUrl('index') ?>"><?php echo $translate->sys('LB_SITE_TITLE') ?></a></strong>
    <aside>
        <a href="<?php echo $this->getUrl('dev') ?>"><?php echo $translate->sys('LB_SITE_SUPPORT') ?></a>
        <a href="<?php echo $this->getUrl('info/partners') ?>"><?php echo $translate->sys('LB_SITE_INFO_PARTNERS') ?></a>
        <a href="<?php echo $this->getUrl('info/authors') ?>"><?php echo $translate->sys('LB_SITE_INFO_AUTHORS') ?></a>
    </aside>
    <div class="menu">
        <a href="<?php echo $aSocial['vk'] ?>">
            <?php echo $translate->sys('LB_AUTH_VK') ?>
            <span>50</span>
        </a>
        <a href="<?php echo $aSocial['facebook'] ?>">
            <?php echo $translate->sys('LB_AUTH_FACEBOOK') ?>
            <span>?</span>
        </a>
        <a href="<?php echo $aSocial['google'] ?>">
            <?php echo $translate->sys('LB_AUTH_GOOGLE') ?>
            <span>?</span>
        </a>
        <a href="<?php echo $aSocial['twitter'] ?>">
            <?php echo $translate->sys('LB_AUTH_TWITTER') ?>
            <span>?</span>
        </a>
    </div>
    <p>
        <strong>
            <a href="<?php echo $this->getUrl('info') ?>"><?php echo $translate->sys('LB_COPYRIGHT') ?></a>
            © 2008 - <?php echo date('Y') ?>
        </strong>
        <?php echo $translate->sys('LB_COPYRIGHT_CONDITIONS') ?>
    </p>
</footer>