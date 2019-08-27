<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Landlord;
use App\ApiBundle\Entity\Investment;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class LandlordController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT l FROM AppApiBundle:Landlord l WHERE (l.name LIKE :name OR l.surname LIKE :name) AND l.deleted=0");
            $query->setParameter('name', "%$term%");
            $contacts = $query->getResult();

            //$fb = $this->get('my_fire_php');
            foreach ($contacts as $contact) {

                //$fb->log($contact,"contact");
                $response[] = array('id'      => $contact->getId(),
                                    'value'   => $contact->getFullname(),
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

    public function getAction($idlandlord, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idlandlord=="%s") {
                throw new HttpException(100, "The idlandlord is required");
            }

            $query = $em->createQuery("SELECT l FROM AppApiBundle:Landlord l WHERE l.id=:idlandlord AND l.deleted=0");
            $query->setParameter("idlandlord", $idlandlord);

            $landlord = $query->getOneOrNullResult();

            if (!$landlord) {
                throw new HttpException(100, "The landlord \"$idlandlord\" doesn't exist.");
            }

            $response['data'] = $landlord;

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
            $query = $em->createQuery("SELECT l FROM AppApiBundle:Landlord l");
            $landlords = $query->getResult();
            $response['data'] = $landlords;
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

            $post = $request->request->get('landlord', array());

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

    public function putAction($idlandlord, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('landlord', array());

            if (!$idlandlord) {
                throw new HttpException(100, "The idlandlord is required");
            }

            $this->proccessData($post, $response, $idlandlord);
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

    public function deleteAction($idlandlord, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idlandlord=="%s") {
                throw new HttpException(100, "The idlandlord is required");
            }

            $landlord = $em->find("\App\ApiBundle\Entity\Landlord", $idlandlord);

            if (!$landlord) {
                throw new HttpException(100, "The landlord \"$idlandlord\" doesn't exist.");
            }

            $landlord->setDeleted(true);

            $em->persist($landlord);
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

    private function proccessData($post, &$response, $idlandlord = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $validator = $this->get('validator');

            if ($idlandlord) {
                $landlord = $em->find("\App\ApiBundle\Entity\Landlord", $idlandlord);
                if (!$landlord) {
                    throw new HttpException(100, "The Landlord with id \"$idlandlord\" doesn't exist.");
                }
            } else {
                $landlord = new Landlord();
            }

            if (isset($post['name'])) {
                $landlord->setName($post['name']);
            }
            if (isset($post['surname'])) {
                $landlord->setSurname($post['surname']);
            }
            if (isset($post['email'])) {
                $landlord->setEmail($post['email']);
            }
            if (isset($post['landline'])) {
                $landlord->setLandline($post['landline']);
            }
            if (isset($post['mobile'])) {
                $landlord->setMobile($post['mobile']);
            }
            if (isset($post['address'])) {
                $landlord->setAddress($post['address']);
            }
            if (isset($post['postcode'])) {
                $landlord->setPostcode($post['postcode']);
            }
            if (isset($post['town'])) {
                $landlord->setTown($post['town']);
            }
            if (isset($post['accreditation_references'])) {
                $landlord->setAccreditationReferences($post['accreditation_references']);
            }
            if (isset($post['is_investor'])) {
                $landlord->setIsInvestor($post['is_investor']);
            }
            if(isset($post['contact_method_other'])){
                $landlord->setContactMethodOther($post['contact_method_other']);
            }
            if(isset($post['comments'])){
                $landlord->setComments($post['comments']);
            }

            // contact method
            if(isset($post['contact_method']) && $post['contact_method'])
            {
                $contactMethod = $em->find("\App\ApiBundle\Entity\ContactMethod", $post['contact_method']);
                if($contactMethod)
                    $landlord->setContactMethod($contactMethod);
            }
            else
            {
                $landlord->setContactMethod(null);
            }

            // type & status
            if((isset($post['contact_type']) && $post['contact_type']) && (isset($post['contact_status']) && $post['contact_status']))
            {
                $typeHasStatus = $em->find("\App\ApiBundle\Entity\TypeHasStatus", array("type"=>$post['contact_type'],"status"=>$post['contact_status']));

                if($typeHasStatus)
                {
                    $landlord->setTypeHasStatus($typeHasStatus);
                }
                else
                {
                    $landlord->setTypeHasStatus(null);
                }
            }
            else
            {
                $landlord->setTypeHasStatus(null);
            }

            // contact title
            if (isset($post['contact_title']) && $post['contact_title']) {
                $contactTitle = $em->find("\App\ApiBundle\Entity\ContactTitle", $post['contact_title']);
                if ($contactTitle) {
                    $landlord->setContactTitle($contactTitle);
                }
            } else {
                $landlord->setContactTitle(null);
            }

            // organisation
            if (isset($post['organisation']) && $post['organisation']) {
                $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $post['organisation']);
                if ($organisation) {
                    $landlord->setOrganisation($organisation);
                }
            } else {
                $landlord->setOrganisation(null);
            }

            // accreditation
            if (isset($post['landlord_accreditation']) && $post['landlord_accreditation']) {
                $accreditation = $em->find("\App\ApiBundle\Entity\LandlordAccreditation", $post['landlord_accreditation']);
                if ($accreditation) {
                    $landlord->setLandlordAccreditation($accreditation);
                }
            } else {
                $landlord->setLandlordAccreditation(null);
            }

            if (!isset($post['investments']) || !is_array($post['investments']) || !$post['investments']) {
                $post['investments'] = array();
            }

            // investments
            if ($idlandlord) {
                $current_investments = $landlord->getInvestmentsIds();

                $post_investments = array();

                foreach ($post['investments'] as $investment) {
                    if (isset($investment['id']) && $investment['id']) {
                        $post_investments[] = $investment['id'];
                    }
                }
                // to delete
                $to_delete = array_diff($current_investments, $post_investments);

                if ($to_delete) {
                /*
                    $q = $em->createQuery('DELETE FROM \App\ApiBundle\Entity\Investment inv WHERE inv.id IN (:ids)');
                    $q->setParameter('ids', $to_delete);
                    $numDeleted = $q->execute();*/
                    foreach ($to_delete as $idinvestment) {
                        $delInvestment = $em->find("\App\ApiBundle\Entity\Investment", $idinvestment);
                        if (!$delInvestment) {
                            throw new HttpException(100, "The Investment with id \"{$investment['id']}\" doesn't exist.");
                        }
                        $landlord->removeInvestment($delInvestment);
                    }
                }
            }

            // to update or to create
            foreach ($post['investments'] as $investment) {
                if (isset($investment['id']) && $investment['id']) {
                    $newInvestment = $em->find("\App\ApiBundle\Entity\Investment", $investment['id']);
                    if (!$newInvestment) {
                        throw new HttpException(100, "The Investment with id \"{$investment['id']}\" doesn't exist.");
                    }
                } else {
                    $newInvestment = new Investment();
                }

                $newInvestment->setAmount($investment['amount']);
                $newInvestment->setDesiredReturn($investment['desired_return']);
                $newInvestment->setDistance($investment['distance']);
                $newInvestment->setPostcode($investment['postcode']);
                $landlord->addInvestment($newInvestment);
                unset($newInvestment);
            }

            /* check validation */
            $errors = $validator->validate($landlord);

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
            $em->persist($landlord);
            $em->flush();
            $response['data'] = $landlord; // return the id created
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
