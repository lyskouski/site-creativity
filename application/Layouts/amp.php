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

$result = '';
/* @var $oTemplate \Engine\Response\Template */
foreach ($aContent as $oTemplate):
    $result .= $oTemplate->compile(false);
endforeach;
$html = $oDesc->compile($result);

$metaList = $this->getMeta();
// AMP header limitations
$metaFiles = new \System\Minify\MetaFiles();
foreach ($metaList as $i => $meta):
    /* @var $meta \Engine\Response\Meta\Style */
    // Only inline css files
    if ($meta instanceof \Engine\Response\Meta\Style):
        $min = $metaFiles->getPath($meta->getSrc(), $meta->getExtension());
        $css = file_get_contents(
                \System\Registry::config()->getPublicPath()
                . substr($min, 0, strpos($min, '?'))
        );
        $metaList[$i] = new \Engine\Response\Meta\Style('', $css);
    // Remove permitted meta data
    elseif (
            $meta instanceof \Engine\Response\Meta\MetaHttp
            || $meta instanceof \Engine\Response\Meta\MetaContent
            || $meta instanceof \Engine\Response\Meta\MetaCharset
            || $meta instanceof \Engine\Response\Meta\Meta && $meta['meta']['name'] === \Engine\Response\Meta\Meta::TYPE_REVISIT_AFTER
    ):
        unset ($metaList[$i]);
    endif;
endforeach;

?><!DOCTYPE html>
<html amp><?php
    $content = new \System\Minify\AmpPage();
    ob_start();

    ?><head><meta charset="utf-8"><?php
    echo $oDesc->compile($oHeaders->processMeta($metaList));
    ?><script type="application/ld+json"><?php echo $oDesc->compile(str_replace('\\\\', '\\', json_encode(\System\Registry::structured()->getArrayCopy()))) ?></script><?php
    // AMP Boilerplate Code ('head > style[amp-boilerplate]' and 'noscript > style[amp-boilerplate]')
    ?><style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript><?php
    // AMP JavaScript adaptation
    ?><script async src="https://cdn.ampproject.org/v0.js"></script><?php
    // AMP Form adaptation
    if (strpos($html, '<form')):
        ?><script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script><?php
        $html = str_replace(' action="', ' action-xhr="', $html);
    endif;

    ?></head><body itemscope itemtype="http://schema.org/WebPage"><?php

    // HTML content
    echo $html;
    // JavaScript classes disabled for AMP
    // echo $oDesc->compile($oHeaders->processMeta($this->getScripts()));

    ?></body><?php

    $res = ob_get_contents();
    ob_end_clean();
    echo $content->flush($res);
?></html>