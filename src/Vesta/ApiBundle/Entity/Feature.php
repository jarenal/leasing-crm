<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feature
 *
 * @ORM\Table(name="feature")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Feature
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
     * @Assert\NotBlank(message="The name is required.")
     * @JMS\Expose
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="FeatureCategories")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(message="The category is required.")
     * @JMS\Expose
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="Property", mappedBy="features", fetch="EXTRA_LAZY")
     * @JMS\MaxDepth(2)
     */
    private $properties;

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
     * @return Feature
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
     * Set category
     *
     * @param \App\ApiBundle\Entity\FeatureCategories $category
     * @return Feature
     */
    public function setCategory(\App\ApiBundle\Entity\FeatureCategories $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \App\ApiBundle\Entity\FeatureCategories
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->properties = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add property
     *
     * @param \App\ApiBundle\Entity\Property $property
     *
     * @return Feature
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
