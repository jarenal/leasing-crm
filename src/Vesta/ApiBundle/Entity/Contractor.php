<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contractor
 *
 * @ORM\Table(name="contractor")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Contractor extends Contact
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="require_certification", type="boolean")
     * @JMS\Expose
     */
    private $requireCertification;

    /**
     * @var boolean
     *
     * @ORM\Column(name="liability_insurance", type="boolean")
     * @JMS\Expose
     */
    private $liabilityInsurance;

    /**
    * @ORM\OneToMany(targetEntity="ContractorArea", mappedBy="contractor", orphanRemoval=true, cascade={"persist"})
    *
    * @Assert\Count(
    *      min = "1",
    *      max = "20",
    *      minMessage = "You must specify at least one area",
    *      maxMessage = "You cannot specify more than {{ limit }} areas"
    * )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(1)
    */
    private $areas;

    /**
    * @ORM\ManyToMany(targetEntity="Service", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="contractor_has_service",
    *      joinColumns={@ORM\JoinColumn(name="contractor_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
    *      )
    *
    * @Assert\Count(
    *      min = "1",
    *      max = "15",
    *      minMessage = "You must specify at least one service",
    *      maxMessage = "You cannot specify more than {{ limit }} services"
    * )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $services;

    /**
     * @var string
     *
     * @ORM\Column(name="file_certification", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $file_certification;

    /**
     * @var string
     *
     * @ORM\Column(name="file_insurance", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $file_insurance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
        $this->requireCertification = 0;
        $this->liabilityInsurance = 0;
    }

    /**
     * Set requireCertification
     *
     * @param boolean $requireCertification
     * @return Contractor
     */
    public function setRequireCertification($requireCertification)
    {
        $this->requireCertification = $requireCertification;

        return $this;
    }

    /**
     * Get requireCertification
     *
     * @return boolean
     */
    public function getRequireCertification()
    {
        return $this->requireCertification;
    }

    /**
     * Add areas
     *
     * @param \App\ApiBundle\Entity\ContractorArea $areas
     * @return Contractor
     */
    public function addArea(\App\ApiBundle\Entity\ContractorArea $areas)
    {
        $areas->setContractor($this);
        $this->areas[] = $areas;

        return $this;
    }

    /**
     * Remove areas
     *
     * @param \App\ApiBundle\Entity\ContractorArea $areas
     */
    public function removeArea(\App\ApiBundle\Entity\ContractorArea $areas)
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

    /**
     * Add services
     *
     * @param \App\ApiBundle\Entity\Service $services
     * @return Contractor
     */
    public function addService(\App\ApiBundle\Entity\Service $services)
    {
        $this->services[] = $services;

        return $this;
    }

    /**
     * Remove services
     *
     * @param \App\ApiBundle\Entity\Service $services
     */
    public function removeService(\App\ApiBundle\Entity\Service $services)
    {
        $this->services->removeElement($services);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set liabilityInsurance
     *
     * @param boolean $liabilityInsurance
     * @return Contractor
     */
    public function setLiabilityInsurance($liabilityInsurance)
    {
        $this->liabilityInsurance = $liabilityInsurance;

        return $this;
    }

    /**
     * Get liabilityInsurance
     *
     * @return boolean
     */
    public function getLiabilityInsurance()
    {
        return $this->liabilityInsurance;
    }

    public function getServicesIds()
    {
        $tmp = array();
        foreach ($this->services as $service)
        {
            $tmp[] = $service->getId();
        }

        return $tmp;
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

    /**
     * Set file_certification
     *
     * @param string $fileCertification
     * @return Contractor
     */
    public function setFileCertification($fileCertification)
    {
        $this->file_certification = $fileCertification;

        return $this;
    }

    /**
     * Get file_certification
     *
     * @return string
     */
    public function getFileCertification()
    {
        return $this->file_certification;
    }

    /**
     * Set file_insurance
     *
     * @param string $fileInsurance
     * @return Contractor
     */
    public function setFileInsurance($fileInsurance)
    {
        $this->file_insurance = $fileInsurance;

        return $this;
    }

    /**
     * Get file_insurance
     *
     * @return string
     */
    public function getFileInsurance()
    {
        return $this->file_insurance;
    }

    public function getFiles()
    {
        $files = array();

        $i=1;

        if($this->getFileCertification())
            $files[] = array("id"=>$i++, "name" => $this->getFileCertification(), "path" => "/uploads/contacts/".$this->getToken()."/documents/".$this->getFileCertification());

        if($this->getFileInsurance())
            $files[] = array("id"=>$i++,"name" => $this->getFileInsurance(), "path" => "/uploads/contacts/".$this->getToken()."/documents/".$this->getFileInsurance());

        return $files;
    }
}
