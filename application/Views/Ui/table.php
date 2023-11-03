<?php /* @var $this \Engine\Response\Template */ ?>
<table class="el_border" border="1">
    <?php
    foreach ($this->get('table') as $row):
        ?><tr class=""><?php
        foreach ($row as $col):
            ?><td class="el_border indent"<?php
                if ($col['colspan']):
                    ?> colspan="<?php echo 1 + $col['colspan'] ?>"<?php
                endif;
                if ($col['rowspan']):
                    ?> rowspan="<?php echo 1 + $col['rowspan'] ?>"<?php
                endif;
                ?>><?php echo $col['value'] ?></td><?php
        endforeach;
        ?></tr><?php
    endforeach;
    ?>
</table>