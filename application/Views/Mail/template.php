<?php
/* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
$aSocial = \System\Registry::config()->getSocial();
$url = \System\Registry::config()->getUrl($translate->getTargetLanguage());

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
        <title><?php echo $this->get('title') ?></title>
        <base href="<?php echo $url ?>" />
    </head>
    <body style="margin:0;padding:0;background: #f0f0ee;">

        <table width="100%" style="background:#f0f0ee">
            <tr>
                <td valign="top" rowspan="2" width="234px" style="background: #f0f0ee;box-shadow: inset 0 -1px 0 white">
                    <a href="<?php echo $url  ?>">
                        <img style="float:left" width="300px" height="88px" src="cid:logo.png" alt="<?php echo \System\Registry::config()->getUrl() ?>" />
                    </a>
                </td>
                <td style="background: #f0f0ee">
                    <a style="background: #f0f0ee;font-size:24px;margin:0;padding:0;text-decoration: none;color:#768e96;text-shadow: 1px 1px 0 white" href="<?php echo $url ?>"><?php echo $translate->sys('LB_SITE_TITLE') ?></a>
                </td>
            </tr>
            <tr>
                <td style="
                    height: 52px;
                    padding: 0;
                    margin: 0;
                    background: #9ac0cc;
                    color: #3f4e53;
                    border-top-left-radius: 5px;
                    text-shadow: 1px 1px 0 rgba(255,255,255,0.4);
                    border-left: 1px solid rgba(255,255,255,0.4);
                    border-top: 1px solid rgba(255,255,255,0.4);
                    box-shadow: inset 1px 1px 0 white, inset 2px 2px 0 #3f4e53">
                    <h1 style="font-size:24px;margin:-12px 0 0; padding-left:12px"><?php echo $this->get('title') ?></h1>
                </td>
            </tr>
        </table>

        <div style="background: white; padding: 16px;margin: 0; font-family: serif; font-size: 17px;">

                <div style="margin: -24px 24px;
                     padding: 12px 24px 3px;
                     background: white;
                     box-shadow: 1px 1px 0 white, -1px 1px 0 white, inset -1px -1px 0 silver, inset 1px -1px 0 silver, -44px 0 0 #f0f0f0, 44px 0 0 #f0f0f0;
                     border-radius: 3px">
                    <p><?php echo $translate->sys('LB_MAIL_HELLO') ?></p>
                    <p><?php echo $this->get('content') ?></p>
                    <p><?php echo $translate->sys('LB_MAIL_REGARDS') ?></p>
                    <p>
                        <hr />
                        <small style="color: #999999;">
                            <?php echo $translate->sys('LB_MAIL_IGNORE') ?>:
                            <a style="color: #6a6351;" href="mailto:<?php echo $translate->sys('EMAIL') ?>"><?php echo $translate->sys('EMAIL') ?></a><br />
                            <?php echo $translate->sys('LB_MAIL_REQUEST') ?>: <?php echo date('Y-m-d H:i:s') ?>
                        </small>
                    </p>
                    <div style="margin: 0 -21px;
                         padding: 2px 12px 5px;
                         border-radius: 3px;
                         background: #e0dbce;
                         color: #6a6351;
                         text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.4);
                         box-shadow: inset 0 0 1px #6a6a6a;
                         font-size: 12px">

                        <footer>
                            <strong><a style="color:#6a6351; font-size:16px;" href="<?php echo $this->getUrl('index') ?>"><?php echo $translate->sys('LB_SITE_TITLE') ?></a></strong>
                            <aside style="color:#656565; padding: 0 0 4px 32px; float: right; margin: 4px 0 4px 24px;">
                                <a style="color: #6a6351;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 0 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.8);" href="<?php echo $this->getUrl('dev') ?>"><?php echo $translate->sys('LB_SITE_SUPPORT') ?></a>
                                <a style="color: #6a6351;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 0 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.8);" href="<?php echo $this->getUrl('info/partners') ?>"><?php echo $translate->sys('LB_SITE_INFO_PARTNERS') ?></a>
                                <a style="color: #6a6351;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 0 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.8);" href="<?php echo $this->getUrl('info/authors') ?>"><?php echo $translate->sys('LB_SITE_INFO_AUTHORS') ?></a>
                            </aside>
                            <div style="margin:0 0 4px 2px; padding:3px 0 0; font-size:12px;">
                                <a style="color: #6a6351;float: left;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 2px 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.6);" href="<?php echo $aSocial['vk'] ?>">
                                    <?php echo $translate->sys('LB_AUTH_VK') ?>
                                </a>
                                <a style="color: #6a6351;float: left;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 2px 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.6);" href="<?php echo $aSocial['facebook'] ?>">
                                    <?php echo $translate->sys('LB_AUTH_FACEBOOK') ?>
                                </a>
                                <a style="color: #6a6351;float: left;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 2px 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.6);" href="<?php echo $aSocial['google'] ?>">
                                    <?php echo $translate->sys('LB_AUTH_GOOGLE') ?>
                                </a>
                                <a style="color: #6a6351;float: left;border-radius: 3px;
                box-shadow: 0 -1px 1px #b3b3b3 inset, -1px -1px 0 rgba(255,255,255,0.8), 1px 1px 0 rgba(255,255,255,0.8), 1px -1px 0 rgba(255,255,255,0.8), -1px 1px 0 rgba(255,255,255,0.8);
                display: block;
                margin: 0 2px 4px;
                padding: 4px 7px 5px;
                text-align: center;
                text-decoration: none;
                background: rgba(255,255,255,0.6);" href="<?php echo $aSocial['twitter'] ?>">
                                    <?php echo $translate->sys('LB_AUTH_TWITTER') ?>
                                </a>
                            </div>
                            <p style="clear: left; font-size: 12px; padding: 4px 0;">
                                <strong>
                                    <a style="color: #6a6351;" href="<?php echo $this->getUrl('info') ?>"><?php echo $translate->sys('LB_COPYRIGHT') ?></a>
                                    © 2008 - <?php echo date('Y') ?>
                                </strong>
                                <?php echo $translate->sys('LB_COPYRIGHT_CONDITIONS') ?>
                            </p>
                        </footer>
                    </div>
                </div>

        </div>
    </body>
</html>
