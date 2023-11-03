<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_RETURN'),
        'title_href' => (new \Engine\Request\Input)->getRefererUrl(),
        'subtitle' => $translate->sys('LB_SITE_RETURN2MAIN'),
        'subtitle_href' => $this->getUrl('index'),
    ));
    ?>
    <header class="el_content_header">
        <h2 class="bg_attention indent"><?php echo $translate->sys('LB_ERROR') ?>: <?php echo $this->get(\Error\TextAbstract::E_MESSAGE) ?></h2>
    </header>
    <p class="indent">
    <?php
    if (\System\Registry::user()->isAdmin()):
        ?>
            <br /><?php echo $this->get(\Error\TextAbstract::E_FILE), ' : ', $this->get(\Error\TextAbstract::E_LINE) ?>
            <p><strong>Backtrace data</strong></p>
            <pre><?php // print_r($this->get(\Error\TextAbstract::E_TRACE)) ?></pre>
            <p><strong>Logger data</strong></p>
            <pre><?php echo \System\Registry::logger() ?></pre>

        <?php
    else:
    // @todo:restore    echo $oTranslate->get(['content#0', 'about/error']);
    endif;
    ?>
    </p>
</article>
<script type="text/javascript">
    if (window.location.hash && ~window.location.hash.indexOf('39;')) {
        window.location.replace(window.location.href.replace('&#39;', "'"));
    }
</script>