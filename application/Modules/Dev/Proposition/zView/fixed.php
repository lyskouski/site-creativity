<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
foreach ($this->get('list') as $url):
    ?>
    <section class="el_table indent">
        <header>
            <a href="<?php echo $this->getUrl($url) ?>">
                <img width="12px" height="12px" alt="<?php echo $translate->sys('LB_TOPIC_FIXED') ?>" src="<?php echo $imgPath->get() ?>icon/pin.svg" />
                <?php echo $translate->get(['og:title', $url]) ?>
            </a>
            <p><?php echo $translate->get(['description', $url]) ?></p>
        </header>
    </section>
    <?php
endforeach;

if ($this->get('list')):
    ?><p>&nbsp;</p><?php
endif;