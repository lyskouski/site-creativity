<?php namespace Engine;

use System\Registry;
use Engine\Response\Meta;

/**
 * Viewer functionality
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Response
{

    const POSITION_END = -1;
    const POSITION_BEGING = 0;

    /**
     * @var string
     */
    protected $sLayoutType;

    /**
     * @var array
     */
    protected $aContent = array();

    /**
     * @var array
     */
    protected $aMeta = array();

    /**
     * @var array
     */
    protected $aHeaders = array();

    /**
     * Add user auth into the view
     */
    public function __construct()
    {
        $this->sLayoutType = \Defines\Extension::HTML;
    }

    /**
     * @param string $sLayoutType
     * @return \Engine\Response
     */
    public function setLayoutType($sLayoutType)
    {
        $this->sLayoutType = $sLayoutType;
        return $this;
    }

    /**
     * Add metadata in response
     *
     * @param \Engine\Response\Meta\MetaInterface $oMetaData
     * @param boolean $bIgnoreCheck
     * @param (boolean|string|integer) $mUnshift
     * @param boolean $bIgnoreCheck
     */
    public function meta(Meta\MetaInterface $oMetaData, $bIgnoreCheck = false, $mUnshift = false)
    {
        if (!$bIgnoreCheck) {
            foreach ($this->aMeta as $i => $oCurrMeta) {
                if ($oCurrMeta->isEqual($oMetaData)) {
                    unset($this->aMeta[$i]);
                    break;
                }
            }
        }
        // Add at the beginning
        if (is_bool($mUnshift)) {
            array_unshift($this->aMeta, $oMetaData);
        // Find target URL
        } elseif (is_string($mUnshift)) {
            foreach ($this->aMeta as $i => $oCurrMeta) {
                if ($oCurrMeta->getSrc() === $mUnshift) {
                    break;
                }
            }
            $this->meta($oMetaData, $bIgnoreCheck, $i+1);
        // Add to the specified position
        } elseif ($mUnshift) {
            $this->aMeta = array_merge(
                array_slice($this->aMeta, 0, $mUnshift),
                [$oMetaData],
                array_slice($this->aMeta, $mUnshift)
            );
        // Add to end
        } else {
            $this->aMeta[] = $oMetaData;
        }
    }

    /**
     * Add to title description
     *
     * @param string $sTitle
     */
    public function title($sTitle, $delimiter = Meta\Title::DELIMITER)
    {
        $oTitle = new Meta\Title($sTitle);
        $bMissing = true;
        foreach ($this->aMeta as $i => $oCurrMeta) {
            if ($oCurrMeta->isEqual($oTitle)) {
                $this->aMeta[$i]->setTitle($sTitle . $delimiter . $oCurrMeta->getTitle());
                $bMissing = false;
                break;
            }
        }
        if ($bMissing) {
            $this->meta($oTitle, true);
        }
        $this->meta(new Meta\Ogp(Meta\Ogp::TYPE_TITLE, $sTitle));
    }

    public function titleOverride($sTitle, $delimiter = Meta\Title::DELIMITER) {
        if ($delimiter) {
            if ($sTitle) {
                $sTitle .= $delimiter;
            }
            $sTitle .= \System\Registry::translation()->sys('LB_SITE_TITLE');
        }
        $oTitle = new Meta\Title($sTitle);
        $this->meta($oTitle);
        $this->meta(new Meta\Ogp(Meta\Ogp::TYPE_TITLE, $sTitle));
    }

    /**
     * Return metainformation
     * @return array
     */
    public function getMeta()
    {
        return array_filter($this->aMeta, function($oMeta) {
            return !$oMeta instanceof Meta\Script;
        });
    }

    /**
     * Return scripts list
     * @return array
     */
    public function getScripts()
    {
        return array_values(array_filter($this->aMeta, function($oMeta) {
            return $oMeta instanceof Meta\Script;
        }));
    }

    /**
     *
     * @param string $sType
     * @param string $sInfo
     * @return \Engine\Response
     */
    public function header($sType, $sInfo = '')
    {
        $this->aHeaders[$sType] = $sInfo;
        return $this;
    }

    /**
     * Return headers information
     */
    public function sendHeaders($contentList, $code = \Defines\Response\Code::E_OK)
    {
        $sFile = '';
        $iLine = 0;
        if (headers_sent($sFile, $iLine)) {
            $aBacktrace = array(
                'file' => $sFile,
                'line' => $iLine
            );
            \System\Registry::logger()->emergency("Headers issue", $aBacktrace);
            return;
        }

        /* @var $template \Engine\Response\Template  */
        foreach ($contentList as $template) {
            if ($template->get(\Error\TextAbstract::E_CODE)) {
                $code = $template->get(\Error\TextAbstract::E_CODE);
                break;
            }
        }

        if ($this->sLayoutType === \Defines\Extension::JSON) {// && $code === \Defines\Response\Code::E_GOTO) {
            $code = \Defines\Response\Code::E_OK;
        }

        header('HTTP/1.1 ' . $code . ' ' . \Defines\Response\Code::getHeader($code));
        foreach ($this->aHeaders as $key => $content) {
            if ($key === 'Content-type') {
                $content .= '; charset="utf-8"';
            }
            header("$key: $content");
        }
    }

    /**
     * Add to response
     *
     * @param \Engine\Response\Template $oTemplate
     * @return \Engine\Response
     */
    public function push(Response\Template $oTemplate, $iPosition = self::POSITION_END)
    {
        switch ($iPosition) {
            case self::POSITION_END:
                array_push($this->aContent, $oTemplate);
                break;

            case self::POSITION_BEGING:
                array_unshift($this->aContent, $oTemplate);
                break;

            default:
                $aContent = array_slice($this->aContent, 0, $iPosition);
                array_push($aContent, $oTemplate);
                $this->aContent = array_merge($aContent, array_slice($this->aContent, $iPosition));
        }
        return $this;
    }

    /**
     * Clear response
     * - in case of errors
     *
     * @return \Engine\Response
     */
    public function clear()
    {
        $this->aContent = array();
        return $this;
    }

    /**
     * Return array of \Engine\Response\Template objects
     *
     * @return array
     */
    public function getContent()
    {
        return $this->aContent;
    }

    /**
     * Return response to browser
     */
    public function flush()
    {
        require Registry::config()->getAppPath() . "../Layouts/{$this->sLayoutType}.php";
    }
}
