<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Contact;
use App\ApiBundle\Entity\Investment;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\ApiBundle\Entity\ContactTitle;
use App\ApiBundle\Entity\ContactStatus;
use JMS\Serializer\SerializationContext;
use App\ApiBundle\Entity\ContactRiskAssessment;
use FOS\RestBundle\Controller\Annotations\Get;

class ContactController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT con.id id,
                                              con.name name,
                                              con.surname surname,
                                              con.email email,
                                              con.landline landline,
                                              con.mobile mobile,
                                              con.address address,
                                              con.postcode postcode,
                                              con.town town,
                                              cst.name status,
                                              cty.name contact_type,
                                              cti.name contact_title,
                                              org.name organisation,
                                              cty.id contact_type_id,
                                              CONCAT(cti.name,' ',con.name,' ',con.surname) fullname
                                    FROM AppApiBundle:Contact con
                                    LEFT JOIN con.typeHasStatus ths
                                    LEFT JOIN con.contact_title cti
                                    LEFT JOIN con.organisation org
                                    LEFT JOIN ths.type cty
                                    LEFT JOIN ths.status cst
                                    WHERE con.deleted=0");
            $contacts = $query->getResult();
            $response['data'] = $contacts;
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

    public function cgetByOrganisationAction($organisation, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT con.id id,
                                              con.name name,
                                              con.surname surname,
                                              con.email email,
                                              con.landline landline,
                                              con.mobile mobile,
                                              con.address address,
                                              con.postcode postcode,
                                              con.town town,
                                              cst.name status,
                                              cty.name contact_type,
                                              cti.name contact_title,
                                              org.name organisation,
                                              cty.id contact_type_id,
                                              CONCAT(cti.name,' ',con.name,' ',con.surname) fullname
                                    FROM AppApiBundle:Contact con
                                    LEFT JOIN con.typeHasStatus ths
                                    LEFT JOIN con.contact_title cti
                                    LEFT JOIN con.organisation org
                                    LEFT JOIN ths.type cty
                                    LEFT JOIN ths.status cst
                                    WHERE con.deleted=0 AND con.organisation=:organisation");
            $query->setParameter('organisation', $organisation);
            $contacts = $query->getResult();
            $response['data'] = $contacts;
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

    public function cgetTitlesAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT l FROM AppApiBundle:ContactTitle l");
            $titles = $query->getResult();
            $response['data'] = $titles;
        } catch (\Exception $ex) {
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

    public function cgetStatusesAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT s FROM AppApiBundle:ContactStatus s");
            $status = $query->getResult();
            $response['data'] = $status;
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

    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT c FROM AppApiBundle:Contact c INNER JOIN c.typeHasStatus ths INNER JOIN ths.type ct WHERE (c.name LIKE :name OR c.surname LIKE :name OR ct.name LIKE :name) AND c.deleted=0");
            $query->setParameter('name', "%$term%");
            $contacts = $query->getResult();

            //$fb = $this->get('my_fire_php');
            foreach ($contacts as $contact) {

                //$fb->log($contact,"contact");
                $response[] = array('id'      => $contact->getId(),
                                    'value'   => $contact->getFullnameWithType(),
                                    );
            }

            $response[] = array("id"=>"", "value"=>"N/A");

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        $view->setFormat("json");

        return $this->handleView($view);
    }

    public function cgetRelatedContactsComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT c FROM AppApiBundle:Contact c INNER JOIN c.typeHasStatus ths INNER JOIN ths.type ct WHERE (c.name LIKE :name OR c.surname LIKE :name OR ct.name LIKE :name) AND c.deleted=0 AND ths.type IN (1,2,3) ORDER BY c.name ASC, c.surname ASC");
            $query->setParameter('name', "%$term%");
            $contacts = $query->getResult();

            //$fb = $this->get('my_fire_php');
            foreach ($contacts as $contact) {

                //$fb->log($contact,"contact");
                $response[] = array('id'      => $contact->getId(),
                                    'value'   => $contact->getFullnameWithType(),
                                    );
            }

            $response[] = array("id"=>"", "value"=>"N/A");

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        $view->setFormat("json");

        return $this->handleView($view);
    }

    public function postRiskAssessmentAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('risk_assessment', array());

            $em = $this->getDoctrine()->getManager();

            $em->transactional(
                function ($em) use ($post, &$response) {
                    //$fb = $this->get('my_fire_php');
                    $validator = $this->get('validator');

                    $risk_assessment = $em->find("\App\ApiBundle\Entity\ContactRiskAssessment", array("contact"=>$post['contact'], "question"=>$post["question"]));

                    if(!$risk_assessment)
                    {
                        $risk_assessment = new ContactRiskAssessment();

                        if (isset($post['contact']) && $post['contact'])
                        {
                            $contact = $em->find("\App\ApiBundle\Entity\Contact", $post['contact']);
                            if ($contact)
                                $risk_assessment->setContact($contact);
                            else
                                throw new HttpException("The contact '{$post['contact']}' doesn't exist.", 100);
                        }

                        if (isset($post['question']) && $post['question'])
                        {
                            $question = $em->find("\App\ApiBundle\Entity\RiskAssessmentQuestion", $post['question']);
                            if ($question)
                                $risk_assessment->setQuestion($question);
                            else
                                throw new HttpException("The question '{$post['question']}' doesn't exist.", 100);
                        }

                        $em->persist($risk_assessment);
                    }

                    if(isset($post['answer']))
                        $risk_assessment->setAnswer($post['answer']);
                    else
                        $risk_assessment->setAnswer(null);

                    if(isset($post['comments']))
                        $risk_assessment->setComments($post['comments']);
                    else
                        $risk_assessment->setComments(null);

                    if(isset($post['level_of_risk']) && $post['level_of_risk'])
                        $risk_assessment->setLevelOfRisk($post['level_of_risk']);

                    if(isset($post['action_needed']))
                        $risk_assessment->setActionNeeded($post['action_needed']);
                    else
                        $risk_assessment->setActionNeeded(null);

                    if (isset($post['associated_task']) && $post['associated_task'])
                    {
                        $ticket = $em->find("\App\ApiBundle\Entity\Ticket", $post['associated_task']);
                        if ($ticket)
                            $risk_assessment->setTicket($ticket);
                        else
                            $risk_assessment->setTicket(null);
                    }
                    else
                    {
                        $risk_assessment->setTicket(null);
                    }

                    /* check validation */
                    $errors = $validator->validate($risk_assessment);

                    if ($errors->count()) {
                        foreach ($errors as $error) {
                            $response['errors'][] = array($error->getPropertyPath() => $error->getMessage());
                        }
                        throw new HttpException(100, "validation");
                    }

                    // save
                    $em->flush();
                    $em->persist($risk_assessment);
                    $response['data'] = $risk_assessment;
                }
            );

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

    /**
     * @Get("/contacts/by/email/{email}")
     */
    public function getByEmailAction($email, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $query = $em->createQuery("SELECT c FROM AppApiBundle:Contact c WHERE c.email=:email AND c.deleted=0");
            $query->setParameter("email", $email);

            $contact = $query->getOneOrNullResult();

            $response['data']['contact'] = $contact;

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->setGroups(array('basic'))
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Get("/contacts/by/fullname/{fullname}")
     */
    public function getByFullnameAction($fullname, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);


            $query = $em->createQuery("SELECT c FROM AppApiBundle:Contact c HAVING CONCAT(c.name, ' ', c.surname)=:fullname AND c.deleted=0");
            $query->setParameter("fullname", $fullname);

            $contact = $query->getOneOrNullResult();

            $response['data']['contact'] = $contact;

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->setGroups(array('basic'))
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }
}
