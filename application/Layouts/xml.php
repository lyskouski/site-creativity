<?php

$aContent = $this->getContent();

// Send summarized headers
$this->header('Content-type', 'application/xml');
$this->sendHeaders($aContent);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
foreach ($aContent as $oTemplate):
    echo $oTemplate->compile();
endforeach;
