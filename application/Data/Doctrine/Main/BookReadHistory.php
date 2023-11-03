<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookReadHistory
 *
 * @ORM\Table(name="book_read_history", indexes={@ORM\Index(name="brh_updated_at", columns={"updated_at"}), @ORM\Index(name="brh_book_read_id", columns={"book_read_id"}), @ORM\Index(name="brh_content_id", columns={"content_id"})})
 * @ORM\Entity
 */
class BookReadHistory
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
     * @var integer
     *
     * @ORM\Column(name="page", type="integer", nullable=true)
     */
    private $page;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \Data\Doctrine\Main\BookRead
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\BookRead")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_read_id", referencedColumnName="id")
     * })
     */
    private $bookRead;

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
     * Set page
     *
     * @param integer $page
     *
     * @return BookReadHistory
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return BookReadHistory
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
     * @return BookReadHistory
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
     * Set bookRead
     *
     * @param BookRead $bookRead
     *
     * @return BookReadHistory
     */
    public function setBookRead(BookRead $bookRead = null)
    {
        $this->bookRead = $bookRead;

        return $this;
    }

    /**
     * Get bookRead
     *
     * @return BookRead
     */
    public function getBookRead()
    {
        return $this->bookRead;
    }
}
