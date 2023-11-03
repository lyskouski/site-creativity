<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sData = $this->get('data', '');
$sClass = $this->get('ui', 'ui-delay');
?>
<section class="el_panel <?php
    if ($this->get('edit')):
        echo $sClass; ?> el_border_dashed" data-type="<?php echo $this->get('type', 'text') ?>" data-value="<?php echo $sData ? $sData : '&nbsp;' ?>" data-class="View/DragDrop" data-actions="drag<?php
    endif;
    ?>">
    <span class="ui-target">
        <?php
        if ($sData || ! $this->get('edit')):
            echo $sData;
        else:
            echo $translate->sys('LB_PERSON_TEXT');
        endif;
        ?>
    </span>
</section>