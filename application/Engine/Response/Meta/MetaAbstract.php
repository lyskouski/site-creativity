<?php namespace Engine\Response\Meta;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
abstract class MetaAbstract extends CustomArray implements MetaInterface
{

    const TYPE_MULTIPLE = 0;
    const VAR_CONTENT = '_';

    public function getRepresentationType()
    {
        return self::TYPE_MULTIPLE;
    }

    protected function addMetaPath(&$attr, $filePath, $pathType)
    {
        if (strpos($filePath, '//') === 0) {
            $attr[static::NAME][$pathType] = $filePath;
        } elseif ($filePath) {
            $attr[static::NAME][$pathType] = $filePath;
            $ext = '.' . $this->getExtension();
            if (!strpos($filePath, '?') && strrpos($filePath, $ext) !== strlen($filePath) - strlen($ext)) {
                $attr[static::NAME][$pathType] .= $ext;
            }
        }
    }

    public function getExtension()
    {
        return '';
    }
    public function getSrc()
    {
        return '';
    }

    public function isEqual(MetaInterface $oMeta)
    {
        foreach ($this as $aList) {
            foreach ($oMeta as $aSearch) {
                $b = !is_string($aSearch);
                if (
                    ($b && $aSearch['name'] && $aSearch['name'] === $aList['name'])
                    || ($b && $aSearch['http-equiv'] && $aSearch['http-equiv'] === $aList['http-equiv'])
                ) {
                    return true;
                }
            }
        }
        return false;
    }

}
