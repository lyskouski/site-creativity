<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

$pattern = trim($this->getUrl($this->get('href'), ' ', ' '), '/. ');
/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = (new \Data\ContentHelper)->getRepository()->getPages($pattern);
$pageCount = $this->get('pageCount', $oStat->getContentCount());

$callback = $this->get('callback', 'ui" data-class="View/Href" data-actions="target" data-target=".ui-target');

?>
<section class="el_notion_small nowrap indent <?php echo $callback ?>">
    <div class="left indent">
        <div class="el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" data-proc="<?php echo $pageCount ?>" data-max="<?php echo $pageCount ?>">
            <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="el_circle_head" />
            <div class="el_circle_num"><?php echo $pageCount ?></div>
        </div>
    </div>
    <div class="nowrap">
        <?php
        if ($this->get('href')):
            ?><a class="co_approve nowrap ui-target <?php if ($this->get('async', true)): ?>ui" data-class="Request/Pjax" data-actions="init<?php endif ?>" href="<?php echo $this->getUrl($this->get('href')) ?>">
                <?php
                $sTitle = trim($this->get('title', ''));
                if (!$sTitle):
                    $sTitle = \System\Registry::translation()->sys('LB_TITLE');
                endif;
                echo $sTitle;
                ?>
            </a>
            <?php
        else:
            ?><span class="nowrap"><?php echo $this->get('title', \System\Registry::translation()->sys('LB_TITLE')) ?></span><?php
        endif;
        ?>
        <p><small><?php
        $sText = $this->get('text', '');
        if (!$sText):
            $sText = \System\Registry::translation()->sys('LB_PAGE_DESCRIPTION');
        endif;
        echo $sText;
        ?></small></p>
    </div>
</section>