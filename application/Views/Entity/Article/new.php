<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'subtitle' => $translate->get(['og:title', $this->get('title_href')]),
        'subtitle_href' => $this->get('title_href')
    ));
    ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('title_href'), \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="create" />
            <div class="el_grid_normalized">
                <div class="el_grid_top">
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_ARTICLE_TITLE') ?>:</span>
                        <input autocomplete="off" type="text" name="og:title" required  placeholder="<?php echo $translate->sys('LB_ARTICLE_TITLE') ?>..." />
                    </p>
                    <p>
                        <span><?php echo $translate->sys('LB_ARTICLE_SEARCH_CATEGORY') ?>:</span>
                        <select name="category" class="width_auto">
                            <option class="co_inactive" value="" selected=""><em><?php echo $translate->sys('LB_ARTICLE_SEARCH_CATEGORY') ?>...</em></option>
                            <?php foreach ($this->get('categories') as $value):
                                ?><option value="<?php echo $value ?>"><?php echo $value ?></option><?php
                            endforeach; ?>
                        </select>
                    </p>
                    <p>&nbsp;</p>
                    <p><?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?></p>
                    <textarea name="description" rows="4" required placeholder="<?php echo $translate->sys('LB_ARTICLE_DESCRIPTION') ?>..."></textarea>
                </div>
                <div class="w-2p">&nbsp;</div>
                <div class="w-10p">
                    <div class="el_grid_top ui" data-class="Modules/Person/Work" data-actions="image" data-name="og:image" data-value="<?php echo $imgPath->getWork() . $this->get('img') ?>.svg">
                        <input type="hidden" name="og:image" value="<?php echo $imgPath->getWork() . $this->get('img') ?>.svg" />
                        <div class="hidden">
                            <?php echo $this->partial('Ui/image') ?>
                        </div>
                        <div><?php echo $translate->sys('LB_ARTICLE_IMAGE') ?>:</div>
                        <img class="el_width_full bg_mask el_border cr_pointer" src="<?php echo $imgPath->getWork() . $this->get('img') ?>.svg" title="<?php echo $translate->sys('LB_ARTICLE_CHANGE_IMAGE') ?>" />
                    </div>
                </div>
            </div>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CREATE') ?>" />
        </form>
    </section>
</article>