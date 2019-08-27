<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Property;
use App\ApiBundle\Entity\File;
use App\ApiBundle\Entity\PropertyRiskAssessment;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\Get;

class PropertyController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT pro FROM AppApiBundle:Property pro INNER JOIN pro.landlord lan WHERE (pro.address LIKE :name OR pro.postcode LIKE :name OR pro.town LIKE :name OR lan.name LIKE :name OR lan.surname LIKE :name) AND pro.deleted=0 AND lan.deleted=0");
            $query->setParameter('name', "%$term%");
            $properties = $query->getResult();

            foreach ($properties as $property) {
                $response[] = array('id'      => $property->getId(),
                                    'value' => $property->getFulltitle(),
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
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT p.id, p.address, p.postcode, p.town, s.name status_name, COUNT(la.id) leases, COUNT(ten.id) tenancies, s.id idstatus, lauth.name local_authority_name, CONCAT(lan.name,' ',lan.surname) landlord_name FROM AppApiBundle:Property p LEFT JOIN p.status s LEFT JOIN p.lease_agreements la WITH la.id IN (SELECT la2 FROM AppApiBundle:LeaseAgreement la2 WHERE la2.deleted=0) LEFT JOIN p.tenancies ten WITH ten.id IN (SELECT ten2 FROM AppApiBundle:Tenancy ten2 WHERE ten2.deleted=0) LEFT JOIN p.local_authority lauth LEFT JOIN p.landlord lan WHERE p.deleted=0 GROUP BY p.id");
            $properties = $query->getResult();
            $response['data'] = $properties;
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
     * @Get("/properties/by/landlord/{landlord}")
     */
    public function cgetByLandlordAction($landlord, Request $request)
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT p.id, p.address, p.postcode, p.town, s.name status_name, s.id idstatus FROM AppApiBundle:Property p LEFT JOIN p.status s WHERE p.deleted=0 AND p.landlord=:landlord");
            $query->setParameter("landlord", $landlord);
            $properties = $query->getResult();
            $response['data'] = $properties;
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
     * @Get("/properties/by/tenant/{tenant}")
     */
    public function cgetByTenantAction($tenant, Request $request)
    {
        /* TODO re-implement for tenancies?? */
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT p.id, p.address, p.postcode, p.town, s.name status_name, s.id idstatus FROM AppApiBundle:Property p LEFT JOIN p.status s WHERE p.deleted=0 AND :tenant MEMBER OF p.tenants");
            $query->setParameter("tenant", $tenant);
            $properties = $query->getResult();
            $response['data'] = $properties;
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

            $query = $em->createQuery("SELECT p FROM AppApiBundle:Property p WHERE p.id=:id AND p.deleted=0");
            $query->setParameter("id", $id);

            $contact = $query->getOneOrNullResult();

            if (!$contact) {
                throw new HttpException(100, "The property \"$id\" doesn't exist.");
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

    public function postAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('property', array());

            /*$fb = $this->get('my_fire_php');
            $fb->log($_POST, '$_POST: ');
            $fb->log($_REQUEST, '$_REQUEST: ');*/

            $files = $request->files->get('property');
            //$fb->log($files, 'FILES');

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

    public function postUpdateAction($idproperty, Request $request)
    {
        try
        {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('property', array());

            $files = $request->files->get('property');

            if(!$idproperty)
                throw new HttpException(100, "The idproperty is required");

            $this->proccessData($post, $response, $idproperty, $files);

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
    public function putAction($idproperty, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('property', array());

            if (!$idproperty) {
                throw new HttpException(100, "The idproperty is required");
            }

            $this->proccessData($post, $response, $idproperty);
        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        return $this->handleView($view);
    }
    */

    public function deleteAction($idproperty, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idproperty=="%s") {
                throw new HttpException(100, "The idproperty is required");
            }

            $property = $em->find("\App\ApiBundle\Entity\Property", $idproperty);

            if (!$property) {
                throw new HttpException(100, "The property \"$idproperty\" doesn't exist.");
            }

            $property->setDeleted(true);
            $em->persist($property);
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

    private function proccessData($post, &$response, $idproperty = null, $files=array())
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(
                function ($em) use ($post, &$response, $idproperty, $files) {
                    $fb = $this->get('my_fire_php');
                    //$fb->log($post, "post in processData(): ");
                    $validator = $this->get('validator');

                    if ($idproperty) {
                        $property = $em->find("\App\ApiBundle\Entity\Property", $idproperty);
                        if (!$property) {
                            throw new HttpException(100, "The Property with id \"$idproperty\" doesn't exist.");
                        }
                    } else {
                        $property = new Property();
                        $em->persist($property);
                    }

                    // Landlord
                    if (isset($post['landlord']) && $post['landlord']) {
                        $landlord = $em->find("\App\ApiBundle\Entity\Landlord", $post['landlord']);
                        if ($landlord) {
                            $property->setLandlord($landlord);
                        }
                    } else {
                        $property->setLandlord(null);
                    }

                    // Local Authority
                    if (isset($post['local_authority']) && $post['local_authority']) {
                        $local_authority = $em->find("\App\ApiBundle\Entity\Organisation", $post['local_authority']);
                        if ($local_authority) {
                            $property->setLocalAuthority($local_authority);
                        }
                    } else {
                        $property->setLocalAuthority(null);
                    }

                    // Address
                    if (isset($post['address'])) {
                        $property->setAddress($post['address']);
                    }
                    else {
                        $property->setAddress(null);
                    }

                    if (isset($post['postcode'])) {
                        $property->setPostcode($post['postcode']);
                    }
                    else {
                        $property->setPostcode(null);
                    }

                    if (isset($post['town'])) {
                        $property->setTown($post['town']);
                    }
                    else {
                        $property->setTown(null);
                    }

                    // Token
                    if(!$idproperty && $property->getAddress() && $property->getPostcode() && $property->getTown())
                    {
                        if($landlord)
                            $landlord_name = $landlord->getName().$landlord->getSurname();
                        else
                            $landlord_name = time();

                        $seed = $landlord_name.$property->getAddress() . $property->getPostcode() . $property->getTown().date("YmdH");
                        $property->setToken(md5($seed).strlen($seed));
                    }

                    // Housing features
                    if (isset($post['available_date'])) {
                        $property->setAvailableDate($post['available_date']);
                    }
                    else {
                        $property->setAvailableDate(null);
                    }

                    if (isset($post['parking_for'])) {
                        $property->setParkingFor($post['parking_for']);
                    }
                    else {
                        $property->setParkingFor(null);
                    }

                    if (isset($post['special_design_features'])) {
                        $property->setSpecialDesignFeatures($post['special_design_features']);
                    }
                    else {
                        $property->setSpecialDesignFeatures(null);
                    }

                    if (isset($post['previous_crimes_near'])) {
                        $property->setPreviousCrimesNear($post['previous_crimes_near']);
                    }
                    else {
                        $property->setPreviousCrimesNear(false);
                    }

                    if (isset($post['previous_crimes_description'])) {
                        $property->setPreviousCrimesDescription($post['previous_crimes_description']);
                    }
                    else {
                        $property->setPreviousCrimesDescription(null);
                    }

                    // Value
                    if (isset($post['property_value'])) {
                        $property->setPropertyValue($post['property_value']);
                    }
                    else {
                        $property->setPropertyValue(null);
                    }

                    if (isset($post['valuation_date'])) {
                        $property->setValuationDate($post['valuation_date']);
                    }
                    else {
                        $property->setValuationDate(null);
                    }

                    if (isset($post['target_rent'])) {
                        $property->setTargetRent($post['target_rent']);
                    }
                    else {
                        $property->setTargetRent(null);
                    }

                    if (isset($post['mortgage_outstanding'])) {
                        $property->setMortgageOutstanding($post['mortgage_outstanding']);
                    }
                    else {
                        $property->setMortgageOutstanding(false);
                    }

                    if (isset($post['buy_to_let'])) {
                        $property->setBuyToLet($post['buy_to_let']);
                    }
                    else {
                        $property->setBuyToLet(false);
                    }
                    if (isset($post['comments'])) {
                        $property->setComments($post['comments']);
                    }
                    else {
                        $property->setComments("");
                    }

                    // Administration
                    if (isset($post['land_registry_docs'])) {
                        $property->setLandRegistryDocs($post['land_registry_docs']);
                    }
                    else {
                        $property->setLandRegistryDocs(false);
                    }

                    if (isset($post['status']) && $post['status']) {
                        $status = $em->find("\App\ApiBundle\Entity\PropertyStatus", $post['status']);
                        if ($status) {
                            $property->setStatus($status);
                        }
                    } else {
                        $property->setStatus(null);
                    }

                    /*** FEATURES ****/
                    if(!isset($post['features']) || !is_array($post['features']) || !$post['features'])
                        $post['features'] = array();


                    $current_features = array();
                    $new_features = array();
                    $deleted_features = array();

                    // get current features
                    if($idproperty)
                    {
                        $current_features = $property->getFeaturesIds();
                    }

                    // get deleted features
                    $deleted_features = array_diff($current_features, $post['features']);

                    // get new features
                    $new_features = array_diff($post['features'], $current_features);

                    // add new features
                    foreach ($new_features as $feature)
                    {
                        $newFeature = $em->find("\App\ApiBundle\Entity\Feature", $feature);
                        if(!$newFeature)
                            throw new HttpException(100, "The Feature with id \"{$feature}\" doesn't exist.");

                        $property->addFeature($newFeature);
                        unset($newFeature);
                    }

                    // delete features
                    foreach ($deleted_features as $feature)
                    {
                        $oldFeature = $em->find("\App\ApiBundle\Entity\Feature", $feature);
                        if(!$oldFeature)
                            throw new HttpException(100, "The Feature with id \"{$feature}\" doesn't exist.");

                        $property->removeFeature($oldFeature);
                        unset($oldFeature);
                    }

                    /*** FILES ***/
                    $path_uploaded = false;
                    //$fb->log($files, "uploaded files...");

                    if(isset($files) && is_array($files) && $files)
                    {
                        $uploads_handler = $this->get('uploads_handler');

                        if(isset($files['documents']) && is_array($files['documents']) && $files['documents'])
                        {
                            foreach ($files['documents'] as $field_name => $file)
                            {
                                $path_uploaded = $uploads_handler->uploadFile($property->getToken(), $file, $response, "DOCUMENTS", "PROPERTIES");
                                if($path_uploaded)
                                {
                                    $newFile = new File();
                                    $newFile->setName(basename($path_uploaded));
                                    $newFile->setType("D");
                                    $newFile->setPath(str_replace($_SERVER['DOCUMENT_ROOT'], "", $path_uploaded));
                                    $property->addFile($newFile);
                                    unset($newFile);
                                }
                            }
                        }

                        if(isset($files['images']) && is_array($files['images']) && $files['images'])
                        {
                            foreach ($files['images'] as $field_name => $file)
                            {
                                $path_uploaded = $uploads_handler->uploadFile($property->getToken(), $file, $response, "IMAGES", "PROPERTIES");
                                if($path_uploaded)
                                {
                                    $newFile = new File();
                                    $newFile->setName(basename($path_uploaded));
                                    $newFile->setType("I");
                                    $newFile->setPath(str_replace($_SERVER['DOCUMENT_ROOT'], "", $path_uploaded));
                                    $property->addFile($newFile);
                                    unset($newFile);
                                }
                            }
                        }
                    }

                    /* check validation */
                    $errors = $validator->validate($property);

                    if ($errors->count()) {
                        foreach ($errors as $error) {
                            $response['errors'][] = array($error->getPropertyPath() => $error->getMessage());
                        }
                        throw new HttpException(100, "validation");
                    }

                    // save
                    $em->flush();
                    $em->persist($property);
                    $response['data'] = $property; // return the id created
                }
            );
        } catch (HttpException $ex) {
            throw $ex;
        }
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

                    $risk_assessment = $em->find("\App\ApiBundle\Entity\PropertyRiskAssessment", array("property"=>$post['property'], "question"=>$post["question"]));

                    if(!$risk_assessment)
                    {
                        $risk_assessment = new PropertyRiskAssessment();

                        if (isset($post['property']) && $post['property'])
                        {
                            $property = $em->find("\App\ApiBundle\Entity\Property", $post['property']);
                            if ($property)
                                $risk_assessment->setProperty($property);
                            else
                                throw new HttpException("The property '{$post['property']}' doesn't exist.", 100);
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
}
