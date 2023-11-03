<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$oInput = new \Engine\Request\Input();
$sUrl = $oInput->getUrl(null);
/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
$iCurrPage = $oInput->getGet('/1', 0, FILTER_SANITIZE_NUMBER_INT);

if (!$oStat->getContent()):
    throw new \Error\Validation(
        \System\Registry::translation()->sys('LB_HEADER_410'),
        \Defines\Response\Code::E_DELETED
    );
endif;

$sAuthor = \Data\UserHelper::getUsername($oStat->getContent()->getAuthor());

$aLanguages = (new \Data\ContentHelper)->getRepository()->getContentLanguages($sUrl);

$bPages = $oStat->getContentCount() > 1;
?>
<header class="el_header_top el_content_header" itemscope itemtype="http://schema.org/Book">
    <?php echo $this->partial('Basic/Nav/crumbs') ?>
    <div class="title">
        <?php
        if (sizeof($aLanguages) > 1):
            echo $this->partial('Basic/language', array(
                'languages' => $aLanguages,
                'url' => null
            ));
        endif;
        ?>
        <h1 itemprop="name" class="nowrap<?php echo $bPages ? '' : ' indent' ?>"><?php echo $oStat->getContent()->getContent() ?></h1>
    </div>

    <div class="right indent" itemprop="datePublished"><?php echo (new \Modules\Dev\History\Model)->getFirstDate($oStat->getContent()) ?></div>
    <meta itemprop="inLanguage" content="<?php echo $oStat->getContent()->getLanguage() ?>" />
    <meta itemprop="description" content="<?php echo $translate->get(['description', $oStat->getContent()->getPattern()]) ?>" />

    <?php
    $sOrigAuthor = null;
    if ($oStat->getContent()->getContent2()):
        $sOrigAuthor = \Data\UserHelper::getUsername($oStat->getContent()->getContent2()->getAuthor());
    endif;
    if ($sOrigAuthor && $sOrigAuthor !== $sAuthor):
        ?>
    <div class="left indent">
        <meta itemprop="translationOfWork" itemscope itemtype="http://schema.org/CreativeWork" itemid="<?php
            $origin = $oStat->getContent()->getContent2();
            echo $this->getUrl($origin->getPattern(), null, $origin->getLanguage());
            ?>" />
        <?php echo $translate->sys('LB_ACCESS_TRANSLATOR') ?>:
        <a itemprop="author" property="itemprop" itemscope itemtype="http://schema.org/Person" href="<?php echo $this->getUrl("person/$sAuthor") ?>"><span itemprop="name"><?php echo $sAuthor ?></span></a>
        / <?php echo $translate->sys('LB_ACCESS_AUTHOR') ?>:
        <a itemprop="author" property="itemprop" itemscope itemtype="http://schema.org/Person" href="<?php echo $this->getUrl("person/$sOrigAuthor") ?>"><span itemprop="name"><?php echo $sOrigAuthor ?></span></a>
    </div>
    <?php else: ?>
    <div class="left indent"><?php echo $translate->sys('LB_ACCESS_AUTHOR') ?>:
        <a itemprop="author" property="itemprop" itemscope itemtype="http://schema.org/Person" href="<?php echo $this->getUrl("person/$sAuthor")?>"><span itemprop="name"><?php echo $sAuthor ?></span></a>
    </div>
    <?php endif ?>

    <?php if ($bPages): ?>
    <div class="center el_pagination">
        <div class="nowrap">
            <?php echo $translate->sys('LB_PAGE') ?>
            <input max="<?php echo $oStat->getContentCount() ?>" min="0" type="text" size="3" value="<?php echo 1+$iCurrPage ?>" class="center ui" data-class="Request/Pjax" data-actions="page" data-url="<?php echo $this->getUrl($sUrl, false) ?>" />
            <span><?php echo $translate->sys('LB_PAGE_COUNT'), ' ', $oStat->getContentCount() ?></span>
        </div>
        <meta itemprop="numberOfPages" content="<?php echo $oStat->getContentCount() ?>" />
    </div>
    <?php else: ?>
    <div class="center el_pagination unvisible">&nbsp;</div>
    <?php endif ?>

    <div class="el_popup">
        <?php
        $aNav = array(
            array(
                'title' => $translate->sys('LB_SITE_RETURN2MAIN'),
                'url' => $this->getUrl('index'),
                'sync' => true
            )
        );
        if (\System\Registry::user()->isLogged()):
            $aNav[] = array(
                'title' => $translate->sys('LB_PERSONAL'),
                'url' => $this->getUrl('person'),
                'sync' => true
            );
        endif;

        $aNav[] = array(
            'title' => $translate->sys('LB_COMMENTS'),
            'url' => $this->getUrl("$sUrl/0"),
            'sync' => true
        );

        // @todo: initiate translation to another language

        // Add navigation ['nav'] if present
        $aNav[] = $translate->get(['nav', $oStat->getContent()->getPattern()], null, function($data) {
            if (strpos($data, '{') === 0):
                $data = '';
            endif;
            return $data;
        });

        if (\System\Registry::user()->checkAccess('dev/tasks/auditor/nav', 'index')):
            $aNav[] = array(
                'title' => $translate->sys('LB_TASK_NAVIGATION'),
                'url' => $this->getUrl("dev/tasks/auditor/nav/{$oStat->getContent()->getId()}"),
                'sync' => true
            );
        endif;

        echo $this->partial('Basic/Nav/catalog', array(
            'list' => $aNav,
            'url' => $sUrl,
            'align' => 'left',
            'url_active' => false
        ));
        ?>
    </div>
</header>