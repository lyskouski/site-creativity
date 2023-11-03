<?php /* @var $this \Engine\Response\Template */

/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
$data = $oStat->getContent();

$ratingValue = \Defines\Database\Params::getRating($oStat);
$ratingCount = $oStat->getVotesDown() + $oStat->getVotesUp();
$firstDate = (new \Modules\Dev\History\Model)->getFirstDate($data);

$numConv = new \System\Converter\Number();

$articleLD = new \Engine\Response\JsonLd\Article();
\System\Registry::structured()->append(
    $articleLD->getAttributes($data, $this->get('content'), $oStat, $firstDate)
);

$username = \Data\UserHelper::getUsername($data->getAuthor());

?>
<article itemprop="mainEntity" itemscope itemtype="http://schema.org/Article">
    <meta itemprop="name" content="<?php echo $data->getContent() ?>" />
    <img itemprop="image" class="hidden" src="<?php echo \System\Registry::config()->getUrl(null, false), $translate->get(['og:image', $data->getPattern()]) ?>" />
    <meta itemprop="author" content="<?php echo $username ?>" itemid="<?php echo $this->getUrl('person/' . $username) ?>" />
    <?php
    if ($data->getAuditor()):
        \System\Registry::structured()->add($articleLD->getEditor($data));
        ?><meta itemprop="editor" content="<?php echo $data->getAuditor()->getUsername() ?>" itemid="<?php echo $this->getUrl('person/' . $data->getAuditor()->getUsername()) ?>" /><?php
    endif;
    ?>
    <meta itemprop="inLanguage" content="<?php echo $data->getLanguage() ?>" />
    <meta itemprop="datePublished" content="<?php echo $firstDate ?>" />
    <meta itemprop="dateModified" content="<?php echo $data->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT) ?>" />
    <meta itemprop="headline" content="<?php echo $translate->get(['description', $data->getPattern()]) ?>" />

<?php if ($this->get('page') > 0): ?>
    <footer class="center el_content" id="ui-prev_page">
        <p class="indent">&nbsp;</p>
        <p class="blur txt_left indent"><?php echo $translate->sys('LB_PAGE_INFINITE_SCROLL') ?></p>
    </footer>
    <div class="center indent_neg">
        <a name="" class="button bg_form ui" href="<?php echo $this->getUrl($this->get('url')) ?>" data-class="Request/Pjax" data-actions="init"><?php echo $translate->sys('LB_PAGE_PREVIOUS') ?></a>
    </div>
    <section class="el_content fs_read bg_headers">

<?php elseif ($this->get('page') == -1): ?>
    <section class="el_content fs_read bg_headers">

<?php else: ?>
    <a name="pg<?php echo $this->get('page') ?>" class="hidden">&nbsp;</a>
    <section class="el_content fs_read bg_headers">
        <p class="indent">&nbsp;</p>
        <p class="indent">&nbsp;</p>
<?php endif ?>

        <?php if (\System\Registry::user()->checkAccess('dev/tasks/auditor', 'update') && $this->get('entity')): ?>
        <div class="indent ui" data-class="Modules/Person/Work" data-actions="update" data-id="<?php echo $this->get('entity')->getId() ?>" itemprop="articleBody mainEntityOfPage"><?php echo $this->get('content') ?></div>
        <?php else: ?>
        <div class="indent" itemprop="articleBody mainEntityOfPage"><?php echo $this->get('content') ?></div>
        <?php endif ?>

        <p class="indent" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
            <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                <img itemprop="image" class="hidden" src="<?php echo \System\Registry::config()->getUrl(null, false) ?>/favicon.png" />
                <meta itemprop="url" itemid="<?php echo \System\Registry::config()->getUrl(null, false) ?>/favicon.png" />
                <meta itemprop="width" content="32" />
                <meta itemprop="height" content="32" />
                &nbsp;
            </span>
            <meta itemprop="name" content="<?php echo $translate->sys('LB_SITE_TITLE') ?>" />
            <meta itemprop="address" content="Belarus, Minsk" />
            <meta itemprop="telephone" content="+375 (29) 302-**-**" />
        </p>

        <footer class="el_footer indent">
            <aside itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                <meta itemprop="bestRating" content="<?php echo \Defines\Database\Params::MAX_RATING ?>" />
                <meta itemprop="worstRating" content="0" />
                <meta itemprop="ratingValue" content="<?php echo $ratingValue ?>" />
                <meta itemprop="reviewCount" content="<?php echo $ratingCount ?>" />
                <meta itemprop="ratingCount" content="<?php echo $ratingCount ?>" />
                <?php if ($oStat->getContent()): ?>
                <a href="<?php echo $this->getUrl($oStat->getContent()->getPattern() . '/0') ?>">
                    <?php echo $translate->sys('LB_SET_MARK') ?>
                </a>
                <?php endif ?>
                <span><?php echo $numConv->getFloat($oStat->getVisitors()) ?>&nbsp;</span>
                <span class="im_center im_icon im_icon_views">&nbsp;</span>
                <span><?php echo $ratingValue, ' ', $translate->sys('LB_PAGE_COUNT'), ' ', Defines\Database\Params::MAX_RATING, ' (', $numConv->getFloat($oStat->getVotesDown() + $oStat->getVotesUp()), ')'; ?>&nbsp;</span>
                <span class="im_center im_icon im_icon_rating">&nbsp;</span>
            </aside>
        </footer>
    </section>
</article>

