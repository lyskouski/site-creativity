<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Released
 *
 * @ORM\Table(name="released", indexes={@ORM\Index(name="r_updated_at", columns={"updated_at"}), @ORM\Index(name="r_content_id", columns={"content_id"})})
 * @ORM\Entity(repositoryClass="Data\Model\Released")
 */
class Released
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
     * @var integer
     *
     * @ORM\Column(name="branch", type="integer", nullable=true)
     */
    private $branch;

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
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tested", type="boolean", nullable=true)
     */
    private $tested = '0';

    /**
     * Set branch
     *
     * @param integer $branch
     *
     * @return Released
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return integer
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Released
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
     * @return Released
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
     * @return Released
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
     * Set content
     *
     * @param Content $content
     *
     * @return Released
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set tested
     *
     * @param boolean $tested
     *
     * @return Released
     */
    public function setTested($tested)
    {
        $this->tested = $tested;

        return $this;
    }

    /**
     * Get tested
     *
     * @return boolean
     */
    public function getTested()
    {
        return $this->tested;
    }
}
