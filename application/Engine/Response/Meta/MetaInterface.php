<?php namespace Engine\Response\Meta;

/**
 * Interface for meta data
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Response/Meta
 */
interface MetaInterface
{

    public function getRepresentationType ();

    public function isEqual ( MetaInterface $oMeta );

}
