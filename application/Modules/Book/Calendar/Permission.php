<?php namespace Modules\Book\Calendar;

use Access\Filter;

/**
 * Description of Permission
 *
 * @since 2016-09-23
 * @author Viachaslau Lyskouski
 */
class Permission extends \Modules\AbstractPermission
{
    public function defaultAction()
    {
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', (new Filter\Pattern)->publication()->get())
                ->copyToExtension(\Defines\Extension::JSON)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action')
                    ->bindKey('title', (new Filter\TypeString(3, 64))->get());
    }

    public function searchAction($isbn)
    {
        $this->defaultAction();
        $this->access->addRequestMethod(\Defines\RequestMethod::GET)
                ->unbindExtension(\Defines\Extension::HTML)
            ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON);
        $this->access->bindKey('type', (new Filter\TypeString(0, 64))->get());
        if ($isbn) {
            $this->access->bindKey('isbn')
                ->bindKey('title')
                ->bindKey('author');
        } else {
            $this->access->bindKey('title', (new Filter\TypeString(0, 64))->get())
                ->bindKey('author', (new Filter\TypeString(0, 24))->get())
                ->bindKey('isbn');
        }
        $this->access->bindKey('language', array('list' => \Defines\Language::getList()));
    }

    public function pageAction()
    {
        $this->defaultAction();
        $this->access->bindKey('isbn', null, true)
            ->bindKey('page', array('ctype' => 'integer'), true);
    }

    public function removeAction()
    {
        $this->defaultAction();
        $this->access->bindKey('id', array('ctype' => 'integer'), true);
    }

    public function changeAction()
    {
        $this->defaultAction();
        $this->access->bindKey('id', array('ctype' => 'integer'), true)
            ->bindKey('list', array('ctype' => 'integer'), false);
    }

    public function restoreAction()
    {
        $this->removeAction();
    }

    public function moveAction()
    {
        $intFilter = array('ctype' => 'integer');
        $this->defaultAction();
        $this->access->bindKey('isbn', null, true)
            ->bindKey('pos', $intFilter, true)
            ->bindKey('type', $intFilter, true)
            ->bindKey('language', array('list' => \Defines\Language::getList()));
    }

    public function updateAction()
    {
        $this->defaultAction();
        $this->access->bindKey('content')
            ->bindKey('height')
            ->bindKey('width');
    }
}
