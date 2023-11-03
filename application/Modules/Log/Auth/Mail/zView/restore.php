<?php /* @var $this \Engine\Response\Template */
$aError = $this->get('error', array('field' => '', 'text' => ''));
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array('title' => \System\Registry::translation()->sys('LB_AUTH_FINAL_STAGE'))); ?>
    <section class="indent">
        <p><?php echo $translate->sys('LB_MAIL_RESTORE_CONTENT') ?></p>
    </section>
    
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('log/auth/mail', \Defines\Extension::JSON) ?>">
            <input type="hidden" name="action" value="change" />
            <input type="hidden" name="email" value="<?php echo $this->get('email', '') ?>" />
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_AUTH_TOKEN') ?>:</span>
                <input required="" tabindex="1" type="text" name="token" value="<?php echo $this->get('token', '') ?>" />
                <?php
                if ($aError['field'] === 'token'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            
            <div class="el_grid">
                <span><?php echo \System\Registry::translation()->sys('LB_AUTH_PASSWORD') ?>:</span>
                <input tabindex="2" type="password" name="pssw" />
                <?php
                if ($aError['field'] === 'pssw'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            
            <div class="el_grid">
                <span><?php echo \System\Registry::translation()->sys('LB_AUTH_PASSWORD') ?> (2):</span>
                <input tabindex="3" type="password" name="pssw_retry" data-aes="<?php echo (new \System\CryptoJS)->getPassphrase() ?>" />
                <?php
                if ($aError['field'] === 'pssw_retry'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            
            <input class="bg_attention" tabindex="4" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CONTINUE') ?>" />
        </form>
    </section>
</article>