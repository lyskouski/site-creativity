<?php
/* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();

?>
<article class="el_content" itemscope itemtype="http://schema.org/Article">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_LICENSE')
    ));

    echo $this->partial('Basic/Desc/dynamic', array(
        'url' => 'license',
        'updated' => true
    ));
    ?>
</article>