<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Tenancy;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\Get;

class TenancyController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT t.id, t.startDate, t.endDate, t.reviewDate, p.address address, CONCAT(o.name,' ',o.surname) owner, COUNT(bk.id) total_breakdowns FROM AppApiBundle:Tenancy t INNER JOIN t.property p INNER JOIN t.owner o LEFT JOIN t.breakdowns bk WHERE t.deleted=0 GROUP BY t.id ORDER BY t.id DESC");
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
    * @Get("/tenancies/by/property/{property}")
    */
    public function cgetByPropertyAction($property, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT t.id, t.startDate, t.endDate, t.reviewDate, p.address address, CONCAT(o.name,' ',o.surname) owner, COUNT(bk.id) total_breakdowns FROM AppApiBundle:Tenancy t INNER JOIN t.property p INNER JOIN t.owner o LEFT JOIN t.breakdowns bk WHERE t.property=:property AND t.deleted=0 GROUP BY t.id ORDER BY t.id");
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

            $post = $request->request->get('tenancy', array());
            $files = $request->files->get('tenancy');
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

            $entity = $em->find("\App\ApiBundle\Entity\Tenancy", $id);

            if (!$entity) {
                throw new HttpException(100, "The tenancy \"$id\" doesn't exist.");
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

            $uploads_handler->removeFile($token, $filename, $response, "DOCUMENTS", "TENANCIES");

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository("\App\ApiBundle\Entity\Tenancy");

            $entity = $repository->findOneBy(array("token"=>$token));

            if(!$entity)
                throw new HttpException(100, "The tenancy with token \"$token\" doesn't exist.");

            if($entity->getTenancyAgreementFile() == $filename){
                $entity->setTenancyAgreementFile("");
            }
            elseif($entity->getTenancyAgreementVisualFile() == $filename){
                $entity->setTenancyAgreementVisualFile("");
            }
            elseif ($entity->getServiceLevelAgreementFile()== $filename) {
                $entity->setServiceLevelAgreementFile("");
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

    public function postUpdateAction($id, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('tenancy', array());
            $files = $request->files->get('tenancy');

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

    private function proccessData($post, &$response, $idtenancy = null, $files=array())
    {
        $fb = $this->get('my_fire_php');
        $fb->log($files, "processData init files");
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(function ($em) use ($post, &$response, $idtenancy, $files) {
                $fb = $this->get('my_fire_php');
                $fb->log($files, "processData transactional files");
                $validator = $this->get('validator');
                if ($idtenancy) {
                    $entity = $em->find("\App\ApiBundle\Entity\Tenancy", $idtenancy);
                    if (!$entity) {
                        throw new HttpException(100, "The tenancy \"$idtenancy\" doesn't exist.");
                    }
                } else {
                    $entity = new Tenancy();
                    $em->persist($entity);
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

                if (isset($post['tenancy_type'])) {
                    $entity->setTenancyType($post['tenancy_type']);
                } else{
                    $entity->setTenancyType(null);
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

                // LFL contact
                if (isset($post['owner']) && $post['owner']) {
                    $owner = $em->find("\App\ApiBundle\Entity\Other", $post['owner']);
                    if ($owner) {
                        $entity->setOwner($owner);
                    }
                } else {
                    $entity->setOwner(null);
                }

                /*** TENANTS ****/
                if(!isset($post['tenants']) || !is_array($post['tenants']) || !$post['tenants'])
                    $post['tenants'] = array();

                $current_tenants = array();
                $new_tenants = array();
                $deleted_tenants = array();

                // get current tenants
                if($idtenancy)
                {
                    $current_tenants = $entity->getTenantsIds();
                }

                // get deleted tenants
                $deleted_tenants = array_diff($current_tenants, $post['tenants']);

                // get new tenants
                $new_tenants = array_diff($post['tenants'], $current_tenants);

                // add new tenant
                foreach ($new_tenants as $tenant)
                {
                    $newTenant = $em->find("\App\ApiBundle\Entity\Tenant", $tenant);
                    if(!$newTenant)
                        throw new HttpException(100, "The tenant with id \"{$tenant['id']}\" doesn't exist.");

                    $entity->addTenant($newTenant);
                    unset($newTenant);
                }

                // delete tenant
                foreach ($deleted_tenants as $tenant)
                {
                    $oldTenant = $em->find("\App\ApiBundle\Entity\Tenant", $tenant);
                    if(!$oldTenant)
                        throw new HttpException(100, "The tenant with id \"{$tenant['id']}\" doesn't exist.");

                    $entity->removeTenant($oldTenant);
                    unset($oldTenant);
                }

                // Token
                // TODO include tenant data for avoid duplicated tokens
                if(!isset($post['token']) || !$post['token'])
                {
                    $tenants = implode("-", $post['tenants']);
                    if(!$idtenancy && ($post['property'] && $post['start_date'] && $post['end_date'] && $post['owner'] && $tenants)) {
                        $token = md5($post['property'].$post['start_date'].$post['end_date'].$post['owner'].$tenants.date("YmdH"));
                        $entity->setToken($token);
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
                        $fb->log($field_name, "TenancyController->processData(): field_name");
                        $path_uploaded = $uploads_handler->uploadFile($entity->getToken(), $file, $response, "DOCUMENTS", "TENANCIES");
                        if($path_uploaded)
                        {
                            switch ($field_name) {
                                case 'tenancy_agreement_file':
                                    $entity->setTenancyAgreementFile(basename($path_uploaded));
                                    break;
                                case 'tenancy_agreement_visual_file':
                                    $entity->setTenancyAgreementVisualFile(basename($path_uploaded));
                                    break;
                                case 'service_level_agreement_file':
                                    $entity->setServiceLevelAgreementFile(basename($path_uploaded));
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
