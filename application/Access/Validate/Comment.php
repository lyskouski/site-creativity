<?php namespace Access\Validate;

/**
 * Validate access to comment, reply and vote for comments
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class Comment extends Check
{
    public function getType() {
        return \Defines\User\Access::COMMENT;
    }
}
