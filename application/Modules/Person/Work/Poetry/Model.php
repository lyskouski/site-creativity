<?php namespace Modules\Person\Work\Poetry;

/**
 * Model to create new article
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Model extends \Modules\Person\Work\Article\Model
{

    /**
     * Get initial url for topic
     * @abstract for other classes
     *
     * @return string
     */
    public function getUrl()
    {
        return 'person/work/poetry';
    }

    /**
     * Get list of categories
     * @abstract for other classes
     *
     * @return array
     */
    public function getTyped()
    {
        $fullList = \Defines\Catalog::getOeuvre();
        return array($fullList[1]);
    }
}
