<?php namespace Engine\Response\Helper;

use System\Registry;
use Engine\Response\Translation;

/**
 * Updated translation cache
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Helper
 */
class Locales
{

    /**
     * Update .po-files and recompile .mo-files
     *
     * @param array $aMissing
     * @param string $sType
     * @param string $sSuffix
     * @param string $sPrefix
     */
    public function add(array $aMissing, $sType = Translation::TB_SYSTEM, $sSuffix = '', $sPrefix = '{{ ')
    {
        $aAdd = array();
        foreach ($aMissing as $sKey) {
            $aAdd[$sKey] = "$sKey\"\nmsgstr \"{$sPrefix}{$sKey}{$sSuffix}\"";
        }

        $sPath = Registry::config()->getTranslationPath();
        $aList = scandir($sPath);
        foreach ($aList as $sDir) {
            if (in_array($sDir, array('nocache', '.', '..'))) {
                continue;
            }
            $sFilepath = "{$sPath}/{$sDir}/LC_MESSAGES/" . $sType . ".po";
            file_put_contents($sFilepath, $this->getOrderedList($sFilepath, $aAdd));
            shell_exec("cd $sPath/{$sDir}/LC_MESSAGES;msgfmt " . $sType . ".po --output-file=" . $sType . ".mo");
        }

        \bindtextdomain($sType, $sPath . '/nocache');
        \bindtextdomain($sType, $sPath);
    }

    protected function getOrderedList($sFilepath, $aAdd)
    {
        $aList = array();
        foreach (explode('msgid "', file_get_contents($sFilepath)) as $sRow) {
            $aRow = explode('"', $sRow);
            $aList[$aRow[0]] = trim($sRow);
        }
        foreach ($aAdd as $sKey => $sRow) {
            if (!isset($aList[$sKey])) {
                $aList[$sKey] = $sRow;
            }
        }
        ksort($aList);
        return 'msgid "' . implode("\n\nmsgid \"", $aList);
    }

    /**
     * Find all translation values in the project
     *
     * @return array
     */
    public function findAll()
    {
        $sResponse = explode("\n", shell_exec('grep -rni \>sys\( ' . realpath(Registry::config()->getAppPath() . '/../../')));
        $aUpdate = array();
        foreach ($sResponse as $sLine) {
            $a = explode('->sys(', $sLine);
            if (sizeof($a) > 1) {
                $s = trim($a[1]);
                $symb = $s[0];
                if (!in_array($symb, ['"', "'"]) || !$s) {
                    continue;
                }
                $key = substr($s, 1);
                $sCurrLine = substr($key, 0, strpos($key, $symb));
                // exclude concatenations
                if (strpos($sCurrLine, '$') !== false || !$sCurrLine) {
                    continue;
                }
                $aUpdate[] = $sCurrLine;
            }
        }
        return $this->findJS($aUpdate);
    }

    /**
     * Find all translation values in the project
     *
     * @return array
     */
    public function findJS(array $aUpdate = array())
    {
        $sResponse = explode("\n", shell_exec('grep -rni tget\( ' . Registry::config()->getPublicPath() . '/js'));
        foreach ($sResponse as $sLine) {
            $a = explode('tget(', $sLine);
            if (sizeof($a) > 1) {
                $s = trim($a[1]);
                $sCurrLine = substr(trim(substr($s, 1, strpos($s, ")") - 1)), 0, -1);
                // exclude concatenations
                if (strpos($sCurrLine, '+') !== false) {
                    continue;
                }
                $aUpdate[] = $sCurrLine;
            }
        }
        return array_unique($aUpdate);
    }
}
