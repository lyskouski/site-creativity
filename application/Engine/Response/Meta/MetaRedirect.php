<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class MetaRedirect extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'http-equiv';
    const VAR_CONTENT = 'content';  
    // basic
    const TYPE_REDIRECT = 'refresh';

    /**
     * Redirect to $sUrl
     * 
     * @param integer $iTime - delay before redirect
     * @param string $sUrl - target ulr
     */
    public function __construct ( $iTime, $sUrl )
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    self::VAR_NAME => self::TYPE_REDIRECT,
                    self::VAR_CONTENT => "$iTime;url=$sUrl"
                ))
            )
        );

    }

}
