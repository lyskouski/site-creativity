<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Released
 *
 * @ORM\Table(name="released_live", indexes={@ORM\Index(name="rl_updated_at", columns={"updated_at"}), @ORM\Index(name="rl_released_id", columns={"released_id"})})
 * @ORM\Entity
 */
class ReleasedLive
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
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=128, nullable=true)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=256, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \Data\Doctrine\Main\Released
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Released")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="released_id", referencedColumnName="id")
     * })
     */
    private $released;

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ReleasedLive
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return ReleasedLive
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ReleasedLive
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ReleasedLive
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
     * Set released
     *
     * @param Released $released
     *
     * @return ReleasedLive
     */
    public function setReleased(Released $released = null)
    {
        $this->released = $released;

        return $this;
    }

    /**
     * Get released
     *
     * @return Released
     */
    public function getReleased()
    {
        return $this->released;
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
}
