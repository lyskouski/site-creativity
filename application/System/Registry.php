<?php namespace System;

use Engine\Request;
use Engine\Response\Translation;

/**
 * Singelton pattern for global classes
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Registry
{

    /**
     * @var array
     */
    protected static $aData = array();

    /**
     * Save object in the global scope
     *
     * @param string $sClassName
     * @param mixed $oClass
     * @return mixed
     * @throws Exception - Missing global scope {class name}
     */
    protected static function init($sClassName, $oClass = null)
    {
        if (!is_null($oClass)) {
            self::$aData[$sClassName] = $oClass;
        }
        if (self::isMissing($sClassName)) {
            throw new \Error\Validation("{$sClassName} is not defined");
        }
        return self::$aData[$sClassName];
    }

    public static function set($className, $class)
    {
        if (self::isMissing($className)) {
            self::init($className, $class);
        } else {
            throw new \Exception('Already defined!');
        }
    }

    public static function get($className)
    {
        self::init($className);
    }

    /**
     * Check availability
     *
     * @param string $sClassName
     * @return boolean
     */
    protected static function isMissing($sClassName)
    {
        return !array_key_exists($sClassName, self::$aData);
    }

    /**
     * Register (get/set) configuration object
     *
     * @param \Engine\Request\Config $oConfig
     * @return \Engine\Request\Config
     */
    public static function setConfig(Request\Config $oConfig = null)
    {
        return self::init(__FUNCTION__, $oConfig);
    }

    /**
     * Register (get/set) configuration object
     *
     * @return \Engine\Request\Config
     */
    public static function config()
    {
        return self::setConfig(null);
    }

    /**
     * Register (get/set) configuration object
     *
     * @param \Access\User $oConfig
     * @return \Access\User
     */
    public static function setUser(\Access\User $oUser = null)
    {
        return self::init(__FUNCTION__, $oUser);
    }

    /**
     * Register (get/set) configuration object
     *
     * @return \Access\User
     */
    public static function user()
    {
        return self::setUser(null);
    }

    /**
     * Register (get/set) debug object
     *
     * @param \System\Logger $oLogger
     * @return \System\Logger
     */
    public static function setLogger(Logger $oLogger = null)
    {
        if (!is_null($oLogger) && !self::isMissing(__FUNCTION__)) {
            $oLogger->filter(self::$aData[__FUNCTION__]);
        }
        return self::init(__FUNCTION__, $oLogger);
    }

    /**
     * Register (get/set) debug object
     *
     * @return \System\Logger
     */
    public static function logger()
    {
        if (self::isMissing('setLogger')) {
            self::setLogger(new \System\Logger(\Defines\Logger::getDefault()));
        }
        return self::setLogger(null);
    }

    /**
     * Register translation class
     *
     * @param \Engine\Response\Translation $oTranlsation
     * @return \Engine\Response\Translation
     */
    public static function setTranslation(Translation $oTranlsation = null)
    {
        return self::init(__FUNCTION__, $oTranlsation);
    }

    /**
     * Register translation class
     *
     * @return \Engine\Response\Translation
     */
    public static function translation()
    {
        // translation should be always available
        if (is_null(self::$aData['setTranslation'])) {
            self::$aData['setTranslation'] = new Translation(\Defines\Language::getDefault());
        }
        // get translation object
        return self::setTranslation(null);
    }

    public static function updateStatistics()
    {
        $sUrl = (new \Engine\Request\Input)->getUrl(null);
        $oHelper = new \Data\ContentHelper();
        /* @var $oStat \Data\Doctrine\Main\ContentViews */
        $oStat = $oHelper->getRepository()->getPages($sUrl);
        if ($oStat->getVisitors()) {
            $oStat->setVisitors(1 + $oStat->getVisitors());
            $oHelper->getEntityManager()->persist($oStat);
            $oHelper->getEntityManager()->flush();
        }
        self::init('stat', $oStat);
    }

    /**
     * Get page statistics
     *
     * @return \Data\Doctrine\Main\ContentViews
     */
    public static function stat()
    {
        if (!array_key_exists(__FUNCTION__, self::$aData)) {
            self::updateStatistics();
        }
        return self::init(__FUNCTION__);
    }

    /**
     * Init json-ld
     *
     * @param \Engine\Response\JsonLd|null $obj
     * @return \Engine\Response\JsonLd
     */
    public static function structured($obj = null)
    {
        if (!array_key_exists(__FUNCTION__, self::$aData)) {
            $obj = new \Engine\Response\JsonLd([]);
        }
        return self::init(__FUNCTION__, $obj);
    }

    /**
     * Get Doctrine ORM connection
     * @link http://docs.doctrine-project.org/en/latest/
     *
     * @param string $type
     * @return \Doctrine\ORM\EntityManager
     */
    public static function connection($type = \Defines\Connector::MYSQL_DOCTRINE)
    {
        $typeName = __FUNCTION__ . $type;
        if (self::isMissing($typeName)) {
            $doctrine = new \System\Database\Connector($type);
            self::init($typeName, $doctrine->getConnection());
        }
        return self::init($typeName);
    }

    public static function changeConnection($type, $object)
    {
        self::init("connection{$type}", $object);
    }

    public static function cron()
    {
        return self::setCron(null);
    }

    public static function setCron($value = null)
    {
        return self::init(__FUNCTION__, $value);
    }
}
