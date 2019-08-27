<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\RentBreakdown;
use App\ApiBundle\Entity\BreakdownHasItems;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\Get;

class BreakdownController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT rb.id, rb.startDate FROM AppApiBundle:RentBreakdown rb WHERE rb.deleted=0 ORDER BY rb.id DESC");
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
    * @Get("/breakdowns/by/tenancy/{tenancy}")
    */
    public function cgetByTenancyAction($tenancy, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT rb.id, rb.startDate FROM AppApiBundle:RentBreakdown rb WHERE rb.deleted=0 AND rb.tenancy=:tenancy ORDER BY rb.id DESC");
            $query->setParameter("tenancy", $tenancy);
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

            $post = $request->request->get('breakdown', array());
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

    public function putAction($idbreakdown, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('breakdown', array());

            if (!$idbreakdown) {
                throw new HttpException(100, "The idbreakdown is required");
            }

            $this->proccessData($post, $response, $idbreakdown);
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

            $entity = $em->find("\App\ApiBundle\Entity\RentBreakdown", $id);

            if (!$entity) {
                throw new HttpException(100, "The rent breakdown \"$id\" doesn't exist.");
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

    private function proccessData($post, &$response, $idbreakdown = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(function ($em) use ($post, &$response, $idbreakdown) {

                $validator = $this->get('validator');
                if ($idbreakdown) {
                    $entity = $em->find("\App\ApiBundle\Entity\RentBreakdown", $idbreakdown);
                    if (!$entity) {
                        throw new HttpException(100, "The rent breakdown \"$idbreakdown\" doesn't exist.");
                    }
                } else {
                    $entity = new RentBreakdown();
                    $em->persist($entity);
                }

                // Tenancy
                if (isset($post['tenancy']) && $post['tenancy']) {
                    $tenancy = $em->find("\App\ApiBundle\Entity\Tenancy", $post['tenancy']);
                    if ($tenancy) {
                        $entity->setTenancy($tenancy);
                    }
                } else {
                    $entity->setTenancy(null);
                }

                if (isset($post['start_date'])) {
                    $entity->setStartDate($post['start_date']);
                } else{
                    $entity->setStartDate(null);
                }

                if (isset($post['recurring_rent_review'])) {
                    $entity->setRecurringRentReview($post['recurring_rent_review']);
                } else{
                    $entity->setRecurringRentReview(null);
                }

                if (isset($post['recurring_rent_review_timescale'])) {
                    $entity->setRecurringRentReviewTimescale($post['recurring_rent_review_timescale']);
                } else{
                    $entity->setRecurringRentReviewTimescale(null);
                }

                /* ITEMS */
                foreach ($post['items'] as $item)
                {
                    if($idbreakdown)
                    {
                        $bhi = $em->find("\App\ApiBundle\Entity\BreakdownHasItems", array("breakdown"=>$idbreakdown, "item"=>$item['id']));
                    }
                    else
                    {
                        $bhi = new BreakdownHasItems();
                        $itemEntity = $em->find("\App\ApiBundle\Entity\RentBreakdownItem", $item['id']);
                        if (!$itemEntity)
                            throw new HttpException(100, "The rent breakdown item \"{$item['id']}\" doesn't exist.");
                        $bhi->setItem($itemEntity);
                        $bhi->setBreakdown($entity);
                    }

                    $bhi->setAmount($item['value']);
                    $em->persist($bhi);
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
