<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<section class="indent">
    <p><?php echo $translate->sys('LB_DEV_PROPOSITION_TEXT') ?></p>
    <p class="el_grid el_table_pair">
        <span class="el_table_newline el_table indent"><?php echo $translate->sys('LB_TODO_DO') ?>:</span>
        <a href="<?php echo $this->getUrl('dev/board/recent') ?>" class="el_table center"><?php echo $this->get('num_do') ?></a>

        <span class="el_table_newline el_table indent"><?php echo $translate->sys('LB_TODO_IN') ?>:</span>
        <a href="<?php echo $this->getUrl('dev/board/active') ?>" class="el_table center"><?php echo $this->get('num_in') ?></a>

        <span class="el_table_newline el_table indent"><?php echo $translate->sys('LB_TODO_OK') ?>:</span>
        <a href="<?php echo $this->getUrl('dev/board/finish') ?>" class="el_table center"><?php echo $this->get('num_ok') ?></a>

        <span class="el_table_newline el_table indent"><?php echo $translate->sys('LB_TODO_NO') ?>:</span>
        <a href="<?php echo $this->getUrl('dev/board/reject') ?>" class="el_table center"><?php echo $this->get('num_no') ?></a>
    </p>

    <div class="el_grid el_table_pair">
        <?php
        /* @var $oWorkflow \Data\Doctrine\Main\Workflow */
        foreach ($this->get('curr') as $oWorkflow):
            if ($oWorkflow->getStatus()):
                $iSec = (new \DateTime)->getTimestamp();
            else:
                $iSec = $oWorkflow->getEndedAt()->getTimestamp();
            endif;
            $iSec -= $oWorkflow->getStartedAt()->getTimestamp();
            ?><p class="el_table indent">
                <span class="right ui" data-class="View/Animate/Timer" data-actions="init" data-value="<?php echo $iSec ?>">--:--:--</span>
                <a href="<?php echo $this->getUrl("person/{$oWorkflow->getUser()->getUsername()}") ?>"><?php echo $oWorkflow->getUser()->getUsername() ?></a>
                <?php echo $translate->sys('LB_TODO_WORK_ON') ?>
                <a href="<?php echo $this->getUrl($oWorkflow->getContent()->getPattern()) ?>"><?php echo $oWorkflow->getContent()->getContent() ?></a>
            </p><?php
        endforeach;
        ?>
    </div>

    <p>&nbsp;</p>
    <?php echo $this->partial('stat', ['stat' => $this->get('stat')], __DIR__) ?>

    <p class="indent">&nbsp;</p>
</section>
