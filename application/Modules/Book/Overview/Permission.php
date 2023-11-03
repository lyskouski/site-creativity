<?php namespace Modules\Book\Overview;

/**
 * Permissions for Books overview page
 *
 * @since 2016-12-19
 * @author Viachaslau Lyskouski
 */
class Permission extends \Modules\AbstractPermission
{
    public function defaultAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('pattern' => '/^(i|\d)\d{0,}$/'))
                    ->bindKey('/1', array('ctype' => 'integer'))
                    ->bindKey('/2', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
    }

    public function indexAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('search', array('sanitize' => FILTER_SANITIZE_STRING))
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('/1', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey('/2', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
    }

    public function searchAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('sanitize' => FILTER_SANITIZE_STRING))//, 'min_length' => 3))
                    ->bindKey('/1', array('ctype' => 'integer'))
                ->copyToExtension(\Defines\Extension::JSON);
        // Basic for all search modules
        (new \Access\Request\Search)->updateAccess($this->access);
    }

    public function authorAction()
    {
        return $this->searchAction();
    }

    public function udcAction()
    {
        $this->defaultAction();
        $this->access->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING));
    }
}
