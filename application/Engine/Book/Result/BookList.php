<?php namespace Engine\Book\Result;

/**
 * Viewer functionality
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search/Result
 */
class BookList extends \System\ArrayUndef
{
    public function __construct($input = '[]', $flags = 0, $iterator_class = "ArrayIterator")
    {
        $this->undef = function() {
            return new Book();
        };
        parent::__construct($input, $flags, $iterator_class);
    }

    public function current()
    {
        $o = current($this);
        if (!$o) {
            $o = new Book();
        }
        return $o;
    }
}
