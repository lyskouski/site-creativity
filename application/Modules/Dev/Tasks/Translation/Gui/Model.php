<?php namespace Modules\Dev\Tasks\Translation\Gui;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{
    /**
     * @var \Engine\Response\Helper\Locales
     */
    protected $oLoc;

    /**
     * Init Locales Helper
     */
    public function __construct()
    {
        $this->oLoc = new \Engine\Response\Helper\Locales();
    }

    /**
     * Get list of all translations
     *
     * @return array
     */
    public function getList()
    {
        $content = file_get_contents($this->getUrl(\Defines\Language::BE));
        preg_match_all('/msgid "(.*?)"/', $content, $march);
        unset($march[1][0]);
        return array_values($march[1]);
    }

    /**
     * Get URL to a translation system-file
     *
     * @param type $sLang
     * @return type
     */
    public function getUrl($sLang) {
        return \System\Registry::config()->getTranslationPath() . '/'
            . \Engine\Response\Meta\Ogp\Locales::getLocale( $sLang )
            . '/LC_MESSAGES/system.po';
    }

    /**
     * Update translation in a system-file
     *
     * @param string $sLang - const from \Defines\Languages
     * @param array $aList - list of changes [key=>value]
     */
    public function saveList($sLang, $aList) {
        $sPath = $this->getUrl($sLang);
        $sContent = str_replace("\"\n\"", '', file_get_contents( $sPath ));
        $oTranslation = \System\Registry::translation();
        foreach ($aList as $sKey => $sValue) {
            $s = str_replace(array("\t", "\n","\r"), array(' '), stripcslashes(strip_tags($sValue, '<q>')));
            $sContent = str_replace(
                'msgstr "' . $oTranslation->sys($sKey, $sLang) . '"',
                'msgstr "' . str_replace(array(' "', '"'), array(' <q>', '</q>'), trim($s)) . '"',
                $sContent
            );
        }
        $bWritten = file_put_contents($sPath, str_replace('\n', "\\n\"\n\"", $sContent));
        if (!$bWritten) {
            throw new \Error\Validation('Not writable path:' . $sPath);
        }
        // Revalidate translation
        $this->oLoc->add(array());
        // Update JS files
        $this->updateJs($sLang);
    }

    public function updateJs($sLang)
    {
        $filePath = \System\Registry::config()->getPublicPath() . "/js/classes/model/translate/{$sLang}.js";
        $content = file_get_contents($filePath);

        $oTranslate = \System\Registry::translation();

        $new = substr($content, 0, strpos($content, '/* bind */') + 11) . "\n        ";
        foreach ($this->oLoc->findJS([]) as $value) {
            $new .= ", $value: '" . str_replace("'", "\\'", $oTranslate->sys($value, $sLang)) . "'\n        ";
        }
        $new .= substr($content, strpos($content, '/* final */'));
        $bWritten = file_put_contents($filePath, $new);
        if (!$bWritten) {
            throw new \Error\Validation('Not writable path:' . $filePath);
        }
    }
}
