<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sData = $this->get('data', '');

$sImgData = $sData;
if (strpos($sImgData, '/files/') === 0):
    $sImgData = (new \Modules\Files\Model)->getBase64(str_replace('/files/', '', $sImgData));
endif;

$sClass = $this->get('ui', 'ui-delay');

?>
<section class="el_panel <?php
    if ($this->get('edit')):
        echo $sClass ?> el_border_dashed" data-type="<?php echo $this->get('type', 'image') ?>" data-value="<?php
        echo $sImgData ?>" data-class="View/DragDrop" data-actions="drag<?php
    endif;
    ?>">
    <?php
    if (!$sData && $this->get('edit')):
        echo $translate->sys('LB_PERSON_IMAGE');
    else:
        ?><img class="el_adapt" src="<?php echo $sData ?>" /><?php
    endif;
    ?>
</section>