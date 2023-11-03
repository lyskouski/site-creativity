<?php /* @var $this \Engine\Response\Template */

if ($this->get('error')):
    ?>
    <div class="el_table nowrap indent">
        <p class="clear-nowrap"><?php echo $this->get('error') ?></p>
    </div>
    <?php

elseif ($this->get('content')):
    ?><div class="ui-container" style="display:none"><?php echo $this->get('content') ?></div><?php
endif;