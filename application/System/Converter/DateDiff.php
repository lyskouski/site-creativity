<?php namespace System\Converter;

/**
 * Operations with dates
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package System/Converter
 */
class DateDiff
{

    /**
     * Get microtime difference
     *
     * @param string $mktime - 'micro seconds'
     * @return float
     */
    public function getMkDiff($mktime)
    {
        $curr = explode(' ', microtime());
        $a = explode(' ', $mktime);
        return ($curr[1] - $a[1]) + ($curr[0] - $a[0]);
    }

    /**
     * Check Russian plural modification
     *
     * @param string $key
     * @param \DateInterval $diff
     * @return boolean
     */
    protected function checkPlural($key, \DateInterval $diff) {
        $format = $diff->format($key);
        $type = substr($format, -1, 1) < 5 && substr($format, -1, 1) > 1;
        return ($type && $format > 0 && $format < 10)
            || ($type && $format > 20);
    }

    /**
     * Get date representation
     *
     * @param integer $number
     * @param string $text
     * @return string
     */
    protected function getDate($number, $text) {
        $compile = sprintf($text, (string) $number);
        if ($compile === $text) {
            $compile = "$number $text";
        }
        return $compile;
    }

    /**
     * Special text representation for slavic languages
     * @sample 21, 31, 101
     *
     * @return boolean
     */
    protected function checkSlavic()
    {
        $lang = \System\Registry::translation()->getTargetLanguage();
        $slavic = array(
            \Defines\Language::BE,
            \Defines\Language::UK,
            \Defines\Language::RU
        );
        return in_array($lang, $slavic);
    }

    /**
     * Get a text value of interval
     *
     * @param \DateInterval $oDiff
     * @return string
     */
    public function getInterval(\DateInterval $oDiff)
    {
        $oTranslate = \System\Registry::translation();
        switch (true) {
            case $oDiff->format('%y') == 1:
                $sResult = $oTranslate->sys('LB_YEAR_BEFORE');
                break;
            case $this->checkSlavic() && substr($oDiff->format('%y'), -1, 1) == 1:
                $sResult = $this->getDate($oDiff->format('%y'), $oTranslate->sys('LB_YEAR_BEFORE'));
                break;
            case $this->checkPlural('%y', $oDiff):
                $sResult = $this->getDate($oDiff->format('%y'), $oTranslate->sys('LB_YEAR4_BEFORE'));
                break;
            case $oDiff->format('%y') > 0:
                $sResult = $this->getDate($oDiff->format('%y'), $oTranslate->sys('LB_YEARS_BEFORE'));
                break;
            case $oDiff->format('%m') == 1:
                $sResult = $oTranslate->sys('LB_MONTH_BEFORE');
                break;
            case $this->checkSlavic() && substr($oDiff->format('%m'), -1, 1) == 1:
                $sResult = $this->getDate($oDiff->format('%m'), $oTranslate->sys('LB_MONTH_BEFORE'));
                break;
            case $this->checkPlural('%m', $oDiff):
                $sResult = $this->getDate($oDiff->format('%m'), $oTranslate->sys('LB_MONTH4_BEFORE'));
                break;
            case $oDiff->format('%m') > 0:
                $sResult = $this->getDate($oDiff->format('%m'), $oTranslate->sys('LB_MONTHS_BEFORE'));
                break;
            case $oDiff->format('%d') == 1:
                $sResult = $oTranslate->sys('LB_DAY_BEFORE');
                break;
            case $this->checkSlavic() && substr($oDiff->format('%d'), -1, 1) == 1:
                $sResult = $this->getDate($oDiff->format('%d'), $oTranslate->sys('LB_DAY_BEFORE'));
                break;
            case $this->checkPlural('%d', $oDiff):
                $sResult = $this->getDate($oDiff->format('%d'), $oTranslate->sys('LB_DAY4_BEFORE'));
                break;
            case $oDiff->format('%d') > 0:
                $sResult = $this->getDate($oDiff->format('%d'), $oTranslate->sys('LB_DAYS_BEFORE'));
                break;
            case $oDiff->format('%h') == 1:
                $sResult = $oTranslate->sys('LB_HOUR_BEFORE');
                break;
            case $this->checkSlavic() && substr($oDiff->format('%h'), -1, 1) == 1:
                $sResult = $this->getDate($oDiff->format('%h'), $oTranslate->sys('LB_HOUR_BEFORE'));
                break;
            case $this->checkPlural('%h', $oDiff):
                $sResult = $this->getDate($oDiff->format('%h'), $oTranslate->sys('LB_HOUR4_BEFORE'));
                break;
            case $oDiff->format('%h') > 0:
                $sResult = $this->getDate($oDiff->format('%h'), $oTranslate->sys('LB_HOURS_BEFORE'));
                break;
            case $oDiff->format('%i') == 1:
                $sResult = $oTranslate->sys('LB_SECOND_BEFORE');
                break;
            case $this->checkSlavic() && substr($oDiff->format('%i'), -1, 1) == 1:
                $sResult = $this->getDate($oDiff->format('%i'), $oTranslate->sys('LB_SECONDS_BEFORE'));
                break;
            case $this->checkPlural('%i', $oDiff):
                $sResult = $this->getDate($oDiff->format('%i'), $oTranslate->sys('LB_SECOND4_BEFORE'));
                break;
            case $oDiff->format('%i') > 0:
                $sResult = $this->getDate($oDiff->format('%i'), $oTranslate->sys('LB_SECONDS_BEFORE'));
                break;
            default:
                $sResult = $oTranslate->sys('LB_SECOND_BEFORE');
        }
        return $sResult;
    }

}
