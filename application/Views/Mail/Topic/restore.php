<?php /* @var $this \Engine\Response\Template */ 
$translate = \System\Registry::translation();

echo $translate->sys('LB_MAIL_RESTORE_CONTENT');
?><br /><br /><?php 
echo $translate->sys('LB_ACCESS_ENTER_TOKEN') ?>:<br />
<strong><font color="maroon"><?php echo $this->get('token') ?></forn></strong><br />