<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tenant
 *
 * @ORM\Table(name="tenant")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Tenant extends Contact
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date")
     * @JMS\Expose
     * @Assert\NotBlank(message="The birthdate is required.")
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="nin", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The nin is required.")
     */
    private $nin;

    /**
    * @ORM\ManyToOne(targetEntity="Gender")
    * @ORM\JoinColumn(name="gender_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    * @Assert\NotBlank(message="The gender is required.")
    */
    private $gender;

    /**
    * @ORM\ManyToOne(targetEntity="MaritalStatus")
    * @ORM\JoinColumn(name="marital_status_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    * @Assert\NotBlank(message="The marital status is required.")
    */
    private $marital_status;

    /**
    * @ORM\ManyToOne(targetEntity="Organisation")
    * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    * @Assert\NotBlank(message="The local authority is required.")
    */
    private $local_authority;

    /**
    * @ORM\ManyToOne(targetEntity="Other")
    * @ORM\JoinColumn(name="social_services_contact_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $social_services_contact;

    /**
    * @ORM\OneToMany(targetEntity="Child", mappedBy="tenant", orphanRemoval=true, cascade={"persist"})
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $children;

    /**
    * @ORM\ManyToMany(targetEntity="TenantNightsSupport", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tenant_has_nights_support",
    *      joinColumns={@ORM\JoinColumn(name="tenant_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="nights_support_id", referencedColumnName="id")}
    *      )
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $nights_support;

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_night_support", type="boolean")
     * @JMS\Expose
     */
    private $need_night_support;

    /**
     * Has Continuing Health Care Budget?
     *
     * @var boolean
     *
     * @ORM\Column(name="has_chc_budget", type="boolean")
     * @JMS\Expose
     */
    private $has_chc_budget;

    /**
     * @var integer
     *
     * @ORM\Column(name="support_package_hours", type="smallint")
     * @JMS\Expose
     */
    private $support_package_hours;

    /**
    * @ORM\OneToMany(targetEntity="TenantHasCondition", mappedBy="tenant", orphanRemoval=true, cascade={"persist", "remove"})
    * @JMS\Expose
    * @JMS\MaxDepth(3)
    */
    private $tenantHasCondition;

    /**
    * @ORM\ManyToOne(targetEntity="Organisation")
    * @ORM\JoinColumn(name="agency_support_provider_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $agency_support_provider;

    /**
    * @ORM\ManyToOne(targetEntity="Contact")
    * @ORM\JoinColumn(name="contact_support_provider_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $contact_support_provider;

    /**
     * Does the tenant lack capacity?
     *
     * @var boolean
     *
     * @ORM\Column(name="lack_capacity", type="boolean")
     * @JMS\Expose
     */
    private $lackCapacity;

    /**
    * @ORM\ManyToOne(targetEntity="Contact")
    * @ORM\JoinColumn(name="deputy_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $deputy;

    /**
     * Are they on the housing register?
     *
     * @var boolean
     *
     * @ORM\Column(name="housing_register", type="boolean")
     * @JMS\Expose
     */
    private $housingRegister;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="move_date", type="date", nullable=true)
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $moveDate;

    /**
    * @ORM\OneToMany(targetEntity="TenantArea", mappedBy="tenant", orphanRemoval=true, cascade={"persist"})
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $areas;

    /**
     * Is it out of county?
     *
     * @var boolean
     *
     * @ORM\Column(name="out_county", type="boolean")
     * @JMS\Expose
     */
    private $outCounty;

    /**
     * @var string
     *
     * @ORM\Column(name="special_design_features", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $specialDesignFeatures;

    /**
     * @var string
     *
     * @ORM\Column(name="tenant_personality", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $tenantPersonality;

    /**
     * Are they willing to share?
     *
     * @var boolean
     *
     * @ORM\Column(name="willing_to_share", type="boolean")
     * @JMS\Expose
     */
    private $willingToShare;

    /**
     * History of drug and alcohol abuse?
     *
     * @var boolean
     *
     * @ORM\Column(name="drug_historial", type="boolean")
     * @JMS\Expose
     */
    private $drugHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="drug_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $drugHistorialDetails;

    /**
     * History of sexual offences?
     *
     * @var boolean
     *
     * @ORM\Column(name="sexual_offences_historial", type="boolean")
     * @JMS\Expose
     */
    private $sexualOffencesHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="sexual_offences_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $sexualOffencesHistorialDetails;

    /**
     * History of arson?
     *
     * @var boolean
     *
     * @ORM\Column(name="arson_historial", type="boolean")
     * @JMS\Expose
     */
    private $arsonHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="arson_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $arsonHistorialDetails;

    /**
     * History of previous evictions?
     *
     * @var boolean
     *
     * @ORM\Column(name="evictions_historial", type="boolean")
     * @JMS\Expose
     */
    private $evictionsHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="evictions_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $evictionsHistorialDetails;

    /**
     * History of Violence towards Others
     *
     * @var boolean
     *
     * @ORM\Column(name="violence_historial", type="boolean")
     * @JMS\Expose
     */
    private $violenceHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="violence_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $violenceHistorialDetails;

    /**
     * History of Anti-Social Behaviour
     *
     * @var boolean
     *
     * @ORM\Column(name="anti_social_historial", type="boolean")
     * @JMS\Expose
     */
    private $antiSocialHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="anti_social_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $antiSocialHistorialDetails;

    /**
     * History of Rent Arrears
     *
     * @var boolean
     *
     * @ORM\Column(name="rent_arrears_historial", type="boolean")
     * @JMS\Expose
     */
    private $rentArrearsHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="rent_arrears_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $rentArrearsHistorialDetails;

    /**
     * Vulnerability in public
     *
     * @var boolean
     *
     * @ORM\Column(name="vulnerability_historial", type="boolean")
     * @JMS\Expose
     */
    private $vulnerabilityHistorial;

    /**
     * @var string
     *
     * @ORM\Column(name="vulnerability_historial_details", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $vulnerabilityHistorialDetails;

    /**
     * @var string
     *
     * @ORM\Column(name="tenantReferences", type="text", nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $tenantReferences;

    /**
    * @ORM\ManyToMany(targetEntity="Feature", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tenant_has_requirements",
    *      joinColumns={@ORM\JoinColumn(name="tenant_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="requirement_id", referencedColumnName="id")}
    *      )
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $requirements;

    /**
     * @var integer
     *
     * @ORM\Column(name="parking_for", type="smallint")
     * @JMS\Expose
     */
    private $parkingFor;

    /**
    * @ORM\ManyToOne(targetEntity="Other")
    * @ORM\JoinColumn(name="lfl_contact_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $lfl_contact;

    /**
    * @ORM\ManyToMany(targetEntity="Tenancy", mappedBy="tenants")
    *
    * @JMS\MaxDepth(1)
    */
    private $tenancies;

    public function __construct()
    {
        //$this->birthdate = new \DateTime();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->need_night_support = false;
        $this->has_chc_budget = false;
        $this->support_package_hours = 0;
        $this->housingRegister = false;
        $this->outCounty = false;
        $this->willingToShare = false;
        $this->drugHistorial = false;
        $this->sexualOffencesHistorial = false;
        $this->arsonHistorial = false;
        $this->evictionsHistorial = false;
        $this->parkingFor = 0;
        $this->violenceHistorial = false;
        $this->antiSocialHistorial = false;
        $this->rentArrearsHistorial = false;
        $this->vulnerabilityHistorial = false;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return Tenant
     */
    public function setBirthdate($birthdate)
    {
        if($birthdate)
            $this->birthdate = new \DateTime(str_replace("/", ".", $birthdate));
        else
            $this->birthdate = null;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set gender
     *
     * @param \App\ApiBundle\Entity\Gender $gender
     * @return Tenant
     */
    public function setGender(\App\ApiBundle\Entity\Gender $gender = null)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return \App\ApiBundle\Entity\Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set marital_status
     *
     * @param \App\ApiBundle\Entity\MaritalStatus $maritalStatus
     * @return Tenant
     */
    public function setMaritalStatus(\App\ApiBundle\Entity\MaritalStatus $maritalStatus = null)
    {
        $this->marital_status = $maritalStatus;

        return $this;
    }

    /**
     * Get marital_status
     *
     * @return \App\ApiBundle\Entity\MaritalStatus
     */
    public function getMaritalStatus()
    {
        return $this->marital_status;
    }

    /**
     * Set local_authority
     *
     * @param \App\ApiBundle\Entity\Organisation $localAuthority
     * @return Tenant
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

    public function getChildrenIds()
    {
        $tmp = array();
        foreach ($this->children as $child)
        {
            $tmp[] = $child->getId();
        }

        return $tmp;
    }

    /**
     * Add children
     *
     * @param \App\ApiBundle\Entity\Child $children
     * @return Tenant
     */
    public function addChild(\App\ApiBundle\Entity\Child $children)
    {
        $children->setTenant($this);
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \App\ApiBundle\Entity\Child $children
     */
    public function removeChild(\App\ApiBundle\Entity\Child $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add nights_support
     *
     * @param \App\ApiBundle\Entity\TenantNightsSupport $nightsSupport
     * @return Tenant
     */
    public function addNightsSupport(\App\ApiBundle\Entity\TenantNightsSupport $nightsSupport)
    {
        $this->nights_support[] = $nightsSupport;

        return $this;
    }

    /**
     * Remove nights_support
     *
     * @param \App\ApiBundle\Entity\TenantNightsSupport $nightsSupport
     */
    public function removeNightsSupport(\App\ApiBundle\Entity\TenantNightsSupport $nightsSupport)
    {
        $this->nights_support->removeElement($nightsSupport);
    }

    /**
     * Get nights_support
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNightsSupport()
    {
        return $this->nights_support;
    }

    public function getNightsSupportIds()
    {
        $tmp = array();
        foreach ($this->nights_support as $support)
        {
            $tmp[] = $support->getId();
        }

        return $tmp;
    }

    public function getNightsSupportNames()
    {
        $tmp = array();
        foreach ($this->nights_support as $support)
        {
            $tmp[] = $support->getName();
        }

        return $tmp;
    }

    /**
     * Set need_night_support
     *
     * @param boolean $needNightSupport
     * @return Tenant
     */
    public function setNeedNightSupport($needNightSupport)
    {
        $this->need_night_support = $needNightSupport;

        return $this;
    }

    /**
     * Get need_night_support
     *
     * @return boolean
     */
    public function getNeedNightSupport()
    {
        return $this->need_night_support;
    }

    /**
     * Set has_chc_budget
     *
     * @param boolean $hasChcBudget
     * @return Tenant
     */
    public function setHasChcBudget($hasChcBudget)
    {
        $this->has_chc_budget = $hasChcBudget;

        return $this;
    }

    /**
     * Get has_chc_budget
     *
     * @return boolean
     */
    public function getHasChcBudget()
    {
        return $this->has_chc_budget;
    }

    /**
     * Set support_package_hours
     *
     * @param integer $supportPackageHours
     * @return Tenant
     */
    public function setSupportPackageHours($supportPackageHours)
    {
        $this->support_package_hours = $supportPackageHours;

        return $this;
    }

    /**
     * Get support_package_hours
     *
     * @return integer
     */
    public function getSupportPackageHours()
    {
        return $this->support_package_hours;
    }

    /**
     * Add tenantHasCondition
     *
     * @param \App\ApiBundle\Entity\TenantHasCondition $tenantHasCondition
     * @return Tenant
     */
    public function addTenantHasCondition(\App\ApiBundle\Entity\TenantHasCondition $tenantHasCondition)
    {
        $this->tenantHasCondition[] = $tenantHasCondition;

        return $this;
    }

    /**
     * Remove tenantHasCondition
     *
     * @param \App\ApiBundle\Entity\TenantHasCondition $tenantHasCondition
     */
    public function removeTenantHasCondition(\App\ApiBundle\Entity\TenantHasCondition $tenantHasCondition)
    {
        $this->tenantHasCondition->removeElement($tenantHasCondition);
    }

    /**
     * Get tenantHasCondition
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTenantHasCondition()
    {
        return $this->tenantHasCondition;
    }

    public function getConditionsIds()
    {
        $tmp = array();
        foreach ($this->tenantHasCondition as $thc)
        {
            $tmp[] = $thc->getCondition()->getId();
        }

        return $tmp;
    }

    /**
     * Set support_provider
     *
     * @param \App\ApiBundle\Entity\Other $supportProvider
     * @return Tenant
     */
    public function setSupportProvider(\App\ApiBundle\Entity\Other $supportProvider = null)
    {
        $this->support_provider = $supportProvider;

        return $this;
    }

    /**
     * Get support_provider
     *
     * @return \App\ApiBundle\Entity\Other
     */
    public function getSupportProvider()
    {
        return $this->support_provider;
    }

    /**
     * Set deputy
     *
     * @param \App\ApiBundle\Entity\Contact $deputy
     * @return Tenant
     */
    public function setDeputy(\App\ApiBundle\Entity\Contact $deputy = null)
    {
        $this->deputy = $deputy;

        return $this;
    }

    /**
     * Get deputy
     *
     * @return \App\ApiBundle\Entity\Contact
     */
    public function getDeputy()
    {
        return $this->deputy;
    }

    /**
     * Set agency_support_provider
     *
     * @param \App\ApiBundle\Entity\Organisation $agencySupportProvider
     * @return Tenant
     */
    public function setAgencySupportProvider(\App\ApiBundle\Entity\Organisation $agencySupportProvider = null)
    {
        $this->agency_support_provider = $agencySupportProvider;

        return $this;
    }

    /**
     * Get agency_support_provider
     *
     * @return \App\ApiBundle\Entity\Organisation
     */
    public function getAgencySupportProvider()
    {
        return $this->agency_support_provider;
    }

    /**
     * Set contact_support_provider
     *
     * @param \App\ApiBundle\Entity\Contact $contactSupportProvider
     * @return Tenant
     */
    public function setContactSupportProvider(\App\ApiBundle\Entity\Contact $contactSupportProvider = null)
    {
        $this->contact_support_provider = $contactSupportProvider;

        return $this;
    }

    /**
     * Get contact_support_provider
     *
     * @return \App\ApiBundle\Entity\Contact
     */
    public function getContactSupportProvider()
    {
        return $this->contact_support_provider;
    }


    /**
     * Set housingRegister
     *
     * @param boolean $housingRegister
     * @return Tenant
     */
    public function setHousingRegister($housingRegister)
    {
        $this->housingRegister = $housingRegister;

        return $this;
    }

    /**
     * Get housingRegister
     *
     * @return boolean
     */
    public function getHousingRegister()
    {
        return $this->housingRegister;
    }

    /**
     * Set moveDate
     *
     * @param \DateTime $moveDate
     * @return Tenant
     */
    public function setMoveDate($moveDate)
    {
        if($moveDate)
            $this->moveDate = new \DateTime(str_replace("/", ".", $moveDate));
        else
            $this->moveDate = null;

        return $this;
    }

    /**
     * Get moveDate
     *
     * @return \DateTime
     */
    public function getMoveDate()
    {
        return $this->moveDate;
    }

    /**
     * Set outCounty
     *
     * @param boolean $outCounty
     * @return Tenant
     */
    public function setOutCounty($outCounty)
    {
        $this->outCounty = $outCounty;

        return $this;
    }

    /**
     * Get outCounty
     *
     * @return boolean
     */
    public function getOutCounty()
    {
        return $this->outCounty;
    }

    /**
     * Set specialDesignFeatures
     *
     * @param string $specialDesignFeatures
     * @return Tenant
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
     * Set tenantPersonality
     *
     * @param string $tenantPersonality
     * @return Tenant
     */
    public function setTenantPersonality($tenantPersonality)
    {
        $this->tenantPersonality = $tenantPersonality;

        return $this;
    }

    /**
     * Get tenantPersonality
     *
     * @return string
     */
    public function getTenantPersonality()
    {
        return $this->tenantPersonality;
    }

    /**
     * Set willingToShare
     *
     * @param boolean $willingToShare
     * @return Tenant
     */
    public function setWillingToShare($willingToShare)
    {
        $this->willingToShare = $willingToShare;

        return $this;
    }

    /**
     * Get willingToShare
     *
     * @return boolean
     */
    public function getWillingToShare()
    {
        return $this->willingToShare;
    }

    /**
     * Set drugHistorial
     *
     * @param boolean $drugHistorial
     * @return Tenant
     */
    public function setDrugHistorial($drugHistorial)
    {
        $this->drugHistorial = $drugHistorial;

        return $this;
    }

    /**
     * Get drugHistorial
     *
     * @return boolean
     */
    public function getDrugHistorial()
    {
        return $this->drugHistorial;
    }

    /**
     * Set drugHistorialDetails
     *
     * @param string $drugHistorialDetails
     * @return Tenant
     */
    public function setDrugHistorialDetails($drugHistorialDetails)
    {
        $this->drugHistorialDetails = $drugHistorialDetails;

        return $this;
    }

    /**
     * Get drugHistorialDetails
     *
     * @return string
     */
    public function getDrugHistorialDetails()
    {
        return $this->drugHistorialDetails;
    }

    /**
     * Set sexualOffencesHistorial
     *
     * @param boolean $sexualOffencesHistorial
     * @return Tenant
     */
    public function setSexualOffencesHistorial($sexualOffencesHistorial)
    {
        $this->sexualOffencesHistorial = $sexualOffencesHistorial;

        return $this;
    }

    /**
     * Get sexualOffencesHistorial
     *
     * @return boolean
     */
    public function getSexualOffencesHistorial()
    {
        return $this->sexualOffencesHistorial;
    }

    /**
     * Set sexualOffencesHistorialDetails
     *
     * @param string $sexualOffencesHistorialDetails
     * @return Tenant
     */
    public function setSexualOffencesHistorialDetails($sexualOffencesHistorialDetails)
    {
        $this->sexualOffencesHistorialDetails = $sexualOffencesHistorialDetails;

        return $this;
    }

    /**
     * Get sexualOffencesHistorialDetails
     *
     * @return string
     */
    public function getSexualOffencesHistorialDetails()
    {
        return $this->sexualOffencesHistorialDetails;
    }

    /**
     * Set arsonHistorial
     *
     * @param boolean $arsonHistorial
     * @return Tenant
     */
    public function setArsonHistorial($arsonHistorial)
    {
        $this->arsonHistorial = $arsonHistorial;

        return $this;
    }

    /**
     * Get arsonHistorial
     *
     * @return boolean
     */
    public function getArsonHistorial()
    {
        return $this->arsonHistorial;
    }

    /**
     * Set arsonHistorialDetails
     *
     * @param string $arsonHistorialDetails
     * @return Tenant
     */
    public function setArsonHistorialDetails($arsonHistorialDetails)
    {
        $this->arsonHistorialDetails = $arsonHistorialDetails;

        return $this;
    }

    /**
     * Get arsonHistorialDetails
     *
     * @return string
     */
    public function getArsonHistorialDetails()
    {
        return $this->arsonHistorialDetails;
    }

    /**
     * Set evictionsHistorial
     *
     * @param boolean $evictionsHistorial
     * @return Tenant
     */
    public function setEvictionsHistorial($evictionsHistorial)
    {
        $this->evictionsHistorial = $evictionsHistorial;

        return $this;
    }

    /**
     * Get evictionsHistorial
     *
     * @return boolean
     */
    public function getEvictionsHistorial()
    {
        return $this->evictionsHistorial;
    }

    /**
     * Set evictionsHistorialDetails
     *
     * @param string $evictionsHistorialDetails
     * @return Tenant
     */
    public function setEvictionsHistorialDetails($evictionsHistorialDetails)
    {
        $this->evictionsHistorialDetails = $evictionsHistorialDetails;

        return $this;
    }

    /**
     * Get evictionsHistorialDetails
     *
     * @return string
     */
    public function getEvictionsHistorialDetails()
    {
        return $this->evictionsHistorialDetails;
    }

    /**
     * Set tenantReferences
     *
     * @param string $tenantReferences
     * @return Tenant
     */
    public function setTenantReferences($tenantReferences)
    {
        $this->tenantReferences = $tenantReferences;

        return $this;
    }

    /**
     * Get tenantReferences
     *
     * @return string
     */
    public function getTenantReferences()
    {
        return $this->tenantReferences;
    }

    /**
     * Add areas
     *
     * @param \App\ApiBundle\Entity\TenantArea $areas
     * @return Tenant
     */
    public function addArea(\App\ApiBundle\Entity\TenantArea $areas)
    {
        $areas->setTenant($this);
        $this->areas[] = $areas;

        return $this;
    }

    /**
     * Remove areas
     *
     * @param \App\ApiBundle\Entity\TenantArea $areas
     */
    public function removeArea(\App\ApiBundle\Entity\TenantArea $areas)
    {
        $this->areas->removeElement($areas);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    public function getAreasIds()
    {
        $tmp = array();
        foreach ($this->areas as $area)
        {
            $tmp[] = $area->getId();
        }

        return $tmp;
    }

    public function getRequirementsIds()
    {
        $tmp = array();
        foreach ($this->requirements as $requirement)
        {
            $tmp[] = $requirement->getId();
        }

        return $tmp;
    }

    /**
     * Set parkingFor
     *
     * @param integer $parkingFor
     * @return Tenant
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
     * Add requirements
     *
     * @param \App\ApiBundle\Entity\Feature $requirements
     * @return Tenant
     */
    public function addRequirement(\App\ApiBundle\Entity\Feature $requirements)
    {
        $this->requirements[] = $requirements;

        return $this;
    }

    /**
     * Remove requirements
     *
     * @param \App\ApiBundle\Entity\Feature $requirements
     */
    public function removeRequirement(\App\ApiBundle\Entity\Feature $requirements)
    {
        $this->requirements->removeElement($requirements);
    }

    /**
     * Get requirements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set socialServicesContact
     *
     * @param \App\ApiBundle\Entity\Other $socialServicesContact
     *
     * @return Tenant
     */
    public function setSocialServicesContact(\App\ApiBundle\Entity\Other $socialServicesContact = null)
    {
        $this->social_services_contact = $socialServicesContact;

        return $this;
    }

    /**
     * Get socialServicesContact
     *
     * @return \App\ApiBundle\Entity\Other
     */
    public function getSocialServicesContact()
    {
        return $this->social_services_contact;
    }

    /**
     * Set lflContact
     *
     * @param \App\ApiBundle\Entity\Other $lflContact
     *
     * @return Tenant
     */
    public function setLflContact(\App\ApiBundle\Entity\Other $lflContact = null)
    {
        $this->lfl_contact = $lflContact;

        return $this;
    }

    /**
     * Get lflContact
     *
     * @return \App\ApiBundle\Entity\Other
     */
    public function getLflContact()
    {
        return $this->lfl_contact;
    }

    /**
     * Set violenceHistorial
     *
     * @param boolean $violenceHistorial
     *
     * @return Tenant
     */
    public function setViolenceHistorial($violenceHistorial)
    {
        $this->violenceHistorial = $violenceHistorial;

        return $this;
    }

    /**
     * Get violenceHistorial
     *
     * @return boolean
     */
    public function getViolenceHistorial()
    {
        return $this->violenceHistorial;
    }

    /**
     * Set violenceHistorialDetails
     *
     * @param string $violenceHistorialDetails
     *
     * @return Tenant
     */
    public function setViolenceHistorialDetails($violenceHistorialDetails)
    {
        $this->violenceHistorialDetails = $violenceHistorialDetails;

        return $this;
    }

    /**
     * Get violenceHistorialDetails
     *
     * @return string
     */
    public function getViolenceHistorialDetails()
    {
        return $this->violenceHistorialDetails;
    }

    /**
     * Set antiSocialHistorial
     *
     * @param boolean $antiSocialHistorial
     *
     * @return Tenant
     */
    public function setAntiSocialHistorial($antiSocialHistorial)
    {
        $this->antiSocialHistorial = $antiSocialHistorial;

        return $this;
    }

    /**
     * Get antiSocialHistorial
     *
     * @return boolean
     */
    public function getAntiSocialHistorial()
    {
        return $this->antiSocialHistorial;
    }

    /**
     * Set antiSocialHistorialDetails
     *
     * @param string $antiSocialHistorialDetails
     *
     * @return Tenant
     */
    public function setAntiSocialHistorialDetails($antiSocialHistorialDetails)
    {
        $this->antiSocialHistorialDetails = $antiSocialHistorialDetails;

        return $this;
    }

    /**
     * Get antiSocialHistorialDetails
     *
     * @return string
     */
    public function getAntiSocialHistorialDetails()
    {
        return $this->antiSocialHistorialDetails;
    }

    /**
     * Set rentArrearsHistorial
     *
     * @param boolean $rentArrearsHistorial
     *
     * @return Tenant
     */
    public function setRentArrearsHistorial($rentArrearsHistorial)
    {
        $this->rentArrearsHistorial = $rentArrearsHistorial;

        return $this;
    }

    /**
     * Get rentArrearsHistorial
     *
     * @return boolean
     */
    public function getRentArrearsHistorial()
    {
        return $this->rentArrearsHistorial;
    }

    /**
     * Set rentArrearsHistorialDetails
     *
     * @param string $rentArrearsHistorialDetails
     *
     * @return Tenant
     */
    public function setRentArrearsHistorialDetails($rentArrearsHistorialDetails)
    {
        $this->rentArrearsHistorialDetails = $rentArrearsHistorialDetails;

        return $this;
    }

    /**
     * Get rentArrearsHistorialDetails
     *
     * @return string
     */
    public function getRentArrearsHistorialDetails()
    {
        return $this->rentArrearsHistorialDetails;
    }

    /**
     * Set vulnerabilityHistorial
     *
     * @param boolean $vulnerabilityHistorial
     *
     * @return Tenant
     */
    public function setVulnerabilityHistorial($vulnerabilityHistorial)
    {
        $this->vulnerabilityHistorial = $vulnerabilityHistorial;

        return $this;
    }

    /**
     * Get vulnerabilityHistorial
     *
     * @return boolean
     */
    public function getVulnerabilityHistorial()
    {
        return $this->vulnerabilityHistorial;
    }

    /**
     * Set vulnerabilityHistorialDetails
     *
     * @param string $vulnerabilityHistorialDetails
     *
     * @return Tenant
     */
    public function setVulnerabilityHistorialDetails($vulnerabilityHistorialDetails)
    {
        $this->vulnerabilityHistorialDetails = $vulnerabilityHistorialDetails;

        return $this;
    }

    /**
     * Get vulnerabilityHistorialDetails
     *
     * @return string
     */
    public function getVulnerabilityHistorialDetails()
    {
        return $this->vulnerabilityHistorialDetails;
    }

    /**
     * Set nin
     *
     * @param string $nin
     *
     * @return Tenant
     */
    public function setNin($nin)
    {
        $this->nin = $nin;

        return $this;
    }

    /**
     * Get nin
     *
     * @return string
     */
    public function getNin()
    {
        return $this->nin;
    }

    /**
     * Set lackCapacity
     *
     * @param boolean $lackCapacity
     *
     * @return Tenant
     */
    public function setLackCapacity($lackCapacity)
    {
        $this->lackCapacity = $lackCapacity;

        return $this;
    }

    /**
     * Get lackCapacity
     *
     * @return boolean
     */
    public function getLackCapacity()
    {
        return $this->lackCapacity;
    }

    public function getFiles()
    {
        $files = array();

        foreach ($this->getTenancies() as $tenancy)
        {
            $files = array_merge($files, $tenancy->getFiles());
        }

        return $files;
    }

    /**
     * Add tenancy
     *
     * @param \App\ApiBundle\Entity\Tenancy $tenancy
     *
     * @return Tenant
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
