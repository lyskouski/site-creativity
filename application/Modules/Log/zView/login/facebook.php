<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
<?php echo $this->partial('Basic/title', array('title' => \System\Registry::translation()->sys('LB_AUTH_FACEBOOK'))); ?>
    <section class="el_form">
        <div class="el_grid_normalized">
            <div class="indent"><?php echo $translate->sys('LB_AUTH_FACEBOOK_DESCRIPTION') ?>:</div>
            <div class="center indent">
                <div id="ui-fb-button"><fb:login-button scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button></div>
                <form id="ui-fb" class="ui el_hidden" data-class="Request/Form" data-actions="init" method="POST" action="<?php echo $this->getUrl('log/auth/facebook', \Defines\Extension::JSON) ?>">
                    <input type="hidden" name="accessToken" value="" />
                    <input type="hidden" name="signed_request" value="" />
                    <input type="hidden" name="userID" value="" />
                    <input type="hidden" name="name" value="" />
                    <input class="button bg_attention" type="submit" value="<?php echo $translate->sys('LB_AUTH_ENTER') ?>" />
                </form>
            </div>
        </div>
    </section>
</article>