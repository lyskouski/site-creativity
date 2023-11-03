<?php namespace Engine\Response\Meta;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class CustomArray extends \ArrayObject
{

    public function offsetGet($index)
    {
        if (isset($this[$index])) {
            return parent::offsetGet($index);
        }
    }
}
