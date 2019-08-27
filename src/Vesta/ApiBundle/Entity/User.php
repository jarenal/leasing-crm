<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\ApiBundle\Entity\UserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class User implements AdvancedUserInterface, \Serializable
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
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @JMS\Expose
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @JMS\Expose
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     * @JMS\Expose
     */
    private $isActive;

    /**
    * @ORM\ManyToMany(targetEntity="Ticket", mappedBy="read_by", fetch="EXTRA_LAZY")
    * @ORM\JoinTable(name="users_read_tickets",
    *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")}
    *      )
    *
    * @JMS\Expose
    * @JMS\MaxDepth(2)
    */
    private $read_tickets;

    /**
     * @ORM\OneToOne(targetEntity="Contact", inversedBy="user")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\MaxDepth(3)
     */
    private $contact;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login_at", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $lastLoginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_updated_at", type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $passwordUpdatedAt;

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    public function eraseCredentials()
    {

    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // $this->salt

        ) = unserialize($serialized);
    }

    /**
    * Checks whether the user's account has expired.
    */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
    * Checks whether the user is locked.
    */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
    * Checks whether the user's credentials (password) has expired.
    */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
    * Checks whether the user is enabled.
    */
    public function isEnabled()
    {
        return $this->isActive;
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
     * @return User
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add readTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $readTicket
     *
     * @return User
     */
    public function addReadTicket(\App\ApiBundle\Entity\Ticket $readTicket)
    {
        $this->read_tickets[] = $readTicket;

        return $this;
    }

    /**
     * Remove readTicket
     *
     * @param \App\ApiBundle\Entity\Ticket $readTicket
     */
    public function removeReadTicket(\App\ApiBundle\Entity\Ticket $readTicket)
    {
        $this->read_tickets->removeElement($readTicket);
    }

    /**
     * Get readTickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReadTickets()
    {
        return $this->read_tickets;
    }

    /**
     * Set contact
     *
     * @param \App\ApiBundle\Entity\Contact $contact
     *
     * @return User
     */
    public function setContact(\App\ApiBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \App\ApiBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set lastLoginAt
     *
     * @param \DateTime $lastLoginAt
     *
     * @return User
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * Get lastLoginAt
     *
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * Set passwordUpdatedAt
     *
     * @param \DateTime $passwordUpdatedAt
     *
     * @return User
     */
    public function setPasswordUpdatedAt($passwordUpdatedAt)
    {
        $this->passwordUpdatedAt = $passwordUpdatedAt;

        return $this;
    }

    /**
     * Get passwordUpdatedAt
     *
     * @return \DateTime
     */
    public function getPasswordUpdatedAt()
    {
        return $this->passwordUpdatedAt;
    }
}
