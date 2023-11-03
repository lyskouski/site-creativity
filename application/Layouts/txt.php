<?php
/* @var $this \Engine\Response  */
$oHeaders = new \Engine\Response\Headers();
$oDesc = new \Engine\Response\Helper\Description();

$aContent = $this->getContent();
if (!$aContent) {
    return;
}

// Send summarized headers
$this->header('Content-type', 'text/html');
$this->sendHeaders($aContent);

foreach ($aContent as $oTemplate):
    echo $oTemplate->compile() . "\n";
endforeach;
