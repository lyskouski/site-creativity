<?php
/* @var $this \Engine\Response  */
$oHeaders = new \Engine\Response\Headers();
$oDesc = new \Engine\Response\Helper\Description();

$aContent = $this->getContent();
if (!$aContent) {
    return;
}

// Send summarized headers
$this->header('Content-type', 'text/html');
$this->sendHeaders($aContent);

try {
    $result = '';
    /* @var $oTemplate \Engine\Response\Template */
    foreach ($aContent as $oTemplate):
        $result .= $oTemplate->compile(false);
    endforeach;
    $html = $oDesc->compile($result);

} catch (\Error\TextInterface $e) {
    throw $e;

} catch (\Exception $e) {
    $html = $e->getMessage();
}

?><!DOCTYPE html>
<html lang="<?php echo \System\Registry::translation()->getTargetLanguage() ?>" prefix="og: http://ogp.me/ns#">
    <head>
        <?php echo $oDesc->compile($oHeaders->processMeta($this->getMeta())), "\n"; ?>
        <script type="application/ld+json"><?php echo $oDesc->compile(str_replace('\\\\', '\\', json_encode(\System\Registry::structured()->getArrayCopy()))) ?></script>
    </head>
    <body itemscope itemtype="http://schema.org/WebPage">
        <?php
        // HTML content
        echo $html;
        // JavaScript classes
        echo $oDesc->compile($oHeaders->processMeta($this->getScripts())), "\n";

        // Google analytics
        if (\System\Registry::config()->getIndexing()):
            ?>
            <script type="text/javascript">
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-3260788-12', 'auto');
                ga('require', 'linkid');
                ga('send', 'pageview');
            </script>
            <?php
        endif;
        /** @todo For debugging in development mode */
        // echo '<pre class="clear">', \System\Registry::logger(), '</pre>';
        ?>
    </body>
</html>