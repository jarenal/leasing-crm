<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Routing\ClassResourceInterface,
    Symfony\Component\HttpFoundation\Request,
    App\ApiBundle\Entity\Contractor,
    App\ApiBundle\Entity\ContractorArea,
    Symfony\Component\HttpKernel\Exception\HttpException,
    JMS\Serializer\SerializationContext,
    Symfony\Component\HttpFoundation\File\UploadedFile;

class ContractorController extends FOSRestController implements ClassResourceInterface
{
    public function getAction($idcontractor, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idcontractor=="%s") {
                throw new HttpException(100, "The idcontractor is required");
            }

            $query = $em->createQuery("SELECT c FROM AppApiBundle:Contractor c WHERE c.id=:idcontractor AND c.deleted=0");
            $query->setParameter("idcontractor", $idcontractor);

            $contractor = $query->getOneOrNullResult();

            if (!$contractor) {
                throw new HttpException(100, "The contractor \"$idcontractor\" doesn't exist.");
            }

            $response['data'] = $contractor;

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
		try
		{
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT c FROM AppApiBundle:Contractor c");
            $contractors = $query->getResult();
            $response['data'] = $contractors;
		}
		catch(HttpException $ex)
		{
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
        try
        {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('contractor', array());
            //$fb = $this->get('my_fire_php');
            //$fb->log($_POST, '$_POST: ');
            //$fb->log($_REQUEST, '$_REQUEST: ');

            $files = $request->files->get('contractor');
            //$fb->log($files, 'FILES');
            $this->proccessData($post, $response, null, $files);

        }
        catch(HttpException $ex)
        {
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

    public function postUpdateAction($idcontractor, Request $request)
    {
        try
        {
            //$fb = $this->get('my_fire_php');
            //$fb->log("postUpdateAction");

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('contractor', array());

            //$fb->log($_POST, '$_POST: ');
            //$fb->log($_REQUEST, '$_REQUEST: ');

            $files = $request->files->get('contractor');
            //$fb->log($files, 'FILES');

            if(!$idcontractor)
                throw new HttpException(100, "The idcontractor is required");

            $this->proccessData($post, $response, $idcontractor, $files);

        }
        catch(HttpException $ex)
        {
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

    /* Commented because PUT method doesn't support upload files :(
    public function putAction($idcontractor, Request $request)
    {
        try
        {
            $fb = $this->get('my_fire_php');
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $streamPostData = $this->get('stream_post_data');

            $stream_data = array();

            $streamPostData->init($stream_data);

            $fb->log($stream_data, '$stream_data: ');

            $post = $stream_data['post'];
            $files = array();

            foreach ($stream_data['file'] as $key => $file)
            {
                $subido = is_uploaded_file($file['tmp_name']);
                $fue_subido = $subido ? "si": "no";
                $fb->log($fue_subido, "{$file['tmp_name']} - subido correctamente???");

                if($file['error']===UPLOAD_ERR_OK)
                {
                    $fb->log(UPLOAD_ERR_OK, "UPLOAD_ERR_OK Correcto!!!!");
                }
                else
                {
                    $fb->log($file['error'], "Codigo de error incorrecto...");
                }

                $files[$key] = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);

                $error_code = $files[$key]->getError();
                $fb->log($error_code, 'error_code UploadedFile');
            }

            $fb->log($files, '$files: ');

            if(!$idcontractor)
                throw new HttpException(100, "The idcontractor is required");

            $this->proccessData($post, $response, $idcontractor, $files);
        }
        catch(HttpException $ex)
        {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        return $this->handleView($view);
    } */

    public function deleteAction($idcontractor, Request $request)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if($idcontractor=="%s")
                throw new HttpException(100, "The idcontractor is required");

            $contractor = $em->find("\App\ApiBundle\Entity\Contractor", $idcontractor);

            if(!$contractor)
                throw new HttpException(100, "The contractor \"$idcontractor\" doesn't exist.");

            $contractor->setDeleted(true);
            $em->persist($contractor);
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

    private function proccessData($post, &$response, $idcontractor=null, $files=array())
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $validator = $this->get('validator');

            if($idcontractor)
            {
                $contractor = $em->find("\App\ApiBundle\Entity\Contractor", $idcontractor);
                if(!$contractor)
                    throw new HttpException(100, "The Contractor with id \"$idcontractor\" doesn't exist.");
            }
            else
            {
                $contractor = new Contractor();
            }

            if(isset($post['name']))
                $contractor->setName($post['name']);
            if(isset($post['surname']))
                $contractor->setSurname($post['surname']);
            if(isset($post['email']))
                $contractor->setEmail($post['email']);
            if(isset($post['landline']))
                $contractor->setLandline($post['landline']);
            if(isset($post['mobile']))
                $contractor->setMobile($post['mobile']);
            if(isset($post['address']))
                $contractor->setAddress($post['address']);
            if(isset($post['postcode']))
                $contractor->setPostcode($post['postcode']);
            if(isset($post['town']))
                $contractor->setTown($post['town']);
            if(isset($post['require_certification']))
                $contractor->setRequireCertification($post['require_certification']);
            if(isset($post['liability_insurance']))
                $contractor->setLiabilityInsurance($post['liability_insurance']);
            if(isset($post['contact_method_other']))
                $contractor->setContactMethodOther($post['contact_method_other']);
            if(isset($post['comments']))
                $contractor->setComments($post['comments']);

            // contact method
            if(isset($post['contact_method']) && $post['contact_method'])
            {
                $contactMethod = $em->find("\App\ApiBundle\Entity\ContactMethod", $post['contact_method']);
                if($contactMethod)
                    $contractor->setContactMethod($contactMethod);
            }
            else
            {
                $contractor->setContactMethod(null);
            }

            // Token
            if(!$contractor->getToken())
            {
                if(!isset($post['name']) || !$post['name'])
                    throw new HttpException(100, "We can't generate a token. The name is required.");
                if(!isset($post['surname']) || !$post['surname'])
                    throw new HttpException(100, "We can't generate a token. The surname is required.");
                if(!isset($post['contact_type']) || !$post['contact_type'])
                    throw new HttpException(100, "We can't generate a token. The contact_type is required.");

                $hash = $post['name'].$post['surname'].$post['contact_type'].date("YmdH");

                $contractor->setToken(md5($hash));
            }

            // type & status
            if((isset($post['contact_type']) && $post['contact_type']) && (isset($post['contact_status']) && $post['contact_status']))
            {
                $typeHasStatus = $em->find("\App\ApiBundle\Entity\TypeHasStatus", array("type"=>$post['contact_type'],"status"=>$post['contact_status']));

                if($typeHasStatus)
                {
                    $contractor->setTypeHasStatus($typeHasStatus);
                }
                else
                {
                    $contractor->setTypeHasStatus(null);
                }
            }
            else
            {
                $contractor->setTypeHasStatus(null);
            }

            // contact title
            if(isset($post['contact_title']) && $post['contact_title'])
            {
                $contactTitle = $em->find("\App\ApiBundle\Entity\ContactTitle", $post['contact_title']);
                if($contactTitle)
                    $contractor->setContactTitle($contactTitle);
            }
            else
            {
                $contractor->setContactTitle(null);
            }

            // organisation
            if(isset($post['organisation']) && $post['organisation'])
            {
                $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $post['organisation']);
                if($organisation)
                    $contractor->setOrganisation($organisation);
            }
            else
            {
                $contractor->setOrganisation(null);
            }

            if(!isset($post['services']) || !is_array($post['services']) || !$post['services'])
                $post['services'] = array();

            /*** SERVICES ****/
            $current_services = array();
            $new_services = array();
            $deleted_services = array();

            // get current services
            if($idcontractor)
            {
                /*
                $contractor_services = $contractor->getServices();

                foreach ($contractor_services as $service)
                {
                    $current_services[] = $service->getId();
                } */
                $current_services = $contractor->getServicesIds();
            }

            // get deleted services
            $deleted_services = array_diff($current_services, $post['services']);

            // get new services
            $new_services = array_diff($post['services'], $current_services);
            /*
            $fb = $this->get('my_fire_php');
            $fb->log($deleted_services,"deleted_services");
            $fb->log($new_services,"new_services");
            $fb->log($post['services'],"post['services']");
            $fb->log($current_services,"current_services");*/

            // add new services
            foreach ($new_services as $service)
            {
                $newService = $em->find("\App\ApiBundle\Entity\Service", $service);
                if(!$newService)
                    throw new HttpException(100, "The Service with id \"{$service['id']}\" doesn't exist.");

                $contractor->addService($newService);
                unset($newService);
            }

            // delete services
            foreach ($deleted_services as $service)
            {
                $oldService = $em->find("\App\ApiBundle\Entity\Service", $service);
                if(!$oldService)
                    throw new HttpException(100, "The Service with id \"{$service['id']}\" doesn't exist.");

                $contractor->removeService($oldService);
                unset($oldService);
            }

            /*** AREAS ***/
            if(!isset($post['areas']) || !is_array($post['areas']) || !$post['areas'])
                $post['areas'] = array();

            if($idcontractor)
            {
                $current_areas = $contractor->getAreasIds();

                $post_areas = array();

                foreach ($post['areas'] as $area)
                {
                    if(isset($area['id']) && $area['id'])
                        $post_areas[] = $area['id'];
                }
                // to delete
                $to_delete = array_diff($current_areas, $post_areas);

                if($to_delete)
                {
                    foreach ($to_delete as $idarea)
                    {
                        $delArea = $em->find("\App\ApiBundle\Entity\ContractorArea", $idarea);
                        if(!$delArea)
                            throw new HttpException(100, "The Area with id \"{$idarea}\" doesn't exist.");
                        $contractor->removeArea($delArea);
                    }
                }
            }

            // to update or to create
            foreach ($post['areas'] as $area)
            {
                // If area doesn't have postcode jump to the next...
                if(!$area['postcode'])
                    continue;

                if(isset($area['id']) && $area['id'])
                {
                    $newArea = $em->find("\App\ApiBundle\Entity\ContractorArea", $area['id']);
                    if(!$newArea)
                        throw new HttpException(100, "The Area with id \"{$idarea}\" doesn't exist.");
                }
                else
                {
                    $newArea = new ContractorArea();
                }

                $newArea->setDistance($area['distance']);
                $newArea->setPostcode($area['postcode']);
                $contractor->addArea($newArea);
                unset($newArea);
            }

            /* check validation */
            $errors = $validator->validate($contractor);

            if($errors->count())
            {
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

            /* files */
            //$fb = $this->get('my_fire_php');
            //$fb->log($files, "uploaded files...");
            $path_uploaded = false;

            if(isset($files) && is_array($files) && $files)
            {
                $uploads_handler = $this->get('uploads_handler');

                foreach ($files as $field_name => $file)
                {
                    $path_uploaded = $uploads_handler->uploadFile($contractor->getToken(), $file, $response, "DOCUMENTS", "CONTACTS");
                    if($path_uploaded)
                    {
                        switch ($field_name) {
                            case 'file_certification':
                                $contractor->setFileCertification(basename($path_uploaded));
                                break;

                            case 'file_insurance':
                                $contractor->setFileInsurance(basename($path_uploaded));
                                break;
                        }

                    }
                }
            }

            // save
            $em->persist($contractor);
            $em->flush();
            $response['data'] = $contractor; // return the id created
        }
        catch (HttpException $ex)
        {
            throw $ex;
        }
    }

    public function deleteFileTypeAction($token, $filename, $type, Request $request)
    {
        try
        {
            // TODO increase security!!!!!!!!!!!!!!!!!
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if($type=="%s")
                throw new HttpException(100, "The type is required");

            if($token=="%s")
                throw new HttpException(100, "The token is required");

            if($filename=="%s")
                throw new HttpException(100, "The filename is required");

            $response['data']['token'] = $token;
            $response['data']['filename'] = $filename;
            $response['data']['type'] = $type;

            $uploads_handler = $this->get('uploads_handler');

            $uploads_handler->removeFile($token, $filename, $response, "DOCUMENTS", "CONTACTS");

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository("\App\ApiBundle\Entity\Contractor");

            $contractor = $repository->findOneBy(array("token"=>$token));

            if(!$contractor)
                throw new HttpException(100, "The contractor with token \"$token\" doesn't exist.");

            switch ($type)
            {
                case "certification":
                $contractor->setFileCertification("");
                break;
                case "insurance":
                $contractor->setFileInsurance("");
                break;
            }
            $em->persist($contractor);
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
}