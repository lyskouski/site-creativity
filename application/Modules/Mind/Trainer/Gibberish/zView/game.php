<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = \System\Registry::stat();

$content = $this->get('content');
$stat = $this->get('stat', []);
$date = $this->get('date', ['left' => 0]);
?><article class="el_content">
    <p>&nbsp;</p>
    <section class="indent">
        <div class="el_form indent">
            <p class="indent"><?php echo $translate->get(['description', $oStat->getContent()->getPattern()]) ?></p>
            <?php
            foreach ($this->get('target') as $trg):
                ?><span class="el_border button bg_attention center inline left indent_ext w-2p"><?php echo $trg ?></span><?php
            endforeach;
            ?>
            <p>&nbsp;</p>
        </div>
        <p class="clear">&nbsp;</p>
        <?php
        $num = 0;
        foreach ($content as $num => $value):
            ?><span class="gm-plate ui <?php echo in_array($num, $stat) ? 'bg_accepted' : '' ?>"
                    data-class="Modules/Mind/Trainer/Gibberish" data-actions="push" data-id="<?php echo $num ?>">
                <span class="gm-plate-text"><?php echo $value ?></span>
            </span><?php
        endforeach;
        ?>
        <p class="clear">&nbsp;</p>
        <div class="el_form indent">
            <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($oStat->getContent()->getPattern(), \Defines\Extension::JSON) ?>">
                <input type="hidden" name="action" value="stop" />
                <input class="ui-target-time" type="hidden" value="<?php echo $date['left'] ?>" />
                <input class="ui-target-element" type="hidden" name="content" value="" />
                <input class="button bg_attention left" type="submit" value="<?php echo $translate->sys('LB_BUTTON_FINISH') ?>" />
            </form>
        </div>
    </section>
</article>
