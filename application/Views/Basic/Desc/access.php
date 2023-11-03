<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$aList = \Defines\User\Access::getList();
$a = preg_split("//u", $this->get('value', '000'));
$aType = array(
    1 => $translate->sys('LB_ACCESS_4AUTHOR'),
    2 => $translate->sys('LB_ACCESS_4GROUP'),
    3 => $translate->sys('LB_ACCESS_4OTHERS')
);
foreach ($a as $i => $s):
    if ($s):
        ?><u title="<b><?php echo $aType[$i], ':</b><br />', $aList[(int)$s] ?>"><?php echo $s ?></u><?php
    endif;
endforeach;