<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeHasStatus
 *
 * @ORM\Table(name="type_has_status")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class TypeHasStatus
{
    /**
    * @ORM\ManyToOne(targetEntity="ContactType", inversedBy="typeHasStatus")
    * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
    * @ORM\Id
    * @JMS\Expose
    */
    private $type;

    /**
    * @ORM\ManyToOne(targetEntity="ContactStatus", inversedBy="typeHasStatus")
    * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
    * @ORM\Id
    * @JMS\Expose
    */
    private $status;


    /**
     * Set type
     *
     * @param \App\ApiBundle\Entity\ContactType $type
     * @return TypeHasStatus
     */
    public function setType(\App\ApiBundle\Entity\ContactType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \App\ApiBundle\Entity\ContactType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param \App\ApiBundle\Entity\ContactStatus $status
     * @return TypeHasStatus
     */
    public function setStatus(\App\ApiBundle\Entity\ContactStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \App\ApiBundle\Entity\ContactStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}
