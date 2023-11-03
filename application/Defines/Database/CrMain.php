<?php namespace Defines\Database;

/**
 * Table `cr_main` tables
 *
 * @sample
 *      $oEntityManager = \System\Registry::manager( \Defines\Connector::MYSQL_DOCTRINE );
 *      $oRepository = $this->oEntityManager->getRepository( CrMain::CONTENT );
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Database
 */
class CrMain
{
    /**
     * Connection to `access`-table object
     * @see \Data\Doctrine\Main\Access
     *
     * @var string
     */
    const ACCESS = 'Data\\Doctrine\\Main\\Access';

    /**
     * Connection to `access_action`-table object
     * @see \Data\Doctrine\Main\AccessAction
     *
     * @var string
     */
    const ACCESS_ACTION = 'Data\\Doctrine\\Main\\AccessAction';

    /**
     * Connection to `action`-table object
     * @see \Data\Doctrine\Main\Action
     *
     * @var string
     */
    const ACTION = 'Data\\Doctrine\\Main\\Action';

    /**
     * Connection to `book`-table object
     * @see \Data\Doctrine\Main\Book
     *
     * @var string
     */
    const BOOK = 'Data\\Doctrine\\Main\\Book';

    /**
     * Connection to `book_read`-table object
     * @see \Data\Doctrine\Main\BookRead
     *
     * @var string
     */
    const BOOK_READ = 'Data\\Doctrine\\Main\\BookRead';

    /**
     * Connection to `book_read_history`-table object
     * @see \Data\Doctrine\Main\BookReadHistory
     *
     * @var string
     */
    const BOOK_READ_HISTORY = 'Data\\Doctrine\\Main\\BookReadHistory';

    /**
     * Connection to `book_read_history`-table object
     * @see \Data\Doctrine\Main\BookReadHistoryDaily
     *
     * @var string
     */
    const BOOK_READ_HISTORY_DAILY = 'Data\\Doctrine\\Main\\BookReadHistoryDaily';

    /**
     * Connection to `book_read_history`-table object
     * @see \Data\Doctrine\Main\BookReadHistoryMonthly
     *
     * @var string
     */
    const BOOK_READ_HISTORY_MONTHLY = 'Data\\Doctrine\\Main\\BookReadHistoryMonthly';

    /**
     * Connection to `content`-table object
     * @see \Data\Doctrine\Main\Content
     *
     * @var string
     */
    const CONTENT = 'Data\\Doctrine\\Main\\Content';

    /**
     * Connection to `content_blob`-table object
     * @see \Data\Doctrine\Main\ContentBlob
     *
     * @var string
     */
    const CONTENT_BLOB = 'Data\\Doctrine\\Main\\ContentBlob';

    /**
     * Connection to `content_history`-table object
     * @see \Data\Doctrine\Main\ContentHistory
     *
     * @var string
     */
    const CONTENT_HISTORY = 'Data\\Doctrine\\Main\\ContentHistory';

    /**
     * Connection to `content_new`-table object
     * @see \Data\Doctrine\Main\ContentNew
     *
     * @var string
     */
    const CONTENT_NEW = 'Data\\Doctrine\\Main\\ContentNew';

    /**
     * Connection to `content_series`-table object
     * @see \Data\Doctrine\Main\ContentSeries
     *
     * @var string
     */
    const CONTENT_SERIES = 'Data\\Doctrine\\Main\\ContentSeries';

    /**
     * Connection to `content_views`-table object
     * @see \Data\Doctrine\Main\ContentViews
     *
     * @var string
     */
    const CONTENT_VIEWS = 'Data\\Doctrine\\Main\\ContentViews';

    /**
     * Connection to `cron`-table object
     * @see \Data\Doctrine\Main\Cron
     *
     * @var string
     */
    const CRON = 'Data\\Doctrine\\Main\\Cron';

    /**
     * Connection to `cron_history`-table object
     * @see \Data\Doctrine\Main\CronHistory
     *
     * @var string
     */
    const CRON_HISTORY = 'Data\\Doctrine\\Main\\CronHistory';

    /**
     * Connection to `cron_task_mail`-table object
     * @see \Data\Doctrine\Main\CronTaskMail
     *
     * @var string
     */
    const CRON_TASK_MAIL = 'Data\\Doctrine\\Main\\CronTaskMail';

    /**
     * Connection to `released`-table object
     * @see \Data\Doctrine\Main\Released
     *
     * @var string
     */
    const RELEASED = 'Data\\Doctrine\\Main\\Released';

    /**
     * Connection to `released_live`-table object
     * @see \Data\Doctrine\Main\ReleasedLive
     *
     * @var string
     */
    const RELEASED_LIVE = 'Data\\Doctrine\\Main\\ReleasedLive';

    /**
     * Connection to `user`-table object
     * @see \Data\Doctrine\Main\User
     *
     * @var string
     */
    const USER = 'Data\\Doctrine\\Main\\User';

    /**
     * Connection to `user_access`-table object
     * @see \Data\Doctrine\Main\UserAccess
     *
     * @var string
     */
    const USER_ACCESS = 'Data\\Doctrine\\Main\\UserAccess';

    /**
     * Connection to `user_account`-table object
     * @see \Data\Doctrine\Main\UserAccount
     *
     * @var string
     */
    const USER_ACCOUNT = 'Data\\Doctrine\\Main\\UserAccount';

    /**
     * Connection to `user_protocol`-table object
     * @see \Data\Doctrine\Main\UserProtocol
     *
     * @var string
     */
    const USER_PROTOCOL = 'Data\\Doctrine\\Main\\UserProtocol';

    /**
     * Connection to `workflow`-table object
     * @see \Data\Doctrine\Main\Workflow
     *
     * @var string
     */
    const WORKFLOW = 'Data\\Doctrine\\Main\\Workflow';

}
