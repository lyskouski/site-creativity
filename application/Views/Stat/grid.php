<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

if ($this->get('edit') && !$this->get('data')):
    ?><p><?php echo $translate->sys('LB_PERSON_GRID_SECTION') ?>:</p><?php
endif;

$sClass = $this->get('ui', 'ui-delay');
?>

<section class="el_panel <?php
    if ($this->get('edit')):
        echo $sClass ?> el_border_dashed" data-type="<?php echo $this->get('type', 'grid') ?>" data-value="" data-class="View/DragDrop" data-actions="drag<?php
    endif;
    ?>">
    <div class="el_grid">
        <?php if (!$this->get('data')): ?>
        <section class="el_panel el_border <?php
        if ($this->get('edit')):
            echo $sClass; ?>" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person:trap<?php
        endif;
        ?>">
            <?php echo $translate->sys('LB_PERSON_POSITION') ?>
        </section>
        <section class="el_panel el_border <?php
        if ($this->get('edit')):
            echo $sClass; ?>" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person:trap<?php
        endif;
        ?>">
            <?php echo $translate->sys('LB_PERSON_POSITION') ?>
        </section>
        <?php else:
            foreach ($this->get('data') as $sType => $sContent):
                $a = explode('#', $sType);
                echo $this->partial("Stat/{$a[0]}", array(
                    'user' => $this->get('user'),
                    'edit' => $this->get('edit'),
                    'data' => $sContent,
                    'ui' => $sClass
                ));
            endforeach;
        endif ?>
    </div>
</section>