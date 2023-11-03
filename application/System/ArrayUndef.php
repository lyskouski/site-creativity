<?php namespace System;

/**
 * Array with missing values
 * @note to resolve undefined operations and return 'n.a.' for them instead
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
class ArrayUndef extends \ArrayObject
{

    protected $aMissing = array();
    protected $undef;

    /**
     * Get value from array (or default)
     *
     * @param array $aList
     * @param mixed $mName
     * @param mixed $mDefault
     * @param integer $iFilter - FILTER_VALIDATE_INT
     * @return mixed
     */
    public static function getValue($aList, $mName, $mDefault, $iFilter)
    {
        $oSearch = new self($aList);
        if ($oSearch[$mName] instanceof ArrayUndef) {
            $oSearch[$mName] = $mDefault;
        }
        return filter_var($oSearch[$mName], $iFilter);
    }

    /**
     * What should be returned if value is missing
     *
     * @param mixed $value
     */
    public function setUndefined($value)
    {
        $this->undef = $value;
    }

    /**
     * Returns the value at the specified index
     * @link http://php.net/manual/en/arrayobject.offsetget.php
     *
     * @param mixed $sIndex - the index with the value.
     * @return mixed The value at the specified index or NULL
     */
    public function offsetGet($sIndex)
    {
        if ($this->offsetExists($sIndex)) {
            $mValue = parent::offsetGet($sIndex);
        } elseif (is_callable($this->undef)) {
            $cal = $this->undef;
            $mValue = $cal();
        } elseif (!is_null($this->undef)) {
            $mValue = $this->undef;
        } else {
            $mValue = new self();
        }
        // Defined value
        if (!$this->offsetExists($sIndex)) {
            $this[$sIndex] = $this->aMissing[$sIndex] = $mValue;
        }
        return $mValue;
    }

    public function getMissings()
    {
        foreach ($this->aMissing as $s => $m) {
            if ($m instanceof ArrayUndef) {
                $this->aMissing[$s] = $m->getMissings();
            }
        }
        return $this->aMissing;
    }

    /**
     * Get full-level merged array
     *
     * @param array $aAnother
     * @param \System\ArrayUndef $aTarget
     * @return array
     */
    public function union(array $aAnother, $aTarget = null)
    {
        if (is_null($aTarget)) {
            $aTarget = $this;
        } else {
            $aTarget = new self($aTarget);
        }
        foreach ($aAnother as $s => $m) {
            if (is_array($m)) {
                $aTarget[$s] = (new self($m))->union($m, $aTarget[$s]);
            }
        }
        return (array) $aTarget;
    }

    /**
     * Is used to avoid broken interraction with a missing object
     *
     * @return \System\ArrayUndef
     */
    public function __call($sName, $aParams)
    {
        return new self();
    }

    /**
     * Return '' for a missing value
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }

}
