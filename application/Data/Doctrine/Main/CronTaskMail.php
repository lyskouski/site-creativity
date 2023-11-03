<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * CronTaskMail
 *
 * @ORM\Table(name="cron_task_mail", indexes={@ORM\Index(name="nm_status", columns={"status", "created_at"}), @ORM\Index(name="nm_mailto", columns={"mailto"})})
 * @ORM\Entity
 */
class CronTaskMail
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
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="mailto", type="string", length=64, nullable=true)
     */
    private $mailto;

    /**
     * @var string
     *
     * @ORM\Column(name="mailfrom", type="string", length=64, nullable=true)
     */
    private $mailfrom;

    /**
     * @var string
     *
     * @ORM\Column(name="topic", type="string", length=255, nullable=true)
     */
    private $topic;

    /**
     * @var string
     *
     * @ORM\Column(name="reply_topic", type="string", length=255, nullable=true)
     */
    private $replyTopic;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="errors", type="text", length=65535, nullable=true)
     */
    private $errors;

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return CronTaskMail
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CronTaskMail
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set mailto
     *
     * @param string $mailto
     *
     * @return CronTaskMail
     */
    public function setMailto($mailto)
    {
        $this->mailto = $mailto;

        return $this;
    }

    /**
     * Get mailto
     *
     * @return string
     */
    public function getMailto()
    {
        return $this->mailto;
    }

    /**
     * Set mailfrom
     *
     * @param string $mailfrom
     *
     * @return CronTaskMail
     */
    public function setMailfrom($mailfrom)
    {
        $this->mailfrom = $mailfrom;

        return $this;
    }

    /**
     * Get mailfrom
     *
     * @return string
     */
    public function getMailfrom()
    {
        return $this->mailfrom;
    }

    /**
     * Set topic
     *
     * @param string $topic
     *
     * @return CronTaskMail
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set replyTopic
     *
     * @param string $replyTopic
     *
     * @return CronTaskMail
     */
    public function setReplyTopic($replyTopic)
    {
        $this->replyTopic = $replyTopic;

        return $this;
    }

    /**
     * Get replyTopic
     *
     * @return string
     */
    public function getReplyTopic()
    {
        return $this->replyTopic;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return CronTaskMail
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
     * Set errors
     *
     * @param string $errors
     *
     * @return CronTaskMail
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
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
