<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$sFocus = $translate->getTargetLanguage();
$sPath = (new \Engine\Request\Params)->getModuleUrl();
$sCurrUrl = (new \Engine\Request\Input\Server)->getRelativePath();

$sUrl = $this->get('url', null);
if (is_null($sUrl)):
    $a = explode($sPath, $sCurrUrl);
    if (sizeof($a) > 1):
        $sUrl = $sPath . $a[1];
    endif;
endif;
if (!$sUrl):
    $sUrl = 'index.html';
endif;
// Convert searchable attributes
$aConvert = (new \System\Converter\Massive)->getConvertable($sUrl);

/* @note is used for updating MO files
$oTranslate->sys('LB_LANG_BE');
$oTranslate->sys('LB_LANG_DE');
$oTranslate->sys('LB_LANG_EN');
$oTranslate->sys('LB_LANG_FR');
$oTranslate->sys('LB_LANG_RU');
$oTranslate->sys('LB_LANG_UA');
*/
?>
<div class="menu select">
    <?php
    ?><strong class="ui" data-class="Ui/Element" data-actions="selectEvent"><?php
    if ($this->get('url_active', null) !== null):
        echo $this->get('url_active') ? $this->get('url_active') : '&nbsp;';
    else:
        echo $sFocus;
    endif;
    ?></strong><?php

    foreach ($this->get('languages') as $sLanguage):
        $sActive = '';
        $hrefLang = $sLanguage;
        if (in_array($sLanguage, array($sFocus, $this->get('url_active')), true)):
            $sActive = 'active';
        else:
            $hrefLang .= '" rel="alternate';
        endif;
        ?><a hreflang="<?php echo $hrefLang ?>" class="<?php echo $sActive;
        $aReplace = array_merge(array(
            '{language}' => $sLanguage,
            '{focus}' => $sFocus,
            '{current}' => $sCurrUrl,
            '{module}' => $sPath
        ));
        if ($aConvert):
            $key = key($aConvert);
            $aReplace[$key] = $translate->sys("{$aConvert[$key]}", $sLanguage);
        endif;
        $sActualUrl = str_replace(
            array_keys($aReplace),
            array_values($aReplace),
            $sUrl
        );
        // Special case
        if ($this->get('ui', false)):
            $uiData = $this->get('ui', []);
            $uiClass = $uiData['class'];
            unset($uiData['class']);
            $uiAct = $uiData['actions'];
            unset($uiData['actions']);
            ?> ui" data-class="<?php echo $uiClass ?>" data-actions="<?php echo $uiAct ?>" <?php
            echo 'data-language="', $sLanguage, '"';
            foreach($uiData as $key => $value):
                echo " data-{$key}=", '"', $value, '"';
            endforeach;
            ?> href="#<?php
        // Async request
        elseif (strpos($sUrl, '#!') !== false):
            ?> ui" data-class="Request/Pjax" data-actions="init" href="<?php
            echo $sActualUrl;
        // Sync request
        else:
            ?>" href="<?php echo $this->getUrl($sActualUrl, Defines\Extension::HTML, $sLanguage);
            if ($this->link()):
                $altLink = new \Engine\Response\Meta\Link('alternate', $this->getUrl($sActualUrl, \Defines\Extension::HTML, $sLanguage));
                $this->link()->meta($altLink->addExtra('hreflang', $sLanguage), true);
            endif;
        endif;
        ?>">
            <strong class="im_center"><span class="im_lang im_lang_<?php echo $sLanguage ?>">&nbsp;</span></strong>
            <span><?php echo $translate->sys(strtoupper("LB_LANG_{$sLanguage}"), $sLanguage) ?></span>
        </a><?php
    endforeach;
    ?>
</div>