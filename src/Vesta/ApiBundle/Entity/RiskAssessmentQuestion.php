<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RiskAssessmentQuestion
 *
 * @ORM\Table(name="risk_assessment_question")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class RiskAssessmentQuestion
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
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
    * @ORM\ManyToOne(targetEntity="RiskAssessmentCategory", inversedBy="questions")
    * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    */
    private $category;


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
     * @return RiskAssessment
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return RiskAssessment
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
     * Set category
     *
     * @param \App\ApiBundle\Entity\RiskAssessmentCategory $category
     *
     * @return RiskAssessmentQuestion
     */
    public function setCategory(\App\ApiBundle\Entity\RiskAssessmentCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \App\ApiBundle\Entity\RiskAssessmentCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}
