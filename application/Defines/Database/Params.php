<?php namespace Defines\Database;

/**
 * Database formats and data attributes
 *
 * @author Viachaslau Lyskouski
 * @since 2015-10-07
 * @package Defines/Database
 */
class Params
{

    /**
     * Limitation on attemps for `user_protocol`
     * @see \Data\Doctrine\Main\UserProtocol
     *
     * @var integer
     */
    const USER_PROTOCOL_LIMIT = 5;

    const TIMESTAMP = 'Y-m-d H:i:s';

    const DATE_FORMAT = 'Y-m-d';

    const DEFAULT_USER = 0;

    const COOKIE_DAYS = 30;

    const MAX_RATING = 5;
    const MAX_USER_RATING = 10;

    const RATING_PRECISION = 1;

    const COMMENTS_ON_PAGE = 10;

    const CONTENT_ON_PAGE = 20;

    const CACHE_IMAGE = 3600;

    const CACHE_CONTENT = 259200; // 3 days (86400)

    const WORKFLOW_ACTIVE = 1;

    const ENCODING = 'utf8';

    /**
     * Get calculated rating
     *
     * @param \Data\Doctrine\Main\ContentViews $oStat
     * @return float
     */
    public static function getRating(\Data\Doctrine\Main\ContentViews $oStat)
    {
        if (!$oStat->getVotesUp()) {
            $oStat->setVotesUp(1);
        }
        return round(self::MAX_RATING * $oStat->getVotesUp() / ($oStat->getVotesUp() + $oStat->getVotesDown()), self::RATING_PRECISION);
    }

    /**
     * Get formated value by locale
     *
     * @param float $number
     * @param integer $decimals
     * @return string
     */
    public static function getNumber($number, $decimals = self::RATING_PRECISION)
    {
        $locale = localeconv();
        if (!strlen($locale['thousands_sep'])) {
            $locale['thousands_sep'] = "'";
        }
        return number_format($number, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
    }

    /**
     * Get page counts
     *
     * @param \Data\Doctrine\Main\ContentViews $oStat
     * @return integer
     */
    public static function getPageCount($mCount, $iMax = self::COMMENTS_ON_PAGE)
    {
        if ($mCount instanceof \Data\Doctrine\Main\ContentViews) {
            $mCount = $mCount->getContentCount();
        }
        return ceil($mCount / $iMax) - 1;
    }
}
