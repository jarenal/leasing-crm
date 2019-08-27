<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * RentBreakdown
 *
 * @ORM\Table(name="rent_breakdown")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class RentBreakdown
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"onlyid"})
     * @JMS\Expose
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Tenancy", inversedBy="breakdowns")
    * @ORM\JoinColumn(name="tenancy", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @Assert\NotBlank(message="The tenancy is required.")
    * @JMS\MaxDepth(2)
    */
    private $tenancy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     */
    private $startDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="recurring_rent_review", type="smallint")
     * @JMS\Expose
     */
    private $recurringRentReview;

    /**
     *
     * @ORM\Column(name="recurring_rent_review_timescale", type="review_timescale")
     * @JMS\Expose
     * @Assert\Choice(choices = {"Weeks", "Months", "Years"}, message = "The rent review timescale only can be: Weeks, Months, or Years.")
     * @Assert\NotBlank(message="The rent review timescale is required.")
     */
    private $recurringRentReviewTimescale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Expose
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
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $created_by;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $updated_by;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted=false;

    /**
    * @ORM\OneToMany(targetEntity="BreakdownHasItems", mappedBy="breakdown")
    * @JMS\MaxDepth(3)
    * @JMS\Expose
    */
    private $items;

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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return RentBreakdown
     */
    public function setStartDate($startDate)
    {
        if($startDate)
            $this->startDate =  new \DateTime(str_replace("/", ".", $startDate));
        else
            $this->startDate = null;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set recurringRentReview
     *
     * @param integer $recurringRentReview
     *
     * @return RentBreakdown
     */
    public function setRecurringRentReview($recurringRentReview)
    {
        $this->recurringRentReview = $recurringRentReview;

        return $this;
    }

    /**
     * Get recurringRentReview
     *
     * @return integer
     */
    public function getRecurringRentReview()
    {
        return $this->recurringRentReview;
    }

    /**
     * Set recurringRentReviewTimescale
     *
     * @param string $recurringRentReviewTimescale
     *
     * @return RentBreakdown
     */
    public function setRecurringRentReviewTimescale($recurringRentReviewTimescale)
    {
        $this->recurringRentReviewTimescale = $recurringRentReviewTimescale;

        return $this;
    }

    /**
     * Get recurringRentReviewTimescale
     *
     * @return string
     */
    public function getRecurringRentReviewTimescale()
    {
        return $this->recurringRentReviewTimescale;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return RentBreakdown
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
     * @return RentBreakdown
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
     * Set tenancy
     *
     * @param \App\ApiBundle\Entity\Tenancy $tenancy
     *
     * @return RentBreakdown
     */
    public function setTenancy(\App\ApiBundle\Entity\Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    /**
     * Get tenancy
     *
     * @return \App\ApiBundle\Entity\Tenancy
     */
    public function getTenancy()
    {
        return $this->tenancy;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return RentBreakdown
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
     * @return RentBreakdown
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return RentBreakdown
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
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add item
     *
     * @param \App\ApiBundle\Entity\BreakdownHasItems $item
     *
     * @return RentBreakdown
     */
    public function addItem(\App\ApiBundle\Entity\BreakdownHasItems $item)
    {
        $item->setBreakdown($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \App\ApiBundle\Entity\BreakdownHasItems $item
     */
    public function removeItem(\App\ApiBundle\Entity\BreakdownHasItems $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
