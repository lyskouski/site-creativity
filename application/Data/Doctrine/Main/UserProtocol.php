<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProtocol
 *
 * @ORM\Table(name="user_protocol", uniqueConstraints={@ORM\UniqueConstraint(name="up_address", columns={"address", "user_id"})}, indexes={@ORM\Index(name="up_attemps", columns={"attemps"}), @ORM\Index(name="up_udated_at", columns={"updated_at"}), @ORM\Index(name="up_user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class UserProtocol
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
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=15, nullable=true)
     */
    private $address = '000.000.000.000';

    /**
     * @var string
     *
     * @ORM\Column(name="attemps", type="string", nullable=true)
     */
    private $attemps = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

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
     * Set address
     *
     * @param string $address
     *
     * @return UserProtocol
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set attemps
     *
     * @param string $attemps
     *
     * @return UserProtocol
     */
    public function setAttemps($attemps)
    {
        $this->attemps = $attemps;

        return $this;
    }

    /**
     * Get attemps
     *
     * @return string
     */
    public function getAttemps()
    {
        return $this->attemps;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return UserProtocol
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
     * Set user
     *
     * @param User $user
     *
     * @return UserProtocol
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
}
