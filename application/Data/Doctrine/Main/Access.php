<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Access
 *
 * @ORM\Table(name="access", uniqueConstraints={@ORM\UniqueConstraint(name="a_title", columns={"title"})}, indexes={@ORM\Index(name="a_id", columns={"access_id"})})
 * @ORM\Entity
 */
class Access
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
     * @ORM\Column(name="title", type="string", length=32, nullable=true)
     */
    private $title;

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
     * Set title
     *
     * @param string $title
     *
     * @return Access
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set access
     *
     * @param Access $access
     *
     * @return Access
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
