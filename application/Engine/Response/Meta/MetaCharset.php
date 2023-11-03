<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class MetaCharset extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'charset';

    /**
     * @param string $sCharset
     */
    public function __construct ( $sCharset )
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    self::VAR_NAME => $sCharset
                ))
            )
        );

    }

    public function isEqual ( MetaInterface $oMeta )
    {
        return $oMeta instanceof MetaCharset;

    }

}
