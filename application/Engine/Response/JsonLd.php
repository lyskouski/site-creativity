<?php namespace Engine\Response;

/**
 * Description of StructuredData
 *
 * @since 2016-10-12
 * @author Viachaslau Lyskouski
 */
class JsonLd extends \ArrayObject
{
    public function add($mix)
    {
        $i = key($this);
        $this[$i] = array_merge($this[$i], $mix);
    }

    public function focus($key)
    {
        reset($this);
        if (array_key_exists($key, $this)) {
            while (key($this) !== $key) {
                next($this);
            }
            $result = current($this);
        } else {
            $result = null;
        }
        return $result;
    }
}
