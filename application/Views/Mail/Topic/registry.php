<?php /* @var $this \Engine\Response\Template */ 
$translate = \System\Registry::translation();

echo $translate->sys('LB_AUTH_FINAL_STAGE');
?>: <a href="<?php echo $this->getUrl('log/auth') ?>?account=<?php echo $this->get('account') ?>&type=<?php echo $this->get('type') ?>&token=<?php echo $this->get('token') ?>" target="_blank">
    <?php echo $translate->sys('LB_MAIL_FOLLOW_LINK') ?>
</a>.<br />
<br />   
<?php echo $translate->sys('LB_ACCESS_ENTER_TOKEN') ?>:<br />
<strong><font color="maroon"><?php echo $this->get('token') ?></forn></strong><br />