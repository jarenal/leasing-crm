<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Investment
 *
 * @ORM\Table(name="investment")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Investment
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
     * @ORM\Column(name="amount", type="decimal", precision=7, scale=2)
     * @JMS\Expose
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="desired_return", type="decimal", precision=7, scale=2)
     * @JMS\Expose
     */
    private $desiredReturn;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="smallint")
     * @JMS\Expose
     */
    private $distance;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=10)
     * @JMS\Expose
     */
    private $postcode;

    /**
    * @ORM\ManyToOne(targetEntity="Landlord", inversedBy="investments", cascade={"persist"})
    * @ORM\JoinColumn(name="landlord_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $landlord;

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
     * Set amount
     *
     * @param string $amount
     * @return Investment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set desiredReturn
     *
     * @param string $desiredReturn
     * @return Investment
     */
    public function setDesiredReturn($desiredReturn)
    {
        $this->desiredReturn = $desiredReturn;

        return $this;
    }

    /**
     * Get desiredReturn
     *
     * @return string
     */
    public function getDesiredReturn()
    {
        return $this->desiredReturn;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     * @return Investment
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
     * Set postcode
     *
     * @param string $postcode
     * @return Investment
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
     * Set landlord
     *
     * @param \App\ApiBundle\Entity\Landlord $landlord
     * @return Investment
     */
    public function setLandlord(\App\ApiBundle\Entity\Landlord $landlord = null)
    {
        $this->landlord = $landlord;

        return $this;
    }

    /**
     * Get landlord
     *
     * @return \App\ApiBundle\Entity\Landlord
     */
    public function getLandlord()
    {
        return $this->landlord;
    }
}
