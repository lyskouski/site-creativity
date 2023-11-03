<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class MetaContent extends MetaAbstract
{
    const NAME = 'meta';
    const VAR_NAME = 'http-equiv';
    const VAR_CONTENT = 'content';

    const TYPE_CONTENT = 'Content-Type';

    /**
     * @param string $sTitle
     */
    public function __construct ( $sType, $sSub = '' )
    {
        parent::__construct( array(
            self::NAME => new CustomArray(array(
                self::VAR_NAME => self::TYPE_CONTENT,
                self::VAR_CONTENT => $this->getType( $sType . $sSub )
            ))
        ));

    }

    public function getType ( $sType )
    {
        switch ( $sType )
        {
            case \Defines\Extension::HTML:
                $sContentType = 'text/html';
                break;
            default:
                $sContentType = "application/$sType";
        }
        return "{$sContentType};charset=utf-8";

    }

}
