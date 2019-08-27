<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Property
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
    * @ORM\ManyToOne(targetEntity="Landlord", inversedBy="properties")
    * @ORM\JoinColumn(name="landlord_id", referencedColumnName="id")
    *
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    * @Assert\NotBlank(message="The landlord is required.")
    */
    private $landlord;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The address is required.")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=10)
     * @JMS\Expose
     * @Assert\NotBlank(message="The postcode is required.")
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The town is required.")
     */
    private $town;

    /**
    * @ORM\ManyToOne(targetEntity="Organisation")
    * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
    *
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    * @Assert\NotBlank(message="The local authority is required.")
    */
    private $local_authority;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="availableDate", type="date", nullable=true)
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     * @Assert\NotBlank(message="The available date is required.")
     */
    private $availableDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="parkingFor", type="smallint", nullable=true)
     * @JMS\Expose
     */
    private $parkingFor;

    /**
     * @var string
     *
     * @ORM\Column(name="specialDesignFeatures", type="text", nullable=true)
     * @JMS\Expose
     */
    private $specialDesignFeatures;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyValue", type="decimal", nullable=true, precision=9, scale=2)
     * @JMS\Expose
     */
    private $propertyValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valuationDate", type="date", nullable=true)
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     */
    private $valuationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="targetRent", type="decimal", precision=6, scale=2)
     * @JMS\Expose
     * @Assert\NotBlank(message="The target rent is required.")
     */
    private $targetRent;

    /**
     * Mortgage outstanding on property?
     *
     * @var boolean
     *
     * @ORM\Column(name="mortgage_outstanding", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $mortgageOutstanding=false;

    /**
     * Buy to Let permitted on the mortgage?
     *
     * @var boolean
     *
     * @ORM\Column(name="buy_to_let", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $buyToLet=false;

    /**
    * @ORM\ManyToMany(targetEntity="Feature", inversedBy="properties", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="property_has_feature")
    *
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $features;

    /**
     * @var boolean
     *
     * @ORM\Column(name="previous_crimes_near", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $previousCrimesNear=false;

    /**
     * @var string
     *
     * @ORM\Column(name="previous_crimes_description", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $previousCrimesDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="land_registry_docs", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $landRegistryDocs=false;

    /**
    * @ORM\ManyToOne(targetEntity="PropertyStatus")
    * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    * @Assert\NotBlank(message="The status is required.")
    */
    private $status;

    /**
    * @ORM\OneToMany(targetEntity="File", mappedBy="property", orphanRemoval=true, cascade={"persist"})
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $files;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $token;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $created_by;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $updated_by;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Expose
     */
    private $created_at;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @JMS\Expose
     */
    private $updated_at;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $deleted=false;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     * @JMS\Expose
     */
    private $comments;

    /**
    * @ORM\ManyToMany(targetEntity="Ticket", mappedBy="related_properties", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tickets_related_properties",
    *      joinColumns={@ORM\JoinColumn(name="property_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")}
    *      )
    * @JMS\MaxDepth(1)
    */
    private $related_tickets;

    /**
    * @ORM\OneToMany(targetEntity="PropertyRiskAssessment", mappedBy="property")
    * @JMS\MaxDepth(3)
    * @JMS\Expose
    */
    private $risks_assessments;

    /**
    * @ORM\OneToMany(targetEntity="Tenancy", mappedBy="property")
    * @JMS\MaxDepth(3)
    * @JMS\Expose
    */
    private $tenancies;

    /**
    * @ORM\OneToMany(targetEntity="LeaseAgreement", mappedBy="property")
    * @JMS\MaxDepth(3)
    * @JMS\Expose
    */
    private $lease_agreements;

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
     * Set address
     *
     * @param string $address
     * @return Property
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return Property
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set town
     *
     * @param string $town
     * @return Property
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set availableDate
     *
     * @param \DateTime $availableDate
     * @return Property
     */
    public function setAvailableDate($availableDate)
    {
        if($availableDate)
            $this->availableDate = new \DateTime(str_replace("/", ".", $availableDate));
        else
            $this->availableDate = null;

        return $this;
    }

    /**
     * Get availableDate
     *
     * @return \DateTime
     */
    public function getAvailableDate()
    {
        return $this->availableDate;
    }

    /**
     * Set parkingFor
     *
     * @param integer $parkingFor
     * @return Property
     */
    public function setParkingFor($parkingFor)
    {
        $this->parkingFor = $parkingFor;

        return $this;
    }

    /**
     * Get parkingFor
     *
     * @return integer
     */
    public function getParkingFor()
    {
        return $this->parkingFor;
    }

    /**
     * Set specialDesignFeatures
     *
     * @param string $specialDesignFeatures
     * @return Property
     */
    public function setSpecialDesignFeatures($specialDesignFeatures)
    {
        $this->specialDesignFeatures = $specialDesignFeatures;

        return $this;
    }

    /**
     * Get specialDesignFeatures
     *
     * @return string
     */
    public function getSpecialDesignFeatures()
    {
        return $this->specialDesignFeatures;
    }

    /**
     * Set propertyValue
     *
     * @param string $propertyValue
     * @return Property
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return string
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Set valuationDate
     *
     * @param \DateTime $valuationDate
     * @return Property
     */
    public function setValuationDate($valuationDate)
    {
        if($valuationDate)
            $this->valuationDate = new \DateTime(str_replace("/", ".", $valuationDate));
        else
            $this->valuationDate = null;

        return $this;
    }

    /**
     * Get valuationDate
     *
     * @return \DateTime
     */
    public function getValuationDate()
    {
        return $this->valuationDate;
    }

    /**
     * Set targetRent
     *
     * @param string $targetRent
     * @return Property
     */
    public function setTargetRent($targetRent)
    {
        $this->targetRent = $targetRent;

        return $this;
    }

    /**
     * Get targetRent
     *
     * @return string
     */
    public function getTargetRent()
    {
        return $this->targetRent;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->features = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add features
     *
     * @param \App\ApiBundle\Entity\Feature $features
     * @return Property
     */
    public function addFeature(\App\ApiBundle\Entity\Feature $features)
    {
        $this->features[] = $features;

        return $this;
    }

    /**
     * Remove features
     *
     * @param \App\ApiBundle\Entity\Feature $features
     */
    public function removeFeature(\App\ApiBundle\Entity\Feature $features)
    {
        $this->features->removeElement($features);
    }

    /**
     * Get features
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set previousCrimesDescription
     *
     * @param string $previousCrimesDescription
     * @return Property
     */
    public function setPreviousCrimesDescription($previousCrimesDescription)
    {
        $this->previousCrimesDescription = $previousCrimesDescription;

        return $this;
    }

    /**
     * Get previousCrimesDescription
     *
     * @return string
     */
    public function getPreviousCrimesDescription()
    {
        return $this->previousCrimesDescription;
    }

    /**
     * Set landlord
     *
     * @param \App\ApiBundle\Entity\Landlord $landlord
     * @return Property
     */
    public function setLandlord(\App\ApiBundle\Entity\Landlord $landlord = null)
    {
        $this->landlord = $landlord;

        return $this;
    }

    /**
     * Get landlord
     *
     * @return \App\ApiBundle\Entity\Landlord
     */
    public function getLandlord()
    {
        return $this->landlord;
    }

    /**
     * Set local_authority
     *
     * @param \App\ApiBundle\Entity\Organisation $localAuthority
     * @return Property
     */
    public function setLocalAuthority(\App\ApiBundle\Entity\Organisation $localAuthority = null)
    {
        $this->local_authority = $localAuthority;

        return $this;
    }

    /**
     * Get local_authority
     *
     * @return \App\ApiBundle\Entity\Organisation
     */
    public function getLocalAuthority()
    {
        return $this->local_authority;
    }

    /**
     * Set status
     *
     * @param \App\ApiBundle\Entity\PropertyStatus $status
     * @return Property
     */
    public function setStatus(\App\ApiBundle\Entity\PropertyStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \App\ApiBundle\Entity\PropertyStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getFeaturesIds()
    {
        $tmp = array();
        foreach ($this->features as $feature)
        {
            $tmp[] = $feature->getId();
        }

        return $tmp;
    }

    /**
     * Set mortgageOutstanding
     *
     * @param boolean $mortgageOutstanding
     *
     * @return Property
     */
    public function setMortgageOutstanding($mortgageOutstanding)
    {
        $this->mortgageOutstanding = $mortgageOutstanding;

        return $this;
    }

    /**
     * Get mortgageOutstanding
     *
     * @return boolean
     */
    public function getMortgageOutstanding()
    {
        return $this->mortgageOutstanding;
    }

    /**
     * Set buyToLet
     *
     * @param boolean $buyToLet
     *
     * @return Property
     */
    public function setBuyToLet($buyToLet)
    {
        $this->buyToLet = $buyToLet;

        return $this;
    }

    /**
     * Get buyToLet
     *
     * @return boolean
     */
    public function getBuyToLet()
    {
        return $this->buyToLet;
    }

    /**
     * Set landRegistryDocs
     *
     * @param boolean $landRegistryDocs
     *
     * @return Property
     */
    public function setLandRegistryDocs($landRegistryDocs)
    {
        $this->landRegistryDocs = $landRegistryDocs;

        return $this;
    }

    /**
     * Get landRegistryDocs
     *
     * @return boolean
     */
    public function getLandRegistryDocs()
    {
        return $this->landRegistryDocs;
    }

    /**
     * Set previousCrimesNear
     *
     * @param boolean $previousCrimesNear
     *
     * @return Property
     */
    public function setPreviousCrimesNear($previousCrimesNear)
    {
        $this->previousCrimesNear = $previousCrimesNear;

        return $this;
    }

    /**
     * Get previousCrimesNear
     *
     * @return boolean
     */
    public function getPreviousCrimesNear()
    {
        return $this->previousCrimesNear;
    }

    /**
     * Add file
     *
     * @param \App\ApiBundle\Entity\File $file
     *
     * @return Property
     */
    public function addFile(\App\ApiBundle\Entity\File $file)
    {
        $file->setProperty($this);
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \App\ApiBundle\Entity\File $file
     */
    public function removeFile(\App\ApiBundle\Entity\File $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Property
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
    * @ORM\PrePersist()
    */
    public function tasksOnPrePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
    }

    /**
    * @ORM\PreUpdate()
    */
    public function tasksOnPreUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entity->setUpdatedAt(new \DateTime());
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Property
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Property
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return Property
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
     * Set modifiedBy
     *
     * @param \App\ApiBundle\Entity\User $modifiedBy
     *
     * @return Property
     */
    public function setModifiedBy(\App\ApiBundle\Entity\User $modifiedBy = null)
    {
        $this->modified_by = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Property
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
     * Set updatedBy
     *
     * @param \App\ApiBundle\Entity\User $updatedBy
     *
     * @return Property
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
     * Set comments
     *
     * @param string $comments
     *
     * @return Property
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
     * Add relatedTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $relatedTicket
     *
     * @return Property
     */
    public function addRelatedTicket(\App\ApiBundle\Entity\Ticket $relatedTicket)
    {
        $this->related_tickets[] = $relatedTicket;

        return $this;
    }

    /**
     * Remove relatedTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $relatedTicket
     */
    public function removeRelatedTicket(\App\ApiBundle\Entity\Ticket $relatedTicket)
    {
        $this->related_tickets->removeElement($relatedTicket);
    }

    /**
     * Get relatedTickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedTickets()
    {
        return $this->related_tickets;
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
        return $this->getAddress().", ".$this->getTown().", ".$this->getPostcode()." (".$this->getLandlord()->getFullname().")";
    }

    /**
     * Add risksAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $risksAssessment
     *
     * @return Property
     */
    public function addRisksAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $risksAssessment)
    {
        $this->risks_assessments[] = $risksAssessment;

        return $this;
    }

    /**
     * Remove risksAssessment
     *
     * @param \App\ApiBundle\Entity\PropertyRiskAssessment $risksAssessment
     */
    public function removeRisksAssessment(\App\ApiBundle\Entity\PropertyRiskAssessment $risksAssessment)
    {
        $this->risks_assessments->removeElement($risksAssessment);
    }

    /**
     * Get risksAssessments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRisksAssessments()
    {
        return $this->risks_assessments;
    }


    /**
     * Add leaseAgreement
     *
     * @param \App\ApiBundle\Entity\LeaseAgreement $leaseAgreement
     *
     * @return Property
     */
    public function addLeaseAgreement(\App\ApiBundle\Entity\LeaseAgreement $leaseAgreement)
    {
        $this->lease_agreements[] = $leaseAgreement;

        return $this;
    }

    /**
     * Remove leaseAgreement
     *
     * @param \App\ApiBundle\Entity\LeaseAgreement $leaseAgreement
     */
    public function removeLeaseAgreement(\App\ApiBundle\Entity\LeaseAgreement $leaseAgreement)
    {
        $this->lease_agreements->removeElement($leaseAgreement);
    }

    /**
     * Get leaseAgreements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeaseAgreements()
    {
        return $this->lease_agreements;
    }

    /**
     * Add tenancy
     *
     * @param \App\ApiBundle\Entity\Tenancy $tenancy
     *
     * @return Property
     */
    public function addTenancy(\App\ApiBundle\Entity\Tenancy $tenancy)
    {
        $this->tenancies[] = $tenancy;

        return $this;
    }

    /**
     * Remove tenancy
     *
     * @param \App\ApiBundle\Entity\Tenancy $tenancy
     */
    public function removeTenancy(\App\ApiBundle\Entity\Tenancy $tenancy)
    {
        $this->tenancies->removeElement($tenancy);
    }

    /**
     * Get tenancies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTenancies()
    {
        return $this->tenancies;
    }
}
