<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

$list = $this->get('list');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $list['og:title']->getContent(),
        'title_href' => $this->getUrl($list['og:title']->getPattern()),
        'subtitle' => $translate->get(['og:title', $this->get('title_href')]),
        'subtitle_href' => $this->get('title_href')
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('title_href'), \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="update" />
            <div class="el_grid_normalized">
                <div class="el_grid_top">
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</span>
                        <input autocomplete="off" type="text" name="content[<?php echo $list['og:title']->getId() ?>]" required value="<?php echo $list['og:title']->getContent() ?>" />
                    </p>
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_ARTICLE_SEARCH_CATEGORY') ?>:</span>
                        <input autocomplete="off" type="text" name="content[<?php echo $list['keywords']->getId() ?>]" required value="<?php echo $list['keywords']->getContent() ?>" />
                    </p>
                    <p>&nbsp;</p>
                    <p><?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?></p>
                    <textarea name="content[<?php echo $list['description']->getId() ?>]" rows="4"><?php echo $list['description']->getContent() ?></textarea>
                </div>
                <div class="w-2p">&nbsp;</div>
                <?php
                if (!isset($list['og:image'])):
                    $img = $translate->entity(['og:image', $list['og:title']->getPattern()]);
                    $img->setContent('/img/css/el_notion/work/book.svg');
                    $list['og:image'] = $imgPath->adapt($img);
                endif;
                $name = "content[{$list['og:image']->getId()}]";
                $value = $list['og:image']->getContent();
                ?>
                <div class="w-10p">
                    <div class="el_grid_top ui" data-class="Modules/Person/Work" data-actions="image" data-name="<?php echo $name ?>" data-value="<?php echo $value ?>">
                        <input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" />
                        <div class="hidden">
                            <?php echo $this->partial('Ui/image') ?>
                        </div>
                        <div><?php echo $translate->sys('LB_ARTICLE_IMAGE') ?>:</div>
                        <img class="el_width_full bg_mask el_border cr_pointer" src="<?php echo $value ?>" title="<?php echo $translate->sys('LB_ARTICLE_CHANGE_IMAGE') ?>" />
                    </div>
                </div>
            </div>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_EDIT') ?>" />
        </form>
    </section>
</article>