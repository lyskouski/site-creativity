<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sFocus = $translate->getTargetLanguage();
$aUrl = explode('/', (new \Engine\Request\Params)->getModuleUrl());

$aList = array();
foreach ($aUrl as $i => $sPart):
    $aList[implode('/', array_slice($aUrl, 0, 1 + $i))] = array('og:title' => array($sFocus => []));
endforeach;
//$aList[implode('/', $aUrl)] = array('og:title' => array($sFocus => []));
$a = (new \Data\ContentHelper)->find($aList, false);
/* @var $oContent \Data\Doctrine\Main\Content */
?>
<div class="el_crumbs nowrap" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php foreach ($a as $i => $oContent): ?>
    <div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a class="nowrap" href="<?php echo $this->getUrl($oContent->getPattern()) ?>" itemprop="item"><span itemprop="name"><?php echo $oContent->getContent() ?></span></a>
        <meta itemprop="position" content="<?php echo $i + 1 ?>" />
    </div>
    <?php endforeach ?>
</div>