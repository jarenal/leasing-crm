<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TenantHasCondition
 *
 * @ORM\Table(name="tenant_has_condition")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class TenantHasCondition
{
    /**
     * @var string
     *
     * @ORM\Column(name="other", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $other;

    /**
    * @ORM\ManyToOne(targetEntity="Tenant", inversedBy="tenantHasCondition")
    * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id")
    * @ORM\Id
    * @JMS\Expose
    */
    private $tenant;

    /**
    * @ORM\ManyToOne(targetEntity="TenantCondition", inversedBy="tenantHasCondition")
    * @ORM\JoinColumn(name="condition_id", referencedColumnName="id")
    * @ORM\Id
    * @JMS\Expose
    */
    private $condition;

    /**
     * Set tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     * @return TenantHasCondition
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

    /**
     * Set condition
     *
     * @param \App\ApiBundle\Entity\TenantCondition $condition
     * @return TenantHasCondition
     */
    public function setCondition(\App\ApiBundle\Entity\TenantCondition $condition = null)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition
     *
     * @return \App\ApiBundle\Entity\TenantCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set other
     *
     * @param string $other
     * @return TenantHasCondition
     */
    public function setOther($other)
    {
        $this->other = $other;

        return $this;
    }

    /**
     * Get other
     *
     * @return string 
     */
    public function getOther()
    {
        return $this->other;
    }
}
