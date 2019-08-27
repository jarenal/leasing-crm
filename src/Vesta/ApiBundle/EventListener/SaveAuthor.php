<?php
namespace App\ApiBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\ApiBundle\Entity\Contact;
use App\ApiBundle\Entity\Organisation;
use App\ApiBundle\Entity\Property;
use App\ApiBundle\Entity\Ticket;
use App\ApiBundle\Entity\LeaseAgreement;
use App\ApiBundle\Entity\Tenancy;
use App\ApiBundle\Entity\RentBreakdown;
use App\ApiBundle\Entity\TicketComment;

class SaveAuthor
{
    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Contact
                && !$entity instanceof Organisation
                && !$entity instanceof Property
                && !$entity instanceof Ticket
                && !$entity instanceof LeaseAgreement
                && !$entity instanceof Tenancy
                && !$entity instanceof RentBreakdown
                && !$entity instanceof TicketComment){
            return;
        }

        $em = $args->getObjectManager();

        if($this->session->getToken())
        {
            $is_authenticate = $this->session->getToken()->isAuthenticated();
            if($is_authenticate)
            {
                $user = $this->session->getToken()->getUser();

                if($user->getId())
                {
                    $author = $em->find("\App\ApiBundle\Entity\User", $user->getId());
                    if($author)
                        $entity->setCreatedBy($author);
                }
            }
        }

        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Contact
                && !$entity instanceof Organisation
                && !$entity instanceof Property
                && !$entity instanceof Ticket
                && !$entity instanceof LeaseAgreement
                && !$entity instanceof Tenancy
                && !$entity instanceof RentBreakdown
                && !$entity instanceof TicketComment){
            return;
        }

        $em = $args->getObjectManager();

        if($this->session->getToken())
        {
            $is_authenticate = $this->session->getToken()->isAuthenticated();
            if($is_authenticate)
            {
                $user = $this->session->getToken()->getUser();

                if($user->getId())
                {
                    $author = $em->find("\App\ApiBundle\Entity\User", $user->getId());
                    if($author)
                        $entity->setUpdatedBy($author);
                }
            }
        }

        $entity->setUpdatedAt(new \DateTime());

        // If contact method isn't 'Other' then we will clear the field 'contact_method_other'
        if ($entity instanceof Contact)
        {
            if($entity->getContactMethod())
            {
                if($entity->getContactMethod()->getId()!="4")
                    $entity->setContactMethodOther("");
            }
        }

    }
}