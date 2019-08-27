<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Other
 *
 * @ORM\Table(name="other")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Other extends Contact
{
    /**
     * @var string
     *
     * @ORM\Column(name="job_title", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     *
     */
    private $jobTitle;

    /**
     * @ORM\ManyToOne(targetEntity="OtherType")
     * @ORM\JoinColumn(name="other_type_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotNull(message="The subtype is required.")
     *
     * @JMS\Expose
     */
    private $other_type;

    /**
     * Get id
     *
     * @return integer
     */

    /**
     * Set jobTitle
     *
     * @param string $jobTitle
     * @return Other
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Get jobTitle
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set other_type
     *
     * @param \App\ApiBundle\Entity\OtherType $otherType
     * @return Other
     */
    public function setOtherType(\App\ApiBundle\Entity\OtherType $otherType = null)
    {
        $this->other_type = $otherType;

        return $this;
    }

    /**
     * Get other_type
     *
     * @return \App\ApiBundle\Entity\OtherType
     */
    public function getOtherType()
    {
        return $this->other_type;
    }
}
