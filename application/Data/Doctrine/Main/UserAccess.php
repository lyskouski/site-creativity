<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAccess
 *
 * @ORM\Table(name="user_access", indexes={@ORM\Index(name="ua_user_id", columns={"user_id"}), @ORM\Index(name="ua_access_id", columns={"access_id"})})
 * @ORM\Entity
 */
class UserAccess
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
     * @var \Data\Doctrine\Main\Access
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Access")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="access_id", referencedColumnName="id")
     * })
     */
    private $access;

    /**
     * @var \Data\Doctrine\Main\User
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * Set user
     *
     * @param User $user
     *
     * @return UserAccess
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set access
     *
     * @param Access $access
     *
     * @return UserAccess
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
