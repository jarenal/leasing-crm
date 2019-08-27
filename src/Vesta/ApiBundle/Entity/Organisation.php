<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Organisation
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Organisation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"onlyid"})
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
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank(message="The phone is required.")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     * @JMS\Expose
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=10, nullable=true)
     * @JMS\Expose
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $town;

    /**
    * @ORM\OneToMany(targetEntity="Contact", mappedBy="organisation")
    * @JMS\MaxDepth(1)
    */
    private $contacts;

    /**
     * @ORM\ManyToOne(targetEntity="OrganisationType")
     * @ORM\JoinColumn(name="organisation_type_id", referencedColumnName="id")
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The organisation type is required.")
     */
    private $organisation_type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $deleted=false;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     * @JMS\Expose
     */
    private $comments;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $created_by;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
    * @JMS\Expose
    */
    private $updated_by;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Expose
     */
    private $created_at;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @JMS\Expose
     */
    private $updated_at;

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
     * @return Organisation
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
     * Set phone
     *
     * @param string $phone
     * @return Organisation
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Organisation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Organisation
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Organisation
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add contacts
     *
     * @param \App\ApiBundle\Entity\Contact $contacts
     * @return Organisation
     */
    public function addContact(\App\ApiBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \App\ApiBundle\Entity\Contact $contacts
     */
    public function removeContact(\App\ApiBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return Organisation
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
     * Set town
     *
     * @param string $town
     * @return Organisation
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set organisation_type
     *
     * @param \App\ApiBundle\Entity\OrganisationType $organisationType
     * @return Organisation
     */
    public function setOrganisationType(\App\ApiBundle\Entity\OrganisationType $organisationType = null)
    {
        $this->organisation_type = $organisationType;

        return $this;
    }

    /**
     * Get organisation_type
     *
     * @return \App\ApiBundle\Entity\OrganisationType
     */
    public function getOrganisationType()
    {
        return $this->organisation_type;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Organisation
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Organisation
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Organisation
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Organisation
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return Organisation
     */
    public function setCreatedBy(\App\ApiBundle\Entity\User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set updatedBy
     *
     * @param \App\ApiBundle\Entity\User $updatedBy
     *
     * @return Organisation
     */
    public function setUpdatedBy(\App\ApiBundle\Entity\User $updatedBy = null)
    {
        $this->updated_by = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("fullname_with_type")
     * @return string
     */
    public function getFullnameWithType()
    {
        return $this->getName()." (".$this->getOrganisationType()->getName().")";
    }
}
