<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactStatus
 *
 * @ORM\Table(name="contact_status")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class ContactStatus
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
    * @ORM\OneToMany(targetEntity="TypeHasStatus", mappedBy="status", orphanRemoval=true, cascade={"persist", "remove"})
    * @JMS\MaxDepth(1)
    */
    private $typeHasStatus;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->typeHasStatus = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ContactStatus
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
     * Add typeHasStatus
     *
     * @param \App\ApiBundle\Entity\TypeHasStatus $typeHasStatus
     * @return ContactStatus
     */
    public function addTypeHasStatus(\App\ApiBundle\Entity\TypeHasStatus $typeHasStatus)
    {
        $this->typeHasStatus[] = $typeHasStatus;

        return $this;
    }

    /**
     * Remove typeHasStatus
     *
     * @param \App\ApiBundle\Entity\TypeHasStatus $typeHasStatus
     */
    public function removeTypeHasStatus(\App\ApiBundle\Entity\TypeHasStatus $typeHasStatus)
    {
        $this->typeHasStatus->removeElement($typeHasStatus);
    }

    /**
     * Get typeHasStatus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypeHasStatus()
    {
        return $this->typeHasStatus;
    }
}
