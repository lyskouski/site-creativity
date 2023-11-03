<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentViews
 *
 * @ORM\Table(name="content_views", indexes={@ORM\Index(name="cv_visitors", columns={"visitors"}), @ORM\Index(name="cv_votes", columns={"votes_up", "votes_down"})})
 * @ORM\Entity
 */
class ContentViews
{
    /**
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(fetch="EAGER", targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="content_count", type="bigint", nullable=true)
     */
    private $contentCount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="visitors", type="bigint", nullable=true)
     */
    private $visitors = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votes_up", type="integer", nullable=true)
     */
    private $votesUp = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votes_down", type="integer", nullable=true)
     */
    private $votesDown = '0';

    /**
     * Set contentCount
     *
     * @param integer $contentCount
     *
     * @return ContentViews
     */
    public function setContentCount($contentCount)
    {
        $this->contentCount = $contentCount;

        return $this;
    }

    /**
     * Get contentCount
     *
     * @return integer
     */
    public function getContentCount()
    {
        return $this->contentCount;
    }

    /**
     * Set visitors
     *
     * @param integer $visitors
     *
     * @return ContentViews
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;

        return $this;
    }

    /**
     * Get visitors
     *
     * @return integer
     */
    public function getVisitors()
    {
        return $this->visitors;
    }

    /**
     * Set votesUp
     *
     * @param integer $votesUp
     *
     * @return ContentViews
     */
    public function setVotesUp($votesUp)
    {
        $this->votesUp = $votesUp;

        return $this;
    }

    /**
     * Get votesUp
     *
     * @return integer
     */
    public function getVotesUp()
    {
        return $this->votesUp;
    }

    /**
     * Set votesDown
     *
     * @param integer $votesDown
     *
     * @return ContentViews
     */
    public function setVotesDown($votesDown)
    {
        $this->votesDown = $votesDown;

        return $this;
    }

    /**
     * Get votesDown
     *
     * @return integer
     */
    public function getVotesDown()
    {
        return $this->votesDown;
    }

    /**
     * Set content
     *
     * @param Content $content
     *
     * @return ContentViews
     */
    public function setContent(Content $content)
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
}
