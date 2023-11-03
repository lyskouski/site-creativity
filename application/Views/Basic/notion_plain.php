<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
$numConv = new \System\Converter\Number();

$pattern = trim($this->getUrl($this->get('href'), ' ', ' '), '/. ');

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = (new \Data\ContentHelper)->getRepository()->getPages($pattern);
$pageCount = $this->get('pageCount', $oStat->getContentCount());

$callback = $this->get('callback', 'ui" data-class="View/Href" data-actions="target" data-target=".ui-target');
$drag = '';
if ($this->get('draggable')):
    $drag = ' class="cr_move"';
endif;

$sText = $this->get('text', '');
if (!$sText):
    $sText = \System\Registry::translation()->sys('LB_PAGE_DESCRIPTION');
endif;

?>
<section class="el_table nowrap indent <?php echo $callback ?>">
    <aside class="nowrap">
        <?php
        if ($this->get('aside') !== null):
            echo $this->get('aside');

        elseif ($this->get('book_style')):
            ?>
            <p>
                <a href="<?php echo $this->getUrl($pattern . '/0') ?>">
                    <span class="im_icon im_icon_rating left">&nbsp;</span>
                    <?php echo \Defines\Database\Params::getRating($oStat), ' ', $translate->sys('LB_PAGE_COUNT'), ' ', Defines\Database\Params::MAX_RATING ?>
                </a>
                (<?php echo $numConv->getFloat($oStat->getVotesDown() + $oStat->getVotesUp()) ?>)</a>
            </p>
            <p><?php echo $translate->sys('LB_BOOK_DATE'), ': ', $this->get('updated_at') ?></p><?php
            echo $this->partial('Entity/Book/state');

        else:
            ?><p class="el_grid">
                <a href="<?php echo $this->getUrl($pattern . '/0') ?>" class=""><span class="left im_icon im_icon_views">&nbsp;</span><?php echo $numConv->getFloat($oStat->getVisitors()) ?>&nbsp;</a>
                <a href="<?php echo $this->getUrl($pattern . '/0') ?>" class=""><span class="left im_icon im_icon_comment">&nbsp;</span><?php echo $numConv->getFloat($oStat->getVotesUp() + $oStat->getVotesDown()) ?>&nbsp;</a>
            </p>
            <p><?php
                echo $translate->sys('LB_SITE_UPDATES') ?>:<br /><?php
                echo (new \System\Converter\DateDiff)->getInterval(
                    (new \DateTime($this->get('updated_at', date(\Defines\Database\Params::DATE_FORMAT))))->diff(new \DateTime)
                );
                ?>, <span><?php echo $pageCount ?>&nbsp;<?php echo $translate->sys('LB_PAGE') ?></span>
            </p><?php
        endif;
        ?>
    </aside>
    <div class="left indent">
        <div class="el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $this->get('img', $imgPath->get() . 'css/el_notion/default.svg') ?>" data-proc="<?php echo $pageCount ?>" data-max="<?php echo $pageCount ?>">
            <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="<?php echo $drag ? 'cr_move' : '' ?> el_circle_head" />
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
    if (!$this->get('book_style')):
        ?>&nbsp; / &nbsp;<small><?php
        echo $translate->sys('LB_ACCESS_AUTHOR'), ': ';
        if ($this->get('author_txt')):
            ?><a class="co_approve nowrap" href="<?php echo $this->getUrl($this->get('href')) ?>"><?php echo $this->get('author_txt') ?></a><?php
        elseif ($this->get('author')):
            ?><a class="co_approve nowrap ui" data-class="View/Href" data-actions="stopPropagation" href="<?php echo $this->getUrl('person/' . $this->get('author')) ?>"><?php echo $this->get('author') ?></a><?php
        endif;
        ?></small>
    </div>
    <p class="clear-nowrap"><?php echo $sText ?></p>
    <?php
    else:
        ?><p><small><?php echo $translate->sys('LB_BOOK_AUTHOR'), ': ', $this->get('author_txt'); ?></small></p>
    </div>
    <?php
    endif;
    ?>
</section>