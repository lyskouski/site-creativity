<?php namespace Engine\Response;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response
 */
class Headers
{
    /**
     * @var \System\Minify\MetaFiles
     */
    protected $meta;
    protected $isMin;

    public function __construct()
    {
        $this->meta = new \System\Minify\MetaFiles();
        $this->isMin = \System\Registry::config()->getMinimize();
    }

    protected function prepareMeta($name, $attr)
    {
        $metaString = '';
        if (is_array($attr) || $attr instanceof Meta\CustomArray) {
            $metaString = "<{$name}";

            // Content inside
            $content = '';
            if (isset($attr[Meta\MetaAbstract::VAR_CONTENT])) {
                $content = $attr[Meta\MetaAbstract::VAR_CONTENT];
                unset($attr[Meta\MetaAbstract::VAR_CONTENT]);
            }

            foreach ($attr as $sN => $sV) {
                $metaString .= " {$sN}=\"{$sV}\"";
            }

            if ($name === 'script' || $name === 'style' && $content) {
                $metaString .= ">{$content}</{$name}>";
            } else {
                $metaString .= " />";
            }
        } else {
            $metaString = "<$name>$attr</$name>";
        }
        return $metaString;
    }

    /**
     * Prepare simple list of strings with meta-data
     *
     * @param array $aResponse - array of \Engine\Response\Meta\MetaAbstract
     * @return array
     */
    public function processMeta(array $aResponse)
    {
        $aMetaData = array();
        /* @var $oMeta \Engine\Response\Meta\MetaAbstract  */
        foreach ($aResponse as $oMeta) {
            // Check minimized
            if ($oMeta instanceof Meta\Script || $oMeta instanceof Meta\Style) {
                $list = $this->meta->getList($oMeta->getExtension());
                //$path = $this->meta->getPrefix($oMeta->getExtension());
                if ($this->isMin && in_array($oMeta->getSrc(), $list)) {
                    continue;
                }
                //$oMeta->setSrc($path . $oMeta->getSrc() . $this->meta->getSuffix());
                if ($oMeta->getSrc()) {
                    $oMeta->setSrc($this->meta->getPath($oMeta->getSrc(), $oMeta->getExtension()));
                }
            }

            foreach ($oMeta as $mSubName => $mSubAttr) {
                $aMetaData[] = $this->prepareMeta($mSubName, $mSubAttr);
            }
        }
        return $aMetaData;
    }

}
