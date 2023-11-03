<?php namespace System;

/**
 * Different known hacks to operate with PHP instances
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
class Aggregator
{

    public function getValue($aList, $mKey, $mDefault = null)
    {
        $mResult = $mDefault;
        if (array_key_exists($mKey, $aList)) {
            $mResult = $aList[$mKey];
        }
        return $mResult;
    }

    public function func2array($sClassName, $sFilter = '')
    {
        $aList = array();
        $oList = new \ReflectionClass($sClassName);
        foreach ($oList->getMethods() as $oMethod) {
            /* @var $oMethod \ReflectionMethod */
            if (strpos($oMethod->getName(), $sFilter) !== false) {
                $aList[] = $oMethod->getName();
            }
        }
        return $aList;
    }

    /**
     * Get field from controller object
     *
     * @param string $sClass
     * @param string $sValue
     * @return mixed
     */
    protected function class4value($sClass, $sValue)
    {
        $oProperty = new \ReflectionProperty($sClass, $sValue);
        $oProperty->setAccessible(true);
        return $oProperty->getValue($sClass);
    }

    /**
     * Take const from object and return array
     *
     * @param string $sClassName
     * @return array
     */
    public function const2array($sClassName, $bAssociate = false)
    {
        $oList = new \ReflectionClass($sClassName);
        $aList = $oList->getConstants();
        return $bAssociate ? array_flip($aList) : array_values($aList);
    }

    /**
     * function is_array
     * @sample \System\Converter::is_array
     *
     * This function is part of the PHP manual
     * @note \ArrayObject will return false for basic in_array function
     * @link https://bugs.php.net/bug.php?id=52626
     * @link https://bugs.php.net/bug.php?id=62059
     *
     * @param type $mValue
     * @param boolean
     */
    public static function is_array($mValue)
    {
        return is_array($mValue) || $mValue instanceof \ArrayObject;
    }
}
