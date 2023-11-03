<?php
/* @var $this \Engine\Response\Template */
/** @link https://login.persona.org/ru/embedded_privacy */

$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemscope itemtype="http://schema.org/Article">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_PRIVACY_POLICY')
    ));

    echo $this->partial('Basic/Desc/dynamic', array(
        'url' => 'info/policy',
        'updated' => true
    ));
    ?>
</article>