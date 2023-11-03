<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Style extends MetaAbstract
{
    const NAME = 'link';
    const VAR_REL = 'rel';
    const VAR_HREF = 'href';
    const VAR_MEDIA = 'media';
    const TYPE_STYLESHEET = 'stylesheet';

    public function __construct($sPath = '', $sContent = '')
    {
        if ($sContent) {
            $aResult = array(
                'style' => array(
                    self::VAR_CONTENT => $sContent
                )
            );

        } else {
            $aResult = array(
                self::NAME => new CustomArray(array(
                    self::VAR_REL => self::TYPE_STYLESHEET,
                    self::VAR_HREF => '',
                    self::VAR_MEDIA => 'all'
                ))
            );
            $this->addMetaPath($aResult, $sPath, self::VAR_HREF);
        }

        parent::__construct($aResult);
    }

    public function setSrc($src)
    {
        $this[self::NAME][self::VAR_HREF] = $src;
        return $this;
    }

    public function getSrc()
    {
        $src = '';
        if (array_key_exists(self::NAME, $this)) {
            $src = $this[self::NAME][self::VAR_HREF];
        }
        return $src;
    }

    public function getExtension()
    {
        return 'css';
    }

    /**
     * Set media type
     * @sample
     *   all - All devices
     *   braille - Devices based on the Braille system, designed for blind people
     *   handheld - Handhelds, smart phones, devices with small width of the screen
     *   print - The printing apparatus like the printer
     *   screen - The monitor screen
     *   speech - Voice synthesizers, as well as programs for the reproduction of text aloud. This includes speech browsers
     *   projection - Projector
     *   tty - Teletypes, terminals, portable devices with limited display capability. For them to be used as a pixel unit.
     *   tv - TV
     *
     * @param string $sType - all|braille|handheld|print|screen|speech|projection|tty|tv
     * @return \Engine\Response\Meta\Style
     */
    public function setMedia($sType = 'all')
    {
        if (array_key_exists(self::NAME, $this)) {
            $this[self::NAME][self::VAR_MEDIA] = $sType;
        }
        return $this;
    }

    public function isEqual(MetaInterface $oMeta)
    {
        $equal = false;
        if (
            array_key_exists(self::NAME, $oMeta)
            && array_key_exists(self::VAR_HREF, $oMeta[self::NAME])
            && $oMeta[self::NAME][self::VAR_HREF]
            && $oMeta[self::NAME][self::VAR_MEDIA] === $this[self::NAME][self::VAR_MEDIA]
            && $oMeta[self::NAME][self::VAR_HREF] === $this[self::NAME][self::VAR_HREF]
        ) {
            $equal = true;
        }

        return $equal;
    }
}
