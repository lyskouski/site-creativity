<?php namespace Engine\Response\Meta;

/**
 * Meta - Title
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Title extends MetaAbstract
{

    const NAME = 'title';
    const DELIMITER = ' - '; //»

    protected $prefix = '';

    /**
     * @param string $title
     */

    public function __construct($title)
    {
        parent::__construct([]);
        $this->prefix = \System\Registry::config()->getTitlePrefix();
        $this->setTitle($title);
    }

    /**
     * Override comarison mechanizm
     *
     * @param \Engine\Response\Meta\MetaInterface $oMeta
     * @return boolean
     */
    public function isEqual(MetaInterface $oMeta)
    {
        return $oMeta instanceof Title;
    }

    public function getRepresentationType()
    {
        return self::NAME;
    }

    /**
     * Return title name
     *
     * @return string
     */
    public function getTitle()
    {
        return substr($this[self::NAME], strlen($this->prefix));
    }

    /**
     * Set title name
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this[self::NAME] = $this->prefix . $title;
    }
}
