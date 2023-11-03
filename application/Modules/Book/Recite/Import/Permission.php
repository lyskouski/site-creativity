<?php namespace Modules\Book\Recite\Import;

/**
 * Description of Permission
 *
 * @since 2016-12-19
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
    }

    public function manualAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('quote', array('min_length' => 10))
                    ->bindKey('page', array('ctype' => 'integer', 'min' => 1))
                    ->bindKey('isbn');
    }

    public function changeAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('quote', array('min_length' => 10))
                    ->bindKey('forward', array('sanitize' => FILTER_VALIDATE_URL))
                    ->bindKey('id', array('ctype' => 'integer'));
    }

    public function pocketbookAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('type', array('list'=>['all','content','quote']))
                    ->bindKey('isbn')
                    ->bindKey('content');
    }
}
