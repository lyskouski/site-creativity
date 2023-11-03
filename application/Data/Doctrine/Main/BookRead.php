<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookRead
 *
 * @ORM\Table(name="book_read", uniqueConstraints={@ORM\UniqueConstraint(name="br_uniq_book", columns={"content_id", "user_id", "book_id"})}, indexes={@ORM\Index(name="br_user_id", columns={"user_id"}), @ORM\Index(name="br_book_id", columns={"book_id"}), @ORM\Index(name="br_content_id", columns={"content_id"}), @ORM\Index(name="br_status", columns={"status"}), @ORM\Index(name="br_queue", columns={"queue"})})
 * @ORM\Entity(repositoryClass="Data\Model\BookRead")
 */
class BookRead
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
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="integer", nullable=true)
     */
    private $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="queue", type="integer", nullable=true)
     */
    private $queue = '0';

    /**
     * @var \Data\Doctrine\Main\Book
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Data\Doctrine\Main\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    /**
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return BookRead
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set page
     *
     * @param integer $page
     *
     * @return BookRead
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
     * Set queue
     *
     * @param integer $queue
     *
     * @return BookRead
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get queue
     *
     * @return integer
     */
    public function getQueue()
    {
        return $this->queue;
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
     * @return BookRead
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
     * Set content
     *
     * @param Content $content
     *
     * @return BookRead
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
     * Set book
     *
     * @param Book $book
     *
     * @return BookRead
     */
    public function setBook(Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }



    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Content
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
        if (is_string($this->updatedAt)) {
            $this->updatedAt = new \DateTime;
        }
        return $this->updatedAt;
    }
}
