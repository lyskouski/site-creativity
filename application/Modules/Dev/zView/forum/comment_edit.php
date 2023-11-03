<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oInput = new \Engine\Request\Input();
$sUrl = $oInput->getUrl(null);

$aList = \Defines\User\Access::getList();

/* @var $oData \Data\Doctrine\Main\Content */
$oData = $this->get('comment');
if (!$oData):
    throw new \Error\Validation($translate->sys('LB_ERROR_INCORRECT_REQUEST'));
endif;
$sLogin = \Data\UserHelper::getUsername($oData->getAuthor());
$sAccess = $oData->getAccess();

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();

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
            <input type="hidden" name="action" value="modify" />
            <input type="hidden" name="id" value="<?php echo $oData->getId() ?>" />
            <p>
                <a href="<?php echo $this->getUrl("person/$sLogin") ?>" target="_blank"><?php echo $sLogin ?></a>
                (<span><?php echo $oData->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?></span>)
                <?php if ($oData->getAuditor()):
                    $sAuditor = $oData->getAuditor()->getUsername();
                    ?><a href="<?php echo $this->getUrl("person/$sAuditor") ?>" target="_blank"><?php echo $sAuditor ?></a><?php
                endif; ?>
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
            <textarea rows="10" name="content" required><?php echo str_replace("\n", '\n', $oData->getContent()) ?></textarea>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_EDIT') ?>" />
        </form>
    </section>
</article>
