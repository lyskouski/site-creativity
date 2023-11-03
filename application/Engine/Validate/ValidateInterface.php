<?php namespace Engine\Validate;

/**
 * interface for validation classes
 *
 * @since 2015-06-18
 * @author Viachaslau Lyskouski
 */
interface ValidateInterface
{

    /**
     * Return value back if it's valid
     *
     * @param mixed $mValue
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed - get back sanitized $mValue
     */
    public function sanitize($mValue, $mFilterType = FILTER_DEFAULT);
}
