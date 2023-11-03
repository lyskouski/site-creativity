<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$self = $this;

$fParam = function($name, $default = '', $prefix = '') use ($self) {
    $value = $self->get($name, $default);
    if (is_object($value)):
        $res = $prefix.'name="list['.$value->getId().']" '.$prefix.'value="'.$value->getContent().'"';
    else:
        $res = 'name="'.$name.'" value="'.$value.'"';
    endif;
    return $res;
};

$fValue = function($name, $default = '') use ($self) {
    $value = $self->get($name, $default);
    if (is_object($value)):
        $value = $value->getContent();
    endif;
    return $value;
};

?>
            <div class="el_grid_normalized">
                <div class="el_grid_top">
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</span>
                        <input autocomplete="off" type="text" required <?php echo $fParam('og:title') ?> placeholder="<?php echo $translate->sys('LB_ARTICLE_TITLE') ?>..." />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_AUTHOR') ?>:</span>
                        <input autocomplete="off" type="text" required <?php echo $fParam('author') ?> placeholder="<?php echo $translate->sys('LB_BOOK_AUTHOR_DESC') ?>..." />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_UDC') ?>:</span>
                        <input autocomplete="off" type="text" <?php echo $fParam('udc') ?> />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_ISBN') ?>:</span>
                        <input autocomplete="off" type="text" <?php echo $fParam('isbn') ?> />
                        <span>&nbsp;</span>
                        <span><?php echo $translate->sys('LB_BOOK_PAGES') ?>:</span>
                        <input autocomplete="off" type="text" required <?php echo $fParam('pageCount', 0) ?> />
                        <span>&nbsp;</span>
                        <span><?php echo $translate->sys('LB_BOOK_DATE') ?>:</span>
                        <input autocomplete="off" type="date" required <?php echo $fParam('date', '') ?> placeholder="YYYY-MM-DD" />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_BOOK_CATEGORY') ?>:</span>
                        <input autocomplete="off" type="text" <?php echo $fParam('keywords', '') ?> placeholder="<?php echo $translate->sys('LB_BOOK_CATEGORY_DESC') ?>..." />
                    </p>
                    <p><?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?></p>
                    <?php if (is_object($this->get('description'))): ?>
                        <textarea rows="4" required placeholder="<?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?>..." name="list[<?php echo $this->get('description')->getId() ?>]"><?php echo $this->get('description')->getContent() ?></textarea>
                    <?php else: ?>
                        <textarea rows="4" required placeholder="<?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?>..." name="description"><?php echo $this->get('description') ?></textarea>
                    <?php endif; ?>
                </div>
                <div class="w-2p">&nbsp;</div>
                <div class="w-10p">
                    <div class="el_grid_top ui" data-class="Modules/Person/Work" data-actions="image" <?php echo $fParam('og:image', '', 'data-') ?> >
                        <input type="hidden" <?php echo $fParam('og:image') ?> />
                        <div class="hidden">
                            <?php echo $this->partial('Ui/image') ?>
                        </div>
                        <div><?php echo $translate->sys('LB_BOOK_COVER') ?>:</div>
                        <img class="el_width_full bg_mask el_border cr_pointer" src="<?php echo $fValue('og:image') ?>" title="<?php echo $translate->sys('LB_ARTICLE_CHANGE_IMAGE') ?>" />
                    </div>
                </div>
            </div>

            <?php if (is_object($this->get('content#0'))): ?>
                <textarea rows="10" name="list[<?php echo $this->get('content#0')->getId() ?>]"><?php echo $this->get('content#0')->getContent() ?></textarea>
            <?php else: ?>
                <input type="hidden" name="content#0" value="<?php echo $this->get('content#0') ?>" />
            <?php endif; ?>