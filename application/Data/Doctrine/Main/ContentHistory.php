<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentHistory
 *
 * @ORM\Table(name="content_history", indexes={@ORM\Index(name="ch_pattern", columns={"pattern"}), @ORM\Index(name="ch_author_id", columns={"author_id"}), @ORM\Index(name="ch_auditor_id", columns={"auditor_id"}), @ORM\Index(name="ch_updated_at", columns={"updated_at"}), @ORM\Index(name="ch_id", columns={"id"}), @ORM\Index(name="ch_search", columns={"search"})})
 * @ORM\Entity
 */
class ContentHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="content_id", type="bigint", nullable=true)
     */
    private $contentId;

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
    private $access = '525';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    private $authorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="auditor_id", type="integer", nullable=true)
     */
    private $auditorId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="search", type="text", length=65535, nullable=true)
     */
    private $search;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return ContentHistory
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set contentId
     *
     * @param integer $contentId
     *
     * @return ContentHistory
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * Get contentId
     *
     * @return integer
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     *
     * @return ContentHistory
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
     * @return ContentHistory
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
     * @return ContentHistory
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
     * @return ContentHistory
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
     * @return ContentHistory
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
     * Set authorId
     *
     * @param integer $authorId
     *
     * @return ContentHistory
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Get authorId
     *
     * @return integer
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set auditorId
     *
     * @param integer $auditorId
     *
     * @return ContentHistory
     */
    public function setAuditorId($auditorId)
    {
        $this->auditorId = $auditorId;

        return $this;
    }

    /**
     * Get auditorId
     *
     * @return integer
     */
    public function getAuditorId()
    {
        return $this->auditorId;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return ContentHistory
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
     * Set search
     *
     * @param string $search
     *
     * @return ContentHistory
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }
}
