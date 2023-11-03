<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

$lang = $this->get('language', $translate->getTargetLanguage());

$callback = $this->get('callback', 'ui" data-class="View/Href" data-actions="target" data-target=".ui-target');
$drag = '';
if ($this->get('draggable')):
    $drag = ' class="cr_move"';
endif;
?>
<section class="el_notion <?php echo $callback ?>">
    <aside class="<?php echo $this->get('class', '') ?>">&nbsp;</aside>
    <?php if ($this->get('obj_img')):
        ?><object class="fullsize" data="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" type="image/svg+xml"></object><?php
        else:
            ?><img class="fullsize" src="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" /><?php
    endif;
    ?>
    <header>
        <?php
        if ($this->get('obj_img_type')):
            ?><object <?php echo $drag ?> data="<?php echo $this->get('img_type', $imgPath->get() . 'css/el_notion/default_type.svg') ?>" type="image/svg+xml" class="embedcontext"></object><?php
        else:
            ?><img <?php echo $drag ?> src="<?php echo $this->get('img_type', $imgPath->get() . 'css/el_notion/default_type.svg') ?>" /><?php
        endif;

        if ($this->get('author_txt')):
            ?><a class="nowrap" href="<?php echo $this->getUrl($this->get('href')) ?>"><?php echo $this->get('author_txt') ?></a><?php
        elseif ($this->get('author')):
            ?><a class="nowrap ui" data-class="View/Href" data-actions="stopPropagation" href="<?php echo $this->getUrl('person/' . $this->get('author'), null, $lang) ?>"><?php echo $this->get('author') ?></a><?php
        endif;
        ?>
        <h3 class="<?php echo ($this->get('author') ? '' : 'el_notion_title') . ' ' . $this->get('class_title', '') ?>">
            <?php if ($this->get('href')): ?>
                <a class="nowrap ui-target <?php if ($this->get('async', true)): ?>ui" data-class="Request/Pjax" data-actions="init<?php endif ?>" href="<?php echo $this->getUrl($this->get('href'), null, $lang) ?>">
                    <?php
                    $sTitle = trim($this->get('title', ''));
                    if (!$sTitle):
                        $sTitle = $translate->sys('LB_TITLE');
                    endif;
                    echo $sTitle;
                    ?>
                </a>
            <?php else: ?>
                <span class="nowrap"><?php echo $this->get('title', $translate->sys('LB_TITLE')) ?></span>
            <?php endif ?>
        </h3>
    </header>
    <p class="clear clear-nowrap"><?php
        $sText = $this->get('text', '');
        if (!$sText):
            $sText = $translate->sys('LB_PAGE_DESCRIPTION');
        endif;
        echo $sText;
        ?></p>
    <footer><?php echo $this->get('updated_at', date(\Defines\Database\Params::DATE_FORMAT)) ?></footer>
</section>