<?php
/* @var $this \Engine\Response  */
$aContent = $this->getContent();
$iCode = 200;
$sMessage = '';
if ($aContent):
    $oFirstTemplate = current($aContent);
    $iCode = $oFirstTemplate->get(\Error\TextAbstract::E_CODE, \Defines\Response\Code::E_OK);
    $sMessage = $oFirstTemplate->get(\Error\TextAbstract::E_MESSAGE, '');
endif;

// Find title
$aTitle = array_filter($this->getMeta(), function($o) {
    return $o instanceof \Engine\Response\Meta\Title;
});
if ($aTitle):
    $sTitle = current($aTitle)->getTitle();
else:
    $sTitle = \System\Registry::translation()->sys('LB_SITE_TITLE');
endif;

// Check if only data is required
$bData = (new \Engine\Request\Input)->getParam('getdata', 0, FILTER_VALIDATE_BOOLEAN);

// Form response

$oDesc = new \Engine\Response\Helper\Description();
$aResponse = array(
    'success' => $iCode,
    'message' => $sMessage,
    'title' => $oDesc->compile($sTitle),
    'data' => array(),
    'params' => array(),
    'script' => array(),
    'runsrc' => array()
);

if ($bData):
    unset($aResponse['title']);
endif;

if ($aContent):
    /* @var $oTemplate Engine\Response\Template */
    foreach ($aContent as $i => $oTemplate):
        // Fill parameters
        if ($bData):
            $aResponse['params'][] = $oTemplate->getAll();
        endif;
        // Fill content
        $sData = $oTemplate->compile();
        if ($sData):
            $aResponse['data'][] = $sData;
        endif;
    endforeach;
endif;

// Check JS
if ($this->getScripts()):
    /* @var $oScript Engine\Response\Meta\Script */
    foreach ($this->getScripts() as $i => $oScript):
        if ($oScript->getSrc()):
            $aResponse['script'][] = $oScript->getSrc();
        else:
            $aResponse['runsrc'][] = $oScript->getContent();
        endif;
    endforeach;
endif;

// Check CSS
$prev = str_replace([\System\Registry::config()->getUrl() . '/', '.html'], '', (new \Engine\Request\Input)->getRefererUrl());
$metaFile = new \System\Minify\MetaFiles();
$cssType = \System\Minify\MetaFiles::TYPE_CSS;
$styles = array_diff(
    $metaFile->getList($cssType),
    (new \System\Minify\MetaFiles($prev))->getList($cssType)
);

$iniPath = \System\Registry::config()->getPublicPath();
if ($styles) {
    $aResponse['style'] = '';
    foreach ($styles as $path) {
        $aResponse['style'] .= file_get_contents($iniPath . $metaFile->getPrefix($path, $cssType) . $path);
    }
}

// To avoid showing json response in browser output
$this->header('Refresh', ' 0; url=' . (new \Engine\Request\Input)->getUrl());
// Add header type
$this->header('Content-type', 'text/plain');// 'application/json');
$this->sendHeaders($aContent, Defines\Response\Code::E_OK);

// Output response
echo json_encode($aResponse);
