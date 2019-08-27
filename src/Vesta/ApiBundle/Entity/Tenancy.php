<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Tenancy
 *
 * @ORM\Table(name="tenancy")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("token",message="Token can't be duplicated.")
 * @ORM\HasLifecycleCallbacks
 */
class Tenancy
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
     * @ORM\Column(name="token", type="string", length=255, unique=true, nullable=false)
     * @JMS\Expose
     * @Assert\NotBlank(message="The token is required.")
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     * @Assert\NotBlank(message="The start date is required.")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     * @Assert\NotBlank(message="The end date is required.")
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_date", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @JMS\Expose
     */
    private $reviewDate;

    /**
     *
     * @ORM\Column(name="tenancy_type", type="tenancy_type")
     * @JMS\Expose
     * @Assert\Choice(choices = {"Single", "Joint", "Shared"}, message = "The tenancy type only can be: Single, Joint or Shared.")
     * @Assert\NotBlank(message="The tenancy type is required.")
     */
    private $tenancyType;

    /**
    * @ORM\ManyToOne(targetEntity="Property", inversedBy="tenancies")
    * @ORM\JoinColumn(name="property", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @Assert\NotBlank(message="The property is required.")
    * @JMS\MaxDepth(2)
    */
    private $property;

    /**
    * @ORM\ManyToMany(targetEntity="Tenant", inversedBy="tenancies")
    * @ORM\JoinTable(name="tenancy_has_tenants",
    *      joinColumns={@ORM\JoinColumn(name="tenancy_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="tenant_id", referencedColumnName="id")}
    *      )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $tenants;

    /**
     * @var string
     *
     * @ORM\Column(name="tenancy_agreement_file", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The tenancy agreement file is required.")
     */
    private $tenancyAgreementFile;

    /**
     * @var string
     *
     * @ORM\Column(name="tenancy_agreement_visual_file", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $tenancyAgreementVisualFile;

    /**
     * @var string
     *
     * @ORM\Column(name="service_level_agreement_file", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $serviceLevelAgreementFile;

    /**
    * @ORM\ManyToOne(targetEntity="Other")
    * @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @Assert\NotBlank(message="The owner is required.")
    * @JMS\MaxDepth(2)
    */
    private $owner;

    /**
    * @ORM\OneToMany(targetEntity="RentBreakdown", mappedBy="tenancy")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    */
    private $breakdowns;

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

    public function __construct()
    {
        $this->tenants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Tenancy
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
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Tenancy
     */
    public function setEndDate($endDate)
    {
        if($endDate)
            $this->endDate =  new \DateTime(str_replace("/", ".", $endDate));
        else
            $this->endDate = null;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set reviewDate
     *
     * @param \DateTime $reviewDate
     *
     * @return Tenancy
     */
    public function setReviewDate($reviewDate)
    {
        if($reviewDate)
            $this->reviewDate =  new \DateTime(str_replace("/", ".", $reviewDate));
        else
            $this->reviewDate = null;

        return $this;
    }

    /**
     * Get reviewDate
     *
     * @return \DateTime
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * Set tenancyAgreementFile
     *
     * @param string $tenancyAgreementFile
     *
     * @return Tenancy
     */
    public function setTenancyAgreementFile($tenancyAgreementFile)
    {
        $this->tenancyAgreementFile = $tenancyAgreementFile;

        return $this;
    }

    /**
     * Get tenancyAgreementFile
     *
     * @return string
     */
    public function getTenancyAgreementFile()
    {
        return $this->tenancyAgreementFile;
    }

    /**
     * Get getTenancyAgreementFilePermalink
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("tenancy_agreement_file_permalink")
     * @return string
     */
    public function getTenancyAgreementFilePermalink()
    {
        if($this->getToken() && $this->getTenancyAgreementFile())
            return "/uploads/tenancies/".$this->getToken()."/documents/".$this->getTenancyAgreementFile();
        else
            return "";
    }

    /**
     * Set tenancyAgreementVisualFile
     *
     * @param string $tenancyAgreementVisualFile
     *
     * @return Tenancy
     */
    public function setTenancyAgreementVisualFile($tenancyAgreementVisualFile)
    {
        $this->tenancyAgreementVisualFile = $tenancyAgreementVisualFile;

        return $this;
    }

    /**
     * Get tenancyAgreementVisualFile
     *
     * @return string
     */
    public function getTenancyAgreementVisualFile()
    {
        return $this->tenancyAgreementVisualFile;
    }

    /**
     * Get getTenancyAgreementVisualFilePermalink
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("tenancy_agreement_visual_file_permalink")
     * @return string
     */
    public function getTenancyAgreementVisualFilePermalink()
    {
        if($this->getToken() && $this->getTenancyAgreementVisualFile())
            return "/uploads/tenancies/".$this->getToken()."/documents/".$this->getTenancyAgreementVisualFile();
        else
            return "";
    }

    /**
     * Set serviceLevelAgreementFile
     *
     * @param string $serviceLevelAgreementFile
     *
     * @return Tenancy
     */
    public function setServiceLevelAgreementFile($serviceLevelAgreementFile)
    {
        $this->serviceLevelAgreementFile = $serviceLevelAgreementFile;

        return $this;
    }

    /**
     * Get serviceLevelAgreementFile
     *
     * @return string
     */
    public function getServiceLevelAgreementFile()
    {
        return $this->serviceLevelAgreementFile;
    }

    /**
     * Get getServiceLevelAgreementFilePermalink
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("service_level_agreement_file_permalink")
     * @return string
     */
    public function getServiceLevelAgreementFilePermalink()
    {
        if($this->getToken() && $this->getServiceLevelAgreementFile())
            return "/uploads/tenancies/".$this->getToken()."/documents/".$this->getServiceLevelAgreementFile();
        else
            return "";
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Tenancy
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Tenancy
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
     * @return Tenancy
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
     * @return Tenancy
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
     * Set tenancyType
     *
     * @param tenancy_type $tenancyType
     *
     * @return Tenancy
     */
    public function setTenancyType($tenancyType)
    {
        $this->tenancyType = $tenancyType;

        return $this;
    }

    /**
     * Get tenancyType
     *
     * @return tenancy_type
     */
    public function getTenancyType()
    {
        return $this->tenancyType;
    }

    /**
     * Set property
     *
     * @param \App\ApiBundle\Entity\Property $property
     *
     * @return Tenancy
     */
    public function setProperty(\App\ApiBundle\Entity\Property $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \App\ApiBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Add tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     *
     * @return Tenancy
     */
    public function addTenant(\App\ApiBundle\Entity\Tenant $tenant)
    {
        $this->tenants[] = $tenant;

        return $this;
    }

    /**
     * Remove tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     */
    public function removeTenant(\App\ApiBundle\Entity\Tenant $tenant)
    {
        $this->tenants->removeElement($tenant);
    }

    /**
     * Get tenants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTenants()
    {
        return $this->tenants;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return Tenancy
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
     * @return Tenancy
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
     * Set owner
     *
     * @param \App\ApiBundle\Entity\Other $owner
     *
     * @return Tenancy
     */
    public function setOwner(\App\ApiBundle\Entity\Other $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \App\ApiBundle\Entity\Other
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function getTenantsIds()
    {
        $tmp = array();
        foreach ($this->tenants as $tenant)
        {
            $tmp[] = $tenant->getId();
        }

        return $tmp;
    }

    /**
     * Add breakdown
     *
     * @param \App\ApiBundle\Entity\RentBreakdown $breakdown
     *
     * @return Tenancy
     */
    public function addBreakdown(\App\ApiBundle\Entity\RentBreakdown $breakdown)
    {
        $this->breakdowns[] = $breakdown;

        return $this;
    }

    /**
     * Remove breakdown
     *
     * @param \App\ApiBundle\Entity\RentBreakdown $breakdown
     */
    public function removeBreakdown(\App\ApiBundle\Entity\RentBreakdown $breakdown)
    {
        $this->breakdowns->removeElement($breakdown);
    }

    /**
     * Get breakdowns
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBreakdowns()
    {
        return $this->breakdowns;
    }

    public function getFiles()
    {
        $files = array();
        $i=1;

        if($this->getTenancyAgreementFilePermalink())
            $files[] = array("id"=>$this->getId()."-".$i++, "name"=>$this->getTenancyAgreementFile(), "path"=>$this->getTenancyAgreementFilePermalink());

        if($this->getTenancyAgreementVisualFilePermalink())
            $files[] = array("id"=>$this->getId()."-".$i++, "name"=>$this->getTenancyAgreementVisualFile(), "path"=>$this->getTenancyAgreementVisualFilePermalink());

        if($this->getServiceLevelAgreementFilePermalink())
            $files[] = array("id"=>$this->getId()."-".$i++, "name"=>$this->getServiceLevelAgreementFile(), "path"=>$this->getServiceLevelAgreementFilePermalink());

        return $files;
    }
}
