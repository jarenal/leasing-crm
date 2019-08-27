<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TenantCondition
 *
 * @ORM\Table(name="tenant_condition")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class TenantCondition
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
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The name is required.")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="TenantCondition", mappedBy="parent")
     * @JMS\MaxDepth(1)
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="TenantCondition", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;

    /**
    * @ORM\OneToMany(targetEntity="TenantHasCondition", mappedBy="condition", orphanRemoval=true, cascade={"persist", "remove"})
    * @JMS\MaxDepth(1)
    */
    private $tenantHasCondition;

    /**
     * is other?
     *
     * @var boolean
     *
     * @ORM\Column(name="is_other", type="boolean")
     * @JMS\Expose
     */
    private $is_other;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->is_other = false;
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
     * Set name
     *
     * @param string $name
     * @return TenantCondition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add children
     *
     * @param \App\ApiBundle\Entity\TenantCondition $children
     * @return TenantCondition
     */
    public function addChild(\App\ApiBundle\Entity\TenantCondition $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \App\ApiBundle\Entity\TenantCondition $children
     */
    public function removeChild(\App\ApiBundle\Entity\TenantCondition $children)
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
     * Set parent
     *
     * @param \App\ApiBundle\Entity\TenantCondition $parent
     * @return TenantCondition
     */
    public function setParent(\App\ApiBundle\Entity\TenantCondition $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \App\ApiBundle\Entity\TenantCondition
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add tenantHasCondition
     *
     * @param \App\ApiBundle\Entity\TenantHasCondition $tenantHasCondition
     * @return TenantCondition
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

    /**
     * Set is_other
     *
     * @param boolean $isOther
     * @return TenantCondition
     */
    public function setIsOther($isOther)
    {
        $this->is_other = $isOther;

        return $this;
    }

    /**
     * Get is_other
     *
     * @return boolean 
     */
    public function getIsOther()
    {
        return $this->is_other;
    }
}
