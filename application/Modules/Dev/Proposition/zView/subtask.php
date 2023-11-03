<?php
/* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
$num = 0;
?><section class="el_table_pair" id="subtask_list">
    <?php

    if ($this->get('list')):
        ?><p class="clear">&nbsp;</p>
        <section class="el_table">
            <header class="indent bg_accepted">
                <h3><?php echo $translate->sys('LB_SITE_BOARD_LIST') ?></h3>
            </header>
        </section>
        <?php
    endif;

    foreach ($this->get('list') as $o):
        ?><div class="el_table indent"><?php
        echo \Defines\Database\BoardCategory::getIcon($o->getAccess(), $translate->get([$o->getType(), $o->getPattern()]));
        ?></div><?php
        $num++;
    endforeach;
    $num++;

    if (\System\Registry::user()->checkAccess('dev/board', 'index')):
        ?>
        <section class="indent el_form">
            <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('url'), \Defines\Extension::JSON) ?>">
                <input type="hidden" name="action" value="subtask" />
                <input type="hidden" name="title" value="subtask#<?php echo $num ?>" />
                <div class="el_grid">
                    <span><?php echo $translate->sys('LB_BUTTON_ADD') ?>:</span>
                    <input type="text" autocomplete="off" name="description" />
                </div>
                <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_ADD') ?>" />
            </form>
        </section>
        <?php
    endif;
    ?>
</section>