<?php namespace Access\Request;

/**
 * Validate topic parameters
 *
 * @since 2016-02-02
 * @author Viachaslau Lyskouski
 */
class Topic
{

    /**
     * Update acces for topic parameters validation
     *
     * @param \Access\Allowed $oAccess
     * @return \Access\Allowed
     */
    public function updateAccess(\Access\Allowed $oAccess, $title = 'title')
    {
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('action', array('list' => ['create', 'comment']))
                ->bindKey($title, array('min_length' => 3, 'max_length' => 32), true)
                ->bindKey('description', array('min_length' => 3, 'max_length' => 120), true)
                ->bindKey('content', array('min_length' => 3));
    }

}
