<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Favicon extends MetaAbstract
{

    const NAME = 'link';
    const VAR_TYPE = 'href';
    const VAR_REL = 'rel';
    const VAR_HREF = 'href';

    const TYPE_IMAGE_SRC = 'image_src';

    // <link id="favicon" href="/favicon.png" type="image/png" rel="shortcut icon">,
    public function __construct ()
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    self::VAR_REL => 'shortcut icon',
                    self::VAR_TYPE => 'image/png',
                    self::VAR_HREF => '/favicon.png'
                ))
            )
        );

    }

    public function isEqual ( MetaInterface $oMeta )
    {
        return $oMeta instanceof Favicon;

    }

}
