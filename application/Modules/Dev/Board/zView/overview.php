<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('title'),
        'subtitle' => $translate->get(['og:title','dev/proposition']),
        'subtitle_href' => $this->getUrl('dev/proposition')
    ));
    ?>
    <div class="el_grid_top">
        <div class="el_table_pair">
            &nbsp;
            <?php
            $list = $this->get('list');
            /* @var $o \Data\Doctrine\Main\Content */
            foreach ($list[\Defines\Content\Attribute::TYPE_TITLE] as $url => $o):
                $lang = $o->getLanguage();
                ?>
                <div class="el_table nowrap indent">
                    <div class="left el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $list['image'][$url] ?>" data-proc="1" data-max="1">
                        <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="cr_move el_circle_head" />
                        <div class="el_circle_num"></div>
                    </div>
                    <div>
                        &nbsp;<a href="<?php echo $this->getUrl($url) ?>"><?php echo $translate->get(['og:title', $url]) ?></a>
                        (<?php echo \Data\UserHelper::getUsername($o->getAuthor()) ?>)
                    </div>
                    <p>&nbsp;<?php echo $list[\Defines\Content\Attribute::TYPE_DESC][$url] ?></p>
                </div>
                <?php
            endforeach;
            ?>
        </div>
    </div>
    <?php
    echo $this->partial('Basic/Nav/pages');
    ?>
</article>