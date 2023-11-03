<?php /* @var $this \Engine\Response\Template */
$aError = $this->get('error', array('field' => '', 'text' => ''));
?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array('title' => \System\Registry::translation()->sys('LB_AUTH_WHATSUP'))); ?>
    <section class="indent el_form">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('log/auth/whatsup', \Defines\Extension::JSON) ?>">
            <div class="el_grid">
                <span><?php echo \System\Registry::translation()->sys('LB_AUTH_PHONE_NUMBER') ?>:</span>
                <input required="" tabindex="1" autofocus="true" type="text" name="phone" value="<?php echo $this->get('phone', '') ?>" />
                <?php
                if ($aError['field'] === 'phone'):
                    echo $this->partial('Basic/Input/error', $aError);
                endif;
                ?>
            </div>
            <a class="button bg_attention right" tabindex="4" data-type="submit" data-extra="action=registry"><?php echo \System\Registry::translation()->sys('LB_AUTH_REGISTRY') ?></a>
        </form>
    </section>
</article>