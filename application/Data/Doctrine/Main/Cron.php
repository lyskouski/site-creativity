<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cron
 *
 * @ORM\Table(name="cron", indexes={@ORM\Index(name="cr_status", columns={"status"})})
 * @ORM\Entity
 */
class Cron
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
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="ev_minute", type="string", length=32, nullable=true)
     */
    private $evMinute;

    /**
     * @var string
     *
     * @ORM\Column(name="ev_hour", type="string", length=32, nullable=true)
     */
    private $evHour;

    /**
     * @var string
     *
     * @ORM\Column(name="ev_day", type="string", length=32, nullable=true)
     */
    private $evDay;

    /**
     * @var string
     *
     * @ORM\Column(name="ev_month", type="string", length=32, nullable=true)
     */
    private $evMonth;

    /**
     * @var string
     *
     * @ORM\Column(name="ev_week_day", type="string", length=32, nullable=true)
     */
    private $evWeekDay;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=255, nullable=true)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Cron
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Cron
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
     * Set evMinute
     *
     * @param string $evMinute
     *
     * @return Cron
     */
    public function setEvMinute($evMinute)
    {
        $this->evMinute = $evMinute;

        return $this;
    }

    /**
     * Get evMinute
     *
     * @return string
     */
    public function getEvMinute()
    {
        return $this->evMinute;
    }

    /**
     * Set evHour
     *
     * @param string $evHour
     *
     * @return Cron
     */
    public function setEvHour($evHour)
    {
        $this->evHour = $evHour;

        return $this;
    }

    /**
     * Get evHour
     *
     * @return string
     */
    public function getEvHour()
    {
        return $this->evHour;
    }

    /**
     * Set evDay
     *
     * @param string $evDay
     *
     * @return Cron
     */
    public function setEvDay($evDay)
    {
        $this->evDay = $evDay;

        return $this;
    }

    /**
     * Get evDay
     *
     * @return string
     */
    public function getEvDay()
    {
        return $this->evDay;
    }

    /**
     * Set evMonth
     *
     * @param string $evMonth
     *
     * @return Cron
     */
    public function setEvMonth($evMonth)
    {
        $this->evMonth = $evMonth;

        return $this;
    }

    /**
     * Get evMonth
     *
     * @return string
     */
    public function getEvMonth()
    {
        return $this->evMonth;
    }

    /**
     * Set evWeekDay
     *
     * @param string $evWeekDay
     *
     * @return Cron
     */
    public function setEvWeekDay($evWeekDay)
    {
        $this->evWeekDay = $evWeekDay;

        return $this;
    }

    /**
     * Get evWeekDay
     *
     * @return string
     */
    public function getEvWeekDay()
    {
        return $this->evWeekDay;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return Cron
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Cron
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
}
