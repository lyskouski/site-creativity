<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Ogp extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'property';
    const VAR_CONTENT = 'content';
    const TYPE_TITLE = 'og:title';
    const TYPE_SITE = 'og:site_name';
    const TYPE_TYPE = 'og:type'; // video.movie
    const TYPE_URL = 'og:url';
    const TYPE_IMAGE = 'og:image';
    const TYPE_DESC = 'og:description';

    /**
     * @param string $sTitle
     */
    public function __construct ( $sName, $sContent )
    {
        parent::__construct(
                array(
                    self::NAME => new CustomArray(array(
                        self::VAR_NAME => $sName,
                        self::VAR_CONTENT => $sContent
                    ))
                )
        );

    }

    public function isEqual ( MetaInterface $oMeta )
    {
        $bEqual = false;
        if (
                $oMeta[ self::NAME ][ self::VAR_NAME ]
                && $oMeta[ self::NAME ][ self::VAR_NAME ] === $this[ self::NAME ][ self::VAR_NAME ]
        )
        {
            $bEqual = true;
        }
        return $bEqual;

    }

}
