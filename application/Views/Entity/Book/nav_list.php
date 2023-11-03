<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<div class="right"><?php
    echo $this->partial('Basic/Nav/vertical', array(
        'rotate' => 'rf',
        'color' => 'bg_attention',
        'menu' => [
            $this->getUrl($this->get('url')) => $translate->sys('LB_OVERVIEW'),
            $this->getUrl($this->get('url') . '/content') => $translate->sys('LB_CONTENT'),
            $this->getUrl($this->get('url') . '/comment') => $translate->sys('LB_COMMENT'),
            $this->getUrl($this->get('url') . '/quote') => $translate->sys('LB_QUOTE')
        ],
        'active' => $this->getUrl($this->get('url_active'))
    ))

    ?></div>
<p>&nbsp;</p>