<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentSeries
 *
 * @ORM\Table(name="content_series", indexes={@ORM\Index(name="cs_content_id", columns={"content_id"}), @ORM\Index(name="cs_series_id", columns={"series_id"})})
 * @ORM\Entity
 */
class ContentSeries
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
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var \Data\Doctrine\Main\Content
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="series_id", referencedColumnName="id")
     * })
     */
    private $series;

    /**
     * Set content
     *
     * @param Content $content
     *
     * @return ContentSeries
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
     * Set series
     *
     * @param Content $series
     *
     * @return ContentSeries
     */
    public function setSeries(Content $series = null)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series
     *
     * @return Content
     */
    public function getSeries()
    {
        return $this->series;
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

