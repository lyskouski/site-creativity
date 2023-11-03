<?php namespace Engine\Response\Meta;

/**
 * Javasript files
 * <script src="/js/index.js?20150409" type="text/javascript"></script>
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Script extends MetaAbstract
{

    const NAME = 'script';
    const VAR_SRC = 'src';
    const VAR_TYPE = 'type';

    public function __construct($sPath = '', $sContent = '')
    {
        $aResult = array(
            self::NAME => new CustomArray(array(
                self::VAR_TYPE => 'text/javascript',
                self::VAR_SRC => ''
            ))
        );

        $this->addMetaPath($aResult, $sPath, self::VAR_SRC);

        if ($sContent) {
            unset($aResult[self::NAME][self::VAR_SRC]);
            $aResult[self::NAME][self::VAR_CONTENT] = $sContent;
        }

        parent::__construct($aResult);
    }

    public function getExtension()
    {
        return 'js';
    }

    public function setSrc($src)
    {
        $this[self::NAME][self::VAR_SRC] = $src;
        return $this;
    }

    public function getSrc()
    {
        $src = '';
        if (array_key_exists(self::VAR_SRC, $this[self::NAME])) {
            $src = $this[self::NAME][self::VAR_SRC];
        }
        return $src;
    }

    public function getContent()
    {
        return $this[self::NAME][self::VAR_CONTENT];
    }

    public function isEqual(MetaInterface $oMeta)
    {
        if (
            array_key_exists(self::NAME, $oMeta)
            && array_key_exists(self::VAR_SRC, $oMeta[self::NAME])
            && $oMeta[self::NAME][self::VAR_SRC]
            && $oMeta[self::NAME][self::VAR_SRC] === $this[self::NAME][self::VAR_SRC]
        ) {
            return true;
        }

        return false;
    }
}
