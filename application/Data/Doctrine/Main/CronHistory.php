<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * CronHistory
 *
 * @ORM\Table(name="cron_history", indexes={@ORM\Index(name="crh_updated_at", columns={"updated_at"}), @ORM\Index(name="crh_cron_id", columns={"cron_id"})})
 * @ORM\Entity
 */
class CronHistory
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
    private $updatedAt;

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
     * @var integer
     *
     * @ORM\Column(name="peak_memory", type="bigint", nullable=true)
     */
    private $peakMemory;

    /**
     * @var float
     *
     * @ORM\Column(name="peak_cpu", type="float", precision=10, scale=0, nullable=true)
     */
    private $peakCpu;

    /**
     * @var integer
     *
     * @ORM\Column(name="peak_time", type="integer", nullable=true)
     */
    private $peakTime;

    /**
     * @var string
     *
     * @ORM\Column(name="cron_log", type="text", length=65535, nullable=true)
     */
    private $cronLog;

    /**
     * @var \Data\Doctrine\Main\Cron
     *
     * @ORM\ManyToOne(targetEntity="Data\Doctrine\Main\Cron")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cron_id", referencedColumnName="id")
     * })
     */
    private $cron;

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * @return CronHistory
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
     * Set peakMemory
     *
     * @param integer $peakMemory
     *
     * @return CronHistory
     */
    public function setPeakMemory($peakMemory)
    {
        $this->peakMemory = $peakMemory;

        return $this;
    }

    /**
     * Get peakMemory
     *
     * @return integer
     */
    public function getPeakMemory()
    {
        return $this->peakMemory;
    }

    /**
     * Set peakCpu
     *
     * @param float $peakCpu
     *
     * @return CronHistory
     */
    public function setPeakCpu($peakCpu)
    {
        $this->peakCpu = $peakCpu;

        return $this;
    }

    /**
     * Get peakCpu
     *
     * @return float
     */
    public function getPeakCpu()
    {
        return $this->peakCpu;
    }

    /**
     * Set peakTime
     *
     * @param integer $peakTime
     *
     * @return CronHistory
     */
    public function setPeakTime($peakTime)
    {
        $this->peakTime = $peakTime;

        return $this;
    }

    /**
     * Get peakTime
     *
     * @return integer
     */
    public function getPeakTime()
    {
        return $this->peakTime;
    }

    /**
     * Set cronLog
     *
     * @param string $cronLog
     *
     * @return CronHistory
     */
    public function setCronLog($cronLog)
    {
        $this->cronLog = $cronLog;

        return $this;
    }

    /**
     * Get cronLog
     *
     * @return string
     */
    public function getCronLog()
    {
        return $this->cronLog;
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
     * Set cron
     *
     * @param Cron $cron
     *
     * @return CronHistory
     */
    public function setCron(Cron $cron = null)
    {
        $this->cron = $cron;

        return $this;
    }

    /**
     * Get cron
     *
     * @return Cron
     */
    public function getCron()
    {
        return $this->cron;
    }
}
