<?php namespace Modules\Files;

/**
 * Validate File Permissions
 *
 * @since 2015-09-29
 * @author Viachaslau Lyskouski
 */
class Permission extends \Modules\AbstractPermission
{
    public function defaultAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindKey('/0', array('type' => 'integer'));
    }

    public function uploadAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('/0', array('sanitize' => FILTER_SANITIZE_STRING))
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('data')
                    ->bindKey('width', array('type' => 'integer'), false);
    }
}
