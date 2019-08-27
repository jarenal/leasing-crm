<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Landlord
 *
 * @ORM\Table(name="landlord")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Landlord extends Contact
{
    /**
     * @var string
     *
     * @ORM\Column(name="accreditation_references", type="text")
     *
     * @JMS\Expose
     *
     */
    private $accreditationReferences;

    /**
     * @ORM\ManyToOne(targetEntity="LandlordAccreditation")
     * @ORM\JoinColumn(name="landlord_accreditation_id", referencedColumnName="id")
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The accreditation is required.")
     */
    private $landlord_accreditation;

    /**
     * @ORM\OneToMany(targetEntity="Investment", mappedBy="landlord", orphanRemoval=true, cascade={"persist"})
     * @JMS\Expose
     * @JMS\MaxDepth(1)
     */
    private $investments;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_investor", type="boolean")
     * @JMS\Expose
     */
    private $isInvestor;

    /**
     * @ORM\OneToMany(targetEntity="Property", mappedBy="landlord")
     * @JMS\MaxDepth(1)
     */
    private $properties;

    public function __construct()
    {
        $this->investments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isInvestor = 0;
    }

    /**
     * Set accreditationReferences
     *
     * @param string $accreditationReferences
     * @return Landlord
     */
    public function setAccreditationReferences($accreditationReferences)
    {
        $this->accreditationReferences = $accreditationReferences;

        return $this;
    }

    /**
     * Get accreditationReferences
     *
     * @return string
     */
    public function getAccreditationReferences()
    {
        return $this->accreditationReferences;
    }

    /**
     * Set landlord_accreditation
     *
     * @param \App\ApiBundle\Entity\LandlordAccreditation $landlordAccreditation
     * @return Landlord
     */
    public function setLandlordAccreditation(\App\ApiBundle\Entity\LandlordAccreditation $landlordAccreditation = null)
    {
        $this->landlord_accreditation = $landlordAccreditation;

        return $this;
    }

    /**
     * Get landlord_accreditation
     *
     * @return \App\ApiBundle\Entity\LandlordAccreditation
     */
    public function getLandlordAccreditation()
    {
        return $this->landlord_accreditation;
    }

    /**
     * Add investments
     *
     * @param \App\ApiBundle\Entity\Investment $investments
     * @return Landlord
     */
    public function addInvestment(\App\ApiBundle\Entity\Investment $investments)
    {
        $investments->setLandlord($this);
        $this->investments[] = $investments;

        return $this;
    }

    /**
     * Remove investments
     *
     * @param \App\ApiBundle\Entity\Investment $investments
     */
    public function removeInvestment(\App\ApiBundle\Entity\Investment $investments)
    {
        $this->investments->removeElement($investments);
    }

    /**
     * Get investments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvestments()
    {
        return $this->investments;
    }

    public function getInvestmentsIds()
    {
        $tmp = array();
        foreach ($this->investments as $investment)
        {
            $tmp[] = $investment->getId();
        }

        return $tmp;
    }

    /**
     * Set isInvestor
     *
     * @param boolean $isInvestor
     * @return Landlord
     */
    public function setIsInvestor($isInvestor)
    {
        $this->isInvestor = $isInvestor;

        return $this;
    }

    /**
     * Get isInvestor
     *
     * @return boolean
     */
    public function getIsInvestor()
    {
        return $this->isInvestor;
    }

    public function getFiles()
    {
        $files = array();

        foreach ($this->getProperties() as $property) 
        {
            foreach ($property->getLeaseAgreements() as $lease_agreement) 
            {
                $files = array_merge($files, $lease_agreement->getFiles());
            }
        }

        return $files;
    }

    /**
     * Add property
     *
     * @param \App\ApiBundle\Entity\Property $property
     *
     * @return Landlord
     */
    public function addProperty(\App\ApiBundle\Entity\Property $property)
    {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \App\ApiBundle\Entity\Property $property
     */
    public function removeProperty(\App\ApiBundle\Entity\Property $property)
    {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
