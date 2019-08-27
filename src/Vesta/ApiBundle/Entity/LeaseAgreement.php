<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * LeaseAgreement
 *
 * @ORM\Table(name="lease_agreement")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("token",message="Token can't be duplicated.")
 * @ORM\HasLifecycleCallbacks
 */
class LeaseAgreement
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
    * @ORM\ManyToOne(targetEntity="Property", inversedBy="lease_agreements")
    * @ORM\JoinColumn(name="property", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @Assert\NotBlank(message="The property is required.")
    */
    private $property;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @Assert\NotBlank(message="The start date is required.")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     * @Assert\NotBlank(message="The end date is required.")
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_date", type="datetime", nullable=true)
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $reviewDate;

    /**
     * @var string
     *
     * @ORM\Column(name="core_lease_charge_per_week", type="decimal", precision=8, scale=2)
     * @JMS\Expose
     */
    private $coreLeaseChargePerWeek;

    /**
     * @var string
     *
     * @ORM\Column(name="lease_agreement_file", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The lease agreement file is required.")
     */
    private $leaseAgreementFile;

    /**
     * @var string
     *
     * @ORM\Column(name="management_agreement_file", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The management agreement file is required.")
     */
    private $managementAgreementFile;

    /**
    * @ORM\ManyToOne(targetEntity="Other")
    * @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
    * @JMS\Expose
    * @Assert\NotBlank(message="The owner is required.")
    */
    private $owner;

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
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted=false;

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
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * Set leaseAgreementFile
     *
     * @param string $leaseAgreementFile
     *
     * @return LeaseAgreement
     */
    public function setLeaseAgreementFile($leaseAgreementFile)
    {
        $this->leaseAgreementFile = $leaseAgreementFile;

        return $this;
    }

    /**
     * Get leaseAgreementFile
     *
     * @return string
     */
    public function getLeaseAgreementFile()
    {
        return $this->leaseAgreementFile;
    }

    /**
     * Set managementAgreementFile
     *
     * @param string $managementAgreementFile
     *
     * @return LeaseAgreement
     */
    public function setManagementAgreementFile($managementAgreementFile)
    {
        $this->managementAgreementFile = $managementAgreementFile;

        return $this;
    }

    /**
     * Get managementAgreementFile
     *
     * @return string
     */
    public function getManagementAgreementFile()
    {
        return $this->managementAgreementFile;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * Set property
     *
     * @param \App\ApiBundle\Entity\Property $property
     *
     * @return LeaseAgreement
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
     * Set owner
     *
     * @param \App\ApiBundle\Entity\Other $owner
     *
     * @return LeaseAgreement
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

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return LeaseAgreement
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
     * @return LeaseAgreement
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
     * Get lease_agreement_permalink
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("lease_agreement_permalink")
     * @return string
     */
    public function getLeaseAgreementFilePermalink()
    {
        if($this->getToken() && $this->getLeaseAgreementFile())
            return "/uploads/lease-agreements/".$this->getToken()."/documents/".$this->getLeaseAgreementFile();
        else
            return "";
    }

    /**
     * Get lease_management_permalink
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("lease_management_permalink")
     * @return string
     */
    public function getManagementAgreementFilePermalink()
    {
        if($this->getToken() && $this->getManagementAgreementFile())
            return "/uploads/lease-agreements/".$this->getToken()."/documents/".$this->getManagementAgreementFile();
        else
            return "";
    }

    /**
     * Set coreLeaseChargePerWeek
     *
     * @param string $coreLeaseChargePerWeek
     *
     * @return LeaseAgreement
     */
    public function setCoreLeaseChargePerWeek($coreLeaseChargePerWeek)
    {
        $this->coreLeaseChargePerWeek = $coreLeaseChargePerWeek;

        return $this;
    }

    /**
     * Get coreLeaseChargePerWeek
     *
     * @return string
     */
    public function getCoreLeaseChargePerWeek()
    {
        return $this->coreLeaseChargePerWeek;
    }

    public function getFiles()
    {
        $files = array();
        $i=1;

        if($this->getLeaseAgreementFilePermalink())
            $files[] = array("id"=>$this->getId()."-".$i++, "name"=>$this->getLeaseAgreementFile(), "path"=>$this->getLeaseAgreementFilePermalink());

        if($this->getManagementAgreementFilePermalink())
            $files[] = array("id"=>$this->getId()."-".$i++, "name"=>$this->getManagementAgreementFile(), "path"=>$this->getManagementAgreementFilePermalink());

        return $files;
    }
}
