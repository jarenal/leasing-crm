<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class Contact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"onlyid","basic"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @JMS\Groups({"onlyid","basic"})
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The name is required.")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     *
     * @JMS\Expose
     * @JMS\Groups({"basic"})
     * @Assert\NotBlank(message="The surname is required.")
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     *
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     strict = false
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="landline", type="string", length=20)
     *
     * @JMS\Expose
     */
    private $landline;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=20)
     *
     * @JMS\Expose
     *
     * @Assert\NotBlank(message="The mobile is required.")
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=10, nullable=true)
     *
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
     * @ORM\ManyToOne(targetEntity="TypeHasStatus")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="type_id", referencedColumnName="type_id"),
     *      @ORM\JoinColumn(name="status_id", referencedColumnName="status_id")
     * })
     *
     * @JMS\Expose
     * @Assert\NotBlank(message="The status is required.")
     **/
    private $typeHasStatus;

    /**
    * @ORM\ManyToOne(targetEntity="ContactTitle")
    * @ORM\JoinColumn(name="contact_title_id", referencedColumnName="id")
    *
    * @JMS\Expose
    * @Assert\NotBlank(message="The contact title is required.")
    */
    private $contact_title;

    /**
    * @ORM\ManyToOne(targetEntity="Organisation", inversedBy="contacts")
    * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $organisation;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $token;

    /**
    * @ORM\ManyToOne(targetEntity="ContactMethod")
    * @ORM\JoinColumn(name="contact_method_id", referencedColumnName="id")
    *
    * @JMS\Expose
    *
    */
    private $contact_method;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_method_other", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $contact_method_other;

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
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $deleted=false;

    /**
    * @ORM\ManyToMany(targetEntity="Ticket", mappedBy="related_contacts", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="tickets_related_contacts",
    *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")}
    *      )
    * @JMS\MaxDepth(2)
    */
    private $related_tickets;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="contact")
     */
    private $user;

    /**
    * @ORM\OneToMany(targetEntity="ContactRiskAssessment", mappedBy="contact")
    * @JMS\MaxDepth(3)
    * @JMS\Expose
    */
    private $risks_assessments;

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
     * @return Contact
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
     * Set surname
     *
     * @param string $surname
     * @return Contact
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
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
     * Set landline
     *
     * @param string $landline
     * @return Contact
     */
    public function setLandline($landline)
    {
        $this->landline = $landline;

        return $this;
    }

    /**
     * Get landline
     *
     * @return string
     */
    public function getLandline()
    {
        return $this->landline;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Contact
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Contact
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
     * Set postcode
     *
     * @param string $postcode
     * @return Contact
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
     * Set contact_title
     *
     * @param \App\ApiBundle\Entity\ContactTitle $contactTitle
     * @return Contact
     */
    public function setContactTitle(\App\ApiBundle\Entity\ContactTitle $contactTitle = null)
    {
        $this->contact_title = $contactTitle;

        return $this;
    }

    /**
     * Get contact_title
     *
     * @return \App\ApiBundle\Entity\ContactTitle
     */
    public function getContactTitle()
    {
        return $this->contact_title;
    }

    /**
     * Set organisation
     *
     * @param \App\ApiBundle\Entity\Organisation $organisation
     * @return Contact
     */
    public function setOrganisation(\App\ApiBundle\Entity\Organisation $organisation = null)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get organisation
     *
     * @return \App\ApiBundle\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set town
     *
     * @param string $town
     * @return Contact
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
     * Set token
     *
     * @param string $token
     * @return Contact
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set typeHasStatus
     *
     * @param \App\ApiBundle\Entity\TypeHasStatus $typeHasStatus
     * @return Contact
     */
    public function setTypeHasStatus(\App\ApiBundle\Entity\TypeHasStatus $typeHasStatus = null)
    {
        $this->typeHasStatus = $typeHasStatus;

        return $this;
    }

    /**
     * Get typeHasStatus
     *
     * @return \App\ApiBundle\Entity\TypeHasStatus
     */
    public function getTypeHasStatus()
    {
        return $this->typeHasStatus;
    }

    /**
     * Get ContactType
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("contact_type")
     * @return string
     */
    public function getContactType()
    {
        if($this->getTypeHasStatus())
            return $this->getTypeHasStatus()->getType();
    }

    /**
     * Get ContactStatus
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("contact_status")
     * @return string
     */
    public function getContactStatus()
    {
        if($this->getTypeHasStatus())
            return $this->getTypeHasStatus()->getStatus();
    }

    /**
     * Get FullName
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("fullname")
     * @JMS\Groups({"basic"})
     * @return string
     */
    public function getFullname()
    {
        return $this->getContactTitle()->getName()." ".$this->getName()." ".$this->getSurname();
    }

    /**
     * Get FullName
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("fullname_with_type")
     * @return string
     */
    public function getFullnameWithType()
    {
        if($this->getTypeHasStatus())
            return $this->getFullname()." (".$this->getTypeHasStatus()->getType()->getName().")";
        else
            return $this->getFullname()." (N/A)";
    }

    /**
     * Set contactMethodOther
     *
     * @param string $contactMethodOther
     *
     * @return Contact
     */
    public function setContactMethodOther($contactMethodOther)
    {
        $this->contact_method_other = $contactMethodOther;

        return $this;
    }

    /**
     * Get contactMethodOther
     *
     * @return string
     */
    public function getContactMethodOther()
    {
        return $this->contact_method_other;
    }

    /**
     * Set contactMethod
     *
     * @param \App\ApiBundle\Entity\ContactMethod $contactMethod
     *
     * @return Contact
     */
    public function setContactMethod(\App\ApiBundle\Entity\ContactMethod $contactMethod = null)
    {
        $this->contact_method = $contactMethod;

        return $this;
    }

    /**
     * Get contactMethod
     *
     * @return \App\ApiBundle\Entity\ContactMethod
     */
    public function getContactMethod()
    {
        return $this->contact_method;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Contact
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
     * Set createdBy
     *
     * @param \App\ApiBundle\Entity\User $createdBy
     *
     * @return Contact
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
    * @ORM\PrePersist()
    */
    public function tasksOnPrePersist(LifecycleEventArgs $args)
    {
        /* TODO For to remove because this was moved to EventListener/SaveAuthor.php

        global $kernel;

        $entity = $args->getObject();
        $em = $entityManager = $args->getObjectManager();

        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $session = $kernel->getContainer()->get('security.token_storage');

        if($session->getToken())
        {
            $is_authenticate = $session->getToken()->isAuthenticated();
            if($is_authenticate)
            {
                $user = $session->getToken()->getUser();

                if($user->getId())
                {
                    $author = $em->find("\App\ApiBundle\Entity\User", $user->getId());
                    if($author)
                        $entity->setCreatedBy($author);
                }
            }
        }
        */

    }

    /**
    * @ORM\PreUpdate()
    */
    public function tasksOnPreUpdate(LifecycleEventArgs $args)
    {
        /* TODO For to remove because this was moved to EventListener/SaveAuthor.php
        global $kernel;

        $entity = $args->getObject();
        $em = $entityManager = $args->getObjectManager();

        if($entity->getContactMethod())
        {
            if($entity->getContactMethod()->getId()!="4")
                $entity->setContactMethodOther("");
        }

        $entity->setUpdatedAt(new \DateTime());

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $session = $kernel->getContainer()->get('security.token_storage');

        if($session->getToken())
        {
            $is_authenticate = $session->getToken()->isAuthenticated();
            if($is_authenticate)
            {
                $user = $session->getToken()->getUser();

                if($user->getId())
                {
                    $author = $em->find("\App\ApiBundle\Entity\User", $user->getId());
                    if($author)
                        $entity->setUpdatedBy($author);
                }
            }
        }*/
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Contact
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
     * @return Contact
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
     * Set updatedBy
     *
     * @param \App\ApiBundle\Entity\User $updatedBy
     *
     * @return Contact
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Contact
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
     * Constructor
     */
    public function __construct()
    {
        $this->related_tickets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add relatedTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $relatedTicket
     *
     * @return Contact
     */
    public function addRelatedTicket(\App\ApiBundle\Entity\Ticket $relatedTicket)
    {
        $this->related_tickets[] = $relatedTicket;

        return $this;
    }

    /**
     * Remove relatedTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $relatedTicket
     */
    public function removeRelatedTicket(\App\ApiBundle\Entity\Ticket $relatedTicket)
    {
        $this->related_tickets->removeElement($relatedTicket);
    }

    /**
     * Get relatedTickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedTickets()
    {
        return $this->related_tickets;
    }

    /**
     * Set user
     *
     * @param \App\ApiBundle\Entity\User $user
     *
     * @return Contact
     */
    public function setUser(\App\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\ApiBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add risksAssessment
     *
     * @param \App\ApiBundle\Entity\ContactRiskAssessment $risksAssessment
     *
     * @return Contact
     */
    public function addRisksAssessment(\App\ApiBundle\Entity\ContactRiskAssessment $risksAssessment)
    {
        $this->risks_assessments[] = $risksAssessment;

        return $this;
    }

    /**
     * Remove risksAssessment
     *
     * @param \App\ApiBundle\Entity\ContactRiskAssessment $risksAssessment
     */
    public function removeRisksAssessment(\App\ApiBundle\Entity\ContactRiskAssessment $risksAssessment)
    {
        $this->risks_assessments->removeElement($risksAssessment);
    }

    /**
     * Get risksAssessments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRisksAssessments()
    {
        return $this->risks_assessments;
    }
}
