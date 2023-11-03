<?php namespace Engine\Response\Meta;

/**
 * Meta
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
class Meta extends MetaAbstract
{

    const NAME = 'meta';
    const VAR_NAME = 'name';
    const VAR_CONTENT = 'content';
    // basic
    const TYPE_AUTHOR = 'author';
    const TYPE_DESCRIPTION = 'description';
    const TYPE_KEYWORDS = 'keywords';
    // robots
    const TYPE_ROBOTS = 'robots';
    const TYPE_DOCUMENT_STATE = 'document-state';
    const TYPE_REVISIT_AFTER = 'revisit-after';

    public function __construct($sName, $sContent)
    {
        parent::__construct(
            array(
                self::NAME => new CustomArray(array(
                    self::VAR_NAME => $sName,
                    self::VAR_CONTENT => str_replace('"', '', $sContent)
                ))
            )
        );
    }
}
