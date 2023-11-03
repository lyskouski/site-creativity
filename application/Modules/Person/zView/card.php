<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
/* @var $aContent array<\Data\Doctrine\Main\Content> */
$aContent = new \System\ArrayUndef($this->get('content', array()));
$imgPath = new \System\Minify\Images();

echo $this->partial(
    'Basic/title', array(
        'title' => $this->get('username'),
        'title_href' => $this->getUrl($this->get('url')),
        'languages' => $this->get('languages')
    )
);

?>
<section class="el_panel el_border clear">
    <header class="bg_attention">
        <?php
        /* @var $oAccess \Data\Doctrine\Main\UserAccess */
        foreach ($this->get('access', array()) as $oAccess):
            ?><span class="fs_small right el_header_button"><?php echo $translate->sys("{$oAccess->getAccess()->getTitle()}"); ?></span><?php
        endforeach;
        echo $aContent['author']->getContent() ?>&nbsp;
    </header>
    <div class="el_grid el_width_full">
        <div class="el_grid_top el_width_auto el_normal indent">
            <?php echo nl2br( $aContent['description']->getContent() ) ?>
            <aside class="el_bottom">
                <a class="button bg_note" href="<?php echo $this->getUrl($this->get('url') . '/artwork') ?>"><?php echo $translate->sys('LB_PERSON_WORK') ?></a>
                <a class="button bg_note" href="<?php echo $this->getUrl($this->get('url') . '/topic') ?>"><?php echo $translate->sys('LB_TASK_MODER_TOPICS') ?></a>
                <a class="button bg_note" href="<?php echo $this->getUrl($this->get('url') . '/comment') ?>"><?php echo $translate->sys('LB_TASK_MODER_REPLY') ?></a>
            </aside>
        </div>
        <img class="right el_border el_grid_top el_width_auto el_adapt" src="<?php echo $aContent['og:image']->getContent() ?>" />
    </div>
    <footer class="el_footer">
        <aside>
        <?php
        $aTypes = \Defines\User\Account::getTextList();
        /* @var $oAccount \Data\Doctrine\Main\UserAccount */
        foreach ($this->get('accounts', array()) as $oAccount):
            $sLink = \Defines\Response\AccountLinks::get($oAccount->getType(), $oAccount->getAccount());
            ?><a <?php echo $sLink ? 'href="'.$sLink.'" target="_blank" class="el_width_auto active"' : 'class="inactive el_width_auto"' ?>>
                <img width="14px" class="left" src="<?php echo $imgPath->adaptAccount($oAccount->getType(), '_type') ?>" />
                &nbsp;<?php echo $aTypes[$oAccount->getType()]; ?>&nbsp;
            </a><?php
        endforeach;
        ?>
        </aside>
    </footer>
</section>