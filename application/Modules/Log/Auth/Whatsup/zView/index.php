<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_AUTH_WHATSUP'))
    );
    ?>
</article>