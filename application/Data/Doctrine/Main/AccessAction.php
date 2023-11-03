<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessAction
 *
 * @ORM\Table(name="access_action", indexes={@ORM\Index(name="aa_access_id", columns={"access_id"}), @ORM\Index(name="aa_action_id", columns={"action_id"}), @ORM\Index(name="aa_access_action", columns={"action_id", "access_id"})})
 * @ORM\Entity
 */
class AccessAction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permission", type="boolean", nullable=true)
     */
    private $permission;

    /**
     * @var \Data\Doctrine\Main\Access
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Data\Doctrine\Main\Access")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="access_id", referencedColumnName="id")
     * })
     */
    private $access;

    /**
     * @var \Data\Doctrine\Main\Action
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Action")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * })
     */
    private $action;

    /**
     * Set permission
     *
     * @param boolean $permission
     *
     * @return AccessAction
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return boolean
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     *
     * @param Action $action
     *
     * @return AccessAction
     */
    public function setAction(Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set access
     *
     * @param Access $access
     *
     * @return AccessAction
     */
    public function setAccess(Access $access = null)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Get access
     *
     * @return Access
     */
    public function getAccess()
    {
        return $this->access;
    }
}
