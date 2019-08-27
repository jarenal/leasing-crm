<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TenantArea
 *
 * @ORM\Table(name="tenant_area")
 * @ORM\Entity.
 *
 * @JMS\ExclusionPolicy("all")
 */
class TenantArea
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
    * @ORM\ManyToOne(targetEntity="Tenant", inversedBy="areas", cascade={"persist"})
    * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $tenant;

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
     * @return TenantArea
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
     * @return TenantArea
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
     * Set tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     * @return TenantArea
     */
    public function setTenant(\App\ApiBundle\Entity\Tenant $tenant = null)
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return \App\ApiBundle\Entity\Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }
}
