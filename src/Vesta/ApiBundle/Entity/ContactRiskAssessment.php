<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactRiskAssessment
 *
 * @ORM\Table(name="contact_risk_assessment")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class ContactRiskAssessment
{
    /**
    * @ORM\Id
    * @ORM\ManyToOne(targetEntity="Contact", inversedBy="risks_assessments")
    * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    * @JMS\Groups({"onlyid"})
    *
    */
    private $contact;

    /**
    * @ORM\Id
    * @ORM\ManyToOne(targetEntity="RiskAssessmentQuestion")
    * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    * @JMS\Groups({"onlyid"})
    *
    */
    private $question;

    /**
     * @var boolean
     *
     * @ORM\Column(name="answer", type="boolean")
     * @JMS\Expose
     */
    private $answer=true;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     * @JMS\Expose
     */
    private $comments="";

    /**
    * @ORM\Column(name="level_of_risk", type="level_of_risk")
    * @JMS\Expose
    */
    private $levelOfRisk="Low";

    /**
     * @var boolean
     *
     * @ORM\Column(name="action_needed", type="boolean")
     * @JMS\Expose
     */
    private $actionNeeded=false;

    /**
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="risk_assessments")
    * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", nullable=true)
    * @JMS\MaxDepth(2)
    * @JMS\Expose
     */
    private $ticket;

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
     * Set answer
     *
     * @param boolean $answer
     *
     * @return ContactRiskAssessment
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return boolean
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return ContactRiskAssessment
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set levelOfRisk
     *
     * @param string $levelOfRisk
     *
     * @return ContactRiskAssessment
     */
    public function setLevelOfRisk($levelOfRisk)
    {
        $this->levelOfRisk = $levelOfRisk;

        return $this;
    }

    /**
     * Get levelOfRisk
     *
     * @return string
     */
    public function getLevelOfRisk()
    {
        return $this->levelOfRisk;
    }

    /**
     * Set actionNeeded
     *
     * @param boolean $actionNeeded
     *
     * @return ContactRiskAssessment
     */
    public function setActionNeeded($actionNeeded)
    {
        $this->actionNeeded = $actionNeeded;

        return $this;
    }

    /**
     * Get actionNeeded
     *
     * @return boolean
     */
    public function getActionNeeded()
    {
        return $this->actionNeeded;
    }

    /**
     * Set tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     *
     * @return RiskAssessment
     */
    public function setTenant(\App\ApiBundle\Entity\Tenant $tenant)
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return \App\ApiBundle\Entity\Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * Set question
     *
     * @param \App\ApiBundle\Entity\RiskAssessmentQuestion $question
     *
     * @return RiskAssessment
     */
    public function setQuestion(\App\ApiBundle\Entity\RiskAssessmentQuestion $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \App\ApiBundle\Entity\RiskAssessmentQuestion
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set ticket
     *
     * @param \App\ApiBundle\Entity\Ticket $ticket
     *
     * @return RiskAssessment
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
     * Set contact
     *
     * @param \App\ApiBundle\Entity\Contact $contact
     *
     * @return ContactRiskAssessment
     */
    public function setContact(\App\ApiBundle\Entity\Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \App\ApiBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
