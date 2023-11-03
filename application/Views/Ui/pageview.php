<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

if ($this->get('error')):
    ?>
    <div class="el_table nowrap indent">
        <?php if ($this->get('pattern')):
            ?><aside>{pageview: <?php echo $this->get('pattern') ?>}</aside><?php
        endif;
        ?><p class="clear-nowrap"><?php echo $this->get('error') ?></p>
    </div>
    <?php

elseif ($this->get('content')):
    /* @var $content \Data\Doctrine\Main\Content */
    $content = $this->get('content');
    ?>
    <div class="el_table nowrap indent">
        <aside class="nowrap">
            <p><?php
                echo $translate->sys('LB_SITE_UPDATES');
                ?>:<br /><?php
                echo (new \System\Converter\DateDiff)->getInterval($content->getUpdatedAt()->diff(new \DateTime));
                ?>
            </p>
        </aside>
        <div class="left indent">
            <div class="el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $translate->get(['og:image', $content->getPattern()], null, $imgPath->adaptWork($content->getPattern())) ?>" data-proc="0" data-max="0">
                <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="el_circle_head" />
                <div class="el_circle_num"></div>
            </div>
        </div>
        <div class="nowrap">
            <a class="co_approve nowrap ui-target" href="<?php echo $this->getUrl($content->getPattern(), null, $content->getLanguage()) ?>"><?php
                echo $content->getContent();
            ?></a>&nbsp; / &nbsp;<small><?php
            if ($content->getAuthor()):
                ?><a class="co_approve nowrap ui" data-class="View/Href" data-actions="stopPropagation" href="<?php echo $this->getUrl('person/' . $content->getAuthor()->getUsername()) ?>"><?php
                    echo $content->getAuthor()->getUsername();
                ?></a><?php
            endif;
            ?></small>
        </div>
        <p class="clear-nowrap"><?php echo $translate->get(['description', $content->getPattern()]) ?></p>
    </div>
    <?php
    
elseif ($this->get('url')):
    $url = $this->get('url');
    ?>
    <div class="el_table nowrap indent">
        <div class="left indent">
            <div class="el_circle_cover ui" data-class="Modules/Book/Calendar" data-actions="status" data-cover="<?php echo $translate->get(['og:image', $url], null, $imgPath->adaptWork($url)) ?>" data-proc="0" data-max="0">
                <img src="<?php echo $imgPath->get() ?>css/el_box/null.png" class="el_circle_head" />
                <div class="el_circle_num"></div>
            </div>
        </div>
        <div class="nowrap">
            <a class="co_approve nowrap ui-target" href="<?php echo $this->getUrl($url) ?>"><?php
                echo $translate->get(['og:title', $url]);
            ?></a>
        </div>
        <p class="clear-nowrap"><?php echo $translate->get(['description', $url]) ?></p>
    </div>
    <?php
endif;