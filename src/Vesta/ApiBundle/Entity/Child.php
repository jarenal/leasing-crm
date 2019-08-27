<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
//JMS\Serializer\Annotation\ExclusionPolicy, // Allow hide all the fields.
//JMS\Serializer\Annotation\Expose, // Allow show or hide fields when export to JSON.
//JMS\Serializer\Annotation\Type, // Allow format dates
use Symfony\Component\Validator\Constraints as Assert; // Allow required fields

/**
 * Child
 *
 * @ORM\Table(name="child")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Child
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
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $birthdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="guardianship", type="boolean")
     * @JMS\Expose
     */
    private $guardianship;

    /**
    * @ORM\ManyToOne(targetEntity="Tenant", inversedBy="children", cascade={"persist"})
    * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $tenant;

    public function __construct()
    {
        $this->guardianship = 0;
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
     * @return Child
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
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return Child
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
     * Set guardianship
     *
     * @param boolean $guardianship
     * @return Child
     */
    public function setGuardianship($guardianship)
    {
        $this->guardianship = $guardianship;

        return $this;
    }

    /**
     * Get guardianship
     *
     * @return boolean
     */
    public function getGuardianship()
    {
        return $this->guardianship;
    }

    /**
     * Set tenant
     *
     * @param \App\ApiBundle\Entity\Tenant $tenant
     * @return Child
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
