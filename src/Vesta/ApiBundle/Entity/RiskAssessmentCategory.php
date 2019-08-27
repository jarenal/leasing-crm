<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RiskAssessmentCategory
 *
 * @ORM\Table(name="risk_assessment_category")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class RiskAssessmentCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The name is required.")
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive=false;

    /**
    * @ORM\ManyToOne(targetEntity="RiskAssessmentType", inversedBy="categories")
    * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
    * @JMS\MaxDepth(1)
    * @JMS\Expose
    *
    */
    private $type;

    /**
    * @ORM\OneToMany(targetEntity="RiskAssessmentQuestion", mappedBy="category")
    * @JMS\MaxDepth(1)
    */
    private $questions;

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("active_questions")
     * @return string
     */
    public function getActiveQuestions()
    {
        $questions = array();

        foreach ($this->questions as $question)
        {
            if($question->getIsActive())
            {
                $questions[] = $question;
            }
        }

        return $questions;
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
     * Set name
     *
     * @param string $name
     *
     * @return RiskAssessmentCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return RiskAssessmentCategory
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set type
     *
     * @param \App\ApiBundle\Entity\RiskAssessmentType $type
     *
     * @return RiskAssessmentCategory
     */
    public function setType(\App\ApiBundle\Entity\RiskAssessmentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \App\ApiBundle\Entity\RiskAssessmentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add question
     *
     * @param \App\ApiBundle\Entity\RiskAssessmentQuestion $question
     *
     * @return RiskAssessmentCategory
     */
    public function addQuestion(\App\ApiBundle\Entity\RiskAssessmentQuestion $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \App\ApiBundle\Entity\RiskAssessmentQuestion $question
     */
    public function removeQuestion(\App\ApiBundle\Entity\RiskAssessmentQuestion $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
