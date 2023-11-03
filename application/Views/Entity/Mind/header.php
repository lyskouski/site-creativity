<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$oInput = new \Engine\Request\Input();
$sUrl = $oInput->getUrl(null);

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
if (!$oStat || !$oStat->getContent()):
    $translate->entity(['og:title', $sUrl]);
    (new \Deprecated\Migration)->redirect($this->getUrl($sUrl));
endif;
$url = $oStat->getContent()->getPattern();

$ratingValue = \Defines\Database\Params::getRating($oStat);
$ratingCount = $oStat->getVotesDown() + $oStat->getVotesUp();

$numConv = new \System\Converter\Number();

$sAuthor = \Data\UserHelper::getUsername($oStat->getContent()->getAuthor());

?>
<header class="el_header_top el_content_header" itemscope itemtype="http://schema.org/Game">
    <?php echo $this->partial('Basic/Nav/crumbs') ?>
    <div class="title">
        <?php echo $this->partial('Basic/language', array(
            'languages' => \Defines\Language::getList(),
            'url' => null
        )); ?>
        <h1 itemprop="name" class="nowrap indent"><?php echo $oStat->getContent()->getContent() ?></h1>
    </div>

    <meta itemprop="datePublished" content="<?php echo (new \Modules\Dev\History\Model)->getFirstDate($oStat->getContent()) ?>" />
    <meta itemprop="inLanguage" content="<?php echo $oStat->getContent()->getLanguage() ?>" />
    <meta itemprop="description" content="<?php echo $translate->get(['description', $url]) ?>" />
    <meta itemprop="author" content="<?php echo $sAuthor ?>" itemid="<?php echo $this->getUrl("person/$sAuthor") ?>" />
    <?php
    $sOrigAuthor = null;
    if ($oStat->getContent()->getContent2()):
        $sOrigAuthor = \Data\UserHelper::getUsername($oStat->getContent()->getContent2()->getAuthor());
    endif;

    if ($sOrigAuthor && $sOrigAuthor !== $sAuthor):
        $origin = $oStat->getContent()->getContent2();
        ?>
        <meta itemprop="translationOfWork" itemscope itemtype="http://schema.org/CreativeWork" itemid="<?php echo $this->getUrl($origin->getPattern(), null, $origin->getLanguage()) ?>" />
        <meta itemprop="author" content="<?php echo $sOrigAuthor ?>" itemid="<?php echo $this->getUrl("person/$sOrigAuthor") ?>" />
    <?php endif ?>


    <meta itemprop="gameLocation" content="<?php echo $this->getUrl($sUrl) ?>" />
    <meta itemprop="numberOfPlayers" content="1" />
    <meta itemprop="characterAttribute" content="<?php echo $translate->get(['keywords', $url]) ?>" />
    <meta itemprop="quest" content="<?php echo $translate->get(['description', $url]) ?>" />

    <footer class="el_footer">
        <div class="menu indent" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <meta itemprop="bestRating" content="<?php echo \Defines\Database\Params::MAX_RATING ?>" />
            <meta itemprop="worstRating" content="0" />
            <meta itemprop="ratingValue" content="<?php echo $ratingValue ?>" />
            <meta itemprop="reviewCount" content="<?php echo $ratingCount ?>" />
            <meta itemprop="ratingCount" content="<?php echo $ratingCount ?>" />
            <?php if ($this->get('rating')): ?>
                <a class="right bg_attention ui" href="<?php echo $this->getUrl($url) ?>" data-class="Modules/Mind/Trainer" data-actions="start" data-data="{'action':'start'}">
                    <?php echo $translate->sys('LB_BUTTON_START') ?>
                    <span class="co_attention unvisible">&nbsp;</span>
                </a>
                <a class="right" href="<?php echo $this->getUrl($url) ?>" title="<?php echo $translate->sys('LB_GAME_RATING_DESC') ?>">
                    <?php echo $translate->sys('LB_GAME_RATING') ?>
                    <span><?php echo $numConv->getFloat($this->get('rating')->getContent()) ?></span>
                </a>
                <a class="right" href="<?php echo $this->getUrl($url) ?>" title="<?php echo $translate->sys('LB_GAME_LEVEL_DESC') ?>">
                    <?php echo $translate->sys('LB_GAME_LEVEL') ?>
                    <span class="ui-target-level"><?php echo $numConv->getFloat($this->get('rating')->getSearch()) ?></span>
                </a>
            <?php endif ?>
            <a class="bg_attention left" href="<?php echo $this->getUrl($url . '/comment') ?>">
                <?php echo $translate->sys('LB_SET_MARK') ?>
                <span class="co_attention"><?php echo $ratingValue, ' / ', Defines\Database\Params::MAX_RATING , ' (', $numConv->getFloat($ratingCount), ')'; ?></span>
            </a>
        </div>
    </footer>
</header>