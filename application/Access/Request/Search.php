<?php namespace Access\Request;

/**
 * Validate search parameters
 *
 * @since 2016-04-29
 * @author Viachaslau Lyskouski
 */
class Search
{
    const SORT = 'ui-sort';
    const SORT_TYPE = 'ui-sort-type';
    const SPLIT = 'ui-split';

    const SORT_NEW = 'new';
    const SORT_RATING = 'rating';
    const SORT_VIEW = 'view';

    const SORT_TYPE_DESC = '0';
    const SORT_TYPE_ASC = '1';

    /* extra sorting for books */
    const SORT_BOOK_DATE = 'book_date';
    const SORT_BOOK_TITLE = 'book_title';
    const SORT_BOOK_AUHOR = 'book_author';

    const SPLIT_TILE = 'tile';
    const SPLIT_PLAIN = 'plain';

    /**
     * Update acces for topic parameters validation
     *
     * @param \Access\Allowed $oAccess
     * @return \Access\Allowed
     */
    public function updateAccess(\Access\Allowed $oAccess)
    {
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                ->bindKey('search', array('sanitize' => FILTER_SANITIZE_STRING))
                ->bindKey(self::SORT, array('list' => [
                    self::SORT_NEW,
                    self::SORT_RATING,
                    self::SORT_VIEW,
                    self::SORT_BOOK_DATE,
                    self::SORT_BOOK_TITLE,
                    self::SORT_BOOK_AUHOR
                ]))
                ->bindKey(self::SPLIT, array('list' => [
                    self::SPLIT_TILE,
                    self::SPLIT_PLAIN
                ]))
                ->bindKey(self::SORT_TYPE, array('list' => [
                    self::SORT_TYPE_ASC,
                    self::SORT_TYPE_DESC
                ]));
    }
}
