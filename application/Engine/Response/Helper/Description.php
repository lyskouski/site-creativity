<?php namespace Engine\Response\Helper;

use Engine\Response\Translation;
use System\ArrayUndef;

/**
 * Get page description
 *
 * @author Viachaslau Lyskouski
 * @since 2015-08-18
 * @package Defines
 */
class Description
{

    /**
     * Identify values that wasn't translated
     * @var null
     */
    const NOT_SET = null;

    protected static $aValues = array();
    protected static $aResult = array();
    protected static $aMissing = array();

    public static function registry($sUrl, $sType, $sTargetLang, $sPattern, $fCallback = null)
    {
        if (!array_key_exists($sPattern, self::$aResult)) {
            self::$aResult[$sPattern] = self::NOT_SET;

            if (!isset(self::$aValues[$sUrl])) {
                self::$aValues[$sUrl] = array();
            }

            if (!isset(self::$aValues[$sUrl][$sType])) {
                self::$aValues[$sUrl][$sType] = array();
            }

            self::$aValues[$sUrl][$sType][$sTargetLang] = $fCallback;
        }
    }

    public static function getMissings()
    {
        return self::$aMissing;
    }

    protected function updateTranslations()
    {
        if (!self::$aValues) {
            return;
        }

        $helper = new \Data\ContentHelper();
        $aContent = $helper->find(self::$aValues, true, true);

        foreach (self::$aValues as $sUrl => $aParams) {
            foreach ($aParams as $sType => $aValues) {
                foreach ($aValues as $sLang => $func) {
                    $sPattern = Translation::getMarker($sUrl, $sType, $sLang);
                    // Prepare value
                    $aTmp = $aContent[$sUrl][$sType];
                    /* @var $altern \Data\Doctrine\Main\Content */
                    $altern = null;
                    $exist = !($aTmp[$sLang] instanceof ArrayUndef) && strpos($aTmp[$sLang], '{') !== 0;
                    if ($exist) {
                        $sTmp = $aTmp[$sLang];
                    // Get help for Slavic languages
                    } elseif (\Defines\Language::isSlavic($sLang)) {
                        $altern = $helper->getRepository()->findOneBy([
                            'pattern' => $sUrl,
                            'type' => $sType,
                            'language' => \Defines\Language::RU
                        ]);
                    // Get English version for others
                    } else {
                        $altern = $helper->getRepository()->findOneBy([
                            'pattern' => $sUrl,
                            'type' => $sType,
                            'language' => \Defines\Language::EN
                        ]);
                    }
                    // Check alternative value if original is missing
                    if ($altern && substr($altern->getContent(), 0, 1) !== '{') {
                        $sTmp = "[{$altern->getLanguage()}] {$altern->getContent()}";
                    // Find anything
                    } elseif (!$exist) {
                        $altern = $helper->getRepository()->findOneBy([
                            'pattern' => $sUrl,
                            'type' => $sType
                        ], ['id' => 'ASC']);
                        if ($altern) {
                            $sTmp = "[{$altern->getLanguage()}] {$altern->getContent()}";
                        } else {
                            $sTmp = "{ {$sLang} : {$sUrl} }";
                        }
                    }
                    // Callback functionality
                    if (is_callable($func)) {
                        $sTmp = $func($sTmp);
                    }
                    self::$aResult[$sPattern] = $sTmp;
                }
            }
            unset(self::$aValues[$sUrl]);
        }

        $aNew = new ArrayUndef(self::$aMissing);
        self::$aMissing = (array) $aNew->union($aContent->getMissings());
    }

    /**
     * Compile content
     * - translations
     * - preuso-TeX markers
     *
     * @param string|array $content
     * @return string
     */
    public function compile($content)
    {
        if (is_array($content)) {
            $content = implode("\n        ", $content);
        }
        // Find missing translations
        $this->compileSys($content);

        // Compile preuso-TeX elements
        $tex = new \System\Converter\Helper\LaTeX();
        return $tex->compile($this->compileSys($content));
    }

    /**
     * Compile translations
     *
     * @param string $content
     * @return string
     */
    protected function compileSys($content)
    {
        $transform = array_values(self::$aResult);
        if (array_search(self::NOT_SET, $transform) !== false) {
            $this->updateTranslations();
        }
        $from = array_keys(self::$aResult);

        $result = str_replace($from, $transform, $content);
        return str_replace(array_map(function($v) {
            return str_replace('/', '\\/', $v);
        }, $from), $transform, $result);
    }
}
