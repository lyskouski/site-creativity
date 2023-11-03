<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<div id="ui-buttons-prompt">
    <button class="button bg_button" data-type="prompt"><?php echo $translate->sys('LB_BUTTON_CONTINUE') ?></button>
    <button class="button bg_attention" data-type="cancel"><?php echo $translate->sys('LB_BUTTON_CANCEL') ?></button>
</div>

<div id="ui-sample-image">
    <p class="el_grid">
        <span><?php echo $translate->sys('LB_IMAGE_SIZE') ?>:</span>
        <input type="text" name="width" value="320" />
        <span><?php echo $translate->sys('LB_IMAGE_SIZE_SEP') ?></span>
        <input type="text" name="height" value="auto" />
    </p>
    <?php echo $translate->sys('LB_PERSON_SELECT_IMAGE') ?>: <input type="file" />
</div>