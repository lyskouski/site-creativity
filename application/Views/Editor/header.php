<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();

if ($this->get('buttons', false)):
    ?><aside class="el_top"><?php
    foreach ($this->get('buttons') as $aButton):
        ?><a class="el_top_button bg_<?php echo $this->take($aButton, 'type', 'button') ?>" <?php
        foreach ($this->take($aButton, 'data', array()) as $sType => $sData):
            echo " data-{$sType}=\"{$sData}\"";
        endforeach;
        ?> href="<?php echo $this->take($aButton, 'href', '/') ?>"><?php
        echo $this->take($aButton, 'title', '');
        ?></a><?php
    endforeach;
    ?></aside><?php
endif;

?><header class="el_header">
    <?php
    if ($this->get('ext') === \Defines\Extension::AMP):
        ?><amp-img width="138px" height="39px" title="<?php echo $translate->sys('LB_SITE_RETURN2MAIN') ?>" src="/img.min/logo.png"></amp-img><?php
    else:
        ?><canvas style="width:138px;height:39px;" width="460px" height="130px" title="<?php echo $translate->sys('LB_SITE_RETURN2MAIN') ?>" class="ui" data-class="View/Graphics/Canvas" data-actions="resize,logo"><?php echo $translate->sys('LB_SITE_TITLE') ?></canvas><?php
    endif;
    ?><h1><?php echo $this->get('og:title') ?></h1><?php
    echo $this->partial('Basic/Nav/crumbs');
    ?>
</header><?php

if ($this->get('og:reply')):
    ?>
    <p>&nbsp;</p>
    <section class="el_panel bs_recess bg_mask">
        <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_REJECTED') ?></header>
        <p><?php echo $this->get('og:reply') ?></p>
        <div class="right button bg_attention ui" data-class="View/Height" data-actions="spoiler" data-target="closest:section:" data-status="1"><?php echo $translate->sys('LB_BUTTON_CONTINUE') ?></div>
        <p>&nbsp;</p>
    </section>
    <p>&nbsp;</p>
    <?php
endif;