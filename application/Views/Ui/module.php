<?php /* @var $this \Engine\Response\Template */

if ($this->get('error')):
    ?>
    <div class="el_table nowrap indent">
        <p class="clear-nowrap"><?php echo $this->get('error') ?></p>
    </div>
    <?php

elseif ($this->get('content')):
    ?><div class="ui-target ui" data-class="<?php echo $this->get('content') ?>" data-actions="init"></div><?php
endif;