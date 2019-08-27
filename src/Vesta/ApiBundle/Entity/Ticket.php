<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class Ticket
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The title is required.")
     * @JMS\Groups({"onlyid"})
     */
    private $title;

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
     * @ORM\Column(name="action_needed", type="text", nullable=true)
     * @JMS\Expose
     */
    private $action_needed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reported", type="datetime")
     * @JMS\Expose
     * @Assert\NotBlank(message="The date reported is required.")
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $dateReported;

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
     */
    private $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted=false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duedate_at", type="datetime", nullable=true)
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $duedateAt;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $created_by;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $updated_by;

    /**
    * @ORM\ManyToOne(targetEntity="TicketStatus")
    * @ORM\JoinColumn(name="ticket_status_id", referencedColumnName="id")
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The status is required.")
    */
    private $status;

    /**
    * @ORM\ManyToOne(targetEntity="Contact")
    * @ORM\JoinColumn(name="reported_by", referencedColumnName="id", nullable=true)
    *
    * @JMS\Expose
    */
    private $reported_by;

    /**
    * @ORM\ManyToOne(targetEntity="Other")
    * @ORM\JoinColumn(name="assign_to", referencedColumnName="id", nullable=true)
    *
    * @JMS\Expose
    */
    private $assign_to;

    /**
     * @ORM\ManyToOne(targetEntity="TicketType")
     * @ORM\JoinColumn(name="ticket_type_id", referencedColumnName="id")
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The task type is required.")
     */
    private $ticket_type;

    /**
    * @ORM\ManyToOne(targetEntity="Ticket")
    * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $parent;

    /**
     * @ORM\Column(name="time_spent", type="decimal", precision=5, scale=2)
     * @JMS\Expose
     */
    private $time_spent=0;

    /**
    * @ORM\Column(type="time_units")
    * @JMS\Expose
    */
    private $time_spent_unit="Minutes";

    /**
    * @ORM\ManyToMany(targetEntity="User", inversedBy="read_tickets", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="users_read_tickets",
    *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
    *      )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $read_by;

    /**
    * @ORM\ManyToMany(targetEntity="Contact", inversedBy="related_tickets", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tickets_related_contacts",
    *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")}
    *      )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(3)
    */
    private $related_contacts;

    /**
    * @ORM\ManyToMany(targetEntity="Property", inversedBy="related_tickets", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tickets_related_properties",
    *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="property_id", referencedColumnName="id")}
    *      )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(3)
    */
    private $related_properties;

    /**
     * @ORM\OneToMany(targetEntity="PropertyRiskAssessment", mappedBy="ticket")
     */
    private $risk_assessments;

    /**
     * @ORM\OneToMany(targetEntity="TicketComment", mappedBy="ticket")
    * @JMS\Expose
    * @JMS\MaxDepth(4)
     */
    private $comments;

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
     * Set title
     *
     * @param string $title
     *
     * @return Ticket
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
     * Set description
     *
     * @param string $description
     *
     * @return Ticket
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
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
     * @return Ticket
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Ticket
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
     * Set duedateAt
     *
     * @param \DateTime $duedateAt
     *
     * @return Ticket
     */
    public function setDuedateAt($duedateAt)
    {
        if($duedateAt)
            $this->duedateAt =  new \DateTime(str_replace("/", ".", $duedateAt));
        else
            $this->duedateAt = null;

        return $this;
    }

    /**
     * Get duedateAt
     *
     * @return \DateTime
     */
    public function getDuedateAt()
    {
        return $this->duedateAt;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return Ticket
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
     * @return Ticket
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
     * Set status
     *
     * @param \App\ApiBundle\Entity\TicketStatus $status
     *
     * @return Ticket
     */
    public function setStatus(\App\ApiBundle\Entity\TicketStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \App\ApiBundle\Entity\TicketStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reportedBy
     *
     * @param \App\ApiBundle\Entity\Contact $reportedBy
     *
     * @return Ticket
     */
    public function setReportedBy(\App\ApiBundle\Entity\Contact $reportedBy = null)
    {
        $this->reported_by = $reportedBy;

        return $this;
    }

    /**
     * Get reportedBy
     *
     * @return \App\ApiBundle\Entity\Contact
     */
    public function getReportedBy()
    {
        return $this->reported_by;
    }

    /**
     * Set assignTo
     *
     * @param \App\ApiBundle\Entity\Other $assignTo
     *
     * @return Ticket
     */
    public function setAssignTo(\App\ApiBundle\Entity\Other $assignTo = null)
    {
        $this->assign_to = $assignTo;

        return $this;
    }

    /**
     * Get assignTo
     *
     * @return \App\ApiBundle\Entity\Other
     */
    public function getAssignTo()
    {
        return $this->assign_to;
    }

    /**
     * Set ticketType
     *
     * @param \App\ApiBundle\Entity\TicketType $ticketType
     *
     * @return Ticket
     */
    public function setTicketType(\App\ApiBundle\Entity\TicketType $ticketType = null)
    {
        $this->ticket_type = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return \App\ApiBundle\Entity\TicketType
     */
    public function getTicketType()
    {
        return $this->ticket_type;
    }

    /**
     * Set parent
     *
     * @param \App\ApiBundle\Entity\Ticket $parent
     *
     * @return Ticket
     */
    public function setParent(\App\ApiBundle\Entity\Ticket $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \App\ApiBundle\Entity\Ticket
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get FullTitle
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("fulltitle")
     * @return string
     */
    public function getFulltitle()
    {
        if($this->getTicketType())
            return "#{$this->getId()} - {$this->getTitle()} ({$this->getTicketType()->getName()})";
        else
            return "#{$this->getId()} - {$this->getTitle()} (N/A)";
    }

    /**
     * Set timeSpent
     *
     * @param string $timeSpent
     *
     * @return Ticket
     */
    public function setTimeSpent($timeSpent)
    {
        if($timeSpent)
            $this->time_spent = $timeSpent;
        else
            $this->time_spent = 0;

        return $this;
    }

    /**
     * Get timeSpent
     *
     * @return string
     */
    public function getTimeSpent()
    {
        return $this->time_spent;
    }

    /**
     * Set timeSpentUnit
     *
     * @param \time_units $timeSpentUnit
     *
     * @return Ticket
     */
    public function setTimeSpentUnit($timeSpentUnit)
    {
        if($timeSpentUnit)
            $this->time_spent_unit = $timeSpentUnit;
        else
            $this->time_spent_unit = "Minutes";

        return $this;
    }

    /**
     * Get timeSpentUnit
     *
     * @return \time_units
     */
    public function getTimeSpentUnit()
    {
        return $this->time_spent_unit;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->read_by = new \Doctrine\Common\Collections\ArrayCollection();
        $this->related_contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->risk_assessments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add readBy
     *
     * @param \App\ApiBundle\Entity\User $readBy
     *
     * @return Ticket
     */
    public function addReadBy(\App\ApiBundle\Entity\User $readBy)
    {
        $readBy->addReadTicket($this);
        $this->read_by[] = $readBy;

        return $this;
    }

    /**
     * Remove readBy
     *
     * @param \App\ApiBundle\Entity\User $readBy
     */
    public function removeReadBy(\App\ApiBundle\Entity\User $readBy)
    {
        $this->read_by->removeElement($readBy);
    }

    /**
     * Get readBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReadBy()
    {
        return $this->read_by;
    }

    /**
     * Add relatedContact
     *
     * @param \App\ApiBundle\Entity\Contact $relatedContact
     *
     * @return Ticket
     */
    public function addRelatedContact(\App\ApiBundle\Entity\Contact $relatedContact)
    {
        $relatedContact->addRelatedTicket($this);
        $this->related_contacts[] = $relatedContact;

        return $this;
    }

    /**
     * Remove relatedContact
     *
     * @param \App\ApiBundle\Entity\Contact $relatedContact
     */
    public function removeRelatedContact(\App\ApiBundle\Entity\Contact $relatedContact)
    {
        $this->related_contacts->removeElement($relatedContact);
    }

    /**
     * Get relatedContacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedContacts()
    {
        return $this->related_contacts;
    }

    public function getRelatedContactsIds()
    {
        $tmp = array();
        foreach ($this->getRelatedContacts() as $contact)
        {
            $tmp[] = $contact->getId();
        }

        return $tmp;
    }

    /**
     * Add relatedProperty
     *
     * @param \App\ApiBundle\Entity\Property $relatedProperty
     *
     * @return Ticket
     */
    public function addRelatedProperty(\App\ApiBundle\Entity\Property $relatedProperty)
    {
        $relatedProperty->addRelatedTicket($this);
        $this->related_properties[] = $relatedProperty;

        return $this;
    }

    /**
     * Remove relatedProperty
     *
     * @param \App\ApiBundle\Entity\Property $relatedProperty
     */
    public function removeRelatedProperty(\App\ApiBundle\Entity\Property $relatedProperty)
    {
        $this->related_properties->removeElement($relatedProperty);
    }

    /**
     * Get relatedProperties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedProperties()
    {
        return $this->related_properties;
    }

    public function getRelatedPropertiesIds()
    {
        $tmp = array();
        foreach ($this->getRelatedProperties() as $property)
        {
            $tmp[] = $property->getId();
        }

        return $tmp;
    }

    /**
     * Set riskAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment
     *
     * @return Ticket
     */
    public function setPropertyRiskAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment = null)
    {
        $this->risk_assessment = $riskAssessment;

        return $this;
    }

    /**
     * Get riskAssessment
     *
     * @return \App\ApiBundle\Entity\PropertyRiskAssessment
     */
    public function getPropertyRiskAssessment()
    {
        return $this->risk_assessment;
    }

    /**
     * Add riskAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment
     *
     * @return Ticket
     */
    public function addPropertyRiskAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment)
    {
        $this->risk_assessments[] = $riskAssessment;

        return $this;
    }

    /**
     * Remove riskAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment
     */
    public function removePropertyRiskAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment)
    {
        $this->risk_assessments->removeElement($riskAssessment);
    }

    /**
     * Get riskAssessments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyRiskAssessments()
    {
        return $this->risk_assessments;
    }

    /**
     * Add riskAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment
     *
     * @return Ticket
     */
    public function addRiskAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment)
    {
        $this->risk_assessments[] = $riskAssessment;

        return $this;
    }

    /**
     * Remove riskAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment
     */
    public function removeRiskAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $riskAssessment)
    {
        $this->risk_assessments->removeElement($riskAssessment);
    }

    /**
     * Get riskAssessments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRiskAssessments()
    {
        return $this->risk_assessments;
    }

    /**
     * Set actionNeeded
     *
     * @param string $actionNeeded
     *
     * @return Ticket
     */
    public function setActionNeeded($actionNeeded)
    {
        $this->action_needed = $actionNeeded;

        return $this;
    }

    /**
     * Get actionNeeded
     *
     * @return string
     */
    public function getActionNeeded()
    {
        return $this->action_needed;
    }

    /**
     * Set dateReported
     *
     * @param \DateTime $dateReported
     *
     * @return Ticket
     */
    public function setDateReported($dateReported)
    {
        if($dateReported)
            $this->dateReported =  new \DateTime(str_replace("/", ".", $dateReported));
        else
            $this->dateReported = null;

        return $this;
    }

    /**
     * Get dateReported
     *
     * @return \DateTime
     */
    public function getDateReported()
    {
        return $this->dateReported;
    }

    /**
     * Add comment
     *
     * @param \App\ApiBundle\Entity\TicketComment $comment
     *
     * @return Ticket
     */
    public function addComment(\App\ApiBundle\Entity\TicketComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \App\ApiBundle\Entity\TicketComment $comment
     */
    public function removeComment(\App\ApiBundle\Entity\TicketComment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
