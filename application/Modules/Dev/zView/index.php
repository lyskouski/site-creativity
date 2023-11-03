<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oAccess = \System\Registry::user();
?>
<article class="el_content">
    <?php
    $aHeader = array(
        'title' => $translate->sys('LB_SITE_SUPPORT'),
        'title_href' => $this->getUrl('dev'),
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_FORUM_NEWS'),
        'subtitle_href' => $this->getUrl('dev/news')
    );
    foreach ($this->get('list') as $sName => $aData):
        $aHeader['subtitle'] = $translate->sys(strtoupper("LB_FORUM_{$sName}"));

        $url = "dev/{$sName}";
        $aHeader['subtitle_href'] = $this->getUrl($url);

        if (!$oAccess->checkAccess($url)):
            continue;
        endif;

        echo $this->partial('Basic/title', $aHeader);
        if ($aData):
            echo $this->partial('Basic/table', array('list' => $aData));
        else:
            $test = $translate->sys('LB_CONTENT_IS_MISSING');
            ?><p class="indent"><?php echo substr($test, 0, strpos($test, '.')) ?> (<a href="<?php echo $aHeader['subtitle_href'] ?>"><?php echo $translate->sys('LB_FORUM_CREATE_TOPIC') ?></a>).</p><?php
        endif;

        $aHeader = array();
    endforeach;
    ?>
</article>