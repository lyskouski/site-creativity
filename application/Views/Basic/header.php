<?php
/* @var $this \Engine\Response\Template */

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

if ($this->get('menu', false)):
    ?><header class="el_header">
        <?php
        if ($this->get('ext') === \Defines\Extension::AMP):
            ?><amp-img width="460px" height="130px" layout="responsive" title="<?php echo $translate->sys('LB_SITE_RETURN2MAIN') ?>" src="/img.min/logo.png"></amp-img><?php
        else:
            ?><canvas width="460px" height="130px" title="<?php echo $translate->sys('LB_SITE_RETURN2MAIN') ?>" class="ui" data-class="View/Graphics/Canvas" data-actions="resize,logo"><?php echo $translate->sys('LB_SITE_TITLE') ?></canvas><?php
        endif; ?>

        <div class="menu">
        <?php
        if (is_array($this->get('menu'))):
            foreach ($this->get('menu') as $aMenu):
                ?><a class="<?php echo $this->take($aMenu, 'class', '') ?>" <?php
                   foreach ($this->take($aMenu, 'data', array()) as $sType => $sData):
                        echo " data-{$sType}=\"{$sData}\"";
                    endforeach;
                    ?> href="<?php echo $this->take($aMenu, 'href', '/') ?>">
                    <span><?php echo $this->take($aMenu, 'title', '') ?></span>
                </a><?php
            endforeach;
        endif;
        ?>
        </div>
    </header><?php
endif;
