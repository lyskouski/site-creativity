<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class MetaHttp extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'http-equiv';
    const VAR_CONTENT = 'content';

    const TYPE_LANGUAGE = 'Content-Language';
    const TYPE_COMPATIBLE = 'X-UA-Compatible';

    /**
     * @param string $sTitle
     */
    public function __construct ( $sName, $sContent )
    {
        parent::__construct( array(
            self::NAME => new CustomArray(array(
                self::VAR_NAME => $sName,
                self::VAR_CONTENT => $sContent
            ))
        ));

    }

}
