<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Link extends MetaAbstract
{

    const NAME = 'link';
    const VAR_REL = 'rel';
    const VAR_HREF = 'href';
    const TYPE_IMAGE_SRC = 'image_src';

    public function __construct($sName, $sContent)
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    self::VAR_REL => $sName,
                    self::VAR_HREF => $sContent
                ))
            )
        );
    }

    public function addExtra($name, $value)
    {
        $this[self::NAME][$name] = $value;
        return $this;
    }

    public function isEqual(MetaInterface $oMeta)
    {
        foreach ($this as $aList) {
            foreach ($oMeta as $aSearch) {
                if (
                    !is_string($aSearch) && $aSearch[self::VAR_REL] && $aSearch[self::VAR_REL] === $aList[self::VAR_REL]
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}
