<?php namespace System\Converter;

/**
 * Operations with string in UTF-8
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package System/Converter
 */
class StringUtf
{

    public function convert($str, $from = 'auto', $to = 'UTF-8')
    {
        return mb_convert_encoding($str, $to, $from);
    }

    public function strto($str, $bLower = true)
    {
        return $bLower ? $this->strtolower($str) : $this->strtoupper($str);
    }

    public function split($str)
    {
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function strlen($str)
    {
        return sizeof($this->split($str));
    }

    public function substr($str, $s, $l = null)
    {
        return implode("", array_slice($this->split($str), $s, $l));
    }

    public function strtolower($str)
    {
        return mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
    }

    public function strtoupper($str)
    {
        return mb_convert_case($str, MB_CASE_UPPER, "UTF-8");
    }

    public function substr_compare($str1, $str2, $i = 0)
    {
        $str1 = iconv('UTF-8', 'cp1251', $str1);
        $str2 = iconv('UTF-8', 'cp1251', $str2);

        for ($i; $i < strlen($str1); $i++) {
            if ($str1[$i] !== $str2[$i]) {
                return $i - 1;
            }
        }
        return 0;
    }

    public function transliterate($sContent, $bLatin = true)
    {
        //$o = Transliterator::create("Any-Latin; Latin-ASCII; Lower()");
        //$s = str_replace(array(':', ' '), array('_','-'), $o->transliterate($sUsername));
        $aSearch = array(
            'а' => 'a', 'к' => 'k', 'х' => 'kh',
            'б' => 'b', 'л' => 'l', 'ц' => 'ts',
            'в' => 'v', 'м' => 'm', 'ч' => 'ch',
            'г' => 'g', 'н' => 'n', 'ш' => 'sh',
            'д' => 'd', 'о' => 'o', 'щ' => 'shch',
            'е' => 'e', 'п' => 'p', 'ы' => 'y',
            'ё' => 'e', 'р' => 'r', 'э' => 'e',
            'ж' => 'zh', 'с' => 's', 'ю' => 'yu',
            'з' => 'z', 'т' => 't', 'я' => 'ya',
            'и' => 'i', 'у' => 'u',
            'й' => 'y', 'ф' => 'f',
            'А' => 'а', 'К' => 'k', 'Х' => 'kh',
            'Б' => 'b', 'Л' => 'l', 'Ц' => 'ts',
            'В' => 'v', 'М' => 'm', 'Ч' => 'ch',
            'Г' => 'g', 'Н' => 'n', 'Ш' => 'sh',
            'Д' => 'd', 'О' => 'o', 'Щ' => 'shch',
            'Е' => 'e', 'П' => 'p', 'Ы' => 'y',
            'Ё' => 'e', 'Р' => 'r', 'Э' => 'e',
            'Ж' => 'zh', 'С' => 's', 'Ю' => 'yu',
            'З' => 'z', 'Т' => 't', 'Я' => 'ya',
            'И' => 'i', 'У' => 'u',
            'Й' => 'y', 'Ф' => 'f',
        );
        if ($bLatin) {
            $str = str_replace(array_keys($aSearch), array_values($aSearch), trim($sContent));
            $sResult = str_replace(array(' ', ':'), array('-', '_'), $str);
        } else {
            $str = str_replace(array_values($aSearch), array_keys($aSearch), trim($sContent));
            $sResult = str_replace(array('-', '_'), array(' ', ':'), $str);
        }
        return preg_replace('/[^\00-\255]+/u', '', $sResult);
    }

}
