<?php

namespace Data\Doctrine\Proxy\__CG__\Data\Doctrine\Main;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ContentViews extends \Data\Doctrine\Main\ContentViews implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     *
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'content', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'contentCount', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'visitors', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'votesUp', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'votesDown'];
        }

        return ['__isInitialized__', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'content', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'contentCount', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'visitors', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'votesUp', '' . "\0" . 'Data\\Doctrine\\Main\\ContentViews' . "\0" . 'votesDown'];
    }

    /**
     *
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (ContentViews $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     *
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }


    /**
     * {@inheritDoc}
     */
    public function setContentCount($contentCount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContentCount', [$contentCount]);

        return parent::setContentCount($contentCount);
    }

    /**
     * {@inheritDoc}
     */
    public function getContentCount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContentCount', []);

        return parent::getContentCount();
    }

    /**
     * {@inheritDoc}
     */
    public function setVisitors($visitors)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVisitors', [$visitors]);

        return parent::setVisitors($visitors);
    }

    /**
     * {@inheritDoc}
     */
    public function getVisitors()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVisitors', []);

        return parent::getVisitors();
    }

    /**
     * {@inheritDoc}
     */
    public function setVotesUp($votesUp)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVotesUp', [$votesUp]);

        return parent::setVotesUp($votesUp);
    }

    /**
     * {@inheritDoc}
     */
    public function getVotesUp()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVotesUp', []);

        return parent::getVotesUp();
    }

    /**
     * {@inheritDoc}
     */
    public function setVotesDown($votesDown)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVotesDown', [$votesDown]);

        return parent::setVotesDown($votesDown);
    }

    /**
     * {@inheritDoc}
     */
    public function getVotesDown()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVotesDown', []);

        return parent::getVotesDown();
    }

    /**
     * {@inheritDoc}
     */
    public function setContent(\Data\Doctrine\Main\Content $content)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContent', [$content]);

        return parent::setContent($content);
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContent', []);

        return parent::getContent();
    }

}