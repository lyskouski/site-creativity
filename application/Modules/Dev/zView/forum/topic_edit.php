<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oInput = new \Engine\Request\Input();
$sUrl = $oInput->getUrl(null);

$aList = \Defines\User\Access::getList();

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
$sAccess = $oStat->getContent()->getAccess();

echo $this->partial('Basic/Nav/crumbs');

?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $oStat->getContent()->getContent(),
        'title_href' => $this->getUrl($oStat->getContent()->getPattern())
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($sUrl, \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="edit" />
            <p class="el_grid">
                <span><?php echo $translate->sys('LB_FORUM_TITLE') ?>:</span>
                <input type="text" name="title" required value="<?php echo $translate->get(['og:title', $sUrl]) ?>" />
            </p>
            <p class="el_grid">
                <strong><?php echo $translate->sys('LB_ACCESS') ?></strong>
                <span>&nbsp;<?php echo $translate->sys('LB_ACCESS_4AUTHOR') ?>:</span>
                <select class="w-200" required name="access[]">
                    <?php foreach ($aList as $iKey => $sValue): ?>
                    <option value="<?php echo $iKey ?>"<?php echo $sAccess[0] == $iKey ? ' selected' : ''?>><?php echo $sValue ?></option>
                    <?php endforeach ?>
                </select>
                <span><?php echo $translate->sys('LB_ACCESS_4GROUP') ?>:</span>
                <select class="w-200" required name="access[]">
                    <?php foreach ($aList as $iKey => $sValue): ?>
                    <option value="<?php echo $iKey ?>"<?php echo $sAccess[1] == $iKey ? ' selected' : ''?>><?php echo $sValue ?></option>
                    <?php endforeach ?>
                </select>
                <span><?php echo $translate->sys('LB_ACCESS_4OTHERS') ?>:</span>
                <select class="w-200" required name="access[]">
                    <?php foreach ($aList as $iKey => $sValue): ?>
                    <option value="<?php echo $iKey ?>"<?php echo $sAccess[2] == $iKey ? ' selected' : ''?>><?php echo $sValue ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>&nbsp;</p>
            <p class="el_grid">
                <span><?php echo $translate->sys('LB_FORUM_KEYWORDS') ?>:</span>
                <input type="text" name="keywords" required value="<?php echo $translate->get(['keywords', $sUrl]) ?>" />
            </p>
            <p><?php echo $translate->sys('LB_FORUM_DESCRIPTION') ?></p>
            <textarea name="description" required><?php echo $translate->get(['description', $sUrl]) ?></textarea>

            <?php if (\System\Registry::user()->isAdmin()):
                ?><input type="checkbox" checked name="skip" /> <?php echo $translate->sys('LB_SKIP_MODIFICATION') ?><?php
            endif; ?>

            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_EDIT') ?>" />
        </form>
    </section>
</article>