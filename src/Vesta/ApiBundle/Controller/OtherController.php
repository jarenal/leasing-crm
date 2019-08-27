<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Other;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class OtherController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT oth FROM AppApiBundle:Other oth WHERE (oth.name LIKE :name OR oth.surname LIKE :name) AND oth.deleted=0");
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
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function cgetAndLandlordsComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT oth FROM AppApiBundle:Contact oth WHERE (oth.name LIKE :name OR oth.surname LIKE :name) AND oth.deleted=0 AND (oth INSTANCE OF AppApiBundle:Landlord OR oth INSTANCE OF AppApiBundle:Other)");
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

            $query = $em->createQuery("SELECT o FROM AppApiBundle:Other o WHERE o.id=:id AND o.deleted=0");
            $query->setParameter("id", $id);

            $contact = $query->getOneOrNullResult();

            if (!$contact) {
                throw new HttpException(100, "The contact \"$id\" doesn't exist.");
            }

            $response['data'] = $contact;

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

    public function cgetAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT o FROM AppApiBundle:Other o");
            $tenants = $query->getResult();
            $response['data'] = $tenants;
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

    public function postAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('other', array());

            $this->proccessData($post, $response);

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

    public function putAction($idcontact, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('other', array());

            if (!$idcontact) {
                throw new HttpException(100, "The idcontact is required");
            }

            $this->proccessData($post, $response, $idcontact);
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

    public function deleteAction($idcontact, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idcontact == "%s") {
                throw new HttpException(100, "The idcontact is required");
            }

            $contact = $em->find("\App\ApiBundle\Entity\Other", $idcontact);

            if (!$contact) {
                throw new HttpException(100, "The contact \"$idcontact\" doesn't exist.");
            }

            $contact->setDeleted(true);
            $em->persist($contact);
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

    private function proccessData($post, &$response, $idcontact = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(function ($em) use ($post, &$response, $idcontact) {
                $validator = $this->get('validator');
                if ($idcontact) {
                    $contact = $em->find("\App\ApiBundle\Entity\Other", $idcontact);
                    if (!$contact) {
                        throw new HttpException(100, "The contact with id \"$idcontact\" doesn't exist.");
                    }
                } else {
                    $contact = new Other();
                    $em->persist($contact);
                }

                if (isset($post['name'])) {
                    $contact->setName($post['name']);
                }
                if (isset($post['surname'])) {
                    $contact->setSurname($post['surname']);
                }
                if (isset($post['email'])) {
                    $contact->setEmail($post['email']);
                }
                if (isset($post['landline'])) {
                    $contact->setLandline($post['landline']);
                }
                if (isset($post['mobile'])) {
                    $contact->setMobile($post['mobile']);
                }
                if (isset($post['address'])) {
                    $contact->setAddress($post['address']);
                }
                if (isset($post['postcode'])) {
                    $contact->setPostcode($post['postcode']);
                }
                if (isset($post['town'])) {
                    $contact->setTown($post['town']);
                }
                if (isset($post['job_title'])) {
                    $contact->setJobTitle($post['job_title']);
                }
                if (isset($post['comments'])) {
                    $contact->setComments($post['comments']);
                }
                if(isset($post['contact_method_other'])){
                    $contact->setContactMethodOther($post['contact_method_other']);
                }

                // contact method
                if(isset($post['contact_method']) && $post['contact_method'])
                {
                    $contactMethod = $em->find("\App\ApiBundle\Entity\ContactMethod", $post['contact_method']);
                    if($contactMethod)
                        $contact->setContactMethod($contactMethod);
                }
                else
                {
                    $contact->setContactMethod(null);
                }

                // Organisation
                if (isset($post['organisation']) && $post['organisation']) {
                    $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $post['organisation']);
                    if ($organisation) {
                        $contact->setOrganisation($organisation);
                    }
                } else {
                    $contact->setOrganisation(null);
                }

                // type & status
                if((isset($post['contact_type']) && $post['contact_type']) && (isset($post['contact_status']) && $post['contact_status']))
                {
                    $typeHasStatus = $em->find("\App\ApiBundle\Entity\TypeHasStatus", array("type"=>$post['contact_type'],"status"=>$post['contact_status']));

                    if($typeHasStatus)
                    {
                        $contact->setTypeHasStatus($typeHasStatus);
                    }
                    else
                    {
                        $contact->setTypeHasStatus(null);
                    }
                }
                else
                {
                    $contact->setTypeHasStatus(null);
                }

                // other type
                if (isset($post['other_type']) && $post['other_type']) {
                    $otherType = $em->find("\App\ApiBundle\Entity\OtherType", $post['other_type']);
                    if ($otherType) {
                        $contact->setOtherType($otherType);
                    }
                } else {
                    $contact->setOtherType(null);
                }

                // contact title
                if (isset($post['contact_title']) && $post['contact_title']) {
                    $contactTitle = $em->find("\App\ApiBundle\Entity\ContactTitle", $post['contact_title']);
                    if ($contactTitle) {
                        $contact->setContactTitle($contactTitle);
                    }
                } else {
                    $contact->setContactTitle(null);
                }
/*
                $user = $this->getUser();
                $fb = $this->get('my_fire_php');
                $fb->log($user->getId(), "user->getId() session");*/



                /* check validation */
                $errors = $validator->validate($contact);
                if ($errors->count()) {
                    foreach ($errors as $error)
                    {
                        if($error->getPropertyPath()=="typeHasStatus")
                            $property="status";
                        else
                            $property=$error->getPropertyPath();

                        $response['errors'][] = array($property => $error->getMessage());
                    }
                    throw new HttpException(100, "validation");
                }

                // save
                $em->flush();
                $em->persist($contact);
                $response['data'] = $contact; // return the id created
            });
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
