<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Tenant;
use App\ApiBundle\Entity\TenantArea;
use App\ApiBundle\Entity\Child;
use App\ApiBundle\Entity\TenantHasCondition;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class TenantController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            //$fb = $this->get('my_fire_php');
            $response = array();

            $term = $request->query->get('term');
            //$fb->log($term, "TenantController->cgetComboboxAction(): \$term");

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT ten FROM AppApiBundle:Tenant ten WHERE ten.deleted=0 AND (ten.name LIKE :name OR ten.surname LIKE :name OR CONCAT(CONCAT(ten.name, ' '),ten.surname) LIKE :name)");
            $query->setParameter('name', "%$term%");
            $contacts = $query->getResult();

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
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function getAction($idtenant, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idtenant=="%s") {
                throw new HttpException(100, "The idtenant is required");
            }

            $query = $em->createQuery("SELECT t FROM AppApiBundle:Tenant t WHERE t.id=:idtenant AND t.deleted=0");
            $query->setParameter("idtenant", $idtenant);

            $tenant = $query->getOneOrNullResult();

            if (!$tenant) {
                throw new HttpException(100, "The tenant \"$idtenant\" doesn't exist.");
            }

            $response['data'] = $tenant;

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
            $query = $em->createQuery("SELECT t FROM AppApiBundle:Tenant t");
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

            $post = $request->request->get('tenant', array());

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

    public function putAction($idtenant, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('tenant', array());

            if (!$idtenant) {
                throw new HttpException(100, "The idtenant is required");
            }

            $this->proccessData($post, $response, $idtenant);
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

    public function deleteAction($idtenant, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $validator = $this->get('validator');

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idtenant=="%s") {
                throw new HttpException(100, "The idtenant is required");
            }

            $tenant = $em->find("\App\ApiBundle\Entity\Tenant", $idtenant);

            if (!$tenant) {
                throw new HttpException(100, "The tenant \"$idtenant\" doesn't exist.");
            }

            $tenant->setDeleted(true);
            $em->persist($tenant);
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

    private function proccessData($post, &$response, $idtenant = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->transactional(
                function ($em) use ($post, &$response, $idtenant) {

                    $validator = $this->get('validator');

                    if ($idtenant) {
                        $tenant = $em->find("\App\ApiBundle\Entity\Tenant", $idtenant);
                        if (!$tenant) {
                            throw new HttpException(100, "The Tenant with id \"$idtenant\" doesn't exist.");
                        }
                    } else {
                        $tenant = new Tenant();
                        $em->persist($tenant);
                    }

                    if (isset($post['name'])) {
                        $tenant->setName($post['name']);
                    }
                    if (isset($post['surname'])) {
                        $tenant->setSurname($post['surname']);
                    }
                    if (isset($post['email'])) {
                        $tenant->setEmail($post['email']);
                    }
                    if (isset($post['landline'])) {
                        $tenant->setLandline($post['landline']);
                    }
                    if (isset($post['mobile'])) {
                        $tenant->setMobile($post['mobile']);
                    }
                    if (isset($post['address'])) {
                        $tenant->setAddress($post['address']);
                    }
                    if (isset($post['postcode'])) {
                        $tenant->setPostcode($post['postcode']);
                    }
                    if (isset($post['town'])) {
                        $tenant->setTown($post['town']);
                    }
                    if (isset($post['birthdate'])) {
                        $tenant->setBirthdate($post['birthdate']);
                    }
                    if (isset($post['nin'])) {
                        $tenant->setNin($post['nin']);
                    }
                    if (isset($post['need_night_support'])) {
                        $tenant->setNeedNightSupport($post['need_night_support']);
                    }
                    if (isset($post['has_chc_budget'])) {
                        $tenant->setHasChcBudget($post['has_chc_budget']);
                    }
                    if (isset($post['support_package_hours'])) {
                        $tenant->setSupportPackageHours($post['support_package_hours']);
                    }
                    if(isset($post['contact_method_other'])){
                        $tenant->setContactMethodOther($post['contact_method_other']);
                    }
                    if(isset($post['comments'])){
                        $tenant->setComments($post['comments']);
                    }

                    // contact method
                    if(isset($post['contact_method']) && $post['contact_method'])
                    {
                        $contactMethod = $em->find("\App\ApiBundle\Entity\ContactMethod", $post['contact_method']);
                        if($contactMethod)
                            $tenant->setContactMethod($contactMethod);
                    }
                    else
                    {
                        $tenant->setContactMethod(null);
                    }

                    // type & status
                    if((isset($post['contact_type']) && $post['contact_type']) && (isset($post['contact_status']) && $post['contact_status']))
                    {
                        $typeHasStatus = $em->find("\App\ApiBundle\Entity\TypeHasStatus", array("type"=>$post['contact_type'],"status"=>$post['contact_status']));

                        if($typeHasStatus)
                        {
                            $tenant->setTypeHasStatus($typeHasStatus);
                        }
                        else
                        {
                            $tenant->setTypeHasStatus(null);
                        }
                    }
                    else
                    {
                        $tenant->setTypeHasStatus(null);
                    }

                    // contact title
                    if (isset($post['contact_title']) && $post['contact_title']) {
                        $contactTitle = $em->find("\App\ApiBundle\Entity\ContactTitle", $post['contact_title']);
                        if ($contactTitle) {
                            $tenant->setContactTitle($contactTitle);
                        }
                    } else {
                        $tenant->setContactTitle(null);
                    }

                    // Gender
                    if (isset($post['gender']) && $post['gender']) {
                        $gender = $em->find("\App\ApiBundle\Entity\Gender", $post['gender']);
                        if ($gender) {
                            $tenant->setGender($gender);
                        }
                    } else {
                        $tenant->setGender(null);
                    }

                    // Marital Status
                    if (isset($post['marital_status']) && $post['marital_status']) {
                        $marital_status = $em->find("\App\ApiBundle\Entity\MaritalStatus", $post['marital_status']);
                        if ($marital_status) {
                            $tenant->setMaritalStatus($marital_status);
                        }
                    } else {
                        $tenant->setMaritalStatus(null);
                    }

                    // Local Authority
                    if (isset($post['local_authority']) && $post['local_authority']) {
                        $local_authority = $em->find("\App\ApiBundle\Entity\Organisation", $post['local_authority']);
                        if ($local_authority) {
                            $tenant->setLocalAuthority($local_authority);
                        }
                    } else {
                        $tenant->setLocalAuthority(null);
                    }

                    // Social services contact
                    if (isset($post['social_services_contact']) && $post['social_services_contact']) {
                        $social_services_contact = $em->find("\App\ApiBundle\Entity\Other", $post['social_services_contact']);
                        if ($social_services_contact) {
                            $tenant->setSocialServicesContact($social_services_contact);
                        }
                    } else {
                        $tenant->setSocialServicesContact(null);
                    }

                    // Agency support provider
                    if (isset($post['agency_support_provider']) && $post['agency_support_provider']) {
                        $agency_support_provider = $em->find("\App\ApiBundle\Entity\Organisation", $post['agency_support_provider']);
                        if ($agency_support_provider) {
                            $tenant->setAgencySupportProvider($agency_support_provider);
                        }
                    } else {
                        $tenant->setAgencySupportProvider(null);
                    }

                    // Contact support provider
                    if (isset($post['contact_support_provider']) && $post['contact_support_provider']) {
                        $contact_support_provider = $em->find("\App\ApiBundle\Entity\Contact", $post['contact_support_provider']);
                        if ($contact_support_provider) {
                            $tenant->setContactSupportProvider($contact_support_provider);
                        }
                    } else {
                        $tenant->setContactSupportProvider(null);
                    }

                    if(isset($post['lack_capacity'])){
                        $tenant->setLackCapacity($post['lack_capacity']);
                    }
                    // Deputy
                    if (isset($post['deputy']) && $post['deputy']) {
                        $deputy = $em->find("\App\ApiBundle\Entity\Contact", $post['deputy']);
                        if ($deputy) {
                            $tenant->setDeputy($deputy);
                        }
                    } else {
                        $tenant->setDeputy(null);
                    }

                    // Housing requirements
                    if(isset($post['housingRegister'])){
                        $tenant->setHousingRegister($post['housingRegister']);
                    }
                    if(isset($post['moveDate'])){
                        $tenant->setMoveDate($post['moveDate']);
                    }
                    if(isset($post['outCounty'])){
                        $tenant->setOutCounty($post['outCounty']);
                    }
                    if(isset($post['specialDesignFeatures'])){
                        $tenant->setSpecialDesignFeatures($post['specialDesignFeatures']);
                    }
                    if(isset($post['tenantPersonality'])){
                        $tenant->setTenantPersonality($post['tenantPersonality']);
                    }
                    if(isset($post['willingToShare'])){
                        $tenant->setWillingToShare($post['willingToShare']);
                    }
                    if(isset($post['parkingFor'])){
                        $tenant->setParkingFor($post['parkingFor']);
                    }

                    // Tenant History
                    if(isset($post['drugHistorial'])){
                        $tenant->setDrugHistorial($post['drugHistorial']);
                    }
                    if(isset($post['drugHistorialDetails'])){
                        $tenant->setDrugHistorialDetails($post['drugHistorialDetails']);
                    }
                    if(isset($post['sexualOffencesHistorial'])){
                        $tenant->setSexualOffencesHistorial($post['sexualOffencesHistorial']);
                    }
                    if(isset($post['sexualOffencesHistorialDetails'])){
                        $tenant->setSexualOffencesHistorialDetails($post['sexualOffencesHistorialDetails']);
                    }
                    if(isset($post['arsonHistorial'])){
                        $tenant->setArsonHistorial($post['arsonHistorial']);
                    }
                    if(isset($post['arsonHistorialDetails'])){
                        $tenant->setArsonHistorialDetails($post['arsonHistorialDetails']);
                    }
                    if(isset($post['evictionsHistorial'])){
                        $tenant->setEvictionsHistorial($post['evictionsHistorial']);
                    }
                    if(isset($post['evictionsHistorialDetails'])){
                        $tenant->setEvictionsHistorialDetails($post['evictionsHistorialDetails']);
                    }
                    if(isset($post['violenceHistorial'])){
                        $tenant->setViolenceHistorial($post['violenceHistorial']);
                    }
                    if(isset($post['violenceHistorialDetails'])){
                        $tenant->setViolenceHistorialDetails($post['violenceHistorialDetails']);
                    }
                    if(isset($post['antiSocialHistorial'])){
                        $tenant->setAntiSocialHistorial($post['antiSocialHistorial']);
                    }
                    if(isset($post['antiSocialHistorialDetails'])){
                        $tenant->setAntiSocialHistorialDetails($post['antiSocialHistorialDetails']);
                    }
                    if(isset($post['rentArrearsHistorial'])){
                        $tenant->setRentArrearsHistorial($post['rentArrearsHistorial']);
                    }
                    if(isset($post['rentArrearsHistorialDetails'])){
                        $tenant->setRentArrearsHistorialDetails($post['rentArrearsHistorialDetails']);
                    }
                    if(isset($post['vulnerabilityHistorial'])){
                        $tenant->setVulnerabilityHistorial($post['vulnerabilityHistorial']);
                    }
                    if(isset($post['vulnerabilityHistorialDetails'])){
                        $tenant->setVulnerabilityHistorialDetails($post['vulnerabilityHistorialDetails']);
                    }
                    if(isset($post['tenantReferences'])){
                        $tenant->setTenantReferences($post['tenantReferences']);
                    }

                    /*** CHILDREN ***/
                    if (!isset($post['children']) || !is_array($post['children']) || !$post['children']) {
                        $post['children'] = array();
                    }

                    if ($idtenant) {
                        $current_children = $tenant->getChildrenIds();

                        $post_children = array();

                        foreach ($post['children'] as $child) {
                            if (isset($child['id']) && $child['id']) {
                                $post_children[] = $child['id'];
                            }
                        }
                        // to delete
                        $to_delete = array_diff($current_children, $post_children);

                        if ($to_delete) {
                            foreach ($to_delete as $idchild) {
                                $delChild = $em->find("\App\ApiBundle\Entity\Child", $idchild);
                                if (!$delChild) {
                                    throw new HttpException(100, "The Child with id \"{$idchild}\" doesn't exist.");
                                }
                                $tenant->removeChild($delChild);
                            }
                        }
                    }

                    // to update or to create
                    foreach ($post['children'] as $child) {
                    // If child doesn't have name or birthdate
                        if (!$child['name'] || !$child['birthdate']) {
                            continue;
                        }

                        if (isset($child['id']) && $child['id']) {
                            $newChild = $em->find("\App\ApiBundle\Entity\Child", $child['id']);
                            if (!$newChild) {
                                throw new HttpException(100, "The Child with id \"{$idchild}\" doesn't exist.");
                            }
                        } else {
                            $newChild = new Child();
                        }

                        $newChild->setName($child['name']);
                        $newChild->setBirthdate($child['birthdate']);
                        $newChild->setGuardianship($child['guardianship']);
                        $tenant->addChild($newChild);
                        unset($newChild);
                    }

                    /*** NIGHTS SUPPORT ****/
                    if (!isset($post['nights_support']) || !is_array($post['nights_support']) || !$post['nights_support']) {
                        $post['nights_support'] = array();
                    }

                    $current_nights_support = array();
                    $new_nights_support = array();
                    $deleted_nights_support = array();

                    // get current nights_support
                    if ($idtenant) {
                        $current_nights_support = $tenant->getNightsSupportIds();
                    }

                    // get deleted nights_support
                    $deleted_nights_support = array_diff($current_nights_support, $post['nights_support']);

                    // get new nights_support
                    $new_nights_support = array_diff($post['nights_support'], $current_nights_support);

                    // add new nights_support
                    foreach ($new_nights_support as $support) {
                        $newNightSupport = $em->find("\App\ApiBundle\Entity\TenantNightsSupport", $support);
                        if (!$newNightSupport) {
                            throw new HttpException(100, "The Night Support with id \"{$support['id']}\" doesn't exist.");
                        }

                        $tenant->addNightsSupport($newNightSupport);
                        unset($newNightSupport);
                    }

                    // delete nights_support
                    foreach ($deleted_nights_support as $support) {
                        $oldNightSupport = $em->find("\App\ApiBundle\Entity\TenantNightsSupport", $support);
                        if (!$oldNightSupport) {
                            throw new HttpException(100, "The Night Support with id \"{$support['id']}\" doesn't exist.");
                        }

                        $tenant->removeNightsSupport($oldNightSupport);
                        unset($oldNightSupport);
                    }

                    /*** AREAS ***/
                    if(!isset($post['areas']) || !is_array($post['areas']) || !$post['areas'])
                        $post['areas'] = array();

                    if($idtenant)
                    {
                        $current_areas = $tenant->getAreasIds();

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
                                $delArea = $em->find("\App\ApiBundle\Entity\TenantArea", $idarea);
                                if(!$delArea)
                                    throw new HttpException(100, "The Area with id \"{$idarea}\" doesn't exist.");
                                $tenant->removeArea($delArea);
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
                            $newArea = $em->find("\App\ApiBundle\Entity\TenantArea", $area['id']);
                            if(!$newArea)
                                throw new HttpException(100, "The Area with id \"{$idarea}\" doesn't exist.");
                        }
                        else
                        {
                            $newArea = new TenantArea();
                        }

                        $newArea->setDistance($area['distance']);
                        $newArea->setPostcode($area['postcode']);
                        $tenant->addArea($newArea);
                        unset($newArea);
                    }

                    /*** REQUIREMENTS ****/
                    if(!isset($post['requirements']) || !is_array($post['requirements']) || !$post['requirements'])
                        $post['requirements'] = array();


                    $current_requirements = array();
                    $new_requirements = array();
                    $deleted_requirements = array();

                    // get current requirements
                    if($idtenant)
                    {
                        $current_requirements = $tenant->getRequirementsIds();
                    }

                    // get deleted requirements
                    $deleted_requirements = array_diff($current_requirements, $post['requirements']);

                    // get new requirements
                    $new_requirements = array_diff($post['requirements'], $current_requirements);

                    // add new requirements
                    foreach ($new_requirements as $requirement)
                    {
                        $newRequirement = $em->find("\App\ApiBundle\Entity\Feature", $requirement);
                        if(!$newRequirement)
                            throw new HttpException(100, "The Requirement with id \"{$requirement}\" doesn't exist.");

                        $tenant->addRequirement($newRequirement);
                        unset($newRequirement);
                    }

                    // delete requirements
                    foreach ($deleted_requirements as $requirement)
                    {
                        $oldRequirement = $em->find("\App\ApiBundle\Entity\Feature", $requirement);
                        if(!$oldRequirement)
                            throw new HttpException(100, "The Requirement with id \"{$requirement}\" doesn't exist.");

                        $tenant->removeRequirement($oldRequirement);
                        unset($oldRequirement);
                    }

                    // LFL contact
                    if (isset($post['lfl_contact']) && $post['lfl_contact']) {
                        $lfl_contact = $em->find("\App\ApiBundle\Entity\Other", $post['lfl_contact']);
                        if ($lfl_contact) {
                            $tenant->setLflContact($lfl_contact);
                        }
                    } else {
                        $tenant->setLflContact(null);
                    }

                    /* check validation */
                    $errors = $validator->validate($tenant);

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

                    // add new conditions
                    $em->flush();

                    /*** CONDITIONS ****/
                    if (!isset($post['conditions']) || !is_array($post['conditions']) || !$post['conditions']) {
                        $post['conditions'] = array();
                    }

                    $current_conditions = array();
                    $new_conditions = array();
                    $deleted_conditions = array();

                    // get current_conditions
                    if ($idtenant) {
                        $current_conditions = $tenant->getConditionsIds();
                    }

                    // get deleted conditions
                    $deleted_conditions = array_diff($current_conditions, $post['conditions']);

                    // get new conditions
                    $new_conditions = array_diff($post['conditions'], $current_conditions);

                    foreach ($new_conditions as $condition) {
                        $newCondition = $em->find("\App\ApiBundle\Entity\TenantCondition", $condition);
                        if (!$newCondition) {
                            throw new HttpException(100, "The Condition with id \"{$condition['id']}\" doesn't exist.");
                        }

                        $thc = new TenantHasCondition();
                        $thc->setCondition($newCondition);
                        $thc->setTenant($tenant);

                        //$fb = $this->get('my_fire_php');

                        if (isset($post['other_'.$condition])) {
                            if (array_key_exists("other_".$condition, $post)) {
                                //$fb->log($post['other_'.$condition], "The condition ".$condition." exist in post[other]");
                                $thc->setOther($post['other_'.$condition]);
                            }
                        }

                        $em->persist($thc);
                        $tenant->addTenantHasCondition($thc);
                        unset($newCondition);
                    }

                    // delete conditions
                    foreach ($deleted_conditions as $condition) {
                        $oldCondition = $em->find("\App\ApiBundle\Entity\TenantCondition", $condition);
                        if (!$oldCondition) {
                            throw new HttpException(100, "The Condition with id \"{$condition['id']}\" doesn't exist.");
                        }

                        $thc = new TenantHasCondition();
                        $thc->setCondition($oldCondition);
                        $thc->setTenant($tenant);
                        $em->flush();
                        $tenant->removeTenantHasCondition($thc);
                        unset($oldCondition);
                    }

                    // save
                    //$em->flush();
                    $em->persist($tenant);
                    $response['data'] = $tenant; // return the id created
                }
            );
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
