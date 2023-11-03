<?php namespace Modules\Book\Recite;

/**
 * Description of Permission
 *
 * @since 2016-11-14
 * @author Viachaslau Lyskouski
 */
class Permission extends \Modules\AbstractPermission
{
    public function defaultAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action');
                    //->bindKey('search')
                    //->bindKey('type')
                    //->bindKey('ui-sort')
                    //->bindKey('ui-sort-type');
    }
}
