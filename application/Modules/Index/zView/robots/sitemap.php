<?php
$tmp = '';
$basicUrl = \System\Registry::config()->getUrl(null, false);
foreach (\Defines\Language::getList() as $sLang):
    $url = \System\Registry::config()->getUrl($sLang);
    if ($url !== $tmp && strpos($url, $basicUrl) === 0):
        echo "Sitemap: {$url}/sitemap.xml\n";
    endif;
    $tmp = $url;
endforeach;
