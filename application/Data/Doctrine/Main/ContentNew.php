<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentNew
 *
 * @ORM\Table(name="content_new", uniqueConstraints={@ORM\UniqueConstraint(name="cn_un_search", columns={"pattern", "language", "type"})}, indexes={@ORM\Index(name="cn_pattern", columns={"pattern"}), @ORM\Index(name="cn_author_id", columns={"author_id"}), @ORM\Index(name="cn_auditor_id", columns={"auditor_id"}), @ORM\Index(name="cn_updated_at", columns={"updated_at"}), @ORM\Index(name="cn_content_id", columns={"content_id"})})
 * @ORM\Entity
 */
class ContentNew
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pattern", type="string", length=255, nullable=true)
     */
    private $pattern;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=32, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="access", type="string", length=3, nullable=true)
     */
    private $access = '444';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var \Data\Doctrine\Main\User
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auditor_id", referencedColumnName="id")
     * })
     */
    private $auditor;

    /**
     * @var \Data\Doctrine\Main\User
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;

    /**
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content2;

    /**
     * Set pattern
     *
     * @param string $pattern
     *
     * @return ContentNew
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return ContentNew
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ContentNew
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set access
     *
     * @param string $access
     *
     * @return ContentNew
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Get access
     *
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ContentNew
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
     * Set content
     *
     * @param string $content
     *
     * @return ContentNew
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * Set content2
     *
     * @param Content $content2
     *
     * @return ContentNew
     */
    public function setContent2(Content $content2 = null)
    {
        $this->content2 = $content2;

        return $this;
    }

    /**
     * Get content2
     *
     * @return Content
     */
    public function getContent2()
    {
        return $this->content2;
    }

    /**
     * Set author
     *
     * @param User $author
     *
     * @return ContentNew
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set auditor
     *
     * @param User $auditor
     *
     * @return ContentNew
     */
    public function setAuditor(User $auditor = null)
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get auditor
     *
     * @return User
     */
    public function getAuditor()
    {
        return $this->auditor;
    }
}
