<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial('card');

    echo $this->partial('Stat/_compile', array(
        'data' => $this->get('content', array()),
        'user' => $this->get('user'),
        'edit' => false
    ));
    ?>
</article>