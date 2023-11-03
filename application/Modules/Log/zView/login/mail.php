<?php /* @var $this \Engine\Response\Template */
$aError = $this->get('error', array('field' => '', 'text' => ''));
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_AUTH_BY_MAIL'),
        'languages' => \Defines\Language::getList()
    )) ?>

    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('log/auth/mail', \Defines\Extension::JSON) ?>">
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_AUTH_MAIL') ?>:</span>
                <input required="" tabindex="1" autofocus="true" type="text" name="email" value="<?php echo $this->get('email', '') ?>" />
                <?php
                if ($aError['field'] === 'email'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            <div class="el_grid">
                <span><?php echo $translate->sys('LB_AUTH_PASSWORD') ?>:</span>
                <input tabindex="2" type="password" name="pssw" data-aes="<?php echo (new \System\CryptoJS)->getPassphrase() ?>" />
                <?php
                if ($aError['field'] === 'pssw'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
                <a data-type="submit" tabindex="5" data-extra="action=restore" href="#"><?php echo $translate->sys('LB_AUTH_RESTORE_PASSWORD') ?></a>
            </div>
            <a class="button bg_attention right" tabindex="4" data-type="submit" data-extra="action=registry"><?php echo $translate->sys('LB_AUTH_REGISTRY') ?></a>
            <input class="bg_normal" tabindex="3" type="submit" value="<?php echo $translate->sys('LB_AUTH_ENTER') ?>" />
        </form>
    </section>

</article>