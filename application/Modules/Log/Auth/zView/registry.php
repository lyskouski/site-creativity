<?php /* @var $this \Engine\Response\Template */
$aError = $this->get('error', array('field' => '', 'text' => ''));
$translate = \System\Registry::translation();
$iTab = 1;
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_AUTH_FINAL_STAGE')
    )) ?>
    <section class="indent">
        <p><?php echo $translate->sys('LB_AUTH_FINAL_STAGE_DESCRIPTION') ?></p>
    </section>

    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('log/auth', \Defines\Extension::JSON) ?>?account=<?php echo $this->get('account', '') ?>&type=<?php echo $this->get('type', '') ?>">
            <input type="hidden" name="action" value="accept" />
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_AUTH_USERNAME') ?>:</span>
                <input required="" tabindex="<?php echo $iTab++ ?>" autofocus="true" type="text" name="username" value="<?php echo $this->get('username', '') ?>" />
                <?php
                if ($aError['field'] === 'username'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            <?php if ($this->get('token', false) !== false): ?>
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_AUTH_TOKEN') ?>:</span>
                <input required="" tabindex="<?php echo $iTab++ ?>" type="text" name="token" value="<?php echo $this->get('token', '') ?>" />
                <?php
                if ($aError['field'] === 'token'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            <?php endif ?>
            <p>
                <small>
                    <?php echo $translate->sys('LB_AUTH_ACCEPT_DESCRIPTION') ?>
                    <a href="<?php echo $this->getUrl('/info') ?>" target="_blank"><?php echo $translate->sys( 'LB_GET_DETAILS' ) ?></a>
                </small>
            </p>
            <input class="bg_attention" tabindex="<?php echo $iTab ?>" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CONTINUE') ?>" />
        </form>
    </section>
</article>