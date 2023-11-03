<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

// Display other blocks
$aUserData = new \System\ArrayUndef(
    array_diff_key(
        (array) $this->get('data', array()),
        array_flip(\Defines\Content\Attribute::getBasicList())
    )
);

$this->regFunction('del', function($s) {
    $a = $this->get('_del', array());
    $a[] = $s;
    $this->set('_del', $a);
});

$this->regFunction('add', function($a, $b) use ($aUserData) {
    $aCompiled = array();
    foreach ($a as $sType):
        if ($b):
            $tmp = $this->evalFunction('del', array($sType));
        endif;

        $tmp = $aUserData[$sType]->getContent();

        $a = explode('#', $sType);
        if (in_array($a[0], ['grid', 'sked'])):
            $tmp = $this->evalFunction('add', array(explode(',', $tmp), true));
        endif;
        $aCompiled[$sType] = $tmp;
    endforeach;
    return $aCompiled;
});

$aList = array_keys($aUserData->getArrayCopy());
$aCompiled = $this->evalFunction('add', array($aList, false));

$aClear = $this->get('_del');
foreach ($aCompiled as $sType => $mContent):
    if (is_array($aClear) && in_array($sType, $aClear, true)):
        continue;
    endif;
    $a = explode('#', $sType);
    echo $this->partial("Stat/{$a[0]}", array(
        'user' => $this->get('user'),
        'edit' => $this->get('edit', false),
        'data' => $mContent,
        'type' => $sType,
        'ui' => 'ui'
    ));
endforeach;