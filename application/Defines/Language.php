<?php namespace Defines;

/**
 * Languages list
 * @see ListInterface
 *
 * @sample \Defines\Language::getList()
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Defines
 */
class Language implements ListInterface
{

    /**
     * Belarusian language
     * @var string
     */
    const BE = 'be';

    /**
     * Russian language
     * @var string
     */
    const RU = 'ru';

    /**
     * Ukraine language
     * @sample \System\Registry::translation()->sys('LB_LANG_UK')
     * @var string
     */
    const UK = 'uk';

    /**
     * @deprecated - has to be used `uk`
     */
    const UA = 'ua';

    /**
     * English
     * @var string
     */
    const EN = 'en';

    /**
     * French language
     * @var string
     */
    const FR = 'fr';

    /**
     * German language
     * @var string
     */
    const DE = 'de';

    /**
     * Portugal language
     * @var string
     */
    const PT = 'pt';

    /**
     * Polish language
     * @var string
     */
    const PL = 'pl';

    public static function getDefault()
    {
        return self::BE;
    }

    public static function isSlavic($language)
    {
        return in_array(
            $language,
            array(
                self::BE,
                self::RU,
                self::UK
            ),
            true
        );
    }

    public static function getList($deprecated = false)
    {
        $list = array(
            self::BE,
            self::RU,
            self::UK,
            self::EN,
            self::FR,
            self::DE,
            self::PT,
            self::PL
        );
        if ($deprecated) {
            $list = (new \System\Aggregator)->const2array(__CLASS__);
        }
        return $list;
    }

    public static function getContentList($pattern)
    {
        return \System\Registry::connection()
            ->getRepository(Database\CrMain::CONTENT)
            ->getContentLanguages($pattern);
    }
}
