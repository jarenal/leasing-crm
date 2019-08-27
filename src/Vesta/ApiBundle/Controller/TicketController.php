<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Ticket;
use App\ApiBundle\Entity\TicketComment;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;
use Doctrine\ORM\Query as Query;
use FOS\RestBundle\Controller\Annotations\Get;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class TicketController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT t FROM AppApiBundle:Ticket t LEFT JOIN t.ticket_type tt WHERE (t.title LIKE :name OR t.id LIKE :name OR tt.name LIKE :name) AND t.deleted=0");
            $query->setParameter('name', "%$term%");
            $tickets = $query->getResult();

            //$fb = $this->get('my_fire_php');
            foreach ($tickets as $ticket) {
                $response[] = array('id'      => $ticket->getId(),
                                    'value'   => $ticket->getFulltitle(),
                                    );
            }

            $response[] = array("id"=>"", "value"=>"N/A");

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function cgetAction(Request $request)
    {
        try {
            $logger = $this->get('logger');
            $logger->info('Probando MONOLOG!!!');
            //$logger->error('Este es un mensaje de error desde el LOG!!!');

            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus FROM AppApiBundle:Ticket t LEFT JOIN t.status s WHERE t.deleted=0 ORDER BY t.id DESC");
            $tickets = $query->getResult();
            $response['data'] = $tickets;
        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/user/{user}")
     */
    public function cgetByUserAction($user, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus FROM AppApiBundle:Ticket t LEFT JOIN t.status s INNER JOIN t.assign_to o INNER JOIN o.user u WHERE t.deleted=0 AND u.id=:user AND s.id NOT IN (3,4) ORDER BY t.duedateAt ASC");
            $query->setParameter("user", $user);

            $tickets = $query->getResult();
            $response['data'] = $tickets;
        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/landlord/{landlord}")
     */
    public function cgetByLandlordAction($landlord, Request $request)
    {
        /* TODO show Landlord's tenants tasks too? */
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus
                                        FROM AppApiBundle:Ticket t
                                        LEFT JOIN t.status s
                                        LEFT JOIN t.related_contacts rc
                                        LEFT JOIN t.related_properties rp
                                        LEFT JOIN AppApiBundle:Tenant ten WITH rc.id=ten.id
                                        WHERE t.deleted=0 AND (rc.id=:landlord OR rp.id IN (SELECT p2.id FROM AppApiBundle:Property p2 WHERE p2.landlord=:landlord))
                                        GROUP BY t.id
                                        ORDER BY t.id DESC");
            $query->setParameter("landlord", $landlord);
            $tickets = $query->getResult();

            $response['data'] = $tickets;

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);

        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/tenant/{tenant}")
     */
    public function cgetByTenantAction($tenant, Request $request)
    {
        /* TODO show tenant's landlord and property tasks too? */
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus
                                        FROM AppApiBundle:Ticket t
                                        LEFT JOIN t.status s
                                        LEFT JOIN t.related_contacts rc
                                        LEFT JOIN t.related_properties rp
                                        WHERE t.deleted=0 AND rc.id=:tenant
                                        GROUP BY t.id
                                        ORDER BY t.id DESC");
            $query->setParameter("tenant", $tenant);
            $tickets = $query->getResult();

            $response['data'] = $tickets;

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/contractor/{contractor}")
     */
    public function cgetByContractorAction($contractor, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus
                                        FROM AppApiBundle:Ticket t
                                        LEFT JOIN t.status s
                                        LEFT JOIN t.related_contacts rc
                                        LEFT JOIN t.related_properties rp
                                        WHERE t.deleted=0 AND (rc.id=:contractor)
                                        GROUP BY t.id
                                        ORDER BY t.id DESC");
            $query->setParameter("contractor", $contractor);
            $tickets = $query->getResult();

            $response['data'] = $tickets;

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/property/{property}")
     */
    public function cgetByPropertyAction($property, Request $request)
    {
        /* TODO show Property's tenants tasks too? */
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus
                                        FROM AppApiBundle:Ticket t
                                        LEFT JOIN t.status s
                                        LEFT JOIN t.related_contacts rc
                                        LEFT JOIN t.related_properties rp
                                        WHERE t.deleted=0 AND (rp.id=:property OR rc.id IN (SELECT IDENTITY(p3.landlord) FROM AppApiBundle:Property p3 WHERE p3.id=:property))
                                        GROUP BY t.id
                                        ORDER BY t.id DESC");
            $query->setParameter("property", $property);
            $tickets = $query->getResult();

            $response['data'] = $tickets;

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/tickets/by/other/{other}")
     */
    public function cgetByOtherAction($other, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t.id, t.title, t.description, t.createdAt, t.updatedAt, t.duedateAt, t.createdAt, t.updatedAt, s.name status_name, s.id idstatus
                                        FROM AppApiBundle:Ticket t
                                        LEFT JOIN t.status s
                                        LEFT JOIN t.related_contacts rc
                                        LEFT JOIN t.related_properties rp
                                        WHERE t.deleted=0 AND (rc.id=:other)
                                        GROUP BY t.id
                                        ORDER BY t.id DESC");
            $query->setParameter("other", $other);
            $tickets = $query->getResult();

            $response['data'] = $tickets;

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function getAction($id, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($id=="%s") {
                throw new HttpException(100, "The id is required");
            }

            $query = $em->createQuery("SELECT t FROM AppApiBundle:Ticket t WHERE t.id=:id AND t.deleted=0");
            $query->setParameter("id", $id);

            $ticket = $query->getOneOrNullResult();

            if (!$ticket) {
                throw new HttpException(100, "The task \"$id\" doesn't exist.");
            }

            $response['data'] = $ticket;

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('ticket', array());

            $this->proccessData($post, $response, null);

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->setGroups(array('onlyid'))
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function putAction($idticket, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('ticket', array());

            if (!$idticket) {
                throw new HttpException(100, "The idticket is required");
            }

            $this->proccessData($post, $response, $idticket);
        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function deleteAction($idticket, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idticket=="%s") {
                throw new HttpException(100, "The idticket is required");
            }

            $ticket = $em->find("\App\ApiBundle\Entity\Ticket", $idticket);

            if (!$ticket) {
                throw new HttpException(100, "The task \"$idticket\" doesn't exist.");
            }

            $ticket->setDeleted(true);
            $em->persist($ticket);
            $em->flush();
        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    private function proccessData($post, &$response, $idticket = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(
                function ($em) use ($post, &$response, $idticket) {

                    $validator = $this->get('validator');

                    if ($idticket) {
                        $ticket = $em->find("\App\ApiBundle\Entity\Ticket", $idticket);
                        if (!$ticket) {
                            throw new HttpException(100, "The task with id \"$idticket\" doesn't exist.");
                        }
                    } else {
                        $ticket = new Ticket();
                        $em->persist($ticket);
                    }

                    // Status
                    if (isset($post['status']) && $post['status']) {
                        $status = $em->find("\App\ApiBundle\Entity\TicketStatus", $post['status']);
                        if ($status) {
                            $ticket->setStatus($status);
                        }
                    } else {
                        $ticket->setStatus(null);
                    }

                    // Title
                    if (isset($post['title'])) {
                        $ticket->setTitle($post['title']);
                    }
                    else {
                        $ticket->setTitle(null);
                    }

                    if (isset($post['description'])) {
                        $ticket->setDescription($post['description']);
                    }
                    else {
                        $ticket->setDescription("");
                    }

                    if (isset($post['action_needed'])) {
                        $ticket->setActionNeeded($post['action_needed']);
                    }
                    else {
                        $ticket->setActionNeeded("");
                    }

                    if (isset($post['date_reported'])) {
                        $ticket->setDateReported($post['date_reported']);
                    }
                    else {
                        $ticket->setDateReported(null);
                    }

                    if (isset($post['duedate_at'])) {
                        $ticket->setDuedateAt($post['duedate_at']);
                    }
                    else {
                        $ticket->setDuedateAt(null);
                    }

                    // reported_by
                    if (isset($post['reported_by']) && $post['reported_by']) {
                        $contact = $em->find("\App\ApiBundle\Entity\Contact", $post['reported_by']);
                        if ($contact) {
                            $ticket->setReportedBy($contact);
                        }
                        else {
                            throw new HttpException(300, "The contact '{$post['reported_by']}' doesn't exist.");
                        }
                    } else {
                        $ticket->setReportedBy(null);
                    }

                    // assign_to
                    if (isset($post['assign_to']) && $post['assign_to']) {
                        $other = $em->find("\App\ApiBundle\Entity\Other", $post['assign_to']);
                        if ($other) {
                            $ticket->setAssignTo($other);
                        }
                        else {
                            throw new HttpException(300, "The contact '{$post['assign_to']}' doesn't exist.");
                        }
                    } else {
                        $ticket->setAssignTo(null);
                    }

                    // ticket_type
                    if (isset($post['ticket_type']) && $post['ticket_type']) {
                        $ticket_type = $em->find("\App\ApiBundle\Entity\TicketType", $post['ticket_type']);
                        if ($ticket_type) {
                            $ticket->setTicketType($ticket_type);
                        }
                        else {
                            throw new HttpException(300, "The task '{$post['ticket_type']}' doesn't exist.");
                        }
                    } else {
                        $ticket->setTicketType(null);
                    }

                    // parent
                    if (isset($post['parent']) && $post['parent']) {
                        $parent = $em->find("\App\ApiBundle\Entity\Ticket", $post['parent']);
                        if ($parent) {
                            $ticket->setParent($parent);
                        }
                        else {
                            throw new HttpException(300, "The parent task '{$post['parent']}' doesn't exist.");
                        }
                    } else {
                        $ticket->setParent(null);
                    }

                    // time_spent
                    if (isset($post['time_spent']) && $post['time_spent']) {
                        $ticket->setTimeSpent($post['time_spent']);
                        $ticket->setTimeSpentUnit($post['time_spent_unit']);
                    }

                    /* related_contacts */
                    if(!isset($post['related_contacts']) || !is_array($post['related_contacts']) || !$post['related_contacts'])
                        $post['related_contacts'] = array();

                    $this->processRelatedContacts($em, $ticket, $idticket, $post['related_contacts']);

                    /* related_properties */
                    if(!isset($post['related_properties']) || !is_array($post['related_properties']) || !$post['related_properties'])
                        $post['related_properties'] = array();

                    $this->processRelatedProperties($em, $ticket, $idticket, $post['related_properties']);

                    /* check validation */
                    $errors = $validator->validate($ticket);

                    if ($errors->count()) {
                        foreach ($errors as $error) {
                            $response['errors'][] = array($error->getPropertyPath() => $error->getMessage());
                        }
                        throw new HttpException(100, "validation");
                    }

                    // save
                    $em->flush();
                    $em->persist($ticket);
                    $response['data'] = $ticket; // return the id created
                }
            );
        } catch (HttpException $ex) {
            throw $ex;
        }
    }

    private function processRelatedContacts($em, $entity, $id, $related_contacts)
    {
        try
        {
            $current_items = array();
            $new_items = array();
            $deleted_items = array();

            // get current contacts
            if($id)
            {
                $current_items = $entity->getRelatedContactsIds();
            }

            // get deleted items
            $deleted_items = array_diff($current_items, $related_contacts);

            // get new items
            $new_items = array_diff($related_contacts, $current_items);

            // add new items
            foreach ($new_items as $item)
            {
                $new_item = $em->find("\App\ApiBundle\Entity\Contact", $item);
                if(!$new_item)
                    throw new HttpException(100, "The contact with id \"{$item}\" doesn't exist.");

                $entity->addRelatedContact($new_item);
                unset($new_item);
            }

            // delete items
            foreach ($deleted_items as $item)
            {
                $old_item = $em->find("\App\ApiBundle\Entity\Contact", $item);
                if(!$old_item)
                    throw new HttpException(100, "The contact with id \"{$item}\" doesn't exist.");

                $entity->removeRelatedContact($old_item);
                unset($old_item);
            }
        }
        catch(HttpException $ex)
        {
            throw $ex;
        }
    }

    private function processRelatedProperties($em, $entity, $id, $related_properties)
    {
        try
        {
            $current_items = array();
            $new_items = array();
            $deleted_items = array();

            // get current contacts
            if($id)
            {
                $current_items = $entity->getRelatedPropertiesIds();
            }

            // get deleted items
            $deleted_items = array_diff($current_items, $related_properties);

            // get new items
            $new_items = array_diff($related_properties, $current_items);

            // add new items
            foreach ($new_items as $item)
            {
                $new_item = $em->find("\App\ApiBundle\Entity\Property", $item);
                if(!$new_item)
                    throw new HttpException(100, "The property with id \"{$item}\" doesn't exist.");

                $entity->addRelatedProperty($new_item);
                unset($new_item);
            }

            // delete items
            foreach ($deleted_items as $item)
            {
                $old_item = $em->find("\App\ApiBundle\Entity\Property", $item);
                if(!$old_item)
                    throw new HttpException(100, "The property with id \"{$item}\" doesn't exist.");

                $entity->removeRelatedProperty($old_item);
                unset($old_item);
            }
        }
        catch(HttpException $ex)
        {
            throw $ex;
        }
    }

    public function postCommentAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('comment', array());

            $em = $this->getDoctrine()->getManager();

            $em->transactional(
                function ($em) use ($post, &$response) {

                    $validator = $this->get('validator');

                    $comment = new TicketComment();

                    if(isset($post['ticket'])) {
                        $ticket = $em->find("\App\ApiBundle\Entity\Ticket", $post['ticket']);
                        if (!$ticket) {
                            throw new HttpException(100, "The task with id \"$idticket\" doesn't exist.");
                        }
                        $comment->setTicket($ticket);
                    }
                    else {
                        $comment->setTicket(null);
                    }

                    if(isset($post['update_date'])) {
                        $comment->setUpdateDate($post['update_date']);
                    }
                    else {
                        $comment->setUpdateDate(null);
                    }

                    if (isset($post['description'])) {
                        $comment->setDescription($post['description']);
                    }
                    else {
                        $comment->setDescription("");
                    }

                    if (isset($post['action_needed'])) {
                        $comment->setActionNeeded($post['action_needed']);
                    }
                    else {
                        $comment->setActionNeeded("");
                    }

                    // time_spent
                    if (isset($post['time_spent']) && $post['time_spent']) {
                        $comment->setTimeSpent($post['time_spent']);
                        $comment->setTimeSpentUnit($post['time_spent_unit']);
                    }

                    /* check validation */
                    $errors = $validator->validate($comment);

                    if ($errors->count()) {
                        foreach ($errors as $error) {
                            $response['errors'][] = array($error->getPropertyPath() => $error->getMessage());
                        }
                        throw new HttpException(100, "validation");
                    }

                    // save
                    $em->flush();
                    $em->persist($comment);
                    $response['data'] = $comment; // return the id created
                }
            );

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }
}
