<?php namespace Engine\Response\Meta\Ogp;
use Engine\Response\Meta\MetaAbstract;
use Engine\Response\Meta\CustomArray;
use Engine\Response\Meta\MetaInterface;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta/Ogp
 */
class Locales extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'property';
    const VAR_CONTENT = 'content';
    const TYPE_LOCALE = 'og:locale'; //language_TERRITORY
    const TYPE_LOCALE_ALT = 'og:locale:alternate';

    /**
     * @param string $sTitle
     */
    public function __construct ( $sName, $sContent )
    {
        parent::__construct(
                array(
                    self::NAME => new CustomArray( array(
                        self::VAR_NAME => $sName,
                        self::VAR_CONTENT => self::getLocale( $sContent )
                    ) )
                )
        );

    }

    public function isEqual ( MetaInterface $oMeta )
    {
        $bEqual = false;
        if (
                $oMeta[ self::NAME ][ self::VAR_NAME ]
                && $oMeta[ self::NAME ][ self::VAR_NAME ] !== self::TYPE_LOCALE_ALT
                && $oMeta[ self::NAME ][ self::VAR_NAME ] === $this[ self::NAME ][ self::VAR_NAME ]
        )
        {
            $bEqual = true;
        }
        return $bEqual;

    }

    public static function getLocale ( $sLanguage )
    {
        switch ( $sLanguage )
        {
            case \Defines\Language::RU:
                $sLocale = 'ru_RU';
                break;
            case \Defines\Language::UA:
            case \Defines\Language::UK:
                $sLocale = 'uk_UA';
                break;
            case \Defines\Language::EN:
                $sLocale = 'en_GB';
                break;
            case \Defines\Language::DE:
                $sLocale = 'de_DE';
                break;
            case \Defines\Language::PT:
                $sLocale = 'pt_PT';
                break;
            case \Defines\Language::FR:
                $sLocale = 'fr_FR';
                break;
            case \Defines\Language::BE:
                $sLocale = 'be_BY';
                break;
            case \Defines\Language::PT:
                $sLocale = 'pt_PT';
                break;
            default:
                throw new \Error\Validation( "Cannot take country locale for the language $sLanguage !" );
        }
        return $sLocale;

    }

}
