<?php
/* @var $this \Engine\Response\Template */

$imgPath = new \System\Minify\Images();

?>
<section class="el_notion el_notion_big ui" data-class="View/Href" data-actions="target" data-target=".ui-target">
    <aside class="<?php echo $this->get('class', '') ?>">&nbsp;</aside>
    <?php
    if ($this->get('obj_img')):
        ?><object class="fullsize" data="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" type="image/svg+xml"></object><?php
    else:
        ?><img class="fullsize" src="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" /><?php
    endif;

    ?><header><?php
        if ($this->get('obj_img_type')):
            ?><object data="<?php echo $this->get('img_type', $imgPath->get() . 'css/el_notion/default_type.svg') ?>" type="image/svg+xml" class="embedcontext"></object><?php
        else:
            ?><img src="<?php echo $this->get('img_type', $imgPath->get() . 'css/el_notion/default_type.svg') ?>" /><?php
        endif;

        ?><h3 class="<?php echo ($this->get('author') ? '' : 'el_notion_title') . ' ' . $this->get('class_title', '') ?>"><?php
            if ($this->get('href')): ?>
                <a class="ui-target" href="<?php echo $this->get('href') ?>">
                    <?php echo $this->get('title', \System\Registry::translation()->sys('LB_TITLE')) ?>
                </a>
                <?php
            else:
                ?><span><?php echo $this->get('title', \System\Registry::translation()->sys('LB_TITLE')) ?></span><?php
            endif;
            ?>
        </h3>
    </header>
    <div class="indent">
        <div class="hidden"> <?php echo $this->get('text_extra') ?><br /></div>
        <?php echo $this->get('text', \System\Registry::translation()->sys('LB_PAGE_DESCRIPTION')) ?>
    </div>
</section>