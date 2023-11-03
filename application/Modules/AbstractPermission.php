<?php namespace Modules;

/**
 * Description of AbstractPermission
 *
 * @since 2016-09-23
 * @author Viachaslau Lyskouski
 */
abstract class AbstractPermission
{

    /**
     * @var \Access\Allowed
     */
    protected $access;

    public function __construct($action, $special = array())
    {
        $this->access = new \Access\Allowed();
        $this->$action($special);
    }

    abstract public function defaultAction();

    public function commentAction()
    {
        $this->defaultAction();
        $this->access->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('action', array('list' => ['comment']))
                ->bindKey('content', array('min_length' => 3))
                ->bindKey('mark', array('list' => ['', 'votes_up', 'votes_down']));
    }

    /**
     * Validate permissions
     *
     * @param string $requestMethod
     * @param string $responseType
     * @return \Access\Allowed
     */
    public function validate($requestMethod, $responseType)
    {
        return $this->access->isReach($requestMethod, $responseType);
    }

    public function __call($name, $arguments)
    {
        $method = "{$name}Action";
        if (!method_exists($this, $method)) {
            $method = 'defaultAction';
        }
        call_user_func_array([$this, $method], $arguments);
    }
}
