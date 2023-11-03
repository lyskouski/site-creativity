<?php /* @var $this \Engine\Response  */

$aContent = $this->getContent();
/* @var $oTemplate \Engine\Response\Template */
$oTemplate = $aContent[0];
$iCache = \Defines\Database\Params::CACHE_IMAGE;

$this->header('Content-type', $oTemplate->get('type'));
$this->header('Content-Length', strlen($oTemplate->get('content')));
$this->header('Expires', gmdate("D, d M Y H:i:s", time() + $iCache) . ' GMT');
$this->header('Pragma', 'cache');
$this->header('Cache-Control', " max-age=$iCache");
$this->sendHeaders($aContent);

echo $oTemplate->get('content');