<?php
/* @var $this \Engine\Response\Template */
$sUrl = \System\Registry::config()->getUrl();
/* @var $el \FilesystemIterator */

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84"><?php
    foreach ($this->get('count') as $el): ?>
    <sitemap>
        <loc><?php echo $sUrl ?>/<?php echo $el->getFilename() ?></loc>
    </sitemap><?php
    endforeach;
?></sitemapindex>