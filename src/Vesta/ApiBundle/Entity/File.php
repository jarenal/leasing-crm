<?php

namespace App\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("token",message="Token can't be duplicated.")
 * @ORM\HasLifecycleCallbacks
 */
class File
{
    /**
     * @var bigint
     *
     * @ORM\Column(name="id", type="bigint", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     * @JMS\Expose
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, unique=true, nullable=false)
     * @JMS\Expose
     */
    private $token;

    /**
    * @ORM\ManyToOne(targetEntity="Property", inversedBy="files", cascade={"persist"})
    * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
    */
    private $property;

    /**
    * @ORM\Column(type="file_type")
    */
    private $type;

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
     *
     * @return File
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
     * Set type
     *
     * @param file_type $type
     *
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return file_type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set property
     *
     * @param \App\ApiBundle\Entity\Property $property
     *
     * @return File
     */
    public function setProperty(\App\ApiBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \App\ApiBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
    * @ORM\PrePersist()
    */
    public function generateTokenOnPrePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entity->setToken(md5($entity->getPath().time()));
    }

    /**
    * @ORM\PostRemove()
    */
    public function deleteFileOnPostRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $full_path = $_SERVER['DOCUMENT_ROOT'].$entity->getPath();
        if(file_exists($full_path))
            unlink($full_path);
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return File
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
}
