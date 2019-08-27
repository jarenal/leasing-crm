<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * BreakdownHasItems
 *
 * @ORM\Table(name="breakdowns_has_items")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class BreakdownHasItems
{
    /**
    * @ORM\Id
    * @ORM\ManyToOne(targetEntity="RentBreakdown", inversedBy="items")
    * @ORM\JoinColumn(name="breakdown_id", referencedColumnName="id")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    * @JMS\Groups({"onlyid"})
    *
    */
    private $breakdown;

    /**
    * @ORM\Id
    * @ORM\ManyToOne(targetEntity="RentBreakdownItem")
    * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
    * @JMS\MaxDepth(2)
    * @JMS\Expose
    * @JMS\Groups({"onlyid"})
    *
    */
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=8, scale=2)
     * @JMS\Expose
     */
    private $amount;

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return BreakdownHasItems
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
     * Set breakdown
     *
     * @param \App\ApiBundle\Entity\RentBreakdown $breakdown
     *
     * @return BreakdownHasItems
     */
    public function setBreakdown(\App\ApiBundle\Entity\RentBreakdown $breakdown)
    {
        $this->breakdown = $breakdown;

        return $this;
    }

    /**
     * Get breakdown
     *
     * @return \App\ApiBundle\Entity\RentBreakdown
     */
    public function getBreakdown()
    {
        return $this->breakdown;
    }

    /**
     * Set item
     *
     * @param \App\ApiBundle\Entity\RentBreakdownItem $item
     *
     * @return BreakdownHasItems
     */
    public function setItem(\App\ApiBundle\Entity\RentBreakdownItem $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \App\ApiBundle\Entity\RentBreakdownItem
     */
    public function getItem()
    {
        return $this->item;
    }
}
