<?php /* @var $this \Engine\Response\Template */
// h0 - impossible, h1 - reserved
$i = 2 + $this->get('head');

?><div class="el_grid">
    <?php if ($this->get('head')):
        ?><span><?php echo str_repeat('&nbsp;', $this->get('head') * 4) ?></span><?php
    endif;
    echo '<h', $i ,'>', $this->get('title'), '</h', $i ,'>';
    ?>
    <div class="w-inherit el_underline">&nbsp;</div>
    <div><?php echo 1 + $this->get('page') ?></div>
</div>