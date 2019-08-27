<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\LeaseAgreement;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\Get;

class LeaseagreementController extends FOSRestController implements ClassResourceInterface
{
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

            $query = $em->createQuery("SELECT la FROM AppApiBundle:LeaseAgreement la WHERE la.id=:id AND o.deleted=0");
            $query->setParameter("id", $id);

            $entity = $query->getOneOrNullResult();

            if (!$entity) {
                throw new HttpException(100, "The lease agreement \"$id\" doesn't exist.");
            }

            $response['data'] = $entity;

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
            $query = $em->createQuery("SELECT la.id, la.startDate, la.endDate, la.reviewDate, p.address address, CONCAT(o.name,' ',o.surname) owner FROM AppApiBundle:LeaseAgreement la INNER JOIN la.property p INNER JOIN la.owner o WHERE la.deleted=0 ORDER BY la.id DESC");
            $entity = $query->getResult();
            $response['data'] = $entity;
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
     * @Get("/leaseagreements/by/property/{property}")
     */
    public function cgetByPropertyAction($property, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT la.id, la.startDate, la.endDate, la.reviewDate, p.address address, CONCAT(o.name,' ',o.surname) owner FROM AppApiBundle:LeaseAgreement la INNER JOIN la.property p INNER JOIN la.owner o WHERE la.deleted=0 AND la.property = :property ORDER BY la.id DESC");
            $query->setParameter("property", $property);
            $entity = $query->getResult();
            $response['data'] = $entity;
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

            $post = $request->request->get('leaseagreement', array());
            $files = $request->files->get('leaseagreement');
            $fb = $this->get("my_fire_php");
            $fb->log($files, "postAction: files");
            $this->proccessData($post, $response, null, $files);

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

    public function postUpdateAction($id, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('leaseagreement', array());
            $files = $request->files->get('leaseagreement');

            if (!$id) {
                throw new HttpException(100, "The id is required");
            }

            $this->proccessData($post, $response, $id, $files);
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

    public function deleteAction($id, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($id == "%s") {
                throw new HttpException(100, "The id is required");
            }

            $entity = $em->find("\App\ApiBundle\Entity\LeaseAgreement", $id);

            if (!$entity) {
                throw new HttpException(100, "The lease agreement \"$id\" doesn't exist.");
            }

            $entity->setDeleted(true);
            $em->persist($entity);
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

    public function deleteFileAction($token, $filename, Request $request)
    {
        try
        {
            // TODO increase security!!!!!!!!!!!!!!!!!
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if($token=="%s")
                throw new HttpException(100, "The token is required");

            if($filename=="%s")
                throw new HttpException(100, "The filename is required");

            $response['data']['token'] = $token;
            $response['data']['filename'] = $filename;

            $uploads_handler = $this->get('uploads_handler');

            $uploads_handler->removeFile($token, $filename, $response, "DOCUMENTS", "LEASE_AGREEMENT");

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository("\App\ApiBundle\Entity\LeaseAgreement");

            $entity = $repository->findOneBy(array("token"=>$token));

            if(!$entity)
                throw new HttpException(100, "The lease agreement with token \"$token\" doesn't exist.");

            if($entity->getLeaseAgreementFile() == $filename){
                $entity->setLeaseAgreementFile("");
            }
            elseif ($entity->getManagementAgreementFile()== $filename) {
                $entity->setManagementAgreementFile("");
            }
            else {
                throw new HttpException(101, "We didn't find the filename '$filename' in the entity.");
            }

            $em->persist($entity);
            $em->flush();
        }
        catch(HttpException $ex)
        {
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

    private function proccessData($post, &$response, $id = null, $files=array())
    {
        $fb = $this->get('my_fire_php');
        $fb->log($files, "processData init files");
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(function ($em) use ($post, &$response, $id, $files) {
                $fb = $this->get('my_fire_php');
                $fb->log($files, "processData transactional files");
                $validator = $this->get('validator');
                if ($id) {
                    $entity = $em->find("\App\ApiBundle\Entity\LeaseAgreement", $id);
                    if (!$entity) {
                        throw new HttpException(100, "The lease agreement \"$id\" doesn't exist.");
                    }
                } else {
                    $entity = new LeaseAgreement();
                    $em->persist($entity);
                }

                // Property
                if (isset($post['property']) && $post['property']) {
                    $property = $em->find("\App\ApiBundle\Entity\Property", $post['property']);
                    if ($property) {
                        $entity->setProperty($property);
                    }
                } else {
                    $entity->setProperty(null);
                }

                if (isset($post['start_date'])) {
                    $entity->setStartDate($post['start_date']);
                } else{
                    $entity->setStartDate(null);
                }

                if (isset($post['end_date'])) {
                    $entity->setEndDate($post['end_date']);
                } else{
                    $entity->setEndDate(null);
                }

                if (isset($post['review_date'])) {
                    $entity->setReviewDate($post['review_date']);
                } else{
                    $entity->setReviewDate(null);
                }

                if (isset($post['core_lease_charge_per_week'])) {
                    $entity->setCoreLeaseChargePerWeek($post['core_lease_charge_per_week']);
                }

                // LFL contact
                if (isset($post['owner']) && $post['owner']) {
                    $owner = $em->find("\App\ApiBundle\Entity\Other", $post['owner']);
                    if ($owner) {
                        $entity->setOwner($owner);
                    }
                } else {
                    $entity->setOwner(null);
                }

                // Token
                if(!isset($post['token']) || !$post['token'])
                {
                    if(!$id && ($post['property'] && $entity->getStartDate() && $entity->getEndDate() && $post['owner'])) {
                        $entity->setToken(md5($post['property'].$post['start_date'].$post['end_date'].$post['owner'].date("YmdH")));
                    } else {
                        $entity->setToken(null);
                    }
                }

                /* FILES */
                $path_uploaded = false;

                if(isset($files) && is_array($files) && $files)
                {
                    $uploads_handler = $this->get('uploads_handler');

                    foreach ($files as $field_name => $file)
                    {
                        $fb->log($field_name, "LeaseAgreementController->processData(): field_name");
                        $path_uploaded = $uploads_handler->uploadFile($entity->getToken(), $file, $response, "DOCUMENTS", "LEASE_AGREEMENT");
                        if($path_uploaded)
                        {
                            switch ($field_name) {
                                case 'input_lease_agreement_file':
                                    $entity->setLeaseAgreementFile(basename($path_uploaded));
                                    break;

                                case 'input_management_agreement_file':
                                    $entity->setManagementAgreementFile(basename($path_uploaded));
                                    break;
                            }

                        }
                    }
                }
                else
                {
                    $fb->log($files, "files no es un array");
                }

                /* check validation */
                $errors = $validator->validate($entity);
                if ($errors->count()) {
                    foreach ($errors as $error)
                    {
                        $property=$error->getPropertyPath();
                        $response['errors'][] = array($property => $error->getMessage());
                    }
                    throw new HttpException(100, "validation");
                }

                // save
                $em->flush();
                $em->persist($entity);
                $response['data'] = $entity; // return the id created
            });
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
