<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * TicketComment
 *
 * @ORM\Table(name="ticket_comment")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class TicketComment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"onlyid"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime")
     * @JMS\Expose
     * @Assert\NotBlank(message="The update date is required.")
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $updateDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @JMS\Expose
     * @Assert\NotBlank(message="The description is required.")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="action_needed", type="text")
     * @JMS\Expose
     */
    private $actionNeeded;

    /**
     * @var string
     *
     * @ORM\Column(name="time_spent", type="decimal")
     * @JMS\Expose
     */
    private $timeSpent=0;

    /**
     * @var time_units
     *
     * @ORM\Column(name="time_spent_unit", type="time_units")
     * @JMS\Expose
     */
    private $timeSpentUnit="Minutes";

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted=false;

    /**
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="comments")
    * @ORM\JoinColumn(name="ticket", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    * @Assert\NotBlank(message="The ticket is required.")
    */
    private $ticket;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $updatedAt;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    * @JMS\MaxDepth(3)
    */
    private $created_by;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $updated_by;


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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return TicketComment
     */
    public function setUpdateDate($updateDate)
    {
        if($updateDate)
            $this->updateDate =  new \DateTime(str_replace("/", ".", $updateDate));
        else
            $this->updateDate = null;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TicketComment
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set actionNeeded
     *
     * @param string $actionNeeded
     *
     * @return TicketComment
     */
    public function setActionNeeded($actionNeeded)
    {
        $this->actionNeeded = $actionNeeded;

        return $this;
    }

    /**
     * Get actionNeeded
     *
     * @return string
     */
    public function getActionNeeded()
    {
        return $this->actionNeeded;
    }

    /**
     * Set timeSpent
     *
     * @param string $timeSpent
     *
     * @return TicketComment
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    /**
     * Get timeSpent
     *
     * @return string
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return TicketComment
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TicketComment
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return TicketComment
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
     * Set ticket
     *
     * @param \App\ApiBundle\Entity\Ticket $ticket
     *
     * @return TicketComment
     */
    public function setTicket(\App\ApiBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \App\ApiBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return TicketComment
     */
    public function setCreatedBy(\App\ApiBundle\Entity\User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set updatedBy
     *
     * @param \App\ApiBundle\Entity\User $updatedBy
     *
     * @return TicketComment
     */
    public function setUpdatedBy(\App\ApiBundle\Entity\User $updatedBy = null)
    {
        $this->updated_by = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * Set timeSpentUnit
     *
     * @param time_units $timeSpentUnit
     *
     * @return TicketComment
     */
    public function setTimeSpentUnit($timeSpentUnit)
    {
        $this->timeSpentUnit = $timeSpentUnit;

        return $this;
    }

    /**
     * Get timeSpentUnit
     *
     * @return time_units
     */
    public function getTimeSpentUnit()
    {
        return $this->timeSpentUnit;
    }
}
