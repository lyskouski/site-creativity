<?php namespace Defines\Templates;

/**
 *  Helper for templates
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Templates
 */
class Basic
{

    /**
     * Basic template for titles
     * @see application/Views/Basic/title.php
     *
     * @sample \Engine\Response\Template->partial( Basic::TITLE, array('title'=> '{left side}', 'subtitle' => '{right side}'  ));
     *
     * @var string
     */
    const TITLE = 'Basic/title';

    /**
     * Basic template for titles
     * @see application/Views/Basic/table.php
     *
     * @sample \Engine\Response\Template->partial( Basic::TABLE, array('list'=>array(\Engine\Response\Elements\Topic, ...)) );
     *
     * @var string
     */
    const TABLE = 'Basic/table';

}
