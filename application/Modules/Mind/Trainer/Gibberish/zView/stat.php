<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();
$numConv = new \System\Converter\Number();

?><article class="el_content">
    <p>&nbsp;</p>
    <section class="indent">
        <div class="el_grid el_border bg_note">
            <strong class="w-20p indent"><?php echo $translate->sys('LB_GAME_RATING') ?>:</strong>
            <strong class="w-20p txt_right"><?php echo $numConv->getFloat($this->get('rating_new'), 2) ?></strong>
            <strong class="w-20p txt_right indent"><?php echo $numConv->getIncrement($this->get('rating_new') - $this->get('rating'), 4) ?></strong>
        </div>
        <div class="el_grid el_border bg_note">
            <strong class="w-20p indent"><?php echo $translate->sys('LB_GAME_LEVEL') ?>:</strong>
            <strong class="w-20p txt_right"><?php echo $this->get('lvl_new') ?></strong>
            <strong class="w-20p txt_right indent"><?php echo $numConv->getIncrement($this->get('lvl_new') - $this->get('lvl')) ?></strong>
        </div>
        <div class="el_grid el_border bg_mask">
            <strong class="w-20p indent"><?php echo $translate->sys('LB_GAME_MISTAKES') ?>:</strong>
            <strong class="w-20p txt_right"><?php echo $this->get('mistakes') ?></strong>
            <strong class="w-20p txt_right indent"><?php echo  $numConv->getFloat($this->get('mistakes_percent'), 2) ?>%</strong>
        </div>
        <div class="el_grid">
            <strong class="indent"><?php echo $translate->sys('LB_GAME_TIME_START') ?>:</strong>
            <span class="w-2p txt_right indent"><?php echo $this->get('time_start') ?></span>
        </div>
        <div class="el_grid ">
            <strong class="indent"><?php echo $translate->sys('LB_GAME_TIME_FIN') ?>:</strong>
            <span class="w-2p txt_right indent"><?php echo $this->get('time_finished') ?></span>
        </div>
        <div class="el_grid el_border bg_mask">
            <strong class="indent"><?php echo $translate->sys('LB_GAME_TIME_LEFT') ?>:</strong>
            <span class="w-2p txt_right indent"><?php
            $seconds = floor($this->get('time_left') % 60);
            if ($seconds < 10) {
                $seconds = "0{$seconds}";
            }
            $minutes = floor(($this->get('time_left')/60) % 60);
            if ($minutes < 10) {
                $minutes = "0{$minutes}";
            }
            echo $minutes, ':', $seconds;
            ?></span>
        </div>

        <p>&nbsp;</p>

        <a class="button bg_button co_highlight" href="<?php echo $this->getUrl($oStat->getContent()->getPattern()) ?>"><?php echo $translate->sys('LB_BUTTON_BACK') ?></a>

        <p class="clear">&nbsp;</p>
        <?php
        $data = $this->get('data');
        $target = $data['target'];
        $stat = $data['stat'];
        $num = 0;
        foreach ($data['content'] as $num => $value):
            $class = '';
            $bTarget = in_array($value, $target);
            $bStat = in_array($num, $stat);
            if ($bTarget && $bStat):
                $class = 'bg_accepted';
            elseif ($bTarget && !$bStat):
                $class = 'bg_attention';
            elseif (!$bTarget && $bStat):
                $class = 'bg_mask co_attention';
            endif;
            ?><span class="gm-plate <?php echo $class ?>">
                <span class="gm-plate-text"><?php echo $value ?></span>
            </span><?php
        endforeach;
        ?>
        <p class="clear">&nbsp;</p>
    </section>
</article>