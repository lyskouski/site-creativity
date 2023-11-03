<?php namespace Engine\Request\Helper;

/**
 * Communicate with Mercurial (Hg)
 *
 * @since 2016-01-01
 * @author Viachaslau Lyskouski
 * @package Engine/Request/Helper
 */
class Mercurial
{
    protected $dir;

    public function __construct($path)
    {
        $this->dir = "cd $path";
    }

    public function setPull() {
        return (new \System\Shell)->run("{$this->dir};hg pull", false);
    }

    public function setUpdate($revision) {
        return (new \System\Shell)->run("{$this->dir};hg update -r $revision", false);
    }

    protected function parseLog($sOutput)
    {
        $aData = explode('changeset:', $sOutput);
        unset($aData[0]);
        foreach ($aData as $i => $a) {
            $a = explode('user:', $a);
            $sVersion = trim($a[0]);
            $tag = 'tag:';
            if (strpos($sVersion, $tag)) {
                $sVersion = trim(substr($sVersion, 0, strpos($sVersion, $tag)));
            }
            //$a = explode('user:', $a[1]);
            $a = explode('date:', $a[1]);
            $sUser = $a[0];
            $a = explode('summary:', $a[1]);
            $aData[$i] = array(
                'id' => (int)$sVersion,
                'version' => $sVersion,
                'user' => trim($sUser),
                'date' => new \DateTime($a[0]),
                'title' => trim($a[1])
            );
        }
        return $aData;
    }

    /**
     * Get actual version for alpha-version
     *
     * @return string
     */
    public function getSummary()
    {
        $log = (new \System\Shell)->run("{$this->dir};hg summary");
        return trim($log);
    }

    /**
     * Get actual version for alpha-version
     *
     * @return string
     */
    public function getVersion()
    {
        $output = (new \System\Shell)->run("{$this->dir};hg id -i", false);
        return substr(trim($output), 0, -1);
    }

    /**
     * Get list of unversional files
     *
     * @return string
     */
    public function getStat()
    {
        return (new \System\Shell)->run("{$this->dir};hg stat", false);
    }

    /**
     * Get actual version for alpha-version
     *
     * @param integer $limit
     * @return string
     */
    public function getLog($limit = 24)
    {
        $log = (new \System\Shell)->run("{$this->dir};hg log --limit $limit");
        return $this->parseLog($log);
    }
}
