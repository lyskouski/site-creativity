<?php

namespace Data\Doctrine\Main;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table(name="action", uniqueConstraints={@ORM\UniqueConstraint(name="action_un", columns={"url", "action"})}, indexes={@ORM\Index(name="action_url", columns={"url"}), @ORM\Index(name="action_usages", columns={"usages"})})
 * @ORM\Entity
 */
class Action
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=64, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=16, nullable=true)
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(name="usages", type="integer", nullable=true)
     */
    private $usages = '0';

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Action
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Action
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set usages
     *
     * @param integer $usages
     *
     * @return Action
     */
    public function setUsages($usages)
    {
        $this->usages = $usages;

        return $this;
    }

    /**
     * Get usages
     *
     * @return integer
     */
    public function getUsages()
    {
        return $this->usages;
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
