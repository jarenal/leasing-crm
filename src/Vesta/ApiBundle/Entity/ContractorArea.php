<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContractorArea
 *
 * @ORM\Table(name="contractor_area")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class ContractorArea
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
     * @ORM\Column(name="postcode", type="string", length=10)
     * @JMS\Expose
     * @Assert\NotBlank(message="The postcode is required.")
     */
    private $postcode;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="smallint")
     * @JMS\Expose
     * @Assert\NotBlank(message="The distance is required.")
     */
    private $distance;

    /**
    * @ORM\ManyToOne(targetEntity="Contractor", inversedBy="areas", cascade={"persist"})
    * @ORM\JoinColumn(name="contractor_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $contractor;

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
     * Set postcode
     *
     * @param string $postcode
     * @return ContractorArea
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
     * Set distance
     *
     * @param integer $distance
     * @return ContractorArea
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set contractor
     *
     * @param \App\ApiBundle\Entity\Contractor $contractor
     * @return ContractorArea
     */
    public function setContractor(\App\ApiBundle\Entity\Contractor $contractor = null)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return \App\ApiBundle\Entity\Contractor
     */
    public function getContractor()
    {
        return $this->contractor;
    }
}
