<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\File;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class FileController extends FOSRestController implements ClassResourceInterface
{
    public function cgetDocumentsByPropertyAction($idproperty, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT fil
                                    FROM AppApiBundle:File fil
                                    WHERE fil.property=:idproperty AND fil.type=:type");
            $query->setParameter("idproperty", $idproperty);
            $query->setParameter("type", "D");
            $files = $query->getResult();
            $response['data'] = $files;
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

    public function cgetImagesByPropertyAction($idproperty, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT fil
                                    FROM AppApiBundle:File fil
                                    WHERE fil.property=:idproperty AND fil.type=:type");
            $query->setParameter("idproperty", $idproperty);
            $query->setParameter("type", "I");
            $files = $query->getResult();
            $response['data'] = $files;
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

    public function deleteAction($token, Request $request)
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

            $response['data']['token'] = $token;

            /*
            $uploads_handler = $this->get('uploads_handler');
            $uploads_handler->removeFile($token, $filename, $response, "DOCUMENTS", "CONTACTS");
            */

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository("\App\ApiBundle\Entity\File");

            $file = $repository->findOneBy(array("token"=>$token));

            if(!$file)
                throw new HttpException(100, "The file with token '\"$token\"' doesn't exist.");

            $em->remove($file);
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

    public function cgetByContractorAction($idcontractor, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT c FROM AppApiBundle:Contractor c WHERE c.id=:idcontractor AND c.deleted=0");
            $query->setParameter("idcontractor", $idcontractor);

            $contractor = $query->getOneOrNullResult();

            if (!$contractor) {
                throw new HttpException(100, "The contractor \"$idcontractor\" doesn't exist.");
            }

            $response['data'] = $contractor->getFiles();
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

    public function cgetByLandlordAction($idlandlord, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT l FROM AppApiBundle:Landlord l WHERE l.id=:idlandlord AND l.deleted=0");
            $query->setParameter("idlandlord", $idlandlord);

            $landlord = $query->getOneOrNullResult();

            if (!$landlord) {
                throw new HttpException(100, "The landlord \"$idlandlord\" doesn't exist.");
            }

            $response['data'] = $landlord->getFiles();
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

    public function cgetByTenantAction($idtenant, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery("SELECT t FROM AppApiBundle:Tenant t WHERE t.id=:idtenant AND t.deleted=0");
            $query->setParameter("idtenant", $idtenant);

            $tenant = $query->getOneOrNullResult();

            if (!$tenant) {
                throw new HttpException(100, "The tenant \"$idtenant\" doesn't exist.");
            }

            $response['data'] = $tenant->getFiles();
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
}
